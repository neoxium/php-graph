<?php declare(strict_types=1);

namespace Neoxium\GraphTests\Algorithms;

use Neoxium\Graph\Algorithm\ShortestPath;
use Neoxium\Graph\Edge;
use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;
use PHPUnit\Framework\TestCase;

class ShortestPathTest extends TestCase
{
    public function testDijkstraSimplePath(): void
    {
        $graph = new Graph(directed: true);
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph
            ->addEdge(new Edge($nodeA, $nodeB, 1.0, true))
            ->addEdge(new Edge($nodeB, $nodeC, 2.0, true))
            ->addEdge(new Edge($nodeA, $nodeC, 5.0, true));

        $result = ShortestPath::dijkstra($graph, $nodeA, $nodeC);

        $this->assertNotNull($result);
        $this->assertEquals(3.0, $result['distance']);
        $this->assertCount(3, $result['path']);
    }

    public function testDijkstraNoPath(): void
    {
        $graph = new Graph(directed: true);
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $graph->addNode($nodeA)->addNode($nodeB);

        $result = ShortestPath::dijkstra($graph, $nodeA, $nodeB);

        $this->assertNull($result);
    }

    public function testDijkstraWithWeights(): void
    {
        $graph = new Graph(directed: true);
        $nodes = [
            'A' => new Node('A'),
            'B' => new Node('B'),
            'C' => new Node('C'),
            'D' => new Node('D'),
        ];

        $graph
            ->addEdge(new Edge($nodes['A'], $nodes['B'], 4.0, true))
            ->addEdge(new Edge($nodes['A'], $nodes['C'], 2.0, true))
            ->addEdge(new Edge($nodes['C'], $nodes['B'], 1.0, true))
            ->addEdge(new Edge($nodes['B'], $nodes['D'], 5.0, true))
            ->addEdge(new Edge($nodes['C'], $nodes['D'], 8.0, true));

        $result = ShortestPath::dijkstra($graph, $nodes['A'], $nodes['D']);

        $this->assertNotNull($result);
        $this->assertEquals(8.0, $result['distance']);
    }
}
