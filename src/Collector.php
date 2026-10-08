<?php

declare(strict_types=1);

namespace AstralDebug;

/**
 * Collecte les métriques de debug pour la barre d'outils.
 * Activé uniquement en environnement de développement.
 */
final class Collector
{
    private float $startedAt;

    /** @var list<array{sql: string, ms: float, bindings: array<mixed>}> */
    private array $queries = [];

    /** @var list<array{message: string, level: string}> */
    private array $messages = [];

    /** @var array<string, mixed> */
    private array $meta = [];

    public function __construct()
    {
        $this->startedAt = microtime(true);
    }

    public function reset(): void
    {
        $this->startedAt = microtime(true);
        $this->queries = [];
        $this->messages = [];
        $this->meta = [];
    }

    /** @param array<mixed> $bindings */
    public function addQuery(string $sql, float $ms = 0.0, array $bindings = []): void
    {
        $this->queries[] = [
            'sql'      => $sql,
            'ms'       => $ms,
            'bindings' => $bindings,
        ];
    }

    public function addMessage(string $message, string $level = 'info'): void
    {
        $this->messages[] = [
            'message' => $message,
            'level'   => $level,
        ];
    }

    public function setMeta(string $key, mixed $value): void
    {
        $this->meta[$key] = $value;
    }

    public function elapsedMs(): float
    {
        return round((microtime(true) - $this->startedAt) * 1000, 2);
    }

    public function memoryPeakMb(): float
    {
        return round(memory_get_peak_usage(true) / 1024 / 1024, 2);
    }

    /** @return list<array{sql: string, ms: float, bindings: array<mixed>}> */
    public function queries(): array
    {
        return $this->queries;
    }

    public function queryCount(): int
    {
        return count($this->queries);
    }

    /** @return list<array{message: string, level: string}> */
    public function messages(): array
    {
        return $this->messages;
    }

    /** @return array<string, mixed> */
    public function meta(): array
    {
        return $this->meta;
    }
}
