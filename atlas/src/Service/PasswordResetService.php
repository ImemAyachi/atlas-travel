<?php

namespace App\Service;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Repository\PasswordResetRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;
    private PasswordResetRepository $resetRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        PasswordResetRepository $resetRepository
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->resetRepository = $resetRepository;
    }

    public function generateResetToken(string $email): ?PasswordReset
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return null;
        }

        // Clean up any existing unused tokens for this user
        $this->cleanupOldTokens($user);

        // Generate new token
        $token = bin2hex(random_bytes(32));
        $resetLink = $this->urlGenerator->generate('app_reset_password', [
            'token' => $token
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $passwordReset = new PasswordReset();
        $passwordReset->setUser($user);
        $passwordReset->setToken($token);
        $passwordReset->setResetLink($resetLink);
        $passwordReset->setExpiresAt(new \DateTime('+1 hour'));
        $passwordReset->setCreatedAt(new \DateTime());
        $passwordReset->setIsUsed(false);

        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush();

        return $passwordReset;
    }

    public function sendResetEmail(PasswordReset $passwordReset): void
    {
        try {
            $user = $passwordReset->getUser();
            $emailBody = $this->getEmailTemplate($passwordReset); // Get HTML content from template method
            
            $email = (new Email())
                ->from('imem.playz@gmail.com')
                ->to($user->getEmail())
                ->subject('Reset Your Password - Atlas Travel')
                // ->text('Click the following link to reset your password: ' . $passwordReset->getResetLink()) // Remove plain text version
                ->html($emailBody); // Use the generated HTML

            $this->mailer->send($email);
            
        } catch (\Exception $e) {
            // It's generally better to let the controller handle logging/user feedback
            // error_log('Error sending password reset email: ' . $e->getMessage());
            throw $e; // Re-throw the exception to be caught by the controller
        }
    }

    public function validateToken(string $token): ?PasswordReset
    {
        $passwordReset = $this->resetRepository->findOneBy([
            'token' => $token,
            'isUsed' => false
        ]);

        if (!$passwordReset) {
            return null;
        }

        // Check if token is expired
        if ($passwordReset->getExpiresAt() < new \DateTime()) {
            return null;
        }

        return $passwordReset;
    }

    public function markTokenAsUsed(PasswordReset $passwordReset): void
    {
        $passwordReset->setIsUsed(true);
        $this->entityManager->flush();
    }

    private function cleanupOldTokens(User $user): void
    {
        $tokens = $this->resetRepository->findUnusedTokensByUser($user);
        foreach ($tokens as $token) {
            $this->entityManager->remove($token);
        }
        $this->entityManager->flush();
    }

    private function getEmailTemplate(PasswordReset $passwordReset): string
    {
        $user = $passwordReset->getUser();
        $expiresAt = $passwordReset->getExpiresAt()->format('H:i');

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Your Password - Atlas Travel</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    padding: 20px;
                    background-color: #ffffff;
                }
                .header { 
                    text-align: center; 
                    margin-bottom: 30px;
                    padding: 20px;
                    background-color: #3a7bd5;
                    color: white;
                    border-radius: 8px;
                }
                .header h2 {
                    margin: 0;
                    color: white;
                }
                .btn { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background-color: #3a7bd5; 
                    color: white !important; 
                    text-decoration: none; 
                    border-radius: 4px;
                    margin: 20px 0;
                    font-weight: bold;
                }
                .footer { 
                    margin-top: 30px; 
                    font-size: 0.9em; 
                    color: #666;
                    text-align: center;
                    padding-top: 20px;
                    border-top: 1px solid #eee;
                }
                .content {
                    background: #f9f9f9;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .expiry-notice {
                    font-size: 0.9em;
                    color: #666;
                    font-style: italic;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Reset Your Password</h2>
                </div>
                
                <div class="content">
                    <p>Hello {$user->getPrenom()},</p>
                    
                    <p>We received a request to reset your password for your Atlas Travel account. To proceed with the password reset, click the button below:</p>
                    
                    <p style="text-align: center;">
                        <a href="{$passwordReset->getResetLink()}" class="btn">Reset Password</a>
                    </p>
                    
                    <p class="expiry-notice">This link will expire at {$expiresAt} today. After that, you'll need to request a new password reset link.</p>
                    
                    <p><strong>Important:</strong> If you didn't request this password reset, you can safely ignore this email. Your password won't be changed unless you click the button above.</p>
                </div>
                
                <div class="footer">
                    <p>Best regards,<br>The Atlas Travel Team</p>
                    <p style="font-size: 0.8em; color: #999;">This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
} 