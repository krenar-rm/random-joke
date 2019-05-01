<?php

declare(strict_types = 1);

namespace App\Command;

use App\Entity\JokeCategory;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\JokeCategoryRepository;

/**
 * Команда для загрузки категорий
 */
class DownloadCategoriesCommand extends Command
{
    /**
     * Наименование команды
     *
     * @var string
     */
    protected static $defaultName = 'app:download-categories';

    /**
     * HTTP клиент
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Менеджер сущностей
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Репозиторий категории
     *
     * @var JokeCategoryRepository
     */
    private $categoryRepository;

    /**
     * URL для выгрузки данных по категориям
     *
     * @var string
     */
    private $apiCategoryUrl;

    /**
     * Конструктор
     *
     * @param ClientInterface        $client
     * @param EntityManagerInterface $entityManger
     * @param JokeCategoryRepository $categoryRepository
     * @param string                 $apiCategoryUrl
     */
    public function __construct(
        ClientInterface $client,
        EntityManagerInterface $entityManger,
        JokeCategoryRepository $categoryRepository,
        string $apiCategoryUrl
    ) {
        $this->httpClient         = $client;
        $this->entityManager      = $entityManger;
        $this->categoryRepository = $categoryRepository;
        $this->apiCategoryUrl     = $apiCategoryUrl;

        parent::__construct();
    }

    /**
     * Конфигурация
     */
    protected function configure()
    {
        $this
            ->setDescription('Загружает категории')
            ->setHelp('Данная команда позволяет загрузить категории');
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
        $response = $this->httpClient->request(
            'GET',
            $this->apiCategoryUrl
        );

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (200 === $response->getStatusCode() && 'success' === $data['type']) {
            $this->categoryRepository->removeAll();
            // Массовая загрузка категорий
            foreach ($data['value'] as $categoryCode) {
                $category = new JokeCategory();
                $category
                    ->setCode($categoryCode);

                $this->entityManager->persist($category);
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            $output->writeln('Категории успешно загружены');
            $output->writeln(sprintf('Количество: %d', count($data['value'])));
        } else {
            $output->writeln('Ошибка при загрузке категорий');
            $output->writeln($body);
        }

        return 0;
    }
}
