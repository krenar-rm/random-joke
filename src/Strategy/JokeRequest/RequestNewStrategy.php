<?php

declare(strict_types = 1);

namespace App\Strategy\JokeRequest;

use App\Entity\Joke;
use App\Entity\JokeRequest;
use App\Repository\JokeRepository;
use App\Repository\JokeRequestRepository;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Workflow\Registry;

/**
 * Стратегия для обработки заявок на шутку со статусом "NEW"
 */
class RequestNewStrategy implements StrategyInterface
{
    /**
     * Воркфлоу
     *
     * @var Registry
     */
    private $workflows;

    /**
     * HTTP клиент
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Репозиторий для работы с шуткой
     *
     * @var JokeRepository
     */
    private $jokeRepository;

    /**
     * Репозиторий для работы с заявками на шутку
     *
     * @var JokeRequestRepository
     */
    private $jokeRequestRepository;

    /**
     * URL для получения случайной шутки по категории
     *
     * @var string
     */
    private $apiRandomJoke;

    /**
     * Конструктор
     *
     * @param Registry              $workflows
     * @param ClientInterface       $httpClient
     * @param JokeRepository        $jokeRepository
     * @param JokeRequestRepository $jokeRequestRepository
     * @param string                $apiRandomJoke
     */
    public function __construct(
        Registry $workflows,
        ClientInterface $httpClient,
        JokeRepository $jokeRepository,
        JokeRequestRepository $jokeRequestRepository,
        string $apiRandomJoke
    ) {
        $this->workflows             = $workflows;
        $this->httpClient            = $httpClient;
        $this->jokeRepository        = $jokeRepository;
        $this->jokeRequestRepository = $jokeRequestRepository;
        $this->apiRandomJoke         = $apiRandomJoke;
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
        return JokeRequest::STATUS_NEW === $status;
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
        $response = $this->httpClient->request(
            'GET',
            sprintf(
                $this->apiRandomJoke,
                $request->getCategory()->getCode()
            )
        );

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (200 === $response->getStatusCode() && 'success' === $data['type']) {
            $jokeId    = $data['value']['id'];
            $jokeValue = $data['value']['joke'];

            $joke = $this->jokeRepository->find($jokeId);
            if (null === $joke) {
                $joke = new Joke();
                $joke
                    ->setId($jokeId)
                    ->setValue($jokeValue);

                $this->jokeRepository->save($joke);
            }
            $request->setJoke($joke);

            $workflow = $this->workflows->get($request, 'joke_request');

            $workflow->apply($request, 'joke_choosen');

            $this->jokeRequestRepository->save($request);
        } else {
            throw new \Exception('Ошибка при получении случайной шутки: '.$body);
        }
    }
}
