<?php
namespace App\Form;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Repository\PlatRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('served_at', null, [
                'label' => 'Date du menu',
                'widget' => 'single_text',
            ])
            ->add('entrees', EntityType::class, [
                'class' => Plat::class,
                'label' => '🥗 Entrées',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'query_builder' => fn(PlatRepository $r) =>
                    $r->createQueryBuilder('p')
                      ->where('p.type = :type')
                      ->setParameter('type', 'entree'),
            ])
            ->add('plats', EntityType::class, [
                'class' => Plat::class,
                'label' => '🍽️ Plats principaux',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'query_builder' => fn(PlatRepository $r) =>
                    $r->createQueryBuilder('p')
                      ->where('p.type = :type')
                      ->setParameter('type', 'plat'),
            ])
            ->add('desserts', EntityType::class, [
                'class' => Plat::class,
                'label' => '🍰 Desserts',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'query_builder' => fn(PlatRepository $r) =>
                    $r->createQueryBuilder('p')
                      ->where('p.type = :type')
                      ->setParameter('type', 'dessert'),
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