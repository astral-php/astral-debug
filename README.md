# astral-php/astral-debug

Barre de debug pour [Astral](https://github.com/astral-php) — temps, mémoire, SQL, messages. **Dev uniquement.**

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![Tests](https://img.shields.io/badge/tests-PHPUnit%2010%2B-9933CC)](./phpunit.xml)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> `dump()` / `dd()` sont déjà fournis par **astral-core**. Ce package ajoute une **toolbar** HTML.

---

## Prérequis

| Dépendance | Version |
|---|---|
| PHP | ^8.1 |
| astral-php/astral-core | ^1.2 |

---

## Installation

```bash
composer require astral-php/astral-debug --dev
```

### 1. Service provider

Dans `config/dependencies.php` :

```php
AstralDebug\DebugServiceProvider::class,
```

### 2. Middleware

Appliquer globalement (ou sur les routes HTML) :

```php
use AstralDebug\Middleware\DebugBarMiddleware;

// Exemple : groupe racine / middleware global selon votre Router
$router->get('/', HomeController::class, 'index')
    ->middleware(DebugBarMiddleware::class);
```

### 3. Environnement

```env
APP_ENV=local
APP_DEBUG=true
```

Désactivé automatiquement si `APP_DEBUG=false` ou `APP_ENV=production`.

---

## Usage

```php
use AstralDebug\Collector;

$collector = $container->make(Collector::class);
$collector->addQuery('SELECT * FROM users WHERE id = ?', 1.2, [1]);
$collector->addMessage('Cache miss on homepage', 'warning');
```

---

## Tests

```bash
composer test
```

---

## Compatibilité

| astral-core | PHP | astral-debug |
|---|---|---|
| ^1.2 | 8.1 → 8.4 | 0.x |

---

## Licence

MIT — voir [LICENSE](./LICENSE)
