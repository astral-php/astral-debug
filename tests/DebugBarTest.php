<?php

declare(strict_types=1);

namespace AstralDebug\Tests;

use AstralDebug\Collector;
use AstralDebug\DebugBar;
use PHPUnit\Framework\TestCase;

final class DebugBarTest extends TestCase
{
    public function test_render_contains_metrics(): void
    {
        $c = new Collector();
        $c->addQuery('SELECT * FROM articles', 2.5);
        $c->addMessage('boot ok');
        $c->setMeta('env', 'local');
        $c->setMeta('uri', '/');
        $c->setMeta('method', 'GET');

        $html = (new DebugBar())->render($c);

        $this->assertStringContainsString('Astral Debug', $html);
        $this->assertStringContainsString('SELECT * FROM articles', $html);
        $this->assertStringContainsString('boot ok', $html);
        $this->assertStringContainsString('local', $html);
    }

    public function test_inject_before_body_close(): void
    {
        $c = new Collector();
        $page = '<html><body><h1>Hi</h1></body></html>';
        $out = (new DebugBar())->inject($page, $c);

        $this->assertMatchesRegularExpression('/astral-debug-bar.*<\/body>/s', $out);
    }
}
