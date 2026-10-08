<?php

declare(strict_types=1);

namespace AstralDebug;

use AstralDebug\Middleware\DebugBarMiddleware;
use Core\Container;
use Core\ServiceProviderInterface;

/**
 * Enregistre Collector + DebugBar + middleware.
 *
 * Dans config/dependencies.php :
 *   AstralDebug\DebugServiceProvider::class,
 *
 * Puis appliquer le middleware globalement ou sur un groupe de routes :
 *   DebugBarMiddleware::class
 */
final class DebugServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container, array $appConfig, array $dbConfig): void
    {
        $enabled = $this->isEnabled($appConfig);

        $container->singleton(Collector::class, static fn () => new Collector());
        $container->singleton(DebugBar::class, static fn () => new DebugBar());

        $container->singleton(DebugBarMiddleware::class, static function ($c) use ($enabled) {
            return new DebugBarMiddleware(
                collector: $c->make(Collector::class),
                debugBar: $c->make(DebugBar::class),
                enabled: $enabled,
            );
        });
    }

    /** @param array<string, mixed> $appConfig */
    private function isEnabled(array $appConfig): bool
    {
        $env = (string) ($_ENV['APP_ENV'] ?? $appConfig['env'] ?? 'production');
        $debug = filter_var($_ENV['APP_DEBUG'] ?? $appConfig['debug'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (!$debug) {
            return false;
        }

        return in_array(strtolower($env), ['local', 'development', 'dev'], true);
    }
}
