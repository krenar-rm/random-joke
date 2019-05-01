<?php

declare(strict_types = 1);

namespace App\Strategy\JokeRequest;

use App\Entity\JokeRequest;

/**
 * Обработчик заявок
 */
class RequestHandler
{
    /**
     * Список подходящих стратегий
     *
     * @var StrategyInterface[]
     */
    private $strategies = [];

    /**
     * Добавить стратегию в список
     *
     * @param StrategyInterface $strategy
     */
    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     * Обработать заявку на шутку
     *
     * @param JokeRequest $request
     *
     * @return mixed
     */
    public function handle(JokeRequest $request)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request->getStatus())) {
                return $strategy->execute($request);
            }
        }
    }
}
