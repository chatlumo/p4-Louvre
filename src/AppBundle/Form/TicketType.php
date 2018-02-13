<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 17/01/2018
 * Time: 12:43
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, array(
                'label' => 'app.lastname',

            ))
            ->add('firstname', TextType::class, array(
                'label' => 'app.firstname',

            ))
            ->add('birthdate', BirthdayType::class, array(
                'label' => 'app.birthdate',
                'html5' => true,
                'format' => 'dd/MM/yyyy',

            ))
            ->add('country', CountryType::class, array(
                'label' => 'app.country',
                'preferred_choices' => array('FR'),
                /*'choices' => array(
                    'France' => 'FR',
                    'Angleterre' => 'UK',
                )*/
            ))
            ->add('reducedPrice', CheckboxType::class, array(
                'label' => 'app.step2.reduced_price',
                'required' => false,

            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ticket',
        ));
    }

}
