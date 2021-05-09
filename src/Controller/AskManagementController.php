<?php

namespace App\Controller;

use App\Entity\ChefPole;
use App\Entity\DemandeIntervention;
use App\Form\DemandeInterventionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;

//mettre en place la liste des demande pour un agent de pole
class AskManagementController extends AbstractController
{
    #[Route('/ask/management', name: 'ask_management')]
    public function index(): Response
    {
        return $this->render('ask_management/index.html.twig', [
            'controller_name' => 'AskManagementController',
        ]);
    }

    #[Route('/pole/chef', name: 'pole_chef')]
    public function showPoleChef(Security $security, Request $request, DemandeInterventionRepository $repository)
    {
        $demande = new DemandeIntervention();
        $form = $this->createForm(DemandeInterventionType::class, $demande);
        $form->handleRequest($request);

        //instance d'un chef de pole
        $chefPole = new ChefPole();

        $chefPole = $security->getUser();
        if ($this->isGranted('ROLE_CHEF_POLE')){
            $monPole = $chefPole->getMonPole();
            dump($monPole);
            $demande = $repository->findByPoleConcerne($monPole);
            
            return $this->render('ask_management/chefpole.html.twig', [
                'form' => $form->createView(),
                'demandes' => $demande
            ]);
        }
        elseif ($this->isGranted('ROLE_CHEF_SERVICE')){
            $demande = $repository->findAll();
            return $this->render('ask_management/chefpole.html.twig', [
                'form' => $form->createView(),
                'demandes' => $demande
            ]);
        }
        
        return $this->redirectToRoute('app_login');
        
        
        
        
        
    }
}
