<?php

declare(strict_types = 1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\JokeCategory;

/**
 * Репозиторий категории
 *
 * @method JokeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method JokeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method JokeCategory[]    findAll()
 * @method JokeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JokeCategoryRepository extends ServiceEntityRepository
{
    /**
     * Конструктор
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, JokeCategory::class);
    }

    /**
     * Сохраняет категорию
     *
     * @param JokeCategory $category
     *
     * @throws ORMException
     */
    public function save(JokeCategory $category): void
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush($category);
    }

    /**
     * Удалить все записи
     *
     * @return bool
     */
    public function removeAll(): bool
    {
        foreach ($this->findAll() as $category) {
            $this->getEntityManager()->remove($category);
        }

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        return true;
    }
}
