<?php

namespace Core\UseCases\DTOs\Categories;

class CategoryListInputDto
{
  public function __construct(
    public string $filter = '',
    public string $order = 'DESC'
  ) {
  }
}