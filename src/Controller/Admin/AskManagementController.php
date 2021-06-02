<?php

namespace App\Controller\Admin;

use App\Entity\SearchAsk;
use App\DBAL\Types\StatutType;
use App\Form\SearchAskFormType;
use Symfony\Component\Mime\Email;
use App\Service\InterventionCount;
use App\Entity\DemandeIntervention;
use Flasher\Prime\FlasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use App\Repository\AgentMaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\DemandeInterventionType;

/**
 * @Route("/ask",name="app_ask")
 */

//mettre en place la liste des demande pour un agent de pole
class AskManagementController extends AbstractController
{

    private const ROLE_CHEF_POLE = 'ROLE_CHEF_POLE';
    private const ROLE_CHEF_SERVICE = 'ROLE_CHEF_SERVICE';
    public const ROLE_AGENT = 'ROLE_AGENT';

    private $agentRepository;
    private $flasher;
    private $em;
    private $mailer;

    public function __construct(AgentMaintenanceRepository $agentRepository, FlasherInterface $flasher, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->agentRepository = $agentRepository;
        $this->flasher = $flasher;
        $this->em = $em;
        $this->mailer = $mailer;
    }
    /**
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @Route("/list/{status}", name="_list")
     * 
     */
    public function listAsk(string $status = null, Security $security, Request $request, DemandeInterventionRepository $askRepository, PaginatorInterface $paginator, InterventionCount $interventionCount): Response
    {
        $demandes = [];
        //Agent for this specific pole
        $agents = null;
        //Contains all available agent on a specific request
        $agentsAvailable = array();
        $agentsOfAsk = array();

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
                $agentsOfAsk[$demande->getId()] = array();
                // Liste des agents de cette intervention
                array_push($agentsOfAsk[$demande->getId()], $demande->getTraiteursDemande());
            }
        } elseif ($this->isGranted($this::ROLE_CHEF_SERVICE)) {
            $demandes = $askRepository->findAll();
        } elseif ($this->isGranted($this::ROLE_AGENT)) {
            //liste des interventions pour l'agent en cours.
            //creonsl a méthode dans le repository
            $demandes = $this->em->createQuery('SELECT b from App\Entity\DemandeIntervention b inner join b.traiteursDemande a')->getResult();
        }

        // filtrage

        $searchAsk = new SearchAsk;
        $form = $this->createForm(SearchAskFormType::class, $searchAsk);
        $form->handleRequest($request);

        if ($demandes && $status) {
            if ($searchAsk) {
                if ($status) {
                    $searchAsk->setStatutDemande($status);
                }

                $demandes = $paginator->paginate(
                    $askRepository->findAskBySearch($searchAsk, $this->isGranted($this::ROLE_AGENT) ? $this->getUser()->getId() : null),
                    $request->query->getInt('page', 1),
                    15
                );
            }
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
        } else if ($this->isGranted($this::ROLE_AGENT)) {
            $numberOfInterventions = $this->getUser()->getDemandeInterventions()->count();
            $numberOfInterventionsDone = 0;
            $numberOfInterventionsOnGoing = 0;
            foreach ($this->getUser()->getDemandeInterventions() as $demande) {
                if ($demande->getStatut() == "OK") {
                    $numberOfInterventionsDone++;
                }else if ($demande->getStatut() == "EN_COURS"){
                    $numberOfInterventionsOnGoing++;
                }
            }
        }

        return $this->render('admin/ask_management/listDemandes.html.twig', [
            'demandes' => $demandes,
            'agents' => $agentsAvailable,
            'form' => $form->createView(),
            'agentsOfAsk' => $agentsOfAsk,
            'status' => $status,

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
    public function assignAsk(DemandeIntervention $demande, Request $request, EntityManagerInterface $em,): Response
    {
        $agentIds = $request->request->all();
        foreach ($agentIds as $id) {
            $demande->addTraiteursDemande($this->agentRepository->find($id));
        }

        $this->flasher->addInfo("L'intervention a bien été assignée!!!");
        $em->flush();
        return $this->redirectToRoute('app_ask_list');
    }



    /**
     * @IsGranted("ROLE_AGENT")
     * @Route("/manage/{id<\d+>}", name="_manage")
     * @param DemandeIntervention $demandeIntervention
     * @param Request $request
     * @return Response
     */
    public function gererDemande(DemandeIntervention $demande, Request $request): Response
    {

        $form = $this->createForm(DemandeInterventionType::class, $demande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $demande->setStatut(StatutType::OK);
            $this->em->flush();
            $this->flasher->addInfo("Intervention terminée!!");
            $mail = (new Email())
                ->from('kassamagan5@gmail.com')
                ->to($demande->getEmailDemandeur())
                ->subject('Mise à jour demande Intervention')
                ->html("<h1>Mise à jour de la demande</h1>
<p>Votre demande d'intervention à propos de <b>" . $demande->getPoleConcerne()->getNomPole() . "</b> au niveau de " . $demande->getDepartement() . " a été traitée.</p>");

            $this->mailer->send($mail);

            //mettre ici le message flash assurant.
            return $this->redirectToRoute('app_ask_list');
        }

        return $this->render('admin/ask_management/manageOne.html.twig', [
            'demande' => $demande,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_AGENT")
     * @Route("/manage/begin/{id<\d+>}", name="_manage_begin")
     */
    public function beginIntervention(DemandeIntervention $demande, Request $request, DemandeInterventionRepository $repo,): Response
    {
        $demande->setStatut(StatutType::EN_COURS);
        $demande->setDateIntervention(new \DateTime('now'));
        $id = $demande->getId();
        $this->em->flush();
        $this->flasher->addInfo("L'intervention est desormais en cours");

        $mail = (new Email())
            ->from('kassamagan5@gmail.com')
            ->to($demande->getEmailDemandeur())
            ->subject('Mise à jour demande Intervention')
            ->html("<h1>Mise à jour de la demande</h1>
<p>Votre demande d'intervention à propos de " . $demande->getPoleConcerne()->getNomPole() . " au niveau de " . $demande->getDepartement() . " est en cours de traitement</p>");

        $this->mailer->send($mail);

        return $this->redirectToRoute('app_ask_manage', [
            'id' => $id,
        ]);
    }


    //affichage des statistiques

    /**
     * @IsGranted("ROLE_CHEF_SERVICE")
     * @Route("/stat", name="_stat")
     */
    public function statistique(EntityManagerInterface $em, InterventionCount $interventionCount): Response
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
        dd($DUnbre);
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
