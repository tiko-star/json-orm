<?php

declare(strict_types = 1);

namespace App\Orm\ContentManagement;

use App\Orm\Persistence\ContentObjectStorage;

use function count;

/**
 * Handles content management.
 * Detects new, modified and removed content based on the upcoming and existing content data.
 *
 * @package App\Orm\ContentManagement
 */
class ContentDispatcher
{
    /**
     * Determine new, modified and removed content based on the given data.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     *
     * @return \App\Orm\ContentManagement\DispatchedContent
     */
    public function dispatch(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : DispatchedContent
    {
        $dispatched = new DispatchedContent();

        $new = $this->determineNewContent($upcoming, $existing);

        if (count($new)) {
            $dispatched->setNew($new);
        }

        $modified = $this->determineModifiedContent($upcoming, $existing);

        if (count($modified)) {
            $dispatched->setModified($modified);
        }

        $removed = $this->determineRemovedContent($upcoming, $existing);

        if (count($removed)) {
            $dispatched->setRemoved($removed);
        }

        return $dispatched;
    }

    /**
     * Determine new content which is going to be persisted for the first time.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     *
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function determineNewContent(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : ContentObjectStorage
    {
        $contents = new ContentObjectStorage();
        $upcoming->rewind();
        $existing->rewind();

        while ($upcoming->valid()) {
            /** @var \App\Orm\Entity\Hash $hash */
            $hash = $upcoming->current();

            if (!$existing->contains($hash)) {
                $contents->attach($hash, $upcoming->getInfo());
            }

            $upcoming->next();
        }

        return $contents;
    }

    /**
     * Determine modified content that will be updated during persistence actions.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     *
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function determineModifiedContent(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : ContentObjectStorage
    {
        $contents = new ContentObjectStorage();

        $upcoming->rewind();
        $existing->rewind();

        while ($upcoming->valid()) {
            /** @var \App\Orm\Entity\Hash $hash */
            $hash = $upcoming->current();

            if ($existing->contains($hash)) {
                /** @var \App\Doctrine\Entity\Content $existingContent */
                $existingContent = $existing[$hash];
                /** @var \App\Doctrine\Entity\Content $upcomingContent */
                $upcomingContent = $upcoming->getInfo();

                if ($existingContent->getContent() !== $upcomingContent->getContent()
                    && !empty($upcomingContent->getContent())) {
                    $contents->attach($upcoming->current(), $upcoming->getInfo());
                }
            }

            $upcoming->next();
        }

        return $contents;
    }

    /**
     * Determine removed content that will be completely removed during persistence actions.
     *
     * @param \App\Orm\Persistence\ContentObjectStorage $upcoming
     * @param \App\Orm\Persistence\ContentObjectStorage $existing
     *
     * @return \App\Orm\Persistence\ContentObjectStorage
     */
    public function determineRemovedContent(ContentObjectStorage $upcoming, ContentObjectStorage $existing) : ContentObjectStorage
    {
        $contents = new ContentObjectStorage();

        $upcoming->rewind();
        $existing->rewind();

        while ($existing->valid()) {
            /** @var \App\Orm\Entity\Hash $hash */
            $hash = $existing->current();
            $contains = false;

            if ($upcoming->contains($hash)) {
                /** @var \App\Doctrine\Entity\Content $upcomingContent */
                $upcomingContent = $upcoming[$hash];

                // In case the content is empty treat it asa removed.
                if (!empty($upcomingContent->getContent())) {
                    $contains = true;
                }
            }

            if (!$contains) {
                $contents->attach($hash, $existing->getInfo());
            }

            $existing->next();
        }

        return $contents;
    }
}
