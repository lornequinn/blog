@php /** @var \LorneQuinn\Blog\Core\Models\Post $post */ @endphp
<article>
    <h1>{{ $post->title }}</h1>
    <div class="post-body">
        {!! $renderedBody !!}
    </div>
</article>
