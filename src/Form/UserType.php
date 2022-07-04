<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'required'=> true,
                'constraints' => [
                new NotBlank(["message" => "Entrer un email valid"]),
                ]
            ],
            )
            //->add('roles')
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'required'=> true,
                'label'=> 'Mot de passe',
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                
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
