<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\ChambreRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Projet5Controller extends AbstractController
{
     
    #[Route('/projet5/home', name: 'home')]
    #[Route('/', name: 'home1')]
    public function home(ChambreRepository $repo)
    {
        $chambres = $repo->findAll();
        return $this->render("projet5/home.html.twig", [
            'Chambres' => $chambres
        ]);
    }

    #[Route('/projet5/show', name: 'show')]
    public function show(ChambreRepository $repo)
    {
        $chambres = $repo->findAll();
        return $this->render("projet5/show.html.twig", [
            'Chambres' => $chambres
        ]);
    }
    

    #[Route('/projet5', name: 'app_projet5')]
    public function index(ChambreRepository $repo): Response
    {
        return $this->render('projet5/index.html.twig', [
            'chmbr' => $repo->findAll()
        ]);
    }
    #[Route('/projet5/reservation/{id}', name: 'reservation_chambre')]
    public function reservation(Chambre $chambre = null, EntityManagerInterface $manager, Request $rq)
    {
        if (!$chambre)
            return $this->redirectToRoute('app_projet5');

        $commande = new Commande;
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $commande->setUser($user);
            $commande->setDateEnregistrement(new \DateTime());
            $commande->setChambre($chambre);
            $commande->setPrenom($user->getPrenom());
            $commande->setNom($user->getNom());
            $commande->setEmail($user->getEmail());
            


            $depart = $commande->getDateDepart();
            if ($depart->diff($commande->getDateArrivee())->invert == 1) {
                $this->addFlash('danger', 'Une période de temps ne peut pas être négative.');
                return $this->redirectToRoute('reservation_chambre', [
                    'id' => $chambre->getId()
                ]);
            }
            $jours = $depart->diff($commande->getDateArrivee())->days;
            $prixTotal = ($commande->getChambre()->getPrixJournalier() * $jours) + $commande->getChambre()->getPrixJournalier();
            $commande->setPrixTotal($prixTotal);
            $commande->setPrixTotal($prixTotal);

            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Votre reservation a bien été enregistrée !');
            return $this->redirectToRoute('profil');
        }

        return $this->renderForm('projet5/reservation.html.twig', [
            'form' => $form,
            'cham' => $chambre
        ]);
    }

    #[Route('/projet5/profil', name: 'profil')]
    public function profil(CommandeRepository $repo)
    {
        $commandes = $repo->findBy(['user' => $this->getUser()]);

        return $this->render("projet5/profil.html.twig", [
            'commandes' => $commandes
        ]);
    }










}
