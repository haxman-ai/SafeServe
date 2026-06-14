<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $lundi = new DateTime('monday this week');
        $lundi->modify("{$offset} week");
        $vendredi = clone $lundi;
        $vendredi->modify('+4 days')->setTime(23, 59, 59);
        $menusSemaine = $menuRepository->findMenusSemaine($lundi, $vendredi);

        $semaine = [];
        $jour = clone $lundi;
        for ($i = 0; $i < 5; $i++) {
            $semaine[] = clone $jour;
            $jour->modify('+1 day');
        }

        return $this->render('menu/index.html.twig', [
            'menu' => $menusSemaine,
            'semaine' => $semaine,
        ]);
    }

    #[Route('/menu/new', name: 'app_menu_new')]
    public function form(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($menu->getPlats() as $plat) {
                $menu->removePlat($plat);
            }

            foreach (['entree', 'plat', 'dessert'] as $type) {
                $nom = trim($form->get($type)->getData() ?? '');
                if ($nom !== '') {
                    $plat = new Plat();
                    $plat->setNom($nom);
                    $plat->setType($type);
                    $em->persist($plat);
                    $menu->addPlat($plat);
                }
            }

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
    public function edit(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($menu->getPlats() as $plat) {
                $menu->removePlat($plat);
            }

            foreach (['entree', 'plat', 'dessert'] as $type) {
                $nom = trim($form->get($type)->getData() ?? '');
                if ($nom !== '') {
                    $plat = new Plat();
                    $plat->setNom($nom);
                    $plat->setType($type);
                    $em->persist($plat);
                    $menu->addPlat($plat);
                }
            }

            $em->flush();
            return $this->redirectToRoute('app_menu');
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => true,
        ]);
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
