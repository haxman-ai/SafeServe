<?php
namespace App\Form;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('servedAt', DateType::class, [
                'label' => '📅 Date du menu',
                'widget' => 'single_text',
            ])
            ->add('entree', TextType::class, [
                'label' => '🥗 Entrée❄️',
                'mapped' => false,
                'required' => false,
            ])
            ->add('plat', TextType::class, [
                'label' => '🍽️ Plat principal♨️',
                'mapped' => false,
                'required' => false,
            ])
            ->add('dessert', TextType::class, [
                'label' => '🍰 Dessert❄️',
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}