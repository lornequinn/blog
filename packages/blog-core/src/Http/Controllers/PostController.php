<?php

declare(strict_types=1);

namespace LorneQuinn\Blog\Core\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View as ViewFactory;
use LorneQuinn\Blog\Core\Enums\PostStatus;
use LorneQuinn\Blog\Core\Models\Post;
use LorneQuinn\Blog\Core\Rendering\BodyPipeline;

final class PostController extends Controller
{
    public function show(string $slug, BodyPipeline $pipeline): View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->where(function ($q): void {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->with('tags')
            ->firstOrFail();

        $renderedBody = $pipeline->run((string) ($post->body ?? ''));

        return ViewFactory::make('blog-core::posts.show', [
            'post' => $post,
            'renderedBody' => $renderedBody,
        ]);
    }
}
