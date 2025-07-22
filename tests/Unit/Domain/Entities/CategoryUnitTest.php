<?php

namespace Tests\Unit\Domain\Entities;

use Core\Domain\Entities\Category;
use Core\Domain\Exceptions\EntityValidationException;
use Core\Domain\ValueObjects\BooleanValue;
use Core\Domain\ValueObjects\CreatedAt;
use Core\Domain\ValueObjects\SimpleName;
use Core\Domain\ValueObjects\SimpleText;
use Core\Domain\ValueObjects\Uuid;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Throwable;

class CategoryUnitTest extends TestCase
{
  public function testAttributes()
  {
    $category = new Category(
      name: 'New category',
      description: 'New description',
      isActive: true
    );

    $this->assertNotEmpty($category->getId());
    $this->assertTrue(RamseyUuid::isValid($category->getId()));
    $this->assertEquals('New category', $category->getName());
    $this->assertEquals('New description', $category->getDescription());
    $this->assertTrue($category->isActive());
  }

  public function testImmutability()
  {
    $uuid = RamseyUuid::uuid4()->toString();
    $category = new Category(
      id: $uuid,
      name: 'Test Category',
      description: 'Test Description'
    );

    $this->assertEquals($uuid, $category->getId());
    $this->assertInstanceOf(CreatedAt::class, $category->createdAt);
    $this->assertInstanceOf(Uuid::class, $category->id);
    $this->assertInstanceOf(SimpleName::class, $category->name);
    $this->assertInstanceOf(SimpleText::class, $category->description);
    $this->assertInstanceOf(BooleanValue::class, $category->isActive);
  }

  public function testActivateAndDeactivate()
  {
    $category = new Category(
      name: 'New Category',
      isActive: false,
    );

    $this->assertFalse($category->isActive());
    $category->activate();
    $this->assertTrue($category->isActive());

    $category->deactivate();
    $this->assertFalse($category->isActive());
  }

  public function testUpdate()
  {
    $uuid = RamseyUuid::uuid4()->toString();
    $category = new Category(
      id: $uuid,
      name: 'New category',
      description: 'New description',
      isActive: true
    );

    $category->update(
      name: 'Updated name',
      description: 'Updated description',
    );

    $this->assertEquals($uuid, $category->getId());
    $this->assertEquals('Updated name', $category->getName());
    $this->assertEquals('Updated description', $category->getDescription());
    $this->assertTrue($category->isActive());
  }

  public function testExceptionInvalidName()
  {
    $this->expectException(EntityValidationException::class);
    new Category(
      name: 'Ne',
      description: 'New Desc',
    );
  }

  public function testExceptionInvalidDescription()
  {
    $longDescription = str_repeat('a', 1001);

    $this->expectException(EntityValidationException::class);
    new Category(
      name: 'New Category',
      description: $longDescription,
    );
  }

  public function testExceptionInvalidSimpleName()
  {
    $this->expectException(EntityValidationException::class);
    new Category(
      name: 'InvalidName@!',
      description: 'New Description'
    );
  }

  public function testMagicMethods()
  {
    $category = new Category(
      name: 'Test Category',
      description: 'Test Description'
    );

    // Test magic getter
    $this->assertInstanceOf(CreatedAt::class, $category->createdAt);
    $this->assertInstanceOf(Uuid::class, $category->id);
    $this->assertInstanceOf(SimpleName::class, $category->name);
    $this->assertInstanceOf(SimpleText::class, $category->description);
    $this->assertInstanceOf(BooleanValue::class, $category->isActive);

    // Test magic method ID
    $this->assertEquals($category->getId(), $category->id());
  }

  public function testGetCreatedAtWhenNotProvided()
  {
    $category = new Category(
      name: 'Test Category',
      description: 'Test Description'
    );

    $createdAt = $category->getCreatedAt();
    $this->assertNotEmpty($createdAt);
    $this->assertMatchesRegularExpression(
      '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+\d{4}$/', 
      $createdAt
    );
  }

  public function testGetCreatedAtWithString()
  {
    $dateString = '2023-12-31T23:59:59+0000';
    $category = new Category(
      name: 'Test Category',
      description: 'Test Description',
      createdAt: $dateString
    );

    $this->assertEquals($dateString, $category->getCreatedAt());
  }

  public function testGetCreatedAtWithCreatedAtObject()
  {
    $createdAtObj = CreatedAt::create('2024-01-01T00:00:00+0000');
    $category = new Category(
      name: 'Test Category',
      description: 'Test Description',
      createdAt: $createdAtObj
    );

    $this->assertEquals((string) $createdAtObj, $category->getCreatedAt());
  }
}