<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Email',
                'label_attr'=> [
                    'class' => 'form-label'
                ],
                'attr' => [
                    'placeholder' => 'Entrer votre adresse e-mail',
                    'class' => 'form-control',
                    // 'required' => false,
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Mot de passe',
                'label_attr' => ['class' => 'form-label'],
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'new-password'
                ],
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => ['class' => 'form-label'],
                    'attr' => [
                        'class' => "form-control mb-3",
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe',
                    'label_attr' => ['class' => 'form-label'],
                    'attr' => [
                        'class' => "form-control mb-3",
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Merci d'enregistrer un mot de passe",
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit au moins avoir {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('isMinor', CheckboxType::class, [
                'row_attr' => ['class'=> "form-check mb-2"],
                'label_attr' => ['class'=> "form-check-label"],
                'attr' => ['class'=> "form-check-input"],
                'label' => "Êtes-vous majeur ?",
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez être majeur pour vous inscrire',
                    ]),
                ],
            ])
            ->add('isTerms', CheckboxType::class, [
                'row_attr' => ['class'=> "form-check mb-2"],
                'label_attr' => ['class'=> "form-check-label"],
                'attr' => ['class'=> "form-check-input"],
                'label' => "Acceptez-vous les CGU ?",
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez accepter les CGU pour vous inscrire",
                    ]),
                ],
            ])
            ->add('isGpdr', CheckboxType::class, [
                'row_attr' => ['class' => 'form-check mb-2'],
                'label_attr' => ['class'=> "form-check-label"],
                'attr' => ['class'=> "form-check-input"],
                'label' => "Acceptez-vous notre politique RGPD ?",
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter notre politique RGPD',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    "class" => "btn btn-primary"
                ]
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
