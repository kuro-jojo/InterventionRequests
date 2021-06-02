<?php

namespace App\Controller;

use App\DBAL\Types\StatutType;
use App\Entity\DemandeIntervention;
use Flasher\Prime\FlasherInterface;
use App\Form\DemandeInterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\Security;
use App\Repository\AgentMaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Symfony\Component\Security\Core\User\User;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
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
    public const ROLE_AGENT = 'ROLE_AGENT';

    /**
     *
     * @Route("/list", name="_list")
     * 
     */
    public function listAsk(Security $security, Request $request, DemandeInterventionRepository $askRepository, AgentMaintenanceRepository $agentRepository, EntityManagerInterface $em): Response
    {
        if ($this->isGranted($this::ROLE_AGENT) or $this->isGranted($this::ROLE_CHEF_POLE) or $this->isGranted($this::ROLE_CHEF_SERVICE)){
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
            elseif ($this->isGranted($this::ROLE_AGENT)){
                //liste des interventions pour l'agent en cours.
                dump($chef);
                //creonsl a méthode dans le repository
                $demandes = $em->createQuery('SELECT b from App\Entity\DemandeIntervention b inner join b.traiteursDemande a')->getResult();
            }
            return $this->render('ask_management/listDemandes.html.twig', [
                'demandes' => $demandes,
                'agents' => $agentsAvailable
            ]);
        }
        else{
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @IsGranted("ROLE_AGENT")
     * @Route("/manage/{id<\d+>}", name="_manage")
     * @param DemandeIntervention $demande
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param FlasherInterface $flasher
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function gererDemande(DemandeIntervention $demande, Request $request, EntityManagerInterface $em, FlasherInterface $flasher, MailerInterface $mailer): Response
    {
        $form = $this->createForm(DemandeInterventionType::class, $demande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $demande->setStatut(StatutType::OK);
            $em->flush();
            $flasher->addInfo("Intervention terminée!!");
            $mail = (new Email())
                ->from('kassamagan5@gmail.com')
                ->to('' . $demande->getEmailDemandeur())
                ->subject('Mise à jour demande Intervention')
                ->html("<h1>Mise à jour de la demande</h1>
<p>Votre demande d'intervention à propos de ". $demande->getPoleConcerne()->getNomPole() ." au niveau de ".$demande->getDepartement()." a été traitée.</p>");

            $mailer->send($mail);

            //mettre ici le message flash assurant.
            return $this->redirectToRoute('app_ask_list');
        }

        return $this->render('ask_management/mangeOne.html.twig', [
            'demande' => $demande,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/begin/{id<\d+>}", name="_begin")
     */
    public function beginIntervention(DemandeIntervention $demande, MailerInterface $mailer, Request $request, DemandeInterventionRepository $repo, EntityManagerInterface $em, FlasherInterface $flasher):Response
    {
        $demande->setStatut(StatutType::EN_COURS);
        $demande->setDateIntervention(new \DateTime('now'));
        $id = $demande->getId();
        $em->flush();
        $flasher->addInfo("L'intervention est desormais en cours");

        $mail = (new Email())
            ->from('kassamagan5@gmail.com')
            ->to(''.$demande->getEmailDemandeur())
            ->subject('Mise à jour demande Intervention')
            ->html("<h1>Mise à jour de la demande</h1>
<p>Votre demande d'intervention à propos de ". $demande->getPoleConcerne()->getNomPole() ." au niveau de ".$demande->getDepartement()." est en cours de traitement</p>");

        $mailer->send($mail);

        return $this->redirectToRoute('app_ask_manage', [
            'id' => $id,
        ]);
    }

//    /**
//     * @param DemandeIntervention $demande
//     * @param EntityManagerInterface $em
//     * @return Response
//     * @Route("/end/{id<\d+>}", name="_end")
//     */
//    public function endIntervention(DemandeIntervention $demande, MailerInterface $mailer, EntityManagerInterface $em, FlasherInterface $flasher):Response
//    {
//        $demande->setStatut(StatutType::OK);
//        $em->flush();
//        $flasher->addInfo("Intervention terminée!!");
//        $mail = (new Email())
//            ->from('kassamagan5@gmail.com')
//            ->to('' . $demande->getEmailDemandeur())
//            ->subject('Mise à jour demande Intervention')
//            ->html("<h1>Mise à jour de la demande</h1>
//<p>Votre demande d'intervention à propos de ". $demande->getPoleConcerne()->getNomPole() ." au niveau de ".$demande->getDepartement()." a été traitée.</p>");
//
//        $mailer->send($mail);
//        return $this->redirectToRoute('app_ask_list');
//    }

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

     //affichage des statistiques

    /**
     *
     * @Route("/stat", name="_stat")
     */
     public function statistique(DemandeInterventionRepository $askRepository, EntityManagerInterface $em): Response
     {

         //recuperation pour les statuts
         $enAttenteNbre = 0;
         $encoursNbre = 0;
         $okNbre = 0;
         $enAttenteNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = '.'\'EN_ATTENTE\'')->getSingleScalarResult();
         $encoursNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = '.'\'EN_COURS\'')->getSingleScalarResult();
         $okNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = '.'\'OK\'')->getSingleScalarResult();

         $UNNbre = 0;
         $DUnbre = 0;
         $DPNbre = 0;
         $ANbre = 0;
         $UNNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'UsureNormal\'')->getSingleScalarResult();
         $DUnbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'DefautUtilisateur\'')->getSingleScalarResult();
         $DPNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'DefautProduit\'')->getSingleScalarResult();
         $ANbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'Autres\'')->getSingleScalarResult();

         $causeData = [$UNNbre, $DUnbre, $DPNbre, $ANbre];

         $urgentNbre = 0;
         $peuUrgentNbre = 0;
         $pasUrgentNbre = 0;

         $urgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = '.'\'Urgent\'')->getSingleScalarResult();
         $peuUrgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = '.'\'PeuUrgent\'')->getSingleScalarResult();
         $pasUrgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = '.'\'PasUrgent\'')->getSingleScalarResult();

         $prioriteData = [$urgentNbre, $peuUrgentNbre, $pasUrgentNbre];

         $menuiserieNbre = 0;
         $elecNbre = 0;
         $maconNbre = 0;
         $climaNbre = 0;
         $plombNbre = 0;

         $menuiserieNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = '.'\'Menuiserie\')')->getSingleScalarResult();
         $elecNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = '.'\'Electricite\')')->getSingleScalarResult();
         $maconNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = '.'\'Maconnerie\')')->getSingleScalarResult();
         $climaNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = '.'\'Climatisation\')')->getSingleScalarResult();
         $plombNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.poleConcerne in (select p.id from App\Entity\Pole p where p.nomPole = '.'\'Plomberie\')')->getSingleScalarResult();

         $typeData = [$menuiserieNbre, $elecNbre, $maconNbre, $climaNbre, $plombNbre];
         return $this->render('ask_management/stat.html.twig',[
             'enAttenteNbre' => $enAttenteNbre,
             'okNbre' => $okNbre,
             'encoursNbre' => $encoursNbre,
             'dataCauses' => $causeData,
             'typeData' => $typeData,
             'prioriteData' => $prioriteData,
         ]);
     }

   
}
