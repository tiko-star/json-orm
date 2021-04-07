<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use App\Orm\Entity\Contracts\ContainsDefinitionInterface;

/**
 * Base class for all kind of Entities.
 * Contains all general methods.
 *
 * @package App\Orm\Entity
 */
abstract class AbstractWidget extends AbstractEntity
{
    /**
     * @var string Type of the current Widget.
     */
    protected string $widgetType;

    /**
     * @return string
     */
    public function getWidgetType() : string
    {
        return $this->widgetType;
    }

    /**
     * @param string $widgetType
     */
    public function setWidgetType(string $widgetType) : void
    {
        $this->widgetType = $widgetType;
    }
}
