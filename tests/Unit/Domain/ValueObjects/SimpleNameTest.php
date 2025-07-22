<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\Exceptions\EntityValidationException;
use Core\Domain\ValueObjects\SimpleName;
use PHPUnit\Framework\TestCase;

class SimpleNameTest extends TestCase
{
  public function testConstructor()
  {
    $name = new SimpleName('Product Type');
    $this->assertEquals('Product Type', (string) $name);
  }

  public function testExceptionInvalidCharacters()
  {
    $this->expectException(EntityValidationException::class);
    $this->expectExceptionMessage('Value contains invalid characters');
    new SimpleName('Product@Type!');
  }

  public function testExceptionSingleWord()
  {
    $this->expectException(EntityValidationException::class);
    $this->expectExceptionMessage('Value must have at least a name and type/suffix');
    new SimpleName('Product');
  }

  public function testCreate()
  {
    $name = SimpleName::create('Product Type', 3, 100);
    $this->assertEquals('Product Type', (string) $name);
  }

  public function testExceptionMinLength()
  {
    $this->expectException(EntityValidationException::class);
    SimpleName::create('P T', 4, 100);
  }

  public function testGetBaseName()
  {
    $name = new SimpleName('Product Standard Type');
    $this->assertEquals('Product', $name->getBaseName());
  }

  public function testGetType()
  {
    $name = new SimpleName('Product Standard Type');
    $this->assertEquals('Type', $name->getType());
  }

  public function testGetQualifiers()
  {
    $name = new SimpleName('Product Standard Type');
    $this->assertEquals(['Standard'], $name->getQualifiers());

    $name = new SimpleName('Product Standard Premium Type');
    $this->assertEquals(['Standard', 'Premium'], $name->getQualifiers());
  }

  public function testGetInitials()
  {
    $name = new SimpleName('Product Type');
    $this->assertEquals('PT', $name->getInitials());

    $name = new SimpleName('Article Review');
    $this->assertEquals('AR', $name->getInitials());
  }
}