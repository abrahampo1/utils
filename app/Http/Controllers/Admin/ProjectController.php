<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::ordered()->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'url_label' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'charge' => ['nullable', 'string', 'max:255'],
            'dark_mode' => ['boolean'],
            'is_visible' => ['boolean'],
        ]);

        $data['dark_mode'] = $request->boolean('dark_mode');
        $data['is_visible'] = $request->boolean('is_visible');
        $data['position'] = Project::max('position') + 1;

        Project::create($data);

        return redirect()->route('projects.index')->with('success', 'Project created.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'url_label' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'charge' => ['nullable', 'string', 'max:255'],
            'dark_mode' => ['boolean'],
            'is_visible' => ['boolean'],
        ]);

        $data['dark_mode'] = $request->boolean('dark_mode');
        $data['is_visible'] = $request->boolean('is_visible');

        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:projects,id'],
        ]);

        foreach ($request->ids as $position => $id) {
            Project::where('id', $id)->update(['position' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
