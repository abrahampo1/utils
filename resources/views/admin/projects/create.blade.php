@extends('admin.layouts.app')

@section('header', 'New Project')

@section('content')
<div class="mx-auto max-w-2xl">
    <form method="POST" action="{{ route('projects.store') }}" class="space-y-6 bg-white p-6 shadow sm:rounded-lg">
        @csrf
        @include('admin.projects._form')

        <div class="flex justify-end gap-3">
            <a href="{{ route('projects.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Create</button>
        </div>
    </form>
</div>
@endsection
