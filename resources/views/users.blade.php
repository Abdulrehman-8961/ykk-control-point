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
                ->update(['users' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => $user_id, 'users' => $limit]);
        }
    } elseif ($no_check && !empty($no_check->item_categories)) {
        $limit = $no_check->item_categories;
    }
    
    // Sorting setup
    $field = request('field', 'id');
    $orderby = request('orderBy', 'desc');
    
    // Filters
    $filter_name = request('filter_name');
    $filter_email = request('filter_email');
    $status = request('filter_status');
    $search = request('search');
    
    // Build base query with JOIN
    $qry = DB::table('users as i')->where('i.is_deleted', 0);
    
    // Apply status filter - exact match for 1 or 0
    if (request()->has('filter_status') && in_array($status, ['0', '1'])) {
        $qry->where('i.portal_access', (int) $status);
    }
    
    // Apply search filter
    if (!empty($search)) {
        $qry->where(function ($query) use ($search) {
            $query
                ->where('i.firstname', 'like', '%' . $search . '%')
                ->orWhere('i.lastname', 'like', '%' . $search . '%')
                ->orWhere(DB::raw("CONCAT(firstname, ' ', lastname)"), 'like', '%' . $search . '%')
                ->orWhere('i.email', 'like', '%' . $search . '%');
        });
    }
    
    // Count distinct itemcodes (alternative method)
    $totalQuery = clone $qry;
    $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT i.id) as aggregate'))->first()->aggregate;
    
    // Get final results with grouping
    $qry = $qry
        ->select('i.*')
        ->groupBy('i.id') // Group by itemcode ID to remove duplicates
        ->orderBy($field, $orderby)
        ->paginate($limit)
        ->appends(request()->query());
    
    // Get ID from GET or fallback to first result
    $GETID = !empty($_GET['id']) ? $_GET['id'] : $qry[0]->id ?? null;
    
    ?>




    <main id="main-container pt-0">
        <!-- Hero -->


        <style type="text/css">
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

            .avatar-upload {
                position: relative;
                max-width: 100px;

            }

            .avatar-upload .avatar-edit {
                position: absolute;
                right: 25px;
                z-index: 1;
                top: -5px;
            }

            .avatar-upload .avatar-edit input {
                display: none;
            }

            .avatar-upload .avatar-edit input+label {
                display: inline-block;
                width: 23px;
                height: 23px;
                font-size: 12px !important;
                margin-bottom: 0;
                border-radius: 100%;
                background: #FFFFFF;
                border: 1px solid transparent;
                box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
                cursor: pointer;
                font-weight: normal;
                transition: all 0.2s ease-in-out;
            }

            .avatar-upload .avatar-edit input+label:hover {
                background: #f1f1f1;
                border-color: #d6d6d6;
            }

            .avatar-upload .avatar-edit input+label:after {
                content: "\f303";
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                color: #4194F6;
                position: absolute;
                top: 2px;
                left: 0;
                right: 0;
                text-align: center;
                margin: auto;
            }

            .avatar-upload .avatar-preview {
                width: 65px;
                height: 65px;
                position: relative;
                border-radius: 10;
                border: 6px solid #F8F8F8;
                box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
            }

            .avatar-upload .avatar-preview>div {
                width: 100%;
                height: 100%;

                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
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
                                        $filter = (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') . (isset($_GET['note']) ? '&note=' . $_GET['note'] : '') . (isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : '') . (isset($_GET['filter_name']) ? '&filter_name=' . $_GET['filter_name'] : '') . (isset($_GET['filter_email']) ? '&filter_email=' . $_GET['filter_email'] : '') . (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                                        ?>
                                        <form class="push mb-0" method="get" id="form-search"
                                            action="{{ url('users/') }}?{{ $filter }}">
                                            <div class="input-group main-search-input-group" id="search-container">
                                                <input type="text" value="{{ @$_GET['search'] }}"
                                                    class="form-control searchNew" name="search" placeholder="Search Users"
                                                    id="search-input">
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
                                        <a class="btn btn-dual insert_user d2 " data-custom-class="header-tooltip"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Add User" href="javascript:;" data-toggle="modal"
                                            data-target="#insert-user-modal">
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
                                                data-url="{{ url('export-users') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                                <img src="{{ asset('public/img/cf-menu-icons/header-export.png') }}"
                                                    width="20">
                                            </a>
                                            <a class="btn btn-dual  d2 "
                                                style="padding-bottom: 7px !important; padding-top: 4px !important;"
                                                href="javascript:;" data-custom-class="header-tooltip" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Import" id="ImportUsers">
                                                <img src="{{ asset('public/img/cf-menu-icons/header-import.png') }}"
                                                    width="20">
                                            </a>



                                        @endif
                                    </div>
                                    <div class="col-auto mr-auto text-center" style="">
                                        {{ $qry->appends($_GET)->onEachSide(0)->links() }}
                                    </div>
                                    <form id="limit_form" class="ml-2 mb-0"
                                        action="{{ url('users') }}?{{ $_SERVER['QUERY_STRING'] }}">
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
                                            data-title="Settings"
                                            class="mr-3 text-dark headerSetting d3   "><img
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
                            
                            $class = 'bubble-header-grey';
                            $get_text = '';
                            $get_text_before = '';
                            $get_text_display = 'd-none';
                            
                            if ($search && $filter_status) {
                                $class = 'bubble-header-green';
                                $get_text = 'Filtered and Search Results:';
                                $get_text_before = '';
                                $get_text_display = 'd-block';
                            } elseif ($search) {
                                $class = 'bubble-header-yellow';
                                $get_text = '';
                                $get_text_before = 'Search Results';
                                $get_text_display = 'd-block';
                            } elseif ($filter_status) {
                                $class = 'bubble-header-blue';
                                $get_text = 'Filters Applied:';
                                $get_text_before = '';
                                $get_text_display = 'd-block';
                            }
                            ?>
                            <div class="col-1 {{ $class }}"></div>
                            <p class="col-11 bubble-header-text d-flex justify-content-between align-items-center">Users
                                <span class="{{ $get_text_display }} text-right" style="line-height: 1.3;">
                                    <a class="clear-link" href="{{ url('/users') }}" data-custom-class="header-tooltip"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Clear">
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
                                    <img src="{{ asset('public/img/cf-menu-icons/main-menu-users-white.png') }}"
                                        style="width: 36px; height: 36px;">
                                    <div class="" style="margin-left: 0.91rem;">
                                        <h4 class="mb-1 header-new-text header-item-code text-uppercase"
                                            style="line-height:22px">User Name</h4>
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
                                <div class="col-1 bubble-filter-ht-status {{ $class }}"></div>
                                <form id="filterForm" method="GET" action="{{ url('/users') }}"
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
                                        </div>
                                        <input type="hidden" name="search" value="{{ @$_GET['search'] }}">
                                        <div class="block-content text-right pt-1 pr-0" style="padding-left: 9mm;">
                                            <a href="{{ url('/users') }}" class="btn btn-action mr-3">Clear</a>
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

                                        <div class="w-100 d-flex justify-content-between align-items-center">
                                            <div style="width: 14%;">
                                                @if (empty($q->user_image))
                                                    <img src="{{ asset('public/img/cf-menu-icons/user-icon.png') }}"
                                                        width="50">
                                                @else
                                                    <img src="{{ asset('public/client_logos/' . $q->user_image) }}"
                                                        width="50" height="50" class="rounded-circle">
                                                @endif
                                            </div>
                                            <div style="width: 84%;">
                                                <div class="d-flex justify-content-between">
                                                    <div class="font-signika bubble-item-title bubble-item-title-email"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="Email">
                                                        {{ $q->email }}
                                                    </div>
                                                    <div style="position: absolute;right: 10px;top: 10px;">
                                                        @if ($q->portal_access == 1)
                                                            <span class="font-signika bubble-status-active"
                                                                data-toggle="tooltip" data-trigger="hover"
                                                                data-placement="top" title=""
                                                                data-original-title="Status">Active</span>
                                                        @else
                                                            <span class="font-signika bubble-status-inactive"
                                                                data-toggle="tooltip" data-trigger="hover"
                                                                data-placement="top" title=""
                                                                data-original-title="Status">Inactive</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between" style="margin-top: 5px;">
                                                    <div class="d-flex flex-row" style="padding-top: 3px;">
                                                        <div>
                                                            <span
                                                                class="font-signika bubble-item-desc ml-1">{{ $q->firstname . ' ' . $q->lastname }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="dropdown dropdown-3dot">
                                                            <a class="dropdown-toggle action-dots border-0 bg-transparent px-0"
                                                                data-id="{{ $q->id }}"
                                                                data-status="{{ $q->portal_access }}" href="#"
                                                                role="button" data-toggle="dropdown" aria-expanded="false">
                                                                <img src="{{ asset('public/img/cf-menu-icons/3dots.png') }}"
                                                                    width="9">
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-3dot">
                                                                @if ($q->portal_access == 1)
                                                                    <a class="dropdown-item dropdown-item-3dot btnEnd"
                                                                        href="#" data="{{ $q->portal_access }}"
                                                                        data-id="{{ @$q->id }}"><img
                                                                            src="public/img/cf-menu-icons/3dot-deactivate.png">
                                                                        Deactivate</a>
                                                                @else
                                                                    <a class="dropdown-item dropdown-item-3dot btnEnd"
                                                                        href="#" data="{{ $q->portal_access }}"
                                                                        data-id="{{ @$q->id }}"><img
                                                                            src="public/img/cf-menu-icons/3dot-activate.png">
                                                                        Reactivate</a>
                                                                @endif
                                                                <a class="dropdown-item dropdown-item-3dot edit-dot-icon"
                                                                    data-item-id="{{ @$q->id }}" href="#"><img
                                                                        src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                                <a class="dropdown-item dropdown-item-3dot delete-dot-icon"
                                                                    data-item-id="{{ @$q->id }}" href="#"><img
                                                                        src="public/img/cf-menu-icons/3dot-delete.png">
                                                                    Delete</a>
                                                            </div>
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

            <form class="mb-0 pb-0" action="{{ url('insert-user') }}" id="form-insert-user" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="insert-user-modal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                                <h1 class="modal-header-insert mb-0">
                                    NEW<br>
                                    <span class="modal-subheader">USER</span>
                                </h1>
                                <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="block small-arrow modal-static-ht block-transparent mb-0">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="firstname">First
                                        Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="firstname" id="firstname"
                                            class="modal-input shadow-non e form-control text-uppercase" requ ired>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="lastname">Last
                                        Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="lastname" id="lastname"
                                            class="modal-input shadow-non e form-control text-uppercase" requ ired>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="email">Email
                                        Address</label>
                                    <div class="col-sm-8">
                                        <input type="email" name="email" id="email"
                                            class="modal-input shadow-non e form-control" requ ired>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="user_image">Profile
                                        Image</label>
                                    <div class="col-sm-8">
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' id="imageUpload" name="logo"
                                                    accept=".png, .jpg, .jpeg" />
                                                <label for="imageUpload"></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview"
                                                    style="background-image: url('{{ asset('public/img') }}/image-default.png');">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="modal-hr my-3">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label"
                                        for="itemSelect">Module</label>
                                    <div class="col-sm-8">
                                        <select class="modal-input shadow-non e form-control select2" id="itemSelect">
                                            <option value="">Select Module</option>
                                            <option value="Admin">Admin</option>
                                            <option value="WINGS">WINGS</option>
                                            <option value="Quality Control">Quality Control</option>
                                            <option value="Lost Time">Lost Time</option>
                                            <option value="Waste">Waste</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="access_type">Access
                                        Type</label>
                                    <div class="col-sm-8 d-flex">
                                        <div class="col-4 pl-0 pr-1">
                                            <input type="radio" id="read" name="access_type" value="read"
                                                class="custom-radio" checked>
                                            <label for="read" class="custom-radio-label text-uppercase">Read</label>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="radio" id="write" name="access_type" value="write"
                                                class="custom-radio">
                                            <label for="write" class="custom-radio-label text-uppercase">Write</label>
                                        </div>
                                        <div class="col-4 pr-0 pl-1">
                                            <input type="radio" id="admin" name="access_type" value="admin"
                                                class="custom-radio">
                                            <label for="admin" class="custom-radio-label text-uppercase">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="block-content pt-0 row form-group mt-3">
                                    <div class="col-sm-5 ml-auto text-right">
                                        <button type="button" class="btn btn-yes" id="addItem">Add</button>
                                    </div>
                                </div>

                                <div id="selectedItems" class="small-arrow selected-items-container ro w mt-3 mr-3"
                                    style="max-width: 100%;     height: 186px; max-height: 250px; overflow-y: auto;"></div>

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

            <form class="mb-0 pb-0" action="{{ url('update-user') }}" id="form-edit-user" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="edit_user_id">
                <div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                                <h1 class="modal-header-insert mb-0">
                                    <span id="edit_item_category_heading">EDIT</span><br>
                                    <span class="modal-subheader">USER</span>
                                </h1>
                                <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="block small-arrow modal-static-ht block-transparent mb-0">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="edit_firstname">First
                                        Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="firstname" id="edit_firstname"
                                            class="modal-input shadow-non e form-control  ">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="edit_lastname">Last
                                        Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="lastname" id="edit_lastname"
                                            class="modal-input shadow-non e form-control  ">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="edit_email">Email
                                        Address</label>
                                    <div class="col-sm-8">
                                        <input type="email" name="email" id="edit_email"
                                            class="modal-input shadow-non e form-control">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label" for="user_image">Profile
                                        Image</label>
                                    <div class="col-sm-4">
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' id="editimageUpload" name="logo"
                                                    accept=".png, .jpg, .jpeg" />
                                                <label for="editimageUpload"></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="editimagePreview"
                                                    style="background-image: url('{{ asset('public/img') }}/image-default.png');">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 ml-auto text-right d-flex align-items-center pl-0">
                                        <button type="button" class="btn btn-yes" id="reset_password" data-id="">Reset
                                            Password</button>
                                    </div>
                                </div>
                                <hr class="modal-hr my-3">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label"
                                        for="editItemSelect">Module</label>
                                    <div class="col-sm-8">
                                        <select class="modal-input shadow-non e form-control select2" id="editItemSelect">
                                            <option value="">Select Module</option>
                                            <option value="Admin">Admin</option>
                                            <option value="WINGS">WINGS</option>
                                            <option value="Quality Control">Quality Control</option>
                                            <option value="Lost Time">Lost Time</option>
                                            <option value="Waste">Waste</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label"
                                        for="editform_access_type">Access Type</label>
                                    <div class="col-sm-8 d-flex">
                                        <div class="col-4 pl-0 pr-1">
                                            <input type="radio" id="editRead" name="access_typeEdit" value="read"
                                                class="custom-radio" checked>
                                            <label for="editRead" class="custom-radio-label text-uppercase">Read</label>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="radio" id="editWrite" name="access_typeEdit" value="write"
                                                class="custom-radio">
                                            <label for="editWrite" class="custom-radio-label text-uppercase">Write</label>
                                        </div>
                                        <div class="col-4 pr-0 pl-1">
                                            <input type="radio" id="editAadmin" name="access_typeEdit" value="admin"
                                                class="custom-radio">
                                            <label for="editAadmin" class="custom-radio-label text-uppercase">Admin</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="block-content pt-0 row form-group mt-3">
                                    <div class="col-sm-5 ml-auto text-right">
                                        <button type="button" class="btn btn-yes" id="editAddItem">Add</button>
                                    </div>
                                </div>


                                <div id="editSelectedItems" class="small-arrow selected-items-container r ow mt-3 mr-3"
                                    style="max-width: 100%;     height: 186px; max-height: 250px; overflow-y: auto;"></div>

                                <div class="block-content block-content-full text-right" style="padding-left: 9mm;">
                                    <a href="javascript:;" d="" class="float-left delete-icon delete-edit-user"
                                        data-item-id="" data-item-code="" data-toggle="tooltip" data-trigger="hover"
                                        data-placement="top" title="" data-original-title="Delete">
                                        <img src="{{ asset('public/img/cf-menu-icons/3dot-delete.png') }}" width="24px">
                                    </a>
                                    <button type="submit" id="updateItemCategory" class="btn btn-action">
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

            <form class="mb-0 pb-0" action="{{ url('end-user') }}" id="form-end-user" method="post">
                @csrf
                <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Revoke</span> User</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <!-- <textarea class="form-control" rows="5" required="" name="reason" id="reason"></textarea> -->
                                            <p class="fw-300">Are you sure you wish to <span
                                                    class="revokeText text-lowercase"></span> this User?</p>
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

            <form class="mb-0 pb-0" action="{{ url('delete-user') }}" method="post">
                @csrf
                <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Delete User
                                            <div class="block-options">
                                            </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" id="delete_id" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <p class="fw-300">Are you sure you wish to delete this user?</p>
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

            <form class="mb-0 pb-0" action="{{ url('delete-user-attachment') }}" method="post">
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
                                    <input type="hidden" id="del_userId" name="userId">
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

            <form class="mb-0 pb-0" action="{{ url('delete-comment-user') }}" method="post">
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
                                    <input type="hidden" id="del_comment_userId" name="userId">
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

            <form action="{{ url('insert-comments-user') }}" method="post">
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

            <form action="{{ url('update-comments-user') }}" method="post">
                @csrf
                <input type="hidden" id="edit_comment_id" name="id">
                <input type="hidden" id="edit_comment_userId" name="userId">
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

            <form id="user-attachment-form" action="{{ url('insert-attachment-user') }}" method="post">
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




            <form action="{{ url('/import-users') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Import Users</span>
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
                                            <a href="#" data-url="{{ asset('public/Users_sample.xlsx') }}"
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
                                    <span class="b e section-header"> Edit Access Control</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Module</label>

                                    <div class="col-sm-7">
                                        <input type="text" id="edit_module_name" readonly
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label"
                                        for="edit_access_type">Access Type</label>
                                    <div class="col-sm-8 d-flex">
                                        <div class="col-4 pl-0 pr-1">
                                            <input type="radio" id="edit_read" name="edit_access_type" value="read"
                                                class="custom-radio" required>
                                            <label for="edit_read" class="custom-radio-label text-uppercase">Read</label>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="radio" id="edit_write" name="edit_access_type" value="write"
                                                class="custom-radio">
                                            <label for="edit_write" class="custom-radio-label text-uppercase">Write</label>
                                        </div>
                                        <div class="col-4 pr-0 pl-1">
                                            <input type="radio" id="edit_admin" name="edit_access_type" value="admin"
                                                class="custom-radio">
                                            <label for="edit_admin" class="custom-radio-label text-uppercase">Admin</label>
                                        </div>
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

            <form action="" id="EditAccessModuleForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="EditFormAccessModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header"> Edit Access Control</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Module</label>

                                    <div class="col-sm-7">
                                        <input type="text" id="edited_module_name" readonly
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label"
                                        for="edit_access_type">Access Type</label>
                                    <div class="col-sm-8 d-flex">
                                        <div class="col-4 pl-0 pr-1">
                                            <input type="radio" id="editForm_read" name="editForm_access_type"
                                                value="read" class="custom-radio" required>
                                            <label for="editForm_read"
                                                class="custom-radio-label text-uppercase">Read</label>
                                        </div>
                                        <div class="col-4 px-1">
                                            <input type="radio" id="editForm_write" name="editForm_access_type"
                                                value="write" class="custom-radio">
                                            <label for="editForm_write"
                                                class="custom-radio-label text-uppercase">Write</label>
                                        </div>
                                        <div class="col-4 pr-0 pl-1">
                                            <input type="radio" id="editForm_admin" name="editForm_access_type"
                                                value="admin" class="custom-radio">
                                            <label for="editForm_admin"
                                                class="custom-radio-label text-uppercase">Admin</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content row block-content-full">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-yes" id="saveEditFormAccess">Save</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <form action="" id="reset-password-modal-form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="user_id">
                <div class="modal fade" id="reset-password-modal" tabindex="-1" role="dialog" data-b
                    ackdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Reset Password</span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0">New Password <span
                                            class="fas fa-info-circle ml-2 password-requirements"></span>
                                        <div class="tooltip-box">
                                            <strong>New Password Requirements</strong>
                                            <ul>
                                                <li>8 characters min length</li>
                                                <li>Must contain one CAPITAL</li>
                                                <li>Must contain one lowercase</li>
                                                <li>Must contain one numeric</li>
                                                <li>Must contain one Special Character</li>
                                            </ul>
                                        </div>
                                    </label>

                                    <div class="col-sm-7">
                                        <input type="password" name="new-password" id="new-password"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Confirm
                                        Password</label>
                                    <div class="col-sm-7">
                                        <input type="password" name="confirm-password" id="confirm-password"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mb-0" for="must_change">
                                        Must change password at next logon
                                    </label>
                                    <div class="col-sm-7 d-flex align-items-center">
                                        <input class="form-check-input ml-1" type="checkbox" value="1"
                                            name="must_change" id="must_change" style="width:20px; height: 20px;">
                                    </div>
                                </div>

                                <div class="block-content row block-content-full">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" id="set_password" class="btn btn-action">
                                            <span class="btn-action-gear d-none mr-2"><img
                                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                            Set Password
                                            <span class="btn-action-gear d-none ml-2"><img
                                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        </button>
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

            $('.select2').select2({
                placeholder: "Select an item",
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

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#imagePreview').hide();
                        $('#imagePreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $('#email').focusout(function() {
                var email = $(this).val().trim();

                if (email !== '') {
                    $.ajax({
                        url: '{{ url('/check-email') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            email: email
                        },
                        success: function(response) {
                            if (response.exists) {
                                // alert('This email is already registered!');
                                $('#email').focus();
                                $('#addItem').prop('disabled', true);
                                var message =
                                    `<img src="${warningIcon}" width="24px" class="mt-n1"> Email already exists!`;
                                showCustomWarningNotification(message, "500px");
                            } else {
                                $('#addItem').prop('disabled', false);
                            }
                        }
                    });
                } else {
                    $('#addItem').prop('disabled', true);
                }
            });

            // Optional: If user changes email manually, enable addItem (will re-check on focusout)
            $('#email').on('input', function() {
                $('#addItem').prop('disabled', true);
            });



            $("#imageUpload").change(function() {
                readURL(this);
            });

            function editreadURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#editimagePreview').css('background-image', 'url(' + e.target.result + ')');
                        $('#editimagePreview').hide();
                        $('#editimagePreview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }


            $("#editimageUpload").change(function() {
                editreadURL(this);
            });

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


            // Function to load user data
            function loadUserData(UserId) {
                $.ajax({
                    url: '{{ url('get-user') }}',
                    method: 'POST',
                    data: {
                        id: UserId
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#edit_user_id').val(response.data.id);
                            $('#reset_password').attr('data-id', response.data.id);
                            $('.delete-edit-user').attr('data-item-id', response.data.id);
                            $('#edit_item_category_heading').text(response.data.item_category);
                            $('#edit_firstname').val(response.data.firstname);
                            $('#edit_lastname').val(response.data.lastname);
                            $('#edit_email').val(response.data.email);
                            // Check if user_image exists and is not empty
                            let imageUrl = response.data.user_image && response.data.user_image
                            .trim() !== '' ?
                                'public/client_logos/' + response.data.user_image :
                                '{{ asset('public/img/image-default.png') }}';

                            // Just set the new background-image only
                            $('#editimagePreview').css('background-image', 'url("' + imageUrl + '")');


                            // Clear previous items
                            $('#editSelectedItems').empty();

                            // Add existing itemcodes
                            if (response.modules && response.modules.length > 0) {
                                response.modules.forEach(function(item) {
                                    $('#editSelectedItems').append(`
                                <div class="col-sm-10 mx-auto mb-2 selected-item pl-0" data-module="${item.module_name}">
                                    <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
                                        <div style="width: 65%; display: flex; align-items: center;">
                                            <span class="w-50 selected-itemcode text-capitalize">${item.access_type}</span>
                                            <span class="w-50 selected-itemcode-desc">${item.module_name}</span>
                                        </div>
                                <div class="d-flex align-items-center">
                                        <a class="edit-pen edit-pen-selected-module mr-2" data-module="${item.module_name}" data-accesstype="${item.access_type}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                        </a>
                                        <button type="button" class="close close-cross remove-item" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                </div>
                                    </div>
                                    <input type="hidden" name="module_name[]" value="${item.module_name}">
                                    <input type="hidden" name="module_access_type[]" value="${item.access_type}">
                                </div>
                            `);
                                    toggleEditUserEmailReadonly()
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
                const UserId = $(this).attr('data-item-id');
                loadUserData(UserId);
                $('#edit-user-modal').modal('show');
            });

            $(document).on('click', '.edit-dot-icon', function() {
                const UserId = $(this).attr('data-item-id');
                loadUserData(UserId);
                $('#edit-user-modal').modal('show');
            });

            $(document).on('click', '#reset_password', function() {
                const UserId = $(this).attr('data-id');
                $('#user_id').val(UserId);
                $('#reset-password-modal').modal('show');
            });


            $('#set_password').click(function() {
                var userId = $('#user_id').val();
                var newPassword = $('#new-password').val();
                var confirmPassword = $('#confirm-password').val();
                var mustChange = $('#must_change').is(':checked') ? 1 : 0;

                // Basic front-end check for empty fields
                if (!newPassword || !confirmPassword) {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill all the fields`;
                    showCustomWarningNotification(message, "500px");
                    return;
                }
                var $button = $('.btn-action');

                // Disable button and show loading indicator
                $button.prop('disabled', true);
                $button.find('.btn-action-gear').removeClass('d-none');
                $button.find('.btn-action-gear img').addClass('rotating');

                $.ajax({
                    url: "{{ url('/reset-user-password') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: userId,
                        new_password: newPassword,
                        confirm_password: confirmPassword,
                        must_change: mustChange
                    },
                    success: function(response) {
                        showCustomNotification(response.message, "500px");
                        $('#reset-password-modal').modal('hide');
                        $('#reset-password-modal-form')[0].reset();
                    },
                    error: function(xhr) {
                        var $button = $('.btn-action');

                        // Disable button and show loading indicator
                        $button.prop('disabled', false);
                        $button.find('.btn-action-gear').addClass('d-none');
                        $button.find('.btn-action-gear img').removeClass('rotating');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Match Laravel validation error structure
                            if (errors.confirm_password && errors.confirm_password.includes(
                                    "Confirm password must match new password.")) {
                                var message =
                                    `<img src="${warningIcon}" width="24px" class="mt-n1"> Confirm password must match new password`;
                                showCustomWarningNotification(message, "500px");
                            } else {
                                var message =
                                    `<img src="${warningIcon}" width="24px" class="mt-n1"> Reset password failed validation`;
                                showCustomWarningNotification(message, "500px");
                            }
                        } else {
                            var message =
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Something went wrong`;
                            showCustomWarningNotification(message, "500px");
                        }
                    }
                });
            });






            // Handle Enter key in Select2 search field
            function handleEditAccessTypeBasedOnSelection() {
                const select = $('#editItemSelect');
                const selectedValue = select.val();

                if (selectedValue) {
                    if (selectedValue === 'Admin') {
                        // Select 'admin', disable other radios
                        $('[name="access_typeEdit"][value="admin"]').prop('checked', true);
                        $('[name="access_typeEdit"]').not('[value="admin"]').prop('disabled', true);
                        // Apply CSS to other labels
                        $('[name="access_typeEdit"]').not('[value="admin"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '#f2f2f2',
                                'color': '#7F7F7F',
                                'pointer-events': 'none'
                            });
                        });
                    } else {
                        // Enable all, default to 'read'
                        $('[name="access_typeEdit"]').prop('disabled', false);
                        $('[name="access_typeEdit"][value="read"]').prop('checked', true);
                        // Apply CSS to other labels
                        $('[name="access_typeEdit"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '',
                                'color': '',
                                'pointer-events': ''
                            });
                        });
                    }
                }
            }


            // When selecting an item manually
            $('#editItemSelect').on('change', function() {
                handleEditAccessTypeBasedOnSelection();
            });

            // Handle Enter key in Select2 search field
            // On open of select2
            $('#editItemSelect').on('select2:open', function() {
                // Delay to ensure the search field is available
                setTimeout(() => {
                    $('.select2-search__field').off('keydown.editEnter').on('keydown.editEnter',
                        function(e) {
                            if (e.key === 'Enter' || e.keyCode === 13) {
                                e.preventDefault();
                                e.stopPropagation();

                                const select = $('#editItemSelect');
                                if (select.val()) {
                                    const access_type = $('[name="access_typeEdit"]:checked')
                                        .val()?.trim() || '';

                                    if (access_type === '') {
                                        const message =
                                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select an Access Type.`;
                                        showCustomWarningNotification(message, "500px");
                                        return;
                                    }

                                    const module_name = select.val();
                                    const selectedOption = select.find(':selected');

                                    if (module_name) {
                                        const exists = $('#editSelectedItems').find(
                                            `[data-module='${module_name}']`).length > 0;

                                        if (!exists) {
                                            $('#editSelectedItems').append(`
                                <div class="col-sm-10 mx-auto mb-2 selected-item pl-0" data-module="${module_name}">
                                    <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
                                        <div style="width: 65%; display: flex; align-items: center;">
                                            <span class="w-50 selected-itemcode text-capitalize">${access_type}</span>
                                            <span class="w-50 selected-itemcode-desc">${module_name}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <a class="edit-pen edit-pen-selected-module mr-2" data-module="${module_name}" data-accesstype="${access_type}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                <img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                            </a>
                                            <button type="button" class="close close-cross remove-item" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="module_name[]" value="${module_name}">
                                    <input type="hidden" name="module_access_type[]" value="${access_type}">
                                </div>
                            `);

                                            toggleEditUserEmailReadonly();

                                            showCustomNotification("Module added successfully",
                                                "500px");
                                        } else {
                                            const message =
                                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Module already exists.`;
                                            showCustomWarningNotification(message, "500px");
                                        }
                                    } else {
                                        const message =
                                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select a valid module.`;
                                        showCustomWarningNotification(message, "500px");
                                    }

                                    // Clear and reopen Select2
                                    setTimeout(() => {
                                        select.val(null).trigger('change');
                                        select.select2('open');
                                    }, 0);
                                }
                            }
                        });
                }, 0);
            });


            function toggleEditUserEmailReadonly() {
                if ($('#editSelectedItems .selected-item').length >= 0) {
                    $('#edit_email').attr('readonly', true);
                } else {
                    $('#edit_email').removeAttr('readonly');
                }
            }

            // Add selected item to the list on button click in edit modal
            $('#editAddItem').on('click', function() {

                const access_type = $('[name="access_typeEdit"]:checked').val()?.trim() || '';

                if (access_type === '') {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select an Access Type.`;
                    showCustomWarningNotification(message, "500px");
                    return; // Stop further execution
                }

                const selectedOption = $('#editItemSelect').find(':selected');
                const module_name = selectedOption.val();

                if (module_name) {
                    // Check if already added
                    const exists = $('#editSelectedItems').find(`[data-module='${module_name}']`).length >
                    0;

                    if (!exists) {
                        // Append item display div
                        $('#editSelectedItems').append(`
                    <div class="col-sm-10 mx-auto mb-2 selected-item pl-0" data-module="${module_name}">
                        <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
                            <div style="width: 65%; display: flex; align-items: center;">
                                <span class="w-50 selected-itemcode text-capitalize">${access_type}</span>
                                <span class="w-50 selected-itemcode-desc">${module_name}</span>
                            </div>
                    <div class="d-flex align-items-center">
                            <a class="edit-pen edit-pen-selected-module mr-2" data-module="${module_name}" data-accesstype="${access_type}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                            </a>
                            <button type="button" class="close close-cross remove-item" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                        </div>
                        <input type="hidden" name="module_name[]" value="${module_name}">
                        <input type="hidden" name="module_access_type[]" value="${access_type}">
                    </div>
                `);
                        // Clear and reset Select2 field
                        $('#editItemSelect').val(null).trigger('change');

                        // Focus on the Select2 field
                        $('#editItemSelect').select2('open');

                        toggleEditUserEmailReadonly();

                        showCustomNotification("Module added successfully", "500px");

                    } else {

                        var message =
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Module already exists.`;
                        showCustomWarningNotification(message, "500px");

                    }
                } else {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select a valid module.`;
                    showCustomWarningNotification(message, "500px");
                }
            });

            let lastRemovedItem = null; // Store last removed item for undo

            $('#editSelectedItems').on('click', '.remove-item', function() {
                const $item = $(this).closest('.selected-item');
                lastRemovedItem = {
                    html: $item.prop('outerHTML'),
                    index: $item.index()
                };
                $item.remove();
                toggleEditUserEmailReadonly();
                var message =
                    `Module removed <a href="javascript:;" class="btn-notify undo-remove ml-4">Undo</a>`;
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

                    toggleEditUserEmailReadonly();

                    lastRemovedItem = null;
                }
            });

            // Handle form submission for edit
            $('#form-edit-user').on('submit', function(e) {
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
                            'User updated successfully');
                        resetFormAndReload(form, '#edit-user-modal', '#editSelectedItems');
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
            $('#edit-user-modal').on('keypress', function(e) {
                // Check if Enter key was pressed (key code 13)
                if (e.which === 13) {
                    // Check if there are selected items
                    const hasSelectedItems = $('#editSelectedItems .selected-item').length > 0;

                    // Only submit if there are selected items and we're not in a textarea or input[type="text"]
                    const $target = $(e.target);
                    if (hasSelectedItems && !$target.is('textarea') && !$target.is('input[type="text"]')) {
                        e.preventDefault();
                        $('#form-edit-user').submit();
                    } else {
                        let message = 'User failed validation';
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


        // // Filter as user types
        // $('#itemSearch').on('keyup', function () {
        //   fetchItems($(this).val());
        // });

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




            // Handle Enter key in Select2 search field
            function handleAccessTypeBasedOnSelection() {
                const select = $('#itemSelect');
                const selectedValue = select.val();

                if (selectedValue) {
                    if (selectedValue === 'Admin') {
                        // Select 'admin', disable other radios
                        $('[name="access_type"][value="admin"]').prop('checked', true);
                        $('[name="access_type"]').not('[value="admin"]').prop('disabled', true);
                        // Apply CSS to other labels
                        $('[name="access_type"]').not('[value="admin"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '#f2f2f2',
                                'color': '#7F7F7F',
                                'pointer-events': 'none'
                            });
                        });
                    } else {
                        // Enable all, default to 'read'
                        $('[name="access_type"]').prop('disabled', false);
                        $('[name="access_type"][value="read"]').prop('checked', true);
                        // Apply CSS to other labels
                        $('[name="access_type"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '',
                                'color': '',
                                'pointer-events': ''
                            });
                        });
                    }
                }
            }

            // When pressing Enter inside Select2 search
            $(document).on('keydown', '.select2-search__field', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();
                    handleAccessTypeBasedOnSelection();
                    // Optionally: call your item adding function
                    addSelectedItem();

                    // Clear and reopen Select2
                    setTimeout(() => {
                        select.val(null).trigger('change');
                        select.select2('open');
                    }, 0);
                }
            });

            // When selecting an item manually
            $('#itemSelect').on('change', function() {
                handleAccessTypeBasedOnSelection();
            });


            function toggleUserEmailReadonly() {
                if ($('#selectedItems .selected-item').length > 0) {
                    $('#email').attr('readonly', true);
                } else {
                    $('#email').removeAttr('readonly');
                }
            }


            // Add selected item to the list on button click or Enter key
            $('#addItem').on('click', addSelectedItem);

            // Refactored add item function
            function addSelectedItem() {
                const firstname = $('#firstname').val().trim();

                if (firstname === '') {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill in the First Name first.`;
                    showCustomWarningNotification(message, "500px");
                    return; // Stop further execution
                }
                const lastname = $('#lastname').val().trim();

                if (lastname === '') {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill in the Last Name first.`;
                    showCustomWarningNotification(message, "500px");
                    return; // Stop further execution
                }
                const email = $('#email').val().trim();

                if (email === '') {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill in the Email Address first.`;
                    showCustomWarningNotification(message, "500px");
                    return; // Stop further execution
                }
                // Basic email format validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please enter a valid Email Address.`;
                    showCustomWarningNotification(message, "500px");
                    return;
                }

                const access_type = $('[name="access_type"]:checked').val()?.trim() || '';

                if (access_type === '') {
                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select an Access Type.`;
                    showCustomWarningNotification(message, "500px");
                    return; // Stop further execution
                }


                const selectedOption = $('#itemSelect').find(':selected');
                const module_name = selectedOption.val();

                if (module_name) {
                    // Check if already added
                    const exists = $('#selectedItems').find(`[data-module='${module_name}']`).length > 0;

                    if (!exists) {
                        // Append item display div
                        $('#selectedItems').append(`
                    <div class="col-sm-10 mx-auto mb-2 selected-item pl-0" data-module="${module_name}">
                        <div class="selected-items-list px-3 py-2 d-flex justify-content-between align-items-center">
                            <div style="width: 65%; display: flex; align-items: center;">
                                <span class="w-50 selected-itemcode text-capitalize">${access_type}</span>
                                <span class="w-50 selected-itemcode-desc">${module_name}</span>
                            </div>
                    <div class="d-flex align-items-center">
                            <a class="edit-pen edit-pen-selected-module mr-2" data-module="${module_name}" data-accesstype="${access_type}" data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit"><img src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                            </a>
                            <button type="button" class="close close-cross remove-item" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                        </div>
                        <input type="hidden" name="module_name[]" value="${module_name}">
                        <input type="hidden" name="module_access_type[]" value="${access_type}">
                    </div>
                `);

                        // Clear and reset Select2 field
                        $('#itemSelect').val(null).trigger('change');

                        // Focus on the Select2 field
                        $('#itemSelect').select2('open');

                        toggleUserEmailReadonly();

                        showCustomNotification("Module added successfully", "500px");

                    } else {

                        var message = `<img src="${warningIcon}" width="24px" class="mt-n1"> Module already added.`;
                        showCustomWarningNotification(message, "500px");

                    }
                } else {

                    var message =
                        `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select a valid module.`;
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

                toggleUserEmailReadonly();

                var message =
                    `Module removed <a href="javascript:;" class="btn-notify undo-remove ml-4">Undo</a>`;
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

                    toggleUserEmailReadonly();

                    lastRemovedItem = null;
                }
            });

            $(document).ready(function() {
                const form = $('#form-insert-user');

                // Listen for Enter key in form inputs
                form.find('input').on('keypress', function(e) {
                    if (e.which === 13) { // Enter key
                        e.preventDefault();
                        if (validateForm()) {
                            form.submit();
                        }
                    }
                });
                // Listen for Enter key in form inputs
                form.find('#saveItemCategory').on('click', function(e) {
                    e.preventDefault();
                    if (validateForm()) {
                        form.submit();
                    }
                });

                // Add global Enter key listener
                $(document).on('keydown', function(e) {
                    // Check if Enter key is pressed (keyCode 13)
                    if (e.keyCode === 13) {
                        // Check if no input is focused
                        if (!$('input:focus').length) {
                            // Check if form is valid
                            const firstname = form.find('[name="firstname"]').val().trim();
                            const lastname = form.find('[name="lastname"]').val().trim();
                            const email = form.find('[name="email"]').val().trim();
                            const selectedItems = $('#selectedItems .selected-item').length;

                            if (firstname && lastname && email && selectedItems > 0) {
                                e.preventDefault();
                                if (validateForm()) {
                                    form.submit();
                                }
                            }
                        }
                    }
                });

                $('#editSelectedItems').on('click', '.edit-pen-selected-module', function() {
                    var dataModule = $(this).data('module');
                    var dataAccess = $(this).data('accesstype'); // 'read', 'write', or 'admin'

                    $('#edited_module_name').val(dataModule);

                    // Set checked radio based on value
                    $(`[name="editForm_access_type"][value="${dataAccess}"]`).prop('checked', true);

                    // If admin, disable the other two radios
                    if (dataModule === 'Admin') {
                        $('[name="editForm_access_type"]').not('[value="admin"]').prop('disabled',
                            true);
                        // Apply CSS to other labels
                        $('[name="editForm_access_type"]').not('[value="admin"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '#f2f2f2',
                                'color': '#7F7F7F',
                                'pointer-events': 'none'
                            });
                        });
                    } else {
                        // Re-enable all radios if not admin
                        $('[name="editForm_access_type"]').prop('disabled', false);
                        // Apply CSS to other labels
                        $('[name="editForm_access_type"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '',
                                'color': '',
                                'pointer-events': ''
                            });
                        });
                    }

                    $('#EditFormAccessModal').modal('show');
                });
                $('#saveEditFormAccess').on('click', function() {
                    const moduleName = $('#edited_module_name').val().trim();
                    const accessType = $('[name="editForm_access_type"]:checked').val().trim();

                    if (moduleName === '' || accessType === '') {
                        var message =
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please complete all fields.`;
                        showCustomWarningNotification(message, "500px");
                        return;
                    }

                    // Find the matching row in #selectedItems
                    $('#editSelectedItems .selected-item').each(function() {
                        if ($(this).data('module') === moduleName) {
                            // Update visible text
                            $(this).find('.selected-itemcode').text(accessType.charAt(0)
                                .toUpperCase() + accessType.slice(1));

                            // Update data-accesstype attribute on edit icon
                            $(this).find('.edit-pen-selected-module').data('accesstype',
                                accessType);

                            // Update hidden input field value
                            $(this).find('input[name="module_access_type[]"]').val(
                                accessType);
                        }
                    });

                    // Close the modal
                    $('#EditFormAccessModal').modal('hide');
                });

                $('#selectedItems').on('click', '.edit-pen-selected-module', function() {
                    var dataModule = $(this).data('module');
                    var dataAccess = $(this).data('accesstype'); // 'read', 'write', or 'admin'

                    $('#edit_module_name').val(dataModule);

                    // Set checked radio based on value
                    $(`[name="edit_access_type"][value="${dataAccess}"]`).prop('checked', true);

                    // If admin, disable the other two radios
                    if (dataModule === 'Admin') {
                        $('[name="edit_access_type"]').not('[value="admin"]').prop('disabled',
                        true);
                        // Apply CSS to other labels
                        $('[name="edit_access_type"]').not('[value="admin"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '#f2f2f2',
                                'color': '#7F7F7F',
                                'pointer-events': 'none'
                            });
                        });
                    } else {
                        // Re-enable all radios if not admin
                        $('[name="edit_access_type"]').prop('disabled', false);
                        // Apply CSS to other labels
                        $('[name="edit_access_type"]').each(function() {
                            $('label[for="' + $(this).attr('id') + '"]').css({
                                'background-color': '',
                                'color': '',
                                'pointer-events': ''
                            });
                        });
                    }

                    $('#EditAccessModal').modal('show');
                });
                $('#saveEditedAccess').on('click', function() {
                    const moduleName = $('#edit_module_name').val().trim();
                    const accessType = $('[name="edit_access_type"]:checked').val().trim();

                    if (moduleName === '' || accessType === '') {
                        var message =
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please complete all fields.`;
                        showCustomWarningNotification(message, "500px");
                        return;
                    }

                    // Find the matching row in #selectedItems
                    $('#selectedItems .selected-item').each(function() {
                        if ($(this).data('module') === moduleName) {
                            // Update visible text
                            $(this).find('.selected-itemcode').text(accessType.charAt(0)
                                .toUpperCase() + accessType.slice(1));

                            // Update data-accesstype attribute on edit icon
                            $(this).find('.edit-pen-selected-module').data('accesstype',
                                accessType);

                            // Update hidden input field value
                            $(this).find('input[name="module_access_type[]"]').val(
                                accessType);
                        }
                    });

                    // Close the modal
                    $('#EditAccessModal').modal('hide');
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

                    $.ajax({
                        url: "{{ url('insert-user') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            showNotification('success', response.message ||
                                'User saved successfully');
                            resetFormAndReload(form);
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
                                toggleUserEmailReadonly();
                                $('#firstname').focus();
                            }

                            showNotification('error', message);
                            resetFormState(form, $button);
                        }
                    });
                });

                // Form validation function
                function validateForm() {
                    const selectedItems = $('#selectedItems .selected-item').length;
                    var firstname = form.find('[name="firstname"]').val().trim();
                    var lastname = form.find('[name="lastname"]').val().trim();
                    var email = form.find('[name="email"]').val().trim();
                    const $button = $('.btn-action');

                    if (!firstname) {
                        showNotification('warning', 'Please enter First Name');
                        return false;
                    }

                    if (!lastname) {
                        showNotification('warning', 'Please enter Last Name');
                        return false;
                    }

                    if (!email) {
                        showNotification('warning', 'Please enter an Email Address');
                        return false;
                    }

                    if (selectedItems === 0) {
                        resetButtonState($button);
                        showNotification('warning', 'Please select at least one module');
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
                    $('#insert-user-modal').modal('hide');
                    form[0].reset();
                    $('#selectedItems').empty();
                    setTimeout(() => location.reload(), 1200);
                }

                function resetFormState(form, $button) {
                    resetButtonState($button);
                }
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
                            url: '{{ url('uploadUserAttachment') }}',
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

                            fetch(`{{ url('revertUserAttachment') }}?key=${uniqueFileId}`, {
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
                    $('#user-attachment-form').submit(); // Corrected selector and method
                }
            });

            $('#download-template').on('click', function(e) {
                e.preventDefault(); // prevent default navigation

                const url = $(this).data('url');

                // create a temporary <a> to trigger download
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Users_sample.xlsx';
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



            $(document).on('click', '.insert_user', function() {
                $('#insert-user-modal').modal('show');
            });
            $('#insert-user-modal').on('hidden.bs.modal', function() {
                $('#form-insert-user')[0].reset();
                $('#selectedItems').empty();
                $('#email').prop('readonly', false);

                // Re-enable all radio buttons
                $('#read, #write, #admin').prop('disabled', false);

                // Uncheck all radios first (optional, but safe)
                $('#read, #write, #admin').prop('checked', false);

                // Default check 'read'
                $('#read').prop('checked', true);
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
                var userId = $(this).attr('data-item-id');
                var id = $(this).attr('data-id');
                $('#del_attachment_id').val(id);
                $('#del_userId').val(userId);
                $('#DelAttachmentModal').modal('show');
            });

            $(document).on('click', '.delete-comment', function() {
                var userId = $(this).attr('data-item-id');
                var id = $(this).attr('data-id');
                $('#del_comment_id').val(id);
                $('#del_comment_userId').val(userId);
                $('#DelCommentModal').modal('show');
            });

            $(document).on('click', '.edit-comment', function() {
                var userId = $(this).attr('data-item-id');
                var id = $(this).attr('data-id');
                var comment = $(this).attr('data-comment');
                $('#edit_comment_id').val(id);
                $('#edit_comment_userId').val(userId);
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
                const UserId = parts[2];

                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: message + ' <a href="javascript:;" data="' + id + '" data-user="' + UserId +
                        '" data-notify="dismiss" class="btn-notify undo-delete ml-4">Undo</a>',
                    delay: 50000,
                    type: 'info alert-notify-desktop'
                });
            @endif

            @if (Session::has('alert-delete-attachment'))
                const alertStr = {!! json_encode(Session::get('alert-delete-attachment')) !!}; // ensures it's a proper string
                const parts = alertStr.split("|");
                const message = parts[0];
                const id = parts[1];

                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: message + ' <a href="javascript:;" data="' + id +
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
                        '" data-notify="dismiss" class="btn-notify undo-delete-user ml-4">Undo</a>',
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
                    url: '{{ url('get-user-content') }}',
                    dataType: 'json',
                    beforeSend() {
                        Dashmix.layout('header_loader_on');

                    },

                    success: function(res) {
                        Dashmix.layout('header_loader_off');
                        $(`.viewContent[data='${id}']`).html(res.viewContent);
                        $('.header-item-code').text(res.firstname + " " + res.lastname);
                        $('.header-item-code-lastUpdateAt').text(res.last_updated_at);
                        $('.header-item-code-lastUpdateBy').text(res.last_updated_by);
                        $('#showData').html(res.editContent);
                        $('.tooltip').tooltip('hide');
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

            $(document).on('click', '.viewContent', function(e) {
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
                $("#form-end-user textarea[name=reason]").val('');
                if (status == 1) {
                    $('.revokeText').html('Deactivate')
                } else {
                    $('.revokeText').html('Reactivate')
                }
                $('#EndModal').modal('show')

            })




            $(document).on('click', '.btnDelete', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var id = $(this).attr('data');

                var c = confirm("Are you sure want to delete this User?");
                if (c) {
                    window.location.href = "{{ url('delete-gifi') }}?id=" + id;
                }
            })



            let click = 0;
            $(document).on('keyup', 'input,textarea', function() {
                click = 1;

            })

            $(document).on('change', 'select', function() {
                click = 1;

            })


        });




        $(document).on('click', '#ImportUsers', function() {
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
                var id = $(this).attr('data');
                var UserId = $(this).attr('data-user');
                window.location.href = "{{ url('undo-delete-user-comment') }}?id=" + id + "&userId=" +
                    UserId;
            });
            $(document).on('click', '.undo-delete-attachment', function() {
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-user-attachment') }}?id=" + id;
            });
            $(document).on('click', '.undo-delete-user', function() {
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-user') }}?id=" + id;
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
