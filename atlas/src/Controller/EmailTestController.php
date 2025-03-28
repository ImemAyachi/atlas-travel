<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class EmailTestController extends AbstractController
{
    #[Route('/test-email', name: 'app_test_email_send')]
    public function sendTestEmail(MailerInterface $mailer, LoggerInterface $logger): Response
    {
        // Use your actual email address and app password
        $senderEmail = 'imem.playz@gmail.com';
        $recipientEmail = 'imem.playz@gmail.com'; // Change if you want to test sending to a different address

        $email = (new Email())
            ->from($senderEmail)
            ->to($recipientEmail)
            ->subject('Symfony Mailer Test - ' . date('Y-m-d H:i:s'))
            ->text('This is a test email sent using Symfony Mailer at ' . date('Y-m-d H:i:s'));

        try {
            $logger->info(sprintf('Attempting to send email from %s to %s', $senderEmail, $recipientEmail));
            $mailer->send($email);
            $logger->info('Email sent successfully.');
            return new Response(sprintf('Test email sent successfully from %s to %s. Check your inbox/spam.', $senderEmail, $recipientEmail));
        } catch (\Exception $e) {
            $logger->error('Failed to send email: ' . $e->getMessage(), ['exception' => $e]);
            return new Response('Error sending email: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 