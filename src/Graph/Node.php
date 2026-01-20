<?php declare(strict_types=1);

namespace Neoxium\Graph;

final readonly class Node
{
    public function __construct(
        public string|int $id,
        public mixed $data = null
    ) {}

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }
}