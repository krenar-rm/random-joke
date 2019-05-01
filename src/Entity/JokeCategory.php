<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Категория
 *
 * @ORM\Entity(repositoryClass="App\Repository\JokeCategoryRepository")
 */
class JokeCategory
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Код категории
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $code;

    /**
     * Получение идентификатора
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Получение кода
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Установка кода
     *
     * @param string $code
     *
     * @return JokeCategory
     */
    public function setCode(string $code): JokeCategory
    {
        $this->code = $code;

        return $this;
    }
}
