<?php

namespace Core\UseCases\Mappers\Categories;

use Core\Domain\Entities\Category;
use Core\UseCases\DTOs\Categories\CategoryOutputDto;

class CategoryOutputMapper
{
  /**
   * Converte uma entidade Category para um DTO de saída genérico
   */
  public static function toDto(Category $category): CategoryOutputDto
  {
    return new CategoryOutputDto(
      id: $category->getId(),
      name: $category->getName(),
      description: $category->getDescription(),
      is_active: $category->isActive(),
      created_at: $category->getCreatedAt()
    );
  }

  /**
   * Converte uma entidade Category para um DTO específico de saída de criação
   */
  public static function toCreateDto(Category $category): CategoryOutputDto
  {
    return new CategoryOutputDto(
      id: $category->getId(),
      name: $category->getName(),
      description: $category->getDescription(),
      is_active: $category->isActive()
    );
  }

  /**
   * Converte uma lista de categorias para uma lista de DTOs
   * 
   * @param Category[] $categories
   * @return CategoryOutputDto[]
   */
  public static function toDtoCollection(array $categories): array
  {
    return array_map(
      fn(Category $category) => self::toDto($category),
      $categories
    );
  }
}