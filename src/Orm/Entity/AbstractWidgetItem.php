<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

abstract class AbstractWidgetItem extends AbstractEntity
{
    protected string $widgetItemType;

    /**
     * @return string
     */
    public function getWidgetItemType() : string
    {
        return $this->widgetItemType;
    }

    /**
     * @param string $widgetItemType
     */
    public function setWidgetItemType(string $widgetItemType) : void
    {
        $this->widgetItemType = $widgetItemType;
    }
}