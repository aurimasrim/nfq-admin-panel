<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\GroupType;
use App\Form\UserType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
    public function showUsers(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
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

            return $this->redirectToRoute('admin_show_users');
        }

        return $this->render('admin/new_user_or _group.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/delete", name="admin_delete_user")
     * @Method("POST")
     */
    public function deleteUser(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("admin_show_users");
    }

    /**
     * @Route("/groups", name="admin_show_groups")
     */
    public function showGroups(GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->findAll();

        return $this->render('admin/groups.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/groups/new", name="admin_new_group")
     */
    public function newGroup(Request $request): Response
    {
        $form = $this->createForm(GroupType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('admin_show_groups');
        }

        return $this->render('admin/new_user_or _group.html.twig', [
            'form' => $form->createView()
        ]);
    }
}