<?php

declare(strict_types=1);

namespace AstralDebug\Middleware;

use AstralDebug\Collector;
use AstralDebug\DebugBar;
use Core\Http\Response;
use Core\Middleware\MiddlewareInterface;
use Core\Request;

/**
 * Injecte la barre de debug dans les réponses HTML (dev uniquement).
 */
final class DebugBarMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Collector $collector,
        private readonly DebugBar $debugBar,
        private readonly bool $enabled,
    ) {
    }

    public function handle(Request $request, callable $next): mixed
    {
        if (!$this->enabled) {
            return $next();
        }

        $this->collector->reset();
        $this->collector->setMeta('uri', $request->uri);
        $this->collector->setMeta('method', $request->method);
        $this->collector->setMeta('env', $_ENV['APP_ENV'] ?? 'local');

        $response = $next();

        if (!$response instanceof Response) {
            return $response;
        }

        $contentType = $response->getHeaders()['Content-Type'] ?? 'text/html';
        if (!str_contains(strtolower($contentType), 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === '' || str_starts_with(ltrim($content), '{') || str_starts_with(ltrim($content), '[')) {
            return $response;
        }

        return $response->setContent($this->debugBar->inject($content, $this->collector));
    }
}
