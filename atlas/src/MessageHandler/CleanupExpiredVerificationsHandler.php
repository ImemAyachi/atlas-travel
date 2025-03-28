<?php

namespace App\MessageHandler;

use App\Repository\EmailVerificationRepository;
use App\Scheduler\CleanupExpiredVerificationsMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class CleanupExpiredVerificationsHandler
{
    public function __construct(
        private EmailVerificationRepository $verificationRepository,
        private LoggerInterface $logger
    ) {}

    public function __invoke(CleanupExpiredVerificationsMessage $message)
    {
        try {
            $count = $this->verificationRepository->deleteExpired();
            $this->logger->info(sprintf('Successfully removed %d expired verification(s).', $count));
        } catch (\Exception $e) {
            $this->logger->error('Error cleaning up expired verifications: ' . $e->getMessage());
        }
    }
} 