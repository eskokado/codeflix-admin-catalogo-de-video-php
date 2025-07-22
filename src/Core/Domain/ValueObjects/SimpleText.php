<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Exceptions\EntityValidationException;

class SimpleText
{
  public function __construct(
    protected string $value
  ) {
    $this->value = trim($this->value);
  }

  public function __toString(): string
  {
    return $this->value;
  }

  public static function create(
    string $value,
    int $minLength,
    int $maxLength,
    ?string $attribute = null,
    ?string $object = null
  ): self {
    $value = trim($value);
    $errors = [];

    if (strlen($value) < $minLength) {
      $errors[] = self::formatError(
        "must have at least {$minLength} characters",
        $attribute,
        $object
      );
    }

    if (strlen($value) > $maxLength) {
      $errors[] = self::formatError(
        "must have at most {$maxLength} characters",
        $attribute,
        $object
      );
    }

    if (count($errors) > 0) {
      throw new EntityValidationException(implode("\n", $errors));
    }

    return new static($value);
  }

  protected static function formatError(string $message, ?string $attribute = null, ?string $object = null): string
  {
    $attribute = $attribute ? ucfirst($attribute) : 'Value';
    $objectPrefix = $object ? "{$object} " : '';

    return "{$objectPrefix}{$attribute} {$message}";
  }
}