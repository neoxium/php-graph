<?php declare(strict_types=1);

namespace Neoxium\Graph\Algorithm;

use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;

class GraphTraversal
{
    /**
     * Depth-first search (DFS)
     * @return Node[]
     */
    public static function depthFirstSearch(Graph $graph, Node $start): array
    {
        $visited = [];
        $result = [];
        $stack = [$start];

        while (!empty($stack)) {
            $current = array_pop($stack);
            $id = $current->id;

            if (isset($visited[$id])) {
                continue;
            }

            $visited[$id] = true;
            $result[] = $current;

            foreach (array_reverse($graph->getNeighbors($current)) as $neighbor) {
                if (!isset($visited[$neighbor->id])) {
                    $stack[] = $neighbor;
                }
            }
        }

        return $result;
    }

    /**
     * Breadth-first search (BFS)
     * @return Node[]
     */
    public static function breadthFirstSearch(Graph $graph, Node $start): array
    {
        $visited = [];
        $result = [];
        $queue = [$start];
        $visited[$start->id] = true;

        while (!empty($queue)) {
            $current = array_shift($queue);
            $result[] = $current;

            foreach ($graph->getNeighbors($current) as $neighbor) {
                if (!isset($visited[$neighbor->id])) {
                    $visited[$neighbor->id] = true;
                    $queue[] = $neighbor;
                }
            }
        }

        return $result;
    }

    /**
     * Pathfinding (BFS)
     * @return Node[]|null
     */
    public static function findPath(Graph $graph, Node $start, Node $end): ?array
    {
        if ($start->equals($end)) {
            return [$start];
        }

        $visited = [];
        $parent = [];
        $queue = [$start];
        $visited[$start->id] = true;

        while (!empty($queue)) {
            $current = array_shift($queue);

            foreach ($graph->getNeighbors($current) as $neighbor) {
                if (!isset($visited[$neighbor->id])) {
                    $visited[$neighbor->id] = true;
                    $parent[$neighbor->id] = $current;
                    $queue[] = $neighbor;

                    if ($neighbor->equals($end)) {
                        return self::reconstructPath($parent, $start, $end);
                    }
                }
            }
        }

        return null;
    }

    /** @return Node[] */
    private static function reconstructPath(array $parent, Node $start, Node $end): array
    {
        $path = [$end];
        $current = $end;

        while (!$current->equals($start)) {
            $current = $parent[$current->id];
            array_unshift($path, $current);
        }

        return $path;
    }

    /**
     * Cycle detection (DFS)
     */
    public static function hasCycle(Graph $graph): bool
    {
        $visited = [];
        $recStack = [];

        $test = static function(Node $node) use ($graph, &$visited, &$recStack) {
            return !isset($visited[$node->id])
                && self::hasCycleDFS($graph, $node, $visited, $recStack, null);
        };

        return array_any($graph->getNodes(), static fn($node) => $test($node));
    }

    private static function hasCycleDFS(
        Graph $graph,
        Node $node,
        array &$visited,
        array &$recStack,
        ?Node $parent
    ): bool {
        $visited[$node->id] = true;
        $recStack[$node->id] = true;

        foreach ($graph->getNeighbors($node) as $neighbor) {
            if (!isset($visited[$neighbor->id])) {
                if (self::hasCycleDFS($graph, $neighbor, $visited, $recStack, $node)) {
                    return true;
                }
            } elseif (isset($recStack[$neighbor->id])) {
                if ($parent === null || $graph->isDirected() || !$neighbor->equals($parent)) {
                    return true;
                }
            }
        }

        unset($recStack[$node->id]);
        return false;
    }
}