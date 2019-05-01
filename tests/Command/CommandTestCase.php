<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Tests\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Базовый класс для выполнения команд
 */
abstract class CommandTestCase extends TestCase
{
    /**
     * Приложение
     *
     * @var Application
     */
    protected $application;

    /**
     * Установка окружения
     */
    protected function setUp()
    {
        parent::setUp();

        $kernel            = self::$client->getKernel();
        $this->application = new Application($kernel);
    }

    /**
     * Сброс окружения
     */
    protected function tearDown()
    {
        parent::tearDown();

        unset($this->application);
    }

    /**
     * Выполнение команды по наименованию
     *
     * @param string $commandName
     *
     * @return string
     */
    protected function executeCommand(string $commandName): string
    {
        $command       = $this->application->find($commandName);
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            ['command' => $command->getName()]
        );

        $output = $commandTester->getDisplay();

        return $output;
    }
}
