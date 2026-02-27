@extends('admin.layouts.app')

@section('header', 'Projects')

@section('content')
<div class="mb-4 flex justify-end">
    <a href="{{ route('projects.create') }}"
       class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">
        New Project
    </a>
</div>

<div class="border-black border border-r-3 border-b-3 bg-white">
    <table class="min-w-full">
        <thead>
            <tr class="border-b border-black">
                <th class="py-3.5 pl-4 pr-3 text-left text-sm tinos-bold text-black">Pos</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Name</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Role</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">URL</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Visible</th>
                <th class="px-3 py-3.5 text-right text-sm tinos-bold text-black">Actions</th>
            </tr>
        </thead>
        <tbody id="sortable-projects">
            @forelse($projects as $project)
            <tr data-id="{{ $project->id }}" class="cursor-move border-b border-black">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-600">{{ $project->position }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm tinos-bold text-black">{{ $project->name }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $project->charge }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                    @if($project->url)
                        <a href="{{ $project->url }}" target="_blank" class="text-black hover:underline">{{ $project->url_label }}</a>
                    @endif
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="inline-flex border-black border px-2 py-0.5 text-xs tinos-regular-italic">
                        {{ $project->is_visible ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-right text-sm">
                    <a href="{{ route('projects.edit', $project) }}" class="text-black hover:underline tinos-bold">Edit</a>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline ml-3"
                          onsubmit="return confirm('Delete this project?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-black hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-600">No projects yet.</td>
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
