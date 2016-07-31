<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommentType.
 *
 * @package AppBundle\Form
 * @author Tomasz Chojna
 */
class CommentType extends AbstractType
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
            'content',
            'textarea',
            array(
                'label' => 'Treść',
                'required' => true,
                'max_length' => 128,
                'attr'=> array('class'=>'form-control')
            )
        );
        $builder->add(
            'enabled',
            'checkbox',
            array(
                'label' => 'Opublikowane',
                'required' => false
            )
        );
        $builder->add(
            'Zapisz',
            'submit',
            array(
                'label' => 'Zapisz',
                'attr'=> array('class'=>'btn btn-primary btn-save')

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
                'data_class' => 'AppBundle\Entity\Comment',
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
        return 'comment_form';
    }
}
