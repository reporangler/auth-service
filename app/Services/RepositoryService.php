<?php
namespace App\Services;

use RepoRangler\Entity\Repository;
use RepoRangler\Services\MetadataClient;

class RepositoryService
{
    /**
     * @var MetadataClient
     */
    private $metadataClient;

    public function __construct(MetadataClient $client)
    {
        $this->metadataClient = $client;
    }

    public function getById($id): Repository
    {
        return $this->metadataClient->getRepositoryById($id);
    }

    public function getByName(string $name): Repository
    {
        return $this->metadataClient->getRepositoryByName($name);
    }
}
