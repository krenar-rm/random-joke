<?php

declare(strict_types=1);

namespace App\Tests\Command\RequestWorkflowProcessCommand;

use App\Entity\JokeRequest;
use App\Tests\Command\CommandTestCase;
use App\Tests\FixturesTrait;

/**
 * Тестирование выполнения команды по обработке заявки на шутку в статусе "JOKE_CHOOSEN"
 */
class RequestJokeChoosenStrategyTest extends CommandTestCase
{
    use FixturesTrait;

    /**
     * Логгер сервиса для работы с почтой
     *
     * @var \Swift_Plugins_MessageLogger
     */
    private $mailLogger;

    /**
     * Установка окружения
     */
    protected function setUp()
    {
        parent::setUp();

        $mailer           = self::$client->getContainer()->get('mailer');
        $this->mailLogger = new \Swift_Plugins_MessageLogger();
        $mailer->registerPlugin($this->mailLogger);
    }

    /**
     * Сброс окружения
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->mailLogger);
    }

    /**
     * Обработка заявки в статусе "JOKE_CHOOSEN"
     */
    public function testHandle()
    {
        $this->createJokeRequest(
            [
                'category' => $this->createCategory('some_category_code'),
                'email'    => 'test@example.com',
                'status'   => JokeRequest::STATUS_JOKE_CHOOSEN,
                'joke'     => $this->createJoke(
                    [
                        'id'    => 1,
                        'value' => 'some_joke_value',
                    ]
                ),
            ]
        );

        $output = $this->executeCommand('app:request:workflow-process');

        $this->assertContains('Заявка №1 взята в работу', $output);
        $this->assertContains('Заявка обработана', $output);

        // Проверка что шутка сохранена на диске
        $jokeRequestRepository = $this->entityManager->getRepository(JokeRequest::class);
        $processedJokeRequest  = $jokeRequestRepository->find(1);

        // Проверка, что у заявки изменился статус
        $this->assertInstanceOf(JokeRequest::class, $processedJokeRequest);
        $this->assertSame(JokeRequest::STATUS_EMAIL_SENDED, $processedJokeRequest->getStatus());

        // Проверка на отправку почты
        $this->assertCount(1, $this->mailLogger->getMessages());
        $message = current($this->mailLogger->getMessages());

        $this->assertSame(['test@example.com' => null], $message->getTo());
        $this->assertSame('Случайная шутка из some_category_code', $message->getSubject());
        $this->assertSame('some_joke_value', $message->getBody());
    }
}
