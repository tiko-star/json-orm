<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Entity\Decorators;

use App\Orm\Entity\AbstractEntity;
use App\Orm\Entity\Decorators\AbstractDecorator;
use App\Tests\Orm\EntityCreators;
use PHPUnit\Framework\TestCase;

class EntityDecoratorTest extends TestCase
{
    use EntityCreators;

    public function testGetType_WhenDecorated_ReturnsDecoratedType() : void
    {
        $widget = $this->createSimpleWidget();
        $widget->setType('countdown');

        $widget = $this->decorateWithAsterisks($widget);
        $widget = $this->decorateWithDashes($widget);

        $this->assertEquals('--**countdown**--', $widget->getType());
    }

    public function testGetType_WhenDecoratedInReverseOrder_ReturnsDecoratedType() : void
    {
        $widget = $this->createSimpleWidget();
        $widget->setType('countdown');

        $widget = $this->decorateWithDashes($widget);
        $widget = $this->decorateWithAsterisks($widget);

        $this->assertEquals('**--countdown--**', $widget->getType());
    }

    protected function decorateWithAsterisks(AbstractEntity $entity) : AbstractEntity
    {
        return new class($entity) extends AbstractDecorator {
            public function getType() : string
            {
                $type = parent::getType();

                return '**'.$type.'**';
            }
        };
    }

    protected function decorateWithDashes(AbstractEntity $entity) : AbstractEntity
    {
        return new class($entity) extends AbstractDecorator {
            public function getType() : string
            {
                $type = parent::getType();

                return '--'.$type.'--';
            }
        };
    }
}
