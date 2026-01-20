# PHP Graph Library

A lightweight PHP 8.4 library to handle graph data structures. No external dependencies.

## 🚀 Features

- **Complete Data Structure:** Directed and undirected graphs
- **Traversal Algorithms:** DFS, BFS
- **Pathfinding:** Dijkstra, cycle detection
- **Serialization:** Export/Import to Array and JSON
- **Unit Testing:** Integrated testing framework
- **PHP 8.4:** Uses the latest features (read-only, strict types)


## 🔧 Installation

No dependencies are required, apart from PHPUnit for testing purpose. Simply clone or copy the files into your project.

```bash
# Clone or copy files
# Make sure you have PHP 8.4+ installed
php --version
```

## 💡 Usage

### Basic example

```php
<?php

use Neoxium\Graph\{Graph, Node, Edge, Serializer\GraphSerializer};
use Neoxium\Graph\Algorithm\{GraphTraversal, ShortestPath};
use Neoxium\Graph\Graph\Serializer\GraphSerializer;

// Create a non-oriented graph
$graph = new Graph();

// Add nodes
$paris = new Node('paris', ['population' => 2_161_000]);
$lyon = new Node('lyon', ['population' => 516_000]);
$marseille = new Node('marseille', ['population' => 870_000]);

// Add weighted edges (distance in km)
$graph->addEdge(new Edge($paris, $lyon, 465));
$graph->addEdge(new Edge($paris, $marseille, 775));
$graph->addEdge(new Edge($lyon, $marseille, 314));

echo "Cities count : " . $graph->nodeCount() . "\n";
echo "Roads count : " . $graph->edgeCount() . "\n";
```

### Graph traversal

```php
// Depth-first search (DFS)
$visited = GraphTraversal::depthFirstSearch($graph, $paris);

foreach ($visited as $city) {
    echo $city->id . " (pop: " . $city->data['population'] . ")\n";
}

// Breadth-first search (BFS)
$visited = GraphTraversal::breadthFirstSearch($graph, $paris);

// Find a path
$path = GraphTraversal::findPath($graph, $paris, $marseille);
if ($path) {
    echo "Found path : ";
    echo implode(' -> ', array_map(fn($n) => $n->id, $path));
}
```

### Shortest path (Dijkstra)

```php
use GraphLib\ShortestPath;

// Create a weighted directed graph
$graph = new Graph(directed: true);

$a = new Node('A');
$b = new Node('B');
$c = new Node('C');
$d = new Node('D');

$graph
    ->addEdge(new Edge($a, $b, 4.0, true))
    ->addEdge(new Edge($a, $c, 2.0, true))
    ->addEdge(new Edge($c, $b, 1.0, true))
    ->addEdge(new Edge($b, $d, 5.0, true))
    ->addEdge(new Edge($c, $d, 8.0, true));

$result = ShortestPath::dijkstra($graph, $a, $d);

if ($result) {
    echo "Distance: " . $result['distance'] . "\n";
    echo "Path: ";
    echo implode(' -> ', array_map(fn($n) => $n->id, $result['path']));
}
```

### Cycles detection

```php
if (GraphTraversal::hasCycle($graph)) {
    echo "This graph contains a cycle\n";
} else {
    echo "This graph is acyclic\n";
}
```

### Serialization

```php
// Export to array
$data = GraphSerializer::toArray($graph);
print_r($data);

// Export to JSON
$json = GraphSerializer::toJson($graph);
file_put_contents('graph.json', $json);

// Import from JSON
$restored = GraphSerializer::fromJson(file_get_contents('graph.json'));

// Import from array
$graph2 = GraphSerializer::fromArray([
    'directed' => false,
    'nodes' => [
        ['id' => 'A', 'data' => null],
        ['id' => 'B', 'data' => ['value' => 42]]
    ],
    'edges' => [
        ['from' => 'A', 'to' => 'B', 'weight' => 1.0]
    ]
]);
```

## 🧪 Tests

Install PHPUnit from dev dependencies, then run the tests from the root directory.

```bash
php ./vendor/phpunit/phpunit/phpunit tests
```

## 🎯 Use case

### Social network

```php
$social = new Graph();

$alice = new Node('alice', ['name' => 'Alice', 'age' => 28]);
$bob = new Node('bob', ['name' => 'Bob', 'age' => 32]);
$charlie = new Node('charlie', ['name' => 'Charlie', 'age' => 25]);

$social
    ->addEdge(new Edge($alice, $bob))
    ->addEdge(new Edge($bob, $charlie))
    ->addEdge(new Edge($charlie, $alice));

// Find Alice's friends
$friends = $social->getNeighbors($alice);
```

### Dependency system

```php
$deps = new Graph(directed: true);

$modules = [
    'core' => new Node('core'),
    'auth' => new Node('auth'),
    'api' => new Node('api'),
    'ui' => new Node('ui')
];

$deps
    ->addEdge(new Edge($modules['auth'], $modules['core'], directed: true))
    ->addEdge(new Edge($modules['api'], $modules['auth'], directed: true))
    ->addEdge(new Edge($modules['ui'], $modules['api'], directed: true));

// Check circular dependencies
if (GraphTraversal::hasCycle($deps)) {
    throw new Exception('Circular dependency detected!');
}
```

### GPS Routing

```php
$map = new Graph(directed: true);

$nodes = [
    'home' => new Node('home', ['lat' => 45.75, 'lon' => 4.85]),
    'work' => new Node('work', ['lat' => 45.76, 'lon' => 4.86]),
    'shop' => new Node('shop', ['lat' => 45.74, 'lon' => 4.84])
];

// Distances in minutes
$map
    ->addEdge(new Edge($nodes['home'], $nodes['work'], 15, true))
    ->addEdge(new Edge($nodes['home'], $nodes['shop'], 8, true))
    ->addEdge(new Edge($nodes['shop'], $nodes['work'], 12, true));

$route = ShortestPath::dijkstra($map, $nodes['home'], $nodes['work']);
echo "Optimal travel time: " . $route['distance'] . " min\n";
```

## ⚡ Performance

### Algorithms complexity

- **Node Addition**: O(1)
- **Edge Addition**: O(1)
- **DFS/BFS**: O(V + E)
- **Dijkstra**: O(V²) (with possible optimization to O((V + E) log V))
- **Cycle Detection**: O(V + E)

### Optimizations

- Use of `readonly` for immutability
- Strict types to avoid conversions
- Adjacency structure for fast access
- No external dependencies

## 📚 API Reference

### Main classes

#### `Graph`
- `__construct(bool $directed = false)`
- `addNode(Node $node): self`
- `addEdge(Edge $edge): self`
- `getNode(string|int $id): ?Node`
- `getNodes(): Node[]`
- `getNeighbors(Node $node): Node[]`
- `removeNode(string|int $id): self`
- `nodeCount(): int`
- `edgeCount(): int`

#### `GraphTraversal`
- `depthFirstSearch(Graph $graph, Node $start): Node[]`
- `breadthFirstSearch(Graph $graph, Node $start): Node[]`
- `findPath(Graph $graph, Node $start, Node $end): ?Node[]`
- `hasCycle(Graph $graph): bool`

#### `ShortestPath`
- `dijkstra(Graph $graph, Node $start, Node $end): ?array`

#### `GraphSerializer`
- `toArray(Graph $graph): array`
- `toJson(Graph $graph, int $flags = JSON_PRETTY_PRINT): string`
- `fromArray(array $data): Graph`
- `fromJson(string $json): Graph`

## 🔒 Best practices

1. **Immutability**: Use `readonly` for properties that do not change.
2. **Strict Types**: Always declare `declare(strict_types=1);`
3. **Validation**: Check the existence of nodes before manipulation.
4. **Memory Management**: For large graphs, consider paging.
5. **Serialization**: Use JSON for persistence.

## 📄 Licence

MIT License – Free to use

## 🤝 Contribution

Contributions are welcome! Please ensure that all tests pass before submitting (see the Tests section).