<?php

namespace App\Controller\Admin;

use App\Entity\SearchAsk;
use App\DBAL\Types\StatutType;
use App\Form\SearchAskFormType;
use App\Service\InterventionCount;
use App\Entity\DemandeIntervention;
use Flasher\Prime\FlasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Repository\AgentMaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/ask",name="app_ask")
 */

//mettre en place la liste des demande pour un agent de pole
class AskManagementController extends AbstractController
{

    private const ROLE_CHEF_POLE = 'ROLE_CHEF_POLE';
    private const ROLE_CHEF_SERVICE = 'ROLE_CHEF_SERVICE';

    private $agentRepository;

    public function __construct(AgentMaintenanceRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }
    /**
     * @IsGranted("ROLE_CHEF")
     * @Route("/list", name="_list")
     * 
     */
    public function listAsk(Security $security, Request $request, DemandeInterventionRepository $askRepository, PaginatorInterface $paginator, InterventionCount $interventionCount): Response
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
            $agents = $this->agentRepository->findByPole($monPole);

            // For each , we assign available agent on the pole
            foreach ($demandes as $demande) {
                $agentsAvailable[$demande->getId()] = array();
                //verifions si un agent traite la demande 
                foreach ($agents as $agent) {
                    //vérifier si l'agent is in $demande->getTraiteursDemande()
                    if (!$demande->getTraiteursDemande()->contains($agent)) {
                        array_push($agentsAvailable[$demande->getId()], $agent);
                    }
                }
            }
        } elseif ($this->isGranted($this::ROLE_CHEF_SERVICE)) {
            $demandes = $askRepository->findAll();
        }


        // filtrage
        $searchAsk = new SearchAsk;
        $form = $this->createForm(SearchAskFormType::class, $searchAsk);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $demandes = $paginator->paginate(
                $askRepository->findAskBySearch($searchAsk),
                $request->query->getInt('page', 1),
                15
            );
        }

        // Les nombres de demandes
        $numberOfInterventions = $interventionCount->getNumberOfAsk();
        $numberOfInterventionsDone = $interventionCount->getNumberOfAskDone();
        $numberOfInterventionsOnGoing = $interventionCount->getNumberOfAskOnGoing();
        $numberOfAgents = $interventionCount->getNumberOfAgents(false);

        if ($this->isGranted($this::ROLE_CHEF_POLE)) {
            $numberOfInterventions = $interventionCount->getNumberOfAsk($this->getUser()->getMonPole()->getId());
            $numberOfInterventionsDone = $interventionCount->getNumberOfAskDone($this->getUser()->getMonPole()->getId());
            $numberOfInterventionsOnGoing = $interventionCount->getNumberOfAskOnGoing($this->getUser()->getMonPole()->getId());
            $numberOfAgents = $interventionCount->getNumberOfAgents(true);
        }
       
        return $this->render('admin/ask_management/listDemandes.html.twig', [
            'demandes' => $demandes,
            'agents' => $agentsAvailable,
            'form' => $form->createView(),

            'numberOfInterventions' => $numberOfInterventions,
            'numberOfInterventionsDone' => $numberOfInterventionsDone,
            'numberOfInterventionsOnGoing' => $numberOfInterventionsOnGoing,
            'numberOfAgents' => $numberOfAgents,
        ]);
    }

    /**
     * 
     * @Route("/assign/{id<\d+>}", name="_assign")
     */
    public function assignAsk(DemandeIntervention $demande, Request $request, EntityManagerInterface $em, FlasherInterface $flasher): Response
    {
        $agentIds = $request->request->all();
        foreach ($agentIds as $id) {
            TODO:
            "Vérifier si l'agent a déjà été assigné à cette intervention";
            $demande->addTraiteursDemande($this->agentRepository->find($id));
        }
        if ($demande->getStatut() != StatutType::EN_COURS) {
            $demande->setStatut(StatutType::EN_COURS);
        }
        $flasher->addInfo("L'intervention a bien été assignée!!!");
        $em->flush();
        return $this->redirectToRoute('app_ask_list');
    }

    //affichage des statistiques

    /**
     * @IsGranted("ROLE_CHEF_SERVICE")
     * @Route("/stat", name="_stat")
     */
    public function statistique(EntityManagerInterface $em,InterventionCount $interventionCount): Response
    {

        //recuperation pour les statuts
        $enAttenteNbre = 0;
        $encoursNbre = 0;
        $okNbre = 0;
        $enAttenteNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = ' . '\'EN_ATTENTE\'')->getSingleScalarResult();
        $encoursNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = ' . '\'EN_COURS\'')->getSingleScalarResult();
        $okNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = ' . '\'OK\'')->getSingleScalarResult();

        $UNNbre = 0;
        $DUnbre = 0;
        $DPNbre = 0;
        $ANbre = 0;
        $UNNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = ' . '\'UsureNormal\'')->getSingleScalarResult();
        $DUnbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = ' . '\'DefautUtilisateur\'')->getSingleScalarResult();
        $DPNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = ' . '\'DefautProduit\'')->getSingleScalarResult();
        $ANbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = ' . '\'Autres\'')->getSingleScalarResult();

        $causeData = [$UNNbre, $DUnbre, $DPNbre, $ANbre];

        $urgentNbre = 0;
        $peuUrgentNbre = 0;
        $pasUrgentNbre = 0;

        $urgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = ' . '\'Urgent\'')->getSingleScalarResult();
        $peuUrgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = ' . '\'PeuUrgent\'')->getSingleScalarResult();
        $pasUrgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = ' . '\'PasUrgent\'')->getSingleScalarResult();

        $prioriteData = [$urgentNbre, $peuUrgentNbre, $pasUrgentNbre];

        $menuiserieNbre = 0;
        $elecNbre = 0;
        $maconNbre = 0;
        $climaNbre = 0;
        $plombNbre = 0;

        $menuiserieNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = ' . '\'Menuiserie\')')->getSingleScalarResult();
        $elecNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = ' . '\'Electricite\')')->getSingleScalarResult();
        $maconNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = ' . '\'Maconnerie\')')->getSingleScalarResult();
        $climaNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = ' . '\'Climatisation\')')->getSingleScalarResult();
        $plombNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = ' . '\'Plomberie\')')->getSingleScalarResult();

        $typeData = [$menuiserieNbre, $elecNbre, $maconNbre, $climaNbre, $plombNbre];


        // Les nombres de demandes
        $numberOfInterventions = $interventionCount->getNumberOfAsk();
        $numberOfInterventionsDone = $interventionCount->getNumberOfAskDone();
        $numberOfInterventionsOnGoing = $interventionCount->getNumberOfAskOnGoing();
        $numberOfAgents = $interventionCount->getNumberOfAgents(false);

        if ($this->isGranted($this::ROLE_CHEF_POLE)) {
            $numberOfInterventions = $interventionCount->getNumberOfAsk($this->getUser()->getMonPole()->getId());
            $numberOfInterventionsDone = $interventionCount->getNumberOfAskDone($this->getUser()->getMonPole()->getId());
            $numberOfInterventionsOnGoing = $interventionCount->getNumberOfAskOnGoing($this->getUser()->getMonPole()->getId());
            $numberOfAgents = $interventionCount->getNumberOfAgents(true);
        }
        return $this->render('admin/ask_management/stat.html.twig', [
            'enAttenteNbre' => $enAttenteNbre,
            'okNbre' => $okNbre,
            'encoursNbre' => $encoursNbre,
            'dataCauses' => $causeData,
            'typeData' => $typeData,
            'prioriteData' => $prioriteData,


            'numberOfInterventions' => $numberOfInterventions,
            'numberOfInterventionsDone' => $numberOfInterventionsDone,
            'numberOfInterventionsOnGoing' => $numberOfInterventionsOnGoing,
            'numberOfAgents' => $numberOfAgents,
        ]);
    }
}
