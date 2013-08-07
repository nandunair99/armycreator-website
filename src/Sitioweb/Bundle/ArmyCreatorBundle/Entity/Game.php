<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sitioweb\Bundle\ArmyCreatorBundle\Entity\Game
 *
 * @ORM\Entity(repositoryClass="GameRepository")
 */
class Game
{
    /**
     * @var integer $id
     * @access private
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var string $code
     * @access private
     *
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $code;

    /**
     * @var string $name
     * @access private
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

	/**
	 * breedList
	 * 
	 * @var mixed
	 * @access private
	 *
	 * @ORM\OneToMany(targetEntity="Breed", mappedBy="game")
	 */
	private $breedList;

    /**
     * availableBreedList
     * 
     * @var array
     * @access private
     */
    private $availableBreedList;

	/**
	 * breedGroupList
	 * 
	 * @var mixed
	 * @access private
	 *
	 * @ORM\OneToMany(targetEntity="BreedGroup", mappedBy="game")
	 */
	private $breedGroupList;

    public function __construct()
    {
        $this->breedGroupList = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Game
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Game
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add breedGroupList
     *
     * @param Sitioweb\Bundle\ArmyCreatorBundle\Entity\BreedGroup $breedGroupList
     * @return Game
     */
    public function addBreedGroupList(\Sitioweb\Bundle\ArmyCreatorBundle\Entity\BreedGroup $breedGroupList)
    {
        $this->breedGroupList[] = $breedGroupList;
        return $this;
    }

    /**
     * Remove breedGroupList
     *
     * @param Sitioweb\Bundle\ArmyCreatorBundle\Entity\BreedGroup $breedGroupList
     */
    public function removeBreedGroupList(\Sitioweb\Bundle\ArmyCreatorBundle\Entity\BreedGroup $breedGroupList)
    {
        $this->breedGroupList->removeElement($breedGroupList);
    }

    /**
     * Get breedGroupList
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBreedGroupList()
    {
        return $this->breedGroupList;
    }

    /**
     * Add breedList
     *
     * @param Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed $breedList
     * @return Game
     */
    public function addBreedList(\Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed $breedList)
    {
        $this->breedList[] = $breedList;
        return $this;
    }

    /**
     * Remove breedList
     *
     * @param Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed $breedList
     */
    public function removeBreedList(\Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed $breedList)
    {
        $this->breedList->removeElement($breedList);
    }

    /**
     * Get breedList
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBreedList()
    {
        return $this->breedList;
    }

    /**
     * getAvailableBreedList
     *
     * @access public
     * @return void
     */
    public function getAvailableBreedList()
    {
        if (!isset($this->availableBreedList)) {
            $this->availableBreedList = array();
            $breedList = $this->getBreedList();
            foreach ($breedList as $breed) {
                if ($breed->getAvailable()) {
                    $this->availableBreedList[] = $breed;
                }
            }
        }

        return $this->availableBreedList;
    }

    /**
     * __toString
     *
     * @access public
     * @return string
     */
	public function __toString()
	{
		return $this->getName();
	}
}
