@extends("layouts.hub")

@section("title", $category['title'])

@section("breadcrumbs")
    @include('community.partials.breadcrumbs', ['items' => [
        ['label' => 'Forums', 'url' => route('community.forum')],
        ['label' => $parentCategoryLabel, 'url' => route('community.forum.category', $category['subcategory'])],
        ['label' => $category['title']],
    ]])
@endsection

@section("content")
<div class="p-6">
    @include('community.partials.category-page')
</div>
@endsection
