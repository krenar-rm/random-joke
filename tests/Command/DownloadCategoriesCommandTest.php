<?php

declare(strict_types = 1);

namespace App\Tests\Command;

use App\Tests\MockWebServerTrait;
use donatj\MockWebServer\Response;

/**
 * Тестирование выполнения команды для загрузки категорий
 */
class DownloadCategoriesCommandTest extends CommandTestCase
{
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
     * Тестирование выполнение команды
     */
    public function testExecute()
    {
        $this->mockApiCategoryUrl();

        $output = $this->executeCommand('app:download-categories');

        $this->assertContains('Категории успешно загружены', $output);
        $this->assertContains('Количество: 3', $output);
    }

    /**
     * Мок выгрузки категорий
     *
     * @return void
     */
    private function mockApiCategoryUrl()
    {
        $data = [
            'type'  => 'success',
            'value' => [
                'some_category_1',
                'some_category_2',
                'some_category_3',
            ],
        ];

        $body = json_encode($data);

        $response = new Response(
            $body,
            [],
            200
        );

        $this->server->setResponseOfPath(
            '/categories',
            $response
        );
    }
}
