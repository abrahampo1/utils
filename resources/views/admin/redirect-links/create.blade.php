@extends('admin.layouts.app')

@section('header', 'New Redirect Link')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('redirect-links.store') }}" class="space-y-6 bg-white border-black border border-r-3 border-b-3 p-6">
        @csrf
        @include('admin.redirect-links._form')

        <div class="flex justify-end gap-3">
            <a href="{{ route('redirect-links.index') }}" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold">Cancel</a>
            <button type="submit" class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">Create</button>
        </div>
    </form>
</div>
@endsection
