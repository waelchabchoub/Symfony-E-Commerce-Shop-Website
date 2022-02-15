<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                    'choices'  => [
                      'User' => 'ROLE_USER',
                      'Editor' => 'ROLE_EDITOR',
                      'Admin' => 'ROLE_ADMIN',
                    ],
                ])
        
            ->add('password',PasswordType::class)
            ->add('confirm_password',PasswordType::class)
            
        ;

                    // Data transformer
                    $builder->get('roles')
                    ->addModelTransformer(new CallbackTransformer(
                        function ($rolesArray) {
                            // transform the array to a string
                            return count($rolesArray)? $rolesArray[0]: null;
                        },
                        function ($rolesString) {
                            // transform the string back to an array
                            return [$rolesString];
                        }
                    ));

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
