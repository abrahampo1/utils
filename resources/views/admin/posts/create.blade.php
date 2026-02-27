@extends('admin.layouts.app')

@section('header', 'New Post')

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data"
          class="space-y-6 bg-white border-black border border-r-3 border-b-3 p-6">
        @csrf
        @include('admin.posts._form')

        <div class="flex justify-end gap-3">
            <a href="{{ route('posts.index') }}" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold">Cancel</a>
            <button type="submit" class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">Create</button>
        </div>
    </form>
</div>
@endsection
