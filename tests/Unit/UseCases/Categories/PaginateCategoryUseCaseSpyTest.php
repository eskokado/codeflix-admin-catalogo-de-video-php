<?php

namespace Tests\Unit\UseCases\Categories;

use Core\Domain\Entities\Category;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\Domain\Repositories\PaginationInterface;
use Core\UseCases\Categories\PaginateCategoryUseCase;
use Core\UseCases\DTOs\Categories\CategoryPaginateInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\MockInterface;
use stdClass;

class PaginateCategoryUseCaseSpyTest extends TestCase
{
  /**
   * @var CategoryRepositoryInterface|MockInterface
   */
  private $repository;

  /**
   * @var PaginateCategoryUseCase
   */
  private $useCase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->repository = Mockery::spy(CategoryRepositoryInterface::class);
    $this->useCase = new PaginateCategoryUseCase($this->repository);
  }

  public function testShouldCallPaginateWithCorrectParameters()
  {
    // Arrange
    $mockPagination = $this->mockPagination();

    $this->repository->shouldReceive('paginate')
      ->with('test-filter', 'ASC', 2, 10)
      ->andReturn($mockPagination);

    $input = new CategoryPaginateInputDto(
      filter: 'test-filter',
      order: 'ASC',
      page: 2,
      totalPage: 10
    );

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertTrue(true);
    $this->repository->shouldHaveReceived('paginate')
      ->with('test-filter', 'ASC', 2, 10)
      ->once();

    $mockPagination->shouldHaveReceived('items')->once();
    $mockPagination->shouldHaveReceived('total')->once();
    $mockPagination->shouldHaveReceived('currentPage')->once();
    $mockPagination->shouldHaveReceived('lastPage')->once();
    $mockPagination->shouldHaveReceived('firstPage')->once();
    $mockPagination->shouldHaveReceived('perPage')->once();
    $mockPagination->shouldHaveReceived('to')->once();
    $mockPagination->shouldHaveReceived('from')->once();
  }

  public function testShouldCallPaginateWithDefaultParametersWhenInputIsEmpty()
  {
    // Arrange
    $mockPagination = $this->mockPagination();

    $this->repository->shouldReceive('paginate')
      ->with('', 'DESC', 1, 15)
      ->andReturn($mockPagination);

    $input = new CategoryPaginateInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertTrue(true);
    $this->repository->shouldHaveReceived('paginate')
      ->with('', 'DESC', 1, 15)
      ->once();
  }

  public function testShouldProcessEachItemReturnedFromRepository()
  {
    // Arrange
    $mockItem1 = new stdClass();
    $mockItem1->id = 'uuid-1';

    $mockItem2 = new stdClass();
    $mockItem2->id = 'uuid-2';

    $mockPagination = $this->mockPagination(
      items: [$mockItem1, $mockItem2],
      total: 2
    );

    $this->repository->shouldReceive('paginate')
      ->andReturn($mockPagination);

    $category1 = Mockery::mock(Category::class, [
      'getId' => 'uuid-1',
      'getName' => 'Category 1',
      'getDescription' => 'Description 1',
      'isActive' => true,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    $category2 = Mockery::mock(Category::class, [
      'getId' => 'uuid-2',
      'getName' => 'Category 2',
      'getDescription' => 'Description 2',
      'isActive' => false,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    $this->repository->shouldReceive('toCategory')
      ->with($mockItem1)
      ->andReturn($category1);

    $this->repository->shouldReceive('toCategory')
      ->with($mockItem2)
      ->andReturn($category2);

    $input = new CategoryPaginateInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertCount(2, $result->items);

    $this->repository->shouldHaveReceived('toCategory')
      ->with($mockItem1)
      ->once();

    $this->repository->shouldHaveReceived('toCategory')
      ->with($mockItem2)
      ->once();

    // Verificar se os métodos da entidade Category foram chamados
    $category1->shouldHaveReceived('getId')->once();
    $category1->shouldHaveReceived('getName')->once();
    $category1->shouldHaveReceived('getDescription')->once();
    $category1->shouldHaveReceived('isActive')->once();
    $category1->shouldHaveReceived('getCreatedAt')->once();

    $category2->shouldHaveReceived('getId')->once();
    $category2->shouldHaveReceived('getName')->once();
    $category2->shouldHaveReceived('getDescription')->once();
    $category2->shouldHaveReceived('isActive')->once();
    $category2->shouldHaveReceived('getCreatedAt')->once();

    $this->assertInstanceOf(CategoryOutputDto::class, $result->items[0]);
    $this->assertInstanceOf(CategoryOutputDto::class, $result->items[1]);
  }

  public function testShouldReturnEmptyItemsArrayWhenPaginationReturnsEmpty()
  {
    // Arrange
    $mockPagination = $this->mockPagination();

    $this->repository->shouldReceive('paginate')
      ->andReturn($mockPagination);

    $input = new CategoryPaginateInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertIsArray($result->items);
    $this->assertEmpty($result->items);
    $this->repository->shouldHaveReceived('paginate')->once();
  }

  public function testShouldHandleNullFilterAndOrder()
  {
    // Arrange
    $mockPagination = $this->mockPagination();

    $this->repository->shouldReceive('paginate')
      ->with('', 'DESC', 1, 15)
      ->andReturn($mockPagination);

    // Input com valores null para simular ausência de parâmetros
    $input = new CategoryPaginateInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertTrue(true);
    $this->repository->shouldHaveReceived('paginate')
      ->with('', 'DESC', 1, 15)
      ->once();
  }

  public function testShouldReturnCorrectPaginationMetadata()
  {
    // Arrange
    $mockPagination = $this->mockPagination(
      total: 50,
      currentPage: 2,
      lastPage: 5,
      firstPage: 1,
      perPage: 10,
      to: 20,
      from: 11
    );

    $this->repository->shouldReceive('paginate')
      ->andReturn($mockPagination);

    $input = new CategoryPaginateInputDto();

    // Act
    $result = $this->useCase->execute($input);

    // Assert
    $this->assertEquals(50, $result->total);
    $this->assertEquals(2, $result->current_page);
    $this->assertEquals(5, $result->last_page);
    $this->assertEquals(1, $result->first_page);
    $this->assertEquals(10, $result->per_page);
    $this->assertEquals(20, $result->to);
    $this->assertEquals(11, $result->from);
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
  ): PaginationInterface|MockInterface {
    $mockPagination = Mockery::spy(PaginationInterface::class);
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