<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Votre adresse e-mail',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse email'
                    ]),
                    new Email([
                        'message' => 'L\'adresse email {{ value }} n\'est pas valide'
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Saisisez votre mot de passe pour mettre à jour votre profil',
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'placeholder' => 'Mot de passe',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
                    ])
                ]
            ])
            ->add('username', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => "Votre nom d'utilisateur",
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom d\'utilisateur'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre nom d\'utilisateur doit faire au moins {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Votre nom d\'utilisateur ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('fullname', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Votre nom complet',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre nom complet'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom complet doit faire au moins {{ limit }} caractères',
                        'max' => 100,
                        'maxMessage' => 'Votre nom complet ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('image', DropzoneType::class, [
                'mapped' => false,
                'required' => false,
                'row_attr' => ['class' => 'my-4 bg-white'],
                'label' => false,
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control my-3',
                    'placeholder' => 'Mettre à jour votre photo de profil',
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'Votre image ne doit pas dépasser {{ limit }}',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG)',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier mon profil',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}