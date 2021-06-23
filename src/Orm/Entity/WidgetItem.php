<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

/**
 * Base class for all kind of Widget Items.
 * Contains all general methods.
 *
 * @package App\Orm\Entity
 */
class WidgetItem extends AbstractEntity
{
    protected string $widgetItemType;

    /**
     * Create array representation of the current entity.
     *
     * @return array
     */
    public function convertToArray() : array
    {
        return [
            'type'           => $this->getType(),
            'widgetItemType' => $this->getWidgetItemType(),
            'hash'           => (string) $this->getHash(),
            'params'         => $this->getParams(),
        ];
    }

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
