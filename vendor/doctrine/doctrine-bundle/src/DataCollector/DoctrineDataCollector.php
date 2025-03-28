<?php

namespace Doctrine\Bundle\DoctrineBundle\DataCollector;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Cache\Logging\CacheLoggerChain;
use Doctrine\ORM\Cache\Logging\StatisticsCacheLogger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector as BaseCollector;
use Symfony\Bridge\Doctrine\Middleware\Debug\DebugDataHolder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function array_map;
use function array_sum;
use function arsort;
use function assert;
use function count;
use function usort;

/**
 * @phpstan-type QueryType = array{
 *    executionMS: float,
 *    explainable: bool,
 *    sql: string,
 *    params: ?array<array-key, mixed>,
 *    runnable: bool,
 *    types: ?array<array-key, Type|int|string|null>,
 * }
 * @phpstan-type DataType = array{
 *    caches: array{
 *       enabled: bool,
 *       counts: array<"puts"|"hits"|"misses", int>,
 *       log_enabled: bool,
 *       regions: array<"puts"|"hits"|"misses", array<string, int>>,
 *    },
 *    connections: list<string>,
 *    entities: array<string, array<class-string, array{class: class-string, file: false|string, line: false|int}>>,
 *    errors: array<string, array<class-string, list<string>>>,
 *    managers: list<string>,
 *    queries: array<string, list<QueryType>>,
 *    entityCounts: array<string, array<class-string, int>>
 * }
 * @psalm-property DataType $data
 */
class DoctrineDataCollector extends BaseCollector
{
    private int|null $invalidEntityCount = null;

    private int|null $managedEntityCount = null;

    /**
     * @var mixed[][]|null
     * @phpstan-var ?array<string, list<QueryType&array{count: int, index: int, executionPercent?: float}>>
     * @phpstan-ignore property.unusedType
     */
    private array|null $groupedQueries = null;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly bool $shouldValidateSchema = true,
        DebugDataHolder|null $debugDataHolder = null,
    ) {
        parent::__construct($registry, $debugDataHolder);
    }

    public function collect(Request $request, Response $response, Throwable|null $exception = null): void
    {
        parent::collect($request, $response, $exception);

        $errors       = [];
        $entities     = [];
        $entityCounts = [];
        $caches       = [
            'enabled' => false,
            'log_enabled' => false,
            'counts' => [
                'puts' => 0,
                'hits' => 0,
                'misses' => 0,
            ],
            'regions' => [
                'puts' => [],
                'hits' => [],
                'misses' => [],
            ],
        ];

        foreach ($this->registry->getManagers() as $name => $em) {
            assert($em instanceof EntityManagerInterface);
            if ($this->shouldValidateSchema) {
                $entities[$name] = [];

                $factory   = $em->getMetadataFactory();
                $validator = new SchemaValidator($em);

                foreach ($factory->getLoadedMetadata() as $class) {
                    if (isset($entities[$name][$class->getName()])) {
                        continue;
                    }

                    $classErrors                        = $validator->validateClass($class);
                    $r                                  = $class->getReflectionClass();
                    $entities[$name][$class->getName()] = [
                        'class' => $class->getName(),
                        'file' => $r->getFileName(),
                        'line' => $r->getStartLine(),
                    ];

                    if (empty($classErrors)) {
                        continue;
                    }

                    $errors[$name][$class->getName()] = $classErrors;
                }
            }

            $entityCounts[$name] = [];
            foreach ($em->getUnitOfWork()->getIdentityMap() as $className => $entityList) {
                $entityCounts[$name][$className] = count($entityList);
            }

            // Sort entities by count (in descending order)
            arsort($entityCounts[$name]);

            $emConfig   = $em->getConfiguration();
            $slcEnabled = $emConfig->isSecondLevelCacheEnabled();

            if (! $slcEnabled) {
                continue;
            }

            $caches['enabled'] = true;

            $cacheConfiguration = $emConfig->getSecondLevelCacheConfiguration();
            assert($cacheConfiguration instanceof CacheConfiguration);
            $cacheLoggerChain = $cacheConfiguration->getCacheLogger();
            assert($cacheLoggerChain instanceof CacheLoggerChain || $cacheLoggerChain === null);

            if (! $cacheLoggerChain || ! $cacheLoggerChain->getLogger('statistics')) {
                continue;
            }

            $cacheLoggerStats = $cacheLoggerChain->getLogger('statistics');
            assert($cacheLoggerStats instanceof StatisticsCacheLogger);
            $caches['log_enabled'] = true;

            $caches['counts']['puts']   += $cacheLoggerStats->getPutCount();
            $caches['counts']['hits']   += $cacheLoggerStats->getHitCount();
            $caches['counts']['misses'] += $cacheLoggerStats->getMissCount();

            foreach ($cacheLoggerStats->getRegionsPut() as $key => $value) {
                if (! isset($caches['regions']['puts'][$key])) {
                    $caches['regions']['puts'][$key] = 0;
                }

                $caches['regions']['puts'][$key] += $value;
            }

            foreach ($cacheLoggerStats->getRegionsHit() as $key => $value) {
                if (! isset($caches['regions']['hits'][$key])) {
                    $caches['regions']['hits'][$key] = 0;
                }

                $caches['regions']['hits'][$key] += $value;
            }

            foreach ($cacheLoggerStats->getRegionsMiss() as $key => $value) {
                if (! isset($caches['regions']['misses'][$key])) {
                    $caches['regions']['misses'][$key] = 0;
                }

                $caches['regions']['misses'][$key] += $value;
            }
        }

        $this->data['entities']     = $entities;
        $this->data['errors']       = $errors;
        $this->data['caches']       = $caches;
        $this->data['entityCounts'] = $entityCounts;
        $this->groupedQueries       = null;
    }

    /** @return array<string, array<class-string, array{class: class-string, file: false|string, line: false|int}>> */
    public function getEntities()
    {
        return $this->data['entities'];
    }

    /** @return array<string, array<string, list<string>>> */
    public function getMappingErrors()
    {
        return $this->data['errors'];
    }

    /** @return int */
    public function getCacheHitsCount()
    {
        return $this->data['caches']['counts']['hits'];
    }

    /** @return int */
    public function getCachePutsCount()
    {
        return $this->data['caches']['counts']['puts'];
    }

    /** @return int */
    public function getCacheMissesCount()
    {
        return $this->data['caches']['counts']['misses'];
    }

    /** @return bool */
    public function getCacheEnabled()
    {
        return $this->data['caches']['enabled'];
    }

    /**
     * @return array<string, array<string, int>>
     * @phpstan-return array<"puts"|"hits"|"misses", array<string, int>>
     */
    public function getCacheRegions()
    {
        return $this->data['caches']['regions'];
    }

    /** @return array<string, int> */
    public function getCacheCounts()
    {
        return $this->data['caches']['counts'];
    }

    /** @return int */
    public function getInvalidEntityCount()
    {
        return $this->invalidEntityCount ??= array_sum(array_map('count', $this->data['errors']));
    }

    public function getManagedEntityCount(): int
    {
        if ($this->managedEntityCount === null) {
            $total = 0;
            foreach ($this->data['entityCounts'] as $entities) {
                $total += array_sum($entities);
            }

            $this->managedEntityCount = $total;
        }

        return $this->managedEntityCount;
    }

    /** @return array<string, array<class-string, int>> */
    public function getManagedEntityCountByClass(): array
    {
        return $this->data['entityCounts'];
    }

    /**
     * @return string[][]
     * @phpstan-return array<string, list<QueryType&array{count: int, index: int, executionPercent?: float}>>
     */
    public function getGroupedQueries()
    {
        if ($this->groupedQueries !== null) {
            return $this->groupedQueries;
        }

        $this->groupedQueries = [];
        $totalExecutionMS     = 0;
        foreach ($this->data['queries'] as $connection => $queries) {
            $connectionGroupedQueries = [];
            foreach ($queries as $i => $query) {
                $key = $query['sql'];
                if (! isset($connectionGroupedQueries[$key])) {
                    $connectionGroupedQueries[$key]                = $query;
                    $connectionGroupedQueries[$key]['executionMS'] = 0;
                    $connectionGroupedQueries[$key]['count']       = 0;
                    $connectionGroupedQueries[$key]['index']       = $i; // "Explain query" relies on query index in 'queries'.
                }

                $connectionGroupedQueries[$key]['executionMS'] += $query['executionMS'];
                $connectionGroupedQueries[$key]['count']++;
                $totalExecutionMS += $query['executionMS'];
            }

            usort($connectionGroupedQueries, static function ($a, $b) {
                if ($a['executionMS'] === $b['executionMS']) {
                    return 0;
                }

                return $a['executionMS'] < $b['executionMS'] ? 1 : -1;
            });
            $this->groupedQueries[$connection] = $connectionGroupedQueries;
        }

        foreach ($this->groupedQueries as $connection => $queries) {
            foreach ($queries as $i => $query) {
                $this->groupedQueries[$connection][$i]['executionPercent'] =
                    $this->executionTimePercentage($query['executionMS'], $totalExecutionMS);
            }
        }

        return $this->groupedQueries;
    }

    private function executionTimePercentage(float $executionTimeMS, float $totalExecutionTimeMS): float
    {
        if (! $totalExecutionTimeMS) {
            return 0;
        }

        return $executionTimeMS / $totalExecutionTimeMS * 100;
    }

    /** @return int */
    public function getGroupedQueryCount()
    {
        $count = 0;
        foreach ($this->getGroupedQueries() as $connectionGroupedQueries) {
            $count += count($connectionGroupedQueries);
        }

        return $count;
    }
}
