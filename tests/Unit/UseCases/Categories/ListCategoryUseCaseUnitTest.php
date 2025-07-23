<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entities\Category;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Categories\ListCategoryUseCase;
use Core\UseCases\DTOs\Categories\CategoryListInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use PHPUnit\Framework\TestCase;
use Mockery;

class ListCategoryUseCaseTest extends TestCase
{
  public function testListCategoriesEmpty()
  {
    $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
    $categoryRepository->shouldReceive('findAll')
      ->with('', 'DESC')
      ->andReturn([]);

    $useCase = new ListCategoryUseCase($categoryRepository);
    $input = new CategoryListInputDto();
    $result = $useCase->execute($input);

    // $this->assertIsArray($result);
    $this->assertCount(0, $result);
  }

  public function testListCategories()
  {
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

    $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
    $categoryRepository->shouldReceive('findAll')
      ->with('', 'DESC')
      ->andReturn([$category1, $category2]);

    $useCase = new ListCategoryUseCase($categoryRepository);
    $input = new CategoryListInputDto();
    $result = $useCase->execute($input);

    $this->assertIsArray($result);
    $this->assertCount(2, $result);
    $this->assertInstanceOf(CategoryOutputDto::class, $result[0]);
    $this->assertInstanceOf(CategoryOutputDto::class, $result[1]);
    $this->assertEquals('uuid-1', $result[0]->id);
    $this->assertEquals('Category 1', $result[0]->name);
    $this->assertEquals('uuid-2', $result[1]->id);
    $this->assertEquals('Category 2', $result[1]->name);
  }

  public function testListCategoriesWithFilter()
  {
    $category = Mockery::mock(Category::class, [
      'getId' => 'uuid-1',
      'getName' => 'Category 1',
      'getDescription' => 'Description 1',
      'isActive' => true,
      'getCreatedAt' => '2023-01-01T00:00:00'
    ]);

    $categoryRepository = Mockery::mock(CategoryRepositoryInterface::class);
    $categoryRepository->shouldReceive('findAll')
      ->with('test', 'ASC')
      ->andReturn([$category]);

    $useCase = new ListCategoryUseCase($categoryRepository);
    $input = new CategoryListInputDto(
      filter: 'test',
      order: 'ASC'
    );
    $result = $useCase->execute($input);

    $this->assertIsArray($result);
    $this->assertCount(1, $result);
    $this->assertInstanceOf(CategoryOutputDto::class, $result[0]);
    $this->assertEquals('uuid-1', $result[0]->id);
    $this->assertEquals('Category 1', $result[0]->name);
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}