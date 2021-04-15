<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Persistence;

use App\Doctrine\Entity\Content;
use App\Orm\Entity\Hash;
use App\Orm\Persistence\ContentObjectStorage;
use PHPUnit\Framework\TestCase;

class ContentObjectStorageTest extends TestCase
{
    public function testContains_WithSameHashes_ReturnsTrue() : void
    {
        $storage = $this->createObjectStorage();

        $content = new Content();
        $content->setHash('dfcba963-b14e-41c1-b8c8-33cb0509f0a4');
        $content->setContent(['foo' => 'bar']);

        $storage->attach(new Hash('dfcba963-b14e-41c1-b8c8-33cb0509f0a4'), $content);

        $this->assertTrue($storage->contains(new Hash('dfcba963-b14e-41c1-b8c8-33cb0509f0a4')));
    }

    protected function createObjectStorage() : ContentObjectStorage
    {
        return new ContentObjectStorage();
    }
}
