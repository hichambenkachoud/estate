<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Province;
use App\Entity\Region;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CityType extends AbstractType
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
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
            ->add('name', TextType::class,
                [
                    'label' => $this->trans->trans('admin.name'),
                    'attr' => [
                        'class' => 'form-control'
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
            ->add('province', EntityType::class,
                [
                    'class' => Province::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.enabled = true')
                            ->orderBy('u.name', 'ASC');
                    },
                    'label' => $this->trans->trans('admin.province'),
                    'choice_label' => 'name',
                    'placeholder' => $this->trans->trans('admin.choose.options'),
                    'attr' => [
                        'class' => 'form-control populate',
                        'data-plugin-selectTwo' => ""
                    ]
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }
}
