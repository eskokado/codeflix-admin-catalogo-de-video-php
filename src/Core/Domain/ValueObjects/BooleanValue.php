<?php

namespace Core\Domain\ValueObjects;

class BooleanValue
{
  public function __construct(
    protected bool $value
  ) {
  }

  public static function true(): self
  {
    return new self(true);
  }

  public static function false(): self
  {
    return new self(false);
  }

  public function isTrue(): bool
  {
    return $this->value === true;
  }

  public function isFalse(): bool
  {
    return $this->value === false;
  }

  public function toggle(): self
  {
    return new self(!$this->value);
  }

  public function __toString(): string
  {
    return $this->value ? 'true' : 'false';
  }
}