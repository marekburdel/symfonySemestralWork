<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromdate',DateTimeType::class, [
                'format' => DateTimeType::HTML5_FORMAT,
                'data' => new \DateTime(),
                'html5' => true,
//                'widget' => 'single_text',
            ])
            ->add('todate',DateTimeType::class, [
                'format' => DateTimeType::HTML5_FORMAT,
                'data' => new \DateTime(),
                'html5' => true,
//                'widget' => 'single_text',
            ])
            ->add('confirmed', ChoiceType::class, array(
                'choices'  => array(
                    'Confirmed' => true,
                    'Not confirmed' => false
                )
            ))
            //->add('user_initiator') dont need, in controller filled
            //->add('reservation_admin')
            //->add('reservation_room')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}


