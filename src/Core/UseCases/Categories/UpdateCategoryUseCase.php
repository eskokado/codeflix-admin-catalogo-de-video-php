<?php

namespace Core\UseCases\Categories;

use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Mappers\Categories\CategoryInputMapper;
use Core\UseCases\Mappers\Categories\CategoryOutputMapper;
use Core\UseCases\DTOs\Categories\CategoryUpdateInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use Core\Domain\Exceptions\NotFoundException;

class UpdateCategoryUseCase
{
  protected $repository;

  public function __construct(CategoryRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  public function execute(CategoryUpdateInputDto $input): CategoryOutputDto
  {
    // Busca a entidade no repositório
    $category = $this->repository->findById($input->id);

    // Verifica se a categoria existe
    if ($category === null) {
      throw new NotFoundException("Category not found");
    }

    // Atualiza a entidade usando o mapper
    $category = CategoryInputMapper::fromUpdateDto($input, $category);

    // Persiste a entidade atualizada
    $updatedCategory = $this->repository->update($category);

    // Converte a entidade para DTO de saída
    return CategoryOutputMapper::toDto($updatedCategory);
  }
}