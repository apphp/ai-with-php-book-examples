<?php

declare(strict_types=1);

class InformedSearchGraph {
    private array $adjacencyList;
    private array $levels;
    private array $heuristics;
    private array $edgeCosts;

    public function __construct() {
        $this->adjacencyList = [];
        $this->levels = [];
        $this->heuristics = [];
        $this->edgeCosts = [];
    }

    public function addVertex(string $vertex, int $level = -1, float $heuristic = 0.0): void {
        if (!isset($this->adjacencyList[$vertex])) {
            $this->adjacencyList[$vertex] = [];
            $this->levels[$vertex] = $level;
            $this->heuristics[$vertex] = $heuristic;
            $this->edgeCosts[$vertex] = [];
        }
    }

    public function addEdge(string $from, string $to, float $cost = 1.0): void {
        if (!isset($this->adjacencyList[$from]) || !isset($this->adjacencyList[$to])) {
            throw new InvalidArgumentException("Both vertices must exist in the graph.");
        }

        if (!isset($this->edgeCosts[$from])) {
            $this->edgeCosts[$from] = [];
        }

        if (!in_array($to, $this->adjacencyList[$from])) {
            $this->adjacencyList[$from][] = $to;
            $this->edgeCosts[$from][$to] = $cost;
        }
    }

    public function getEdgeCost(string $from, string $to): float {
        if (!isset($this->edgeCosts[$from][$to])) {
            throw new InvalidArgumentException("No edge exists between $from and $to");
        }
        return $this->edgeCosts[$from][$to];
    }

    public function greedySearch(string $start, string $goal): ?array {
        if (!isset($this->adjacencyList[$start]) || !isset($this->adjacencyList[$goal])) {
            throw new InvalidArgumentException("Both start and goal vertices must exist in the graph.");
        }

        $path = [];
        $currentVertex = $start;

        // Keep going until we reach the goal
        while ($currentVertex !== $goal) {
            // Add current vertex to path
            $path[] = [
                'vertex' => $currentVertex,
                'level' => $this->levels[$currentVertex],
                'heuristic' => $this->heuristics[$currentVertex]
            ];

            // Get all neighbors of current vertex
            $neighbors = $this->adjacencyList[$currentVertex];

            if (empty($neighbors)) {
                return null; // Dead end
            }

            // Find neighbor with lowest heuristic value
            $bestNeighbor = null;
            $bestHeuristic = PHP_FLOAT_MAX;

            foreach ($neighbors as $neighbor) {
                $h = $this->heuristics[$neighbor];
                if ($h < $bestHeuristic) {
                    $bestHeuristic = $h;
                    $bestNeighbor = $neighbor;
                }
            }

            // If we can't find a better neighbor, we're stuck
            if ($bestNeighbor === null) {
                return null;
            }

            // Move to the best neighbor
            $currentVertex = $bestNeighbor;
        }

        // Add the goal vertex to complete the path
        $path[] = [
            'vertex' => $goal,
            'level' => $this->levels[$goal],
            'heuristic' => $this->heuristics[$goal]
        ];

        return $path;
    }

    public function aStarSearch(string $start, string $goal): ?array {
        if (!isset($this->adjacencyList[$start]) || !isset($this->adjacencyList[$goal])) {
            throw new InvalidArgumentException("Both start and goal vertices must exist in the graph.");
        }

        $openSet = [$start];
        $cameFrom = [];
        $gScore = [$start => 0.0];
        $fScore = [$start => $this->heuristics[$start]];

        while (!empty($openSet)) {
            // Find node in openSet with lowest fScore
            $current = $openSet[0];
            $currentFScore = $fScore[$current];
            foreach ($openSet as $node) {
                if ($fScore[$node] < $currentFScore) {
                    $current = $node;
                    $currentFScore = $fScore[$node];
                }
            }

            if ($current === $goal) {
                // Reconstruct path
                $path = [$current];
                while (isset($cameFrom[$current])) {
                    $current = $cameFrom[$current];
                    array_unshift($path, $current);
                }
                return $path;
            }

            // Remove current from openSet
            $openSet = array_values(array_diff($openSet, [$current]));

            // Explore neighbors
            foreach ($this->adjacencyList[$current] as $neighbor) {
                $tentativeGScore = $gScore[$current] + $this->getEdgeCost($current, $neighbor);

                if (!isset($gScore[$neighbor]) || $tentativeGScore < $gScore[$neighbor]) {
                    $cameFrom[$neighbor] = $current;
                    $gScore[$neighbor] = $tentativeGScore;
                    $fScore[$neighbor] = $tentativeGScore + $this->heuristics[$neighbor];

                    if (!in_array($neighbor, $openSet)) {
                        $openSet[] = $neighbor;
                    }
                }
            }
        }

        return null; // No path found
    }

    public function beamSearch(string $start, string $goal, int $beamWidth = 2): ?array {
        if (!isset($this->adjacencyList[$start]) || !isset($this->adjacencyList[$goal])) {
            throw new InvalidArgumentException("Both start and goal vertices must exist in the graph.");
        }

        // Initialize beam with start vertex
        $beam = [[
            'vertex' => $start,
            'path' => [
                [
                    'vertex' => $start,
                    'level' => $this->levels[$start],
                    'heuristic' => $this->heuristics[$start]
                ]
            ]
        ]];

        $visited = [$start => true];

        while (!empty($beam)) {
            $candidates = [];

            // Generate all possible next states from current beam
            foreach ($beam as $state) {
                $currentVertex = $state['vertex'];

                // Check if we've reached the goal
                if ($currentVertex === $goal) {
                    return $state['path'];
                }

                // Get all neighbors of current vertex
                foreach ($this->adjacencyList[$currentVertex] as $neighbor) {
                    // Skip if we've already visited this vertex
                    if (isset($visited[$neighbor])) {
                        continue;
                    }

                    $newPath = $state['path'];
                    $newPath[] = [
                        'vertex' => $neighbor,
                        'level' => $this->levels[$neighbor],
                        'heuristic' => $this->heuristics[$neighbor]
                    ];

                    $candidates[] = [
                        'vertex' => $neighbor,
                        'path' => $newPath,
                        'heuristic' => $this->heuristics[$neighbor]
                    ];

                    $visited[$neighbor] = true;
                }
            }

            if (empty($candidates)) {
                return null; // No path found
            }

            // Sort candidates by heuristic value (ascending)
            usort($candidates, function($a, $b) {
                return $a['heuristic'] <=> $b['heuristic'];
            });

            // Keep only the k best candidates (beam width)
            $beam = array_slice($candidates, 0, $beamWidth);
        }

        return null; // No path found
    }

    public function printPath(array $path, bool $showCost = true): void {
        $totalCost = 0;
        $previousVertex = null;

        foreach ($path as $step) {
            // Get current vertex name depending on path format
            $currentVertex = is_array($step) ? $step['vertex'] : $step;

            if ($showCost) {
                if ($previousVertex !== null) {
                    $edgeCost = $this->getEdgeCost($previousVertex, $currentVertex);
                    $totalCost += $edgeCost;
                    echo sprintf("Edge cost from %s to %s: %.1f\n",
                        $previousVertex,
                        $currentVertex,
                        $edgeCost
                    );
                }
            }

            echo sprintf("Node: %s (Level %d, h=%.1f)\n",
                $currentVertex,
                $this->levels[$currentVertex],
                $this->heuristics[$currentVertex]
            );

            $previousVertex = $currentVertex;
        }

        if ($showCost) {
            echo sprintf("\nTotal path cost: %.1f\n", $totalCost);
        }
    }

    public function printGraph(): void {
        foreach ($this->adjacencyList as $vertex => $neighbors) {
            $costs = array_map(function($neighbor) use ($vertex) {
                return sprintf("%s(%.1f)", $neighbor, $this->edgeCosts[$vertex][$neighbor]);
            }, $neighbors);

            echo sprintf("%s (Level %d, h=%.1f) -> %s\n",
                $vertex,
                $this->levels[$vertex],
                $this->heuristics[$vertex],
                implode(', ', $costs)
            );
        }
    }
}
