<?php

declare(strict_types=1);

namespace App\Strategy\JokeRequest;

use App\Entity\JokeRequest;
use Symfony\Component\Workflow\Registry;

/**
 * Стратегия для обработки заявок на шутку со статусом "JOKE_CHOOSEN"
 */
class RequestJokeChoosenStrategy implements StrategyInterface
{
    /**
     * Воркфлоу
     *
     * @var Registry
     */
    private $workflows;

    /**
     * Сервис для отправки почты
     *
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * От кого: адрес
     *
     * @var string
     */
    private $fromEmail;

    /**
     * Конструктор
     *
     * @param Registry      $workflows
     * @param \Swift_Mailer $mailer
     * @param string        $fromEmail
     */
    public function __construct(Registry $workflows, \Swift_Mailer $mailer, string $fromEmail)
    {
        $this->workflows = $workflows;
        $this->mailer    = $mailer;
        $this->fromEmail = $fromEmail;
    }

    /**
     * Проверки возможности применения стратегии
     *
     * @param string $status
     *
     * @return bool
     */
    public function supports(string $status): bool
    {
        return JokeRequest::STATUS_JOKE_CHOOSEN === $status;
    }

    /**
     * Выполнение инструкций
     *
     * @param JokeRequest $request
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(JokeRequest $request)
    {
        $message = (new \Swift_Message())
            ->setSubject(sprintf('Случайная шутка из %s', $request->getCategory()->getCode()))
            ->setFrom($this->fromEmail)
            ->setTo($request->getEmail())
            ->setBody($request->getJoke()->getValue());

        if ($this->mailer->send($message)) {
            $workflow = $this->workflows->get($request, 'joke_request');

            $workflow->apply($request, 'email_sended');
        } else {
            throw new \Exception('Ошибка при отправке почты');
        }
    }
}
