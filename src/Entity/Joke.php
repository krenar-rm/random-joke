<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Шутка
 *
 * @ORM\Entity(repositoryClass="App\Repository\JokeRepository")
 */
class Joke
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Значение
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $value;

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
     * Установка идентификатора
     *
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): Joke
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Получение значения шутки
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Установка значения шутки
     *
     * @param string $value
     *
     * @return self
     */
    public function setValue(string $value): Joke
    {
        $this->value = $value;

        return $this;
    }
}
