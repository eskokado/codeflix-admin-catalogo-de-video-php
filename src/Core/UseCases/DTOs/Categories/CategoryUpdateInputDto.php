<?php

namespace Core\UseCases\DTOs\Categories;

class CategoryUpdateInputDto
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description = '',
    public bool $isActive = true,
  ) {

  }
}