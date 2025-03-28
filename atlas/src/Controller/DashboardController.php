<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Make sure the user is logged in
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $roleName = $user->getRole();
        if (empty($roleName)) {
            $user->setRole('tourist');
            $entityManager->persist($user);
            $entityManager->flush();
            $roleName = 'tourist';
        }
        
        // Debugging information
        $userRoles = $user->getRoles();
        $roleEmpty = empty($roleName) ? 'true' : 'false';
        
        $currentTime = new \DateTime();
        
        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
            'currentTime' => $currentTime,
            'userRoles' => $userRoles,
            'roleName' => $roleName,
            'roleEmpty' => $roleEmpty,
        ]);
    }
} 