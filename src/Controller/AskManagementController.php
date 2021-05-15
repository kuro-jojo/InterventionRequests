<?php

namespace App\Controller;

use App\DBAL\Types\StatutType;
use App\Entity\DemandeIntervention;
use App\Form\DemandeInterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Repository\AgentMaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Flasher\Prime\FlasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/ask",name="app_ask")
 */

//mettre en place la liste des demande pour un agent de pole
class AskManagementController extends AbstractController
{

    public const ROLE_CHEF_POLE = 'ROLE_CHEF_POLE';
    public const ROLE_CHEF_SERVICE = 'ROLE_CHEF_SERVICE';

    /**
     * @IsGranted("ROLE_CHEF")
     * @Route("/list", name="_list")
     * 
     */
    public function listAsk(Security $security, Request $request, DemandeInterventionRepository $askRepository, AgentMaintenanceRepository $agentRepository): Response
    {
        $demandes = [];
        //Agent for this specific pole
        $agents = null;
        //Contains all available agent on a specific request
        $agentsAvailable = array();

        $chef = $security->getUser();
        if ($this->isGranted($this::ROLE_CHEF_POLE)) {
            $monPole = $chef->getMonPole();
            $demandes = $askRepository->findByPoleConcerne($monPole);
            $agents = $agentRepository->findByPole($monPole);

            // For each , we assign available agent on the pole
            foreach ($demandes as $demande) {
                $agentsAvailable[$demande->getId()]= array();
                //verifions si un agent traite la demande 
                foreach ($agents as $agent) {
                    //vérifier si l'agent is in $demande->getTraiteursDemande()
                    if (!$demande->getTraiteursDemande()->contains($agent)) {
                        array_push($agentsAvailable[$demande->getId()],$agent);
                    }
                }
            }
        } elseif ($this->isGranted($this::ROLE_CHEF_SERVICE)) {
            $demandes = $askRepository->findAll();
        }
        return $this->render('ask_management/listDemandes.html.twig', [
            'demandes' => $demandes,
            'agents' => $agentsAvailable
        ]);
    }

    /**
     * 
     * @Route("/assign/{id<\d+>}", name="_assign")
     */
    public function assignAsk(DemandeIntervention $demande, Request $request, AgentMaintenanceRepository $agentRepository, EntityManagerInterface $em,FlasherInterface $flasher): Response
    {
        $agentIds = $request->request->all();
        foreach ($agentIds as $id) {
            TODO:
            "Vérifier si l'agent a déjà été assigné à cette intervention";
            $demande->addTraiteursDemande($agentRepository->find($id));
        }
        if ($demande->getStatut() != StatutType::EN_COURS) {
            $demande->setStatut(StatutType::EN_COURS);
        }
        $flasher->addInfo("L'intervention a bien été assignée!!!");
        $em->flush();
        return $this->redirectToRoute('app_ask_list');
    }

   
}
