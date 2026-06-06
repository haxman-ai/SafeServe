<?php
namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Plat;
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

        return $this->render('menu/index.html.twig', [
            'menu' => $menuRepository->findAll(), // ← syntaxe tableau correcte
        ]);
    }

    #[Route('/menu/new', name: 'app_menu_new')]
    public function form(Request $request, EntityManagerInterface $em, ?Menu $menu = null): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $isEdit = $menu !== null;

        if (!$menu) {
            $menu = new Menu();
        }

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

            if (!$isEdit) {
                $em->persist($menu);
            }

            $em->flush();
            return $this->redirectToRoute('app_menu'); 
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => $isEdit,
        ]);
    }
}