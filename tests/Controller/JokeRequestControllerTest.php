<?php

declare(strict_types = 1);

namespace App\Tests\Controller;

use App\Entity\JokeCategory;
use App\Entity\JokeRequest;
use App\Tests\FixturesTrait;
use App\Tests\TestCase;

/**
 * Тестирование контролера для работы с заявками на шутку
 */
class JokeRequestControllerTest extends TestCase
{
    use FixturesTrait;

    /**
     * Построение формы с заявкой
     */
    public function testBuildForm()
    {
        $client = self::$client;

        $this->createCategory('some_code_1');
        $this->createCategory('some_code_2');

        $crawler = $client->request('GET', $this->generateUrl('joke_request_form'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSame(
            'Отправка случайной шутки на почту',
            $crawler->filter('.form-style-6 h1')->text()
        );

        $this->assertSame(
            3,
            $crawler->filter('#joke_request_form select option')->count()
        );
    }

    /**
     * Сохранение заявки
     */
    public function testSubmit()
    {
        $category = $this->createCategory('some_code');

        $client = self::$client;

        $client->request(
            'POST',
            $this->generateUrl('joke_request_submit'),
            [
                'joke_request_form' => [
                    'email'    => 'some_email@example.com',
                    'category' => $category->getId(),
                ],
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jokeRequestRepository = $this->entityManager->getRepository(JokeRequest::class);
        $jokeRequestList       = $jokeRequestRepository->findAll();

        $this->assertCount(1, $jokeRequestList);
        $jokeRequest = current($jokeRequestList);

        $this->assertSame(JokeRequest::STATUS_NEW, $jokeRequest->getStatus());
        $this->assertSame('some_email@example.com', $jokeRequest->getEmail());
        $this->assertSame($category, $jokeRequest->getCategory());
    }
}
