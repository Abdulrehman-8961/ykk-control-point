@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <?php
    $userAccess = explode(',', Auth::user()->access_to_client);
    
    $limit = 10;
    $user_id = Auth::id();
    $no_check = DB::table('settings')->where('user_id', $user_id)->first();
    
    if (request()->filled('limit')) {
        $limit = request('limit');
        if ($no_check) {
            DB::table('settings')
                ->where('user_id', $user_id)
                ->update(['test_thresholds' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => $user_id, 'test_thresholds' => $limit]);
        }
    } elseif ($no_check && !empty($no_check->test_thresholds)) {
        $limit = $no_check->test_thresholds;
    }
    
    // Sorting setup
    $sortableFields = ['tt.id', 'tt.item_category_id', 'i.item_category'];
    
    $field = request('field', 'tt.id');
    $orderby = request('orderBy', 'desc');
    
    // Filters
    $status = request('filter_status');
    $search = request('search');
    
    // Base query
    $qry = DB::table('test_thresholds as tt')->leftJoin('test_definitions as td', 'td.id', '=', 'tt.test_name_id')->leftJoin('test_threshold_item_categories as ttic', 'ttic.test_threshold_id', '=', 'tt.id')->leftJoin('itemcodes as i', 'i.id', '=', 'ttic.item_category_id')->where('tt.is_deleted', 0);
    
    // Apply status filter
    if (request()->has('filter_status') && in_array($status, ['0', '1'])) {
        $qry->where('tt.status', (int) $status);
    }
    if (request()->has('filter_test_type')) {
        if (request()->get('filter_test_type') == 'dimension') {
            $qry->where('td.test_type', 'Dimension');
        }
        if (request()->get('filter_test_type') == 'str-min') {
            $qry->where('td.test_type', 'Perf-Str')->where('td.criteria', 'Min');
        }
        if (request()->get('filter_test_type') == 'str-max') {
            $qry->where('td.test_type', 'Perf-Str')->where('td.criteria', 'Max');
        }
        if (request()->get('filter_test_type') == 'perf-weight') {
            $qry->where('td.test_type', 'Perf-Weight');
        }
    }
    if (request()->has('filter_item_cat')) {
        $qry->where('i.item_category', 'like', '%' . request()->get('filter_item_cat') . '%');
    }
    
    // Apply search filter
    if (!empty($search)) {
        $qry->where(function ($query) use ($search) {
            $query->where('td.test_name', 'like', '%' . $search . '%')->orWhere('i.item_category', 'like', '%' . $search . '%');
        });
    }
    
    // Total count with DISTINCT tt.id
    $totalQuery = clone $qry;
    $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT tt.id) as aggregate'))->first()->aggregate;
    
    // Final results with grouping
    $qry = $qry
        ->select('tt.*', 'td.test_name', 'td.uom', 'i.item_category')
        ->groupBy('tt.id')
        ->orderBy($field, $orderby)
        ->paginate($limit)
        ->appends(request()->query());
    
    // Get ID from GET or fallback to first result
    $GETID = !empty($_GET['id']) ? $_GET['id'] : $qry[0]->id ?? null;
    
    ?>




    <main id="main-container pt-0">
        <!-- Hero -->


        <style type="text/css">
            .select2-dropdown {
                z-index: 99999 !important;
            }

            .dropdown-menu {
                z-index: 100000 !important;
            }

            .pagination {
                margin-bottom: 0px;
            }

            #page-header {
                display: none;
            }

            .ActionIcon {

                border-radius: 50%;
                padding: 2px;
            }

            .ActionIcon:hover {

                background: #dadada;
            }

            @media only print {
                .no-print {
                    display: none !important;
                }

                #showData {
                    height: 100% !important;

                }

                .content {
                    background: #F0F3F8;

                }
            }

            body {
                overflow: -moz-scrollbars-vertical;
                overflow-x: hidden;
            }

            .blockDivs .block-header-default {
                background-color: #f1f3f8;
                padding: 7px 1.25rem;
            }

            .blockDivs {
                border: 1px solid lightgrey;
                margin-bottom: 10px !important;
            }

            .cert_type_button label,
            .cert_type_button input {}

            .cert_type_button {
                float: left;
            }

            .cert_type_button input[type="radio"] {
                opacity: 0.011;
                z-index: 100;

                position: absolute;
            }

            .cert_type_button input[type="radio"]:checked+label {
                background: #4194F6;
                font-weight: bold;
                color: white;
            }

            .cert_type_button label:hover {



                background-color: #EEEEEE;
                color: #7F7F7F;


            }

            .cert_type_button label {

                width: 150px;

                border-color: #D9D9D9;
                color: #7F7F7F;
                font-size: 12pt;


            }

            .modal-backdrop {
                background-color: #00000080 !important;
            }

            .alert-info,
            .alert {

                width: auto !important;
                padding-right: 70px;
                background-color: #2626261A !important;
                /*                background-color: #262626 !important;*/

                color: #FFFFFF !important;
                font-family: Signika !important;
                font-size: 14pt !important;
                padding-top: 14px;
                padding-bottom: 14px;
                z-index: 11000 !important;
            }



            .alert-info .close {
                color: #BDBDBE !important;
                font-size: 30px !important;
                top: 10px !important;
                right: 15px !important;
                opacity: 1 !important;
                font-weight: 200 !important;
                width: 33px;
                padding-bottom: 1px;
            }

            .alert-info .close:hover {
                background-color: #BFBFBF !important;
                border-radius: 50%;
            }

            .modal-lg,
            .modal-xl {
                max-width: 950px;
            }

            .alert-info .btn-tooltip {
                color: #00B0F0 !important;
                font-family: Calibri !important;
                font-size: 14pt !important;
                font-weight: bold !important;
            }

            .btn-notify {
                color: #00B0F0;
                font-family: Calibri;
                font-size: 14pt;
                font-weight: bold;
                padding: 5px 13px;
                font-weight: bold;
                border-radius: 7px;
            }

            .btn-link {

                padding: 0.2rem !important;
                margin: .25rem .1rem;
                border-radius: 1rem !important;
            }

            .btn-link:hover {
                /* box-shadow: -1px 2px 4px 3px #99dff9;
                                                                                        background: #99dff9; */
                background-color: #7F7F7F;
                border-radius: 1rem !important;
                box-shadow: none !important;
            }

            .btn-notify:hover {
                color: #00B0F0;
                background: #386875;

            }

            .btnDeleteAttachment {
                position: absolute;
                right: 2px;
                top: 6px;

            }

            .dropdown-menu {
                border: 1px solid #D4DCEC !important;
                box-sizing: 1px 1px 1pxo #D4DCEC;
                box-shadow: 6px 6px 8px #8f8f8f5e;
                border-radius: 11px;
            }

            .bs-select-all,
            .bs-deselect-all,
            .bs-actionsbox .btn-light {
                border: 1px solid #D9D9D9 !important;
                background: white !important;

                color: #2080F4 !important;
                font-weight: normal !important;
                font-family: Calibri !important;
                font-size: 12pt !important;
                border-radius: 15px !important;
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                margin-top: 10px !important;
                margin-bottom: 10px !important;
                margin-left: 10px;
                margin-right: 10px;
                height: 35px !important;
                padding-left: 10px;
                padding-right: 10px;
                min-width: 90px !important;
            }


            .bs-deselect-all:hover {
                background-color: #EEEEEE !important;
                color: #7F7F7F !important;
            }

            .bs-select-all:hover {
                background-color: #EEEEEE !important;
                color: #7F7F7F !important;
            }

            .c1 {
                color: #3F3F3F;
                font-family: 'Calibri';
            }

            .c2 {
                color: #7F7F7F;
                font-family: 'Calibri';
            }

            .c3 {
                color: #595959;
                font-family: 'Calibri';
            }

            .cert_type_button label,
            .cert_type_button input {}

            .cert_type_button {
                float: left;
            }

            .cert_type_button input[type="radio"] {
                opacity: 0.011;
                z-index: 100;

                position: absolute;
            }

            .cert_type_button input[type="radio"]:checked+label {
                background: #4194F6;
                font-weight: bold;
                color: white;
            }

            .cert_type_button label:hover {



                background-color: #EEEEEE;
                color: #7F7F7F;


            }

            .cert_type_button label {

                width: 150px;

                border-color: #D9D9D9;
                color: #7F7F7F;
                font-size: 12pt;


            }

            .modal-backdrop {
                background-color: #00000080 !important;
            }

            .alert-info,
            .alert {

                width: auto !important;
                padding-right: 70px;
                background-color: #262626 !important;

                color: #FFFFFF !important;
                font-family: Calibri !important;
                font-size: 14pt !important;
                padding-top: 14px;
                padding-bottom: 14px;
                z-index: 11000 !important;
            }

            .alert-info .close {
                color: #BDBDBE !important;
                font-size: 30px !important;

                opacity: 1 !important;
                font-weight: 200 !important;
                width: 33px;
                padding-bottom: 3px;
            }

            .alert-info .close:hover {
                background-color: #BFBFBF !important;
                border-radius: 50%;
            }

            .alert-info .btn-tooltip {
                color: #00B0F0 !important;
                font-family: Signika !important;
                font-size: 14pt !important;
                font-weight: bold !important;
            }

            .btn-notify {
                color: #00B0F0;
                font-family: Signika;
                font-size: 14pt;
                font-weight: bold;
                padding: 5px 13px;
                font-weight: bold;
                border-radius: 7px;
            }

            .btn-link {


                margin: .25rem .1rem;
            }

            .btn-link:hover {
                /* box-shadow: -1px 2px 4px 3px #99dff9;
                                                                                        background: #99dff9; */
                background-color: #7F7F7F;
                border-radius: 1rem !important;
                box-shadow: none !important;
            }

            .btn-notify:hover {
                color: #00B0F0;
                background: #386875;

            }

            .btnDeleteAttachment {
                position: absolute;
                right: 2px;
                top: 6px;

            }

            .btnNewAction:hover,
            .btnNewAction1:hover,
            .btnNewAction2:hover {
                background: #59595930;
                border-radius: 50%;
            }

            .btnNewAction {
                height: 29px;
            }

            .btnNewAction1 {
                height: 23px;

            }

            .btnNewAction2 {
                height: 20px;
            }

            .HostActive {
                font-family: Calibri;
                font-size: 9pt;
                font-weight: bold;
                color: #1EFF00;
                letter-spacing: 0px;
            }

            .HostInActive {
                font-family: Calibri;
                font-size: 9pt;
                font-weight: bold;
                color: #E54643;
                letter-spacing: 0px;

            }

            .SSLActive {
                font-family: Calibri;
                font-size: 9pt;
                font-weight: bold;
                color: #FFCC00;
                letter-spacing: 0px;


            }

            .text-info {
                color: #4194F6 !important;
            }

            .text-danger {
                color: #E54643 !important;
            }

            .text-warning {
                color: #FFCC00 !important;
            }



            .custom-tooltip .tooltip-inner {
                font-family: Signika !important;
                font-size: 10pt;
                background-color: #FDFDFD;
                color: #595959 !important;
                min-width: 250px;
                font-weight: normal !important;
                padding: 15px 15px !important;
                border: 1px solid #ECEFF4;
                box-shadow: 3pt 3pt 4pt rgba(0, 0, 0, 0.5);
            }


            .custom-tooltip .tooltip-inner h6 {
                font-size: 11pt !important;
                font-weight: bold !important;
                margin-bottom: 6px !important;
                color: #595959 !important;
            }



            .custom-tooltip .tooltip-inner p {
                margin-left: 7px !important;
                line-height: 1.3 !important;
                color: #000 !important;
                font-weight: normal;
            }


            .custom-tooltip .tooltip-inner .tooltip-h6 {
                margin-bottom: 6px !important;
                margin-left: 0px !important;
            }


            /* .tooltip .arrow::before {
                                                                                        border-top-color: #F2F2F2 !important;
                                                                                        border-bottom-color: #F2F2F2 !important;
                                                                                    } */





            .TopArea input.form-control.searchNew {
                height: 28px !important;
            }

            .TopArea span.input-group-text img {
                width: 15px !important;
            }

            .TopArea .headerSetting img {
                width: 16.1px !important;
            }

            .TopArea select.float-right.form-control.mr-3.px-0 {
                height: 28px !important;
            }

            .spinner {
                color: #ffffff !important;
                display: inline-block;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            /* Class to expand the button */
            .expanded {
                padding-right: 10px;
                width: auto;
                transition: width 0.3s ease-in-out;
            }

            .selected-items-container::-webkit-scrollbar {
                width: 10px !important;
            }
        </style>


        <!-- Page Content -->
        <div class="con   no-print page-header" id="JournalHeader">
            <!-- Full Table -->
            <div class="b   mb-0  ">

                <div class="block-content pt-0 mt-0">

                    <div class="TopArea"
                        style="position: sticky;
    padding-top: 14px;
    z-index: 1000;

    padding-bottom: 11px;">
                        <div class="row align-items-center">
                            <div class="col-sm-4 search-col">
                                <div class="row">
                                    <div class="mb-0 col-sm-1 pl-0 text-cente r pr-0">
                                        <a class="btn btn-dual filterItemcodeModal d2 {{ (@$_GET['filter_status'] && $_GET['filter_status'] !== '') || !empty($_GET['filter_item_code']) || !empty($_GET['description']) ? 'filter-active' : '' }} "
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Filters"
                                            href="javascript:;" id="GeneralFilters">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-filter.png') }}"
                                                width="20">
                                        </a>
                                    </div>
                                    <div class="push mb-0 col-sm-10 pl-0">
                                        <?php
                                        $filter = (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') . (isset($_GET['note']) ? '&note=' . $_GET['note'] : '') . (isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : '') . (isset($_GET['filter_item_code']) ? '&filter_item_code=' . $_GET['filter_item_code'] : '') . (isset($_GET['description']) ? '&description=' . $_GET['description'] : '') . (isset($_GET['filter_item_cat']) ? '&filter_item_cat=' . $_GET['filter_item_cat'] : '') . (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                                        ?>
                                        <form class="push mb-0" method="get" id="form-search"
                                            action="{{ url('test-thresholds/') }}?{{ $filter }}">
                                            <div class="input-group main-search-input-group" id="search-container">
                                                <input type="text" value="{{ @$_GET['search'] }}"
                                                    class="form-control searchNew" name="search"
                                                    placeholder="Search Test Thresholds" id="search-input">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <img src="{{ asset('public/img/ui-icon-search.png') }}"
                                                            width="23px">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="float-left " role="tab" id="accordion2_h1">
                                                <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 pr-sm-4">
                                <div class="row align-items-center">
                                    <div class="col-auto mr-auto text-center" style="">
                                        <!-- <a class="btn btn-dual filterItemcodeModal d2 {{ !empty($_GET['filter_item_category']) || !empty($_GET['filter_item_code']) || !empty($_GET['description']) ? 'filter-active' : '' }} " data-custom-class="header-tooltip"
                                                                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                                                data-original-title="Filters" href="javascript:;" id="GeneralFilters">
                                                                                <img src="{{ asset('public/img/cf-menu-icons/header-filter.png') }}" width="20">
                                                                            </a> -->
                                        <a class="btn btn-dual insert_test_threshold d2 " data-custom-class="header-tooltip"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Add Test Threshold" href="javascript:;" data-toggle="modal"
                                            data-target="#insert-test-threshold-modal">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-add.png') }}"
                                                width="20">
                                        </a>
                                        @if (Auth::user()->role != 'read')
                                            <a class="btn btn-dual d2 btn-export text-white"
                                                data-custom-class="header-tooltip" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Export"
                                                style="padding-bottom: 7px !important; padding-top: 4px !important;"
                                                href="javascript:void();"
                                                data-url="{{ url('export-test-thresholds') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                                <img src="{{ asset('public/img/cf-menu-icons/header-export.png') }}"
                                                    width="20">
                                            </a>
                                            <a class="btn btn-dual  d2 "
                                                style="padding-bottom: 7px !important; padding-top: 4px !important;"
                                                href="javascript:;" data-custom-class="header-tooltip" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Import" id="ImportTestThresholds">
                                                <img src="{{ asset('public/img/cf-menu-icons/header-import.png') }}"
                                                    width="20">
                                            </a>



                                        @endif
                                    </div>
                                    <div class="col-auto mr-auto text-center" style="">
                                        {{ $qry->appends($_GET)->onEachSide(0)->links() }}
                                    </div>
                                    <form id="limit_form" class="ml-2 mb-0"
                                        action="{{ url('test-thresholds') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                        <select name="limit" class="float-right form-control mr-3   px-0"
                                            style="width:auto">
                                            <option value="10" {{ @$limit == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ @$limit == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ @$limit == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ @$limit == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </form>

                                    @if (@Auth::user()->role == 'admin')

                                        <a href="javascript:;" data-toggle="tooltip" data-custom-class="header-tooltip"
                                            data-title="Settings" class="mr-3 text-dark headerSetting d3   "><img
                                                src="{{ asset('public/img/cf-menu-icons/header-settings.png') }}"
                                                width="20">
                                        </a>

                                    @endif
                                    <!-- User Dropdown -->
                                    <div class="dropdown d-inline-block">
                                        <a type="button" class="  " id="page-header-user-dropdown"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">


                                            @if (Auth::user()->user_image == '')
                                                <img class="img-avatar imgAvatar img-avatar48"
                                                    src="{{ asset('public') }}/dashboard_assets/media/avatars/avatar2.jpg"
                                                    alt="">
                                            @else
                                                <img class="img-avatar imgAvatar img-avatar48"
                                                    src="{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}"
                                                    alt="">

                                            @endif
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right p-0"
                                            aria-labelledby="page-header-user-dropdown">

                                            <div class="p-2">
                                                @auth
                                                    <a class="dropdown-item" href="{{ url('change-password') }}">
                                                        <i class="far fa-fw fa-user mr-1"></i> My Profile
                                                    </a>






                                                    <!-- END Side Overlay -->
                                                    <form id="logout-form" class="mb-0" method="post"
                                                        action="{{ url('logout') }}">
                                                        @csrf
                                                    </form>
                                                    <div role="separator" class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="javascript:;"
                                                        onclick="document.getElementById('logout-form').submit()">
                                                        <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                                    </a>
                                                @else
                                                    <a class="dropdown-item" href="{{ url('/login') }}">
                                                        <i class="far fa-fw fa-user mr-1"></i> Login
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>


                    </div>
                </div>
            </div>


















            <div class="container-fluid pb-0">
                <div class="row">
                    <div class="col-md-4 bubble-header">
                        <div class="row align-items-center">
                            <?php
                            $search = isset($_GET['search']) && trim($_GET['search']) !== '';
                            $filter_status = isset($_GET['filter_status']) && trim($_GET['filter_status']) !== '';
                            $filter_item_cat = isset($_GET['filter_item_cat']) && trim($_GET['filter_item_cat']) !== '';
                            $filter_test_type = isset($_GET['filter_test_type']) && trim($_GET['filter_test_type']) !== '';
                            
                            $class = 'bubble-header-grey';
                            $get_text = '';
                            $get_text_before = '';
                            $get_text_display = 'd-none';
                            
                            if ($search && ($filter_status || $filter_item_cat || $filter_test_type)) {
                                $class = 'bubble-header-green';
                                $get_text = 'Filtered and Search Results:';
                                $get_text_before = '';
                                $get_text_display = 'd-block';
                            } elseif ($search) {
                                $class = 'bubble-header-yellow';
                                $get_text = '';
                                $get_text_before = 'Search Results';
                                $get_text_display = 'd-block';
                            } elseif ($filter_status || $filter_item_cat || $filter_test_type) {
                                $class = 'bubble-header-blue';
                                $get_text = 'Filters Applied:';
                                $get_text_before = '';
                                $get_text_display = 'd-block';
                            }
                            ?>
                            <div class="col-1 {{ $class }}"></div>
                            <p class="col-11 bubble-header-text d-flex justify-content-between align-items-center">Testing
                                Threshold
                                <span class="{{ $get_text_display }} text-right" style="line-height: 1.3;">
                                    <a class="clear-link" href="{{ url('/test-thresholds') }}"
                                        data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                        data-placement="top" title="" data-original-title="Clear">
                                        <img class="nav-main-link-icon "
                                            src="{{ asset('public/img/cf-menu-icons/menu-icon-deactivate-grey.png') }}"
                                            data-default="{{ asset('public/img/cf-menu-icons/menu-icon-deactivate-grey.png') }}"
                                            data-hover="{{ asset('public/img/cf-menu-icons/3dot-deactivate.png') }}"
                                            width="16">
                                    </a>
                                    <a type="button" class="filterItemcodeModal bubble-a-tag"
                                        href="javascript:;">{{ $get_text }}</a>
                                    <br>
                                    <span class="bubble-filter-count">{{ $get_text_before }} {{ $totalRows }}
                                        Documents</span>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-8 detail-header detail-header-blue">
                        <div class="h-100 d-flex align-items-center">
                            <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img src="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold-white.png') }}"
                                        style="width: 36px; height: 36px;">
                                    <div class="" style="margin-left: 0.91rem;">
                                        <h4 class="mb-1 header-new-text header-item-code" style="line-height:22px">Testing
                                            Threshold</h4>
                                        <p class="mb-0  header-new-subtext {{ $totalRows == 0 ? 'd-none' : 'd-block' }}"
                                            style="line-height:17px">Last modified on <span
                                                class="header-item-code-lastUpdateAt"></span> by <span
                                                class="header-item-code-lastUpdateBy"></span></p>
                                    </div>
                                </div>
                                <div class="new-header-icon-div d-flex align-items-center  no-print">
                                    @if ($totalRows != 0)
                                        <a href="javascript:;" d="" class="text-white attachment-icon"
                                            data-item-id="{{ @$GETID }}" data-item-code="" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Add Attachment">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-attach2.png') }}"
                                                width="24px">
                                        </a>
                                        <a href="javascript:;" d="" class="text-white comment-icon"
                                            data-item-id="{{ @$GETID }}" data-item-code="" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Add Comment">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-comments.png') }}"
                                                width="24px">
                                        </a>
                                        <span class="status-deactivate">
                                            <a href="javascript:;" class="btnEnd" data=""
                                                data-id="{{ @$GETID }}" data-toggle="tooltip" data-trigger="hover"
                                                data-placement="top" title="" data-original-title="Deactivate"><img
                                                    src="{{ asset('public/img/cf-menu-icons/header-deactivate.png') . '?cache=1' }}"
                                                    width="24px"></a>
                                        </span>
                                        <span class="status-reactivate">
                                            <a href="javascript:;" class="btnEnd" data=""
                                                data-id="{{ @$GETID }}" data-toggle="tooltip" data-trigger="hover"
                                                data-placement="top" title="" data-original-title="Reactivate"><img
                                                    src="{{ asset('public/img/cf-menu-icons/header-activate.png') . '?cache=1' }}"
                                                    width="24px"></a>
                                        </span>
                                        <a href="javascript:;" onclick="window.print()" d="" class="text-white print-icon"
                                            data-item-id="{{ @$GETID }}" data-item-code="" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Print">
                                            <img src="{{ asset('public/img/action-white-print.png') }}" width="24px">
                                        </a>
                                        <a href="javascript:;" d="" class="text-white edit-icon"
                                            data-item-id="{{ @$GETID }}" data-id="{{ @$GETID }}"
                                            data-item-code="" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Edit">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-edit.png') }}"
                                                width="24px">
                                        </a>
                                        <a href="javascript:;" d="" class="text-white delete-icon"
                                            data-item-id="{{ @$GETID }}" data-item-code="" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Delete">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-delete.png') }}"
                                                width="24px">
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid pb-0">
                <div class="row">
                    <div class="col-md-4 bubble-header">
                        <!-- Filter dropdown container - initially hidden -->
                        <div class="filter-dropdown-container roll-down">
                            <div class="row align-items-center">
                                <div class="col-1  {{ $class }}" style="height: 300px;"></div>
                                <form id="filterForm" method="GET" action="{{ url('/test-thresholds') }}"
                                    class="mb-0 col-11 py-0 px-3">
                                    <div class="d-flex justify-content-between align-items-center pl-2 mb-3">
                                        <span class="font-filter">Filters</span>
                                        <button type="button" class="close close-cross close-filter" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="block block-transparent mb-0">
                                        <div class="pl-3 pt-0 pb-0">
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Status</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <select class="modal-input form-control shadow-none" name="filter_status"
                                                        id="filter_status">
                                                        <option value="">Select Status</option>
                                                        <option value="1"
                                                            <?= @$_GET['filter_status'] == '1' ? 'selected' : '' ?>>Active
                                                        </option>
                                                        <option value="0"
                                                            <?= @$_GET['filter_status'] == '0' ? 'selected' : '' ?>>Inactive
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Test Type</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <select class="modal-input form-control shadow-none"
                                                        name="filter_test_type" id="filter_test_type">
                                                        <option value="">Select Test Type</option>
                                                        <option value="dimension"
                                                            <?= @$_GET['filter_test_type'] == 'dimension' ? 'selected' : '' ?>>
                                                            Dimension
                                                        </option>
                                                        <option value="str-min"
                                                            <?= @$_GET['filter_test_type'] == 'str-min' ? 'selected' : '' ?>>
                                                            Perf-Str-Min
                                                        </option>
                                                        <option value="str-max"
                                                            <?= @$_GET['filter_test_type'] == 'str-max' ? 'selected' : '' ?>>
                                                            Perf-Str-Max
                                                        </option>
                                                        <option value="perf-weight"
                                                            <?= @$_GET['filter_test_type'] == 'perf-weight' ? 'selected' : '' ?>>
                                                            Perf-Weight
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Item Category</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <input type="text" class="modal-input form-control" value="{{ request()->get('filter_item_cat') }}"
                                                        style="box-shadow: none" name="filter_item_cat" id="filter_item_cat">
                                                </div>
                                            </div> --}}
                                            {{-- item category filter --}}
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Item
                                                    Category</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <select class="shadow-none form-control select2" id="filter_item_cat"
                                                        name="filter_item_cat">
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- /item category filter --}}
                                        </div>
                                        <input type="hidden" name="search" value="{{ @$_GET['search'] }}">
                                        <div class="block-content text-right pt-1 pr-0" style="padding-left: 9mm;">
                                            <a href="{{ url('/test-thresholds') }}" class="btn btn-action mr-3">Clear</a>
                                            <button type="submit" class="btn btn-action" name="filtered_itemcodes">
                                                <span class="btn-action-gear d-none mr-2"><img
                                                        src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                                Apply
                                                <span class="btn-action-gear d-none ml-2"><img
                                                        src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid pt-0">
                <!-- Page Content -->
                <div class="row px-0">

                    <div class="col-lg-4 pr-1 no-print bubble-div" style="overflow-y: auto;height: 82vh;">
                        <div style="padding-top: 15px;">
                            @foreach ($qry as $q)
                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent mr-2"
                                    data="{{ $q->id }}" style="cursor:pointer;">
                                    <div class="block-content py-2 pl-3 d-flex position-relative pr-2" style="">

                                        <div class="w-100 ">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <span class="font-signika bubble-item-title" data-toggle="tooltip"
                                                        data-trigger="hover" data-placement="top" title=""
                                                        data-original-title="Test Name">
                                                        {{ $q->test_name }}
                                                    </span>
                                                    <span class="font-signika bubble-item-title fw-300 ml-2"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="Test UOM"
                                                        style="font-size: 9pt; border-color: #989698;">
                                                        {{ $q->uom }}
                                                    </span>
                                                </div>
                                                <div style="position: absolute;right: 10px;top: 10px;">
                                                    @if ($q->status == 1)
                                                        <span class="font-signika bubble-status-active" data-toggle="tooltip"
                                                            data-trigger="hover" data-placement="top" title=""
                                                            data-original-title="Status">Active</span>
                                                    @else
                                                        <span class="font-signika bubble-status-inactive"
                                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                            title="" data-original-title="Status">Inactive</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between" style="margin-top: 9px;">
                                                <div class="mob-minor-tags">
                                                    <?php
                                                    $test_threshold_item_categories = DB::table('test_threshold_item_categories as ttic')
                                                        ->select('ttic.*', 'ic.item_category')
                                                        // ->leftJoin('item_categories as ic', 'ic.id', '=', 'ttic.item_category_id')
                                                        ->leftJoin('itemcodes as ic', 'ic.id', '=', 'ttic.item_category_id')
                                                        ->where('ttic.test_threshold_id', $q->id)
                                                        ->where('ttic.is_deleted', 0)
                                                        ->where('ic.is_deleted', 0)
                                                        ->orderBy('ic.item_category', 'asc')
                                                        ->get();
                                                    ?>
                                                    @php
                                                        $count = 0;
                                                    @endphp

                                                    @if ($test_threshold_item_categories->count() > 0)
                                                        @foreach ($test_threshold_item_categories as $ttic)
                                                            @if ($count < 4)
                                                                <span class="minor-tag" data-toggle="tooltip"
                                                                    data-trigger="hover" data-placement="top" title=""
                                                                    data-original-title="Item Category">
                                                                    {{ $ttic->item_category }}
                                                                </span>
                                                            @endif
                                                            @php $count++; @endphp
                                                        @endforeach

                                                        @if ($test_threshold_item_categories->count() > 4)
                                                            <span class="minor-tag">...</span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0"
                                                            data-id="{{ $q->id }}" data-status="{{ $q->status }}"
                                                            href="#" role="button" data-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <img src="{{ asset('public/img/cf-menu-icons/3dots.png') }}"
                                                                width="9">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            @if ($q->status == 1)
                                                                <a class="dropdown-item dropdown-item-3dot btnEnd"
                                                                    href="#" data="{{ $q->status }}"
                                                                    data-id="{{ @$q->id }}"><img
                                                                        src="public/img/cf-menu-icons/3dot-deactivate.png">
                                                                    Deactivate</a>
                                                            @else
                                                                <a class="dropdown-item dropdown-item-3dot btnEnd"
                                                                    href="#" data="{{ $q->status }}"
                                                                    data-id="{{ @$q->id }}"><img
                                                                        src="public/img/cf-menu-icons/3dot-activate.png">
                                                                    Reactivate</a>
                                                            @endif
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon"
                                                                data-item-id="{{ @$q->id }}" href="#"><img
                                                                    src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon"
                                                                data-item-id="{{ @$q->id }}" href="#"><img
                                                                    src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-8 pr-0" id="showData">

                    </div>

                </div>
            </div>

            <form class="mb-0 pb-0" action="{{ url('insert-test-threshold') }}" id="form-insert-test-threshold"
                method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="insert-test-threshold-modal" tabindex="-1" role="dialog"
                    data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                                <h1 class="modal-header-insert mb-0">
                                    NEW<br>
                                    <span class="modal-subheader">TEST THRESHOLD</span>
                                </h1>
                                <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="block small-arrow modal-static-ht block-transparent mb-0">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="testNameSelect">Test
                                        Name</label>
                                    <div class="col-sm-8">
                                        <select class="modal-input shadow-non e form-control select2" name="testNameSelect"
                                            id="testNameSelect">

                                        </select>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Test Type</label>
                                    <div class="col-sm-4 selectedTestType-div" style="display: none;">
                                        <input type="text" class="form-control text-center px-1 green-box"
                                            id="selectedTestType" readonly>
                                    </div>
                                    <div class="col-sm-2 perf-str-input pl-0" style="display: none;">
                                        <input type="text" class="form-control text-center px-1 green-box"
                                            id="selectedCriteria" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Criteria" readonly>
                                    </div>
                                    {{-- <div class="col-sm-2 perf-str-input pl-0" style="display: none;">
                                        <input type="text" class="form-control text-center px-1 green-box"
                                            id="selectedStandard" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Standard" readonly>
                                    </div> --}}
                                </div>
                                <hr class="modal-hr">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Item Category</label>
                                    <div class="col-sm-8">
                                        <select class="modal-input shadow-non e form-control item_cat_select select2"
                                            id="ItemCategorySelect">

                                        </select>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2 test_type_div">
                                    <label class="col-sm-4 d-flex align-items-center modal-label justify-content-between">
                                        <span class="label-text">Min/Max</span>
                                        <span class="fas fa-info-circle ml-2 info-circle"></span>
                                        <div class="tooltip-box">
                                            <strong>Standard (YFS/YFGS)</strong>
                                            <br>
                                            YFS is the YKK internal Standard.
                                            <br>
                                            <br>
                                            YFGS is the Global Standard.
                                            <br>
                                            <br>
                                            Both values are required, however,
                                            either the YFS or the YFGS is used
                                            for QC testing
                                            <br>
                                            <br>
                                            Next to Test Type it will delineate
                                            which standard is used for this
                                            test.
                                        </div>
                                    </label>
                                    <div class="col-sm-3 pr-0 dimension-input">
                                        <input type="text" name="min" id="min" placeholder="Min"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <div class="col-sm-3 pr-0 dimension-input">
                                        <input type="text" name="max" id="max" placeholder="Max"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0 perf-str-input">
                                        <input type="number" step="any" name="YFS" id="YFS"
                                            placeholder="YFS" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0 perf-str-input">
                                        <input type="number" step="any" name="YFGS" id="YFGS"
                                            placeholder="YFGS" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0 perf-weight-input">
                                        <input type="text" name="absorption" id="absorption"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2 align-content-center">
                                        <input type="text" name="uom" id="uom"
                                            class="green-box form-control text-center px-1  " data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2 test_type_div_2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label justify-content-between">
                                        <span class="label-text">Average</span>
                                        <span class="fas fa-info-circle ml-2 info-circle"></span>
                                        <div class="tooltip-box">
                                            <strong>Safety Threshold</strong>
                                            <br>
                                            Any test value below the Standard YFS/YFGS will result in <img
                                                src="public/img/cf-menu-icons/redxcircle.png" width="18">
                                            <br>
                                            <br>
                                            Any test value above the Standard YFS/YFGS + Safety Threshold value will result in
                                            <img src="public/img/cf-menu-icons/greencheck.png" width="18">
                                            <br>
                                            <br>
                                            Any test values within the range of the Standard YFS/YFGS plus the Safety Threshold
                                            will result in <img src="public/img/cf-menu-icons/icon-warning.png"
                                                width="18">
                                        </div>
                                    </label>
                                    <div class="col-sm-3 pr-0 dimension-input">
                                        <input type="number" name="avg" id="avg" placeholder="Avg."  
                                            class="modal-input shadow-non e form-control text-center">
                                    </div>
                                    <div class="col-sm-3 pr-0 perf-str-input">
                                        <input type="number" name="safety_threshold" id="safety_threshold" placeholder="ST"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2" style="padding-top: 5px;">
                                        <input type="text" name="uom" id="uom2"
                                            class="green-box form-control text-center px-1" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row form-group mt-3">
                                    <div class="col-sm-7">
                                        <div class="d-flex align-items-center">
                                            <a type="button" class="mr-2 clear_filter_itemcode" id="clear_filter_itemcode"
                                                style="display: none;">
                                                <img src="{{ asset('public/img/cf-menu-icons/detail-line-remove.png') }}"
                                                    width="15">
                                            </a>
                                            <input type="text" class="filter-input-modal mb-1" id="itemSearch"
                                                placeholder="" autocomplete="off">
                                            <a type="button" class="ml-2 filter_itemcode" id="filter_itemcode"
                                                style="display: none;">
                                                <img src="{{ asset('public/img/cf-menu-icons/menu-icon-right.png') }}"
                                                    width="19">
                                            </a>
                                        </div>
                                        <span class="modal-subheader modal-subheader-text">SEARCH THRESHOLDS</span>
                                    </div>
                                    <div class="col-sm-5 text-right">
                                        <button type="button" class="btn btn-yes" id="addItem">Add</button>
                                    </div>
                                </div>

                                <div id="selectedItems" class="small-arrow selected-items-container ro w mt-3 mr-3"
                                    style="max-width: 100%; height: 220px; max-height: 220px; overflow-y: auto;"></div>

                                <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                                    <button type="submit" id="saveItemCategory" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Create
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="mb-0 pb-0" action="{{ url('update-test-threshold') }}" id="form-edit-test-threshold"
                method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="category_id" id="edit_test_threshold_id">
                <div class="modal fade" id="edit-test-threshold-modal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                                <h1 class="modal-header-insert mb-0">
                                    <span id="edit_item_category_heading">EDIT</span><br>
                                    <span class="modal-subheader">TEST THRESHOLD</span>
                                </h1>
                                <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="block small-arrow modal-static-ht block-transparent mb-0">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label"
                                        for="edit_testNameSelect">Test Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="modal-input shadow-non e form-control"
                                            id="edit_testNameSelect" name="testNameSelect" readonly>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Test Type</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control text-center px-1"
                                            id="edit_selectedTestType" readonly>
                                    </div>
                                    <div class="col-sm-2 edit-perf-str-input pl-0" style="display: none;">
                                        <input type="text" class="form-control text-center px-1 green-box"
                                            id="editselectedCriteria" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Criteria" readonly>
                                    </div>
                                 <!--    <div class="col-sm-2 edit-perf-str-input pl-0" style="display: none;">
                                        <input type="text" class="form-control text-center px-1 green-box"
                                            id="editselectedStandard" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Standard" readonly>
                                    </div> -->
                                </div>
                                <hr class="modal-hr">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Item Category</label>
                                    <div class="col-sm-8">
                                        <select class="modal-input shadow-non e form-control item_cat_select select2"
                                            id="editItemSelect">

                                        </select>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2 edit-test_type_div">
                                    <label class="col-sm-4 d-flex align-items-center modal-label justify-content-between">
                                        <span class="label-text">Min/Max</span>
                                        <span class="fas fa-info-circle ml-2 info-circle"></span>
                                        <div class="tooltip-box">
                                            <strong>Standard (YFS/YFGS)</strong>
                                            <br>
                                            YFS is the YKK internal Standard.
                                            <br>
                                            <br>
                                            YFGS is the Global Standard.
                                            <br>
                                            <br>
                                            Both values are required, however,
                                            either the YFS or the YFGS is used
                                            for QC testing
                                            <br>
                                            <br>
                                            Next to Test Type it will delineate
                                            which standard is used for this
                                            test.
                                        </div>
                                    </label>
                                    <div class="col-sm-3 pr-0 edit-dimension-input">
                                        <input type="text" name="min" id="edit-min" placeholder="Min"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <div class="col-sm-3 pr-0 edit-dimension-input">
                                        <input type="text" name="max" id="edit-max" placeholder="Max"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0 edit-perf-str-input">
                                        <input type="number" step="any" name="YFS" id="edit-YFS"
                                            placeholder="YFS" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0 edit-perf-str-input">
                                        <input type="number" step="any" name="YFGS" id="edit-YFGS"
                                            placeholder="YFGS" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0 edit-perf-weight-input">
                                        <input type="text" name="absorption" id="edit-absorption"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2 align-content-center">
                                        <input type="text" name="uom"
                                            class="green-box form-control text-center px-1 edit-uom" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2 edit-test_type_div_2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label justify-content-between">
                                        <span class="label-text">Average</span>
                                        <span class="fas fa-info-circle ml-2 info-circle"></span>
                                        <div class="tooltip-box">
                                            <strong>Safety Threshold</strong>
                                            <br>
                                            Any test value below the Standard YFS/YFGS will result in <img
                                                src="public/img/cf-menu-icons/redxcircle.png" width="18">
                                            <br>
                                            <br>
                                            Any test value above the Standard YFS/YFGS + Safety Threshold value will result in
                                            <img src="public/img/cf-menu-icons/greencheck.png" width="18">
                                            <br>
                                            <br>
                                            Any test values within the range of the Standard YFS/YFGS plus the Safety Threshold
                                            will result in <img src="public/img/cf-menu-icons/icon-warning.png"
                                                width="18">
                                        </div>
                                    </label>
                                    <div class="col-sm-3 pr-0 edit-dimension-input">
                                        <input type="text" name="avg" id="edit-avg" placeholder="Avg."  
                                            class="modal-input shadow-non e form-control text-center">
                                    </div>
                                    <div class="col-sm-3 pr-0 edit-perf-str-input">
                                        <input type="number" name="safety_threshold" id="edit-safety_threshold"
                                            placeholder="ST" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2" style="padding-top: 5px;">
                                        <input type="text" name="uom"
                                            class="green-box form-control text-center px-1 edit-uom" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row form-group mt-3">
                                    <div class="col-sm-7">
                                        <div class="d-flex align-items-center">
                                            <a type="button" class="mr-2 edit_clear_filter_itemcode"
                                                id="edit_clear_filter_itemcode" style="display: none;">
                                                <img src="{{ asset('public/img/cf-menu-icons/detail-line-remove.png') }}"
                                                    width="15">
                                            </a>
                                            <input type="text" class="filter-input-modal mb-1" id="editItemSearch"
                                                placeholder="" autocomplete="off">
                                            <a type="button" class="ml-2 edit_filter_itemcode" id="edit_filter_itemcode"
                                                style="display: none;">
                                                <img src="{{ asset('public/img/cf-menu-icons/menu-icon-right.png') }}"
                                                    width="19">
                                            </a>
                                        </div>
                                        <span class="modal-subheader modal-subheader-text">SEARCH THRESHOLDS</span>
                                    </div>
                                    <div class="col-sm-5 text-right">
                                        <button type="button" class="btn btn-yes" id="edit-addItem">Add</button>
                                    </div>
                                </div>

                                <div id="editSelectedItems" class="small-arrow selected-items-container ro w mt-3 mr-3"
                                    style="max-width: 100%; height: 220px; max-height: 220px; overflow-y: auto;"></div>

                                <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                                    <button type="submit" id="saveItemCategory" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Update
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="mb-0 pb-0" action="{{ url('end-test-threshold') }}" id="form-end-test-threshold" method="post">
                @csrf
                <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Revoke</span> Test
                                        Threshold</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <!-- <textarea class="form-control" rows="5" required="" name="reason" id="reason"></textarea> -->
                                            <p class="fw-300">Are you sure you wish to <span
                                                    class="revokeText text-lowercase"></span> this Test Threshold?</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-center pt-3"
                                    style="padding-left: 9mm;padding-right: 9mm">
                                    <button type="submit" class="btn mx-2 btn-yes  ">Yes</button>
                                    <button type="button" class="btn mx-2 btn-no" data-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="mb-0 pb-0" action="{{ url('delete-test-threshold') }}" method="post">
                @csrf
                <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Delete Test Threshold
                                            <div class="block-options">
                                            </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" id="delete_id" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <p class="fw-300">Are you sure you wish to delete this Test Threshold?</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-center pt-3"
                                    style="padding-left: 9mm;padding-right: 9mm">
                                    <button type="submit" class="btn mx-2 btn-yes  ">Yes</button>
                                    <button type="button" class="btn mx-2 btn-no" data-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="mb-0 pb-0" action="{{ url('delete-attachment-test-threshold') }}" method="post">
                @csrf
                <div class="modal fade" id="DelAttachmentModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Delete Attachment
                                            <div class="block-options">
                                            </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" id="del_test_threshold_id" name="test_threshold_id">
                                    <input type="hidden" id="del_attachment_id" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <p class="fw-300">Are you sure you wish to delete this attachment?</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-center pt-3"
                                    style="padding-left: 9mm;padding-right: 9mm">
                                    <button type="submit" class="btn mx-2 btn-yes  ">Yes</button>
                                    <button type="button" class="btn mx-2 btn-no" data-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form class="mb-0 pb-0" action="{{ url('delete-comment-test-threshold') }}" method="post">
                @csrf
                <div class="modal fade" id="DelCommentModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="">Delete Comment
                                            <div class="block-options">
                                            </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" id="del_comment_test_threshold_id" name="test_threshold_id">
                                    <input type="hidden" id="del_comment_id" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <p class="fw-300">Are you sure you wish to delete this comment?</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-center pt-3"
                                    style="padding-left: 9mm;padding-right: 9mm">
                                    <button type="submit" class="btn mx-2 btn-yes  ">Yes</button>
                                    <button type="button" class="btn mx-2 btn-no" data-dismiss="modal">No</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form action="{{ url('insert-comment-test-threshold') }}" method="post">
                @csrf
                <input type="hidden" id="comment_id" name="id" value="{{ $GETID }}">
                <div class="modal fade" id="CommentModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-large"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="block block-transparent mb-0">
                                <div class="block-header d-flex justify-content-between align-items-center">
                                    <span class="b e section-header">Add Comment</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="block-content pt-0 row">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" rows="5" required name="comment"></textarea>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-right" style="padding-left: 9mm;">
                                    <button type="submit" class="btn btn-action" id="CommentSave">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Save
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form action="{{ url('update-comment-test-threshold') }}" method="post">
                @csrf
                <input type="hidden" id="edit_comment_id" name="id">
                <input type="hidden" id="edit_comment_test_threshold_id" name="test_threshold_id">
                <div class="modal fade" id="EditCommentModal" tabindex="-1" role="dialog"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="block block-transparent mb-0">
                                <div class="block-header d-flex justify-content-between align-items-center">
                                    <span class="b e section-header">Edit Comment</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="block-content pt-0 row">
                                    <div class="col-sm-12">
                                        <textarea class="form-control" rows="5" required id="comment_text" name="comment"></textarea>
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-right" style="padding-left: 9mm;">
                                    <button type="submit" class="btn btn-action" id="EditCommentSave">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Save
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <form id="insert-attachment-test-threshold-form" action="{{ url('insert-attachment-test-threshold') }}"
                method="post">
                @csrf
                <input type="hidden" id="attachment_id" name="id" value="{{ $GETID }}">
                <input type="hidden" name="attachment_array" id="attachment_array">
                <div class="modal fade" id="AttachmentModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Add Attachment</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="block-content pt-0 row">
                                    <div class="col-sm-12    p      ">
                                        <input type="file" class="  attachment" multiple="" style=""
                                            id="attachment" name="attachment" placeholder="">
                                    </div>
                                </div>
                                <div class="block-content block-content-full text-right" style="padding-left: 9mm;">
                                    <button type="button" class="btn btn-action" id="AttachmentSave">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Save
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>





            <form action="{{ url('/import-test-thresholds') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Import Test Thresholds</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="block-content pt-0 row form-group mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Select CSV File</label>
                                    <div class="col-sm-7">
                                        <input type="file" name="file" class="modal-input form-control" required
                                            accept=".csv,.xlsx">
                                    </div>
                                </div>
                                <div class="block-content row block-content-full">
                                    <div class="col-sm-7 d-flex align-items-center">
                                        <div>
                                            <a href="#" data-url="{{ asset('public/Test-Threshold-Sample.xlsx') }}"
                                                id="download-template" class="fw-400 text-dark">
                                                <img src="{{ asset('public/img/cf-menu-icons/download-icon.png') }}"
                                                    width="25" class="mr-1" alt="Download Icon">
                                                Download Template
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-5 text-right">
                                        <button type="submit" class="btn btn-action">
                                            <span class="btn-action-gear d-none mr-2"><img
                                                    src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                            <span class="btn-action-import">Import</span>
                                            <span class="btn-action-gear d-none ml-2"><img
                                                    src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <form action="" id="EditModuleForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="EditAccessModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Edit Item Category Threshold</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Item Category</label>

                                    <div class="col-sm-7">
                                        <input type="text" id="edit_module_name" readonly
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">Min/Max</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="min" id="edit-pen-min" placeholder="Min"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="max" id="edit-pen-max" placeholder="Max"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2 align-content-center">
                                        <input type="text" name="uom" id="edit-pen-uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">Average</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="avg" id="edit-pen-avg" placeholder="Avg."
                                              class="modal-input shadow-non e form-control text-center">
                                    </div>
                                    <div class="col-sm-2 align-content-center">
                                        <input type="text" name="uom" id="edit-pen-uom2"
                                            class="green-box edit-pen-uom form-control text-center px-1"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-perf-str-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">YFS/YFGS</label>
                                    <div class="col-sm-3 pr-0">
                                        <input type="number" step="any" name="YFS" id="edit-pen-YFS"
                                            placeholder="YFS" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-3 pr-0">
                                        <input type="number" step="any" name="YFGS" id="edit-pen-YFGS"
                                            placeholder="YFGS" class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2 align-content-center">
                                        <input type="text" name="uom" id="edit-pen-uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-perf-str-min-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">Standard</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="number" name="safety_threshold" id="edit-pen-st" placeholder="ST"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2" style="padding-top: 5px;">
                                        <input type="text" name="uom" id="edit-pen-uom2"
                                            class="green-box edit-pen-uom form-control text-center px-1"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-perf-weight-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">Max % Absorption</label>
                                    <div class="col-sm-3 pr-0">
                                        <input type="text" name="absorption" id="edit-pen-absorption"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                    <div class="col-sm-2 align-content-center">
                                        <input type="text" name="uom" id="edit-pen-uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Test UOM" readonly>
                                    </div>
                                </div>
                                <div class="block-content row block-content-full">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-yes" id="saveEditedAccess">Save</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

        </main>
        <!-- END Main Container -->
    @endsection('content')



    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>



    <script type="text/javascript">
        $(document).ready(function() {

            function getUrlParameter(name) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(name);
            }

            let selectedCategory = getUrlParameter("filter_item_cat");

            if (selectedCategory) {
                $.ajax({
                    url: "{{ url('/fetch-item-categories') }}",
                    data: {
                        query: selectedCategory
                    },
                    dataType: 'json'
                }).then(function(data) {
                    let match = data.find(item => item.text === selectedCategory);
                    if (match) {
                        let option = new Option(match.text, match.text, true, true);
                        $('#filter_item_cat').append(option).trigger('change');
                    }
                });
            }

            setTimeout(() => {
                $('#filter_item_cat').select2({
                    placeholder: "Select Item Category",
                    allowClear: true,
                    ajax: {
                        url: "{{ url('/fetch-item-categories') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                query: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.text,
                                        text: item.text,
                                        desc: item.desc
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        return data.text;
                    },
                    templateSelection: function(data, container) {
                        // add data attributes to the real <option>
                        if (data.id) {
                            const optionEl = $('#filter_item_cat').find("option[value='" +
                                data
                                .text + "']");
                        }
                        return data.text || data.workorder_no;
                    }
                });
            }, 200);

            $('.select2').select2({
                placeholder: "Select Test Name",
                allowClear: true,
                minimumResultsForSearch: 0 // Optional: show search always
            });
            $('.item_cat_select').select2({
                placeholder: "Select Item Category",
                allowClear: true,
                minimumResultsForSearch: 0 // Optional: show search always
            });

            $('.clear-link').hover(
                function() {
                    // Hover in: change to hover image
                    $(this).find('img').attr('src', $(this).find('img').data('hover'));
                },
                function() {
                    // Hover out: change back to default image
                    $(this).find('img').attr('src', $(this).find('img').data('default'));
                }
            );

            function showCustomNotification(message, leftPosition = '20px') {
                $.notify({
                    message: message
                }, {
                    type: 'info', // 'info', 'success', 'warning', 'danger'
                    placement: {
                        from: 'bottom',
                        align: 'left'
                    },
                    offset: {
                        left: leftPosition // Dynamic left position
                    },
                    delay: 1000,
                    animate: {
                        enter: 'animated fadeIn',
                        exit: 'animated fadeOut'
                    },
                    template: `
            <div data-notify="container" class="alert alert-{0} col-11 col-sm-4" role="alert" style="position: fixed; bottom: 20px; left: ${leftPosition} !important; z-index: 1033;">
                <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="position: absolute; right: 10px; top: 5px; z-index: 1035;">×</button>
                <span data-notify="message">{2}</span>
            </div>
        `
                });
            }

            function showCustomWarningNotification(message, leftPosition = '20px') {
                $.notify({
                    message: message
                }, {
                    type: 'info',
                    placement: {
                        from: 'bottom',
                        align: 'left'
                    },
                    offset: {
                        left: leftPosition
                    },
                    delay: 1000,
                    animate: {
                        enter: 'animated fadeIn',
                        exit: 'animated fadeOut'
                    },
                    template: `
            <div data-notify="container" class="alert alert-{0} col-11 col-sm-4" role="alert" style="position: fixed; bottom: 20px; left: ${leftPosition} !important; z-index: 1033;">
                <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="position: absolute; right: 10px; top: 5px; z-index: 1035;">×</button>
                <span data-notify="message">{2}</span>
            </div>
        `
                });
            }

            // Correct way to use the asset helper in Blade with JavaScript:
            var warningIcon = "{{ asset('public/img/warning-yellow.png') }}";


            // Function to load category data
            function loadCategoryData(categoryId) {
                $.ajax({
                    url: '{{ url('get-test-threshold') }}',
                    method: 'POST',
                    data: {
                        id: categoryId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#edit_test_threshold_id').val(response.data.id);
                            $('.delete-edit-category').attr('data-item-id', response.data.id);
                            $('#edit_item_category_heading').text(response.data.test_name);
                            $('#edit_testNameSelect').val(response.data.test_name);
                            $('#edit_selectedTestType').val(response.data.test_type);
                            $('#editselectedCriteria').val(response.data.criteria);
                            $('#editselectedStandard').val(response.data.standard);
                            $('.edit-uom').val(response.data.uom);

                            $('.edit-test_type_div').show();
                            $('.edit-test_type_div_2').show();
                            $('.info-circle').hide();

                            if (response.data.test_type == 'Dimension') {
                                $('.edit-test_type_div label .label-text').text('Min/Max');
                                $('.edit-test_type_div_2 label .label-text').text('Average');
                                $('.edit-dimension-input').show();
                                $('.edit-perf-str-input').hide();
                                $('.edit-perf-weight-input').hide();
                            }
                            if (response.data.test_type == 'Perf-Str' && response.data.criteria ==
                                'Min') {
                                $('.edit-test_type_div label .label-text').text('YFS/YFGS');
                                $('.edit-test_type_div_2 label .label-text').text('Safety Threshold');
                                $('.info-circle').show();
                                $('.edit-perf-str-input').show();
                                $('.edit-dimension-input').hide();
                                $('.edit-perf-weight-input').hide();
                            }
                            if (response.data.test_type == 'Perf-Str' && response.data.criteria ==
                                'Max') {
                                $('.edit-test_type_div label .label-text').text('YFS/YFGS');
                                $('.info-circle').show();
                                $('.edit-test_type_div_2').hide();
                                $('.edit-perf-str-input').show();
                                $('.edit-dimension-input').hide();
                                $('.edit-perf-weight-input').hide();
                            }
                            if (response.data.test_type == 'Perf-Weight') {
                                $('.edit-test_type_div_2').hide();
                                $('.edit-test_type_div label .label-text').text('Max % Absorption');
                                $('.edit-perf-weight-input').show();
                                $('.edit-perf-str-input').hide();
                                $('.edit-dimension-input').hide();
                            }

                            // Clear previous items
                            $('#editSelectedItems').empty();

                            // Add existing itemcodes
                            if (response.item_categories && response.item_categories.length > 0) {
                                response.item_categories.forEach(function(item) {


                                    let typeSpecificHTML = '';
                                    let typeSpecificEditBtn = '';

                                    if (response.data.test_type == 'Dimension') {
                                        typeSpecificHTML = `
        <div style="width: 65%; ">
            <span class="modal-subheader">min <span class="minor-tag">${item.min}</span></span>
            <span class="modal-subheader">avg <span class="minor-tag">${item.avg}</span></span>
            <span class="modal-subheader">max <span class="minor-tag">${item.max}</span></span>
        </div>
    `;
                                        typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${item.item_category}" data-type="Dimension" data-min="${item.min}" data-avg="${item.avg}" data-max="${item.max}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                                    } else if (response.data.test_type == 'Perf-Str' && response
                                        .data.criteria === 'Min') {
                                        typeSpecificHTML = `
        <div style="width: 65%; ">
            <span class="modal-subheader">YFS <span class="minor-tag">${item.YFS}</span></span>
            <span class="modal-subheader">YFGS <span class="minor-tag">${item.YFGS}</span></span>
            <span class="modal-subheader">ST <span class="minor-tag">${item.safety_threshold}</span></span>
        </div>
    `;
                                        typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${item.item_category}" data-type="Perf-Str-Min" data-yfs="${item.YFS}" data-yfgs="${item.YFGS}" data-st="${item.safety_threshold}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                                    } else if (response.data.test_type == 'Perf-Str' && response
                                        .data.criteria === 'Max') {
                                        typeSpecificHTML = `
        <div style="width: 65%; ">
            <span class="modal-subheader">YFS <span class="minor-tag">${item.YFS}</span></span>
            <span class="modal-subheader">YFGS <span class="minor-tag">${item.YFGS}</span></span>
        </div>
    `;
                                        typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${item.item_category}" data-type="Perf-Str-Max" data-yfs="${item.YFS}" data-yfgs="${item.YFGS}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                                    } else if (response.data.test_type == 'Perf-Weight') {
                                        typeSpecificHTML = `
        <div style="width: 65%; ">
            <span class="modal-subheader">max <span class="minor-tag">${parseFloat(item.absorption).toFixed(2)}%</span></span>
        </div>
    `;
                                        typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${item.item_category}" data-type="Perf-Weight" data-absorption="${parseFloat(item.absorption).toFixed(2)}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                                    }


                                    // Append item display div
                                    $('#editSelectedItems').append(`    
    <div class="col-sm-11 mx-auto mb-2 selected-item pl-0" data-id="${item.item_category_id}" data-code="${item.item_category}">
        <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
            <span class="selected-itemcode">${item.item_category}</span>
            ${typeSpecificHTML}
    <div class="d-flex align-items-center">
                            ${typeSpecificEditBtn}
                            <button type="button" class="close close-cross remove-item" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
        </div>
        <input type="hidden" name="item_category_id[]" value="${item.item_category_id}">
        <input type="hidden" name="min_values[]" value="${item.min}">
        <input type="hidden" name="max_values[]" value="${item.max}">
        <input type="hidden" name="avg_values[]" value="${item.avg}">
        <input type="hidden" name="yfs_values[]" value="${item.YFS}">
        <input type="hidden" name="yfgs_values[]" value="${item.YFGS}">
        <input type="hidden" name="safety_threshold_values[]" value="${item.safety_threshold}">
        <input type="hidden" name="absorption_values[]" value="${item.absorption}">            
        <input type="hidden" name="absorption_valuess[]" value="${response.data.test_type}">            
    </div>
`);
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }

            $(document).on('click', '.edit-icon', function() {
                const categoryId = $(this).attr('data-item-id');
                loadCategoryData(categoryId);
                $('#edit-test-threshold-modal').modal('show');
            });

            $(document).on('click', '.edit-dot-icon', function() {
                const categoryId = $(this).attr('data-item-id');
                loadCategoryData(categoryId);
                $('#edit-test-threshold-modal').modal('show');
            });

            // Add selected item to the list on button click or Enter key
            $('#edit-addItem').on('click', edit_addSelectedItem);

            // Refactored add item function
            function edit_addSelectedItem() {
                var selectedTestType = $('#edit_selectedTestType').val().trim();
                var selectedCriteria = $('#editselectedCriteria').val().trim();
                var minVal = $('#edit-min').val().trim();
                var maxVal = $('#edit-max').val().trim();

                var old_avg = $('#edit-avg').val().trim();
                let min = parseFloat(minVal);
                let max = parseFloat(maxVal);

                if (!isNaN(min)) {
                    min = parseFloat(min.toFixed(3));
                    $('#edit-min').val(min);
                }

                if (!isNaN(max)) {
                    max = parseFloat(max.toFixed(3));
                    $('#edit-max').val(max);
                    }
                    if(old_avg==''){
                var avg = (!isNaN(min) && !isNaN(max)) ? parseFloat(((min + max) / 2).toFixed(3)) : '';
            }else{
                avg=old_avg;
            }
                var YFS = $('#edit-YFS').val().trim();
                var YFGS = $('#edit-YFGS').val().trim();
                var safety_threshold = $('#edit-safety_threshold').val().trim();

                var absorptionVal = $('#edit-absorption').val().trim();
                let absorption = parseFloat(absorptionVal);

                var itemCategory = $('#editItemSelect').val().trim();

                if (itemCategory === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select item category first.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Dimension' && (minVal === '' || isNaN(min))) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Min value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Dimension' && (maxVal === '' || isNaN(max))) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Max value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Dimension' && !isNaN(min) && !isNaN(max) && min > max) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Min value cannot be greater than Max value.`,
                        "500px"
                    );
                    return;
                }

                if (selectedTestType === 'Perf-Str' && YFS === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select YFS value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Perf-Str' && YFGS === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select YFGS value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Perf-Str' && selectedCriteria === 'Min' && safety_threshold === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Safety Threshold value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Perf-Weight' && (absorptionVal === '' || isNaN(absorption))) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Max % Absorption value.`,
                        "500px");
                    return;
                } else if (!isNaN(absorption)) {
                    $('[name="absorption"]').val(absorption.toFixed(2));
                }

                var selectedOption = $('#editItemSelect').find(':selected');
                var code_val = selectedOption.val();
                var code = selectedOption.text();
                var desc = selectedOption.data('description') || '';
                var id = selectedOption.data('id');

                if (code && id) {
                    // Check if already added
                    const exists = $('#editSelectedItems').find(`[data-id='${id}']`).length > 0;

                    if (!exists) {

                        let typeSpecificHTML = '';
                        let typeSpecificEditBtn = '';

                        if (selectedTestType == 'Dimension') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">min <span class="minor-tag">${min}</span></span>
            <span class="modal-subheader">avg <span class="minor-tag">${avg}</span></span>
            <span class="modal-subheader">max <span class="minor-tag">${max}</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Dimension" data-min="${min}" data-avg="${avg}" data-max="${max}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        } else if (selectedTestType == 'Perf-Str' && selectedCriteria === 'Min') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">YFS <span class="minor-tag">${YFS}</span></span>
            <span class="modal-subheader">YFGS <span class="minor-tag">${YFGS}</span></span>
            <span class="modal-subheader">ST <span class="minor-tag">${safety_threshold}</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Perf-Str-Min" data-yfs="${YFS}" data-yfgs="${YFGS}" data-st="${safety_threshold}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        } else if (selectedTestType == 'Perf-Str' && selectedCriteria === 'Max') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">YFS <span class="minor-tag">${YFS}</span></span>
            <span class="modal-subheader">YFGS <span class="minor-tag">${YFGS}</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Perf-Str-Max" data-yfs="${YFS}" data-yfgs="${YFGS}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        } else if (selectedTestType == 'Perf-Weight') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">max <span class="minor-tag">${absorption.toFixed(2)}%</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Perf-Weight" data-absorption="${absorption.toFixed(2)}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        }

                        // Append item display div
                        $('#editSelectedItems').append(`
    <div class="col-sm-11 mx-auto mb-2 selected-item pl-0" data-id="${id}" data-code="${code}">
        <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
            <span class="selected-itemcode">${code}</span>
            ${typeSpecificHTML}
    <div class="d-flex align-items-center">
                            ${typeSpecificEditBtn}
                            <button type="button" class="close close-cross remove-item" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
        </div>
        <input type="hidden" name="item_category_id[]" value="${id}">
        <input type="hidden" name="min_values[]" value="${min}">
        <input type="hidden" name="max_values[]" value="${max}">
        <input type="hidden" name="avg_values[]" value="${avg}">
        <input type="hidden" name="yfs_values[]" value="${YFS}">
        <input type="hidden" name="yfgs_values[]" value="${YFGS}">
        <input type="hidden" name="safety_threshold_values[]" value="${safety_threshold}">
        <input type="hidden" name="absorption_values[]" value="${absorption.toFixed(2)}">
    </div>
`);


                        // Clear and reset Select2 field
                        $('#editItemSelect').val(null).trigger('change');

                        // Clear the input field
                        $('#edit-min, #edit-max, #edit-YFS, #edit-YFGS, #edit-safety_threshold, #edit-absorption')
                            .val('');

                        // Focus on the Select2 field
                        $('#editItemSelect').select2('open');


                        showCustomNotification("Item Category added successfully", "500px");

                    } else {

                        var message =
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category already added.`;
                        showCustomWarningNotification(message, "500px");

                    }
                } else {

                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select a valid Item Category.`;
                    showCustomWarningNotification(message, "500px");

                }
            }

            let lastRemovedItem = null; // Store last removed item for undo

            $('#editSelectedItems').on('click', '.remove-item', function() {
                const $item = $(this).closest('.selected-item');
                lastRemovedItem = {
                    html: $item.prop('outerHTML'),
                    index: $item.index()
                };
                $item.remove();

                var message =
                    `Item Category removed <a href="javascript:;" class="btn-notify undo-remove ml-4">Undo</a>`;
                showCustomWarningNotification(message, "500px");

            });

            // Handle undo click from notification
            $(document).on('click', '.undo-remove', function() {
                if (lastRemovedItem) {
                    const $itemsContainer = $('#editSelectedItems');
                    const itemCount = $itemsContainer.children().length;

                    // Restore item at original position
                    if (lastRemovedItem.index >= itemCount) {
                        $itemsContainer.append(lastRemovedItem.html);
                    } else {
                        $itemsContainer.children().eq(lastRemovedItem.index).before(lastRemovedItem.html);
                    }

                    lastRemovedItem = null;
                }
            });

            // Handle form submission for edit
            $('#form-edit-test-threshold').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const $button = form.find('.btn-action');
                const formData = new FormData(this);

                // Validate form data
                const editselectedItems = $('#editSelectedItems .selected-item').length;
                if (editselectedItems === 0) {
                    showNotification('warning', 'Please select at least one item');
                    resetBtnAction($button);
                    return false;
                }

                // Disable button and show loading indicator
                $button.prop('disabled', true);
                $button.find('.btn-action-gear').removeClass('d-none');
                $button.find('.btn-action-gear img').addClass('rotating');

                $.ajax({
                    url: form.attr('action'),
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        showNotification('success', response.message ||
                            'Item category updated successfully');
                        resetFormAndReload(form, '#edit-test-threshold-modal',
                            '#editSelectedItems');
                    },
                    error: function(xhr) {
                        let message = 'An error occurred. Please try again.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                message = Object.values(xhr.responseJSON.errors).join('<br>');
                            }
                        }

                        showNotification('error', message);
                        resetFormState(form, $button, '#editSelectedItems');
                    }
                });
            });

            // Handle Enter key press in the edit modal
            $('#edit-test-threshold-modal').on('keypress', function(e) {
                // Check if Enter key was pressed (key code 13)
                if (e.which === 13) {
                    // Check if there are selected items
                    const hasSelectedItems = $('#editSelectedItems .selected-item').length > 0;

                    // Only submit if there are selected items and we're not in a textarea or input[type="text"]
                    const $target = $(e.target);
                    if (hasSelectedItems && !$target.is('textarea') && !$target.is('input[type="text"]')) {
                        e.preventDefault();
                        $('#form-edit-test-threshold').submit();
                    } else {
                        let message = 'Item category failed validation';
                        showNotification('error', message);
                    }
                }
            });

            // Helper functions
            function showNotification(type, message) {
                let icon = '';
                if (type === 'warning') {
                    icon = '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> ';
                } else if (type === 'error') {
                    icon = '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> ';
                }

                var ErrorMessage = icon + message;
                showCustomWarningNotification(ErrorMessage, "500px");
            }

            function resetFormAndReload(form, modalId, selectedItemsId) {
                $(modalId).modal('hide');
                form[0].reset();
                $(selectedItemsId).empty();
                setTimeout(() => location.reload(), 1200);
            }

            function resetFormState(form, $button, selectedItemsId) {
                $button.prop('disabled', false);
                $button.find('.btn-action-gear img').removeClass('rotating');
                $button.find('.btn-action-gear').addClass('d-none');
                $(selectedItemsId).empty();
            }

        });



        $(function() {

            function showCustomNotification(message, leftPosition = '20px') {
                $.notify({
                    message: message
                }, {
                    type: 'info', // 'info', 'success', 'warning', 'danger'
                    placement: {
                        from: 'bottom',
                        align: 'left'
                    },
                    offset: {
                        left: leftPosition // Dynamic left position
                    },
                    delay: 1000,
                    animate: {
                        enter: 'animated fadeIn',
                        exit: 'animated fadeOut'
                    },
                    template: `
            <div data-notify="container" class="alert alert-{0} col-11 col-sm-4" role="alert" style="position: fixed; bottom: 20px; left: ${leftPosition} !important; z-index: 1033;">
                <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="position: absolute; right: 10px; top: 5px; z-index: 1035;">×</button>
                <span data-notify="message">{2}</span>
            </div>
        `
                });
            }

            function showCustomWarningNotification(message, leftPosition = '20px') {
                $.notify({
                    message: message
                }, {
                    type: 'info',
                    placement: {
                        from: 'bottom',
                        align: 'left'
                    },
                    offset: {
                        left: leftPosition
                    },
                    delay: 1000,
                    animate: {
                        enter: 'animated fadeIn',
                        exit: 'animated fadeOut'
                    },
                    template: `
            <div data-notify="container" class="alert alert-{0} col-11 col-sm-4" role="alert" style="position: fixed; bottom: 20px; left: ${leftPosition} !important; z-index: 1033;">
                <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="position: absolute; right: 10px; top: 5px; z-index: 1035;">×</button>
                <span data-notify="message">{2}</span>
            </div>
        `
                });
            }

            // Correct way to use the asset helper in Blade with JavaScript:
            var warningIcon = "{{ asset('public/img/warning-yellow.png') }}";


            // Define fetchItems function
            function fetchItems(query = '') {
                $.post('{{ url('/fetch-test-names') }}', {
                    query: query,
                }, function(response) {
                    const $select = $('#testNameSelect');

                    // Update the options
                    $select.html(response.options);

                    // Reset the value to the empty one
                    $select.val('').trigger('change');

                    // Re-initialize or refresh Select2
                    $select.select2({
                        placeholder: "Select Test Name",
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });
                }).fail(function(xhr) {
                    console.error(xhr.responseText);
                });
            }

            // Initial load: fetch all items
            fetchItems();

            // // Define fetchItems function
            // function fetchItemCategories(query = '') {
            //     $.post('{{ url('/fetch-item-categories') }}', {
            //         query: query,
            //     }, function(response) {
            //         const $select = $('.item_cat_select');

            //         // Update the options
            //         $select.html(response.options);

            //         // Reset the value to the empty one
            //         $select.val('').trigger('change');

            //         // Re-initialize or refresh Select2
            //         $select.select2({
            //             placeholder: "Select Item Category",
            //             allowClear: true,
            //             minimumResultsForSearch: 0
            //         });
            //     }).fail(function(xhr) {
            //         console.error(xhr.responseText);
            //     });
            // }

            // // Initial load: fetch all items
            // fetchItemCategories();

            setTimeout(() => {
                $('#ItemCategorySelect').select2({
                    placeholder: "Select Item Category",
                    allowClear: true,
                    ajax: {
                        url: "{{ url('/fetch-item-categories') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                query: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text,
                                        desc: item.desc,
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        return data.text;
                    },
                    templateSelection: function(data, container) {
                        // add data attributes to the real <option>
                        if (data.id) {
                            const optionEl = $('#ItemCategorySelect').find("option[value='" +
                                data
                                .id + "']");
                            optionEl.attr('data-id', data.id);
                            optionEl.attr('data-description', data.desc);
                        }
                        return data.text || data.workorder_no;
                    }
                });
            }, 200);
            setTimeout(() => {
                $('#editItemSelect').select2({
                    placeholder: "Select Item Category",
                    allowClear: true,
                    ajax: {
                        url: "{{ url('/fetch-item-categories') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                query: params.term || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text,
                                        desc: item.desc
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        return data.text;
                    },
                    templateSelection: function(data, container) {
                        // add data attributes to the real <option>
                        if (data.id) {
                            const optionEl = $('#editItemSelect').find("option[value='" + data
                                .id + "']");
                            optionEl.attr('data-id', data.id);
                            optionEl.attr('data-description', data.desc);
                        }
                        return data.text || data.workorder_no;
                    }
                });
            }, 200);



            // Combined keydown/keyup handler for search input
            $('#editItemSearch').on('keydown keyup', function(e) {
                const hasText = $(this).val().trim() !== '';

                // Handle Enter key specifically on keydown
                if (e.type === 'keydown' && e.keyCode === 13) {
                    e.preventDefault();
                    if (hasText) editfilterItems();
                    return false;
                }

                // Handle regular keyup behavior
                if (e.type === 'keyup') {
                    // Toggle buttons and padding
                    $('#edit_filter_itemcode, #edit_clear_filter_itemcode').toggle(hasText);
                    $('.modal-subheader-text').toggleClass('pl-4', hasText);

                    // Show all items when empty
                    if (!hasText) $('#editSelectedItems .selected-item').show();
                }
            });




            // Optimized filter function with early exit
            function editfilterItems() {
                const searchTerm = $('#editItemSearch').val().toLowerCase().trim();
                if (!searchTerm) return;

                $('#editSelectedItems .selected-item').each(function() {
                    const text = $(this).find('.selected-itemcode')
                        .text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            }

            // Button event handlers
            $('#edit_filter_itemcode').on('click', editfilterItems);
            $('#edit_clear_filter_itemcode').on('click', function() {
                $('#editItemSearch').val('').trigger('keyup');
            });

            // Combined keydown/keyup handler for search input
            $('#itemSearch').on('keydown keyup', function(e) {
                const hasText = $(this).val().trim() !== '';

                // Handle Enter key specifically on keydown
                if (e.type === 'keydown' && e.keyCode === 13) {
                    e.preventDefault();
                    if (hasText) filterItems();
                    return false;
                }

                // Handle regular keyup behavior
                if (e.type === 'keyup') {
                    // Toggle buttons and padding
                    $('#filter_itemcode, #clear_filter_itemcode').toggle(hasText);
                    $('.modal-subheader-text').toggleClass('pl-4', hasText);

                    // Show all items when empty
                    if (!hasText) $('#selectedItems .selected-item').show();
                }
            });




            // Optimized filter function with early exit
            function filterItems() {
                const searchTerm = $('#itemSearch').val().toLowerCase().trim();
                if (!searchTerm) return;

                $('#selectedItems .selected-item').each(function() {
                    const text = $(this).find('.selected-itemcode, .selected-itemcode-desc')
                        .text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            }

            // Button event handlers
            $('#filter_itemcode').on('click', filterItems);
            $('#clear_filter_itemcode').on('click', function() {
                $('#itemSearch').val('').trigger('keyup');
            });



            // Show description when an item is selected
            $('#testNameSelect').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const test_type = selectedOption.data('test-type') || '';
                const criteria = selectedOption.data('criteria') || '';
                const uom = selectedOption.data('uom') || '';
                const standard = selectedOption.data('standard') || '';
                $('#selectedTestType').val(test_type);
                $('#selectedCriteria').val(criteria);
                $('#selectedStandard').val(standard);
                $('#uom').val(uom);
                $('#uom2').val(uom);

                $('.test_type_div').show();
                $('.test_type_div_2').show();
                $('.info-circle').hide();

                if (test_type == 'Dimension') {
                    $('.test_type_div label .label-text').text('Min/Max');
                    $('.test_type_div_2 label .label-text').text('Average');
                    $('.dimension-input').show();
                    $('.selectedTestType-div').show();
                    $('.perf-str-input').hide();
                    $('.perf-weight-input').hide();
                }
                if (test_type == 'Perf-Str' && criteria == 'Min') {
                    $('.test_type_div label .label-text').text('YFS/YFGS');
                    $('.info-circle').show();
                    $('.test_type_div_2 label .label-text').text('Safety Threshold');
                    $('.perf-str-input').show();
                    $('.dimension-input').hide();
                    $('.perf-weight-input').hide();
                    $('.selectedTestType-div').show();
                }
                if (test_type == 'Perf-Str' && criteria == 'Max') {
                    $('.test_type_div label .label-text').text('YFS/YFGS');
                    $('.info-circle').show();
                    $('.test_type_div_2').hide();
                    $('.perf-str-input').show();
                    $('.dimension-input').hide();
                    $('.perf-weight-input').hide();
                    $('.selectedTestType-div').show();
                }
                if (test_type == 'Perf-Weight') {
                    $('.test_type_div_2').hide();
                    $('.test_type_div label .label-text').text('Max % Absorption');
                    $('.perf-weight-input').show();
                    $('.perf-str-input').hide();
                    $('.dimension-input').hide();
                    $('.selectedTestType-div').show();
                }
            });

            // Handle Enter key in Select2 search field
            $(document).on('keydown', '.select2-search__field', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();

                    const select = $('#ItemCategorySelect');
                    if (select.val()) { // Only proceed if an item is selected
                        $('#min').focus();
                    }
                }
            });

            function toggleTestNameReadonly() {
                if ($('#selectedItems .selected-item').length > 0) {
                    $('#testNameSelect').prop('disabled', true).trigger('change.select2');
                } else {
                    $('#testNameSelect').prop('disabled', false).trigger('change.select2');
                }
            }


            $('[name="min"], [name="max"]').on('blur', function() {
                const form = $(this).closest('form'); // Get the current form
var old_Avg=form.find('[name="avg"]').val();
                let min = parseFloat(form.find('[name="min"]').val().trim());
                let max = parseFloat(form.find('[name="max"]').val().trim());

                if (!isNaN(min)) {
                    min = parseFloat(min.toFixed(3));
                    form.find('[name="min"]').val(min);
                }

                if (!isNaN(max)) {
                    max = parseFloat(max.toFixed(3));
                    form.find('[name="max"]').val(max);
                }

                if (!isNaN(min) && !isNaN(max)) {
                    if(old_Avg==''){
                        var avg = parseFloat(((min + max) / 2).toFixed(3));
                    }else{
                        var avg=old_Avg;
                    }

                    form.find('[name="avg"]').val(avg);
                } else {
                    form.find('[name="avg"]').val('');
                }
            });


            // For min & max with Enter key
            $('[name="min"], [name="max"]').on('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    const form = $(this).closest('form');

                    if (form.attr('id') === 'EditModuleForm') {
                        return; // Do nothing for EditModuleForm
                    }

                    e.preventDefault();
                    e.stopPropagation();

                    let min = parseFloat(form.find('[name="min"]').val().trim());
                    let max = parseFloat(form.find('[name="max"]').val().trim());

                    if (!isNaN(min) && !isNaN(max)) {
                        const select = form.find('#ItemCategorySelect');

                        if (select.val()) {
                            addSelectedItem();

                            setTimeout(() => {
                                select.val(null).trigger('change');
                                select.select2('open');
                            }, 0);
                        }
                    }
                }
            });



            $('[name="YFS"], [name="YFGS"], [name="safety_threshold"]').on('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();

                    const criteria = $('#selectedCriteria').val();

                    let YFS = $('[name="YFS"]').val().trim();
                    let YFGS = $('[name="YFGS"]').val().trim();
                    let safety_threshold = $('[name="safety_threshold"]').val().trim();

                    let isValid = false;

                    if (criteria === 'Max') {
                        YFS = parseFloat(YFS);
                        YFGS = parseFloat(YFGS);

                        if (!isNaN(YFS) && YFS > 0 && !isNaN(YFGS) && YFGS > 0) {
                            // Round to 2 decimal places
                            $('[name="YFS"]').val(YFS.toFixed(2));
                            $('[name="YFGS"]').val(YFGS.toFixed(2));
                            isValid = true;
                        } else {
                            if (isNaN(YFS) || YFS <= 0) $('[name="YFS"]').val('');
                            if (isNaN(YFGS) || YFGS <= 0) $('[name="YFGS"]').val('');
                        }
                    } else {
                        YFS = parseInt(YFS);
                        YFGS = parseInt(YFGS);
                        safety_threshold = parseInt(safety_threshold);

                        if (!isNaN(YFS) && YFS > 0 && !isNaN(YFGS) && YFGS > 0 && !isNaN(
                                safety_threshold) && safety_threshold > 0) {
                            $('[name="YFS"]').val(YFS);
                            $('[name="YFGS"]').val(YFGS);
                            $('[name="safety_threshold"]').val(safety_threshold);
                            isValid = true;
                        } else {
                            if (isNaN(YFS) || YFS <= 0) $('[name="YFS"]').val('');
                            if (isNaN(YFGS) || YFGS <= 0) $('[name="YFGS"]').val('');
                            if (isNaN(safety_threshold) || safety_threshold <= 0) $(
                                '[name="safety_threshold"]').val('');
                        }
                    }

                    if (isValid) {
                        const select = $('#ItemCategorySelect');
                        if (select.val()) {
                            addSelectedItem();

                            setTimeout(() => {
                                select.val(null).trigger('change');
                                select.select2('open');
                            }, 0);
                        }
                    }
                }
            });




            $('[name="absorption"]').on('blur', function() {
                let absorption = parseFloat($(this).val().trim());

                if (!isNaN(absorption)) {
                    $(this).val(absorption.toFixed(2));
                } else {
                    $(this).val('');
                }
            });
            $('[name="absorption"]').on('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();

                    let absorption = parseFloat($(this).val().trim());

                    if (!isNaN(absorption)) {
                        const select = $('#ItemCategorySelect');
                        if (select.val()) {
                            addSelectedItem();

                            setTimeout(() => {
                                select.val(null).trigger('change');
                                select.select2('open');
                            }, 0);
                        }
                    }
                }
            });







            // Add selected item to the list on button click or Enter key
            $('#addItem').on('click', addSelectedItem);

            // Refactored add item function
            function addSelectedItem() {
                var testNameSelect = $('#testNameSelect').val().trim();
                var selectedTestType = $('#selectedTestType').val().trim();
                var selectedCriteria = $('#selectedCriteria').val().trim();
                var minVal = $('#min').val().trim();
                var maxVal = $('#max').val().trim();
                let min = parseFloat(minVal);
                let max = parseFloat(maxVal);

                if (!isNaN(min)) {
                    min = parseFloat(min.toFixed(3));
                    $('#min').val(min);
                }

                if (!isNaN(max)) {
                    max = parseFloat(max.toFixed(3));
                    $('#max').val(max);
                }

                var avg = $('#avg').val();

                var YFS = $('#YFS').val().trim();
                var YFGS = $('#YFGS').val().trim();
                var safety_threshold = $('#safety_threshold').val().trim();

                var absorptionVal = $('[name="absorption"]').val().trim();
                let absorption = parseFloat(absorptionVal);

                var itemCategory = $('#ItemCategorySelect').val().trim();

                if (testNameSelect === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Test name first.`,
                        "500px");
                    return;
                }

                if (itemCategory === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select item category first.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Dimension' && (minVal === '' || isNaN(min))) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Min value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Dimension' && (maxVal === '' || isNaN(max))) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Max value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Dimension' && !isNaN(min) && !isNaN(max) && min > max) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Min value cannot be greater than Max value.`,
                        "500px"
                    );
                    return;
                }

                if (selectedTestType === 'Perf-Str' && YFS === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select YFS value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Perf-Str' && YFGS === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select YFGS value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Perf-Str' && selectedCriteria === 'Min' && safety_threshold === '') {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Safety Threshold value.`,
                        "500px");
                    return;
                }

                if (selectedTestType === 'Perf-Weight' && (absorptionVal === '' || isNaN(absorption))) {
                    showCustomWarningNotification(
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Max % Absorption value.`,
                        "500px");
                    return;
                } else if (!isNaN(absorption)) {
                    $('[name="absorption"]').val(absorption.toFixed(2));
                }

                var selectedOption = $('#ItemCategorySelect').find(':selected');
                var code_val = selectedOption.val();
                var code = selectedOption.text();
                var desc = selectedOption.data('description') || '';
                var id = selectedOption.data('id');

                if (code && id) {
                    // Check if already added
                    const exists = $('#selectedItems').find(`[data-id='${id}']`).length > 0;

                    if (!exists) {

                        let typeSpecificHTML = '';
                        let typeSpecificEditBtn = '';

                        if (selectedTestType == 'Dimension') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">min <span class="minor-tag">${min}</span></span>
            <span class="modal-subheader">avg <span class="minor-tag">${avg}</span></span>
            <span class="modal-subheader">max <span class="minor-tag">${max}</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Dimension" data-min="${min}" data-avg="${avg}" data-max="${max}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        } else if (selectedTestType == 'Perf-Str' && selectedCriteria === 'Min') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">YFS <span class="minor-tag">${YFS}</span></span>
            <span class="modal-subheader">YFGS <span class="minor-tag">${YFGS}</span></span>
            <span class="modal-subheader">ST <span class="minor-tag">${safety_threshold}</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Perf-Str-Min" data-yfs="${YFS}" data-yfgs="${YFGS}" data-st="${safety_threshold}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        } else if (selectedTestType == 'Perf-Str' && selectedCriteria === 'Max') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">YFS <span class="minor-tag">${YFS}</span></span>
            <span class="modal-subheader">YFGS <span class="minor-tag">${YFGS}</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Perf-Str-Max" data-yfs="${YFS}" data-yfgs="${YFGS}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        } else if (selectedTestType == 'Perf-Weight') {
                            typeSpecificHTML = `
        <div style="width: 70%; ">
            <span class="modal-subheader">max <span class="minor-tag">${absorption.toFixed(2)}%</span></span>
        </div>
    `;
                            typeSpecificEditBtn = `
        <a class="edit-pen edit-pen-selected-module mr-2" data-item="${code}" data-type="Perf-Weight" data-absorption="${absorption.toFixed(2)}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
        </a>
    `;
                        }

                        // Append item display div
                        $('#selectedItems').append(`
    <div class="col-sm-11 mx-auto mb-2 selected-item pl-0" data-id="${id}" data-code="${code}">
        <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
            <span class="selected-itemcode">${code}</span>
            ${typeSpecificHTML}
    <div class="d-flex align-items-center">
                            ${typeSpecificEditBtn}
                            <button type="button" class="close close-cross remove-item" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
        </div>
        <input type="hidden" name="item_category_ids[]" value="${id}">
        <input type="hidden" name="item_category_name[]" value="${code}">
        <input type="hidden" name="min_values[]" value="${min}">
        <input type="hidden" name="max_values[]" value="${max}">
        <input type="hidden" name="avg_values[]" value="${avg}">
        <input type="hidden" name="yfs_values[]" value="${YFS}">
        <input type="hidden" name="yfgs_values[]" value="${YFGS}">
        <input type="hidden" name="safety_threshold_values[]" value="${safety_threshold}">
        <input type="hidden" name="absorption_values[]" value="${absorption.toFixed(2)}">
    </div>
`);


                        // Clear and reset Select2 field
                        $('#ItemCategorySelect').val(null).trigger('change');

                        // Clear the input field
                        $('#min, #max, #YFS, #YFGS, #safety_threshold, #absorption').val('');

                        // Focus on the Select2 field
                        $('#ItemCategorySelect').select2('open');


                        toggleTestNameReadonly();

                        showCustomNotification("Item Category added successfully", "500px");

                    } else {

                        var message =
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category already added.`;
                        showCustomWarningNotification(message, "500px");

                    }
                } else {

                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select a valid Item Category.`;
                    showCustomWarningNotification(message, "500px");

                }
            }

            let lastRemovedItem = null; // Store last removed item for undo

            $('#selectedItems').on('click', '.remove-item', function() {
                const $item = $(this).closest('.selected-item');
                lastRemovedItem = {
                    html: $item.prop('outerHTML'),
                    index: $item.index()
                };
                $item.remove();

                toggleTestNameReadonly();

                var message =
                    `Item Category removed <a href="javascript:;" class="btn-notify undo-remove ml-4">Undo</a>`;
                showCustomWarningNotification(message, "500px");

            });

            // Handle undo click from notification
            $(document).on('click', '.undo-remove', function() {
                if (lastRemovedItem) {
                    const $itemsContainer = $('#selectedItems');
                    const itemCount = $itemsContainer.children().length;

                    // Restore item at original position
                    if (lastRemovedItem.index >= itemCount) {
                        $itemsContainer.append(lastRemovedItem.html);
                    } else {
                        $itemsContainer.children().eq(lastRemovedItem.index).before(lastRemovedItem.html);
                    }

                    toggleTestNameReadonly();

                    lastRemovedItem = null;
                }
            });

            $('#selectedItems, #editSelectedItems').on('click', '.edit-pen-selected-module', function() {
                var data_item = $(this).data('item');
                var data_type = $(this).data('type');

                var uom;

                if ($(this).closest('#selectedItems').length) {
                    uom = $('#uom').val();
                } else if ($(this).closest('#editSelectedItems').length) {
                    uom = $('.edit-uom').val();
                }

                $('.edit-pen-uom').val(uom);

                $('#edit_module_name').val(data_item);

                if (data_type === 'Dimension') {
                    var min = $(this).data('min');
                    var max = $(this).data('max');
                    var avg = $(this).data('avg');
                    $('#edit-pen-min').val(min);
                    $('#edit-pen-max').val(max);
                    $('#edit-pen-avg').val(avg);
                    $('.edit-pen-divs').hide();
                    $('.edit-dimension-div').show();
                } else if (data_type === 'Perf-Str-Min') {
                    var yfs = $(this).data('yfs');
                    var yfgs = $(this).data('yfgs');
                    var st = $(this).data('st');
                    $('#edit-pen-YFS').val(yfs);
                    $('#edit-pen-YFGS').val(yfgs);
                    $('#edit-pen-st').val(st);
                    $('.edit-pen-divs').hide();
                    $('.edit-perf-str-div').show();
                    $('.edit-perf-str-min-div').show();
                } else if (data_type === 'Perf-Str-Max') {
                    var yfs = $(this).data('yfs');
                    var yfgs = $(this).data('yfgs');
                    $('#edit-pen-YFS').val(yfs);
                    $('#edit-pen-YFGS').val(yfgs);
                    $('.edit-pen-divs').hide();
                    $('.edit-perf-str-div').show();
                } else if (data_type === 'Perf-Weight') {
                    var absorption = $(this).data('absorption');
                    $('#edit-pen-absorption').val(absorption);
                    $('.edit-pen-divs').hide();
                    $('.edit-perf-weight-div').show();
                }

                $('#EditAccessModal').modal('show');
            });
            $('#saveEditedAccess').on('click', function() {
                const moduleName = $('#edit_module_name').val().trim();
                const min = $('#edit-pen-min').val().trim();
                const max = $('#edit-pen-max').val().trim();
                const avg = $('#edit-pen-avg').val().trim();
                const YFS = $('#edit-pen-YFS').val().trim();
                const YFGS = $('#edit-pen-YFGS').val().trim();
                const ST = $('#edit-pen-st').val().trim();
                const absorption = $('#edit-pen-absorption').val().trim();

                var $targetRow = $(`#selectedItems .selected-item[data-code="${moduleName}"]`);

                if (!$targetRow.length) {
                    $targetRow = $(`#editSelectedItems .selected-item[data-code="${moduleName}"]`);
                }


                if (!$targetRow.length) {
                    showCustomWarningNotification('Could not find the selected row to update.', "500px");
                    return;
                }

                // Detect type from edit button
                const $editBtn = $targetRow.find('.edit-pen-selected-module');
                const type = $editBtn.data('type');

                if (type === 'Dimension') {
                    $targetRow.find('.minor-tag').eq(0).text(min);
                    $targetRow.find('.minor-tag').eq(1).text(avg);
                    $targetRow.find('.minor-tag').eq(2).text(max);

                    $targetRow.find('input[name="min_values[]"]').val(min);
                    $targetRow.find('input[name="max_values[]"]').val(max);
                    $targetRow.find('input[name="avg_values[]"]').val(avg);

                    $editBtn.attr('data-min', min)
                        .attr('data-max', max)
                        .attr('data-avg', avg);

                } else if (type === 'Perf-Str-Min') {
                    $targetRow.find('.minor-tag').eq(0).text(YFS);
                    $targetRow.find('.minor-tag').eq(1).text(YFGS);
                    $targetRow.find('.minor-tag').eq(2).text(ST);

                    $targetRow.find('input[name="yfs_values[]"]').val(YFS);
                    $targetRow.find('input[name="yfgs_values[]"]').val(YFGS);
                    $targetRow.find('input[name="safety_threshold_values[]"]').val(ST);

                    $editBtn.attr('data-yfs', YFS)
                        .attr('data-yfgs', YFGS)
                        .attr('data-st', ST);

                } else if (type === 'Perf-Str-Max') {
                    $targetRow.find('.minor-tag').eq(0).text(YFS);
                    $targetRow.find('.minor-tag').eq(1).text(YFGS);

                    $targetRow.find('input[name="yfs_values[]"]').val(YFS);
                    $targetRow.find('input[name="yfgs_values[]"]').val(YFGS);

                    $editBtn.attr('data-yfs', YFS)
                        .attr('data-yfgs', YFGS);

                } else if (type === 'Perf-Weight') {
                    const formattedAbsorption = parseFloat(absorption).toFixed(2);
                    $targetRow.find('.minor-tag').eq(0).text(`${formattedAbsorption}%`);
                    $targetRow.find('input[name="absorption_values[]"]').val(formattedAbsorption);
                    $editBtn.attr('data-absorption', formattedAbsorption);
                }

                $('#EditAccessModal').modal('hide');
            });






            $(document).ready(function() {
                const form = $('#form-insert-test-threshold');

                // Listen for Enter key in form inputs
                form.find('input').on('keypress', function(e) {
                    if (e.which === 13) { // Enter key
                        if ($('#EditAccessModal').is(':visible')) {
                            e.preventDefault();
                            return false;
                        }

                        e.preventDefault();
                        if (validateForm()) {
                            form.submit();
                        }
                    }
                });

                // Save button click
                form.find('#saveItemCategory').on('click', function(e) {
                    e.preventDefault();
                    if (validateForm()) {
                        form.submit();
                    }
                });

                // Global Enter key listener
                $(document).on('keydown', function(e) {
                    if (e.keyCode === 13) {
                        if ($('#EditAccessModal').is(':visible')) {
                            e.preventDefault();
                            return false;
                        }

                        if (!$('input:focus').length) {
                            const selectedItems = $('#selectedItems .selected-item').length;

                            if (selectedItems > 0) {
                                e.preventDefault();
                                if (validateForm()) {
                                    form.submit();
                                }
                            }
                        }
                    }
                });

                form.on('submit', function(e) {
                    e.preventDefault();

                    if (!validateForm()) {
                        return false;
                    }

                    const formData = new FormData(this);
                    const $button = $('.btn-action');

                    // Disable button and show loading indicator
                    $button.prop('disabled', true);
                    $button.find('.btn-action-gear').removeClass('d-none');
                    $button.find('.btn-action-gear img').addClass('rotating');

                    formData.append('testNameSelect', $('#testNameSelect').val().trim());

                    $.ajax({
                        url: "{{ url('insert-test-threshold') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status == "exist") {
                                showNotification('success', response.message ||
                                    'Test Threshold already exists.');
                                resetButtonState($button);
                            } else {
                                showNotification('success', response.message ||
                                    'Test Threshold saved successfully');
                                resetFormAndReload(form);
                            }
                        },
                        error: function(xhr) {
                            let message = 'An error occurred. Please try again.';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                } else if (xhr.responseJSON.errors) {
                                    message = Object.values(xhr.responseJSON.errors)
                                        .join('<br>');
                                }
                                form[0].reset();
                                $('.select2').val(null).trigger('change');
                                $('#selectedItems').empty();
                                toggleTestNameReadonly();
                            }

                            showNotification('error', message);
                            resetFormState(form, $button);
                        }
                    });
                });

                // Form validation function
                function validateForm() {
                    var selectedItems = $('#selectedItems .selected-item').length;
                    var $button = $('.btn-action');

                    // If no selected items, validate fields before adding
                    if (selectedItems === 0) {
                        var testNameSelect = $('#testNameSelect').val().trim();
                        var selectedTestType = $('#selectedTestType').val().trim();
                        var selectedCriteria = $('#selectedCriteria').val().trim();
                        var minVal = $('#min').val().trim();
                        var maxVal = $('#max').val().trim();
                        let min = parseFloat(minVal);
                        let max = parseFloat(maxVal);

                        if (!isNaN(min)) {
                            min = parseFloat(min.toFixed(3));
                            $('#min').val(min);
                        }

                        if (!isNaN(max)) {
                            max = parseFloat(max.toFixed(3));
                            $('#max').val(max);
                        }

                        var avg = $('#avg').val();

                        var YFS = $('#YFS').val().trim();
                        var YFGS = $('#YFGS').val().trim();
                        var safety_threshold = $('#safety_threshold').val().trim();

                        var absorptionVal = $('[name="absorption"]').val().trim();
                        let absorption = parseFloat(absorptionVal);

                        var itemCategory = $('#ItemCategorySelect').val().trim();

                        if (testNameSelect === '') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Test name first.`,
                                "500px");
                            return false;
                        }

                        if (itemCategory === '') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select item category first.`,
                                "500px");
                            return false;
                        }

                        if (selectedTestType === 'Dimension' && (minVal === '' || isNaN(min))) {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Min value.`,
                                "500px");
                            return false;
                        }

                        if (selectedTestType === 'Dimension' && (maxVal === '' || isNaN(max))) {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Max value.`,
                                "500px");
                            return false;
                        }

                        if (selectedTestType === 'Perf-Str' && YFS === '') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select YFS value.`,
                                "500px");
                            return false;
                        }

                        if (selectedTestType === 'Perf-Str' && YFGS === '') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select YFGS value.`,
                                "500px");
                            return false;
                        }

                        if (selectedTestType === 'Perf-Str' && selectedCriteria === 'Min' &&
                            safety_threshold === '') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Safety Threshold value.`,
                                "500px");
                            return false;
                        }

                        if (selectedTestType === 'Perf-Weight' && (absorptionVal === '' || isNaN(
                                absorption))) {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select Max % Absorption value.`,
                                "500px");
                            return false;
                        } else if (!isNaN(absorption)) {
                            $('[name="absorption"]').val(absorption.toFixed(2));
                        }
                    }

                    // After all that, check if at least one item is selected
                    if (selectedItems === 0) {
                        resetButtonState($button);
                        showNotification('warning', 'Please select at least one item');
                        return false;
                    }

                    return true;
                }


                // Rest of your helper functions remain the same...
                function resetButtonState($button) {
                    $button.prop('disabled', false);
                    $button.find('.btn-action-gear img').removeClass('rotating');
                    $button.find('.btn-action-gear').addClass('d-none');
                }

                function showNotification(type, message) {
                    let icon = '';
                    if (type === 'warning') {
                        icon =
                            '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> ';
                    } else if (type === 'error') {
                        icon =
                            '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> ';
                    }

                    var ErrorMessage = icon + message;
                    showCustomWarningNotification(ErrorMessage, "500px");

                }

                function resetFormAndReload(form) {
                    $('#insert-test-threshold-modal').modal('hide');
                    form[0].reset();
                    $('#selectedItems').empty();
                    setTimeout(() => location.reload(), 1200);
                }

                function resetFormState(form, $button) {
                    resetButtonState($button);
                }

                $('#insert-test-threshold-modal').on('hidden.bs.modal', function() {
                    var $modal = $(this);

                    const form = $('#form-insert-test-threshold');

                    form[0].reset();
                    // $('#testNameSelect').val(null).change();
                    $('#testNameSelect').prop('disabled', false);
                    $('#ItemCategorySelect').val(null).change();
                    $('#selectedItems').empty();

                    // Clear hidden fields
                    $('#selectedTestType').val('');
                    $('#selectedCriteria').val('');
                    $('#selectedStandard').val('');
                    $('#uom').val('');
                    $('#uom2').val('');

                    // Reset labels
                    $('.test_type_div label .label-text').text('');
                    $('.test_type_div_2 label .label-text').text('');

                    // Hide all conditional sections
                    $('.selectedTestType-div').hide();
                    $('.dimension-input').hide();
                    $('.perf-str-input').hide();
                    $('.perf-weight-input').hide();
                    $('.test_type_div').hide();
                    $('.test_type_div_2').hide();
                    $('.info-circle').hide();

                    // Reset the select
                    $('#testNameSelect').val('').trigger('change.select2'); // if using Select2

                });
            });





        });

        $(function() {

            FilePond.registerPlugin(
                FilePondPluginImagePreview,
                FilePondPluginImageExifOrientation,
                FilePondPluginFileValidateSize,
                FilePondPluginImageEdit,
                FilePondPluginFileValidateType
            );

            var attachments_file = [];
            var content3_image = []

            let filePond = FilePond.create(
                document.querySelector('.attachment'), {
                    name: 'attachment',
                    allowMultiple: true,
                    allowImagePreview: true,
                    imagePreviewFilterItem: false,
                    imagePreviewMarkupFilter: false,
                    dataMaxFileSize: "2MB",

                    // server

                    server: {
                        process: {
                            url: '{{ url('uploadTestThresholdAttachment') }}',
                            method: 'POST',
                            headers: {
                                'x-customheader': 'Processing File'
                            },
                            onload: (response) => {
                                response = response.replaceAll('"', '');
                                content3_image.push(response);
                                var attachemnts = $('input[name=attachment_array]').val()
                                var attachment_array = attachemnts.split(',');
                                attachment_array.push(response);
                                $('input[name=attachment_array]').val(content3_image.join(','));
                                return response;
                            },
                            onerror: (response) => {
                                return response
                            },
                            ondata: (formData) => {
                                window.h = formData;
                                return formData;
                            }
                        },
                        revert: (uniqueFileId, load, error) => {
                            const formData = new FormData();
                            formData.append("key", uniqueFileId);
                            content3_image = content3_image.filter(function(ele) {
                                return ele != uniqueFileId;
                            });
                            var attachemnts = $('input[name=attachment_array]').val()
                            var attachment_array = attachemnts.split(',');
                            attachment_array = attachment_array.filter(function(ele) {
                                return ele != uniqueFileId;
                            });

                            $('input[name=attachment_array]').val(content3_image.join(','));

                            fetch(`{{ url('revertTestThresholdAttachment') }}?key=${uniqueFileId}`, {
                                    method: "DELETE",
                                    body: formData,
                                })
                                .then(res => res.json())
                                .then(json => {
                                    console.log(json);
                                    // Should call the load method when done, no parameters required
                                    load();
                                })
                                .catch(err => {
                                    console.log(err)
                                    // Can call the error method if something is wrong, should exit after
                                    error(err.message);
                                })
                        },
                        remove: (uniqueFileId, load, error) => {
                            // Should somehow send `source` to server so server can remove the file with this source
                            content3_image = content3_image.filter(function(ele) {
                                return ele != uniqueFileId;
                            });
                            // Should call the load method when done, no parameters required
                            load();
                        },
                    }
                }
            );


            // Attachment ARRAY

            $('#AttachmentSave').click(function() {
                var attachment = content3_image;
                if (attachment.length === 0) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1">  Add an attachment before saving.',
                        delay: 5000
                    });
                } else {
                    $('#insert-attachment-test-threshold-form').submit(); // Corrected selector and method
                }
            });

            $('#download-template').on('click', function(e) {
                e.preventDefault(); // prevent default navigation

                const url = $(this).data('url');

                // create a temporary <a> to trigger download
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Test-Threshold-Sample.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });

            // On form submit
            $('form').on('submit', function() {
                var $button = $(this).find('.btn-action');
                var $button_import = $(this).find('.btn-action-import');

                $button_import.text('Importing');
                $button.prop('disabled', true);
                $button.find('.btn-action-gear').removeClass('d-none');
                $button.find('.btn-action-gear img').addClass('rotating');
            });

            // When modal is closed
            $('.modal').on('hidden.bs.modal', function() {
                resetBtnAction();
            });

            // // Optional: When page is reloaded
            // $(window).on('beforeunload', function () {
            //   resetBtnAction();
            // });

            // Reset function
            function resetBtnAction() {
                var $button = $('.btn-action');
                var $button_import = $('.btn-action-import');
                $button_import.text('Import');
                $button.prop('disabled', false);
                $button.find('.btn-action-gear img').removeClass('rotating');
                $button.find('.btn-action-gear').addClass('d-none');
            }



            $(document).on('click', '.insert_test_threshold', function() {
                $('.test_type_div').hide();
                $('.test_type_div_2').hide();
                $('#insert-test-threshold-modal').modal('show');
            });


            $(document).on('click', '.comment-icon', function() {
                var id = $(this).attr('data-item-id');
                $('#comment_id').val(id);
                $('#CommentModal').modal('show');
            });

            $(document).on('click', '.attachment-icon', function() {
                var id = $(this).attr('data-item-id');
                $('#attachment_id').val(id);
                $('#AttachmentModal').modal('show');
            });

            $(document).on('click', '.delete-icon', function() {
                var id = $(this).attr('data-item-id');
                $('#delete_id').val(id);
                $('#DeleteModal').modal('show');
            });

            $(document).on('click', '.delete-dot-icon', function() {
                var id = $(this).attr('data-item-id');
                $('#delete_id').val(id);
                $('#DeleteModal').modal('show');
            });

            $(document).on('click', '.delete-attachment', function() {
                var test_threshold_id = $(this).attr('data-item-id');
                var id = $(this).attr('data-id');
                $('#del_attachment_id').val(id);
                $('#del_test_threshold_id').val(test_threshold_id);
                $('#DelAttachmentModal').modal('show');
            });

            $(document).on('click', '.delete-comment', function() {
                var test_threshold_id = $(this).attr('data-item-id');
                var id = $(this).attr('data-id');
                $('#del_comment_id').val(id);
                $('#del_comment_test_threshold_id').val(test_threshold_id);
                $('#DelCommentModal').modal('show');
            });

            $(document).on('click', '.edit-comment', function() {
                var test_threshold_id = $(this).attr('data-item-id');
                var id = $(this).attr('data-id');
                var comment = $(this).attr('data-comment');
                $('#edit_comment_id').val(id);
                $('#edit_comment_test_threshold_id').val(test_threshold_id);
                $('#comment_text').text(comment);
                $('#EditCommentModal').modal('show');
            });


            $('.btn-export').on('click', function() {
                var url = $(this).attr('data-url');
                if (url) {
                    $('[data-toggle="tooltip"]').each(function() {
                        $(this).tooltip('hide');
                        $(this).removeAttr('aria-describedby');
                    });
                    var $btn = $(this);
                    var originalContent = $btn.html();
                    if ($btn.hasClass('disabled')) {
                        return;
                    }

                    $btn.addClass('expanded disabled');
                    $btn.html(
                        '<i class="fa fa-cog spinner text-white"></i> Exporting...'
                    );
                    setTimeout(() => {
                        window.location = url;
                    }, 100);

                    setTimeout(() => {
                        $btn.removeClass('expanded disabled').html(originalContent);
                        $('[data-toggle=tooltip]').tooltip();
                    }, 500);
                }

            })




            // $(document).on('click', '.filterItemcodeModal', function() {
            //     $('#filterItemcodeModal').modal('show');
            // })
            $(document).on('click', '.filterItemcodeModal', function(e) {
                e.stopPropagation();
                var dropdown = $('.filter-dropdown-container');

                // Close all other dropdowns
                $('.filter-dropdown-container').not(dropdown).removeClass('open');

                // Toggle this dropdown
                dropdown.toggleClass('open');
            });

            // Close dropdown when clicking close button
            $(document).on('click', '.close-filter', function(e) {
                e.stopPropagation();
                $(this).closest('.filter-dropdown-container').removeClass('open');
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.filter-dropdown-container, .filterItemcodeModal').length) {
                    $('.filter-dropdown-container').removeClass('open');
                }
            });
            Dashmix.helpers('rangeslider')
            @if (Session::has('success'))
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '{{ Session::get('success') }}',
                    delay: 5000
                });
            @endif


            @if (Session::has('alert-delete'))
                const alertStr = {!! json_encode(Session::get('alert-delete')) !!}; // ensures it's a proper string
                const parts = alertStr.split("|");
                const message = parts[0];
                const id = parts[1];
                const test_threshold_id = parts[2];

                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: message + ' <a href="javascript:;" data="' + id + '" data-test-def="' +
                        test_threshold_id +
                        '" data-notify="dismiss" class="btn-notify undo-delete ml-4">Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
            @endif

            @if (Session::has('alert-delete-attachment'))
                const alertStr = {!! json_encode(Session::get('alert-delete-attachment')) !!}; // ensures it's a proper string
                const parts = alertStr.split("|");
                const message = parts[0];
                const id = parts[1];
                const test_threshold_id = parts[2];

                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: message + ' <a href="javascript:;" data="' + id + '" data-test-def="' +
                        test_threshold_id +
                        '" data-notify="dismiss" class="btn-notify undo-delete-attachment ml-4">Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
            @endif

            @if (Session::has('alert-delete-category'))
                const alertStr = {!! json_encode(Session::get('alert-delete-category')) !!}; // ensures it's a proper string
                const parts = alertStr.split("|");
                const message = parts[0];
                const id = parts[1];

                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: message + ' <a href="javascript:;" data="' + id +
                        '" data-notify="dismiss" class="btn-notify undo-delete-category ml-4">Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
            @endif




            showData('{{ @$GETID }}');

            function showData(id) {
                $('.c-active').removeClass('c-active');
                if (id) {
                    $('.viewContent[data=' + id + ']').addClass('c-active');
                }
                $.ajax({
                    type: 'get',
                    data: {
                        id: id
                    },
                    url: '{{ url('get-test-threshold-content') }}',
                    dataType: 'json',
                    beforeSend() {
                        Dashmix.layout('header_loader_on');

                    },

                    success: function(res) {
                        Dashmix.layout('header_loader_off');
                        $(`.viewContent[data='${id}']`).html(res.viewContent);
                        $('.header-item-code').text(res.test_name);
                        $('.header-item-code-lastUpdateAt').text(res.last_updated_at);
                        $('.header-item-code-lastUpdateBy').text(res.last_updated_by);
                        $('#showData').html(res.editContent);
                        $('.tooltip').tooltip('hide');
                        $('.btnEdit').attr('data', res.id);
                        $('.btnDelete').attr('data', res.id);
                        $('.comment-icon').attr('data-item-id', res.id);
                        $('.attachment-icon').attr('data-item-id', res.id);
                        $('.edit-icon').attr('data-item-id', res.id);
                        $('.delete-icon').attr('data-item-id', res.id);
                        if (res.status == 1) {
                            $('.detail-header').removeClass('detail-header-red');
                            $('.detail-header').addClass('detail-header-blue');
                            $('.status-deactivate').show();
                            $('.status-reactivate').hide();
                            $('.status-deactivate a').attr('data', res.status);
                            $('.status-deactivate a').attr('data-id', res.id);
                        } else {
                            $('.detail-header').removeClass('detail-header-blue');
                            $('.detail-header').addClass('detail-header-red');
                            $('.status-deactivate').hide();
                            $('.status-reactivate').show();
                            $('.status-reactivate a').attr('data', res.status);
                            $('.status-reactivate a').attr('data-id', res.id);
                        }


                        $('[data-toggle=tooltip]').tooltip();
                    }
                })
            }

            $(document).on('click', '#btnExport', function() {
                var col = $('#columns').val();

                if (col != '') {
                    var form = $('#exportform');
                    if (form.attr("action") === undefined) {
                        throw "form does not have action attribute"
                    }


                    let url = form.attr("action");
                    var action = '';
                    if (url.includes("?") === false) {
                        let index = url.indexOf("?");
                        action = url
                        let params = url.slice(index);
                        url = new URLSearchParams(params);
                        for (param of url.keys()) {
                            if (param != 'limit') {
                                let paramValue = url.get(param);

                                let attrObject = {
                                    "type": "hidden",
                                    "name": param,
                                    "value": paramValue
                                };
                                let hidden = $("<input>").attr(attrObject);
                                form.append(hidden);
                            }
                        }


                    } else {

                        let index = url.indexOf("?");
                        action = url.slice(0, index)
                        let params = url.slice(index);
                        url = new URLSearchParams(params);
                        for (param of url.keys()) {
                            if (param != 'limit') {
                                let paramValue = url.get(param);

                                let attrObject = {
                                    "type": "hidden",
                                    "name": param,
                                    "value": paramValue
                                };
                                let hidden = $("<input>").attr(attrObject);
                                form.append(hidden);
                            }
                        }
                    }
                    form.attr("action", action)
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Export Complete.  ',
                        delay: 5000
                    });
                    form.submit();
                    $('#ExportModal').modal('hide')
                } else {

                }
            })




            $(document).ready(function() {
                // Function to update URL query string
                function updateQueryStringParameter(uri, key, value) {
                    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                    if (uri.match(re)) {
                        return uri.replace(re, '$1' + key + "=" + value + '$2');
                    } else {
                        return uri + separator + key + "=" + value;
                    }
                }

                // Event Delegation for Dynamic Content
                $(document)
                    .on('input', '#itemcodeSearch', function() {
                        var $items = $('.selectedItemcodes .selected-cat-itemcodes');
                        if ($(this).val().trim() !== '') {
                            $('#filter_itemcode_detail, #clear_filter_itemcode_detail').show();
                            $('.modal-subheader-text').addClass(' pl-4');
                            $items.addClass('d-flex').show();
                        } else {
                            $('#filter_itemcode_detail, #clear_filter_itemcode_detail').hide();
                            $('.selectedItemcodes .selected-cat-itemcodes')
                                .show(); // Show all when empty
                            $('.modal-subheader-text').removeClass(' pl-4');
                            $items.addClass('d-flex').show();
                        }
                    })
                    .on('keypress', '#itemcodeSearch', function(e) {
                        if (e.which === 13) applyItemFilter(); // Enter key
                    })
                    .on('click', '#filter_itemcode_detail', applyItemFilter)
                    .on('click', '#clear_filter_itemcode_detail', function() {
                        $('#itemcodeSearch').val('');
                        $('.modal-subheader-text').removeClass(' pl-4');
                        $(this).hide();
                        $('#filter_itemcode_detail').hide();
                        $('.selectedItemcodes .selected-cat-itemcodes').show(); // Show all on clear
                    })
                    .on('click', '.viewContent', function(e) {
                        var id = $(this).attr('data');
                        var oldURL = window.location.href;

                        if (history.pushState) {
                            var newUrl = updateQueryStringParameter(oldURL, 'id', id);
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);
                        }

                        showData(id);

                    });

                // Filter Function
                function applyItemFilter() {
                    const searchTerm = $('#itemcodeSearch').val().trim().toLowerCase();
                    var $items = $('.selectedItemcodes .selected-cat-itemcodes');

                    $items.removeClass('d-flex').hide();

                    if (!searchTerm) {
                        $items.addClass('d-flex').show();
                        return;
                    }

                    $items.each(function() {
                        const $item = $(this);
                        const itemCode = $item.find('.selected-itemcode').text().toLowerCase();
                        const itemDesc = $item.find('.selected-itemcode-desc').text().toLowerCase();

                        const isMatch = itemCode.includes(searchTerm) || itemDesc.includes(
                            searchTerm);

                        if (isMatch) {
                            $item.addClass('d-flex').show();
                        }
                    });
                }




            });


            $('select[name=limit]').change(function() {
                var form = $('#limit_form');
                if (form.attr("action") === undefined) {
                    throw "form does not have action attribute"
                }


                let url = form.attr("action");
                if (url.includes("?") === false) return false;

                let index = url.indexOf("?");
                let action = url.slice(0, index)
                let params = url.slice(index);
                url = new URLSearchParams(params);
                for (param of url.keys()) {
                    if (param != 'limit') {
                        let paramValue = url.get(param);

                        let attrObject = {
                            "type": "hidden",
                            "name": param,
                            "value": paramValue
                        };
                        let hidden = $("<input>").attr(attrObject);
                        form.append(hidden);
                    }
                }
                form.attr("action", action)

                form.submit();
            })


            $('.ActionIcon').mouseover(function() {
                var data = $(this).attr('data-src');
                $(this).find('img').attr('src', data);
            })
            $('.ActionIcon').mouseout(function() {
                var data = $(this).attr('data-original-src');
                $(this).find('img').attr('src', data);
            })



            $('#form-search').submit(function(e) {
                e.preventDefault();
            })
            $('input[name=search]').keyup(function(e) {

                var val = $(this).val();
                if (e.which == 13) {
                    var form = $('#form-search');

                    let url = form.attr("action");
                    url += '&search=' + val;
                    window.location.href = url
                }
            })


            $(document).on('click', '.btnEnd', function() {
                var id = $(this).attr('data-id');
                var status = $(this).attr('data')
                $('input[name=id]').val(id);
                $("#form-end-test-threshold textarea[name=reason]").val('');
                if (status == 1) {
                    $('.revokeText').html('Deactivate')
                } else {
                    $('.revokeText').html('Reactivate')
                }
                $('#EndModal').modal('show')

            })







            let click = 0;
            $(document).on('keyup', 'input,textarea', function() {
                click = 1;

            })

            $(document).on('change', 'select', function() {
                click = 1;

            })


        });




        $(document).on('click', '#ImportTestThresholds', function() {
            $("#ImportModal").modal('show');
        });

        $(document).on({
            mouseenter: function() {
                var $this = $(this);
                if ($this.data('tooltipShown')) {
                    //$this.tooltip('hide');
                } else {
                    const data_business = $this.attr('data-notes');
                    var html = `
                <p class="mb-0 tooltip-h6">Note</p>
                <p class="text-muted mb-0">${data_business}</p>`;
                    $this.tooltip({
                        title: html,
                        html: true,
                        placement: 'bottom',
                        trigger: 'hover',
                        template: '<div class="tooltip custom-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                    }).tooltip('show');
                }
                $this.data('tooltipShown', !$this.data('tooltipShown'));
            },
            mouseleave: function() {
                var $this = $(this);
                if ($this.data('tooltipShown')) {
                    $this.tooltip('hide');
                }
            },
        }, '.client-info');

        $(document).ready(function() {
            $(document).on('click', '.undo-delete', function() {
                var test_threshold_id = $(this).attr('data-test-def');
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-comment-test-threshold') }}?id=" + id +
                    "&test_threshold_id=" + test_threshold_id;
            });
            $(document).on('click', '.undo-delete-attachment', function() {
                var test_threshold_id = $(this).attr('data-test-def');
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-attachment-test-threshold') }}?id=" + id +
                    "&test_threshold_id=" + test_threshold_id;
            });
            $(document).on('click', '.undo-delete-category', function() {
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-test-threshold') }}?id=" + id;
            });
        });
        $(document).ready(function() {
            $(document).on('shown.bs.modal', '#CommentModal', function() {
                $("#CommentModal textarea[name=comment]").focus();
            });
            $(document).on('shown.bs.modal', '#EndModal', function() {
                $('#EndModal textarea[name=reason]').focus();
            });
        });
    </script>
