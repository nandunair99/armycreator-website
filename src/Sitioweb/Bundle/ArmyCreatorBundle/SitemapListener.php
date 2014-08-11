<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle;

use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Router;

use Doctrine\ORM\EntityManager;

use Sitioweb\Bundle\ArmyCreatorBundle\Entity\Army;

/**
 * SitemapListener
 *
 * @uses SitemapListenerInterface
 */
class SitemapListener implements SitemapListenerInterface
{
    /**
     * router
     *
     * @var Router
     * @access private
     */
    private $router;

    /**
     * entityManager
     *
     * @var EntityManager
     * @access private
     */
    private $entityManager;

    /**
     * event
     *
     * @var SitemapPopulateEvent
     * @access private
     */
    private $event;

    /**
     * treatedUsers
     *
     * @var array
     * @access private
     */
    private $treatedUsers;

    /**
     * __construct
     *
     * @param Router $router
     * @param EntityManager $entityManager
     * @access public
     * @return void
     */
    public function __construct(Router $router, EntityManager $entityManager)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $this->event = $event;
        $section = $event->getSection();
        $this->populateStatic();
        $this->populatePublicArmyList();
    }

    /**
     * populateStatic
     *
     * @access private
     * @return void
     */
    private function populateStatic()
    {
        $routeList = [
            'toolbox_dice' => UrlConcrete::CHANGEFREQ_MONTHLY,
            'toolbox_weapon_statistic' => UrlConcrete::CHANGEFREQ_MONTHLY,
            'toolbox_infight_statistic' => UrlConcrete::CHANGEFREQ_MONTHLY,
            'army_public_list' => UrlConcrete::CHANGEFREQ_DAILY,
            'homepage' => UrlConcrete::CHANGEFREQ_WEEKLY,
            'forum_index' => UrlConcrete::CHANGEFREQ_HOURLY,
        ];

        foreach ($routeList as $route => $freq) {
            $url = $this->router->generate($route);
            $urlConcrete = new UrlConcrete($url, new \DateTime(), $freq, 1);
            $this->event->getGenerator()->addUrl($urlConcrete, 'default');
        }
    }


    /**
     * populatePublicArmyList
     *
     * @access private
     * @return void
     */
    private function populatePublicArmyList()
    {
        $routeListe = [ 'user_index', 'army_detail', ];

        $publicArmyList = $this->entityManager->getRepository('SitiowebArmyCreatorBundle:Army')
            ->findPublicQueryBuilder()
            ->orderBy('a.updateDate', 'DESC')
            ->getQuery()
            ->getResult();

        foreach ($publicArmyList as $army) {
            $freq = $this->getArmyFrequency($army);

            // army route
            $url = $this->router->generate('army_detail', ['slug' => $army->getSlug()]);

            $urlConcrete = new UrlConcrete($url, new \DateTime(), $freq, 1);
            $this->event->getGenerator()->addUrl($urlConcrete, 'army');

            // user route
            $user = $army->getUser();
            if (!isset($this->treatedUsers[$user->getId()])) {
                $url = $this->router->generate('user_index', ['user' => $user->getSlug()]);
                $urlConcrete = new UrlConcrete($url, new \DateTime(), $freq, 1);
                $this->event->getGenerator()->addUrl($urlConcrete, 'user');
            }
        }
    }

    /**
     * getArmyFrequency
     *
     * @param Army $army
     * @access private
     * @return int
     */
    private function getArmyFrequency(Army $army)
    {
        $now = new \DateTime;
        if (!$army->getUpdateDate()) {
            return UrlConcrete::CHANGEFREQ_MONTHLY;
        }

        $diff = $now->format('U') - $army->getUpdateDate()->format('U');

        if ($diff < 604800) {
            return UrlConcrete::CHANGEFREQ_DAILY;
        } else {
            return UrlConcrete::CHANGEFREQ_MONTHLY;
        }
    }
}