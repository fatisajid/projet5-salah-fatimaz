<?php

namespace App\Controller;

use App\Entity\Chambre;
use App\Form\ChambreType;
use App\Repository\ChambreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCrudChambresController extends AbstractController
{
    #[Route('/admin/crud/chambres', name: 'app_admin_crud_chambres')]
    public function index(ChambreRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Chambre::class)->getFieldNames();
        $chmbr = $repo->findAll();

        return $this->render('admin_crud_chambres/index.html.twig', [
            'colonnes' => $colonnes,
             'chmbr' => $chmbr
        ]);
    }
     /**
     * @Route("/admin/crud/chambres/new", name="admin_crud_chambres_new")
     * @Route("/admin/crud/chambres/edit/{id}", name="admin_crud_chambres_edit")
     */
    public function form(Chambre $chambre = null, Request $rq, EntityManagerInterface $manager)
    {
        if (!$chambre)
        {
            $chambre = new Chambre;
            $chambre->setDateEnregistrement(new \DateTime());
        }
        $form = $this->createForm(ChambreType::class, $chambre);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($chambre);
            $manager->flush();
            return $this->redirectToRoute('app_admin_crud_chambres');
        }
        return $this->renderForm('admin_crud_chambres/form.html.twig', [
            'form' => $form,
            'editMode' => $chambre->getId() != NULL
        ]);
    }

    /**
     * @Route("/admin/crud/chambres/delete/{id}", name="admin_crud_chambres_delete")
     */
    public function delete(Chambre $chambre = null, EntityManagerInterface $manager)
    {
        if ($chambre) {
            $manager->remove($chambre);
            $manager->flush();
            $this->addFlash('success', 'La chambre a bien été supprimé !');
        }
        return $this->redirectToRoute('app_admin_crud_chambres');
    }





}
