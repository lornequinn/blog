@php /** @var \LorneQuinn\Blog\Core\Models\Post $post */ @endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $post->title }}</title>
</head>
<body>
    <article>
        <h1>{{ $post->title }}</h1>
        <div class="post-body">
            {!! $renderedBody !!}
        </div>
    </article>
</body>
</html>
