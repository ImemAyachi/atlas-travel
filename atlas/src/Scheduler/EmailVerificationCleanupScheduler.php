<?php

namespace App\Scheduler;

use App\Repository\EmailVerificationRepository;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('email_verification_cleanup')]
class EmailVerificationCleanupScheduler implements ScheduleProviderInterface
{
    public function __construct(
        private EmailVerificationRepository $verificationRepository
    ) {}

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('1 day', new CleanupExpiredVerificationsMessage())
                    ->at('00:00')
            );
    }
} 