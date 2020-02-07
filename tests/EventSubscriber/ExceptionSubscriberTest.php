<?php

namespace App\Tests\EventSubscriber;

use PHPUnit\Framework\TestCase;
use App\EventSubscriber\ExceptionSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionSubscriberTest extends TestCase
{
    public function testEventSubscription()
    {
        $this->assertArrayHasKey(ExceptionEvent::class, ExceptionSubscriber::getSubscribedEvents());
    }

    public function testOnExceptionSendMail()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer->expects($this->once())->method('send');
        $this->dispatcher($mailer);
    }

    public function testSendMailToAdmin()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (\Swift_Message $message) {
                return
                    array_key_exists('from@domain.fr', $message->getFrom()) &&
                    array_key_exists('to@domain.fr', $message->getTo());
            }))
        ;

        $this->dispatcher($mailer);
    }

    public function testSendMailWithBody()
    {
        $mailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (\Swift_Message $message) {
                return strpos($message->getBody(), 'ExceptionSubscriberTest') &&
                    strpos($message->getBody(), 'Hello world');
            }))
        ;

        $this->dispatcher($mailer);
    }


    private function dispatcher($mailer)
    {
        $subscriber = new ExceptionSubscriber($mailer);
        $kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
        $event = new ExceptionEvent($kernel, new Request(), 1, new \Exception('Hello world'));

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }
}