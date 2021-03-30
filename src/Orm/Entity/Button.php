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
     *
     * @return array
     */
    public function initializeContent(Content $content) : array
    {
        $data = $content->getContent();

        return [
            'text' => $data['text'],
        ];
    }
}
