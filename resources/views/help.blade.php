@extends('layouts.app')

@section('title', 'Help Center')

@section('content')
<div class="container py-4 py-lg-5">

    <div class="text-center mb-4">
        <h1 class="h2 mb-2">Help Center</h1>
        <p class="text-muted mb-4">Find quick answers about ordering, payments, delivery and more.</p>

        <div class="mx-auto" style="max-width: 560px;">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="search" id="faq-search" class="form-control" maxlength="100"
                       placeholder="Search for help…" aria-label="Search the help center" autocomplete="off">
            </div>
        </div>
    </div>

    <div class="be-chip-row justify-content-lg-center mb-4" id="faq-chips">
        <button type="button" class="be-cat-chip active" data-category="all">All</button>
        @foreach ($faqs as $category => $items)
            <button type="button" class="be-cat-chip" data-category="{{ $category }}">
                <i class="bi {{ $icons[$category] ?? 'bi-question-circle' }}"></i> {{ $category }}
            </button>
        @endforeach
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">

            @foreach ($faqs as $category => $items)
                @php $group = \Illuminate\Support\Str::slug($category); @endphp

                <section class="mb-4" data-faq-group="{{ $category }}">
                    <h2 class="h5 mb-3"><i class="bi {{ $icons[$category] ?? 'bi-question-circle' }} me-2 text-danger"></i>{{ $category }}</h2>

                    <div class="accordion be-card overflow-hidden" id="faq-{{ $group }}">
                        @foreach ($items as $index => $item)
                            @php $id = "faq-{$group}-{$index}"; @endphp

                            <div class="accordion-item" data-faq-item data-search="{{ \Illuminate\Support\Str::lower($item['q'].' '.$item['a']) }}">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#{{ $id }}"
                                            aria-expanded="false" aria-controls="{{ $id }}">
                                        {{ $item['q'] }}
                                    </button>
                                </h3>
                                <div id="{{ $id }}" class="accordion-collapse collapse" data-bs-parent="#faq-{{ $group }}">
                                    <div class="accordion-body">{{ $item['a'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div id="faq-empty" class="be-card p-5 text-center d-none">
                <i class="bi bi-search fs-1 text-muted"></i>
                <h2 class="h5 mt-2">No answers found</h2>
                <p class="text-muted mb-0">Try different words, or contact us below.</p>
            </div>

            {{-- Contact --}}
            <section class="be-card p-4 mt-5" id="contact">
                <h2 class="h5 mb-3"><i class="bi bi-headset me-2 text-danger"></i>Contact support</h2>

                @if ($contact['email'] || $contact['phone'] || $contact['facebook'])
                    <p class="text-muted">Can’t find what you need? Reach the BinalbaganEats team:</p>

                    <ul class="list-unstyled mb-0">
                        @if ($contact['email'])
                            <li class="py-2"><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
                        @endif
                        @if ($contact['phone'])
                            <li class="py-2"><i class="bi bi-telephone me-2"></i><a href="tel:{{ $contact['phone_link'] }}">{{ $contact['phone'] }}</a></li>
                        @endif
                        @if ($contact['facebook'])
                            <li class="py-2"><i class="bi bi-facebook me-2"></i><a href="{{ $contact['facebook'] }}" target="_blank" rel="noopener noreferrer">Message us on Facebook</a></li>
                        @endif
                        @if ($contact['hours'])
                            <li class="py-2"><i class="bi bi-clock me-2"></i>{{ $contact['hours'] }}</li>
                        @endif
                    </ul>
                @else
                    <p class="text-muted mb-0">
                        Support contact details haven’t been set up yet. Please check back soon.
                    </p>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        (function () {
            var search = document.getElementById('faq-search');
            var chips = document.querySelectorAll('#faq-chips [data-category]');
            var groups = document.querySelectorAll('[data-faq-group]');
            var empty = document.getElementById('faq-empty');
            var category = 'all';

            function apply() {
                var term = search.value.trim().toLowerCase();
                var shown = 0;

                groups.forEach(function (group) {
                    var inCategory = category === 'all' || group.dataset.faqGroup === category;
                    var visibleInGroup = 0;

                    group.querySelectorAll('[data-faq-item]').forEach(function (item) {
                        var match = inCategory && (term === '' || item.dataset.search.indexOf(term) !== -1);
                        item.hidden = !match;
                        if (match) { visibleInGroup++; }
                    });

                    group.hidden = visibleInGroup === 0;
                    shown += visibleInGroup;
                });

                empty.classList.toggle('d-none', shown !== 0);
            }

            search.addEventListener('input', apply);

            chips.forEach(function (chip) {
                chip.addEventListener('click', function () {
                    category = chip.dataset.category;
                    chips.forEach(function (other) { other.classList.toggle('active', other === chip); });
                    apply();
                });
            });
        })();
    </script>
@endpush