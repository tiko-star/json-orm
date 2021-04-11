<?php

declare(strict_types = 1);

namespace App\Tests\Utilities;

use App\Utilities\ObjectMap;
use PHPUnit\Framework\TestCase;
use Generator;

use function iterator_to_array;

class ObjectMapTest extends TestCase
{
    public function testConstructor_WhenInputIsArray_CreatesValidInstance() : void
    {
        $input = [
            'foo' => (object) ['foo' => 'bar'],
            'bar' => (object) ['bar' => 'baz'],
        ];

        $map = $this->createObjectMapInstance($input);
        $entries = iterator_to_array($map);

        $this->assertIsObject($entries['foo']);
        $this->assertIsObject($entries['bar']);

        $this->assertEquals('bar', $entries['foo']->foo);
        $this->assertEquals('baz', $entries['bar']->bar);
    }

    public function testConstructor_WhenInputIsGenerator_CreatesValidInstance() : void
    {
        $generator = static function () : Generator {
            yield (object) ['foo' => 'bar'];
            yield (object) ['bar' => 'baz'];
            yield 'foo' => (object) ['bat' => 'bak'];
        };

        $map = $this->createObjectMapInstance($generator());
        $entries = iterator_to_array($map);

        $this->assertIsObject($entries['0']);
        $this->assertIsObject($entries['1']);
        $this->assertIsObject($entries['foo']);

        $this->assertEquals('bar', $entries['0']->foo);
        $this->assertEquals('baz', $entries['1']->bar);
        $this->assertEquals('bak', $entries['foo']->bat);
    }

    public function testObjectMap_IsIterable() : void
    {
        $map = $this->createObjectMapInstance();

        $this->assertIsIterable($map);
    }

    public function testObjectMap_IsCountable() : void
    {
        $emptyMap = $this->createObjectMapInstance();
        $map = $this->createObjectMapInstance([
            (object) ['foo' => 'bar'],
            (object) ['bar' => 'baz'],
        ]);

        $this->assertCount(0, $emptyMap);
        $this->assertCount(2, $map);
    }

    public function testSet_WithUniqueKey_AddsNewEntryToTheMap() : void
    {
        $map = $this->createObjectMapInstance();

        $entry = (object) ['name' => 'John'];
        $map->set('person', $entry);
        $entries = iterator_to_array($map);

        $this->assertIsObject($entries['person']);
        $this->assertEquals($entries['person'], $entry);
    }

    public function testSet_WithExistingKey_OverwritesExistingEntry() : void
    {
        $map = $this->createObjectMapInstance();

        $entry = (object) ['name' => 'John'];
        $map->set('person', $entry);
        $entries = iterator_to_array($map);

        $this->assertEquals($entries['person'], $entry);

        $anotherEntry = (object) ['age' => 25];
        $map->set('person', $anotherEntry);

        $entries = iterator_to_array($map);

        $this->assertNotEquals($entries['person'], $entry);
        $this->assertEquals($entries['person'], $anotherEntry);
    }

    public function testGet_WithExistingKey_ReturnsSpecificEntry() : void
    {
        $entry = (object) ['name' => 'Bobby'];
        $map = $this->createObjectMapInstance(['person' => $entry]);

        $this->assertEquals($entry, $map->get('person'));
    }

    public function testGet_WithNonExistingKey_ReturnNull() : void
    {
        $map = $this->createObjectMapInstance();

        $this->assertNull($map->get('person'));
    }

    public function testHas_WithExistingAndNonExistingKeys_ReturnsTrueAndFalseRespectively() : void
    {
        $map = $this->createObjectMapInstance(['person' => (object) ['name' => 'Bobby']]);

        $this->assertTrue($map->has('person'));
        $this->assertFalse($map->has('animal'));
    }

    public function testDelete_WithExistingKey_ReturnsRemovedItemAndRemovesItemFromTheMap() : void
    {
        $entry = (object) ['name' => 'Bobby'];
        $map = $this->createObjectMapInstance(['person' => $entry]);

        $deletedEntry = $map->delete('person');
        $entries = iterator_to_array($map);

        $this->assertEquals($deletedEntry, $entry);
        $this->assertArrayNotHasKey('person', $entries);
    }

    public function testDelete_WithNonExistingKey_ReturnsNull() : void
    {
        $map = $this->createObjectMapInstance();

        $this->assertNull($map->delete('person'));
    }

    public function testClear_RemovesAllEntriesFromTheMap() : void
    {
        $person = (object) ['name' => 'Bobby'];
        $pet = (object) ['name' => 'Mickey'];

        $map = $this->createObjectMapInstance(['person' => $person, 'pet' => $pet]);

        $this->assertCount(2, $map);
        $map->clear();

        $this->assertCount(0, $map);
        $this->assertNull($map->get('person'));
        $this->assertNull($map->get('pet'));
    }

    public function testKeys_ReturnsAllTheKeysOfTheMap() : void
    {
        $person = (object) ['name' => 'Bobby'];
        $pet = (object) ['name' => 'Mickey'];

        $map = $this->createObjectMapInstance(['person' => $person, 'pet' => $pet]);

        $this->assertEquals(['person', 'pet'], $map->keys());
    }

    public function testValues_ReturnsAllTheValuesOfTheMap() : void
    {
        $person = (object) ['name' => 'Bobby'];
        $pet = (object) ['name' => 'Mickey'];

        $map = $this->createObjectMapInstance(['person' => $person, 'pet' => $pet]);

        $this->assertEquals([$person, $pet], $map->values());
    }

    protected function createObjectMapInstance(iterable $entries = []) : ObjectMap
    {
        return new ObjectMap($entries);
    }
}
