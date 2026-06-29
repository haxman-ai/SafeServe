<?php

namespace App\Form;

use App\Entity\Plat;
use App\Entity\Temp;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TempType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('temperature')
            ->add('releveAT', null, [
                'widget' => 'single_text',
                'label' => 'Date et Heure',
            ])
            ->add('plat', EntityType::class, [
                'class' => Plat::class,
                'choice_label' => 'name',
                'choices' => $options['plats']
            ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Temp::class,
            'plats'=>[]
            
        ]);

        $resolver->setRequired('plats');
    

    }
}
