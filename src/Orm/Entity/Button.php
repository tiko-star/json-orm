<?php

declare(strict_types = 1);

namespace App\Orm\Entity;

use App\Doctrine\Entity\Content;

class Button extends AbstractWidget
{
    /**
     * Initialize content data for current entity.
     *
     * @param \App\Doctrine\Entity\Content $content
     */
    public function initializeContent(Content $content) : void
    {
        $data = $content->getContent();
        $this->setButtonText($data['text']);
    }

    /**
     * Set text of the current button.
     *
     * @param string $text
     */
    protected function setButtonText(string $text) : void
    {
        $props = $this->getProperties();
        $props['text'] = $text;
        $this->setProperties($props);
    }
}