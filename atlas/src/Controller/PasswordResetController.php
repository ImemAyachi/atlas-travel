<?php

namespace App\Controller;

use App\Service\PasswordResetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class PasswordResetController extends AbstractController
{
    private PasswordResetService $resetService;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(
        PasswordResetService $resetService,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ) {
        $this->resetService = $resetService;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET', 'POST'])]
    public function forgotPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            
            if (empty($email)) {
                return $this->render('authentication/forgot_password.html.twig', [
                    'submitted' => false,
                    'success' => false,
                    'error' => 'Please enter your email address',
                    'email' => $email
                ]);
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->render('authentication/forgot_password.html.twig', [
                    'submitted' => false,
                    'success' => false,
                    'error' => 'Please enter a valid email address',
                    'email' => $email
                ]);
            }

            // Check if user exists
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (!$user) {
                return $this->render('authentication/forgot_password.html.twig', [
                    'submitted' => false,
                    'success' => false,
                    'error' => 'No account found with this email address',
                    'email' => $email
                ]);
            }
            
            try {
                // Generate reset token and send email
                $passwordReset = $this->resetService->generateResetToken($email);
                if ($passwordReset) {
                    $this->resetService->sendResetEmail($passwordReset);
                    
                    return $this->render('authentication/forgot_password.html.twig', [
                        'submitted' => true,
                        'success' => true,
                        'error' => null,
                        'email' => $email
                    ]);
                }
            } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
                error_log('Failed to send reset email: ' . $e->getMessage());
                return $this->render('authentication/forgot_password.html.twig', [
                    'submitted' => false,
                    'success' => false,
                    'error' => 'Unable to send reset email. Please try again later.',
                    'email' => $email
                ]);
            } catch (\Exception $e) {
                error_log('Error in password reset process: ' . $e->getMessage());
                return $this->render('authentication/forgot_password.html.twig', [
                    'submitted' => false,
                    'success' => false,
                    'error' => 'An error occurred. Please try again later.',
                    'email' => $email
                ]);
            }
        }

        return $this->render('authentication/forgot_password.html.twig', [
            'submitted' => false,
            'success' => false,
            'error' => null,
            'email' => null
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(Request $request, string $token): Response
    {
        $passwordReset = $this->resetService->validateToken($token);
        
        if (!$passwordReset) {
            $this->addFlash('error', 'Invalid or expired password reset link.');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $errors = [];

            // Validate password
            if (empty($password)) {
                $errors['password'] = 'Password is required.';
            } elseif (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters long.';
            } elseif (!preg_match('/[A-Z]/', $password)) {
                $errors['password'] = 'Password must contain at least one uppercase letter.';
            } elseif (!preg_match('/[0-9]/', $password)) {
                $errors['password'] = 'Password must contain at least one number.';
            } elseif (!preg_match('/[!@#$%^&*]/', $password)) {
                $errors['password'] = 'Password must contain at least one special character (!@#$%^&*).';
            }

            // Validate confirm password
            if (empty($confirmPassword)) {
                $errors['confirm_password'] = 'Please confirm your password.';
            } elseif ($password !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match.';
            }

            if (!empty($errors)) {
                return $this->render('authentication/reset_password.html.twig', [
                    'token' => $token,
                    'errors' => $errors
                ]);
            }

            try {
                // Update password
                $user = $passwordReset->getUser();
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                // Mark token as used
                $this->resetService->markTokenAsUsed($passwordReset);
                
                $this->entityManager->flush();

                $this->addFlash('success', 'Your password has been successfully reset. You can now log in with your new password.');
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                return $this->render('authentication/reset_password.html.twig', [
                    'token' => $token,
                    'error' => 'An error occurred while resetting your password. Please try again.'
                ]);
            }
        }

        return $this->render('authentication/reset_password.html.twig', [
            'token' => $token,
            'error' => null,
            'errors' => []
        ]);
    }
} 