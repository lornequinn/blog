@php /** @var \LorneQuinn\Blog\Core\Models\Post $post */ @endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $post->title }}</title>
</head>
<body>
    @include('blog-core::posts._article')
</body>
</html>
