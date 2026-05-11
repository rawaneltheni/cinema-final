<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $projects = Project::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('director', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'search' => $search,
            'username' => $request->session()->get('username'),
        ]);
    }

    public function create(): View
    {
        return view('projects.form', [
            'project' => new Project(),
            'username' => session('username'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Project::create($this->validatedProject($request));

        return redirect()->route('projects.index')->with('status', 'Cinema project added successfully.');
    }

    public function edit(Project $project): View
    {
        return view('projects.form', [
            'project' => $project,
            'username' => session('username'),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validatedProject($request));

        return redirect()->route('projects.index')->with('status', 'Cinema project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Cinema project deleted successfully.');
    }

    private function validatedProject(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'director' => ['required', 'string', 'max:255'],
            'release_year' => ['required', 'integer', 'between:1888,2100'],
            'genre' => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'between:1,600'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
