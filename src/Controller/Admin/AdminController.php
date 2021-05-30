<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @IsGranted("ROLE_CHEF")
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
    public function index(): Response
    {
        $numberOfInterventions = $this->askRepository->getNumberOfAsk();
        $numberOfInterventionsDone = $this->askRepository->getNumberOfAskByStatus("OK");
        $numberOfInterventionsOnGoing = $this->askRepository->getNumberOfAskByStatus("EN_COURS");

        $agents = $this->userRepository->findAll();

        $numberOfAgents = 0;
        foreach ($agents as $agent) {
            if ($this->isGranted($this::ROLE_CHEF_POLE)) {
                if (in_array($this::ROLE_AGENT, $agent->getRoles())) {
                    $numberOfAgents++;
                }
            } else {
                $numberOfAgents++;
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
