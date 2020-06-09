<?php

namespace App\Form;

use App\Entity\EstateCharacteristics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EstateCharacteristicsType extends AbstractType
{
    private $trans;
    public function __construct(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class,
                [
                    'label' => $this->trans->trans('admin.code'),
                    'label_attr' => ['class' => 'control-label text-lg'],
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]
            )
            ->add('name', TextType::class,
                [
                    'label' => $this->trans->trans('admin.name'),
                    'attr' => [
                        'class' => 'form-control',
                    ]
                ]
            )
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
            ->add('type', ChoiceType::class,
                [
                    'label' => $this->trans->trans('admin.type'),
                    'placeholder' => $this->trans->trans('admin.choose.options'),
                    'choices' => [
                        $this->trans->trans('admin.characteristic.general') => EstateCharacteristics::GENERAL_TYPE,
                        $this->trans->trans('admin.characteristic.interior') => EstateCharacteristics::INTERIOR_TYPE,
                        $this->trans->trans('admin.characteristic.additional') => EstateCharacteristics::ADDITIONAL_TYPE,
                    ],
                    'attr' => [
                        'class' => 'form-control populate',
                        'data-plugin-selectTwo' => ""
                    ]
                ]
            )
            ->add('icon', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EstateCharacteristics::class,
        ]);
    }
}
