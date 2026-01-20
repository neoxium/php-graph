<?php declare(strict_types=1);

namespace Neoxium\Graph;

class Graph
{
    /** @var array<string|int, Node> */
    private array $nodes = [];

    /** @var array<string|int, array<Edge>> */
    private array $adjacencyList = [];

    private bool $directed;

    public function __construct(bool $directed = false)
    {
        $this->directed = $directed;
    }

    public function addNode(Node $node): self
    {
        if (!isset($this->nodes[$node->id])) {
            $this->nodes[$node->id] = $node;
            $this->adjacencyList[$node->id] = [];
        }
        return $this;
    }

    public function addEdge(Edge $edge): self
    {
        $this->addNode($edge->from);
        $this->addNode($edge->to);

        $this->adjacencyList[$edge->from->id][] = $edge;

        if (!$edge->directed && !$this->directed) {
            $reverseEdge = new Edge($edge->to, $edge->from, $edge->weight, false);
            $this->adjacencyList[$edge->to->id][] = $reverseEdge;
        }

        return $this;
    }

    public function getNode(string|int $id): ?Node
    {
        return $this->nodes[$id] ?? null;
    }

    /** @return Node[] */
    public function getNodes(): array
    {
        return array_values($this->nodes);
    }

    /** @return Edge[] */
    public function getEdges(Node $node): array
    {
        return $this->adjacencyList[$node->id] ?? [];
    }

    /** @return Node[] */
    public function getNeighbors(Node $node): array
    {
        return array_map(
            static fn(Edge $edge) => $edge->to,
            $this->getEdges($node)
        );
    }

    public function hasNode(string|int $id): bool
    {
        return isset($this->nodes[$id]);
    }

    public function removeNode(string|int $id): self
    {
        if (!$this->hasNode($id)) {
            return $this;
        }

        unset($this->nodes[$id], $this->adjacencyList[$id]);

        foreach ($this->adjacencyList as $nodeId => $edges) {
            $this->adjacencyList[$nodeId] = array_filter(
                $edges,
                static fn(Edge $edge) => $edge->to->id !== $id
            );
        }

        return $this;
    }

    public function nodeCount(): int
    {
        return count($this->nodes);
    }

    public function edgeCount(): int
    {
        $count = 0;
        foreach ($this->adjacencyList as $edges) {
            $count += count($edges);
        }
        return $this->directed ? $count : $count / 2;
    }

    public function isDirected(): bool
    {
        return $this->directed;
    }
}