<?php

namespace App\Tests\Controller;

use App\Tests\NeedLogin;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    use FixturesTrait;
    use NeedLogin;

    public function testHelloPage()
    {
        $client = static::createClient();
        $client->request('GET', '/hello');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testHelloPageWithH1()
    {
        $client = static::createClient();
        $client->request('GET', '/hello');
        $this->assertSelectorTextContains('h1', 'Bienvenue sur la page hello');
    }

    public function testRedirectLoginWithAnonymousUser()
    {
        $client = static::createClient();
        $client->request('GET', '/auth');
        $this->assertResponseRedirects('/login');
    }

    public function testRequireRoleUserWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $this->login($client, $users['user_user']);

        $client->request('GET', '/auth');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRequireRoleAdminWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $this->login($client, $users['user_user']);

        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRequireRoleAdminWithAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $this->login($client, $users['user_admin']);

        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testMailSendMail()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $client->request('GET', '/mail');

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());

        /** @var \Swift_Message[] $messages */
        $messages = $mailCollector->getMessages();


        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $messages[0]);
        $this->assertEquals(
            'recipient@example.com',
            key($messages[0]->getTo())
        );
    }
}