@extends('admin.layouts.app')

@section('header', 'Edit Post')

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data"
          class="space-y-6 bg-white p-6 shadow sm:rounded-lg">
        @csrf @method('PUT')
        @include('admin.posts._form')

        <div class="flex justify-end gap-3">
            <a href="{{ route('posts.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update</button>
        </div>
    </form>
</div>
@endsection
