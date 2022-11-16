<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Vehicule;
use App\Entity\TypeVehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class VehiculeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre')
            ->add('Description')
            ->add('PrixDay', IntegerType::class)
            ->add('ImageFront', FileType::class, [
            'attr' => array(
                'class' => 'py-10'
            ),

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez entrer une image valide',
                ])
            ],
        ])
            ->add('ImageBack', FileType::class, [
            'attr' => array(
                'class' => 'py-10'
            ),

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez entrer une image valide',
                ])
            ],
        ])
            ->add('ImageSide', FileType::class, [
            'attr' => array(
                'class' => 'py-10'
            ),

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez entrer une image valide',
                ])
            ],
        ])
            ->add('ImageInside', FileType::class, [
            'attr' => array(
                'class' => 'py-10'
            ),

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Veuillez entrer une image valide',
                ])
            ],
        ])
            ->add('ModelYear', IntegerType::class)
            ->add('Capacity', IntegerType::class)
            ->add('StatusVehicule')
            ->add('DateRegistration')
            ->add('Id_TypeVehicule',EntityType::class, [
                'expanded' => false,
                'required' => false,
                'class' => TypeVehicule::class,
                'multiple' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('Id_Marque', EntityType::class, [
                'expanded' => false,
                'required' => false,
                'class' => Marque::class,
                'multiple' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
    
}
