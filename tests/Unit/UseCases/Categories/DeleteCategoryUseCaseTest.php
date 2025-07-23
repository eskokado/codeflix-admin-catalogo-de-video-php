<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entities\Category;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Categories\DeleteCategoryUseCase;
use Core\Domain\Exceptions\NotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DeleteCategoryUseCaseTest extends TestCase
{
  /**
   * Testa o caso de sucesso na deleção de uma categoria
   */
  public function testDeleteCategory()
  {
    // Arrange
    $categoryId = Uuid::uuid4()->toString();

    // Mock da categoria que será encontrada
    $categoryMock = Mockery::mock(Category::class);
    $categoryMock->shouldReceive('getId')->andReturn($categoryId);

    // Mock do repositório
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('findById')
      ->once()
      ->with($categoryId)
      ->andReturn($categoryMock);

    $repositoryMock->shouldReceive('delete')
      ->once()
      ->with($categoryId)
      ->andReturn(true);

    // Instanciação do caso de uso
    $useCase = new DeleteCategoryUseCase($repositoryMock);

    // Act
    $result = $useCase->execute($categoryId);

    // Assert
    $this->assertTrue($result);

    Mockery::close();
  }

  /**
   * Testa o caso de falha na deleção (repositório retorna false)
   */
  public function testFailToDeleteCategory()
  {
    // Arrange
    $categoryId = Uuid::uuid4()->toString();

    // Mock da categoria que será encontrada
    $categoryMock = Mockery::mock(Category::class);
    $categoryMock->shouldReceive('getId')->andReturn($categoryId);

    // Mock do repositório
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('findById')
      ->once()
      ->with($categoryId)
      ->andReturn($categoryMock);

    $repositoryMock->shouldReceive('delete')
      ->once()
      ->with($categoryId)
      ->andReturn(false);

    // Instanciação do caso de uso
    $useCase = new DeleteCategoryUseCase($repositoryMock);

    // Act
    $result = $useCase->execute($categoryId);

    // Assert
    $this->assertFalse($result);

    Mockery::close();
  }

  /**
   * Testa o caso de categoria não encontrada
   */
  public function testShouldThrowExceptionWhenCategoryNotFound()
  {
    $this->expectException(NotFoundException::class);
    $this->expectExceptionMessage("Category not found");

    // Arrange
    $categoryId = Uuid::uuid4()->toString();

    // Mock do repositório que retornará null (categoria não encontrada)
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('findById')
      ->once()
      ->with($categoryId)
      ->andReturn(null);

    // Instanciação do caso de uso
    $useCase = new DeleteCategoryUseCase($repositoryMock);

    // Act - deve lançar exceção
    $useCase->execute($categoryId);

    Mockery::close();
  }

  /**
   * Teste usando spy para verificar a interação com o repositório
   */
  public function testDeleteCategoryWithSpy()
  {
    // Arrange
    $categoryId = Uuid::uuid4()->toString();

    // Mock da categoria que será encontrada
    $categoryMock = Mockery::mock(Category::class);

    // Spy do repositório
    $repositorySpy = Mockery::spy(CategoryRepositoryInterface::class);

    // Configure o spy para retornar a categoria mockada e true para delete
    $repositorySpy->shouldReceive('findById')->with($categoryId)->andReturn($categoryMock);
    $repositorySpy->shouldReceive('delete')->andReturn(true);

    // Instanciação do caso de uso
    $useCase = new DeleteCategoryUseCase($repositorySpy);

    // Act
    $result = $useCase->execute($categoryId);

    // Assert
    // Verifica se os métodos foram chamados corretamente
    $repositorySpy->shouldHaveReceived('findById')->once()->with($categoryId);
    $repositorySpy->shouldHaveReceived('delete')->once()->with($categoryId);

    // Verifica o resultado
    $this->assertTrue($result);

    Mockery::close();
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}