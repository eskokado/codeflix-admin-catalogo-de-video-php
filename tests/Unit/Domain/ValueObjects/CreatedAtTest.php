<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObjects;

use Core\Domain\ValueObjects\CreatedAt;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use DateTime;

class CreatedAtTest extends TestCase
{
  public function testCreateFromString(): void
  {
    $dateString = '2023-01-15T14:30:00+00:00';
    $createdAt = CreatedAt::create($dateString);

    $this->assertInstanceOf(CreatedAt::class, $createdAt);
    $this->assertEquals(new DateTimeImmutable($dateString), $createdAt->getValueCreatedAt());
  }

  public function testCreateFromDateTimeImmutable(): void
  {
    $dateTime = new DateTimeImmutable('2023-01-15T14:30:00+00:00');
    $createdAt = CreatedAt::create($dateTime);

    $this->assertInstanceOf(CreatedAt::class, $createdAt);
    $this->assertSame($dateTime, $createdAt->getValueCreatedAt());
  }

  public function testCreateFromDateTime(): void
  {
    $dateTime = new DateTime('2023-01-15T14:30:00+00:00');
    $createdAt = CreatedAt::create($dateTime);

    $this->assertInstanceOf(CreatedAt::class, $createdAt);
    $this->assertEquals(DateTimeImmutable::createFromMutable($dateTime), $createdAt->getValueCreatedAt());
    $this->assertNotSame($dateTime, $createdAt->getValueCreatedAt());
  }

  public function testImmutability(): void
  {
    $dateTime = new DateTimeImmutable('2023-01-15T14:30:00+00:00');
    $createdAt = CreatedAt::create($dateTime);

    $reflection = new \ReflectionClass($createdAt);
    $property = $reflection->getProperty('valueCreated');
    $property->setAccessible(true);

    $initialValue = $property->getValue($createdAt);

    // Tentativa de modificar o valor retornado não deve afetar o objeto original
    $returnedValue = $createdAt->getValueCreatedAt();
    $modifiedValue = $returnedValue->modify('+1 day');

    $this->assertEquals($initialValue, $property->getValue($createdAt));
  }

  public function testSerializeUnserialize(): void
  {
    $dateString = '2023-01-15T14:30:00+00:00';
    $createdAt = CreatedAt::create($dateString);

    $serialized = serialize($createdAt);
    $unserialized = unserialize($serialized);

    $this->assertEquals($createdAt->getValueCreatedAt(), $unserialized->getValueCreatedAt());
  }

  public function testToString(): void
  {
    $dateString = '2023-01-15T14:30:00+00:00';
    $dateTime = new DateTimeImmutable($dateString);
    $createdAt = CreatedAt::create($dateTime);

    $this->assertEquals($dateTime->format(\DateTimeInterface::ISO8601), (string) $createdAt);
  }

  public function testInvalidDateString(): void
  {
    $this->expectException(\Exception::class);
    CreatedAt::create('invalid-date');
  }

  public function testEquality(): void
  {
    $dateString = '2023-01-15T14:30:00+00:00';
    $createdAt1 = CreatedAt::create($dateString);
    $createdAt2 = CreatedAt::create($dateString);

    // Test equality through serialization
    $this->assertEquals(serialize($createdAt1), serialize($createdAt2));
  }
}