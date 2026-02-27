@extends('admin.layouts.app')

@section('header', 'Projects')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('projects.create') }}"
       class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        New Project
    </a>
</div>

<div class="overflow-hidden bg-white shadow ring-1 ring-black/5 sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Pos</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">URL</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Visible</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Actions</th>
            </tr>
        </thead>
        <tbody id="sortable-projects" class="divide-y divide-gray-200 bg-white">
            @forelse($projects as $project)
            <tr data-id="{{ $project->id }}" class="cursor-move">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500">{{ $project->position }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900">{{ $project->name }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $project->charge }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    @if($project->url)
                        <a href="{{ $project->url }}" target="_blank" class="text-indigo-600 hover:underline">{{ $project->url_label }}</a>
                    @endif
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $project->is_visible ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $project->is_visible ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-right text-sm">
                    <a href="{{ route('projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline ml-3"
                          onsubmit="return confirm('Delete this project?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No projects yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sortable-projects');
    let draggedRow = null;

    tbody.querySelectorAll('tr[data-id]').forEach(row => {
        row.draggable = true;
        row.addEventListener('dragstart', e => {
            draggedRow = row;
            row.classList.add('opacity-50');
        });
        row.addEventListener('dragend', () => {
            draggedRow = null;
            row.classList.remove('opacity-50');
        });
        row.addEventListener('dragover', e => e.preventDefault());
        row.addEventListener('drop', e => {
            e.preventDefault();
            if (draggedRow && draggedRow !== row) {
                tbody.insertBefore(draggedRow, row.nextSibling);
                saveOrder();
            }
        });
    });

    function saveOrder() {
        const ids = [...tbody.querySelectorAll('tr[data-id]')].map(r => parseInt(r.dataset.id));
        fetch('{{ route("projects.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids })
        });
    }
});
</script>
@endsection
@endsection
