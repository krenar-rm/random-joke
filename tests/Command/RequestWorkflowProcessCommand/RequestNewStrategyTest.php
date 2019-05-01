<?php

declare(strict_types=1);

namespace App\Tests\Command\RequestWorkflowProcessCommand;

use App\Entity\Joke;
use App\Entity\JokeRequest;
use App\Tests\Command\CommandTestCase;
use App\Tests\FixturesTrait;
use App\Tests\MockWebServerTrait;
use donatj\MockWebServer\Response;

/**
 * Тестирование выполнения команды по обработке заявки на шутку в статусе "NEW"
 */
class RequestNewStrategyTest extends CommandTestCase
{
    use FixturesTrait;
    use MockWebServerTrait;

    /**
     * Установка окружения
     */
    public function setUp()
    {
        parent::setUp();

        $this->startMockServer();
    }

    /**
     * Сброс окружения
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->stopMockServer();
    }

    /**
     * Обработка заявки в статусе "NEW"
     */
    public function testHandle()
    {
        $this->mockGetRandomJokeByCategoryApi();

        $jokeRequest = $this->createJokeRequest(
            [
                'category' => $this->createCategory('some_category_code'),
                'email'    => 'test@example.com',
                'status'   => JokeRequest::STATUS_NEW,
            ]
        );

        $output = $this->executeCommand('app:request:workflow-process');

        $this->assertContains('Заявка №1 взята в работу', $output);
        $this->assertContains('Заявка обработана', $output);

        $jokeRequestRepository = $this->entityManager->getRepository(JokeRequest::class);
        $processedJokeRequest  = $jokeRequestRepository->find(1);

        $this->assertInstanceOf(JokeRequest::class, $processedJokeRequest);
        $this->assertSame(JokeRequest::STATUS_JOKE_CHOOSEN, $processedJokeRequest->getStatus());


        $joke = $processedJokeRequest->getJoke();
        $this->assertInstanceOf(Joke::class, $joke);
        $this->assertSame('some_joke_value', $joke->getValue());

        $this->assertSame($jokeRequest->getJoke(), $joke);
    }

    /**
     * Мок ресурса по получении случайной шутки категории
     */
    private function mockGetRandomJokeByCategoryApi()
    {
        $data = [
            'type'  => 'success',
            'value' => [
                'id'         => 1,
                'joke'       => 'some_joke_value',
                'categories' => ['some_category_code'],
            ],
        ];

        $body = json_encode($data);

        $response = new Response(
            $body,
            [],
            200
        );

        $this->server->setResponseOfPath(
            '/jokes/random',
            $response
        );
    }
}
