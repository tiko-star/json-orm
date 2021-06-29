<?php

declare(strict_types = 1);

namespace App\Tests\Orm\Definition;

use App\Orm\Definition\DefinitionCompiler;
use App\Orm\Definition\Exception\DefinitionCompilationException;
use PHPUnit\Framework\TestCase;

class DefinitionCompilerTest extends TestCase
{
    public function testCreateDefinitionInstanceFromString_WithWrongJson_ThrowsException() : void
    {
        $compiler = $this->createCompilerInstance();

        $this->expectException(DefinitionCompilationException::class);
        $this->expectExceptionMessage('Invalid JSON definition: Syntax error');

        $compiler->createDefinitionInstanceFromString('{//}');
    }

    public function testCreateDefinitionInstanceFromString_WhenRequiredPropertyDefinitionIsMissing_ThrowsException() : void
    {
        $compiler = $this->createCompilerInstance();

        $this->expectException(DefinitionCompilationException::class);
        $this->expectExceptionMessage('Required definition property is missing: type');

        // Here name property's definition is missing
        $definition = <<<DEFINITION
{
  "containsChildren": false,
  "properties": [
    {
      "name": "text",
      "type": "STRING"
    },
    {
      "name": "link",
      "type": "STRING"
    }
  ]
}
DEFINITION;

        $compiler->createDefinitionInstanceFromString($definition);
    }

    protected function createCompilerInstance() : DefinitionCompiler
    {
        return new DefinitionCompiler();
    }
}
