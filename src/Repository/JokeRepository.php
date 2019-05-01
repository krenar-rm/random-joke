<?php

declare(strict_types = 1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\Joke;

/**
 * Репозиторий для шутки
 *
 * @method Joke|null find($id, $lockMode = null, $lockVersion = null)
 * @method Joke|null findOneBy(array $criteria, array $orderBy = null)
 * @method Joke[]    findAll()
 * @method Joke[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JokeRepository extends ServiceEntityRepository
{
    /**
     * Конструктор
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Joke::class);
    }

    /**
     * Сохраняет шутку
     *
     * @param Joke $joke
     *
     * @throws ORMException
     */
    public function save(Joke $joke): void
    {
        $this->getEntityManager()->persist($joke);
        $this->getEntityManager()->flush($joke);
    }
}
