<?php

namespace App\Form;

use App\Entity\EstateStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EstateStatusType extends AbstractType
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EstateStatus::class,
        ]);
    }
}
