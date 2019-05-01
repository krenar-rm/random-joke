<?php

declare(strict_types = 1);

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Базовый класс для функциональных тестов
 */
abstract class TestCase extends WebTestCase
{
    use SchemaTrait;

    /**
     * Client
     *
     * @var Client|null
     */
    protected static $client;

    /**
     * Entity manager
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Установка окружения
     */
    protected function setUp()
    {
        parent::setUp();

        self::$client = self::createClient();
        self::$client->disableReboot();

        $this->entityManager = $this->getEntityManager();

        $this->updateSchema($this->entityManager);
    }

    /**
     * Сброс окружения
     */
    protected function tearDown()
    {
        self::$client = null;

        unset(
            $this->entityManager
        );

        parent::tearDown();
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel method
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        if (null === self::$client) {
            self::$client = parent::createClient($options, $server);
        }

        return self::$client;
    }

    /**
     * Получение "Менеджера сущностей"
     *
     * @return EntityManagerInterface
     */
    private function getEntityManager(): EntityManagerInterface
    {
        return self::$client->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Генерация url по наименованию
     *
     * @param string $name       Наименование
     * @param array  $parameters Массив параметров УРЛа
     *
     * @return string
     */
    protected function generateUrl(string $name, array $parameters = []): string
    {
        return self::$client->getContainer()->get('router')->generate($name, $parameters);
    }
}
