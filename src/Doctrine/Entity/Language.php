<?php

declare(strict_types = 1);

namespace App\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Class Language
 *
 * @ORM\Entity
 * @ORM\Table(name="languages")
 *
 * @package App\Doctrine\Entity
 */
class Language implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     *
     * @var int Unique identifier
     */
    protected int $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string Language locale code.
     */
    protected string $code;

    /**
     * @ORM\Column(type="string")
     *
     * @var string Human readable language name.
     */
    protected string $name;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool Whether language is the default or not.
     */
    protected bool $isDefault;

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
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code) : void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isDefault() : bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     */
    public function setIsDefault(bool $isDefault) : void
    {
        $this->isDefault = $isDefault;
    }

    public function jsonSerialize()
    {
        return [
            'id'        => $this->getId(),
            'code'      => $this->getCode(),
            'name'      => $this->getName(),
            'isDefault' => $this->isDefault(),
        ];
    }
}
