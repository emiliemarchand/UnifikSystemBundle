<?php

namespace Egzakt\SystemBundle\Form\Backend;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

use Egzakt\SystemBundle\Form\Backend\SectionType;

/**
 * RootSection Type
 */
class RootSectionType extends SectionType
{
    /**
     * Build Form
     *
     * @param FormBuilderInterface $builder The Builder
     * @param array $options Array of options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'root_section';
    }

    /**
     * Get Default Options
     *
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Egzakt\SystemBundle\Entity\Section'
        );
    }
}