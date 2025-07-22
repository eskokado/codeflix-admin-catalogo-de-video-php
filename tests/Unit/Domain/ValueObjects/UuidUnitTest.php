<?php

namespace Tests\Unit\Domain\ValueObjects;

use Core\Domain\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
  public function testConstructorWithValidUuid()
  {
    $validUuid = 'aa2d0be8-1f47-4ff3-86da-693e3539bb49';
    $uuid = new Uuid($validUuid);

    $this->assertEquals($validUuid, (string) $uuid);
  }

  public function testExceptionWithInvalidUuid()
  {
    $this->expectException(\InvalidArgumentException::class);
    new Uuid('invalid-uuid');
  }

  public function testRandomUuid()
  {
    $uuid1 = Uuid::random();
    $uuid2 = Uuid::random();

    $this->assertInstanceOf(Uuid::class, $uuid1);
    $this->assertInstanceOf(Uuid::class, $uuid2);
    $this->assertNotEquals((string) $uuid1, (string) $uuid2);

    // Test if the generated UUIDs are valid by the Ramsey library
    $this->assertTrue(\Ramsey\Uuid\Uuid::isValid((string) $uuid1));
    $this->assertTrue(\Ramsey\Uuid\Uuid::isValid((string) $uuid2));
  }

  public function testToString()
  {
    $validUuid = 'aa2d0be8-1f47-4ff3-86da-693e3539bb49';
    $uuid = new Uuid($validUuid);

    $this->assertEquals($validUuid, $uuid->__tostring());
    $this->assertEquals($validUuid, (string) $uuid);
  }
}