<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Service\InterventionCount;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 * @Route("/admin", name="app_admin")
 */
class AdminController extends AbstractController
{
    private const ROLE_CHEF_SERVICE = "ROLE_CHEF_SERVICE";
    private const ROLE_CHEF_POLE = "ROLE_CHEF_POLE";
    private const ROLE_AGENT = "ROLE_AGENT";

    private $askRepository;
    private $userRepository;

    public function __construct(DemandeInterventionRepository $askRepository, UserRepository $userRepository)
    {
        $this->askRepository = $askRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/",name="")
     */
    public function index(InterventionCount $interventionCount): Response
    {

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
                } else if ($demande->getStatut() == "EN_COURS") {
                    $numberOfInterventionsOnGoing++;
                }
            }
        }


        return $this->render('admin/index.html.twig', [
            'numberOfInterventions' => $numberOfInterventions,
            'numberOfInterventionsDone' => $numberOfInterventionsDone,
            'numberOfInterventionsOnGoing' => $numberOfInterventionsOnGoing,
            'numberOfAgents' => $numberOfAgents,
        ]);
    }
}
