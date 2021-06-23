<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

/**
 * Base class for all kind of Widgets.
 * Contains all general methods.
 *
 * @package App\Orm\Entity
 */
class Widget extends AbstractEntity
{
    /**
     * @var string Type of the current Widget.
     */
    protected string $widgetType;

    /**
     * Create array representation of the current entity.
     *
     * @return array
     */
    public function convertToArray() : array
    {
        return [
            'type'       => $this->getType(),
            'widgetType' => $this->getWidgetType(),
            'hash'       => (string) $this->getHash(),
            'params'     => $this->getParams(),
        ];
    }

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
