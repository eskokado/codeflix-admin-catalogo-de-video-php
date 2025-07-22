<?php

namespace Core\Domain\Validations;

use Core\Domain\Exceptions\EntityValidationException;

class DomainValidation
{
  public static function notNull(string $value, string $exceptMessage = null)
  {
    if (empty($value)) {
      throw new EntityValidationException($exceptMessage ?? "Should not empty or null");
    }
  }

  public static function strMaxLength(string $value, int $length = 255, string $exceptMessage = null)
  {
    if (empty($value) || strlen($value) > $length) {
      throw new EntityValidationException($exceptMessage ?? "The value must not be greater than {$length} characters");
    }
  }

  public static function strMinLength(string $value, int $length = 5, string $exceptMessage = null)
  {
    if (empty($value) || strlen($value) < $length) {
      throw new EntityValidationException($exceptMessage ?? "The value must be at least {$length} characters");
    }
  }

  public static function strCanNullAndMaxLength(string $value, int $length = 255, string $exceptMessage = null)
  {
    if (!empty($value) && strlen($value) > $length) {
      throw new EntityValidationException($exceptMessage ?? "The value must not be greater than {$length} characters");
    }
  }
}
