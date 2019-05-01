<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Заявка на шутку
 *
 * @ORM\Entity(repositoryClass="App\Repository\JokeRequestRepository")
 */
class JokeRequest
{
    const STATUS_NEW          = 'new';
    const STATUS_JOKE_CHOOSEN = 'joke_choosen';
    const STATUS_EMAIL_SENDED = 'email_sended';
    const STATUS_ERROR        = 'error';

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
     * Дата создания
     *
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetimetz")
     */
    private $createdAt;

    /**
     * Категория
     *
     * @var JokeCategory
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\JokeCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * Шутка
     *
     * @var Joke
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Joke")
     * @ORM\JoinColumn(nullable=true)
     */
    private $joke;

    /**
     * Email
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * Текст ошибки
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $errorMessage;

    /**
     * Статус заявки
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status    = self::STATUS_NEW;
    }

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
     * Получение даты создания заявки
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Получение категории
     *
     * @return JokeCategory
     */
    public function getCategory(): JokeCategory
    {
        return $this->category;
    }

    /**
     * Установка категории
     *
     * @param JokeCategory $category
     *
     * @return JokeRequest
     */
    public function setCategory(JokeCategory $category): JokeRequest
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Получение шутки
     *
     * @return Joke
     */
    public function getJoke(): Joke
    {
        return $this->joke;
    }

    /**
     * Установка шутки
     *
     * @param Joke|null $joke
     *
     * @return JokeRequest
     */
    public function setJoke(?Joke $joke): JokeRequest
    {
        $this->joke = $joke;

        return $this;
    }

    /**
     * Получение email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Установка email
     *
     * @param string $email
     *
     * @return JokeRequest
     */
    public function setEmail(string $email): JokeRequest
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Получает сообщение об ошибке
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Устанавливает сообщение об ошибке
     *
     * @param null|string $errorMessage
     *
     * @return JokeRequest
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * Получение статуса
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Установка статуса
     *
     * @param string $status
     *
     * @return JokeRequest
     */
    public function setStatus(string $status): JokeRequest
    {
        $this->status = $status;
        return $this;
    }
}
