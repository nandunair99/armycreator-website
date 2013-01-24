<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle\Twig;

use \Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed;


class BreedImage extends \Twig_Extension
{

    /**
     * getFunctions
     *
     * @access public
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'breedImage' => new \Twig_Function_Method($this, 'getBreedImage')
        );
    }

    /**
     * getBreedImage
     *
     * @param Breed $breed
     * @param int $width
     * @param int $height
     * @access public
     * @return string
     */
    public function getBreedImage(Breed $breed, $width = null, $height = null)
    {
        $breedImage = $breed->getImage();
        
        $str = '<div
                    class="breedImage"';
        if (!empty($width)) {
            $str .= 'style="width:' . (int) $width . 'px' . (!empty($height) ? ';height:' . $height . 'px' : '') . '"';
        }
        $str .= ' >
                    <img src="/images/breed/' . (empty($breedImage) ? 'null.jpg' : $breedImage) .'"
                        title="' . htmlspecialchars($breed->getName()) . '"
                        alt="' . htmlspecialchars($breed->getName()) . '"
                        />
                </div>';

        return $str;
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'breedImage';
    }
}

