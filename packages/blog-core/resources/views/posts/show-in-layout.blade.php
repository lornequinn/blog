@php /** @var \LorneQuinn\Blog\Core\Models\Post $post */ @endphp
@extends(config('blog-core.layout'))

@section('title', $post->title)

@section(config('blog-core.layout_section', 'content'))
    @include('blog-core::posts._article')
@endsection
