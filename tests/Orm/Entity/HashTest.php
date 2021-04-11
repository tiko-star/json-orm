<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Entity;

use App\Orm\Entity\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    public function testIsDraft_WhenInternalHashIsEmpty_ReturnsTrue() : void
    {
        $hash = new Hash('');

        $this->assertTrue($hash->isDraft());
    }

    public function testIsDraft_WhenInternalHashStartsWithPrefix_ReturnsTrue() : void
    {
        $hash = new Hash('__e76f2ba5-9e84-4141-975c-af48a62d4ac1');

        $this->assertTrue($hash->isDraft());
    }

    public function testIsDraft_WhenInternalHashIsUUID_ReturnsFalse() : void
    {
        $hash = new Hash('e76f2ba5-9e84-4141-975c-af48a62d4ac1');

        $this->assertFalse($hash->isDraft());
    }

    public function testToString_ReturnsInternalHash() : void
    {
        $hash = new Hash('e76f2ba5-9e84-4141-975c-af48a62d4ac1');
        $draft = new Hash('');

        $this->assertEquals('e76f2ba5-9e84-4141-975c-af48a62d4ac1', (string) $hash);
        $this->assertEquals('', (string) $draft);
    }
}
