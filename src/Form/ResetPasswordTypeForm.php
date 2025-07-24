<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;

class ResetPasswordTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           
             ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 30
                    ])
                ],
                    'first_options'  => [
                        'label' => 'Votre nouveau mot de passe', 
                         'attr' => [
                         'placeholder' => "Choisissez votre nouveau mot de passe "
                    ],
                     'hash_property_path' => 'password'
                    ],
                    'second_options' => [
                        'label' => 'Confirmez votre nouveau mot de passe',
                        'attr' => [
                        'placeholder' => "Confirmez votre nouveau mot de passe "
                ]
                    ],
                    'mapped' => false,
            ])
            ->add('Submit', SubmitType::class, [
                'label' => "Mettre à jour mon mot de passe",
                'attr' => [
                    'class' => "btn btn-success"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => User::class,
        ]);
    }
}
