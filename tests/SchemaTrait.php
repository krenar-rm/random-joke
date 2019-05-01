<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Трейт для обновления схемы БД
 */
trait SchemaTrait
{
    /**
     * Обновление схемы
     *
     * @param EntityManagerInterface $entityManager
     */
    protected function updateSchema(EntityManagerInterface $entityManager): void
    {
        $metadata   = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadata);
    }
}
