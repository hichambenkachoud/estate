<?php

namespace App\Form;

use App\Entity\Adverts;
use App\Entity\AdvertType;
use App\Entity\EstateType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdvertsType extends AbstractType
{
    private $trans;
    public function __construct(TranslatorInterface $trans)
    {
        $this->trans = $trans;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class)
            ->add('age', IntegerType::class)
            ->add('area', NumberType::class)
            ->add('soilType', ChoiceType::class, [
                'label' => 'soil',
                'choices' => [
                    'soilA' => 'soilA'
                ]
            ])
            ->add('rooms', IntegerType::class)
            ->add('bedrooms', IntegerType::class)
            ->add('bathrooms', IntegerType::class)
            ->add('floor', ChoiceType::class, [
                'label' => 'etage',
                'choices' => [
                    'etage1' => 'etage1'
                ]
            ])
            ->add('capacity', IntegerType::class)
            ->add('min_night', IntegerType::class)
            ->add('mobileNumber', TextType::class)
            ->add('characteristics')
            ->add('advertType', EntityType::class, [
                'class' => AdvertType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.enabled = true')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'label' => $this->trans->trans('front.adverts.type'),
                'placeholder' => $this->trans->trans('front.choose.options'),
                'attr' => [
                    'class' => 'form-control populate',
                    'data-plugin-selectTwo' => ""
                ]
            ])
            ->add('estateType', EntityType::class, [
                'class' => EstateType::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.enabled = true')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded' => true,
                'label' => $this->trans->trans('front.estates.type'),
                'placeholder' => $this->trans->trans('front.choose.options'),
                'attr' => [
                    'class' => 'form-control populate',
                    'data-plugin-selectTwo' => ""
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Adverts::class,
        ]);
    }
}
