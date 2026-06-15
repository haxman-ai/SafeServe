<?php

namespace App\Controller;

use App\Entity\Temp;
use App\Form\TempType;
use App\Repository\MenuRepository;
use App\Repository\TempRepository;
use App\Service\HaccpService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TempController extends AbstractController
{
    #[Route('/temp', name: 'app_temp')]
    public function index(Request $request, TempRepository $temprepository, HaccpService $haccp, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $pagination = $paginator->paginate(
            $temprepository->findBy([], ['releveAT' => 'DESC']),
            $request->query->getInt('page', 1),
            8
        );

        $conformites = [];
        foreach ($pagination as $t) {
            $conformites[$t->getId()] = $haccp->isConforme($t);
        }

        return $this->render('temp/index.html.twig', [
            'temp' => $pagination,
            'conformites' => $conformites,
        ]);
    }

    #[Route('/temp/new', name: 'app_temp_new')]
    public function form(Request $request, EntityManagerInterface $em ,MenuRepository $menurepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $menuId = $request->query->getInt('menu', 0);
        $menuday = $menurepository->find($menuId);

        if (!$menuday) {
         return $this->redirectToRoute('app_temp_menu');
        }

        $temp = new Temp();
        $temp->setReleveAT($menuday->getServedAt());
        $form = $this->createForm(TempType::class, $temp, ['plats' => $menuday->getPlats()]);
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
    public function menuSemaine(Request $request, MenuRepository $menurepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
    
        $offset = $request->query->getInt('semaine', 0);
        $lundi = new DateTime('monday this week');
        $lundi->modify("{$offset} week");
        $vendredi = clone $lundi;
        $vendredi->modify('+4 days')->setTime(23, 59, 59);

        $menusSemaine = $menurepository->findMenusSemaine($lundi, $vendredi);

        return $this->render('temp/menu-semaine.html.twig', [
            'menu' => $menusSemaine,
        ]);
    }

    #[Route('/menu/conform', name:'app_conform')]
    public function conformites(Request $request,TempRepository $temprepository, HaccpService $haccp,PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
          $pagination = $paginator->paginate(
            $temprepository->findBy([], ['releveAT' => 'DESC']),
            $request->query->getInt('page', 1),
            8
        );

        $conformites = [];
        foreach ($pagination as $t) {
            $conformites[$t->getId()] = $haccp->isConforme($t);
        }

        return $this->render('menu/conform/conform.html.twig', [
            'temp' => $pagination,
            'conformites' => $conformites,
        ]);



    }
}
