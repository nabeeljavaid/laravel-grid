<div class="laravel-grid-header">

    {{-- Main Actions --}}
    <div class="laravel-grid-actions flex flex-wrap gap-2 mb-3">
        @foreach($grid->getMainActions() as $action)
            <a href="{{ $action['url'] }}" target="{{ $action['target'] }}" id="{{ $action['id'] }}" data-method="{{ $action['method'] }}" title="{{ $action['title'] }}" class="{{ $action['class'] }}">{!! $action['icon'] !!} {{ $action['title'] }}</a>
        @endforeach
    </div>

    {{-- Search Form --}}
    @php
        $__cols = $grid->getColumns();
        $__data = $grid->getData();
        $__searchableCols = array_filter($__cols, fn($c) => $c['searchable'] == true);
    @endphp

    @if(!empty($__cols) && count($__searchableCols) > 0)

        <div class="laravel-grid-search mb-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <h3 class="laravel-grid-title text-sm font-semibold text-gray-700 uppercase tracking-wide">Search</h3>
                </div>
                <div class="p-4">
                    @php
                        $__fieldCount = 0;
                        $__inputClass = 'block w-full border border-gray-300 rounded-md text-sm py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500';
                        $__selectClass = 'block w-full border border-gray-300 rounded-md text-sm py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 appearance-none';
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($__cols as $__col)
                        @if($__col['searchable'] == true)
                            @php
                                $__label   = $__col['label'] ?? 'N/A';
                                $__type    = $__col['searchfield']['type'] ?? 'text';
                                $__name    = $__col['searchfield']['name'] ?? $__col['name'];
                                $__id      = $__col['searchfield']['name'] ?? $__col['name'];
                                $__attr    = $__col['searchfield']['attr'] ?? [];
                                $__options = $__col['searchfield']['options'] ?? [];
                                $__help    = isset($__col['searchfield']['help'])
                                    ? ' <button type="button" class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700" data-bs-toggle="popover" title="" data-bs-html="true" data-bs-content="' . $__col['searchfield']['help'] . '" data-bs-original-title="Help">?</button>'
                                    : '';
                                $__value   = $__data['search'][$__name] ?? null;

                                $__attr['id'] = $__id;

                                if (in_array('form-control datepicker', $__attr) || in_array('form-control daterange', $__attr)) {
                                    $__attr['readonly'] = true;
                                }

                                if ($__type == 'text') {
                                    $__attr['class'] = $__inputClass . ' ' . ($__attr['class'] ?? '');
                                    $__field = Form::text('search[' . $__name . ']', $__value, $__attr);
                                } elseif ($__type == 'select') {
                                    $__attr['class'] = $__selectClass . ' ' . ($__attr['class'] ?? '');
                                    $__attr['placeholder'] = '----- Select ' . $__label . ' -----';
                                    $__field = Form::select('search[' . $__name . ']', $__options, $__value, $__attr);
                                } elseif ($__type == 'radios') {
                                    $__field = Form::radios('search[' . $__name . ']', $__options, $__value, $__attr);
                                } elseif ($__type == 'checkboxes') {
                                    $__field = Form::checkboxes('search[' . $__name . ']', $__options, $__value, $__attr);
                                } else {
                                    $__field = '';
                                }

                                $__fieldCount++;
                            @endphp
                            <div class="flex items-start gap-3">
                                <label class="w-1/3 text-sm font-medium text-gray-700 pt-1.5 shrink-0" for="{{ $__id }}">{{ $__label }}{!! $__help !!}</label>
                                <div class="flex-1">
                                    {!! $__field !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                    </div><!-- /.grid -->
                </div><!-- /.card-body -->

                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <div class="flex justify-end gap-2">
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded text-white bg-blue-600 hover:bg-blue-700" name="Search" type="submit"><i class="fa fa-search"></i> Search</button>
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded text-gray-700 bg-white border border-gray-300 hover:bg-gray-50" name="Reset" type="reset">Reset</button>
                    </div>
                </div><!-- /.card-footer -->

            </div><!-- /.card -->
        </div><!-- /.laravel-grid-search -->

    @elseif(!empty($__cols))
        <div class="clearfix"></div>
    @else
        <div class="clearfix"></div>

        <div class="laravel-grid-search mb-4">
            <div class="flex justify-end">
                <div class="flex">
                    <input type="text" name="search" class="border border-gray-300 rounded-l-md text-sm py-1.5 px-2 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search" value="{{ request('search') }}">
                    <button class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-r-md text-gray-700 bg-white border border-l-0 border-gray-300 hover:bg-gray-50" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </div><!-- /.laravel-grid-search -->
    @endif

    {{-- Bulk Actions --}}
    @php $__bulkActions = $grid->getBulkActions(); @endphp
    @if(is_array($__bulkActions) && !empty($__bulkActions))
        <div class="laravel-grid-bulk-actions mb-3">
            <div class="flex justify-end gap-2">
                @foreach($__bulkActions as $__url => $__title)
                    <a href="javascript:void(0)" data-url="{{ $__url }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded text-gray-700 bg-white border border-gray-300 hover:bg-gray-50"><i class="fa fa-check"></i> {{ $__title }}</a>
                @endforeach
            </div>
        </div>
    @endif

</div>
