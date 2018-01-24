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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, array(
                'label' => 'Nom',

            ))
            ->add('firstname', TextType::class, array(
                'label' => 'Prénom',

            ))
            ->add('birthdate', BirthdayType::class, array(
                'label' => 'Date de naissance',
                'html5' => true,
                'format' => 'dd/MM/yyyy',

            ))
            ->add('country', ChoiceType::class, array(
                'label' => 'Pays',
                'choices' => array(
                    'France' => 'FR',
                    'Angleterre' => 'UK',
                )
            ))
            ->add('reducedPrice', CheckboxType::class, array(
                'label' => 'Je bénéficie du tarif réduit à  10 € (étudiant, employé du musée, employé d\'un service du Ministrère de la Culture, militaire)',
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
