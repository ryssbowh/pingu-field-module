<div class="{{ $classes->toHtml() }}">
    @if($showLabel)
        <div class="{{ $labelClasses->toHtml() }}">
            {{ $label }}
        </div>
    @endif
    <div class="field-inner">
        @yield('inner')
    </div>
</div>