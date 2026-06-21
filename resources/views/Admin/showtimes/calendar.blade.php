<x-admin::layouts.app title="Movie Calendar" :username="$username">
    <main class="container calendar-container">
        <section class="panel">
            <div class="toolbar calendar-toolbar">
                <div>
                    <h1>Movie Calendar</h1>
                    <p class="muted">View every movie showtime by date and open an event to edit it.</p>
                </div>

                <div class="calendar-controls">
                    <a class="button secondary" href="{{ route('calendar', ['month' => $month->subMonth()->format('Y-m')]) }}" aria-label="Previous month">&larr;</a>
                    <a class="button secondary" href="{{ route('calendar', ['month' => now()->format('Y-m')]) }}">Today</a>
                    <a class="button secondary" href="{{ route('calendar', ['month' => $month->addMonth()->format('Y-m')]) }}" aria-label="Next month">&rarr;</a>

                    <form action="{{ route('calendar') }}" method="GET">
                        <label class="sr-only" for="calendar_month">Choose month</label>
                        <input class="month-picker" id="calendar_month" name="month" type="month" value="{{ $month->format('Y-m') }}" onchange="this.form.submit()">
                    </form>
                </div>
            </div>

            <div class="toolbar">
                <div>
                    <h2>{{ $month->format('F Y') }}</h2>
                </div>
                <a class="button primary" href="{{ route('showtimes.create') }}">Add Showtime</a>
            </div>

            <div class="calendar-scroll">
                <div class="calendar">
                    <div class="calendar-weekdays" aria-hidden="true">
                        @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $weekday)
                            <div class="calendar-weekday">{{ $weekday }}</div>
                        @endforeach
                    </div>

                    <div class="calendar-grid">
                        @foreach ($calendarDays as $day)
                            @php
                                $dateKey = $day->format('Y-m-d');
                                $dayShowtimes = $showtimesByDate->get($dateKey, collect());
                            @endphp
                            <div class="calendar-day {{ $day->isSameMonth($month) ? '' : 'outside-month' }} {{ $day->isToday() ? 'is-today' : '' }}">
                                <time class="calendar-date" datetime="{{ $dateKey }}" title="{{ $day->format('F j, Y') }}">{{ $day->day }}</time>

                                <div class="calendar-events">
                                    @foreach ($dayShowtimes as $showtime)
                                        <a class="calendar-event" href="{{ route('showtimes.edit', $showtime) }}" title="Edit {{ $showtime->movie_title }}">
                                            @if ($showtime->image)
                                                <img src="{{ str_starts_with($showtime->image, 'http') ? $showtime->image : asset($showtime->image) }}" alt="" loading="lazy">
                                            @else
                                                <span class="calendar-poster-empty">No image</span>
                                            @endif
                                            <span class="calendar-event-info">
                                                <strong>{{ $showtime->movie_title }}</strong>
                                                <small>{{ date('g:i A', strtotime($showtime->start_time)) }} · Hall {{ $showtime->hall_number }}</small>
                                                <small>{{ $showtime->movie_status }}</small>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-admin::layouts.app>
