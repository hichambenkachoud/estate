<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserManagementType extends AbstractType
{
    private $trans;
    public function __construct(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => $this->trans->trans('admin.email'),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('roles', CollectionType::class,
                [
                    'entry_type' => ChoiceType::class,
                    'label' => $this->trans->trans('admin.roles'),
                    'allow_add' => false,
                    'entry_options' => [
                        'label' => false,
                        'choices' => [
                            $this->trans->trans('admin.role.user') => User::ROLE_USER,
                            $this->trans->trans('admin.role.admin') => User::ROLE_ADMIN,
                            $this->trans->trans('admin.role.super_admin') => User::ROLE_SUPER_ADMIN,
                        ],
                        'attr' => [
                            'class' => 'form-control populate',
                            'data-plugin-selectTwo' => ""
                        ]
                    ]

                ]
            )
            ->add('firstName', TextType::class, [
                'label' => $this->trans->trans('admin.firstname'),
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->trans->trans('admin.lastname'),
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('mobileNumber', TextType::class, [
                'label' => $this->trans->trans('admin.mobileNumber'),
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('enabled', ChoiceType::class,
                [
                    'label' => $this->trans->trans('admin.status'),
                    'choices' => [
                        $this->trans->trans('admin.enabled') => true,
                        $this->trans->trans('admin.disabled') => false,
                    ],
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]
            )
        ;


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            // checks if the Product object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$user || null === $user->getId()) {
                $form->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => $this->trans->trans('admin.password.not_match'),
                    'required' => true,
                    'first_options' => array('label' => $this->trans->trans('admin.password'), 'attr' => [ 'class' => 'form-control']),
                    'second_options' => array('label' => $this->trans->trans('admin.confirm_password'), 'attr' => [ 'class' => 'form-control']),
                    'attr' => [
                        'class' => 'password-input'
                    ]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
