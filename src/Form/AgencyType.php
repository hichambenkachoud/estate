<?php

namespace App\Form;

use App\Entity\Agency;
use App\Entity\City;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Contracts\Translation\TranslatorInterface;

class AgencyType extends AbstractType
{
    private $trans;
    public function __construct(TranslatorInterface $translator)
    {
        $this->trans = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'label' => $this->trans->trans('admin.code'),
                'constraints' => new NotBlank(),
                'label_attr' => ['class' => 'control-label text-lg required'],
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans->trans('admin.email'),
                'constraints' => new NotBlank(),
                'label_attr' => ['class' => 'control-label text-lg required'],
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('name', TextType::class,
                [
                    'label' => $this->trans->trans('admin.name'),
                    'label_attr' => ['class' => 'control-label text-lg required'],
                    'required' => false,
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
            ->add('url', TextType::class, [
                'label' => $this->trans->trans('admin.url'),
                'label_attr' => ['class' => 'control-label text-lg required'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => new Url([
                    'message' => $this->trans->trans('admin.url.format'),
                    'protocols' =>  ["http", "https", "ftp"]
                ]),
            ])
            ->add('mobileNumber', TextType::class, [
                'label' => $this->trans->trans('admin.mobileNumber'),
                'label_attr' => ['class' => 'control-label text-lg required'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => new Regex([
                    'pattern' => '/^212(5|6|7)\d{8}$/',
                    'message' => $this->trans->trans('front.mobileNumber.format')
                ]),
            ])
            ->add('address', TextType::class, [
                'label' => $this->trans->trans('admin.address'),
                'label_attr' => ['class' => 'control-label text-lg required'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => new Length([
                    'min' => 20,
                    'minMessage' => $this->trans->trans('admin.address.format')
                ]),
            ])
            ->add('image', FileType::class, [
                'label' => $this->trans->trans('admin.image'),
                'label_attr' => ['class' => 'control-label text-lg required'],
                'data_class' => null,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => $this->trans->trans('admin.image.format'),
                    ])
                ],
            ])

            ->add('city', EntityType::class,
                [
                    'class' => City::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.enabled = true')
                            ->orderBy('u.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'label' => $this->trans->trans('admin.city'),
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
            'data_class' => Agency::class,
        ]);
    }
}
