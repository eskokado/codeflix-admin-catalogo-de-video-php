<?php

namespace Core\UseCases\Categories;

use Core\Domain\Repositories\CategoryRepositoryInterface;
use Core\Domain\Exceptions\NotFoundException;

class DeleteCategoryUseCase
{
  protected $repository;

  public function __construct(CategoryRepositoryInterface $repository)
  {
    $this->repository = $repository;
  }

  public function execute(string $id): bool
  {
    // Busca a entidade no repositório para garantir que existe
    $category = $this->repository->findById($id);

    // Verifica se a categoria existe
    if ($category === null) {
      throw new NotFoundException("Category not found");
    }

    // Executa a deleção e retorna o resultado (sucesso/falha)
    return $this->repository->delete($id);
  }
}