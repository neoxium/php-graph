<?php declare(strict_types=1);

namespace Neoxium\Graph;

final readonly class Edge
{
    public function __construct(
        public Node $from,
        public Node $to,
        public float $weight = 1.0,
        public bool $directed = false
    ) {}
}
