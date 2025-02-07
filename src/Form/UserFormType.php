<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Detail;
use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use PHPUnit\TextUI\XmlConfiguration\File;
use Symfony\UX\Dropzone\Form\DropzoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

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
            ])

            ->add('username', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => "Votre nom d'utilisateur",
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('fullname', TextType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Votre nom complet',
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control']
            ])

            ->add('image', DropzoneType::class, [
                'mapped' => false, // Déconnexion du lien avec l'entité
                'row_attr' => ['class' => 'mb-5'],
                'label' => "Image de profil",
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control my-3',
                    'placeholder' => 'Déposez votre image ici',
                ],
                'constraints' => [
                    new Image([
                        'mimeTypes' => [
                            'jpg',
                            'jpeg',
                            'png',
                        ],
                        'mimeTypesMessage' => "L'image doit être au format .jpg, .jpeg ou .png",
                    ]),
                ],
            ])

            // Prêt
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