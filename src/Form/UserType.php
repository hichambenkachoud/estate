<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
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
                        'class' => 'form-control form-control-lg'
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
