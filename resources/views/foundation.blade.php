@extends('layouts.app')

@section('title', 'Foundation Check')

@section('content')
<div class="container py-5">

    <div class="text-center mb-5">
        <div class="d-flex justify-content-center">
            <x-logo :size="90" />
        </div>
        <h1 class="h3 mt-4">Foundation Check</h1>
        <p class="text-muted mb-0">Phase 0 is complete when every item below is green.</p>
    </div>

    <div class="row g-4">

        {{-- Environment --}}
        <div class="col-md-6">
            <div class="be-card p-4 h-100">
                <h2 class="h6 text-uppercase text-muted mb-3">Environment</h2>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom"><span>PHP</span><strong>{{ $phpVersion }}</strong></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span>Laravel</span><strong>{{ $laravelVersion }}</strong></li>
                    <li class="d-flex justify-content-between py-2 border-bottom"><span>App environment</span><strong>{{ $environment }}</strong></li>
                    <li class="d-flex justify-content-between py-2"><span>Timezone</span><strong>{{ $timezone }}</strong></li>
                </ul>
            </div>
        </div>

        {{-- Database --}}
        <div class="col-md-6">
            <div class="be-card p-4 h-100">
                <h2 class="h6 text-uppercase text-muted mb-3">Database</h2>

                @if ($connected)
                    <p class="mb-2">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        Connected to <strong>{{ $database }}</strong>
                        <span class="text-muted">({{ $driver }})</span>
                    </p>
                    @unless ($usingMysql)
                        <div class="alert alert-warning py-2 mb-2">
                            You are not using MySQL. Set <code>DB_CONNECTION=mysql</code> in <code>.env</code>,
                            then run <code>php artisan config:clear</code>.
                        </div>
                    @endunless
                    <p class="mb-0 text-muted">Migrations recorded: <strong>{{ $migrationsRun }}</strong></p>
                @else
                    <p class="mb-2">
                        <i class="bi bi-x-circle-fill text-danger"></i>
                        Could not connect to the database.
                    </p>
                    <p class="small text-muted mb-0">{{ $error }}</p>
                @endif
            </div>
        </div>

        {{-- Tables --}}
        <div class="col-12">
            <div class="be-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 text-uppercase text-muted mb-0">Required tables</h2>
                    @if ($connected)
                        @php $found = collect($tables)->filter()->count(); @endphp
                        <span class="be-badge {{ $found === count($tables) ? 'be-badge-success' : 'be-badge-danger' }}">
                            {{ $found }} / {{ count($tables) }} found
                        </span>
                    @endif
                </div>

                @if ($connected)
                    <div class="row g-2">
                        @foreach ($tables as $name => $exists)
                            <div class="col-sm-6 col-lg-4">
                                <i class="bi {{ $exists ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }}"></i>
                                <code>{{ $name }}</code>
                            </div>
                        @endforeach
                    </div>
                    @if (collect($tables)->contains(false))
                        <p class="small text-danger mt-3 mb-0">
                            Some tables are missing. Run <code>php artisan migrate</code> in the project folder.
                        </p>
                    @endif
                @else
                    <p class="text-muted mb-0">Fix the database connection first.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection