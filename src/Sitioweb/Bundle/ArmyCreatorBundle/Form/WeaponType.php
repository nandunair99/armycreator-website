<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sitioweb\Bundle\ArmyCreatorBundle\Entity\Breed;
use Sitioweb\Bundle\ArmyCreatorBundle\Form\Type\FArm\EquipementDescriptionType as FArmEquipementDescriptionType;

class WeaponType extends AbstractType
{
    /**
     * breed
     *
     * @var Breed
     * @access private
     */
    private $breed;

    /**
     * __construct
     *
     * @param Breed $breed
     * @access public
     */
    public function __construct(Breed $breed)
    {
        $this->breed = $breed;
    }

    /**
     * buildForm
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @access public
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');

        $builder = $this->addBreedSpecifics($builder);
    }

    /**
     * setDefaultOptions
     *
     * @param OptionsResolverInterface $resolver
     * @access public
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sitioweb\Bundle\ArmyCreatorBundle\Entity\Weapon',
            'translation_domain' => 'forms'
        ));
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'armycreator_weapontype';
    }

    /**
     * addBreedSpecifics
     *
     * @access private
     * @return FormBuilderInterface
     */
    private function addBreedSpecifics(FormBuilderInterface $builder)
    {
        switch ($this->breed->getGame()->getCode()) {
            case 'FArm':
                $builder->add('rule', new FArmEquipementDescriptionType);
                break;
            default:
                $builder->add('type', null, ['required' => false])
                    ->add('range', null, ['required' => false])
                    ->add('strenght', null, ['required' => false])
                    ->add('armorPenetration', null, ['required' => false])
                    ->add('rule', 'textarea', ['required' => false]);
                break;
        }


        return $builder;
    }
}
