<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entities\Category;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\Domain\Repositories\PaginationInterface;
use Core\UseCases\Categories\PaginateCategoryUseCase;
use Core\UseCases\DTOs\Categories\CategoryPaginateInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use PHPUnit\Framework\TestCase;
use Mockery;
use stdClass;

class PaginateCategoryUseCaseUnitTest extends TestCase
{
  public function testPaginateEmpty()
  {
    // Arrange
    $mockPagination = $this->mockPagination();

    $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
    $categoryRepository->shouldReceive('paginate')
      ->with('', 'DESC', 1, 15)
      ->andReturn($mockPagination);

    $useCase = new PaginateCategoryUseCase($categoryRepository);
    $input = new CategoryPaginateInputDto();

    // Act
    $result = $useCase->execute($input);

    // Assert
    $this->assertIsArray($result->items);
    $this->assertCount(0, $result->items);
    $this->assertEquals(0, $result->total);
    $this->assertEquals(1, $result->current_page);
    $this->assertEquals(1, $result->last_page);
    $this->assertEquals(1, $result->first_page);
    $this->assertEquals(15, $result->per_page);
    $this->assertEquals(0, $result->to);
    $this->assertEquals(0, $result->from);
  }

  public function testPaginateWithItems()
  {
    // Arrange
    $mockItem1 = new stdClass();
    $mockItem1->id = 'uuid-1';
    $mockItem1->name = 'Category 1';
    $mockItem1->description = 'Description 1';
    $mockItem1->is_active = true;
    $mockItem1->created_at = '2023-01-01T00:00:00';

    $mockItem2 = new stdClass();
    $mockItem2->id = 'uuid-2';
    $mockItem2->name = 'Category 2';
    $mockItem2->description = 'Description 2';
    $mockItem2->is_active = false;
    $mockItem2->created_at = '2023-01-01T00:00:00';

    $mockCategory1 = Mockery::mock(Category::class, [
      'getId' => 'uuid-1',
      'getName' => 'Category 1',
      'getDescription' => 'Description 1',
      'isActive' => true,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    $mockCategory2 = Mockery::mock(Category::class, [
      'getId' => 'uuid-2',
      'getName' => 'Category 2',
      'getDescription' => 'Description 2',
      'isActive' => false,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    $mockPagination = $this->mockPagination([
      $mockItem1,
      $mockItem2
    ], 2, 1, 1, 1, 15, 2, 1);

    $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
    $categoryRepository->shouldReceive('paginate')
      ->with('', 'DESC', 1, 15)
      ->andReturn($mockPagination);

    $categoryRepository->shouldReceive('toCategory')
      ->with($mockItem1)
      ->andReturn($mockCategory1);

    $categoryRepository->shouldReceive('toCategory')
      ->with($mockItem2)
      ->andReturn($mockCategory2);

    $useCase = new PaginateCategoryUseCase($categoryRepository);
    $input = new CategoryPaginateInputDto();

    // Act
    $result = $useCase->execute($input);

    // Assert
    $this->assertIsArray($result->items);
    $this->assertCount(2, $result->items);
    $this->assertInstanceOf(CategoryOutputDto::class, $result->items[0]);
    $this->assertInstanceOf(CategoryOutputDto::class, $result->items[1]);
    $this->assertEquals('uuid-1', $result->items[0]->id);
    $this->assertEquals('Category 1', $result->items[0]->name);
    $this->assertEquals('uuid-2', $result->items[1]->id);
    $this->assertEquals('Category 2', $result->items[1]->name);

    $this->assertEquals(2, $result->total);
    $this->assertEquals(1, $result->current_page);
    $this->assertEquals(1, $result->last_page);
    $this->assertEquals(1, $result->first_page);
    $this->assertEquals(15, $result->per_page);
    $this->assertEquals(2, $result->to);
    $this->assertEquals(1, $result->from);
  }

  public function testPaginateWithCustomParams()
  {
    // Arrange
    $mockCategory = Mockery::mock(Category::class, [
      'getId' => 'uuid-1',
      'getName' => 'Category 1',
      'getDescription' => 'Description 1',
      'isActive' => true,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    $mockItem = new stdClass();
    $mockItem->id = 'uuid-1';
    $mockItem->name = 'Category 1';
    $mockItem->description = 'Description 1';
    $mockItem->is_active = true;
    $mockItem->created_at = '2023-01-01T00:00:00';

    $mockPagination = $this->mockPagination(
      items: [$mockItem],
      total: 20,
      currentPage: 2,
      lastPage: 4,
      firstPage: 1,
      perPage: 5,
      to: 10,
      from: 6
    );

    $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
    $categoryRepository->shouldReceive('paginate')
      ->with('test', 'ASC', 2, 5)
      ->andReturn($mockPagination);

    $categoryRepository->shouldReceive('toCategory')
      ->with($mockItem)
      ->andReturn($mockCategory);

    $useCase = new PaginateCategoryUseCase($categoryRepository);
    $input = new CategoryPaginateInputDto(
      filter: 'test',
      order: 'ASC',
      page: 2,
      totalPage: 5
    );

    // Act
    $result = $useCase->execute($input);

    // Assert
    $this->assertCount(1, $result->items);
    $this->assertEquals('uuid-1', $result->items[0]->id);
    $this->assertEquals(20, $result->total);
    $this->assertEquals(2, $result->current_page);
    $this->assertEquals(4, $result->last_page);
    $this->assertEquals(1, $result->first_page);
    $this->assertEquals(5, $result->per_page);
    $this->assertEquals(10, $result->to);
    $this->assertEquals(6, $result->from);
  }

  protected function mockPagination(
    array $items = [],
    int $total = 0,
    int $currentPage = 1,
    int $lastPage = 1,
    int $firstPage = 1,
    int $perPage = 15,
    int $to = 0,
    int $from = 0
  ): PaginationInterface {
    $mockPagination = Mockery::mock(PaginationInterface::class);
    $mockPagination->shouldReceive('items')->andReturn($items);
    $mockPagination->shouldReceive('total')->andReturn($total);
    $mockPagination->shouldReceive('currentPage')->andReturn($currentPage);
    $mockPagination->shouldReceive('lastPage')->andReturn($lastPage);
    $mockPagination->shouldReceive('firstPage')->andReturn($firstPage);
    $mockPagination->shouldReceive('perPage')->andReturn($perPage);
    $mockPagination->shouldReceive('to')->andReturn($to);
    $mockPagination->shouldReceive('from')->andReturn($from);

    return $mockPagination;
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}