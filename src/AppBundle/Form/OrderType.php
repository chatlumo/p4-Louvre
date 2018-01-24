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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                'label' => 'Date de visite',
                'years' => range($thisYear,$maxYear)

            ))
            ->add('fullDay', ChoiceType::class, array(
                'choices' => array(
                    'Ticket journée' => true,
                    'Ticket demi-journée (entrée à partir de 14h)' => false,
                ),
                'expanded' => true,
                'label' => 'Choisir un ticket',


            ))
            ->add('nbTickets', ChoiceType::class, array(
                'label' => 'Indiquer le nombre de billets souhaités',
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
            ->add('email', EmailType::class, array(
                'label' => 'Votre adresse email',

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
