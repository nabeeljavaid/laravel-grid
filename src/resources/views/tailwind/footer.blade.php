@php
    $__data                = $grid->getData();
    $__order_by            = $__data['order_by'] ?? '';
    $__order               = $__data['order'] ?? '';
    $__limit               = $__data['limit'] ?? '';
    $__page                = $__data['page'] ?? 1;
    $__total_pages         = $__data['total_pages'] ?? 1;
    $__first_page          = $__data['first_page'] ?? 1;
    $__last_page           = $__data['last_page'] ?? 1;
    $__next_page           = $__data['next_page'] ?? null;
    $__prev_page           = $__data['prev_page'] ?? null;
    $__paginationStyle     = $grid->getPaginationStyle();
    $__recordsPerPageStyle = $grid->getRecordsPerPageStyle();
    $__recordsPerPage      = $grid->getRecordsPerPageOptions();

    $__inputClass  = 'border border-gray-300 rounded text-sm px-2 py-1 w-16 focus:outline-none focus:ring-1 focus:ring-blue-500';
    $__selectClass = 'border border-gray-300 rounded text-sm px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500';
@endphp

<div class="laravel-grid-footer">

    {{-- Pagination --}}
    @if($__paginationStyle == 'LINKS' && $__total_pages > 1)
        <div class="laravel-grid-pagination">
            {!! $__data['links'] !!}
        </div>
    @elseif($__paginationStyle == 'INPUT')
        <div class="laravel-grid-pagination">
            <ul>
                @if($__page != 1)
                    <li><a href="{{ $grid->buildUrl(['page' => $__first_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--first" Page="{{ $__first_page }}"><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>
                    <li><a href="{{ $grid->buildUrl(['page' => $__prev_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--prev" Page="{{ $__prev_page }}"><i class="fa fa-backward" aria-hidden="true"></i></a></li>
                @else
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--first"><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--prev"><i class="fa fa-backward" aria-hidden="true"></i></a></li>
                @endif

                <li><input type="text" name="page" id="laravel-grid-page" class="{{ $__inputClass }}" value="{{ $__page }}"/><span>&nbsp;&nbsp;of {{ $__total_pages }}</span></li>

                @if($__page != $__total_pages)
                    <li><a href="{{ $grid->buildUrl(['page' => $__next_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--next" Page="{{ $__next_page }}"><i class="fa fa-forward" aria-hidden="true"></i></a></li>
                    <li><a href="{{ $grid->buildUrl(['page' => $__last_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--last" Page="{{ $__last_page }}"><i class="fa fa-step-forward" aria-hidden="true"></i></a></li>
                @else
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--next"><i class="fa fa-forward" aria-hidden="true"></i></a></li>
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--last"><i class="fa fa-step-forward" aria-hidden="true"></i></a></li>
                @endif
            </ul>
        </div>
    @elseif($__paginationStyle == 'SELECT')
        <div class="laravel-grid-pagination">
            <ul>
                @if($__page != 1)
                    <li><a href="{{ $grid->buildUrl(['page' => $__first_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--first" Page="{{ $__first_page }}"><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>
                    <li><a href="{{ $grid->buildUrl(['page' => $__prev_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--prev" Page="{{ $__prev_page }}"><i class="fa fa-backward" aria-hidden="true"></i></a></li>
                @else
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--first"><i class="fa fa-step-backward" aria-hidden="true"></i></a></li>
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--prev"><i class="fa fa-backward" aria-hidden="true"></i></a></li>
                @endif

                <li>
                    <select name="page" id="laravel-grid-page" class="{{ $__selectClass }}">
                        @for($__i = 1; $__i <= $__total_pages; $__i++)
                            <option @if($__i == $__page) selected @endif>{{ $__i }}</option>
                        @endfor
                    </select>
                </li>

                @if($__page != $__total_pages)
                    <li><a href="{{ $grid->buildUrl(['page' => $__next_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--next" Page="{{ $__next_page }}"><i class="fa fa-forward" aria-hidden="true"></i></a></li>
                    <li><a href="{{ $grid->buildUrl(['page' => $__last_page]) }}" class="laravel-grid-pagination-item laravel-grid-pagination-item--last" Page="{{ $__last_page }}"><i class="fa fa-step-forward" aria-hidden="true"></i></a></li>
                @else
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--next"><i class="fa fa-forward" aria-hidden="true"></i></a></li>
                    <li><a href="javascript:void(0);" class="laravel-grid-pagination-item laravel-grid-pagination-item--last"><i class="fa fa-step-forward" aria-hidden="true"></i></a></li>
                @endif
            </ul>
        </div>
    @endif

    <div class="clearfix"></div>

    {{-- Records Per Page --}}
    @if($__recordsPerPageStyle == 'LINKS')
        <div class="laravel-grid-records-per-page">Display:
            @foreach($__recordsPerPage as $__record)
                @if($__record == $__limit)
                    <span>{{ $__record }}</span>
                @else
                    <a href="{{ $grid->buildUrl(['limit' => $__record]) }}" recordsPerPage="{{ $__record }}">{{ $__record }}</a>
                @endif
            @endforeach
        </div>
    @elseif($__recordsPerPageStyle == 'INPUT')
        <div class="laravel-grid-records-per-page"> Display: <input type="text" name="limit" id="laravel-grid-limit" class="{{ $__inputClass }}" value="{{ $__limit }}"/></div>
    @elseif($__recordsPerPageStyle == 'SELECT')
        <div class="laravel-grid-records-per-page"> Display:
            <select name="limit" id="laravel-grid-limit" class="{{ $__selectClass }}">
                @foreach($__recordsPerPage as $__record)
                    <option @if($__record == $__limit) selected @endif>{{ $__record }}</option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Display Info --}}
    @if(($__data['total'] ?? 0) > 0)
        <div class="laravel-grid-info">Displaying {{ $__data['from'] }} to {{ $__data['to'] }} of  {{ $__data['total'] }}</div>
    @endif

    <input type="hidden" name="order_by" id="laravel-grid-order-by" value="{{ $__order_by }}"/>
    <input type="hidden" name="order" id="laravel-grid-order" value="{{ $__order }}"/>
    <input type="hidden" name="action" id="laravel-grid-action" value=""/>

    @if($__paginationStyle == 'LINKS')
        <input type="hidden" name="page" id="laravel-grid-page" value="{{ $__page }}"/>
    @endif

    @if($__recordsPerPageStyle == 'LINKS')
        <input type="hidden" name="limit" id="laravel-grid-limit" value="{{ $__limit }}"/>
    @endif

</div>
