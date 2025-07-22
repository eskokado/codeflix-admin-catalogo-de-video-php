<?php

namespace Tests\Unit\Domain\ValueObjects;

use Core\Domain\ValueObjects\BooleanValue;
use PHPUnit\Framework\TestCase;

class BooleanValueTest extends TestCase
{
  public function testConstructor()
  {
    $boolTrue = new BooleanValue(true);
    $boolFalse = new BooleanValue(false);

    $this->assertTrue($boolTrue->isTrue());
    $this->assertFalse($boolTrue->isFalse());

    $this->assertTrue($boolFalse->isFalse());
    $this->assertFalse($boolFalse->isTrue());
  }

  public function testStaticCreation()
  {
    $boolTrue = BooleanValue::true();
    $boolFalse = BooleanValue::false();

    $this->assertTrue($boolTrue->isTrue());
    $this->assertTrue($boolFalse->isFalse());
  }

  public function testToggle()
  {
    $boolTrue = BooleanValue::true();
    $boolToggled = $boolTrue->toggle();

    $this->assertTrue($boolTrue->isTrue());
    $this->assertTrue($boolToggled->isFalse());

    $boolFalse = BooleanValue::false();
    $boolToggled = $boolFalse->toggle();

    $this->assertTrue($boolFalse->isFalse());
    $this->assertTrue($boolToggled->isTrue());
  }

  public function testToString()
  {
    $boolTrue = BooleanValue::true();
    $boolFalse = BooleanValue::false();

    $this->assertEquals('true', (string) $boolTrue);
    $this->assertEquals('false', (string) $boolFalse);
  }
}