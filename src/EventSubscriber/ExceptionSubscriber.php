<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var string */
    private $from;
    /** @var string */
    private $to;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->from = 'from@domain.fr';
        $this->to = 'to@domain.fr';
    }

    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onProcessException'
        ];
    }

    public function onProcessException(ExceptionEvent $event)
    {
        $message = (new \Swift_Message())
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setBody("{$event->getRequest()->getRequestUri()}
                {$event->getException()->getMessage()}
                {$event->getException()->getTraceAsString()}
                ");

        $this->mailer->send($message);
    }
}
