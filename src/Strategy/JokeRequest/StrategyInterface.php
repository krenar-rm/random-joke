<?php

declare(strict_types=1);

namespace App\Strategy\JokeRequest;

use App\Entity\JokeRequest;

/**
 * Интерфейс стратегии по обработке заявок на шутку
 */
interface StrategyInterface
{
    /**
     * Проверки возможности применения стратегии
     *
     * @param string $messageStatus
     *
     * @return bool
     */
    public function supports(string $messageStatus): bool;

    /**
     * Выполнение инструкций
     *
     * @param JokeRequest $message
     */
    public function execute(JokeRequest $message);
}
