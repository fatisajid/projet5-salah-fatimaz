<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\AdminCommandeType;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCrudCommandesController extends AbstractController
{
    #[Route('/admin/crud/commandes', name: 'app_admin_crud_commandes')]
    public function index(CommandeRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Commande::class)->getFieldNames();
        $coms = $repo->findAll();

        return $this->render('admin_crud_commandes/index.html.twig', [
            'coms' => $coms,
            'colonnes' => $colonnes
        ]);
    }
     /**
     * @Route("/admin/crud/commandes/new", name="admin_crud_commandes_new")
     * @Route("/admin/crud/commandes/edit/{id}", name="admin_crud_commandes_edit")
     */
    public function form(Commande $commande = null, EntityManagerInterface $manager, Request $rq, UserRepository $repo)
    {
        if (!$commande) {
            $commande = new Commande;
            $commande->setDateEnregistrement(new \DateTime());
            



        }

        $form = $this->createForm(AdminCommandeType::class, $commande);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repo->find($form->get("user")->getData());
            $commande->setNom($user->getNom());
            $commande->setPrenom($user->getPrenom());
            $commande->setTelephone($form->get("telephone")->getData());
            $depart = $commande->getDateDepart();

            if ($depart->diff($commande->getDateArrivee())->invert == 1) {
                $this->addFlash('danger', 'Une période de temps ne peut pas être négative.');
                if ($commande->getId())
                    return $this->redirectToRoute('admin_crud_commandes_edit', [
                        'id' => $commande->getId()
                    ]);
                else
                    return $this->redirectToRoute('admin_crud_commandes_new');
            }

            $days = $depart->diff($commande->getDateArrivee())->days;
            $prixTotal = ($commande->getChambre()->getPrixJournalier() * $days) + $commande->getChambre()->getPrixJournalier();
            $commande->setPrixTotal($prixTotal);

            $manager->persist($commande);
            $manager->flush();
            return $this->redirectToRoute('app_admin_crud_commandes');
        }
        return $this->renderForm('admin_crud_commandes/form.html.twig', [
            'form' => $form,
            'editMode' => $commande->getId() != NULL
        ]);
    }

    /**
     * @Route("/admin/crud/commandes/delete/{id}", name="admin_crud_commandes_delete")
     */
    public function delete(Commande $commande = null, EntityManagerInterface $manager)
    {

        if ($commande) {
            $manager->remove($commande);
            $manager->flush();
            $this->addFlash('success', 'La commande a bien été supprimée !');
        }
        return $this->redirectToRoute('app_admin_crud_commandes');
    }










}
