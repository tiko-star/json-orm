<?php

namespace App\Orm\Entity;

class AbstractWidget extends AbstractEntity
{
    /**
     * @var string
     */
    protected $widgetType;

    /**
     * @return mixed
     */
    public function getWidgetType() : string
    {
        return $this->widgetType;
    }

    /**
     * @param mixed $widgetType
     */
    public function setWidgetType(string $widgetType) : void
    {
        $this->widgetType = $widgetType;
    }

    public function jsonSerialize()
    {
        return parent::jsonSerialize() + ['widgetType' => $this->getWidgetType()];
    }
}