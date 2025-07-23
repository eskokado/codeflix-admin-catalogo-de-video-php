<?php

namespace Tests\Unit\UseCases\Categories;

use Core\Domain\Entities\Category;
use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Categories\CreateCategoryUseCase;
use Core\UseCases\Mappers\Categories\CategoryInputMapper;
use Core\UseCases\Mappers\Categories\CategoryOutputMapper;
use Core\UseCases\DTOs\Categories\CategoryCreateInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CreateCategoryUseCaseTest extends TestCase
{
  /**
   * @dataProvider provideValidCategoryData
   */
  public function testCreateNewCategory(string $name, string $description, bool $isActive)
  {
    // Arrange
    $uuid = Uuid::uuid4()->toString();

    // Mock da entidade que será retornada pelo repositório
    $categoryMock = Mockery::mock(Category::class);
    $categoryMock->shouldReceive('getId')->andReturn($uuid);
    $categoryMock->shouldReceive('getName')->andReturn($name);
    $categoryMock->shouldReceive('getDescription')->andReturn($description);
    $categoryMock->shouldReceive('isActive')->andReturn($isActive);

    // Mock do repositório
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);
    $repositoryMock->shouldReceive('insert')
      ->once()
      ->andReturn($categoryMock);

    // Instanciação do caso de uso com o repositório mockado
    $useCase = new CreateCategoryUseCase($repositoryMock);

    // Criação do DTO de entrada
    $input = new CategoryCreateInputDto(
      name: $name,
      description: $description,
      isActive: $isActive
    );

    // Act
    $output = $useCase->execute($input);

    // Assert
    $this->assertInstanceOf(CategoryOutputDto::class, $output);
    $this->assertEquals($uuid, $output->id);
    $this->assertEquals($name, $output->name);
    $this->assertEquals($description, $output->description);
    $this->assertEquals($isActive, $output->is_active);

    Mockery::close();
  }

  /**
   * Teste usando spy para verificar a interação com o repositório
   */
  public function testCreateNewCategoryWithSpy()
  {
    // Arrange
    $name = 'Movies Action';
    $description = 'Action movies description';
    $isActive = true;

    $uuid = Uuid::uuid4()->toString();

    // Spy do repositório
    $repositorySpy = Mockery::spy(CategoryRepositoryInterface::class);

    // Prepare a mock Category to be returned by the spy
    $categoryMock = Mockery::mock(Category::class);
    $categoryMock->shouldReceive('getId')->andReturn($uuid);
    $categoryMock->shouldReceive('getName')->andReturn($name);
    $categoryMock->shouldReceive('getDescription')->andReturn($description);
    $categoryMock->shouldReceive('isActive')->andReturn($isActive);

    // Configure o spy para retornar a categoria mockada
    $repositorySpy->shouldReceive('insert')->andReturn($categoryMock);

    // Instanciação do caso de uso com o repositório spy
    $useCase = new CreateCategoryUseCase($repositorySpy);

    // Criação do DTO de entrada
    $input = new CategoryCreateInputDto(
      name: $name,
      description: $description,
      isActive: $isActive
    );

    // Act
    $output = $useCase->execute($input);

    // Verifica se insert foi chamado com um objeto Category
    $repositorySpy->shouldHaveReceived('insert', [Mockery::type(Category::class)]);

    // Verificar se o DTO de saída tem os valores esperados
    $this->assertEquals($uuid, $output->id);
    $this->assertEquals($name, $output->name);
    $this->assertEquals($description, $output->description);
    $this->assertEquals($isActive, $output->is_active);
  }

  public function testShouldThrowExceptionWhenCategoryNameIsInvalid()
  {
    $this->expectException(\Core\Domain\Exceptions\EntityValidationException::class);

    // Mock do repositório
    $repositoryMock = Mockery::mock(CategoryRepositoryInterface::class);

    // Instanciação do caso de uso com o repositório mockado
    $useCase = new CreateCategoryUseCase($repositoryMock);

    // Criação do DTO de entrada com nome inválido (falta o tipo/sufixo)
    $input = new CategoryCreateInputDto(
      name: 'Invalid', // Não tem duas partes (nome e tipo) conforme regra em SimpleName
      description: 'Some description',
      isActive: true
    );

    // Act - deve lançar exceção
    $useCase->execute($input);

    Mockery::close();
  }

  public function provideValidCategoryData()
  {
    return [
      'complete data' => ['Movies Action', 'Action movies description', true],
      'inactive category' => ['Series Drama', 'Drama series description', false],
      'minimal description' => ['Books Fiction', '', true],
    ];
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}