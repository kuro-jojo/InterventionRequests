<?php

namespace App\DataFixtures;

use App\DBAL\Types\PoleType;
use App\Entity\AgentMaintenance;
use App\Entity\ChefPole;
use App\Entity\ChefService;
use App\Entity\Pole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{   
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $chefService = new ChefService;

        $chefService->setNom("Dieng");
        $chefService->setPrenom("Prince");
        $chefService->setRoles(["ROLE_CHEF_SERVICE"]);
        $chefService->setPassword($this->encoder->encodePassword($chefService,"toto"));
        $chefService->setEmail("prince@example.com");

        $pole1 = new Pole;
        $pole1->setNomPole(PoleType::MACONNERIE);

        $pole2 = new Pole;
        $pole2->setNomPole(PoleType::PLOMBERIE);

        $pole3 = new Pole;
        $pole3->setNomPole(PoleType::ELECTRICITE);

        $pole4 = new Pole;
        $pole4->setNomPole(PoleType::MENUISERIE);

        $pole5 = new Pole;
        $pole5->setNomPole(PoleType::CLIMATISATION);

        $chefPole1 = new ChefPole;
        $chefPole1->setNom("Ka");
        $chefPole1->setPrenom("Adama");
        $chefPole1->setRoles(["ROLE_CHEF_POLE"]);
        $chefPole1->setPassword($this->encoder->encodePassword($chefPole1,"toto"));
        $chefPole1->setEmail("ka@example.com");
        $chefPole1->setMonPole($pole1);

        $chefPole2 = new ChefPole;

        $chefPole2->setNom("Faye");
        $chefPole2->setPrenom("Jean");
        $chefPole2->setRoles(["ROLE_CHEF_POLE"]);
        $chefPole2->setPassword($this->encoder->encodePassword($chefPole2,"toto"));
        $chefPole2->setEmail("faye@example.com");
        $chefPole2->setMonPole($pole2);

        

        $agent1 = new AgentMaintenance;

        $agent1->setNom("Faye");
        $agent1->setPrenom("Chris");
        $agent1->setRoles(["ROLE_CHEF_AGENT"]);
        $agent1->setPassword($this->encoder->encodePassword($agent1,"toto"));
        $agent1->setEmail("chris@example.com");
        $agent1->addMesPole($pole1);
        $agent1->addMesPole($pole4);

        $agent2 = new AgentMaintenance;

        $agent2->setNom("Ngom");
        $agent2->setPrenom("Toto");
        $agent2->setRoles(["ROLE_CHEF_AGENT"]);
        $agent2->setPassword($this->encoder->encodePassword($agent2,"toto"));
        $agent2->setEmail("toto@example.com");
        $agent2->addMesPole($pole1);
        $agent2->addMesPole($pole2);
        $agent2->addMesPole($pole3);

        $manager->persist($chefService);
        $manager->persist($chefPole1);
        $manager->persist($chefPole2);
        $manager->persist($pole1);
        $manager->persist($pole2);
        $manager->persist($pole3);
        $manager->persist($pole4);
        $manager->persist($pole5);
        $manager->persist($agent1);
        $manager->persist($agent2);
        
        $manager->flush();
    }
}
