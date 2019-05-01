<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Joke;
use App\Entity\JokeCategory;
use App\Entity\JokeRequest;

trait FixturesTrait
{
    /**
     * Добавление категории
     *
     * @param string $code
     *
     * @return JokeCategory
     */
    protected function createCategory(string $code): JokeCategory
    {
        $category = new JokeCategory();
        $category
            ->setCode($code);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    /**
     * Создать заявку на шутку
     *
     * @param array $data
     *
     * @return JokeRequest
     */
    protected function createJokeRequest(array $data): JokeRequest
    {
        $jokeRequest = new JokeRequest();
        $jokeRequest
            ->setCategory($data['category'])
            ->setEmail($data['email'])
            ->setStatus($data['status'])
            ->setJoke($data['joke'] ?? null);

        $this->entityManager->persist($jokeRequest);
        $this->entityManager->flush();

        return $jokeRequest;
    }

    /**
     * Создание шутки
     *
     * @param array $data
     *
     * @return Joke
     */
    protected function createJoke(array $data): Joke
    {
        $joke = new Joke();
        $joke
            ->setId($data['id'])
            ->setValue($data['value']);

        $this->entityManager->persist($joke);
        $this->entityManager->flush();

        return $joke;
    }
}
