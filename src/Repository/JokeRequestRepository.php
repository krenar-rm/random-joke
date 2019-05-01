<?php

declare(strict_types = 1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\JokeRequest;

/**
 * Репозиторий категории
 *
 * @method JokeRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method JokeRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method JokeRequest[]    findAll()
 * @method JokeRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JokeRequestRepository extends ServiceEntityRepository
{
    /**
     * Конструктор
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, JokeRequest::class);
    }

    /**
     * Сохраняет заявку на шутку
     *
     * @param JokeRequest $request
     *
     * @throws ORMException
     */
    public function save(JokeRequest $request): void
    {
        $this->getEntityManager()->persist($request);
        $this->getEntityManager()->flush($request);
    }

    /**
     * Получить заявки на шутку доступных для обработки
     *
     * @param int $limit
     *
     * @return array
     */
    public function receiveRequestsForProcessing(int $limit): array
    {
        $query = $this
            ->createQueryBuilder('r')
            ->where('r.status NOT IN (:statuses)')
            ->setParameter(
                'statuses',
                [
                    JokeRequest::STATUS_EMAIL_SENDED,
                    JokeRequest::STATUS_ERROR,
                ]
            )
            ->setMaxResults($limit)
            ->orderBy('r.createdAt');

        return $query->getQuery()->getResult();
    }
}
