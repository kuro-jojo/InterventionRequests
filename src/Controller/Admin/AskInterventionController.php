<?php

namespace App\Controller\Admin;

use App\DBAL\Types\StatutType;
use App\Entity\Contact;
use App\Entity\DemandeIntervention;
use App\Form\ContactType;
use App\Form\DemandeInterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Flasher\Prime\FlasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AskInterventionController extends AbstractController
{
    /**
     * @Route("/ask", name="app_ask_intervention")
     * @param Request $request
     * @return Response
     */
    public function demandeIntervention(Request $request, EntityManagerInterface $em,FlasherInterface $flasher): Response
    {
        $demande = new DemandeIntervention();

        $form = $this->createForm(DemandeInterventionType::class, $demande);
        $form->handleRequest($request);

        //traitement des requêtes
        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setDateDemande((new \DateTime('now')));
            $demande->setStatut(StatutType::EN_ATTENTE);
            $em->persist($demande);
            $em->flush();

            $flasher->addSuccess('Votre demande a bien été envoyée. Vous recevrez régulièrement des mails sur son avancement');
            // $flashy->info("Votre demande a bien été envoyée !");
            return $this->redirectToRoute('app_home');
        }
        return $this->render('ask_intervention/ask-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
