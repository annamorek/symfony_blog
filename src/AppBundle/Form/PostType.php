<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\DataTransformer\TagDataTransformer;

/**
 * Class PostType.
 *
 * @package AppBundle\Form
 * @author Tomasz Chojna
 */
class PostType extends AbstractType
{
    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tagDataTransformer = new TagDataTransformer($options['tag_model']);

        $builder->add(
            'id',
            'hidden',
            array('mapped' => false)
        );

        $builder->add(
            'topic',
            'text',
            array(
                'label' => 'Temat',
                'required' => true,
                'max_length' => 128,
                'attr'=> array('class'=>'form-control')
            )
        );
        $builder->add(
            'content',
            'textarea',
            array(
                'label' => 'Treść',
                'required' => false,
                'max_length' => 1000,
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
            $builder
                ->create('tags', 'text')
                ->addModelTransformer($tagDataTransformer)
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
                'data_class' => 'AppBundle\Entity\Post',
            )
        );
        $resolver->setRequired(array('tag_model'));
        $resolver->setAllowedTypes(
            array(
                'tag_model' => 'Doctrine\Common\Persistence\ObjectRepository'
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
        return 'post_form';
    }
}
