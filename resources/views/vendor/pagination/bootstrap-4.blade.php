
</head>
@if ($paginator->hasPages())
  <nav aria-label="Photos Search Navigation">
                                <ul class="pagination 2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item  disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <a   class="page-link" href="javascript:void(0)"  style="border-left:1px solid ; border-top-left-radius: 5px !important; border-bottom-left-radius: 5px !important;" ><i class="fa fa-angle-left" style="font-size: 18px"></i></a>
                </li>
            @else
                <li class="page-item  ">
                    <a class="page-link " href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" style="border-left:1px solid; border-top-left-radius: 5px !important; border-bottom-left-radius: 5px !important;"><i class="fa fa-angle-left" style="font-size: 18px"></i></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class=" page-item  disabled"    aria-disabled="true"><a  class="page-link"   >{{ $element }}</a></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class=" page-item  active" aria-current="page"><a  class="page-link" >{{ $page }}</a></li>
                        @else
                            <li class="page-item  "><a class=" page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item  ">
                    <a class=" page-link " href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" style="border-right:1px solid; border-top-right-radius: 5px !important; border-bottom-right-radius: 5px !important;"><i class="fa fa-angle-right" style="font-size: 18px"></i></a>
                </li>
            @else
                <li class=" page-item  disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <a  class="page-link" aria-hidden="true" style="border-right:1px solid; border-top-right-radius: 5px !important; border-bottom-right-radius: 5px !important;"><i class="fa fa-angle-right" style="font-size: 18px"></i></a>
                </li>
            @endif
        </ul>
    </nav>
@endif
