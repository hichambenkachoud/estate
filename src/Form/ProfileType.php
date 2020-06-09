<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileType extends AbstractType
{
    private $trans;
    public function __construct(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class,
                [
                    'label' => $this->trans->trans('admin.email'),
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
            ->add('firstName', TextType::class,
                [
                    'label' => $this->trans->trans('admin.firstname'),
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
            ->add('lastName', TextType::class,
                [
                    'label' => $this->trans->trans('admin.lastname'),
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
            ->add('mobileNumber', TextType::class,
                [
                    'label' => $this->trans->trans('admin.mobileNumber'),
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => '212(5|6|7)67891234',
                    ]
                ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => $this->trans->trans('admin.password.not_match'),
                'first_options' => array('label' => $this->trans->trans('admin.password'), 'attr' => [ 'class' => 'form-control form-control-lg']),
                'second_options' => array('label' => $this->trans->trans('admin.confirm_password'), 'attr' => [ 'class' => 'form-control form-control-lg']),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
