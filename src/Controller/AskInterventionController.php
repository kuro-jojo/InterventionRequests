<?php

namespace App\Controller;

use App\Entity\DemandeIntervention;
use App\Form\DemandeInterventionType;
use App\Repository\DemandeInterventionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AskInterventionController extends AbstractController
{
    /**
     * @Route("/demandeIntervention", name="demande_inter")
     * @param Request $request
     * @return Response
     */
    public function demandeIntervention(Request $request, DemandeInterventionRepository $repository, EntityManagerInterface $em): Response
    {
        $demande = new DemandeIntervention();

        $form = $this->createForm(DemandeInterventionType::class, $demande);
        $form->handleRequest($request);

        //traitement des requêtes
        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($demande);
            $em->flush();

            return $this->redirectToRoute('app_login');

        }
        return $this->render('ask_intervention/index.html.twig', [
            'controller_name' => 'AskInterventionController',
            'form' => $form->createView(),
        ]);
    }
}
