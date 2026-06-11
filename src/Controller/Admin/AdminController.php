<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
#[Route('/admin')]
class AdminController extends AbstractController
{
#[Route('/', name: 'admin_index')]
public function index(UserRepository $repo, Request $request): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $page = $request->query->getInt('page', 1);
    $users = $repo->findPaginated($page);

    return $this->render('admin/index.html.twig', [
        'users' => $users,
        'page' => $page,
    ]);
}
    

    #[Route('/user/{id}/make-manager', name: 'admin_make_manager')]
    public function makeManager(int $id, UserRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $repo->find($id);
        $user->setRoles(['ROLE_MANAGER']);
        $em->flush();

        return $this->redirectToRoute('admin_index');
    }

    #[Route('/user/{id}/suspend', name: 'admin_suspend')]
    public function suspend(int $id, UserRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $repo->find($id);
        $user->setIsActive(false);
        $em->flush();

        return $this->redirectToRoute('admin_index');
    }

    #[Route('/user/{id}/activate', name: 'admin_activate')]
    public function activate(int $id, UserRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $repo->find($id);
        $user->setIsActive(true);
        $em->flush();

        return $this->redirectToRoute('admin_index');
    }
}