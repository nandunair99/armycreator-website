<?php

namespace Sitioweb\Bundle\ArmyCreatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sitioweb\Bundle\ArmyCreatorBundle\Entity\SquadLine;

class SquadLineType extends AbstractType
{

    private $squadLine;

    /**
     * setSquadLine
     *
     * @param SquadLine $squadLine
     * @access public
     * @return void
     */
    public function setSquadLine(SquadLine $squadLine)
    {
        $this->squadLine = $squadLine;
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
        $builder->add('number', 'integer');
        
        $builder->add('squadLineStuffList', 'collection', array('type' => new SquadLineStuffType()));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sitioweb\Bundle\ArmyCreatorBundle\Entity\SquadLine',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'sitioweb_bundle_armycreatorbundle_squadlinetype';
    }
}


