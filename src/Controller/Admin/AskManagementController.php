<?php

namespace App\Controller\Admin;

use App\DBAL\Types\StatutType;
use App\Entity\DemandeIntervention;
use Flasher\Prime\FlasherInterface;
use App\Form\DemandeInterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use App\Repository\AgentMaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
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
        return $this->render('admin/ask_management/listDemandes.html.twig', [
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

     //affichage des statistiques

    /**
     * @IsGranted("ROLE_CHEF_SERVICE")
     * @Route("/stat", name="_stat")
     */
    // public function statistique(DemandeInterventionRepository $askRepository, ChartBuilderInterface $chartBuilder, EntityManagerInterface $em): Response
    // {

    //     //recuperation pour les statuts
    //     $enAttenteNbre = 0;
    //     $encoursNbre = 0;
    //     $okNbre = 0;
    //     $enAttenteNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = '.'\'EN_ATTENTE\'')->getSingleScalarResult();
    //     $encoursNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = '.'\'EN_COURS\'')->getSingleScalarResult();
    //     $okNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.statut = '.'\'OK\'')->getSingleScalarResult();        

    //     $chart1 = $chartBuilder->createChart(Chart::TYPE_BAR);
    //     $chart1->setData([
    //         'labels' => ['En attente', 'En cours', 'OK'],
    //         'datasets' => [
    //             [
    //                 'label' => 'Statut des demandes',
    //                 'backgroundColor' => 'rgb(255, 99, 132)',
    //                 'borderColor' => 'rgb(255, 99, 132)',
    //                 'data' => [$enAttenteNbre, $encoursNbre, $okNbre],
    //             ],
    //         ],
    //     ]);
    //     $chart1->setOptions([
    //         'scales' => [
    //             'y' => [
    //                 'beginAtZero' => true,
    //             ]
    //         ]
    //     ]);

    //     //les causes:UsureNormal, DefautUtilisateur, DefautProduit, Autres

    //     $UNNbre = 0;
    //     $DUnbre = 0;
    //     $DPNbre = 0;
    //     $ANbre = 0;
    //     $UNNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'UsureNormal\'')->getSingleScalarResult();
    //     $DUnbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'DefautUtilisateur\'')->getSingleScalarResult();
    //     $DPNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'DefautProduit\'')->getSingleScalarResult();
    //     $ANbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.causeDefaillance = '.'\'Autres\'')->getSingleScalarResult();

    //     $chart2 = $chartBuilder->createChart(Chart::TYPE_PIE);
    //     $chart2->setData([
    //         'labels' => ['Usure Normale', 'Défaut Utilisateur', 'Défaut Produit', 'Autres'],
    //         'datasets' => [
    //             [
    //                 'label' => 'Causes défaillance',
    //                 'backgroundColor' => [
    //                     'rgb(255, 99, 132)',
    //                     'rgb(100, 55, 130)',
    //                     'rgb(200, 45, 135)',
    //                     'rgb(156, 74, 139)',
    //                 ],
    //                 'borderColor' => [
    //                     'rgb(255, 99, 132)',
    //                     'rgb(100, 55, 130)',
    //                     'rgb(200, 45, 135)',
    //                     'rgb(156, 74, 139)',
    //                 ],
    //                 'data' => [$UNNbre, $DUnbre, $DPNbre, $ANbre],
    //             ],
    //         ],
    //     ]);
    //     //$chart2->setOptions([
    //       //  'scales' => [
    //          //   'yAxes' => [
    //          //       ['ticks' => ['min' => 0],
    //          //   ]
    //         //]]
    //     //]);

    //     $urgentNbre = 0;
    //     $peuUrgentNbre = 0;
    //     $pasUrgentNbre = 0;

    //     $urgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = '.'\'Urgent\'')->getSingleScalarResult();
    //     $peuUrgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = '.'\'PeuUrgent\'')->getSingleScalarResult();
    //     $pasUrgentNbre = $em->createQuery('SELECT count(d) from App\Entity\DemandeIntervention d where d.priorite = '.'\'PasUrgent\'')->getSingleScalarResult();
    //     $chart3 = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
    //     $chart3->setData([
    //         'labels' => ['Urgente', 'Peu Urgente', 'Pas Urgente'],
    //         'datasets' => [
    //             [
    //                 'label' => 'Priorité des demandes',
    //                 'backgroundColor' => [
    //                     'rgb(255, 99, 132)',
    //                     'rgb(100, 55, 130)',
    //                     'rgb(200, 45, 135)',
    //                 ],
    //                 'borderColor' => [
    //                     'rgb(255, 99, 132)',
    //                     'rgb(100, 55, 130)',
    //                     'rgb(200, 45, 135)',
    //                 ],
    //                 'data' => [$urgentNbre, $peuUrgentNbre, $pasUrgentNbre],
    //             ],
    //         ],
    //     ]);
    //     $chart3->setOptions([
    //         'scales' => [
    //             'y' => [
    //                 'beginAtZero' => true,
    //             ]
    //         ]
    //     ]);

    //     return $this->render('ask_management/stat.html.twig',[
    //         'chart' => $chart1,
    //         'chart2' => $chart2,
    //         'chart3' => $chart3
    //     ]);
    // }
   
}
