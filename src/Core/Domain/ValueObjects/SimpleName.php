<?php

namespace Core\Domain\ValueObjects;

use Core\Domain\Exceptions\EntityValidationException;

class SimpleName extends SimpleText
{
  public function __construct(string $value)
  {
    parent::__construct($value);
    $this->validateSimpleName();
  }

  private function validateSimpleName(): void
  {
    $errors = [];

    // Validar caracteres permitidos - letras, números, ponto, hífen, underscore
    if (!preg_match('/^[a-zA-Z0-9\._\-\s]+$/', $this->value)) {
      $errors[] = self::formatError("contains invalid characters");
    }

    // Verificar se tem pelo menos duas partes (nome e sufixo/tipo)
    $parts = explode(' ', $this->value);
    if (count($parts) < 2) {
      $errors[] = self::formatError("must have at least a name and type/suffix");
    }

    if (count($errors) > 0) {
      throw new EntityValidationException(implode("\n", $errors));
    }
  }

  public static function create(
    string $value,
    int $minLength = 3,
    int $maxLength = 100,
    ?string $attribute = null,
    ?string $object = null
  ): self {
    // Usa o método create da classe pai para validações básicas
    parent::create($value, $minLength, $maxLength, $attribute, $object);
    return new static($value);
  }

  public function getBaseName(): string
  {
    return explode(' ', $this->value)[0];
  }

  public function getType(): string
  {
    $parts = explode(' ', $this->value);
    return end($parts);
  }

  public function getQualifiers(): array
  {
    $parts = explode(' ', $this->value);
    return array_slice($parts, 1, -1);
  }

  public function getInitials(): string
  {
    $baseName = $this->getBaseName();
    $type = $this->getType();

    return mb_substr($baseName, 0, 1) . mb_substr($type, 0, 1);
  }
}