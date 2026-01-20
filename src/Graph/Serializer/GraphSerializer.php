<?php declare(strict_types=1);

namespace Neoxium\Graph\Serializer;

use Neoxium\Graph\Edge;
use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;
use InvalidArgumentException;
use JsonException;

class GraphSerializer
{
    public static function toArray(Graph $graph): array
    {
        $nodes = [];
        $edges = [];

        foreach ($graph->getNodes() as $node) {
            $nodes[] = [
                'id' => $node->id,
                'data' => $node->data
            ];

            foreach ($graph->getEdges($node) as $edge) {
                $edges[] = [
                    'from' => $edge->from->id,
                    'to' => $edge->to->id,
                    'weight' => $edge->weight,
                    'directed' => $edge->directed
                ];
            }
        }

        if (!$graph->isDirected()) {
            $edges = self::deduplicateUndirectedEdges($edges);
        }

        return [
            'directed' => $graph->isDirected(),
            'nodes' => $nodes,
            'edges' => $edges
        ];
    }

    /**
     * @throws JsonException
     */
    public static function toJson(Graph $graph, int $flags = JSON_PRETTY_PRINT): string
    {
        return json_encode(self::toArray($graph), JSON_THROW_ON_ERROR | $flags);
    }

    public static function fromArray(array $data): Graph
    {
        $graph = new Graph($data['directed'] ?? false);

        foreach ($data['nodes'] ?? [] as $nodeData) {
            $graph->addNode(new Node($nodeData['id'], $nodeData['data'] ?? null));
        }

        foreach ($data['edges'] ?? [] as $edgeData) {
            $from = $graph->getNode($edgeData['from']);
            $to = $graph->getNode($edgeData['to']);

            if ($from && $to) {
                $graph->addEdge(new Edge(
                    $from,
                    $to,
                    $edgeData['weight'] ?? 1.0,
                    $edgeData['directed'] ?? false
                ));
            }
        }

        return $graph;
    }

    /**
     * @throws JsonException
     */
    public static function fromJson(string $json): Graph
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid JSON format');
        }
        return self::fromArray($data);
    }

    private static function deduplicateUndirectedEdges(array $edges): array
    {
        $seen = [];
        $result = [];

        foreach ($edges as $edge) {
            $key = self::getEdgeKey($edge['from'], $edge['to']);
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $edge;
            }
        }

        return $result;
    }

    private static function getEdgeKey(string|int $from, string|int $to): string
    {
        $ids = [$from, $to];
        sort($ids);
        return implode('-', $ids);
    }
}