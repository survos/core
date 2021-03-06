<?php

declare(strict_types=1);

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\ZAuthModule\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\Bundle\CoreBundle\Site\SiteDefinitionInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Event\RegistrationPostDeletedEvent;
use Zikula\ZAuthModule\Entity\RepositoryInterface\UserVerificationRepositoryInterface;
use Zikula\ZAuthModule\ZAuthConstant;

class DeletePendingRegistrationsListener implements EventSubscriberInterface
{
    /**
     * @var VariableApiInterface
     */
    private $variableApi;

    /**
     * @var UserVerificationRepositoryInterface
     */
    private $userVerificationRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $mailLoggingEnabled;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SiteDefinitionInterface
     */
    private $site;

    public function __construct(
        VariableApiInterface $variableApi,
        UserVerificationRepositoryInterface $userVerificationRepository,
        EventDispatcherInterface $eventDispatcher,
        MailerInterface $mailer,
        LoggerInterface $mailLogger, // $mailLogger var name auto-injects the mail channel handler
        TranslatorInterface $translator,
        SiteDefinitionInterface $site
    ) {
        $this->variableApi = $variableApi;
        $this->userVerificationRepository = $userVerificationRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
        $this->logger = $mailLogger;
        $this->translator = $translator;
        $this->site = $site;
        $this->mailLoggingEnabled = $variableApi->get('ZikulaMailerModule', 'enableLogging', false);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => ['delete'],
            RegistrationPostDeletedEvent::class => ['sendEmail']
        ];
    }

    public function delete(TerminateEvent $event): void
    {
        // remove expired registrations
        $regExpireDays = $this->variableApi->get('ZikulaZAuthModule', ZAuthConstant::MODVAR_EXPIRE_DAYS_REGISTRATION, ZAuthConstant::DEFAULT_EXPIRE_DAYS_REGISTRATION);
        if ($regExpireDays > 0) {
            $deletedUsers = $this->userVerificationRepository->purgeExpiredRecords($regExpireDays, ZAuthConstant::VERIFYCHGTYPE_REGEMAIL, true);
            foreach ($deletedUsers as $deletedUser) {
                $this->eventDispatcher->dispatch(new RegistrationPostDeletedEvent($deletedUser));
            }
        }
    }

    public function sendEmail(RegistrationPostDeletedEvent $event): void
    {
        $adminMail = $this->variableApi->getSystemVar('adminmail');
        $siteName = $this->site->getName();
        $email = (new Email())
            ->from(new Address($adminMail, $siteName))
            ->to(new Address($event->getUser()->getEmail(), $event->getUser()->getUname()))
            ->subject($this->translator->trans('Registration deleted at %site%', ['%site%' => $siteName]))
            ->text($this->translator->trans(<<<'EOT'
Your registration at %site% associated with this email (%email%) has been deleted from the site.
This could have happened because you have delayed too long in confirming your email address, or because the administrator manually deleted your registration.
If you have any questions, please contact the site administrator or re-register.
EOT
        , ['%site%' => $siteName, '%email%' => $event->getUser()->getEmail()]));
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error($exception->getMessage(), [
                'in' => __METHOD__,
            ]);
        }
        if ($this->mailLoggingEnabled) {
            $this->logger->info(sprintf('Email sent to %s', $event->getUser()->getEmail()), [
                'in' => __METHOD__,
            ]);
        }
    }
}
