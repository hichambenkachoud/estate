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

class RegisterType extends AbstractType
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
                'required' =>true,
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => $this->trans->trans('admin.firstname'),
                'required' =>true,
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->trans->trans('admin.lastname'),
                'required' =>true,
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ]
            ])
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
                    'first_options' => array('label' => $this->trans->trans('admin.password'), 'attr' => [ 'class' => 'form-control form-control-lg']),
                    'second_options' => array('label' => $this->trans->trans('admin.confirm_password'), 'attr' => [ 'class' => 'form-control form-control-lg']),
                    'attr' => [
                        'class' => 'password-input',
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
