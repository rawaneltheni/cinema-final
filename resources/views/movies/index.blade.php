<x-layouts.app title="Cinema Movies" :username="$username">
    <main class="container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>Movie Dashboard</h1>
                    <p class="muted">Add, edit, delete, and search cinema movie records.</p>
                </div>
                <a class="button primary" href="{{ route('movies.create') }}">New Movie</a>
            </div>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form class="search" action="{{ route('movies.index') }}" method="GET">
                <input name="search" value="{{ $search }}" placeholder="Search by title, director, or genre">
                <button class="button secondary" type="submit">Search</button>
                @if ($search)
                    <a class="button secondary" href="{{ route('movies.index') }}">Clear</a>
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
                    @forelse ($movies as $movie)
                        <tr>
                            <td data-label="Title">{{ $movie->title }}</td>
                            <td data-label="Director">{{ $movie->director }}</td>
                            <td data-label="Year">{{ $movie->release_year }}</td>
                            <td data-label="Genre">{{ $movie->genre }}</td>
                            <td data-label="Duration">{{ $movie->duration_minutes }} min</td>
                            <td data-label="Actions">
                                <div class="table-actions">
                                    <a class="button secondary" href="{{ route('movies.edit', $movie) }}">Edit</a>
                                    <form action="{{ route('movies.destroy', $movie) }}" method="POST" onsubmit="return confirm('Delete this movie?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="6">No movies found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $movies->links() }}
            </div>
        </section>
    </main>
</x-layouts.app>
