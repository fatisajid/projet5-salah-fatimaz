<?php

namespace App\Controller;

use App\Entity\Slider;
use App\Form\SliderType;
use App\Repository\SliderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCrudSlidersController extends AbstractController
{
    #[Route('/admin/crud/sliders', name: 'app_admin_crud_sliders')]
    public function index(SliderRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Slider::class)->getFieldNames();
        $sldr = $repo->findAll();

        return $this->render('admin_crud_sliders/index.html.twig', [
            'colonnes' => $colonnes,
            'sldr' => $sldr
        ]);
    }
    /**
     * @Route("/admin/crud/sliders/new", name="admin_crud_sliders_new")
     * @Route("/admin/crud/sliders/edit/{id}", name="admin_crud_sliders_edit")
     */
    public function form(Slider $slider = null, Request $rq, EntityManagerInterface $manager)
    {
        if (!$slider)
        {
            $slider = new Slider;
            $slider->setDateEnregistrement(new \DateTime());
        }
        $form = $this->createForm(SliderType::class, $slider);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($slider);
            $manager->flush();
            return $this->redirectToRoute('app_admin_crud_sliders');
        }
        return $this->renderForm('admin_crud_sliders/form.html.twig', [
            'form' => $form,
            'editMode' => $slider->getId() != NULL
        ]);
    }

    /**
     * @Route("/admin/crud/sliders/delete/{id}", name="admin_crud_sliders_delete")
     */
    public function delete(Slider $slider = null, EntityManagerInterface $manager)
    {
        if ($slider) {
            $manager->remove($slider);
            $manager->flush();
            $this->addFlash('success', 'Le slider a bien été supprimé !');
        }
        return $this->redirectToRoute('app_admin_crud_sliders');
    }

}
