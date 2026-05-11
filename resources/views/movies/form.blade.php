<x-layouts.app :title="$movie->exists ? 'Edit Movie' : 'New Movie'" :username="$username">
    <main class="container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>{{ $movie->exists ? 'Edit Movie' : 'New Movie' }}</h1>
                    <p class="muted">Complete the movie details below.</p>
                </div>
                <a class="button secondary" href="{{ route('movies.index') }}">Back</a>
            </div>

            <form action="{{ $movie->exists ? route('movies.update', $movie) : route('movies.store') }}" method="POST">
                @csrf
                @if ($movie->exists)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="field">
                        <label for="title">Title</label>
                        <input id="title" name="title" value="{{ old('title', $movie->title) }}">
                        @error('title') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="director">Director</label>
                        <input id="director" name="director" value="{{ old('director', $movie->director) }}">
                        @error('director') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="release_year">Release Year</label>
                        <input id="release_year" name="release_year" type="number" min="1888" max="2100" value="{{ old('release_year', $movie->release_year) }}">
                        @error('release_year') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="genre">Genre</label>
                        <input id="genre" name="genre" value="{{ old('genre', $movie->genre) }}">
                        @error('genre') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="duration_minutes">Duration Minutes</label>
                    <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="600" value="{{ old('duration_minutes', $movie->duration_minutes) }}">
                    @error('duration_minutes') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $movie->description) }}</textarea>
                    @error('description') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="actions">
                    <button class="button primary" type="submit">{{ $movie->exists ? 'Update Movie' : 'Create Movie' }}</button>
                </div>
            </form>
        </section>
    </main>
</x-layouts.app>
