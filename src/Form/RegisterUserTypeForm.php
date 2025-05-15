<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterUserTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Votre adresse email",
                'attr' => [
                    'placeholder' => "Indiquez votre adresse email"
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => "Votre mot de passe",
                'attr' => [
                    'placeholder' => "Choisissez votre mot de passe "
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom",
                'attr' => [
                    'placeholder' => "Indiquez votre prénom"
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Votre nom",
                'attr' => [
                    'placeholder' => "Indiquez votre nom"
                ]
            ])
            ->add('Submit', SubmitType::class, [
                'label' => "Valider",
                'attr' => [
                    'class' => "btn btn-success"
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
