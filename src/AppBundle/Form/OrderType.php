<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 15/01/2018
 * Time: 17:35
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = new \DateTime('now');
        $thisYear = $now->format('Y');
        $maxYear = $thisYear + 1;

        $builder
            ->add('dateOfVisit', DateType::class, array(
                'html5' => true,
                'format' => 'dd/MM/yyyy',
                'label' => 'app.step1.choose.dateOfVisit',
                'years' => range($thisYear,$maxYear)

            ))
            ->add('fullDay', ChoiceType::class, array(
                'choices' => array(
                    'app.step1.ticket.fullday' => true,
                    'app.step1.ticket.halfday' => false,
                ),
                'expanded' => true,
                'label' => 'app.step1.choose.ticket',


            ))
            ->add('nbTickets', ChoiceType::class, array(
                'label' => 'app.step1.nbtickets',
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                )

            ))
            ->add('email', RepeatedType::class, array(
                'type' => EmailType::class,
                'invalid_message' => 'app.step1.email.error',
                'required' => true,
                'first_options'  => array('label' => 'app.step1.email.label1'),
                'second_options' => array('label' => 'app.step1.email.label2'),

            ));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Order',
            'validation_groups' => array('step1'),
        ));
    }


}
