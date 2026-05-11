<x-layouts.app :title="$project->exists ? 'Edit Project' : 'New Project'" :username="$username">
    <main class="container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>{{ $project->exists ? 'Edit Project' : 'New Project' }}</h1>
                    <p class="muted">Complete the cinema project details below.</p>
                </div>
                <a class="button secondary" href="{{ route('projects.index') }}">Back</a>
            </div>

            <form action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}" method="POST">
                @csrf
                @if ($project->exists)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="field">
                        <label for="title">Title</label>
                        <input id="title" name="title" value="{{ old('title', $project->title) }}">
                        @error('title') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="director">Director</label>
                        <input id="director" name="director" value="{{ old('director', $project->director) }}">
                        @error('director') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="release_year">Release Year</label>
                        <input id="release_year" name="release_year" type="number" min="1888" max="2100" value="{{ old('release_year', $project->release_year) }}">
                        @error('release_year') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="genre">Genre</label>
                        <input id="genre" name="genre" value="{{ old('genre', $project->genre) }}">
                        @error('genre') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="duration_minutes">Duration Minutes</label>
                    <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="600" value="{{ old('duration_minutes', $project->duration_minutes) }}">
                    @error('duration_minutes') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description">{{ old('description', $project->description) }}</textarea>
                    @error('description') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="actions">
                    <button class="button primary" type="submit">{{ $project->exists ? 'Update Project' : 'Create Project' }}</button>
                </div>
            </form>
        </section>
    </main>
</x-layouts.app>
