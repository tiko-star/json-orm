<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Orm\Persistence\ContentObjectStorage;

/**
 * Simple value object that holds the current state of the dispatched content.
 *
 * @package App\Orm\ContentManagement
 */
class DispatchedContent
{
    /**
     * @var \App\Orm\Persistence\ContentObjectStorage Storage to keep a new content.
     */
    protected ContentObjectStorage $new;

    /**
     * @var \App\Orm\Persistence\ContentObjectStorage Storage to keep a modified content.
     */
    protected ContentObjectStorage $modified;

    /**
     * @var \App\Orm\Persistence\ContentObjectStorage Storage to keep a removed content.
     */
    protected ContentObjectStorage $removed;

    public function __construct()
    {
        $this->new = new ContentObjectStorage();
        $this->modified = new ContentObjectStorage();
        $this->removed = new ContentObjectStorage();
    }

    /**
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function getNew() : ContentObjectStorage
    {
        return $this->new;
    }

    /**
     * @param \App\Orm\Persistence\ContentObjectStorage $new
     */
    public function setNew(ContentObjectStorage $new) : void
    {
        $this->new = $new;
    }

    /**
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function getModified() : ContentObjectStorage
    {
        return $this->modified;
    }

    /**
     * @param \App\Orm\Persistence\ContentObjectStorage $modified
     */
    public function setModified(ContentObjectStorage $modified) : void
    {
        $this->modified = $modified;
    }

    /**
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function getRemoved() : ContentObjectStorage
    {
        return $this->removed;
    }

    /**
     * @param \App\Orm\Persistence\ContentObjectStorage $removed
     */
    public function setRemoved(ContentObjectStorage $removed) : void
    {
        $this->removed = $removed;
    }
}
