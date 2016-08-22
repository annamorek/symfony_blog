<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\User;


/**
 * Class CommentType.
 *
 * @package AppBundle\Form
 * @author Tomasz Chojna
 */
class CommentType extends AbstractType
{
    /**
     * User
     * @var User
     */
    private $user;

    /**
     * TransactionType constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Form builder.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->user;
        $userRoles = $this->user->getRoles();
        $userRole = $userRoles[0];

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
        if ($userRole === 'ROLE_ADMIN')
        {
            $builder->add(
            'enabled',
            'checkbox',
            array(
                'label' => 'Opublikowane',
                'required' => false
            )
        );
        }

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
