<?php

declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Repository\JokeRequestRepository;
use App\Entity\JokeRequest;
use Symfony\Component\Workflow\Registry;
use App\Strategy\JokeRequest\RequestHandler;

/**
 * Команда для обработки заявок на шутку
 */
class RequestWorkflowProcessCommand extends Command
{
    private const LIMIT_DEFAULT = 5;

    /**
     * Наименование команды
     *
     * @var string
     */
    protected static $defaultName = 'app:request:workflow-process';

    /**
     * Репозиторий категории
     *
     * @var JokeRequestRepository
     */
    private $jokeRequestRepository;

    /**
     * Реест потоков
     *
     * @var Registry
     */
    private $workflows;

    /**
     * Обработчик заявок на шутку
     *
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * Конструктор
     *
     * @param JokeRequestRepository $jokeRequestRepository
     * @param Registry              $workflows
     * @param RequestHandler        $requestHandler
     */
    public function __construct(
        JokeRequestRepository $jokeRequestRepository,
        Registry $workflows,
        RequestHandler $requestHandler
    ) {
        $this->jokeRequestRepository = $jokeRequestRepository;
        $this->workflows             = $workflows;
        $this->requestHandler        = $requestHandler;

        parent::__construct();
    }

    /**
     * Конфигурация
     */
    protected function configure()
    {
        $this
            ->setDescription('Обработка заявок на шутку')
            ->setHelp('Данная команда позволяет обработать заявки на шутку')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Количество заявок', self::LIMIT_DEFAULT);
    }

    /**
     * Выполнение команды
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requests = $this->jokeRequestRepository->receiveRequestsForProcessing((int) $input->getArgument('limit'));

        if (empty($requests)) {
            $output->writeln('Отсутствуют заявки для обработки');
        }

        foreach ($requests as $request) {
            $output->writeln(sprintf('Заявка №%d взята в работу', $request->getId()));

            $this->jokeRequestProcessing($request, $output);

            $output->writeln('Заявка обработана');
        }

        return 0;
    }

    /**
     * Обработка заявки на шутку
     *
     * @param JokeRequest     $request
     * @param OutputInterface $output
     */
    private function jokeRequestProcessing(JokeRequest $request, OutputInterface $output)
    {
        $workflow = $this->workflows->get($request, 'joke_request');

        try {
            $this->requestHandler->handle($request);
        } catch (\Throwable $exception) {
            $workflow->apply($request, 'error');

            $request->setErrorMessage($exception->getMessage());

            $output->writeln(
                sprintf(
                    'Заявка №%d обработана с ошибкой: "%s"',
                    $request->getId(),
                    $exception->getMessage()
                )
            );
            $output->writeln(sprintf('File: %s', $exception->getFile()));
            $output->writeln(sprintf('Line: %d', $exception->getLine()));
        } finally {
            $this->jokeRequestRepository->save($request);
        }
    }
}
