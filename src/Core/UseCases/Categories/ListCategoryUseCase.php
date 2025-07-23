<?php

namespace Core\UseCases\Categories;

use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\UseCases\DTOs\Categories\CategoryListInputDto;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;
use Core\UseCases\Mappers\Categories\CategoryOutputMapper;

class ListCategoryUseCase
{
  protected $repository;

  public function __construct(CategoryRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  public function execute(CategoryListInputDto $input): array
  {
    $categories = $this->repository->findAll(
      filter: $input->filter ?? '',
      order: $input->order ?? 'DESC'
    );

    return array_map(
      fn($category) => CategoryOutputMapper::toDto($category),
      $categories
    );
  }
}