<?php

namespace App\Form;

use App\Entity\Detail;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DetailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', ChoiceType::class, [
                'mapped' => false,
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    )
                    ],
                    'choices' => [
                        'Professionnel' => 'Pro',
                        'Agent d\'influenceur' => 'Agent',
                    ]
            ])
            ->add('company_number', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'value' => '1234567890'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    ),
                    new Length(
                        min: 9,
                        max: 14,
                        minMessage: "Le numéro de SIRET ou SIREN doit faire au moins {{ limit }} caractères",
                        maxMessage: "Le numéro de SIRET ou SIREN ne peut pas dépasser {{ limit }} caractères"
                    ),
                    new Regex(
                        pattern: "/^[0-9]{9,14}$/",
                        message: "Le numéro de SIRET ou SIREN doit être composé de chiffres uniquement"
                    )
                ]
            ])
            ->add('company_name', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'value' => 'ABC Prod'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    )
                ]
            ])
            ->add('address', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'value' => '123 rue des alouettes'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    )
                ]
            ])
            ->add('city', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'value' => 'Paris'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    )
                ]
            ])
            ->add('postal_code', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'value' => '75001'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    ),
                    new Length(
                        min: 5,
                        max: 5,
                        minMessage: "Le code postal doit faire au moins {{ limit }} caractères",
                        maxMessage: "Le code postal ne peut pas dépasser {{ limit }} caractères"
                    ),
                    new Regex(
                        pattern: "/^[0-9]{5}$/",
                        message: "Le code postal doit être composé de chiffres uniquement"
                    )
                ]
            ])
            ->add('country', CountryType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    )
                ]
            ])
            ->add('portfolio_link', UrlType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => '',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control', 'value' => 'https://www.google.com'],
                'constraints' => [
                    new NotBlank(
                        message : "Ce champs est obligatoire"
                    )
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregister',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Detail::class,
        ]);
    }
}
