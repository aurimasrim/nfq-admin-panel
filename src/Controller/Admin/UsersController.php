<?php


namespace App\Controller\Admin;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use App\Form\UserType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="admin_users_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/new", name="admin_users_new")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(UserType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/new_user_or _group.html.twig', [
            'form' => $form->createView(),
            'header' => 'New user'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_users_delete")
     * @Method("POST")
     */
    public function deleteUser(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute("admin_users_index");
    }
}