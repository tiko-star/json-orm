<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Doctrine\Entity\Language;
use App\Doctrine\Repository\ContentRepository;

class ContentProvider
{
    protected ContentRepository $repository;

    protected Language $currentLanguage;

    public function __construct(ContentRepository $repository, Language $currentLanguage)
    {
        $this->repository = $repository;
        $this->currentLanguage = $currentLanguage;
    }
}
