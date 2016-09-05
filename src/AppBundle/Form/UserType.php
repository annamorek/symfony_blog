<?php
/**
 * User type.
 *
 * @copyright (c) 2016 Tomasz Chojna
 * @link http://epi.chojna.info.pl
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TagType.
 *
 * @package AppBundle\Form
 * @author Tomasz Chojna
 */
class UserType extends AbstractType
{
    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            'hidden',
            array('mapped' => false)
        );
        $builder->add(
            'username',
            'text',
            array(
                'label' => 'User name',
                'required' => true,
                'max_length' => 128,
            )
        );
        if ($options['edit'] === false) {
            $builder->add(
                'plainPassword',
                'text',
                array(
                    'label' => 'New password',
                    'max_length' => 128,
                    'required' => true
                )
            );
        } else {
            $builder->add(
                'plainPassword',
                'text',
                array(
                    'label' => 'New password',
                    'max_length' => 128,
                    'required' => false
                )
            );
        }
        $builder->add(
            'email',
            'text',
            array(
                'label' => 'Email',
                'required' => true,
                'max_length' => 128
            )
        );
        $builder->add(
            'enabled',
            'checkbox',
            array(
                'label' => 'Enabled',
                'max_length' => 128,
                'required' => false
            )
        );
        $builder->add(
            'save',
            'submit',
            array(
                'label' => 'Save'
            )
        );
    }

    /**
     * Sets default options for form.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\User',
                'edit' => false
            )
        );
    }

    /**
     * Getter for form name.
     *
     * @return string Form name
     */
    public function getName()
    {
        return 'user_form';
    }
}
