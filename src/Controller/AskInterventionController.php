<?php

namespace App\Controller;

use App\DBAL\Types\StatutType;
use App\Entity\DemandeIntervention;
use App\Form\DemandeInterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AskInterventionController extends AbstractController
{
    /**
     * @Route("/ask", name="app_ask_intervention")
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
            $demande->setDateDemande((new \DateTime('now')));
            $demande->setStatut(StatutType::EN_ATTENTE);
            $em->persist($demande);
            $em->flush();

            // $flashy->info("Votre demande a bien été envoyée !");
            return $this->redirectToRoute('app_login');

        }
        return $this->render('ask_intervention/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
