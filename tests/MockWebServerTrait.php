<?php

declare(strict_types=1);

namespace App\Tests;

use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;

/**
 * Примесь для эмулирования внешнего веб-сервиса
 */
trait MockWebServerTrait
{
    /**
     * Мок веб-сервера
     *
     * @var MockWebServer
     */
    protected $server;

    /**
     * Создает и запускает сервер
     *
     * @param int    $port
     * @param string $host
     */
    protected function startMockServer(int $port = 52142, string $host = '127.0.0.1')
    {
        $this->server = new MockWebServer($port, $host);
        $this->server->start();
    }

    /**
     * Останаливает и удаляет сервер
     */
    protected function stopMockServer()
    {
        $this->server->stop();

        unset($this->server);
    }

    /**
     * Возвращает полный путь для обращения в мок веб-серверу
     *
     * @param string|null $path
     *
     * @return string
     */
    protected function getUrl(string $path = null): string
    {
        return $this->server->getServerRoot().$path;
    }

    /**
     * Создает ответ
     *
     * @param array $data
     * @param array $headers
     *
     * @return Response
     */
    protected function createJsonResponse(array $data = [], array $headers = []): Response
    {
        return new Response(
            json_encode($data),
            array_merge(['CONTENT_TYPE' => 'application/json'], $headers)
        );
    }
}
