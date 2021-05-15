<?php

namespace App\Controller;

use App\Entity\ChefService;
use App\Entity\Contact;
use App\Entity\User;
use App\Entity\ChefPole;
use App\DBAL\Types\StatutType;
use App\Entity\DemandeIntervention;
use App\Form\ContactType;
use App\Form\DemandeInterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\Security;
use App\Repository\AgentMaintenanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DemandeInterventionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



//mettre en place la liste des demande pour un agent de pole
class AskManagementController extends AbstractController
{

    public const ROLE_CHEF_POLE = 'ROLE_CHEF_POLE';
    public const ROLE_CHEF_SERVICE = 'ROLE_CHEF_SERVICE';

    /**
     * @IsGranted("ROLE_CHEF")
     *@Route("/list", name="_list")
     * 
     */
    public function listAsk(Security $security, Request $request, DemandeInterventionRepository $askRepository,AgentMaintenanceRepository $agentRepository): Response
    {
        $demandes = new DemandeIntervention();
        $form = $this->createForm(DemandeInterventionType::class, $demandes);
        $form->handleRequest($request);

        //Agent for this specific pole
        $agents = null;

        $chef = $security->getUser();
        if ($this->isGranted($this::ROLE_CHEF_POLE)) {
            $monPole = $chef->getMonPole();
            $demandes = $askRepository->findByPoleConcerne($monPole);
            $agents = $agentRepository->findByPole($monPole);
            // recurperons les traiteurs de demande
            foreach ($demandes as $demande){
                //verifions si un agent traite la demande 
                foreach ($agents as $key => $agent){
                    //vérifier si l'agent is in $demande->getTraiteursDemande()
                    if ( $demande->getTraiteursDemande()->contains($agent)){
                        unset($agents[$key]);
                    }
                }
            }
            
        } elseif ($this->isGranted($this::ROLE_CHEF_SERVICE)) {
            $demandes = $askRepository->findAll();
        }
         return $this->render('ask_management/listDemandes.html.twig', [
            'form' => $form->createView(),
            'demandes' => $demandes,
            'agents'=>$agents
        ]);
    }

    /**
     * 
     * @Route("/assign/{id<\d+>}", name="_assign")
     */
    public function assignAsk(DemandeIntervention $demande,Request $request,AgentMaintenanceRepository $agentRepository,EntityManagerInterface $em): Response
    {
        $agentIds = $request->request->all();
        foreach ($agentIds as $id){
            TODO: "Vérifier si l'agent a déjà été assigné à cette intervention";
            $demande->addTraiteursDemande($agentRepository->find($id));
        }
        if ($demande->getStatut() != StatutType::EN_COURS) {
            $demande->setStatut(StatutType::EN_COURS);
        }
        $em->flush();
        return $this->redirectToRoute('app_ask_list');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contactUs(Request $request,  MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //mise en place de l'envoie de l'email
            $mail = (new Email())
                ->from(''.$contact->getEmail())
                ->to('doumgouyesh@gmail.com')
                ->subject('Contact')
                ->html("<h1>Demande d'information</h1>
<p>Email: ".$contact->getEmail()."<br>Nom: ".$contact->getNom()." ".$contact->getPrenom()."</p>
<p>".$contact->getMessage()."</p>");

            $mailer->send($mail);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('ask_management/contact.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
