<?php

namespace Tests\Unit\UseCases\Categories;

use Core\Domain\Entities\Category;
use Core\Domain\Exceptions\EntityValidationException;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Categories\UpdateCategoryUseCase;
use Core\UseCases\DTOs\Categories\CategoryUpdateInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use Core\Domain\Exceptions\NotFoundException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UpdateCategoryUseCaseTest extends TestCase
{
  /**
   * @dataProvider provideValidUpdateData
   */
  public function testUpdateCategory(string $id, string $name, string $description, bool $isActive)
  {
    // Arrange
    $existingCategory = Mockery::mock(Category::class);
    $existingCategory->shouldReceive('getId')->andReturn($id);
    $existingCategory->shouldReceive('update')->once()->with(Mockery::any(), Mockery::any())->andReturnSelf();

    // Configuração condicional dos métodos de ativação
    $existingCategory->shouldReceive('isActive')->andReturn(!$isActive); // Estado inicial oposto
    
    if ($isActive) {
      $existingCategory->shouldReceive('activate')->once()->andReturnSelf();
    } else {
      $existingCategory->shouldReceive('deactivate')->once()->andReturnSelf();
    }

    $updatedCategory = Mockery::mock(Category::class);
    $updatedCategory->shouldReceive('getId')->andReturn($id);
    $updatedCategory->shouldReceive('getName')->andReturn($name);
    $updatedCategory->shouldReceive('getDescription')->andReturn($description);
    $updatedCategory->shouldReceive('isActive')->andReturn($isActive);
    $updatedCategory->shouldReceive('getCreatedAt')->andReturn('2025-01-01T12:00:00+00:00');

    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('findById')
      ->once()
      ->with($id)
      ->andReturn($existingCategory);

    $repositoryMock->shouldReceive('update')
      ->once()
      ->with($existingCategory)
      ->andReturn($updatedCategory);

    $useCase = new UpdateCategoryUseCase($repositoryMock);

    $input = new CategoryUpdateInputDto(
      id: $id,
      name: $name,
      description: $description,
      isActive: $isActive
    );

    // Act
    $output = $useCase->execute($input);

    // Assert
    $this->assertInstanceOf(CategoryOutputDto::class, $output);
    $this->assertEquals($id, $output->id);
    $this->assertEquals($name, $output->name);
    $this->assertEquals($description, $output->description);
    $this->assertEquals($isActive, $output->is_active);
    $this->assertEquals('2025-01-01T12:00:00+00:00', $output->created_at);
  }

  /**
   * Test category not found case
   */
  public function testShouldThrowExceptionWhenCategoryNotFound()
  {
    $this->expectException(NotFoundException::class);
    $this->expectExceptionMessage("Category not found");

    // Arrange
    $uuid = Uuid::uuid4()->toString();

    // Mock do repositório que retornará null (categoria não encontrada)
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('findById')
      ->once()
      ->with($uuid)
      ->andReturn(null);

    // Instanciação do caso de uso com o repositório mockado
    $useCase = new UpdateCategoryUseCase($repositoryMock);

    // Criação do DTO de entrada com ID inexistente
    $input = new CategoryUpdateInputDto(
      id: $uuid,
      name: 'Movies Action',
      description: 'Some updated description',
      isActive: true
    );

    // Act - deve lançar exceção
    $useCase->execute($input);
  }


  /**
   * Teste usando spy para verificar a interação com o repositório
   */
  public function testUpdateCategoryWithSpy()
  {
    // Arrange
    $id = Uuid::uuid4()->toString();
    $name = 'Movies Action';
    $description = 'Updated action movies description';
    $isActive = true;

    $existingCategory = Mockery::mock(Category::class);
    $existingCategory->shouldReceive('update')->once()->andReturnSelf();
    $existingCategory->shouldReceive('isActive')->andReturn(false); // Estado inicial diferente!
    
    // Chamada esperada apenas se o estado mudar
    $existingCategory->shouldReceive('activate')->once()->andReturnSelf();

    $updatedCategory = Mockery::mock(Category::class);
    $updatedCategory->shouldReceive('getId')->andReturn($id);
    $updatedCategory->shouldReceive('getName')->andReturn($name);
    $updatedCategory->shouldReceive('getDescription')->andReturn($description);
    $updatedCategory->shouldReceive('isActive')->andReturn($isActive);
    $updatedCategory->shouldReceive('getCreatedAt')->andReturn('2025-01-01T12:00:00+00:00');

    $repositorySpy = Mockery::spy(CategoryRepositoryInterface::class);
    $repositorySpy->shouldReceive('findById')->with($id)->andReturn($existingCategory);
    $repositorySpy->shouldReceive('update')->andReturn($updatedCategory);

    $useCase = new UpdateCategoryUseCase($repositorySpy);

    $input = new CategoryUpdateInputDto(
      id: $id,
      name: $name,
      description: $description,
      isActive: $isActive
    );

    // Act
    $output = $useCase->execute($input);

    // Assert
    // VERIFICAÇÕES DO SPY CORRETAMENTE POSICIONADAS
    $repositorySpy->shouldHaveReceived('findById')->once()->with($id);
    $repositorySpy->shouldHaveReceived('update')->once()->with(Mockery::type(Category::class));
    
    $this->assertEquals($id, $output->id);
    $this->assertEquals($name, $output->name);
    $this->assertEquals($description, $output->description);
    $this->assertEquals($isActive, $output->is_active);
  }

  public function testShouldThrowExceptionWhenCategoryNameIsInvalid()
  {
    $this->expectException(EntityValidationException::class);

    // Arrange
    $id = Uuid::uuid4()->toString();

    // Mock da entidade inicial
    $existingCategory = Mockery::mock(Category::class);

    // Mock do repositório
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('findById')
      ->once()
      ->with($id)
      ->andReturn($existingCategory);

    // Instanciação do caso de uso com o repositório mockado
    $useCase = new UpdateCategoryUseCase($repositoryMock);

    // Criação do DTO de entrada com nome inválido (falta o tipo/sufixo)
    $input = new CategoryUpdateInputDto(
      id: $id,
      name: 'Invalid', // Não tem duas partes (nome e tipo) conforme regra em SimpleName
      description: 'Some description',
      isActive: true
    );

    // Act - deve lançar exceção
    $useCase->execute($input);
  }

  public function provideValidUpdateData()
  {
    $id = Uuid::uuid4()->toString();

    return [
      'update name and description' => [$id, 'Movies Thriller', 'Updated thriller movies description', true],
      'update and deactivate' => [$id, 'Series Documentary', 'Updated documentary series', false],
      'update with minimal description' => [$id, 'Books Fantasy', '', true],
    ];
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}