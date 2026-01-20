<?php declare(strict_types=1);

namespace Neoxium\GraphTests\Serializer;

use Neoxium\Graph\Edge;
use Neoxium\Graph\Graph;
use Neoxium\Graph\Node;
use Neoxium\Graph\Serializer\GraphSerializer;
use PHPUnit\Framework\TestCase;
use JsonException;

class GraphSerializerTest extends TestCase
{
    public function testToArray(): void
    {
        $graph = new Graph();
        $nodeA = new Node('A', 'dataA');
        $nodeB = new Node('B', 'dataB');
        $graph->addEdge(new Edge($nodeA, $nodeB, 2.5));

        $array = GraphSerializer::toArray($graph);

        $this->assertFalse($array['directed']);
        $this->assertCount(2, $array['nodes']);
        $this->assertCount(1, $array['edges']);
        $this->assertEquals(2.5, $array['edges'][0]['weight']);
    }

    /**
     * @throws JsonException
     */
    public function testToJson(): void
    {
        $graph = new Graph();
        $graph->addNode(new Node('A'));

        $json = GraphSerializer::toJson($graph);

        $this->assertIsString($json);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotNull($decoded);
        $this->assertCount(1, $decoded['nodes']);
    }

    public function testFromArray(): void
    {
        $data = [
            'directed' => false,
            'nodes' => [
                ['id' => 'A', 'data' => 'dataA'],
                ['id' => 'B', 'data' => 'dataB']
            ],
            'edges' => [
                ['from' => 'A', 'to' => 'B', 'weight' => 1.0]
            ]
        ];

        $graph = GraphSerializer::fromArray($data);

        $this->assertEquals(2, $graph->nodeCount());
        $this->assertTrue($graph->hasNode('A'));
        $this->assertTrue($graph->hasNode('B'));
    }

    /**
     * @throws JsonException
     */
    public function testFromJson(): void
    {
        $json = '{"directed":true,"nodes":[{"id":"X","data":null}],"edges":[]}';
        $graph = GraphSerializer::fromJson($json);

        $this->assertTrue($graph->isDirected());
        $this->assertEquals(1, $graph->nodeCount());
    }

    /**
     * @throws JsonException
     */
    public function testRoundTrip(): void
    {
        $original = new Graph(directed: true);
        $original
            ->addNode(new Node(1, 'first'))
            ->addNode(new Node(2, 'second'))
            ->addEdge(new Edge(
                $original->getNode(1),
                $original->getNode(2),
                3.14,
                true
            ));

        $json = GraphSerializer::toJson($original);
        $restored = GraphSerializer::fromJson($json);

        $this->assertEquals($original->nodeCount(), $restored->nodeCount());
        $this->assertEquals($original->isDirected(), $restored->isDirected());
    }
}
