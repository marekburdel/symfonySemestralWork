<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class)
            ->add('roles', ChoiceType::class, [
                    'choices' => ['ROLE_ADMIN' => 'ROLE_ADMIN'],
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
            ->add('password', PasswordType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('email', EmailType::class)
            ->add('expirationDate',DateTimeType::class, [
                'format' => DateTimeType::HTML5_FORMAT,
                'data' => new \DateTime(),
                'html5' => true,
//                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
