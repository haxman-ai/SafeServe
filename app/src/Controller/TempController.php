<?php

namespace App\Controller;

use App\Entity\Temp;
use App\Form\TempType;
use App\Repository\TempRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Haccpservice;
final class TempController extends AbstractController
{
    #[Route('/temp', name: 'app_temp')]
    public function index(TempRepository $temprepository, Haccpservice $haccp): Response
       
    {   $this->denyAccessUnlessGranted('ROLE_USER');
        
         $temps = $temprepository->findAll();
         $conformites = [];
         foreach ($temps as $t) {
         $conformites[$t->getId()] = $haccp->isconforme($t);
         }

        return $this->render('temp/index.html.twig', [
              'temp' => $temps,
              'conformites' => $conformites,
        ]);
    }

    #[Route('/temp/new',name: 'app_temp_new')]
    public function form(Request $request,EntityManagerInterface $em,?Temp $temp = null):Response

    {  
        $this->denyAccessUnlessGranted('ROLE_USER');

        $isEdit = $temp !== null;

        if(!$temp) {
            $temp = new Temp();
        }

        $form = $this->createForm(TempType::class,$temp);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $temp->setUser($this->getUser());
    
            if (!$isEdit) {
                $em->persist($temp);
            }

            $em->flush();
            return $this->redirectToRoute('app_temp'); 
        
        }
        return $this->render('temp/form.html.twig', [
            'form' => $form->createView(),
            'isEdit' => $isEdit,
        ]);

    }
    
}

