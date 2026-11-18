@props(['id', 'title', 'size' => ''])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered {{ $size }}">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-be-sage border-bottom">
                <h5 class="modal-title fw-bold text-be-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>