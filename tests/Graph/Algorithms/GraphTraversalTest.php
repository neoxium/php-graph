<?php declare(strict_types=1);

namespace Neoxium\GraphTests\Algorithms;

use Neoxium\Graph\Algorithm\GraphTraversal;
use Neoxium\Graph\Edge;
use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;
use PHPUnit\Framework\TestCase;

class GraphTraversalTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        $this->graph = $this->createSampleGraph();
    }

    private function createSampleGraph(): Graph
    {
        $graph = new Graph();
        $nodes = [];
        foreach (['A', 'B', 'C', 'D', 'E'] as $id) {
            $nodes[$id] = new Node($id);
        }

        $graph
            ->addEdge(new Edge($nodes['A'], $nodes['B']))
            ->addEdge(new Edge($nodes['A'], $nodes['C']))
            ->addEdge(new Edge($nodes['B'], $nodes['D']))
            ->addEdge(new Edge($nodes['C'], $nodes['E']));

        return $graph;
    }

    public function testDFS(): void
    {
        $start = $this->graph->getNode('A');
        $result = GraphTraversal::depthFirstSearch($this->graph, $start);

        $this->assertCount(5, $result);
        $this->assertEquals('A', $result[0]->id);
    }

    public function testBFS(): void
    {
        $start = $this->graph->getNode('A');
        $result = GraphTraversal::breadthFirstSearch($this->graph, $start);

        $this->assertCount(5, $result);
        $this->assertEquals('A', $result[0]->id);
        $ids = array_map(static fn($n) => $n->id, $result);
        $this->assertContains('B', $ids);
        $this->assertContains('C', $ids);
    }

    public function testFindPath(): void
    {
        $start = $this->graph->getNode('A');
        $end = $this->graph->getNode('D');
        $path = GraphTraversal::findPath($this->graph, $start, $end);

        $this->assertNotNull($path);
        $this->assertEquals('A', $path[0]->id);
        $this->assertEquals('D', $path[count($path) - 1]->id);
    }

    public function testFindPathToSelf(): void
    {
        $start = $this->graph->getNode('A');
        $path = GraphTraversal::findPath($this->graph, $start, $start);

        $this->assertNotNull($path);
        $this->assertCount(1, $path);
        $this->assertEquals('A', $path[0]->id);
    }

    public function testFindPathNoConnection(): void
    {
        $nodeX = new Node('X');
        $nodeY = new Node('Y');
        $this->graph->addNode($nodeX)->addNode($nodeY);

        $start = $this->graph->getNode('A');
        $path = GraphTraversal::findPath($this->graph, $start, $nodeX);

        $this->assertNull($path);
    }

    public function testHasCycleInAcyclicGraph(): void
    {
        $this->assertFalse(GraphTraversal::hasCycle($this->graph));
    }

    public function testHasCycleInCyclicGraph(): void
    {
        $nodeA = $this->graph->getNode('A');
        $nodeD = $this->graph->getNode('D');
        $this->graph->addEdge(new Edge($nodeD, $nodeA));

        $this->assertTrue(GraphTraversal::hasCycle($this->graph));
    }
}
