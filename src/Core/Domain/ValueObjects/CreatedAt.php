<?php

declare(strict_types=1);

namespace Core\Domain\ValueObjects;

/**
 * Value Object para representar a data de criação
 */
final class CreatedAt
{
  private \DateTimeImmutable $valueCreated;

  /**
   * Construtor privado para forçar o uso do método create
   * 
   * @param \DateTimeImmutable $createdDate
   */
  private function __construct(\DateTimeImmutable $createdDate)
  {
    $this->valueCreated = $createdDate;
  }

  /**
   * Método estático para criar instâncias da classe
   * 
   * @param string|\DateTimeInterface $createdDate
   * @return self
   */
  public static function create(string|\DateTimeInterface $createdDate): self
  {
    if (is_string($createdDate)) {
      $createdDate = new \DateTimeImmutable($createdDate);
    } elseif ($createdDate instanceof \DateTime) {
      $createdDate = \DateTimeImmutable::createFromMutable($createdDate);
    }

    return new self($createdDate);
  }

  /**
   * Retorna o valor da data de criação
   * 
   * @return \DateTimeImmutable
   */
  public function getValueCreatedAt(): \DateTimeImmutable
  {
    return $this->valueCreated;
  }

  /**
   * Impede a serialização direta
   * 
   * @throws \LogicException
   */
  public function __serialize(): array
  {
    return [
      'valueCreated' => $this->valueCreated->format(\DateTimeInterface::ISO8601),
    ];
  }

  /**
   * Restaura a partir de dados serializados
   * 
   * @param array $data
   */
  public function __unserialize(array $data): void
  {
    $this->valueCreated = new \DateTimeImmutable($data['valueCreated']);
  }

  /**
   * Representação string do objeto
   * 
   * @return string
   */
  public function __toString(): string
  {
    return $this->valueCreated->format(\DateTimeInterface::ISO8601);
  }
}