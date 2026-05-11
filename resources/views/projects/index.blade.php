<x-layouts.app title="Cinema Projects" :username="$username">
    <main class="container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>Project Dashboard</h1>
                    <p class="muted">Add, edit, delete, and search cinema website project records.</p>
                </div>
                <a class="button primary" href="{{ route('projects.create') }}">New Project</a>
            </div>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form class="search" action="{{ route('projects.index') }}" method="GET">
                <input name="search" value="{{ $search }}" placeholder="Search by title, director, or genre">
                <button class="button secondary" type="submit">Search</button>
                @if ($search)
                    <a class="button secondary" href="{{ route('projects.index') }}">Clear</a>
                @endif
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Director</th>
                        <th>Year</th>
                        <th>Genre</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td data-label="Title">{{ $project->title }}</td>
                            <td data-label="Director">{{ $project->director }}</td>
                            <td data-label="Year">{{ $project->release_year }}</td>
                            <td data-label="Genre">{{ $project->genre }}</td>
                            <td data-label="Duration">{{ $project->duration_minutes }} min</td>
                            <td data-label="Actions">
                                <div class="table-actions">
                                    <a class="button secondary" href="{{ route('projects.edit', $project) }}">Edit</a>
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this cinema project?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="6">No cinema projects found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $projects->links() }}
            </div>
        </section>
    </main>
</x-layouts.app>
