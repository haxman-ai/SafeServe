<?php

namespace App\Controller;
use App\Repository\TempRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TempController extends AbstractController
{
    #[Route('/temp', name: 'app_temp')]
    public function index(TempRepository $temprepository): Response
       
    {   $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('temp/index.html.twig', [
            'temp'=>$temprepository->findAll()
        ]);
    }
}
