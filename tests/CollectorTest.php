<?php

declare(strict_types=1);

namespace AstralDebug\Tests;

use AstralDebug\Collector;
use PHPUnit\Framework\TestCase;

final class CollectorTest extends TestCase
{
    public function test_tracks_queries_and_messages(): void
    {
        $c = new Collector();
        $c->addQuery('SELECT 1', 0.5, []);
        $c->addMessage('hello', 'info');
        $c->setMeta('uri', '/demo');

        $this->assertSame(1, $c->queryCount());
        $this->assertSame('SELECT 1', $c->queries()[0]['sql']);
        $this->assertSame('hello', $c->messages()[0]['message']);
        $this->assertSame('/demo', $c->meta()['uri']);
        $this->assertGreaterThanOrEqual(0.0, $c->elapsedMs());
        $this->assertGreaterThan(0.0, $c->memoryPeakMb());
    }

    public function test_reset_clears_state(): void
    {
        $c = new Collector();
        $c->addQuery('SELECT 1');
        $c->addMessage('x');
        $c->reset();

        $this->assertSame(0, $c->queryCount());
        $this->assertSame([], $c->messages());
    }
}
