<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 17/01/2018
 * Time: 15:30
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketsFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tickets', CollectionType::class, array(
                'entry_type' => TicketType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'entry_options' => array(
                    'label' => 'app.step2.owner',
                ),
                'label' => false,
            ));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Order',
            'validation_groups' => array('step2'),
        ]);
    }

}