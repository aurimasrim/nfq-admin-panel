<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/users", name="admin_show_users")
     */
    public function showUsers(): Response
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/users/new", name="admin_new_user")
     */
    public function newUser(Request $request): Response
    {
        $form = $this->createForm(UserType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/new_user.html.twig', [
            'form' => $form->createView()
        ]);
    }
}