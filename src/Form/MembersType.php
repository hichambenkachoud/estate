<?php

namespace App\Form;

use App\Entity\Members;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MembersType extends AbstractType
{
    private $trans;
    public function __construct(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => $this->trans->trans('admin.firstname'),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->trans->trans('admin.lastname'),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans->trans('admin.email'),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('mobileNumber', TextType::class, [
                'label' => $this->trans->trans('admin.mobileNumber'),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('birthDay', DateTimeType::class, [
                'label' => $this->trans->trans('admin.birthday'),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker form-control',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-mm-dd'
                ]
            ])
            ->add('userName', TextType::class, [
                'label' => $this->trans->trans('admin.username'),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('genre', ChoiceType::class, [
                'label' => $this->trans->trans('admin.genre'),
                'choices' => [
                    $this->trans->trans('admin.mr') => Members::MR_TYPE,
                    $this->trans->trans('admin.mme') => Members::MME_TYPE,
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
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
            'data_class' => Members::class,
        ]);
    }
}
