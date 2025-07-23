<?php

namespace Tests\Unit\UseCases\Mappers\Categories;

use Core\Domain\Entities\Category;
use Core\UseCases\Mappers\Categories\CategoryOutputMapper;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CategoryOutputMapperTest extends TestCase
{
  public function testToDto()
  {
    // Arrange
    $id = Uuid::uuid4()->toString();
    $name = 'Movies Action';
    $description = 'Action movies description';
    $isActive = true;
    $createdAt = '2023-01-01 10:00:00';

    $category = Mockery::mock(Category::class);
    $category->shouldReceive('getId')->andReturn($id);
    $category->shouldReceive('getName')->andReturn($name);
    $category->shouldReceive('getDescription')->andReturn($description);
    $category->shouldReceive('isActive')->andReturn($isActive);
    $category->shouldReceive('getCreatedAt')->andReturn($createdAt);

    // Act
    $dto = CategoryOutputMapper::toDto($category);

    // Assert
    $this->assertInstanceOf(CategoryOutputDto::class, $dto);
    $this->assertEquals($id, $dto->id);
    $this->assertEquals($name, $dto->name);
    $this->assertEquals($description, $dto->description);
    $this->assertEquals($isActive, $dto->is_active);
    $this->assertEquals($createdAt, $dto->created_at);
  }

  public function testToCreateDto()
  {
    // Arrange
    $id = Uuid::uuid4()->toString();
    $name = 'Movies Action';
    $description = 'Action movies description';
    $isActive = true;

    $category = Mockery::mock(Category::class);
    $category->shouldReceive('getId')->andReturn($id);
    $category->shouldReceive('getName')->andReturn($name);
    $category->shouldReceive('getDescription')->andReturn($description);
    $category->shouldReceive('isActive')->andReturn($isActive);

    // Act
    $dto = CategoryOutputMapper::toCreateDto($category);

    // Assert
    $this->assertInstanceOf(CategoryOutputDto::class, $dto);
    $this->assertEquals($id, $dto->id);
    $this->assertEquals($name, $dto->name);
    $this->assertEquals($description, $dto->description);
    $this->assertEquals($isActive, $dto->is_active);
  }

  public function testToDtoCollection()
  {
    // Arrange
    $category1 = Mockery::mock(Category::class);
    $category1->shouldReceive('getId')->andReturn('id1');
    $category1->shouldReceive('getName')->andReturn('name1');
    $category1->shouldReceive('getDescription')->andReturn('desc1');
    $category1->shouldReceive('isActive')->andReturn(true);
    $category1->shouldReceive('getCreatedAt')->andReturn('2023-01-01');

    $category2 = Mockery::mock(Category::class);
    $category2->shouldReceive('getId')->andReturn('id2');
    $category2->shouldReceive('getName')->andReturn('name2');
    $category2->shouldReceive('getDescription')->andReturn('desc2');
    $category2->shouldReceive('isActive')->andReturn(false);
    $category2->shouldReceive('getCreatedAt')->andReturn('2023-01-02');

    $categories = [$category1, $category2];

    // Act
    $dtos = CategoryOutputMapper::toDtoCollection($categories);

    // Assert
    $this->assertCount(2, $dtos);
    $this->assertInstanceOf(CategoryOutputDto::class, $dtos[0]);
    $this->assertInstanceOf(CategoryOutputDto::class, $dtos[1]);
    $this->assertEquals('id1', $dtos[0]->id);
    $this->assertEquals('id2', $dtos[1]->id);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}