<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Repository\TempRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $offset = $request->query->getInt('semaine', 0);
        [$monday, $friday] = $menuRepository->getWeekBounds($offset);
        $weekMenus = $menuRepository->findWeekMenus($monday, $friday);

        $week = [];
        $day = clone $monday;
        for ($i = 0; $i < 5; $i++) {
            $week[] = clone $day;
            $day->modify('+1 day');
        }

        return $this->render('menu/index.html.twig', [
            'menu' => $weekMenus,
            'semaine' => $week,
        ]);
    }

    #[Route('/menu/new', name: 'app_menu_new')]
    public function form(Request $request, TempRepository $tempRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncPlats($menu, $form, $tempRepository, $em);

            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('app_menu');
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => false,
        ]);
    }

    #[Route('/menu/{id}/edit', name: 'app_menu_edit')]
    public function edit(Menu $menu, Request $request, TempRepository $tempRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(MenuType::class, $menu);
        $form->get('entree')->setData($menu->getPlatsByType('entree')->first()?->getName());
        $form->get('plat')->setData($menu->getPlatsByType('plat')->first()?->getName());
        $form->get('dessert')->setData($menu->getPlatsByType('dessert')->first()?->getName());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncPlats($menu, $form, $tempRepository, $em);

            $em->flush();
            return $this->redirectToRoute('app_menu');
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => true,
        ]);
    }

    /**
     * Retire les anciens plats du menu (en supprimant ceux sans relevé de température)
     * et recrée les plats entrée/plat/dessert à partir des champs du formulaire.
     */
    private function syncPlats(Menu $menu, FormInterface $form, TempRepository $tempRepository, EntityManagerInterface $em): void
    {
        foreach ($menu->getPlats()->toArray() as $plat) {
            $menu->removePlat($plat);
            if ($tempRepository->count(['plat' => $plat]) === 0) {
                $em->remove($plat);
            }
        }

        foreach (['entree', 'plat', 'dessert'] as $type) {
            $name = trim($form->get($type)->getData() ?? '');
            if ($name !== '') {
                $plat = new Plat();
                $plat->setName($name);
                $plat->setType($type);
                $em->persist($plat);
                $menu->addPlat($plat);
            }
        }
    }

    #[Route('/menu/{id}/delete', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Menu $menu, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($menu);
        $em->flush();
        return $this->redirectToRoute('app_menu');
    }

    
}
