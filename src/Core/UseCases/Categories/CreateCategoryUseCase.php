<?php

namespace Core\UseCases\Categories;

use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\Mappers\Categories\CategoryInputMapper;
use Core\UseCases\Mappers\Categories\CategoryOutputMapper;
use Core\UseCases\DTOs\Categories\CategoryCreateInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;

class CreateCategoryUseCase
{
  protected $repository;

  public function __construct(CategoryRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  public function execute(CategoryCreateInputDto $input): CategoryOutputDto
  {
    // Converte DTO de entrada para entidade utilizando o mapper
    $category = CategoryInputMapper::fromCreateDto($input);

    // Persiste a entidade
    $newCategory = $this->repository->insert($category);

    // Converte a entidade para DTO de saída utilizando o mapper
    return CategoryOutputMapper::toCreateDto($newCategory);
  }
}