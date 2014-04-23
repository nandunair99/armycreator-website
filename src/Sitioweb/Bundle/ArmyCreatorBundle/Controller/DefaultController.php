<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle\Controller;

use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * DefaultController
 *
 * @uses Controller
 * @Route("/")
 * @Breadcrumb("breadcrumb.home", route="homepage")
 *
 * @author Julien DENIAU <julien.deniau@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $this->getUser();
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->get('m6_statsd')->increment('website.index.logged');

            return $this->authenticatedIndex();
        } else {
            $this->get('m6_statsd')->increment('website.index.anonymous');

            return [];
        }
    }

    /**
     * authenticatedIndex
     *
     * @access public
     * @return void
     */
    public function authenticatedIndex() {
        $params = [];

        $params['lastArmy'] = $this->container
            ->get('doctrine')
            ->getManager()
            ->getRepository('SitiowebArmyCreatorBundle:Army')
            ->findOneBy(
                ['user' => $this->getUser()],
                ['updateDate' => 'DESC']
            );
        return $this->render(
            'SitiowebArmyCreatorBundle:Default:authenticatedIndex.html.twig',
            $params
        );
    }

    /**
     * getHeader
     *
     * @access public
     * @return string
     *
     * @Route("/header", name="header")
     */
    public function getHeader()
    {
        $assetList = $this->getAssetsList();

        $sidParam = $this->container->getParameter('forum_sid');

        return $this->get('templating')
            ->render(
                'SitiowebArmyCreatorBundle::header.html.twig',
                [
                    'ads' => true,
                    'standalone' => true,
                    'moreCssList' => $assetList['cssList'],
                    'forumSid' => (isset($_COOKIE[$sidParam]) ? $_COOKIE[$sidParam] : null),
                ]
            );
    }

    /**
     * getFooter
     *
     * @access public
     * @return string
     * @Route("/footer", name="footer")
     */
    public function getFooter() {
        $assetList = $this->getAssetsList();

        return $this->get('templating')
            ->render(
                'SitiowebArmyCreatorBundle::footer.html.twig',
                [
                    'moreJsList' => $assetList['jsList'],
                    'standalone' => true,
                ]
            );
    }

    /**
     * getAssetsList
     *
     * @access private
     * @return array
     */
    private function getAssetsList()
    {
        $am = $this->get('assetic.asset_manager');
        $names = $am->getNames();
        $cssList = [];
        $jsList = [];
        foreach ($names as $nameTmp) {
            $name = $am->get($nameTmp)->getTargetPath();
            if (strpos($name, 'global') !== false) {
                if (substr($name, -3) === '.js') {
                    $jsList[] = $name;
                } else {
                    $cssList[] = $name;
                }
            }
        }

        return ['cssList' => $cssList, 'jsList' => $jsList];
    }
}
