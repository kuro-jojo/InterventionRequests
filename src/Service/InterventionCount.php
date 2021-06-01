<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Repository\DemandeInterventionRepository;

class InterventionCount {

    private $askRepository;
    private $userRepository;

    public function __construct(DemandeInterventionRepository $askRepository,UserRepository $userRepository){
        $this->askRepository = $askRepository;
        $this->userRepository = $userRepository;
    }

    public function getNumberOfAsk($pole_id = null){
        if ($pole_id) {
            return $this->askRepository->getNumberOfAskByPole($pole_id);
        }
        return $this->askRepository->getNumberOfAsk();
    }

    public function getNumberOfAskDone($pole_id = null){
        if ($pole_id) {
            return $this->askRepository->getNumberOfAskByStatusByPole("OK",$pole_id);
        }
        return $this->askRepository->getNumberOfAskByStatus("OK");
    }

    public function getNumberOfAskOnGoing($pole_id = null){

        if ($pole_id) {
            return $this->askRepository->getNumberOfAskByStatusByPole("EN_COURS",$pole_id);
        }
        return $this->askRepository->getNumberOfAskByStatus("EN_COURS");
    }

    public function getNumberOfAgents(bool $isAgentPole){
        $agents = $this->userRepository->findAll();

        $numberOfAgents = 0;
        foreach ($agents as $agent) {
            if ($isAgentPole) {
                if (in_array("ROLE_AGENT", $agent->getRoles())) {
                    $numberOfAgents++;
                }
            } else {
                $numberOfAgents++;
            }
        }

        return $numberOfAgents;
    }
}