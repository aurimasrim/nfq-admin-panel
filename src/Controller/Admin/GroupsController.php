<?php


namespace App\Controller\Admin;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

/**
 * @Route("/admin/groups")
 */
class GroupsController extends AbstractController
{
    /**
     * @Route("/", name="admin_groups_index")
     */
    public function index(GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->findAll();

        return $this->render('admin/groups.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/new", name="admin_groups_new")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(GroupType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('admin_groups_index');
        }

        return $this->render('admin/new_user_or _group.html.twig', [
            'form' => $form->createView(),
            'header' => 'New group'
        ]);
    }

    /**
     * @Route("/{id}", name="admin_groups_show")
     * @Entity("group", expr="repository.findById(id)")
     */
    public function show(Group $group, UserRepository $userRepository): Response
    {
        $usersNotInGroup = $userRepository->findByNotInGroup($group->getId());

        return $this->render('admin/group.html.twig', [
            'group' => $group,
            'usersNotInGroup' => $usersNotInGroup
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_groups_delete")
     * @Method("POST")
     */
    public function delete(Request $request, Group $group): Response
    {
        if (!$this->isCsrfTokenValid('delete_group', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_groups_index');
        }

        if (!$group->getUsers()->isEmpty()) {
            return new Response(null, Response::HTTP_CONFLICT);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();

        return $this->redirectToRoute('admin_groups_index');
    }

    /**
     * @Route("/{id}/add", name="admin_groups_add_user")
     * @Method("POST")
     */
    public function addUser(Request $request, Group $group, UserRepository $userRepository): Response
    {
        if (!$this->isCsrfTokenValid('add_user', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_groups_show', [
                'id' => $group->getId()
            ]);
        }

        $userId = $request->get('userId');
        $group->addUser($userRepository->find($userId));
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_groups_show', [
            'id' => $group->getId()
        ]);
    }

    /**
     * @Route("/{groupId}/remove/{userId}", name="admin_groups_remove_user")
     * @Method("POST")
     * @ParamConverter("group", options={"mapping": {"groupId": "id"}})
     * @ParamConverter("user", options={"mapping": {"userId": "id"}})
     */
    public function removeUser(Request $request, Group $group, User $user): Response
    {
        if (!$this->isCsrfTokenValid('remove_user', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_groups_show', [
                'id' => $group->getId()
            ]);
        }

        $group->removeUser($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_groups_show', [
            'id' => $group->getId()
        ]);
    }
}