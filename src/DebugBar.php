<?php

declare(strict_types=1);

namespace AstralDebug;

/**
 * Rend la barre de debug HTML (bas de page).
 */
final class DebugBar
{
    public function render(Collector $collector): string
    {
        $elapsed = $collector->elapsedMs();
        $memory = $collector->memoryPeakMb();
        $queries = $collector->queryCount();
        $php = PHP_VERSION;
        $env = htmlspecialchars((string) ($collector->meta()['env'] ?? ($_ENV['APP_ENV'] ?? 'local')), ENT_QUOTES, 'UTF-8');
        $uri = htmlspecialchars((string) ($collector->meta()['uri'] ?? ($_SERVER['REQUEST_URI'] ?? '/')), ENT_QUOTES, 'UTF-8');
        $method = htmlspecialchars((string) ($collector->meta()['method'] ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET')), ENT_QUOTES, 'UTF-8');

        $queryRows = '';
        foreach ($collector->queries() as $q) {
            $sql = htmlspecialchars($q['sql'], ENT_QUOTES, 'UTF-8');
            $ms = number_format($q['ms'], 2);
            $queryRows .= "<li><code>{$sql}</code> <span>{$ms} ms</span></li>";
        }
        if ($queryRows === '') {
            $queryRows = '<li class="astral-debug-empty">Aucune requête enregistrée</li>';
        }

        $messageRows = '';
        foreach ($collector->messages() as $m) {
            $msg = htmlspecialchars($m['message'], ENT_QUOTES, 'UTF-8');
            $level = htmlspecialchars($m['level'], ENT_QUOTES, 'UTF-8');
            $messageRows .= "<li data-level=\"{$level}\">[{$level}] {$msg}</li>";
        }
        if ($messageRows === '') {
            $messageRows = '<li class="astral-debug-empty">Aucun message</li>';
        }

        return <<<HTML
<style>
.astral-debug-bar{position:fixed;left:0;right:0;bottom:0;z-index:99999;font:12px/1.4 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;background:#12141a;color:#e8eaf0;border-top:1px solid #2a2f3a;box-shadow:0 -8px 24px rgba(0,0,0,.35)}
.astral-debug-bar summary{list-style:none;cursor:pointer;display:flex;flex-wrap:wrap;gap:12px;padding:8px 14px;align-items:center}
.astral-debug-bar summary::-webkit-details-marker{display:none}
.astral-debug-bar .pill{background:#1c2230;border:1px solid #31394a;border-radius:4px;padding:2px 8px;color:#9aa3b5}
.astral-debug-bar .pill strong{color:#7dd3c0;font-weight:600}
.astral-debug-bar .brand{color:#8b7cf7;font-weight:700;letter-spacing:.04em}
.astral-debug-panels{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:0 14px 12px;max-height:220px;overflow:auto}
.astral-debug-panels h4{margin:0 0 6px;color:#9aa3b5;font-size:11px;text-transform:uppercase;letter-spacing:.06em}
.astral-debug-panels ul{margin:0;padding:0;list-style:none}
.astral-debug-panels li{padding:4px 0;border-bottom:1px solid #232833;color:#c9cfdb}
.astral-debug-panels code{color:#f0c674}
.astral-debug-empty{color:#6b7280 !important;border:0 !important}
@media (max-width:720px){.astral-debug-panels{grid-template-columns:1fr}}
</style>
<details class="astral-debug-bar" open>
  <summary>
    <span class="brand">Astral Debug</span>
    <span class="pill"><strong>{$elapsed}</strong> ms</span>
    <span class="pill"><strong>{$memory}</strong> MB</span>
    <span class="pill"><strong>{$queries}</strong> SQL</span>
    <span class="pill">PHP <strong>{$php}</strong></span>
    <span class="pill">{$env}</span>
    <span class="pill">{$method} {$uri}</span>
  </summary>
  <div class="astral-debug-panels">
    <div>
      <h4>Queries</h4>
      <ul>{$queryRows}</ul>
    </div>
    <div>
      <h4>Messages</h4>
      <ul>{$messageRows}</ul>
    </div>
  </div>
</details>
HTML;
    }

    public function inject(string $html, Collector $collector): string
    {
        $bar = $this->render($collector);

        if (stripos($html, '</body>') !== false) {
            return (string) preg_replace('/<\/body>/i', $bar . '</body>', $html, 1);
        }

        return $html . $bar;
    }
}
