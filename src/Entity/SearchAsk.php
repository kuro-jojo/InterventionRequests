<?php

namespace App\Entity;

use App\DBAL\Types\StatutType;

class SearchAsk{

    private $typeDefaillance;

    private $statutDemande;


    private $prioriteDemande;


    public function getTypeDefaillance(){

        return $this->typeDefaillance;
    }
    public function setTypeDefaillance($typeDefaillance){
        $this->typeDefaillance = $typeDefaillance;
    }
       
    public function getPrioriteDemande(){
        return $this->prioriteDemande;
    }

    public function setPrioriteDemande($prioriteDemande){
        $this->prioriteDemande = $prioriteDemande;
    }

    public function getStatutDemande(){
        return $this->statutDemande;
    }

    public function setStatutDemande($statutDemande){
        $this->statutDemande = $statutDemande;
    }
}