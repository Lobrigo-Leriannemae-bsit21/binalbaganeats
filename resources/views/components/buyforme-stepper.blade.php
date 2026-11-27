@props(['status'])

@php
    $current = $status->stage();
@endphp

@if ($current === null)
    <div class="alert alert-danger mb-0" role="status">
        <i class="bi bi-x-circle me-1"></i> {{ $status->message() }}
    </div>
@else
    <ol class="be-stepper list-unstyled mb-0">
        @foreach (\App\Enums\BuyForMeStatus::STAGES as $number => $label)
            @php
                $state = ($number < $current || $current === 6) ? 'done' : ($number === $current ? 'current' : 'todo');
            @endphp
            <li class="be-step {{ $state }}" @if ($state === 'current') aria-current="step" @endif>
                <span class="be-step-dot">@if ($state === 'done')<i class="bi bi-check-lg"></i>@endif</span>
                <span class="be-step-label">{{ $label }}</span>
                @if ($state === 'current')
                    <span class="be-step-note">{{ $status->message() }}</span>
                @endif
            </li>
        @endforeach
    </ol>
@endif