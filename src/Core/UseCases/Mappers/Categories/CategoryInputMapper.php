<?php

namespace Core\UseCases\Mappers\Categories;

use Core\Domain\Entities\Category;
use Core\Domain\ValueObjects\BooleanValue;
use Core\Domain\ValueObjects\SimpleName;
use Core\Domain\ValueObjects\SimpleText;
use Core\UseCases\DTOs\Categories\CategoryCreateInputDto;
use Core\UseCases\DTOs\Categories\CategoryUpdateInputDto;

class CategoryInputMapper
{
  /**
   * Converte um DTO de criação para uma entidade Category
   */
  public static function fromCreateDto(CategoryCreateInputDto $input): Category
  {
    return new Category(
      name: SimpleName::create($input->name),
      description: SimpleText::create($input->description, 0, 255),
      isActive: new BooleanValue($input->isActive)
    );
  }

  /**
   * Converte um DTO de atualização para uma entidade Category
   * Observação: Este método seria implementado quando houver um DTO de atualização
   */
  public static function fromUpdateDto(CategoryUpdateInputDto $input, Category $existingCategory): Category
  {
    $existingCategory->update(
      name: SimpleName::create($input->name),
      description: SimpleText::create($input->description, 0, 255)
    );

    if ($input->isActive && !$existingCategory->isActive()) {
      $existingCategory->activate();
    } elseif (!$input->isActive && $existingCategory->isActive()) {
      $existingCategory->deactivate();
    }

    return $existingCategory;
  }
}