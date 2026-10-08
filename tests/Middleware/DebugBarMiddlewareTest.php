<?php

declare(strict_types=1);

namespace AstralDebug\Tests\Middleware;

use AstralDebug\Collector;
use AstralDebug\DebugBar;
use AstralDebug\Middleware\DebugBarMiddleware;
use Core\Http\Response;
use Core\Request;
use PHPUnit\Framework\TestCase;

final class DebugBarMiddlewareTest extends TestCase
{
    public function test_disabled_passthrough(): void
    {
        $mw = new DebugBarMiddleware(new Collector(), new DebugBar(), enabled: false);
        $response = Response::html('<html><body>ok</body></html>');

        $result = $mw->handle(new Request(), static fn () => $response);

        $this->assertSame($response, $result);
        $this->assertStringNotContainsString('astral-debug-bar', $result->getContent());
    }

    public function test_enabled_injects_bar_into_html(): void
    {
        $mw = new DebugBarMiddleware(new Collector(), new DebugBar(), enabled: true);
        $response = Response::html('<html><body><p>page</p></body></html>');

        /** @var Response $result */
        $result = $mw->handle(new Request(), static fn () => $response);

        $this->assertStringContainsString('astral-debug-bar', $result->getContent());
        $this->assertStringContainsString('<p>page</p>', $result->getContent());
    }

    public function test_skips_json_like_content(): void
    {
        $mw = new DebugBarMiddleware(new Collector(), new DebugBar(), enabled: true);
        $response = Response::html('{"ok":true}')->addHeader('Content-Type', 'application/json');

        /** @var Response $result */
        $result = $mw->handle(new Request(), static fn () => $response);

        $this->assertStringNotContainsString('astral-debug-bar', $result->getContent());
    }
}
