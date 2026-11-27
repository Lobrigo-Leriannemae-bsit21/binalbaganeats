@props(['status'])

@php
    $current = \App\Support\OrderTracker::stage($status);
@endphp

@if ($current === null)
    <div class="alert alert-danger mb-0" role="status">
        <i class="bi bi-x-circle me-1"></i> {{ \App\Support\OrderTracker::message($status) }}
    </div>
@else
    <ol class="be-stepper list-unstyled mb-0">
        @foreach (\App\Support\OrderTracker::STAGES as $number => $label)
            @php
                $state = ($number < $current || $current === 4) ? 'done' : ($number === $current ? 'current' : 'todo');
            @endphp
            <li class="be-step {{ $state }}" @if ($state === 'current') aria-current="step" @endif>
                <span class="be-step-dot">@if ($state === 'done')<i class="bi bi-check-lg"></i>@endif</span>
                <span class="be-step-label">{{ $label }}</span>
                @if ($state === 'current')
                    <span class="be-step-note">{{ \App\Support\OrderTracker::message($status) }}</span>
                @endif
            </li>
        @endforeach
    </ol>
@endif