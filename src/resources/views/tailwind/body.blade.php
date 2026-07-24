@php
    $__cols        = $grid->getColumns();
    $__rows        = $grid->getRows();
    $__data        = $grid->getData();
    $__primaryKey  = $grid->getPrimaryKey();
    $__serialNum   = $grid->getSerialNumber();
    $__bulkActions = $grid->getBulkActions();
    $__hasBulk     = is_array($__bulkActions) && !empty($__bulkActions);
@endphp

<div class="laravel-grid-body overflow-x-auto bg-white rounded-lg border border-gray-200 shadow-sm mb-4">
    <table cellspacing="0" cellpadding="0" border="0" class="laravel-grid-table min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                @if($__hasBulk)
                    <th width="20" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><input type="checkbox" value="" class="laravel-grid-selector" /></th>
                @elseif($__serialNum)
                    <th width="20" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                @endif

                @foreach($__cols as $__col)
                    @if($__col['sortable'] == true)
                        @php
                            if (isset($__data['order_by']) && $__data['order_by'] == $__col['name']) {
                                if ($__data['order'] == 'desc') {
                                    $__thClass   = 'laravel-grid-sort--desc';
                                    $__sortOrder = 'asc';
                                    $__linkClass = 'laravel-grid-sort-link--desc';
                                } else {
                                    $__thClass   = 'laravel-grid-sort--asc';
                                    $__sortOrder = 'desc';
                                    $__linkClass = 'laravel-grid-sort-link--asc';
                                }
                            } else {
                                $__thClass   = 'laravel-grid-sort';
                                $__sortOrder = 'asc';
                                $__linkClass = '';
                            }
                        @endphp
                        <th width="{{ $__col['width'] }}" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider {{ $__thClass }}">
                            <a href="{{ $grid->buildUrl(['order_by' => $__col['name'], 'order' => $__sortOrder]) }}" class="{{ $__linkClass }}">{{ $__col['label'] }}</a>
                        </th>
                    @else
                        <th width="{{ $__col['width'] }}" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $__col['label'] }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @php $__counter = 0; @endphp
            @foreach($__rows as $__row)
                @php
                    $__rowId = isset($__row->{$__primaryKey}) ? $__row->{$__primaryKey} : $__counter;
                @endphp
                <tr class="laravel-grid-row--{{ $__rowId }} hover:bg-gray-50">
                    @if($__hasBulk)
                        <td class="px-4 py-2.5 text-sm text-gray-900"><input type="checkbox" name="{{ $__primaryKey }}[]" value="{{ $__row->{$__primaryKey} }}" class="laravel-grid-selector" /></td>
                    @elseif($__serialNum)
                        <td class="px-4 py-2.5 text-sm text-gray-900" data-title="#">{{ ($__data['from'] + $__counter) }}</td>
                    @endif

                    @foreach($__cols as $__col)
                        @if(isset($__col['value']) && is_callable($__col['value']))
                            <td class="px-4 py-2.5 text-sm text-gray-900" data-title="{{ $__col['label'] }}" id="{{ $__col['name'] }}">{!! call_user_func($__col['value'], $__row) !!}</td>
                        @else
                            <td class="px-4 py-2.5 text-sm text-gray-900" data-title="{{ $__col['label'] }}" id="{{ $__col['name'] }}">N/A</td>
                        @endif
                    @endforeach
                </tr>
                @php $__counter++; @endphp
            @endforeach
        </tbody>
    </table>
</div>
