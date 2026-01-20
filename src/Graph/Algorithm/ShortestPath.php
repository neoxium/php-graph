<?php declare(strict_types=1);

namespace Neoxium\Graph\Algorithm;

use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;

class ShortestPath
{
    /**
     * Dijkstra algorithm
     * @return array{path: Node[], distance: float}|null
     */
    public static function dijkstra(Graph $graph, Node $start, Node $end): ?array
    {
        $distances = [];
        $previous = [];
        $unvisited = [];

        foreach ($graph->getNodes() as $node) {
            $distances[$node->id] = PHP_FLOAT_MAX;
            $unvisited[$node->id] = true;
        }

        $distances[$start->id] = 0.0;

        while (!empty($unvisited)) {
            $current = self::getMinDistanceNode($distances, $unvisited);
            if ($current === null) {
                break;
            }

            unset($unvisited[$current->id]);

            if ($current->equals($end)) {
                return [
                    'path' => self::reconstructDijkstraPath($previous, $end),
                    'distance' => $distances[$end->id]
                ];
            }

            foreach ($graph->getEdges($current) as $edge) {
                $neighbor = $edge->to;
                if (!isset($unvisited[$neighbor->id])) {
                    continue;
                }

                $alt = $distances[$current->id] + $edge->weight;
                if ($alt < $distances[$neighbor->id]) {
                    $distances[$neighbor->id] = $alt;
                    $previous[$neighbor->id] = $current;
                }
            }
        }

        return null;
    }

    private static function getMinDistanceNode(array $distances, array $unvisited): ?Node
    {
        $minDist = PHP_FLOAT_MAX;
        $minNode = null;

        foreach ($unvisited as $id => $_) {
            if ($distances[$id] < $minDist) {
                $minDist = $distances[$id];
                $minNode = $id;
            }
        }

        return $minNode !== null ? new Node($minNode) : null;
    }

    /** @return Node[] */
    private static function reconstructDijkstraPath(array $previous, Node $end): array
    {
        $path = [$end];
        $currentId = $end->id;

        while (isset($previous[$currentId])) {
            $current = $previous[$currentId];
            array_unshift($path, $current);
            $currentId = $current->id;
        }

        return $path;
    }
}