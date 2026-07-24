<div class="laravel-grid-header">

    {{-- Main Actions --}}
    <div class="laravel-grid-actions">
        @foreach($grid->getMainActions() as $action)
            <a href="{{ $action['url'] }}" target="{{ $action['target'] }}" id="{{ $action['id'] }}" data-method="{{ $action['method'] }}" title="{{ $action['title'] }}" class="{{ $action['class'] }}">{!! $action['icon'] !!} {{ $action['title'] }}</a>&nbsp;&nbsp;
        @endforeach
    </div>

    {{-- Search Form --}}
    @php
        $__cols = $grid->getColumns();
        $__data = $grid->getData();
        $__searchableCols = array_filter($__cols, fn($c) => $c['searchable'] == true);
    @endphp

    @if(!empty($__cols) && count($__searchableCols) > 0)
        <div class="clearfix"></div>

        <div class="laravel-grid-search">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title laravel-grid-title text-capitalize">Search</h3>
                </div>
                <div class="card-body">
                    @php $__fieldCount = 0; @endphp
                    <div class="form-row row">
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
                                    ? ' <button type="button" class="btn btn-xs btn-primary" data-bs-toggle="popover" title="" data-bs-html="true" data-bs-content="' . $__col['searchfield']['help'] . '" data-bs-original-title="Help">?</button>'
                                    : '';
                                $__value   = $__data['search'][$__name] ?? null;

                                $__attr['id'] = $__id;

                                if (in_array('form-control datepicker', $__attr) || in_array('form-control daterange', $__attr)) {
                                    $__attr['readonly'] = true;
                                }

                                if ($__type == 'text') {
                                    $__attr['class'] = ($__attr['class'] ?? '') . ' form-control';
                                    $__field = Form::text('search[' . $__name . ']', $__value, $__attr);
                                } elseif ($__type == 'select') {
                                    $__attr['class'] = ($__attr['class'] ?? '') . ' form-select';
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
                            <div class="form-group form-row col-md-6 mb-3 row">
                                <label class="col-md-4 control-label form-label" for="{{ $__id }}">{{ $__label }}{!! $__help !!}</label>
                                <div class="col-md-8">
                                    {!! $__field !!}
                                </div>
                            </div>
                            @if($__fieldCount % 2 == 0)
                                </div><div class="form-row row">
                            @endif
                        @endif
                    @endforeach
                    </div><!-- /.form-row -->
                </div><!-- /.card-body -->

                <div class="card-footer">
                    <div class="float-right float-end">
                        <button class="btn btn-primary" name="Search" type="submit"><i class="fa fa-search"></i> Search</button> <button class="btn btn-light" name="Reset" type="reset"> Reset</button>
                    </div>
                    <div class="clearfix"></div>
                </div><!-- /.card-footer -->

            </div><!-- /.card -->
        </div><!-- /.laravel-grid-search -->

    @elseif(!empty($__cols))
        <div class="clearfix"></div>
    @else
        <div class="clearfix"></div>

        <div class="laravel-grid-search">
            <div class="float-right float-end">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search" value="{{ request('search') }}">
                    <div class="input-group-btn">
                        <button class="btn btn-light" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div><!-- /.laravel-grid-search -->
    @endif

    {{-- Bulk Actions --}}
    @php $__bulkActions = $grid->getBulkActions(); @endphp
    @if(is_array($__bulkActions) && !empty($__bulkActions))
        <div class="laravel-grid-bulk-actions">
            <div class="float-right float-end">
                @foreach($__bulkActions as $__url => $__title)
                    <a href="javascript:void(0)" data-url="{{ $__url }}" class="btn btn-light"><i class="fa fa-check"></i> {{ $__title }}</a>
                @endforeach
            </div>
            <div class="clearfix"></div>
        </div>
    @endif

</div>
