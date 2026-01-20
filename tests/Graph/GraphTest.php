<?php declare(strict_types=1);

namespace Neoxium\GraphTests;

use Neoxium\Graph\Edge;
use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;
use PHPUnit\Framework\TestCase;

class GraphTest extends TestCase
{
    private Graph $graph;

    public function setUp(): void
    {
        $this->graph = new Graph();
    }

    public function testCanCreateEmptyGraph(): void
    {
        $this->assertEquals(0, $this->graph->nodeCount());
        $this->assertEquals(0, $this->graph->edgeCount());
    }

    public function testCanAddNode(): void
    {
        $node = new Node('A', 'data');
        $this->graph->addNode($node);

        $this->assertEquals(1, $this->graph->nodeCount());
        $this->assertTrue($this->graph->hasNode('A'));
    }

    public function testCanAddMultipleNodes(): void
    {
        $this->graph
            ->addNode(new Node('A'))
            ->addNode(new Node('B'))
            ->addNode(new Node('C'));

        $this->assertEquals(3, $this->graph->nodeCount());
    }

    public function testCanAddEdge(): void
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $this->graph->addEdge($edge);

        $this->assertEquals(2, $this->graph->nodeCount());
        $this->assertEquals(1, $this->graph->edgeCount());
    }

    public function testUndirectedEdgeCreatesDoubleConnection(): void
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $this->graph->addEdge(new Edge($nodeA, $nodeB));

        $neighborsA = $this->graph->getNeighbors($nodeA);
        $neighborsB = $this->graph->getNeighbors($nodeB);

        $this->assertCount(1, $neighborsA);
        $this->assertCount(1, $neighborsB);
    }

    public function testDirectedEdgeCreatesSingleConnection(): void
    {
        $graph = new Graph(directed: true);
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $graph->addEdge(new Edge($nodeA, $nodeB, directed: true));

        $neighborsA = $graph->getNeighbors($nodeA);
        $neighborsB = $graph->getNeighbors($nodeB);

        $this->assertCount(1, $neighborsA);
        $this->assertCount(0, $neighborsB);
    }

    public function testCanRemoveNode(): void
    {
        $this->graph
            ->addNode(new Node('A'))
            ->addNode(new Node('B'))
            ->removeNode('A');

        $this->assertEquals(1, $this->graph->nodeCount());
        $this->assertFalse($this->graph->hasNode('A'));
        $this->assertTrue($this->graph->hasNode('B'));
    }

    public function testRemoveNodeRemovesConnectedEdges(): void
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $this->graph
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeC))
            ->removeNode('B');

        $this->assertEquals(2, $this->graph->nodeCount());
        $this->assertEquals(0, $this->graph->edgeCount());
    }

    public function testGetNode(): void
    {
        $node = new Node('A', ['value' => 42]);
        $this->graph->addNode($node);

        $retrieved = $this->graph->getNode('A');
        $this->assertNotNull($retrieved);
        $this->assertEquals('A', $retrieved->id);
        $this->assertEquals(['value' => 42], $retrieved->data);
    }

    public function testGetNonExistentNode(): void
    {
        $node = $this->graph->getNode('Z');
        $this->assertNull($node);
    }
}
