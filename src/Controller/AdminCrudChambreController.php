<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCrudChambreController extends AbstractController
{
    #[Route('/admin/crud/chambre', name: 'app_admin_crud_chambre')]
    public function index(): Response
    {
        return $this->render('admin_crud_chambre/index.html.twig', [
            'controller_name' => 'AdminCrudChambreController',
        ]);
    }
}
