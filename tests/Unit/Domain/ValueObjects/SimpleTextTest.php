<?php

namespace Tests\Unit\Domain\ValueObject;

use Core\Domain\Exceptions\EntityValidationException;
use Core\Domain\ValueObjects\SimpleText;
use PHPUnit\Framework\TestCase;

class SimpleTextTest extends TestCase
{
  public function testConstructor()
  {
    $text = new SimpleText('Simple text value');
    $this->assertEquals('Simple text value', (string) $text);
  }

  public function testTrimValue()
  {
    $text = new SimpleText('  Text with spaces  ');
    $this->assertEquals('Text with spaces', (string) $text);
  }

  public function testStaticCreate()
  {
    $text = SimpleText::create('Valid text', 3, 100);
    $this->assertEquals('Valid text', (string) $text);
  }

  public function testExceptionMinLength()
  {
    $this->expectException(EntityValidationException::class);
    $this->expectExceptionMessage('Value must have at least 5 characters');
    SimpleText::create('Txt', 5, 100);
  }

  public function testExceptionMaxLength()
  {
    $this->expectException(EntityValidationException::class);
    $this->expectExceptionMessage('Value must have at most 10 characters');
    SimpleText::create('Text too long for test', 3, 10);
  }

  public function testCreateWithAttributeAndObject()
  {
    $text = SimpleText::create('Valid text', 3, 100, 'title', 'Post');
    $this->assertEquals('Valid text', (string) $text);

    $this->expectException(EntityValidationException::class);
    $this->expectExceptionMessage('Post Title must have at least 5 characters');
    SimpleText::create('Txt', 5, 100, 'title', 'Post');
  }

  public function testFormatError()
  {
    // Using reflection to test protected method
    $reflectionClass = new \ReflectionClass(SimpleText::class);
    $method = $reflectionClass->getMethod('formatError');
    $method->setAccessible(true);

    $result = $method->invokeArgs(null, ['is invalid']);
    $this->assertEquals('Value is invalid', $result);

    $result = $method->invokeArgs(null, ['is invalid', 'name']);
    $this->assertEquals('Name is invalid', $result);

    $result = $method->invokeArgs(null, ['is invalid', 'name', 'User']);
    $this->assertEquals('User Name is invalid', $result);
  }
}