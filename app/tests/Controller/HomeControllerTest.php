<?php
namespace App\Tests\Controller;

use App\Repository\HitRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    public function testHitCount(): void
    {
        $client = static::createClient();
        $hitRepository = static::getContainer()->get(HitRepository::class);

        $client->request('GET', '/');
        $request = $client->getRequest();

        $this->assertSelectorTextContains('#hits', $hitRepository->countCurrentShopHits($request->server->get('HTTP_HOST')));

        $this->assertResponseIsSuccessful();
    }

    public function testHitResponse(): void
    {
        $client = static::createClient();

        $client->request('POST', '/hit');

        $this->assertJson($client->getResponse()->getContent());

    }

}