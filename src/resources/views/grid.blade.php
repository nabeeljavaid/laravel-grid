<!--Grid Start-->
<div id="{{ $grid->getGridName() }}" class="laravel-grid">
    @php $__fw = config('laravel-grid.css_framework', 'bootstrap'); @endphp
    <form name="gridFrom" action="" method="get"
          class="laravel-grid-form{{ $__fw === 'tailwind' ? '' : ' form-horizontal' }}"
          data-total="{{ $grid->getData()['total'] }}"
          data-export-max-limit="{{ config('settings.export.max_limit') }}">
        <div class="laravel-grid-inner">
            @if($__fw === 'tailwind')
                @include('laravel-grid::tailwind.header')
                @include('laravel-grid::tailwind.body')
                @include('laravel-grid::tailwind.footer')
            @else
                @include('laravel-grid::partials.header')
                @include('laravel-grid::partials.body')
                @include('laravel-grid::partials.footer')
            @endif
            <div class="clearfix"></div>
        </div>
    </form>
</div>
<!--Grid End-->
