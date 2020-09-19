<?php

declare(strict_types = 1);

namespace App\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Class Content
 *
 * @ORM\Entity(repositoryClass="App\Doctrine\Repository\ContentRepository")
 * @ORM\Table(name="contents")
 *
 * @package App\Doctrine\Entity
 */
class Content implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected int $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected string $hash;

    /**
     * @ORM\Column(type="json")
     *
     * @var array
     */
    protected array $content;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getHash() : string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash) : void
    {
        $this->hash = $hash;
    }

    /**
     * @return array
     */
    public function getContent() : array
    {
        return $this->content;
    }

    /**
     * @param array $content
     */
    public function setContent(array $content) : void
    {
        $this->content = $content;
    }

    public function jsonSerialize() : array
    {
        return [
            'id'      => $this->getId(),
            'hash'    => $this->getHash(),
            'content' => $this->getContent(),
        ];
    }
}