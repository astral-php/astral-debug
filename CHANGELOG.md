# Changelog — astral-php/astral-debug

## [0.1.0] — 2026-10-04

### Ajouté

- `Collector` — métriques (temps, mémoire, SQL, messages, meta).
- `DebugBar` — rendu HTML bas de page + injection `</body>`.
- `DebugBarMiddleware` — activé seulement si `APP_DEBUG=true` et env `local|development|dev`.
- `DebugServiceProvider` pour astral-core ^1.2.
- Suite PHPUnit.
