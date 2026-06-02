<?php
namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(MenuRepository $menuRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $menu = $menuRepository->findAll();

        return $this->render('menu/index.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/menu/new', name: 'app_menu_new')]
    public function form(Request $request, EntityManagerInterface $em, ?Menu $menu = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $isEdit = $menu !== null; // ✅ corrigé

        if (!$menu) {
            $menu = new Menu(); // ✅ majuscule
        }

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->getPlats()->clear();
            foreach (['entrees', 'plats', 'desserts'] as $field) {
                foreach ($form->get($field)->getData() as $plat) {
                    $menu->addPlat($plat);
                }
            }

            if (!$isEdit) {
                $em->persist($menu);
            }
            $em->flush();

            return $this->redirectToRoute('app_menu');
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form,
            'menu' => $menu,
            'isEdit' => $isEdit,
        ]);
    }
}