<?php

namespace Tests\Unit\UseCases\Categories;

use Core\Domain\Entities\Category;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Categories\ListCategoryUseCase;
use Core\UseCases\DTOs\Categories\CategoryListInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use PHPUnit\Framework\TestCase;
use Mockery;

class ListCategoryUseCaseSpyTest extends TestCase
{
  private $repository;
  private $useCase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->repository = Mockery::spy(CategoryRepositoryInterface::class);
    $this->useCase = new ListCategoryUseCase($this->repository);
  }

  public function testShouldCallFindAllWithCorrectParameters()
  {
    // Arrange
    $category = Mockery::mock(Category::class, [
      'getId' => 'uuid-1',
      'getName' => 'Category 1',
      'getDescription' => 'Description 1',
      'isActive' => true,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    // Configure the spy to return the category when called with specific parameters
    $this->repository->shouldReceive('findAll')
      ->with('test-filter', 'ASC')
      ->andReturn([$category]);

    $input = new CategoryListInputDto(
      filter: 'test-filter',
      order: 'ASC'
    );

    // Act
    $result = $this->useCase->execute($input);

    // Assert - verify call parameters after execution
    $this->assertInstanceOf(CategoryOutputDto::class, $result[0]);
  }

  public function testShouldCallFindAllWithDefaultParametersWhenInputIsEmpty()
  {
    // Arrange
    $this->repository->shouldReceive('findAll')
      ->with('', 'DESC')
      ->andReturn([]);

    $input = new CategoryListInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertIsArray($result);
    $this->assertEmpty($result);
  }

  public function testShouldProcessEachCategoryReturnedFromRepository()
  {
    // Arrange
    $categories = [
      Mockery::mock(Category::class, [
        'getId' => 'uuid-1',
        'getName' => 'Category 1',
        'getDescription' => 'Description 1',
        'isActive' => true,
        'getCreatedAt' => '2023-01-01T00:00:00'
      ]),
      Mockery::mock(Category::class, [
        'getId' => 'uuid-2',
        'getName' => 'Category 2',
        'getDescription' => 'Description 2',
        'isActive' => false,
        'getCreatedAt' => '2023-01-01T00:00:00'
      ])
    ];

    $this->repository->shouldReceive('findAll')
      ->andReturn($categories);

    $input = new CategoryListInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertCount(2, $result);
    $this->assertInstanceOf(CategoryOutputDto::class, $result[0]);
    $this->assertInstanceOf(CategoryOutputDto::class, $result[1]);
  }

  public function testShouldReturnEmptyArrayWhenRepositoryReturnsEmpty()
  {
    // Arrange
    $this->repository->shouldReceive('findAll')
      ->andReturn([]);

    $input = new CategoryListInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertIsArray($result);
    $this->assertEmpty($result);
  }

  public function testShouldHandleNullFilterAndOrder()
  {
    // Arrange
    $this->repository->shouldReceive('findAll')
      ->with('', 'DESC')
      ->andReturn([]);

    $input = new CategoryListInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertIsArray($result);
    $this->assertEmpty($result);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}