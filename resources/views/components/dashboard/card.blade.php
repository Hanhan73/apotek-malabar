@props(['title', 'value', 'icon', 'color'])

<div class="col-md-6 col-xl-3">
    <div class="card border-{{ $color }} shadow-sm">
        <div class="card-body d-flex align-items-center">
            <div class="flex-grow-1">
                <h6 class="text-{{ $color }} text-uppercase fw-bold small">{{ $title }}</h6>
                <h4 class="mb-0 fw-bold">{{ $value }}</h4>
            </div>
            <div class="ms-3">
                <i class="bi {{ $icon }} fs-2 text-{{ $color }}"></i>
            </div>
        </div>
    </div>
</div>