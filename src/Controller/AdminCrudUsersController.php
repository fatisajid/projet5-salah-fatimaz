<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCrudUsersController extends AbstractController
{
    #[Route('/admin/crud/users', name: 'app_admin_crud_users')]
    public function index(UserRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();
        $usrs = $repo->findAll();
        return $this->render('admin_crud_users/index.html.twig', [
            'usrs' => $usrs,
            'colonnes' => $colonnes
        ]);
    }
    /**
     * @Route("/admin/crud/users/new", name="admin_crud_users_new")
     * @Route("/admin/crud/users/edit/{id}", name="admin_crud_users_edit")
     */
    public function form(User $user = null, EntityManagerInterface $manager, Request $rq, UserPasswordHasherInterface $hasher)
    {
        if (!$user) {
            $user = new User;
            $user->setDateEnregistrement(new \DateTime());
        }

        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($rq);

        if ($form->isSubmitted() && $form->isValid()) {

            // si l'utilisateur est nouveau et le mdp est vide

            if(!$user->getId() && $form->get('plainPassword')->getData() == null)
            {
                // je renvoie une erreur
                
                $this->addFlash('danger', "Un nouveau user doit avoir un mot de passe.");
                return $this->redirectToRoute('app_admin_crud_users_new');
            }

            if ($form->get('plainPassword')->getData()) {
                $user->setPassword($hasher->hashPassword($user, $form->get('plainPassword')->getData()));
            }

            // si c'est un nouvel utilisateur ou qu'on modifie le mot de passe d'un utilisateur existant
            // alors on hash le nouveau mdp

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('app_admin_crud_users');
        }
        return $this->renderForm('admin_crud_users/form.html.twig', [
            'form' => $form,
            'editMode' => $user->getId() != NULL
        ]);
    }

    /**
     * @Route("/admin/crud/users/delete/{id}", name="admin_crud_users_delete")
     */
    public function delete(User $user = null, EntityManagerInterface $manager)
    {
        if ($user) {
            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', 'l utilisateur a bien été supprimé !');
        }
        return $this->redirectToRoute('app_admin_crud_users');
    }


}
