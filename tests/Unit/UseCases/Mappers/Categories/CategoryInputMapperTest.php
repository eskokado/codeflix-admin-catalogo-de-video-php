<?php

namespace Tests\Unit\UseCases\Mappers\Categories;

use Core\Domain\Entities\Category;
use Core\Domain\ValueObjects\BooleanValue;
use Core\Domain\ValueObjects\SimpleName;
use Core\Domain\ValueObjects\SimpleText;
use Core\UseCases\Mappers\Categories\CategoryInputMapper;
use Core\UseCases\DTOs\Categories\CategoryCreateInputDto;
use Core\UseCases\DTOs\Categories\CategoryUpdateInputDto;
use Mockery;
use PHPUnit\Framework\TestCase;

class CategoryInputMapperTest extends TestCase
{
  public function testFromCreateDto()
  {
    // Arrange
    $name = 'Movies Action';
    $description = 'Action movies description';
    $isActive = true;

    $input = new CategoryCreateInputDto(
      name: $name,
      description: $description,
      isActive: $isActive
    );

    // Act
    $category = CategoryInputMapper::fromCreateDto($input);

    // Assert
    $this->assertInstanceOf(Category::class, $category);
    $this->assertEquals($name, $category->getName());
    $this->assertEquals($description, $category->getDescription());
    $this->assertEquals($isActive, $category->isActive());
  }

  public function testFromUpdateDto()
  {
    // Arrange
    $name = 'Movies Action';
    $description = 'Action movies description';
    $isActive = false;

    $input = new CategoryUpdateInputDto(
      id: 'any-id',
      name: $name,
      description: $description,
      isActive: $isActive
    );

    $existingCategory = Mockery::mock(Category::class);
    $existingCategory->shouldReceive('update')
      ->once()
      ->with(Mockery::type(SimpleName::class), Mockery::type(SimpleText::class))
      ->andReturnSelf();

    $existingCategory->shouldReceive('isActive')
      ->once()
      ->andReturn(true);

    $existingCategory->shouldReceive('deactivate')
      ->once();

    // Act
    $category = CategoryInputMapper::fromUpdateDto($input, $existingCategory);

    // Assert
    $this->assertSame($existingCategory, $category);
  }

  public function testFromUpdateDtoActivateCategory()
  {
    // Arrange
    $input = new CategoryUpdateInputDto(
      id: 'any-id',
      name: 'Movies Action',
      description: 'Action movies description',
      isActive: true
    );

    $existingCategory = Mockery::mock(Category::class);
    $existingCategory->shouldReceive('update')->andReturnSelf();
    $existingCategory->shouldReceive('isActive')->andReturn(false);
    $existingCategory->shouldReceive('activate')->once();

    // Act
    $category = CategoryInputMapper::fromUpdateDto($input, $existingCategory);

    // Assert
    $this->assertSame($existingCategory, $category);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}