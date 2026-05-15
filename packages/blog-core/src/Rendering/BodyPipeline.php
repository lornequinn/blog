<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Rendering;

/**
 * Ordered chain of body renderers (each `(string): string`).
 *
 * Renderers register with an integer priority; lower runs earlier. Default
 * priority is 0. Within the same priority, registration order is preserved.
 *
 * Pipeline runs registered renderers in turn, passing each output as the
 * next input. Final output is returned to the caller (typically the
 * PostController).
 *
 * @see docs/graph/decisions/body-pipeline.md
 */
final class BodyPipeline
{
    /** @var array<int, array<int, callable(string): string>> */
    private array $renderers = [];

    /**
     * @param  callable(string): string  $renderer
     */
    public function register(callable $renderer, int $priority = 0): void
    {
        $this->renderers[$priority][] = $renderer;
    }

    public function run(string $body): string
    {
        $sortedKeys = array_keys($this->renderers);
        sort($sortedKeys);

        foreach ($sortedKeys as $priority) {
            foreach ($this->renderers[$priority] as $renderer) {
                $body = $renderer($body);
            }
        }

        return $body;
    }
}
