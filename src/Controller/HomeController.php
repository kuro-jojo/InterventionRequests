<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * 
     * @Route("/", name="app_home")
     */

    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contactUs(Request $request,  MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //mise en place de l'envoie de l'email
            $mail = (new Email())
                ->from('' . $contact->getEmail())
                ->to('kassamagan5@gmail.com')
                ->subject('Contact')
                ->html("<h1>Demande d'information</h1>
<p>Email: " . $contact->getEmail() . "<br>Nom: " . $contact->getNom() . " " . $contact->getPrenom() . "</p>
<p>" . $contact->getMessage() . "</p>");

            $mailer->send($mail);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
