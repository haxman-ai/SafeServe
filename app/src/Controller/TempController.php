<?php

namespace App\Controller;

use App\Entity\Temp;
use App\Form\TempType;
use App\Repository\MenuRepository;
use App\Repository\TempRepository;
use App\Service\HaccpService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TempController extends AbstractController
{
    #[Route('/temp', name: 'app_temp')]
    public function index(Request $request, TempRepository $tempRepository, HaccpService $haccp, PaginatorInterface $paginator): Response
    {
        $this->denyAdminAccess();

        $pagination = $paginator->paginate(
            $tempRepository->findBy([], ['releveAT' => 'DESC']),
            $request->query->getInt('page', 1),
            8
        );

        $conformites = $haccp->mapConformites($pagination);

        return $this->render('temp/index.html.twig', [
            'temp' => $pagination,
            'conformites' => $conformites,
        ]);
    }

    #[Route('/temp/new', name: 'app_temp_new')]
    public function form(Request $request, EntityManagerInterface $em, MenuRepository $menuRepository): Response
    {
        $this->denyAdminAccess();

        $menuId = $request->query->getInt('menu', 0);
        $dailyMenu = $menuRepository->find($menuId);

        if (!$dailyMenu) {
            return $this->redirectToRoute('app_temp_menu');
        }

        $temp = new Temp();
        $temp->setReleveAT($dailyMenu->getServedAt());
        $form = $this->createForm(TempType::class, $temp, ['plats' => $dailyMenu->getPlats()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $temp->setUser($this->getUser());
            $em->persist($temp);
            $em->flush();
            return $this->redirectToRoute('app_temp');
        }

        return $this->render('temp/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => false,
        ]);
    }

    #[Route('/temp/menu-semaine', name: 'app_temp_menu')]
    public function menuSemaine(Request $request, MenuRepository $menuRepository): Response
    {
        $this->denyAdminAccess();

        $offset = $request->query->getInt('semaine', 0);
        [$monday, $friday] = $menuRepository->getWeekBounds($offset);
        $weekMenus = $menuRepository->findWeekMenus($monday, $friday);

        return $this->render('temp/menu-semaine.html.twig', [
            'menu' => $weekMenus,
        ]);
    }

    #[Route('/menu/conform', name: 'app_conform')]
    public function conformites(Request $request, TempRepository $tempRepository, HaccpService $haccp, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pagination = $paginator->paginate(
            $tempRepository->findBy([], ['releveAT' => 'DESC']),
            $request->query->getInt('page', 1),
            8
        );

        $conformites = $haccp->mapConformites($pagination);

        return $this->render('menu/conform/conform.html.twig', [
            'temp' => $pagination,
            'conformites' => $conformites,
        ]);
    }

    /**
     * Réserve les routes /temp aux cuisiniers : User::getRoles() ajoute toujours
     * ROLE_USER, donc les admins le possèdent aussi et doivent être exclus explicitement.
     */
    private function denyAdminAccess(): void
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
    }
}
