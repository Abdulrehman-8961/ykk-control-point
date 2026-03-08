@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <?php
    $userAccess = explode(',', Auth::user()->access_to_client);
    $limit = 10;
    $no_check = DB::Table('settings')->where('user_id', Auth::id())->first();
    if (isset($_GET['limit']) && $_GET['limit'] != '') {
        $limit = $_GET['limit'];
        if ($no_check != '') {
            DB::table('settings')
                ->where('user_id', Auth::id())
                ->update(['client' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => Auth::id(), 'client' => $limit]);
        }
    } else {
        if ($no_check != '') {
            if ($no_check->client != '') {
                $limit = $no_check->client;
            }
        }
    }
    if (sizeof($_GET) > 0) {
        $orderby = 'desc';
        $field = 'c.id';
        if (isset($_GET['orderBy'])) {
            $orderby = $_GET['orderBy'];
            $field = $_GET['field'];
        }
        $type = @$_GET['filter_client_type'] ?? [];
        $business = @$_GET['filter_business'] ?? [];
        $province = @$_GET['filter_province'] ?? [];
        $remittance = @$_GET['filter_remittance'];
        $month_end = @$_GET['filter_month_end'];
        $qry = DB::table('clients as c')
            ->where('c.is_deleted', 0)
            ->leftJoin('cities as p', function ($join) {
                $join->on('c.province', '=', 'p.state_name')->whereRaw('p.id = (SELECT id FROM cities WHERE state_name = c.province LIMIT 1)');
            })
            ->where(function ($query) use ($type, $business, $province, $remittance, $month_end) {
                if (count($type) > 0) {
                    $query->whereIn('c.type', $type);
                }
                if (count($business) > 0) {
                    $query->whereIn('c.business', $business);
                }
                if (count($province) > 0) {
                    $query->whereIn('c.province', $province);
                }
                if (!empty($remittance)) {
                    $query->where('c.tax_remittance', $remittance);
                }
                if (!empty($month_end)) {
                    $query->where('c.fiscal_year_end', $month_end);
                }
                if (@$_GET['search']) {
                    $query->Orwhere('c.corporation_no', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.company', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.display_name', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.firstname', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.lastname', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.business', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.federal_no', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.provincial_no', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.email', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.telephone', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.fax', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.address', 'like', '%' . @$_GET['search'] . '%');
                    $query->Orwhere('c.postal_code', 'like', '%' . @$_GET['search'] . '%');
                }
            })
            ->select('c.*', 'p.state_code')
            ->orderBy($field, $orderby)
            ->paginate($limit);
        $qry->appends([
            'filter_client_type' => $type,
            'filter_business' => $business,
            'filter_province' => $province,
            'filter_remittance' => $remittance,
            'month_end' => $month_end,
        ]);
    } else {
        $qry = DB::table('clients as c')
            ->leftJoin('cities as p', function ($join) {
                $join->on('c.province', '=', 'p.state_name')->whereRaw('p.id = (SELECT id FROM cities WHERE state_name = c.province LIMIT 1)');
            })
            ->where('c.is_deleted', 0)
            ->select('c.*', 'p.state_code')
            ->orderBy('c.id', 'desc')
            ->paginate($limit);
    }
    if (isset($_GET['id'])) {
        $GETID = $_GET['id'];
    } else {
        $GETID = @$qry[0]->id;
    }
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
                background-color: #262626 !important;
                color: #FFFFFF !important;
                font-family: Signika !important;
                font-size: 14pt !important;
                padding-top: 14px;
                padding-bottom: 14px;
                z-index: 11000 !important;
            }

            .attachmentDivNew:hover {
                color: #FFFFFF !important;
                background-color: #4194F6;
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

            .attachmentDivNew:hover {
                color: #FFFFFF !important;
                background-color: #4194F6;
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

            .avatar-upload {
                position: relative;
                max-width: 105px;
            }

            .avatar-upload .avatar-edit {
                position: absolute;
                right: 0px;
                z-index: 1;
                top: -10px;
            }

            .avatar-upload .avatar-edit input {
                display: none;
            }

            .avatar-upload .avatar-edit input+label {
                display: inline-block;
                width: 34px;
                height: 34px;
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
                top: 5px;
                left: 0;
                right: 0;
                text-align: center;
                margin: auto;
            }

            .avatar-upload .avatar-preview {
                width: 82px;
                height: 82px;
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

            .view-------Content {
                background-color: #F2F2F2;
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

            .tooltip .arrow::before {
                border-top-color: #3A3B42 !important;
                border-bottom-color: #3A3B42 !important;
            }

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

            /* Add a class for the spinning gear icon */
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
                /* padding-right: 0px; */
                width: auto;
                transition: width 0.3s ease-in-out;
            }

            .LineTags {
                font-size: 10pt;
                width: 100px !important;
                padding-top: 5px;
                padding-bottom: 5px;
                border: 1px solid #7F7F7F;
                border-radius: 5px !important;
                color: #595959;
            }

            .MainTags {
                font-size: 18pt;
                width: 140px !important;
                font-weight: bold;
                border: 1px solid #595959;
                border-radius: 5px !important;
                color: #595959;
            }

            .bubble-new {
                text-align: right !important;
                color: #000000 !important;
            }

            .dashed-seperater {
                border: 1px dashed lightgrey;
            }

            .comments-text {
                color: #595959 !important;
                font-family: Calibri !important;
                font-size: 13pt !important;
                font-weight: bold;
            }

            .comments-subtext {
                color: #7F7F7F !important;
                font-family: Calibri !important;
                font-size: 10pt !important;
                font-weight: normal;
            }

            .tooltip-head-text {
                font-family: Calibri !important;
                font-weight: 900 !important;
                color: #000000 !important;
            }

            .tooltip.custom-tooltip {
                box-shadow: none !important;
            }

            .tooltip.custom-tooltip .tooltip-inner {
                box-shadow: none !important;
            }

            .spinner-blue {

                color: #2485E8 !important;

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

                padding-right: 40px;

                width: auto;

                transition: width 0.3s ease-in-out;

            }
        </style>
        <!-- Page Content -->
        <div class="con   no-print page-header " id="JournalHeader">
            <!-- Full Table -->
            <div class="b   mb-0  ">
                <div class="block-content pt-0 mt-0">
                    <div class="TopArea" style="position: sticky; padding-top: 14px; z-index: 1000; padding-bottom: 10px;">
                        <div class="row">
                            <div class="col-sm-9 search-col row">
                                <?php
                                $filter =
                                    (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') .
                                    (isset($_GET['firstname']) ? '&firstname=' . $_GET['firstname'] : '') .
                                    (isset($_GET['lastname']) ? '&lastname=' . $_GET['lastname'] : '') .
                                    (isset($_GET['business']) ? '&business=' . $_GET['business'] : '') .
                                    (isset($_GET['type']) ? '&type=' . $_GET['type'] : '') .
                                    (isset($_GET['federal_no']) ? '&federal_no=' . $_GET['federal_no'] : '') .
                                    (isset($_GET['provincial_no']) ? '&provincial_no=' . $_GET['provincial_no'] : '') .
                                    (isset($_GET['email']) ? '&email=' . $_GET['email'] : '') .
                                    (isset($_GET['telephone']) ? '&telephone=' . $_GET['telephone'] : '') .
                                    (isset($_GET['fax']) ? '&fax=' . $_GET['fax'] : '') .
                                    (isset($_GET['country']) ? '&country=' . $_GET['country'] : '') .
                                    (isset($_GET['province']) ? '&province=' . $_GET['province'] : '') .
                                    (isset($_GET['city']) ? '&city=' . $_GET['city'] : '') .
                                    (isset($_GET['postal_code']) ? '&postal_code=' . $_GET['postal_code'] : '') .
                                    (isset($_GET['fiscal_start']) ? '&fiscal_start=' . $_GET['fiscal_start'] : '') .
                                    (isset($_GET['fiscal_year_end']) ? '&fiscal_year_end=' . $_GET['fiscal_year_end'] : '') .
                                    (isset($_GET['account_no']) ? '&account_no=' . $_GET['account_no'] : '') .
                                    (isset($_GET['description']) ? '&description=' . $_GET['description'] : '') .
                                    (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                                ?>
                                <form class="push mb-0 col-sm-4 pr-0" method="get" id="form-search"
                                    action="{{ url('clients/') }}?{{ $filter }}">
                                    <div class="input-group main-search-input-group" style="max-width: 74.375%;">
                                        <input type="text" value="{{ @$_GET['search'] }}" class="form-control searchNew"
                                            name="search" placeholder="Search Clients">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <img src="{{ asset('public/img/ui-icon-search.png') }}" width="23px">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="    float-left " role="tab" id="accordion2_h1">
                                        <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->
                                    </div>
                                </form>
                                <div class="col-sm-auto  1" style="">
                                    <a class="btn btn-dual  d2 " href="javascript:;" data-toggle="tooltip"
                                        data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"
                                        title="" data-original-title="Filters">
                                        <img src="{{ asset('public/img/ui-icon-filters.png') }}" id="GeneralFilters"
                                            data-toggle="modal" data-bs-target="#filterGifiModal"
                                            data-target="#filterGifiModal" style="width:19px">
                                    </a>
                                    @if (Auth::user()->role != 'read')
                                        <a class="btn btn-dual  d2 " href="javascript:;" data-toggle="tooltip"
                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Add Clients">
                                            <img src="{{ asset('public/img/ui-icon-add.png') }}" data-toggle="modal"
                                                data-target="#AddTaxModal" style="width:15px">
                                        </a>
                                    @endif
                                </div>
                                {{-- <div class="col-sm-3 pl-0">
                                {{$qry->appends($_GET)->onEachSide(0)->links()}}
                            </div> --}}
                            </div>
                            <div class="d-flex text-right col-lg-3 justify-content-end">
                                {{ $qry->appends($_GET)->onEachSide(0)->links() }}
                                <form id="limit_form" class="ml-2 mb-0"
                                    action="{{ url('clients') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                    <select name="limit" class="float-right form-control mr-3   px-0" style="width:auto">
                                        <option value="10" {{ @$limit == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ @$limit == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ @$limit == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ @$limit == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </form>
                                @if (@Auth::user()->role == 'admin')
                                    <a href="javascript:;" data-toggle="tooltip" data-title="Settings"
                                        data-custom-class="header-tooltip" class="mr-3 text-dark headerSetting d3   "><img
                                            src="{{ asset('public/img/ui-icon-settings.png') }}" width="23px"></a>
                                @endif
                                <!-- User Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
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
            <div class="content  ">
                <!-- Page Content -->
                <div class="row px-0">
                    <div class="col-lg-8    " id="showData" style="overflow-y: auto;height:90vh;">
                    </div>
                    <div class="col-lg-4     no-print" style="overflow-y: auto;height: 90vh;">
                        <div class="clientFirstDiv" style="overflow-y: auto;height: 90vh;">
                            @foreach ($qry as $q)
                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent mr-2"
                                    data="{{ $q->id }}" style="cursor:pointer;">
                                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative" style="">
                                        <div class=" justify-content-center align-items-center  d-flex mr-1"
                                            style="width: 20%;float: left; padding: 7px;">
                                            @if ($q->logo != '')
                                                <img src="{{ asset('/public') }}/client_logos/{{ $q->logo }}"
                                                    class="rounded-circle  "
                                                    style="width: 72px;height: 72px;object-fit: cover;">
                                            @else
                                                <img src="{{ asset('public/img/image-default.png') }}"
                                                    class="rounded- circle  "
                                                    style="object-fit: cover;width: 72px;height: 72px;">
                                            @endif
                                        </div>
                                        <div class="justify-content-between d-flex" style="width: 79%;">
                                            <div class="d-flex flex-column " style="width: calc(100% - 115px);">
                                                <span class="font-12pt mb-0 text-truncate font-w600 c1"
                                                    style="font-family: Calibri;color:#4194F6 !important;">Client</span>
                                                <div class="d-flex flex-row w-100">
                                                    <span data-toggle="tooltip" data-trigger="hover" data-placement="bottom"
                                                        title="" data-original-title="{{ $q->tax_remittance }}"
                                                        style="
                                                        /* overflow: hidden; */
                                    text-overflow: ellipsis;
                                    white-space: nowrap;font-size:12pt;
                                    /*width: fit-content;**/
                                    width: 26px;
                                    text-align:center;
                                    font-family: Calibri;
                                    border-style: dashed !important;
                                    color:#FFF;
                                    background-color: #404040;
                                    border:1px solid #3F3F3F;
                                    border-radius: 2px;
                                  line-height: 1.6;
                                  padding-top: 2px;
                                  padding-bottom: 2px;
                                  padding-left: 5px;
                                  padding-right: 5px;
                                    margin-right: 0.375rem;
                                    /*max-width: 12%;**/
                                    "
                                                        class="px- 2">{{ $q->tax_remittance[0] }}</span>
                                                    <span class="flex-grow-1"
                                                        style="overflow: hidden;
                                    text-overflow: ellipsis;
                                    white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                                    color: #262626;
                                    border-style: dashed !important;
                                    border:1px solid #262626;
                                    background-color: #BFBFBF;
                                    border-radius: 2px;
                                    line-height: 1.6;
                                    padding-top: 2px;
                                    padding-bottom: 2px;
                                    padding-left: 5px;
                                    padding-right: 5px;">{{ $q->company }}</span>
                                                </div>
                                                <div class="d-flex flex-row" style="padding-top: 3px;">
                                                    <div
                                                        style="overflow: hidden;
                                        text-overflow: ellipsis;
                                        width: fit-content;
                                        line-height: 1.6;
                                        white-space: nowrap;
                                        font-size: 11pt;
                                        font-family: Calibri;">
                                                        <span>Year End: {{ $q->fiscal_year_end }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="position: absolute;right: 10px;top: 10px;">
                                                @if ($q->client_status == 1)
                                                    <span
                                                        style="float:right;
                                    font-family: Calibri;
                                    line-height: 1.5 !important;
                                    color: #FFF;
                                    background-color: #4EA833;
                                    font-weight: 600!important;
                                    border: 1px solid transparent;
                                    width:fit-content;
                                    font-weight: 600!important;
                                    text-align:center;
                                    align-items: center;
                                    border-radius: 5px;
                                    justify-content: center;
                                    display: flex;
                                    padding-left: 15px;
                                    padding-right: 15px;
                                    padding-top: 2px;
                                    padding-bottom: 2px;
                                    display: block;
                                    line-height: 1;
                                    text-align: center;
                                    border-radius: 3px;
                                    font-size: 11pt;">Active</span>
                                                @else
                                                    <span
                                                        style="float:right;
                                    font-family: Calibri;
                                    line-height: 1.5 !important;
                                    color: #FFF;
                                    background-color: #E54643;
                                    font-weight: 600!important;
                                    border: 1px solid transparent;
                                    width:fit-content;
                                    font-weight: 600!important;
                                    text-align:center;
                                    align-items: center;
                                    border-radius: 5px;
                                    justify-content: center;
                                    display: flex;
                                    padding-left: 15px;
                                    padding-right: 15px;
                                    padding-top: 2px;
                                    padding-bottom: 2px;
                                    display: block;
                                    line-height: 1;
                                    text-align: center;
                                    border-radius: 3px;
                                    font-size: 11pt;">Inactive</span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-row justify-content-end"
                                                style="margin-top: 20px;position: absolute;right: 10px;bottom: 4px;">
                                                <div class="ActionIcon  ml-1   "
                                                    style="margin-top: 1px;padding: 0.21rem 0.31rem !important;border-radius: 1rem">
                                                    <a href="javascript:;" data="{{ $q->id }}" class="client-info2"
                                                        data-business="{{ $q->business }}"
                                                        data-federal-no="{{ $q->federal_no }}"
                                                        data-provincial-no="{{ $q->provincial_no }}"
                                                        data-city="{{ $q->city }}" data-country="{{ $q->country }}"
                                                        data-telephone="{{ $q->telephone }}"
                                                        data-address="{{ $q->address }}" data-fax="{{ $q->fax }}"
                                                        data-province="{{ $q->province }}"
                                                        data-postcode="{{ $q->postal_code }}">
                                                        <img src="{{ asset('public') }}/icons2/icon-location-grey.png?cache=1"
                                                            width="22px">
                                                    </a>
                                                </div>
                                                <div class="ActionIcon  ml-1  p-1   " style="border-radius: 1rem">
                                                    <a href="javascript:;" data="{{ $q->id }}" class="client-info"
                                                        data-business="{{ $q->business }}"
                                                        data-federal-no="{{ $q->federal_no }}"
                                                        data-provincial-no="{{ $q->provincial_no }}"
                                                        data-city="{{ $q->city }}" data-country="{{ $q->country }}"
                                                        data-telephone="{{ $q->telephone }}"
                                                        data-address="{{ $q->address }}" data-fax="{{ $q->fax }}"
                                                        data-province="{{ $q->province }}"
                                                        data-postcode="{{ $q->postal_code }}">
                                                        <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                            width="23px">
                                                    </a>
                                                </div>
                                                <?php
                                                    if(Auth::check()){
                                                    if(@Auth::user()->role!='read'){
                                                ?>
                                                <div class="ActionIcon  ml-1    " style="border-radius: 1rem">
                                                    <a href="javascript:;" data="{{ $q->id }}" class="btnEdit ">
                                                        <img src="{{ asset('public') }}/icons2/icon-edit-grey.png?cache=1"
                                                            width="26px">
                                                    </a>
                                                </div>
                                                <div class="ActionIcon ml-1  " style="border-radius: 1rem">
                                                    <a href="javascript:;" class=" btnDelete" data="{{ $q->id }}">
                                                        <img src="{{ asset('public') }}/icons2/icon-delete-grey.png?cache=1"
                                                            width="25px">
                                                    </a>
                                                </div>
                                                <?php } }?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="clientSecondDiv "></div>
                    </div>
                    {{--
    <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="{{$q->id}}"
                        style="cursor:pointer;">
                        <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative"
                            style="height: 78px;">
                            <div class=" justify-content-center align-items-center  d-flex" style="margin-left: 3px;
                            margin-right: 1px;">
                                @if ($q->logo != '')
                                <img src="{{asset('/public')}}/client_logos/{{$q->logo}}" class="rounded-circle  "
                                     style=" padding-top: 9px;height: 70px;width: 70px;padding-left: 9px;object-fit: cover;padding-bottom: 9px;padding-right: 9px;">
                                @else
                                <img src="public/img/image-default.png" class="rounded-circle  " style="padding-top: 10px;height: 70px;width: 70px;padding-left: 10px;object-fit: cover;padding-bottom: 10px;padding-right: 10px;">
                                @endif
                            </div>
                            <div class="w-100 justify-content-between d-flex" style="padding-left: 10px">
                                <div class="d-flex flex-column " style="width: calc(100% - 130px);">
                                    <span style="font-family: Calibri;color:#4194F6;font-size:10pt;">Client</span>
                                    <span class="pl-1" style="overflow: hidden;
                                    text-overflow: ellipsis;
                                    white-space: nowrap;font-size:9pt;width: 60%;font-family: Calibri;color: #262626;border:1px solid #262626;background-color: #BFBFBF;border-radius: 4px;">{{$q->company}}</span>
                                    <div class="d-flex flex-row">
                                        <div>
                                            <span data-toggle="tooltip" data-trigger="hover"
                                            data-placement="bottom" title="" data-original-title="{{$q->tax_remittance}}" style="line-height: 24px;font-family: Calibri;font-size: 9pt;color:#3F3F3F;border:1px solid #3F3F3F;border-radius: 3px;padding-top: 1px;padding-bottom: 1px;margin-right: 0.675rem;" class="px-2">{{$q->tax_remittance[0]}}</span>
                                        </div>
                                        <div style="overflow: hidden;
                                        text-overflow: ellipsis;
                                        white-space: nowrap;font-size: 10pt;font-family: Calibri;">
                                            <span>{{$q->fiscal_year_end}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-column justify-content-between" style="padding-top:4px;padding-bottom: 4px;width:30%;position: absolute;right:20px;">
                                    <div>
                                        @if ($q->client_status == 1)
                                        <span style="float:right;width:70px !important;font-family: Calibri;color: #FFF;background-color: #4EA833;width:100%;padding-left: 8px;padding-right: 8px;padding-top: 7px;padding-bottom: 7px;display: block;line-height: 1;text-align: center;border-radius: 3px;font-size: 9pt;">Active</span>
                                        @else
                                        <span style="float:right;width:70px !important;font-family: Calibri;color: #FFF;background-color: #E54643;width:100%;padding-left: 8px;padding-right: 8px;padding-top: 7px;padding-bottom: 7px;display: block;line-height: 1;text-align: center;border-radius: 3px;font-size: 9pt;">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-row justify-content-end" style="margin-top: 10px;">
                                        <div class="ActionIcon  ml-1   " style="margin-top: 1px;padding: 0.21rem 0.31rem !important;border-radius: 1rem">
                                            <a href="javascript:;" data="{{$q->id}}" class="client-info2"
                                                data-business="{{$q->business}}" data-federal-no="{{$q->federal_no}}"
                                                data-provincial-no="{{$q->provincial_no}}" data-city="{{$q->city}}"
                                                data-country="{{$q->country}}" data-telephone="{{$q->telephone}}"
                                                data-address="{{$q->address}}" data-fax="{{$q->fax}}"
                                                data-province="{{$q->province}}" data-postcode="{{$q->postal_code}}">
                                                <img src="{{asset('public')}}/icons2/icon-location-grey.png?cache=1"
                                                    width="19px">
                                            </a>
                                        </div>
                                        <div class="ActionIcon  ml-1  p-1   " style="border-radius: 1rem">
                                            <a href="javascript:;" data="{{$q->id}}" class="client-info"
                                                data-business="{{$q->business}}" data-federal-no="{{$q->federal_no}}"
                                                data-provincial-no="{{$q->provincial_no}}" data-city="{{$q->city}}"
                                                data-country="{{$q->country}}" data-telephone="{{$q->telephone}}"
                                                data-address="{{$q->address}}" data-fax="{{$q->fax}}"
                                                data-province="{{$q->province}}" data-postcode="{{$q->postal_code}}">
                                                <img src="{{asset('public')}}/icons2/icon-info.png?cache=1"
                                                    width="20px">
                                            </a>
                                        </div>
                                        <?php     if(Auth::check()){
                                            if(@Auth::user()->role!='read'){ ?>
                                        <div class="ActionIcon  ml-1    " style="border-radius: 1rem">
                                            <a href="javascript:;" data="{{$q->id}}" class="btnEdit ">
                                                <img src="{{asset('public')}}/icons2/icon-edit-grey.png?cache=1"
                                                    width="22px">
                                            </a>
                                        </div>
                                        <div class="ActionIcon ml-1  " style="border-radius: 1rem">
                                            <a href="javascript:;" class=" btnDelete" data="{{$q->id}}">
                                                <img src="{{asset('public')}}/icons2/icon-delete-grey.png?cache=1"
                                                    width="22px">
                                            </a>
                                        </div>
                                        <?php } }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    --}}
                    {{--
    <div class="  " style="width:55%;padding-left: 8px !important;">
                                <div class="d-flex " style="padding-top: 6px;margin-bottom: 4px !important;">
                                    <p class="font-11pt mr-1   mb-0 pk-1 pk-blue " style=" " data="{{$q->id}}">
                                        {{$q->state_code}} </p>
                                    <p class="font-11pt mr-1 ml-1  mb-0  text-truncate  " style="color: #595959; "
                                        data="{{$q->id}}">
                                        {{$q->company}}</p>
                                <div class="d-flex pt-1">
                                    <p class="font-11pt mr-1 box-dark bg-secondary mb-0 px-2 text-white  "
                                        style="border-radius: 3px;" data-toggle="tooltip" data-trigger="hover"
                                        data-placement="top" title="" data-original-title="{{$q->tax_remittance}}"
                                        data="{{$q->id}}">{{$q->tax_remittance[0]}}</p>
                                    <p class="font-11pt   mb-0 ml-1    " data="{{$q->id}}">{{$q->fiscal_year_end}}</p>
                                </div>
                            </div>
                            <div style="position: absolute;width:  ; top: 10px;right: 16px;">
                                @if ($q->client_status == 1)
                                <div class="    ml-auto     text-center f   pk-green pk-1  ">
                                    <span class=" ">Active</span>
                                    @else
                                    <div class="    ml-auto     text-center   pk-red pk-1  ">
                                        <span class=" ">Inactive</span> @endif
                                    </div>
                                </div>
                                <div class=" text-right" style="width:10%;;">
                                    <div class=""
                                        style="position: absolute; bottom:12px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                        <div class="ActionIcon  mr-2     " style="border-radius: 1rem">
                                            <a href="javascript:;" data="{{$q->id}}" class="client-info2"
                                                data-business="{{$q->business}}" data-federal-no="{{$q->federal_no}}"
                                                data-provincial-no="{{$q->provincial_no}}" data-city="{{$q->city}}"
                                                data-country="{{$q->country}}" data-telephone="{{$q->telephone}}"
                                                data-address="{{$q->address}}" data-fax="{{$q->fax}}"
                                                data-province="{{$q->province}}" data-postcode="{{$q->postal_code}}">
                                                <img src="{{asset('public')}}/icons2/icon-location-grey.png?cache=1"
                                                    width="20px">
                                            </a>
                                        </div>
                                        <div class="ActionIcon  mr-2  p-1   " style="border-radius: 1rem">
                                            <a href="javascript:;" data="{{$q->id}}" class="client-info"
                                                data-business="{{$q->business}}" data-federal-no="{{$q->federal_no}}"
                                                data-provincial-no="{{$q->provincial_no}}" data-city="{{$q->city}}"
                                                data-country="{{$q->country}}" data-telephone="{{$q->telephone}}"
                                                data-address="{{$q->address}}" data-fax="{{$q->fax}}"
                                                data-province="{{$q->province}}" data-postcode="{{$q->postal_code}}">
                                                <img src="{{asset('public')}}/icons2/icon-info.png?cache=1"
                                                    width="20px">
                                            </a>
                                        </div>
                                        <?php     if(Auth::check()){
                                            if(@Auth::user()->role!='read'){ ?>
                                        <div class="ActionIcon  ml- 2    " style="border-radius: 1rem">
                                            <a href="javascript:;" data="{{$q->id}}" class="btnEdit ">
                                                <img src="{{asset('public')}}/icons2/icon-edit-grey.png?cache=1"
                                                    width="22px">
                                            </a>
                                        </div>
                                        <div class="ActionIcon ml-1   mt-n1  " style="border-radius: 1rem">
                                            <a href="javascript:;" class=" btnDelete" data="{{$q->id}}">
                                                <img src="{{asset('public')}}/icons2/icon-delete-grey.png?cache=1"
                                                    width="22px">
                                            </a>
                                        </div>
                                        <?php } }?>
                                    </div>
                                </div>
    --}}
                    <form id="filterGifiForm" method="GET" action="{{ url('/clients') }}" class="mb-0 pb-0">
                        <div class="modal fade" id="filterGifiModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  " style="padding-top:20px;">
                                            <span class="b e section-header">Filters</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content new-block-content pt-0 pb-0 ">
                                            <div class="row">
                                                <label class="col-sm-3">Type</label>
                                                <div class="col-sm-5 form-group">
                                                    <select type="" class="form-control    selectpicker "
                                                        id="filter_client_type"
                                                        data-style="btn-outline-light border text-dark"
                                                        data-actions-box="true" data-live-search="true" title="All"
                                                        value="" name="filter_client_type[]" multiple="">
                                                        <?php
                                                        $filter_client_type = @$_GET['filter_client_type'] ?? [];
                                                        ?>
                                                        <option value="Sole proprietorship"
                                                            @if (in_array(
                                                                    "Sole
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        proprietorship",
                                                                    $filter_client_type)) selected @endif>Sole
                                                            proprietorship</option>
                                                        <option value="Partnership"
                                                            @if (in_array('Partnership', $filter_client_type)) selected @endif>Partnership
                                                        </option>
                                                        <option value="Corporation"
                                                            @if (in_array('Corporation', $filter_client_type)) selected @endif>Corporation
                                                        </option>
                                                        <option value="Limited liability partnership"
                                                            @if (in_array(
                                                                    "Limited
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        liability partnership",
                                                                    $filter_client_type)) selected @endif>
                                                            Limited liability partnership</option>
                                                        <option value="Cooperative"
                                                            @if (in_array('Cooperative', $filter_client_type)) selected @endif>Cooperative
                                                        </option>
                                                        <option value="Franchise"
                                                            @if (in_array('Franchise', $filter_client_type)) selected @endif>Franchise
                                                        </option>
                                                        <option value="Non-profit organization"
                                                            @if (in_array(
                                                                    "Non-profit
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        organization",
                                                                    $filter_client_type)) selected @endif>
                                                            Non-profit organization</option>
                                                        <option value="Professional corporation"
                                                            @if (in_array(
                                                                    "Professional
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        corporation",
                                                                    $filter_client_type)) selected @endif>
                                                            Professional corporation</option>
                                                        <option value="Joint venture"
                                                            @if (in_array('Joint venture', $filter_client_type)) selected @endif>Joint venture
                                                        </option>
                                                        <option value="Limited partnership"
                                                            @if (in_array(
                                                                    "Limited
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        partnership",
                                                                    $filter_client_type)) selected @endif>Limited
                                                            partnership</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class=" col-sm-3  " for="example-hf-email">Business</label>
                                                <div class="col-sm-7 form-group">
                                                    <select type="" class="form-control    selectpicker "
                                                        id="filter_business" data-style="btn-outline-light border text-dark"
                                                        data-actions-box="true" data-live-search="true" title="All"
                                                        value="" name="filter_business[]" multiple="">
                                                        <?php
                                                        $filtered_business = @$_GET['filter_business'] ?? [];
                                                        ?>
                                                        <option value="Agriculture and Farming"
                                                            @if (@$_GET['filter_business'] == 'Agriculture and Farming') selected @endif>
                                                            Agriculture and Farming</option>
                                                        <option value="Automotive"
                                                            @if (@$_GET['filter_business'] == 'Automotive') selected @endif>
                                                            Automotive</option>
                                                        <option value="Construction"
                                                            @if (@$_GET['filter_business'] == 'Construction') selected @endif>
                                                            Construction
                                                        </option>
                                                        <option value="Consulting"
                                                            @if (@$_GET['filter_business'] == 'Consulting') selected @endif>
                                                            Consulting</option>
                                                        <option value="Education and Training"
                                                            @if (@$_GET['filter_business'] == 'Education and Training') selected @endif>Education
                                                            and Training</option>
                                                        <option value="Energy and Utilities"
                                                            @if (@$_GET['filter_business'] == 'Energy and Utilities') selected @endif>Energy and
                                                            Utilities</option>
                                                        <option value="Entertainment"
                                                            @if (@$_GET['filter_business'] == 'Entertainment') selected @endif>Entertainment
                                                        </option>
                                                        <option value="Finance and Banking"
                                                            @if (@$_GET['filter_business'] == 'Finance and Banking') selected @endif>Finance and
                                                            Banking</option>
                                                        <option value="Food and Beverage"
                                                            @if (@$_GET['filter_business'] == 'Food and Beverage') selected @endif>Food and
                                                            Beverage</option>
                                                        <option value="Government and Non-Profit"
                                                            @if (@$_GET['filter_business'] == 'Government and Non-Profit') selected @endif>
                                                            Government and Non-Profit</option>
                                                        <option value="Healthcare"
                                                            @if (@$_GET['filter_business'] == 'Healthcare') selected @endif>
                                                            Healthcare</option>
                                                        <option value="Hospitality and Tourism"
                                                            @if (@$_GET['filter_business'] == 'Hospitality and Tourism') selected @endif>
                                                            Hospitality and Tourism</option>
                                                        <option value="Information Technology"
                                                            @if (@$_GET['filter_business'] == 'Information Technology') selected @endif>
                                                            Information Technology</option>
                                                        <option value="Marketing and Advertising"
                                                            @if (@$_GET['filter_business'] == 'Marketing and Advertising') selected @endif>
                                                            Marketing and Advertising</option>
                                                        <option value="Media and Communications"
                                                            @if (@$_GET['filter_business'] == 'Media and Communications') selected @endif>Media
                                                            and Communications</option>
                                                        <option value="Professional Services"
                                                            @if (@$_GET['filter_business'] == 'Professional Services') selected @endif>
                                                            Professional Services</option>
                                                        <option value="Real Estate and Property Management"
                                                            @if (@$_GET['filter_business'] == 'Real Estate and Property Management') selected @endif>
                                                            Real Estate and Property Management</option>
                                                        <option value="Retail and Consumer Goods"
                                                            @if (@$_GET['filter_business'] == 'Retail and Consumer Goods') selected @endif>Retail
                                                            and Consumer Goods</option>
                                                        <option value="Transportation and Logistics"
                                                            @if (@$_GET['filter_business'] == 'Transportation and Logistics') selected @endif>
                                                            Transportation and Logistics</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3">Province</label>
                                                <div class="col-sm-5 form-group">
                                                    <select type="" class="form-control    selectpicker "
                                                        id="filter_province" data-style="btn-outline-light border text-dark"
                                                        data-actions-box="true" data-live-search="true" title="All"
                                                        value="" name="filter_province[]" multiple="">
                                                        <?php
                                                        $filter_province = @$_GET['filter_province'] ?? [];
                                                        $provinces = DB::table('cities')->groupBy('state_code')->get();
                                                        foreach ($provinces as $prov) {
                                                            if (in_array($prov->state_name, $filter_province)) {
                                                                echo '<option value="' . $prov->state_name . '" selected >' . $prov->state_name . '</option>';
                                                            } else {
                                                                echo '<option value="' . $prov->state_name . '">' . $prov->state_name . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3">Remittance</label>
                                                <div class="col-sm-5 form-group">
                                                    <select type="" name="filter_remittance" class="form-control"
                                                        placeholder="">
                                                        <option value="Asset"
                                                            @if (@$_GET['filter_remittance'] == 'No') selected @endif>Does Not Remit
                                                        </option>
                                                        <option value="Monthly"
                                                            @if (@$_GET['filter_remittance'] == 'Monthly') selected @endif>
                                                            Monthly</option>
                                                        <option value="Quarterly"
                                                            @if (@$_GET['filter_remittance'] == 'Quarterly') selected @endif>
                                                            Quarterly</option>
                                                        <option value="Yearly"
                                                            @if (@$_GET['filter_remittance'] == 'Yearly') selected @endif>Yearly</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-3">Month End</label>
                                                <div class="col-sm-5 form-group">
                                                    <select type="" name="filter_month_end" class="form-control"
                                                        placeholder="">
                                                        <option value="" selected>Select Month</option>
                                                        <option value="January 31"
                                                            @if (@$_GET['filter_month_end'] == 'January 31') selected @endif>
                                                            January</option>
                                                        <option value="February 28"
                                                            @if (@$_GET['filter_month_end'] == 'February 28') selected @endif>
                                                            February</option>
                                                        <option value="March 31"
                                                            @if (@$_GET['filter_month_end'] == 'March 31') selected @endif>March</option>
                                                        <option value="April 30"
                                                            @if (@$_GET['filter_month_end'] == 'April 30') selected @endif>April</option>
                                                        <option value="May 31"
                                                            @if (@$_GET['filter_month_end'] == 'May 31') selected @endif>May</option>
                                                        <option value="June 30"
                                                            @if (@$_GET['filter_month_end'] == 'June 30') selected @endif>June</option>
                                                        <option value="July 31"
                                                            @if (@$_GET['filter_month_end'] == 'July 31') selected @endif>July</option>
                                                        <option value="August 31"
                                                            @if (@$_GET['filter_month_end'] == 'August 31') selected @endif>
                                                            August</option>
                                                        <option value="September 30"
                                                            @if (@$_GET['filter_month_end'] == 'September 30') selected @endif>September
                                                        </option>
                                                        <option value="October 31"
                                                            @if (@$_GET['filter_month_end'] == 'October 31') selected @endif>
                                                            October</option>
                                                        <option value="November 30"
                                                            @if (@$_GET['filter_month_end'] == 'November 30') selected @endif>
                                                            November</option>
                                                        <option value="December 31"
                                                            @if (@$_GET['filter_month_end'] == 'December 31') selected @endif>
                                                            December</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;">
                                            <button type="submit" class="btn mr-3 btn-new">Apply</button>
                                            <a href="{{ url('/clients') }}" class="btn     btn-new-secondary "
                                                style="">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form class="mb-0 pb-0" action="{{ url('end-clients') }}" method="post">
                        @csrf
                        <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header"><span class="revokeText">Revoke</span>
                                                Client</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content new-block-content pt-0 pb-0 ">
                                            <input type="hidden" name="id">
                                            <div class="row">
                                                <div class="col-sm-12 px-4">
                                                    <textarea class="form-control" rows="5" required="" name="reason" id="reason"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;">
                                            <button type="submit" class="btn btn-new ">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form class="mb-0 pb-0" id="form-edit-client-gifi" action="{{ url('/edit-client-gifi-account') }}"
                        method="post">
                        @csrf
                        <input type="hidden" name="client_id" value="">
                        <input type="hidden" name="client_account_id" value="">
                        <div class="modal fade" id="EditClientGifiModal" tabindex="-1" role="dialog"
                            data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " style="max-width: 750px;"
                                role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Edit Client Account</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option btnCloseAccountEditModal"
                                                    target-modal="#EditClientGifiModal">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content new-block-content pt-0 pb-0 ">
                                            <div class="row justify-content- form-group  push">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Account Type</label>
                                                </div>
                                                <div class="col-lg-8" style="padding-right: 1.75rem !important;">
                                                    <select type="" name="account_type" class="form-control"
                                                        placeholder="">
                                                        <option value="Asset">Asset</option>
                                                        <option value="Liability">Liability</option>
                                                        <option value="Retained Earning">Retained Earning</option>
                                                        <option value="Revenue">Revenue</option>
                                                        <option value="Expense">Expense</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Sub Type</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <select type="" name="sub_account_type" class="form-control"
                                                        placeholder="">
                                                        <?php
                                                        $sub_account_type = DB::Table('sub_account_type')->where('account_type', 'Asset')->get();
                                                        foreach ($sub_account_type as $s) {
                                                            echo '<option value="' . $s->sub_type . '"  data-min="' . $s->min . '"  data-max="' . $s->max . '">' . $s->sub_type . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Account No.</label>
                                                </div>
                                                <div class="col-lg-6 " style="padding-right: 1.75rem !important;">
                                                    <input type="" name="account_no" class="form-control"
                                                        placeholder="4-digit numeric code" disabled>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Description</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <input type="" name="description" class="form-control"
                                                        placeholder="Account description">
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label  ">Note</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <textarea type="" name="note" class="form-control" rows="5" placeholder="Gifi note"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            <button type="submit" class="btn mr-3 btn-new ">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form class="mb-0 pb-0" id="form-add-client-gifi" action="{{ url('/add-client-gifi-account') }}"
                        method="post">
                        @csrf
                        <input type="hidden" name="client_id" value="">
                        <div class="modal fade" id="AddClientGifiModal" tabindex="-1" role="dialog"
                            data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " style="max-width: 750px;"
                                role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Add Client Account</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content new-block-content pt-0 pb-0 ">
                                            <div class="row justify-content- form-group  push">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Account Type</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <select type="" name="account_type" class="form-control"
                                                        placeholder="">
                                                        <option value="Asset">Asset</option>
                                                        <option value="Liability">Liability</option>
                                                        <option value="Retained Earning">Retained Earning</option>
                                                        <option value="Revenue">Revenue</option>
                                                        <option value="Expense">Expense</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Sub Type</label>
                                                </div>
                                                <div class="col-lg-8 " style=" padding-right: 1.75rem !important;">
                                                    <select type="" name="sub_account_type" class="form-control"
                                                        placeholder="">
                                                        <?php
                                                        $sub_account_type = DB::Table('sub_account_type')->where('account_type', 'Asset')->get();
                                                        foreach ($sub_account_type as $s) {
                                                            echo '<option value="' . $s->sub_type . '"  data-min="' . $s->min . '"  data-max="' . $s->max . '">' . $s->sub_type . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Account No.</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <input type="" name="account_no" class="form-control"
                                                        placeholder="4-digit numeric code">
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label mandatory">Description</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <input type="" name="description" class="form-control"
                                                        placeholder="Account description">
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">
                                                    <label class="col-form-label  ">Note</label>
                                                </div>
                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <textarea type="" name="note" class="form-control" rows="5" placeholder="Gifi note"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            <button type="submit" class="btn mr-3 btn-new ">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form class="mb-0 pb-0" id="form-add-tax" action="{{ url('insert-clients') }}"
                        enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="modal fade" id="AddTaxModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  " style="padding-top:20px;">
                                            <span class="b e section-header" style="margin-left: 12px;">Client
                                                Information</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option close-modal"
                                                    target-modal="#AddTaxModal" aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content new-block-content pt-0 pb-0 ">
                                            <div class="block-content pb-0 new-block-content px-2">
                                                <div class="row justify-content-  push">
                                                    <div class="col-sm-12 m-">
                                                        <input type="hidden" name="attachment_array" id="attachment_array">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="form-group row">
                                                                    <label class="col-sm-2 pr-0 col-form-label mandatory"
                                                                        for="example-hf-client_id">Contact</label>
                                                                    <?php ?>
                                                                    <div class="col-sm-2">
                                                                        <select type="text" class="form-control"
                                                                            id="salutation" name="salutation"
                                                                            placeholder="Salutation">
                                                                            <option value="Mr">Mr.</option>
                                                                            <option value="Mrs">Mrs.</option>
                                                                            <option value="Ms">Ms.</option>
                                                                            <option value="Miss">Miss.</option>
                                                                            <option value="Dr">Dr.</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <input type="text" class="form-control"
                                                                            id="firstname" name="firstname"
                                                                            placeholder="First Name">
                                                                    </div>
                                                                    <div class="col-sm-4  ">
                                                                        <input type="text" class="form-control"
                                                                            id="lastname" name="lastname"
                                                                            placeholder="Last Name">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-sm-2  pr-0 col-form-label mandatory"
                                                                        for="example-hf-client_id">Client Name</label>
                                                                    <div class="col-sm-10 ">
                                                                        <input type="text" class="form-control"
                                                                            id="company" name="company"
                                                                            placeholder="Company name">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-sm-2  pr-0 col-form-label mandatory"
                                                                        for="example-hf-client_id">Display Name</label>
                                                                    <div class="col-sm-10 ">
                                                                        <input type="text" class="form-control"
                                                                            id="display_name" name="display_name"
                                                                            placeholder="Display name" maxlength="21">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-9">
                                                                <div class="form-group row " style="">
                                                                    <label class="col-sm-3 col-form-label mandatory pr-0"
                                                                        for="example-hf-client_id"
                                                                        style="margin-right: -4px !important;">Type</label>
                                                                    <div class="col-sm-9 pl-0">
                                                                        <select type="text" class="form-control"
                                                                            id="type" name="type"
                                                                            placeholder="Salutation">
                                                                            <option value="">Select enterprise type
                                                                            </option>
                                                                            <option value="Corporation">Corporation</option>
                                                                            <option value="Cooperative">Cooperative</option>
                                                                            <option value="Franchise">Franchise</option>
                                                                            <option value="Joint Proprietorship">Joint
                                                                                Proprietorship</option>
                                                                            <option value="Joint venture">Joint venture
                                                                            </option>
                                                                            <option value="Limited liability partnership">
                                                                                Limited liability partnership</option>
                                                                            <option value="Limited partnership">Limited
                                                                                partnership</option>
                                                                            <option value="Non-profit organization">Non-profit
                                                                                organization</option>
                                                                            <option value="Partnership">Partnership</option>
                                                                            <option value="Professional corporation">
                                                                                Professional corporation</option>
                                                                            <option value="Sole proprietorship">Sole
                                                                                proprietorship</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row d-none CorporationDiv">
                                                                    <label class="col-sm-3  pr-0 col-form-label mandatory"
                                                                        style="margin-right: -4px !important;"
                                                                        for="example-hf-client_id">Corporation #</label>
                                                                    <div class="col-sm-9 pl-0 ">
                                                                        <input type="text" class="form-control"
                                                                            id="corporation_no" name="corporation_no"
                                                                            placeholder="Corporation #">
                                                                    </div>
                                                                    <div class="position-absolute custom-control custom-  custom-control-  custom-control-lg mt-2 "
                                                                        style="right: -30px;" data-toggle="tooltip"
                                                                        data-title="Use for reports" data-original-title=""
                                                                        title="">
                                                                        <input type="checkbox" class="custom-control-input"
                                                                            id="corporation_checkbox"
                                                                            name="corporation_checkbox" value="1">
                                                                        <label class="custom-control-label"
                                                                            for="corporation_checkbox"> </label>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row ">
                                                                    <label class="col-sm-3 pr-0 col-form-label mandatory "
                                                                        style="margin-right: -4px !important;"
                                                                        for="example-hf-client_id">Business</label>
                                                                    <div class="col-sm-9 pl-0 ">
                                                                        <select type="text" class="form-control"
                                                                            id="business" name="business"
                                                                            placeholder="Salutation">
                                                                            <option value="">Select business type
                                                                            </option>
                                                                            <option value="Agriculture and Farming">
                                                                                Agriculture and Farming</option>
                                                                            <option value="Automotive">Automotive</option>
                                                                            <option value="Construction">Construction
                                                                            </option>
                                                                            <option value="Consulting">Consulting</option>
                                                                            <option value="Education and Training">Education
                                                                                and Training</option>
                                                                            <option value="Energy and Utilities">Energy and
                                                                                Utilities</option>
                                                                            <option value="Entertainment">Entertainment
                                                                            </option>
                                                                            <option value="Finance and Banking">Finance and
                                                                                Banking</option>
                                                                            <option value="Food and Beverage">Food and
                                                                                Beverage</option>
                                                                            <option value="Government and Non-Profit">
                                                                                Government and Non-Profit</option>
                                                                            <option value="Healthcare">Healthcare</option>
                                                                            <option value="Hospitality and Tourism">
                                                                                Hospitality and Tourism</option>
                                                                            <option value="Information Technology">
                                                                                Information Technology</option>
                                                                            <option value="Marketing and Advertising">
                                                                                Marketing and Advertising</option>
                                                                            <option value="Media and Communications">Media
                                                                                and Communications</option>
                                                                            <option value="Professional Services">
                                                                                Professional Services</option>
                                                                            <option
                                                                                value="Real Estate and Property Management">
                                                                                Real Estate and Property Management</option>
                                                                            <option value="Retail and Consumer Goods">Retail
                                                                                and Consumer Goods</option>
                                                                            <option value="Transportation and Logistics">
                                                                                Transportation and Logistics</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3  pr-0 col-form-label mandatory"
                                                                        style="margin-right: -4px !important;"
                                                                        for="example-hf-client_id">Federal Tax #</label>
                                                                    <div class="col-sm-9 pl-0 ">
                                                                        <input type="text" class="form-control"
                                                                            id="federal_no" name="federal_no"
                                                                            placeholder="Federal enterprise number">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3  pr-0 col-form-label mandatory"
                                                                        style="margin-right: -4px !important;"
                                                                        for="example-hf-client_id">Provincial Tax # </label>
                                                                    <div class="col-sm-9 pl-0 ">
                                                                        <input type="text" class="form-control"
                                                                            id="provincial_no" name="provincial_no"
                                                                            placeholder="Provincial enterprise number">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-sm-3  pr-0 col-form-label"
                                                                        style="margin-right: -4px !important;"
                                                                        for="example-hf-client_id">NEQ # </label>
                                                                    <div class="col-sm-9 pl-0 ">
                                                                        <input type="text" class="form-control"
                                                                            id="neq_no" name="neq_no"
                                                                            placeholder="Neq number">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="avatar-upload float-right">
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
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 py-2">
                                                        <h3 class="  section-header ml-n2"
                                                            style="margin-left: -9px !important;">Contact Information
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="email">Email</label>
                                                    <div class="col-sm-10  ">
                                                        <input type="" class="form-control" id="email"
                                                            name="email" placeholder="Company e-mail address">
                                                    </div>
                                                </div>
                                                <div class="form-group row ">
                                                    <label class="col-sm-2 mandatory  col-form-label"
                                                        for="example-hf-email">Telephone </label>
                                                    <div class="col-sm-3  ">
                                                        <input type="" class="form-control" id="telephone"
                                                            name="telephone" placeholder="555-555-5555">
                                                    </div>
                                                    <label class="col-sm-2 text-center    col-form-label"
                                                        for="example-hf-email">Fax </label>
                                                    <div class="col-sm-3 ">
                                                        <input type="" class="form-control" id="fax"
                                                            name="fax" placeholder="555-555-5555">
                                                    </div>
                                                </div>
                                                <div class="form-group f p  row">
                                                    <label class="col-sm-2   col-form-label" for="example-hf-email">Website
                                                    </label>
                                                    <div class="col-sm-10 ">
                                                        <input type="url" class="form-control" id="website"
                                                            name="website" placeholder="https://www.web.url">
                                                    </div>
                                                </div>
                                                <div class="row form-group ">
                                                    <label class="col-sm-2  mandatory col-form-label"
                                                        for="example-hf-email">Country </label>
                                                    <div class="col-sm-6 ">
                                                        <select type="text" class="form-control select2" id="country"
                                                            name="country" placeholder="">
                                                            <?php $use = DB::Table('countries')->get(); ?>
                                                            <option value="">Country</option>
                                                            @foreach ($use as $u)
                                                                <option value="{{ $u->name }}"
                                                                    {{ $u->name == 'Canada' ? 'selected' : '' }}>
                                                                    {{ $u->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Address</label>
                                                    <div class="col-sm-10 ">
                                                        <textarea class="form-control" rows="5" id="address" name="address" placeholder="Address"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">City</label>
                                                    <div class="col-sm-6 ">
                                                        <input class="form-control" id="city" name="city"
                                                            placeholder="City">
                                                    </div>
                                                </div>
                                                <?php
                                                $city_qry = DB::Table('cities')->where('country_name', 'Canada')->groupBy('state_name')->get();
                                                ?>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Province</label>
                                                    <div class="col-sm-4 ">
                                                        <select class="form-control select2" id="province" name="province">
                                                            <option value="">Province</option>
                                                            @foreach ($city_qry as $p)
                                                                <option value="{{ $p->state_name }}"
                                                                    {{ $p->state_name == 'Quebec' ? 'selected' : '' }}>
                                                                    {{ $p->state_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Postal Code</label>
                                                    <div class="col-sm-4 ">
                                                        <input class="form-control" id="postal_code" name="postal_code"
                                                            placeholder="A9A 980">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 py-2">
                                                        <h3 class="  section-header ml-n2">Remittance Information
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label" for=" ">Fiscal
                                                        Start</label>
                                                    <div class="col-sm-4  ">
                                                        <input class="form-control js-flatpickr bg-white" id="fiscal_start"
                                                            name="fiscal_start" placeholder="Fiscal start date"
                                                            data-alt-input="true" data-date-format="Y-m-d"
                                                            data-alt-format="Y-M-d">
                                                    </div>
                                                    <label class="col-sm-2   col-form-label" for=" ">Fiscal Year
                                                        End</label>
                                                    <div class="col-sm-3  ">
                                                        <div class="bubble-white-new2  fiscalEnd w-100"></div><input
                                                            type="hidden" name="fiscal_year_end" id="fiscal_year_end">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Default Prov</label>
                                                    <div class="col-sm-4 ">
                                                        <select class="form-control select2" id="default_province"
                                                            name="default_province">
                                                            <option value="">Province</option>
                                                            @foreach ($city_qry as $p)
                                                                <option value="{{ $p->state_name }}"
                                                                    {{ $p->state_name == 'Quebec' ? 'selected' : '' }}>
                                                                    {{ $p->state_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2   col-form-label" for=" ">Frequency</label>
                                                    <div class="col-sm-3 ">
                                                        <input class="js-rangeslider" id="tax_remittance"
                                                            name="tax_remittance" value="Monthly"
                                                            data-values="No,Yearly,Quarterly,Monthly" data-from="4">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Federal Tax Acct</label>
                                                    <div class="col-sm-4 ">
                                                        <select class="form-control select2" id="federal_tax"
                                                            name="federal_tax">
                                                            @foreach ($gifi as $g)
                                                                <option class="{{ $g->id }}"
                                                                    {{ $g->account_no == '2500' ? 'selected' : '' }}>
                                                                    {{ $g->account_no }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Provincial Tax Acct</label>
                                                    <div class="col-sm-4 ">
                                                        <select class="form-control select2" id="provincial_tax"
                                                            name="provincial_tax">
                                                            @foreach ($gifi as $g)
                                                                <option class="{{ $g->id }}"
                                                                    {{ $g->account_no == '2510' ? 'selected' : '' }}>
                                                                    {{ $g->account_no }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-sm-2 mandatory col-form-label"
                                                        for="example-hf-email">Dividends Account</label>
                                                    <div class="col-sm-4 ">
                                                        <select class="form-control select2" id="dividends_account"
                                                            name="dividends_account">
                                                            @foreach ($gifi as $g)
                                                                <option class="{{ $g->id }}"
                                                                    {{ $g->account_no == '3200' ? 'selected' : '' }}>
                                                                    {{ $g->account_no }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="block-content block-content-full text-right  pt-4"
                                        style="padding-left: 9mm;padding-right: 9mm">
                                        <button type="submit" class="btn mr-3 btn-new ">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                </form>

                <div class="modal fade" id="CommentModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Add Comments</span>
                                    <div class="block-options">
                                        <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                    <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                </button> -->
                                    </div>
                                </div>
                                <div class="block-content pt-0 row">
                                    <div class="col-sm-12    p      ">
                                        <textarea class="form-control  " rows="4" required="" name="comment"></textarea>
                                    </div>
                                </div>
                                <div class="block-content block-content-full   " style="padding-left: 9mm;">
                                    <button type="button" class="btn mr-3 btn-new" id="CommentSave">Save</button>
                                    <button type="button" class="btn     btn-new-secondary"
                                        data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="CommentModalEdit" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header  ">
                                    <span class="b e section-header">Edit Comments</span>
                                    <div class="block-options">
                                        <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                    <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                </button> -->
                                    </div>
                                </div>
                                <div class="block-content pt-0 row">
                                    <input type="hidden" name="comment_id_edit">
                                    <div class="col-sm-12      ">
                                        <textarea class="form-control  " rows="4" required="" name="comment_edit"></textarea>
                                    </div>
                                </div>
                                <div class="block-content block-content-full   " style="padding-left: 9mm;">
                                    <button type="button" class="btn mr-3 btn-new" id="CommentSaveEdit">Save</button>
                                    <button type="button" class="btn     btn-new-secondary"
                                        data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="AttachmentModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Add Attachment</span>
                                    <div class="block-options">
                                        <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                    <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                </button> -->
                                    </div>
                                </div>
                                <div class="block-content pt-0 row">
                                    <div class="col-sm-12    p      ">
                                        <input type="file" class="  attachment" multiple="" style=""
                                            id="attachment" name="attachment" placeholder="">
                                    </div>
                                </div>
                                <div class="block-content block-content-full   " style="padding-left: 9mm;">
                                    <button type="button" class="btn mr-3 btn-new" id="AttachmentSave">Save</button>
                                    <button type="button" class="btn     btn-new-secondary" id="AttachmentClose"
                                        data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="{{ url('/get-clients-account') }}" id="filterClientAccountForm" method="GET"
                    class="mb-0 pb-0">
                    <div class="modal fade" id="filterClientAccountModal" tabindex="-1" role="dialog"
                        data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                            <div class="modal-content">
                                <div class="block  block-transparent mb-0">
                                    <div class="block-header pb-0  " style="padding-top:20px;">
                                        <span class="b e section-header">Filters</span>
                                        <div class="block-options">
                                            <button type="button" class="btn-block-option" data-dismiss="modal"
                                                aria-label="Close">
                                                <i class="fa fa-fw fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="block-content new-block-content pt-0 pb-0 ">
                                        <div class="row">
                                            <label class="col-sm-3">Account Type</label>
                                            <div class="col-sm-6 form-group">
                                                <select type="" name="account_type" class="form-control"
                                                    placeholder="">
                                                    <option value="Asset"
                                                        @if (@$_GET['account_type'] == 'Asset') selected @endif>
                                                        Asset</option>
                                                    <option value="Liability"
                                                        @if (@$_GET['account_type'] == 'Liability') selected @endif>
                                                        Liability</option>
                                                    <option value="Retained Earning"
                                                        @if (@$_GET['account_type'] == 'Retained Earning') selected @endif>
                                                        Retained Earning</option>
                                                    <option value="Revenue"
                                                        @if (@$_GET['account_type'] == 'Revenue') selected @endif>
                                                        Revenue</option>
                                                    <option value="Expense"
                                                        @if (@$_GET['account_type'] == 'Expense') selected @endif>
                                                        Expense</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-3">Sub Type</label>
                                            <div class="col-sm-6 form-group">
                                                <select type="" class="form-control    selectpicker "
                                                    id="sub_account_type" data-style="btn-outline-light border text-dark"
                                                    data-actions-box="true" data-live-search="true" title="All"
                                                    value="" name="sub_account_type[]" multiple="">
                                                    <?php
                                                    $sub_account_type = DB::Table('sub_account_type')->where('account_type', 'Asset')->get();
                                                    foreach ($sub_account_type as $s) {
                                                        echo '<option value="' . $s->sub_type . '"  data-min="' . $s->min . '"  data-max="' . $s->max . '">' . $s->sub_type . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class=" col-sm-3  " for="example-hf-email">Account No</label>
                                            <div class="col-sm-6 form-group">
                                                <select type="" class="form-control    selectpicker "
                                                    id="filter_account" data-style="btn-outline-light border text-dark"
                                                    data-actions-box="true" data-live-search="true" title="All"
                                                    value="" name="account[]" multiple="">
                                                    <?php
                                                    $filter_accounts = DB::table('gifi')->where('is_deleted', 0)->where('account_type', 'Asset')->orderBy('account_no')->get();
                                                    ?>
                                                    @foreach ($filter_accounts as $fa)
                                                        <option value="{{ $fa->account_no }}">{{ $fa->account_no }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="block-content block-content-full text-right  pt-4"
                                        style="padding-left: 9mm;">
                                        <a href="javascript:;" id="gifi-clear-filter-1"
                                            class="btn     btn-new-secondary float-right mr-3 " style="">Clear
                                        </a>
                                        <button type="submit" class="btn  btn-new">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="modal fade" id="closeYearConfirmModel" tabindex="-1" role="dialog"
                    data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-bac" style="max-width: 450px;" role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Close Fiscal Year </span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"
                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 confirmation-container">

                                    <h5 class="text-info total-journals text-center mb-2" style="font-family: Signika;">0

                                        journals

                                        found</h5>

                                    <p class=" found-journals mb-3" style="font-family: Signika;">To confirm closing this
                                        year, enter the amount of journals
                                        found below and click on Yes</p>
                                    <p class=" found-journals mb-3 text-center" style="font-family: Signika; color: red;">
                                        IMPORTANT: This operation cannot be undone!</p>

                                    <div class="pt-2 mb-3" style="display: flex; justify-content: center">

                                        <input type="number" class="form-control" style="max-width: 120px;"
                                            name="journal_count" id="journal_count">

                                    </div>

                                </div>

                                <div class="block-content block-content-full pt-2 px-7"
                                    style=" display: flex; justify-content: space-around;">

                                    <button type="button"
                                        class="btn btn-new font-calibri btn-submit-close-year">Yes</button>

                                    <button type="button" class="btn btn-new font-calibri" data-dismiss="modal"
                                        style="color: #C41E3A;">No</button>

                                </div>

                            </div>



                        </div>

                    </div>

                </div>

                <div class="modal fade" id="closeYearDetailModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document"
                        style="width: 550px;">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  " style="padding-top:20px;">
                                    <span class="b e section-header" style="margin-left: 12px;">Close Fiscal Year</span>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal"
                                            aria-label="Close">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 "
                                    style="padding-top: 3mm !important;">
                                    <div class="block-content pb-0 new-block-content px-2"
                                        style="padding-top: 0mm !important;">
                                        <div class="row justify-content-  push">
                                            <div class="col-sm-12 m-">
                                                <div class="row">
                                                    <div class="col-sm-12 d-flex align-items-center mb-4">

                                                        <img width="34px" height="34px" style="object-fit:cover;"
                                                            class="rounded mr-3 client-img"
                                                            src="public/client_logos/7481734981605.png">
                                                        <div>
                                                            <h2 class="mb-0 comments-text"> <span class="client-name">Ilyas
                                                                    Oudhini</span> <br><span class="comments-subtext date">On
                                                                    2024-Dec-07
                                                                    at 09:42:31 PM GMT
                                                                </span></h2>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12">
                                                        <div class="form-group row">
                                                            <label class="col-sm-6 pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Fiscal
                                                                Year to Close </label>
                                                            <?php ?>
                                                            <div class="col-sm-6">
                                                                <div class="bubble-new ending_year">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-6  pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Number
                                                                of Journals</label>
                                                            <div class="col-sm-6 ">
                                                                <div class="bubble-new no_of_journals">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12 py-2">
                                                        <h3 class="  section-header ml-n2"
                                                            style="margin-left: -9px !important;">Statement of Retained
                                                            Earnings</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Retained
                                                                Earnings,
                                                                beginning of period</label>
                                                            <div class="col-sm-2">
                                                                <a href="javascript:;"
                                                                    style="position: absolute;

                                    

                                    top: 10px;"
                                                                    class="hover-info"
                                                                    data-standard="Retained Earnings, beginning of period"
                                                                    data-translated="Opening balance of retained earnings (Account #3100)"
                                                                    data-original-title="" title="">

                                                                    <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                                        width="20px">

                                                                </a>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="bubble-new retained_earnings">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Net
                                                                Income</label>
                                                            <div class="col-sm-2">
                                                                <a href="javascript:;"
                                                                    style="position: absolute;

                                    

                                    top: 10px;"
                                                                    class="hover-info" data-standard="Net Income"
                                                                    data-translated="Total Revenue minus Expenses"
                                                                    data-original-title="" title="">

                                                                    <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                                        width="20px">

                                                                </a>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="bubble-new close_net_income">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Items
                                                                affecting
                                                                retained earnings</label>
                                                            <div class="col-sm-2">
                                                                <a href="javascript:;"
                                                                    style="position: absolute;

                                    

                                    top: 10px;"
                                                                    class="hover-info"
                                                                    data-standard="Items affecting retained earnings"
                                                                    data-translated="All journals where Sub-Type = ‘Retained earning/deficit’ with the exception of Account#’s 3100 and 3200"
                                                                    data-original-title="" title="">

                                                                    <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                                        width="20px">

                                                                </a>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="bubble-new items_effecting">

                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label class="col-sm-4 pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Dividends
                                                                (owner’s
                                                                equity)</label>
                                                            <div class="col-sm-2">
                                                                <a href="javascript:;"
                                                                    style="position: absolute;

                                    

                                    top: 10px;"
                                                                    class="hover-info"
                                                                    data-standard="Dividends (owner’s equity)"
                                                                    data-translated="All journals posted to Account# 3200"
                                                                    data-original-title="" title="">

                                                                    <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                                        width="20px">

                                                                </a>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="bubble-new dividends">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="dashed-seperater form-group"></div>
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 pr-0 col-form-label"
                                                                style="line-height: 1;" for="example-hf-client_id">Retained
                                                                earnings, end of
                                                                period</label>
                                                            <div class="col-sm-2"> <a href="javascript:;"
                                                                    style="position: absolute;

                                    

                                                                top: 10px;"
                                                                    class="hover-info-2"
                                                                    data-standard="Retained earnings, end of period"
                                                                    data-translated="Retained earnings, start
                            + Net Income
                            + Items affecting retained earnings
                            + Dividends (owner’s equity)"
                                                                    data-original-title="" title="">

                                                                    <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                                        width="20px">

                                                                </a> </div>
                                                            <div class="col-sm-6">
                                                                <div class="bubble-new end_of_period">

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
                        </div>
                    </div>
                </div>
        </main>
        <!-- END Main Container -->
    @endsection('content')
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('public/js/moment.min.js') }}"></script>
    <script type="text/javascript">
        let click_edit_account_form = 0;

        $(document).on('click', '.btn-detail', function() {
            var date = $(this).attr('data-date');
            var endingYear = $(this).attr('data-year');
            var img = $(this).attr('data-img');
            var client = $(this).attr('data-closed-by');
            var count = $(this).attr('data-count');
            var retained_earnings = $(this).attr('data-retained-earnings');
            var net_income = $(this).attr('data-net-income');
            var items_effecting = $(this).attr('data-items-effecting');
            var dividends = $(this).attr('data-dividends');
            var end_of_period = $(this).attr('data-end-of-period');
            var imgSrc = '{{ asset('public') }}/img/profile-white.png';
            if (img) {
                imgSrc = '{{ asset('public') }}/client_logos/' + img;
            }

            $('#closeYearDetailModal .client-img').attr('src', imgSrc);
            $('#closeYearDetailModal .client-name').text(client);
            $('#closeYearDetailModal .date').text(date);
            $('#closeYearDetailModal .ending_year').text(endingYear);
            $('#closeYearDetailModal .no_of_journals').text(count);
            $('#closeYearDetailModal .retained_earnings').text(retained_earnings);
            $('#closeYearDetailModal .close_net_income').text(net_income);
            $('#closeYearDetailModal .items_effecting').text(items_effecting);
            $('#closeYearDetailModal .dividends').text(dividends);
            $('#closeYearDetailModal .end_of_period').text(end_of_period);
            $('#closeYearDetailModal').modal('show');
        })

        $(document).on('click', '.select-close-year', function() {
            var client = $(this).attr('data-client');
            $('#AddCloseYearModal').modal('show');
            getCloseYearCntent();
        })
        $(document).on('change', '#close_year', function() {
            getCloseYearCntent();
        })

        function getCloseYearCntent() {
            var client = $('#close_year option:selected').attr('data-client');
            var year = $('#close_year option:selected').val();
            if (client != '' && client != undefined && year != '' && year != undefined) {
                $.ajax({
                    type: 'get',
                    url: '{{ url('get-close-year-content') }}',
                    dataType: 'json',
                    data: {
                        client: client,
                        year: year
                    },
                    success: function(res) {
                        $('.no_of_journals').text(res.no_of_journals.toLocaleString('en-US', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }));
                        $('#no_of_journals').val(res.no_of_journals);
                        $('.end_of_period').text(formatCurrency(res.total));
                        $('#end_of_period').val(parseFloat(res.total).toFixed(2));
                        $('.retained_earnings').text(formatCurrency(res.total_retained_earnings));
                        $('#retained_earnings').val(parseFloat(res.total_retained_earnings).toFixed(2));
                        $('.close_net_income').text(formatCurrency(res.net_income));
                        $('#close_net_income').val(parseFloat(res.net_income).toFixed(2));
                        $('.items_effecting').text(formatCurrency(res.total_items));
                        $('#items_effecting').val(parseFloat(res.total_items).toFixed(2));
                        $('.dividends').text(formatCurrency(res.total_dividend));
                        $('#dividends').val(parseFloat(res.total_dividend).toFixed(2));
                    },
                    error: function() {}
                })
            }
        }

        function formatCurrency(amount) {
            const isNegative = amount < 0;
            const formatted = Math.abs(amount).toLocaleString('en-US', {
                style: 'currency',
                currency: 'USD',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            return isNegative ? `(${formatted.replace('$', '$ ')})` : formatted.replace('$', '$ ');
        }

        $(document).on('click', '.btn-close-proceed', function() {
            var year = $('#AddCloseYearModal #close_year option:selected').val();
            var noOfJournals = $('#AddCloseYearModal #no_of_journals').val();
            $('#closeYearConfirmModel #count').val(noOfJournals);
            $('#closeYearConfirmModel .section-header').text(`Close Fiscal Year ${year}`);
            $('#closeYearConfirmModel .total-journals').text(`${noOfJournals} journals found`);

            $('#AddCloseYearModal').modal('hide');
            $('#closeYearConfirmModel').modal('show');
        })
        $(document).on('click', '.btn-submit-close-year', function() {
            var journals = $('#journal_count').val();
            var noOfJournals = $('#AddCloseYearModal #no_of_journals').val();

            if (journals == noOfJournals) {
                var $btn = $(this);

                var originalContent = $btn.html();

                if ($btn.hasClass('disabled')) {

                    return;

                }



                $btn.addClass('expanded disabled');

                $btn.html(

                    '<i class="fa fa-cog spinner-blue text-white"></i> Processing...'

                );
                setTimeout(() => {
                    $('#form-close-year').submit();
                }, 100);
            } else {
                Dashmix.helpers('notify', {
                    type: 'info alert-notify-desktop',
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please input correct number of journals.',
                    delay: 5000
                });
            }
        })

        function resetCloseYear() {
            $('.no_of_journals').text('');
            $('#no_of_journals').val('');
            $('.end_of_period').text('');
            $('#end_of_period').val('');
            $('.retained_earnings').text('');
            $('#retained_earnings').val('');
            $('.close_net_income').text('');
            $('#close_net_income').val('');
            $('.items_effecting').text('');
            $('#items_effecting').val('');
            $('.dividends').text('');
            $('#dividends').val('');
        }

        function getClientAccounts(id, searchVal = '', filters = {}) {
            let data = {
                id: id,
                searchVal: searchVal,
            }
            if (Object.keys(filters).length != 0) {
                data = Object.assign({}, data, filters);
            }
            $.ajax({
                type: 'get',
                data: data,
                url: '{{ url('cleints-get-clients-account') }}',
                beforeSend() {
                    Dashmix.layout('header_loader_on');
                },
                success: function(res) {
                    Dashmix.layout('header_loader_off');
                    $('.clientFirstDiv').addClass('d-none')
                    $('.clientSecondDiv').html(res);
                    $('.tooltip').tooltip('hide');
                    Dashmix.helpers('rangeslider')
                    $('#fiscal_start_edit').flatpickr()
                    $('[data-toggle=tooltip]').tooltip();
                }
            })
        }
        $(function() {
            $(document).on('click', '.btn-export', function() {
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
                        '<i class="fa fa-cog spinner text-white" style="font-size: 20px;"></i>'
                    );
                    setTimeout(() => {
                        $.ajax({
                            type: 'get',
                            url: url,
                            dataType: 'json',
                            beforeSend() {
                                Dashmix.layout('header_loader_on');
                            },
                            success: function(res) {
                                console.log(res);
                                if (res.success) {
                                    window.location.href = res.download_url;
                                }
                                $('[data-toggle=tooltip]').tooltip();
                                $btn.removeClass('expanded disabled').html(
                                    originalContent);
                            },
                            error: function(xhr) {
                                $btn.removeClass('expanded disabled').html(
                                    originalContent);
                                console.log("Error:", xhr.responseText);
                            }
                        })
                    }, 100);
                }
            })
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
                const alert = '{{ Session::get('alert-delete') }}';
                const message = alert.split("|")[0];
                const id = alert.split("|")[1];
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: message + ' <a href="javascript:;" data="' + id +
                        '" data-notify="dismiss" class="  btn-notify undo-delete ml-4" >Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
            @endif
            showData('{{ @$GETID }}');

            function showData(id) {
                $('.clientFirstDiv').removeClass('d-none')
                $('.clientSecondDiv').html('');
                $('.c-active').removeClass('c-active');
                if (id) {
                    $('.viewContent[data=' + id + ']').addClass('c-active');
                    $('.c4').css({
                        'backgroundColor': '#D9D9D9',
                        'color': '#7F7F7F',
                        'borderColor': '#7F7F7F'
                    })
                    $('.c4[data=' + id + ']').css({
                        'backgroundColor': '#97C0FF',
                        'color': '#595959',
                        'borderColor': '#595959'
                    })
                }
                $.ajax({
                    type: 'get',
                    data: {
                        id: id
                    },
                    url: '{{ url('get-clients-content') }}',
                    dataType: 'json',
                    beforeSend() {
                        Dashmix.layout('header_loader_on');
                    },
                    success: function(res) {
                        Dashmix.layout('header_loader_off');
                        $('#showData').html(res);
                        $('.tooltip').tooltip('hide');
                        Dashmix.helpers('rangeslider')
                        $('[data-toggle=tooltip]').tooltip();
                    }
                })
            }

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

            function readURL1(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.imagePreview1').css('background-image', 'url(' + e.target.result + ')');
                        $('.imagePreview1').hide();
                        $('.imagePreview1').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function validatePhone(value) {
                // Regular expression pattern to match the desired formats
                var pattern = /^(\d{1}-)?(\d+-)?\d+(-\d+)*$/;
                return pattern.test(value) && value.length <= 14;
            }
            // $(document).on('change', '#telephone, #telephone_edit, #fax, #fax_edit', function () {
            //     const value = $(this).val();
            //     if(!validatePhone(value)) {
            //         $(this).val('');
            //     }
            // });
            $("#imageUpload").change(function() {
                readURL(this);
            });
            $(document).on('change', '.imageUpload1', function() {
                readURL1(this);
            });
            $(document).on('mouseenter', '.hover-info', function() {
                console.log('enter tooltip');

                const $this = $(this);
                const data_standard = $this.attr('data-standard');
                const data_translated = $this.attr('data-translated');

                const html = `
        <div class="mb-3 d-flex justify-content-between"><div class="tooltip-head-text w-75">${data_standard}</div><div class=""><img src="{{ asset('public') }}/icons2/icon-info.png?cache=1" width="20px"></div></div>
        <p class="mb-2"><span class="col-form-label" style="font-family: Calibri !important; line-height: 1;">${data_translated}</span></p>
        `;

                $this.tooltip({
                    title: html,
                    html: true,
                    placement: 'bottom',
                    trigger: 'manual',
                    template: `
            <div class="tooltip custom-tooltip" role="tooltip">
                <div class="arrow"></div>
                <div class="tooltip-inner" style="box-shadow: none !important;"></div>
                    </div>
                    `
                });

                $this.tooltip('show');
            });

            $(document).on('mouseleave', '.hover-info', function() {
                console.log('out tooltip');
                $(this).tooltip('dispose');
            });
            $(document).on('mouseenter', '.hover-info-2', function() {
                console.log('enter tooltip');

                const $this = $(this);
                const data_standard = $this.attr('data-standard');
                const data_translated = $this.attr('data-translated');

                const html = `
        <div class="mb-3 d-flex justify-content-between"><div class="tooltip-head-text w-75">Retained earnings, end of period</div><div class=""><img src="{{ asset('public') }}/icons2/icon-info.png?cache=1" width="20px"></div></div>
        <p class="mb-2"><span class="col-form-label" style="font-family: Calibri !important; line-height: 1;">Retained earnings, start <br> + Net Income <br> + Items affecting retained earnings <br> + Dividends (owner’s equity)</span></p>
        `;

                $this.tooltip({
                    title: html,
                    html: true,
                    placement: 'bottom',
                    trigger: 'manual',
                    template: `
            <div class="tooltip custom-tooltip" role="tooltip">
                <div class="arrow"></div>
                <div class="tooltip-inner" style="box-shadow: none !important;"></div>
                    </div>
                    `
                });

                $this.tooltip('show');
            });

            $(document).on('mouseleave', '.hover-info-2', function() {
                console.log('out tooltip');
                $(this).tooltip('dispose');
            });
            $('select[name=country]').change(function() {
                var country = $(this).val();
                $.ajax({
                    type: 'get',
                    data: {
                        country: country
                    },
                    url: "{{ url('get-province') }}",
                    asnyc: false,
                    success: function(res) {
                        var html = '';
                        html += '<option value="">Province</option>';
                        for (var i = 0; i < res.length; i++) {
                            html += '<option value="' + res[i].state_name + '">' + res[i]
                                .state_name + '</option>';
                        }
                        $('select[name=province]').select2('destroy');
                        $('select[name=province]').html(html);
                        $('select[name=province]').select2();
                        $('select[name=default_province]').select2('destroy');
                        $('select[name=default_province]').html(html);
                        $('select[name=default_province]').select2();
                    }
                })
            })
            $(document).on('change', 'select[name=country_edit]', function() {
                var country = $(this).val();
                $.ajax({
                    type: 'get',
                    data: {
                        country: country
                    },
                    url: "{{ url('get-province') }}",
                    asnyc: false,
                    success: function(res) {
                        var html = '';
                        html += '<option value="">Province</option>';
                        for (var i = 0; i < res.length; i++) {
                            html += '<option value="' + res[i].state_name + '">' + res[i]
                                .state_name + '</option>';
                        }
                        // $('select[name=province_edit]').select2('destroy');
                        $('select[name=province_edit]').html(html);
                        //$('select[name=province_edit]').select2();
                        //$('select[name=default_province_edit]').select2('destroy');
                        $('select[name=default_province_edit]').html(html);
                        //$('select[name=default_province_edit]').select2();
                    }
                })
            })
            $('#slider1').change(function() {
                if ($(this).val() == 1) {
                    $('.TaxDiv1').removeClass('d-none')
                    $('.TaxDiv2').addClass('d-none')
                    $('input[name=tax_label_2]').val('')
                    $('input[name=tax_rate_2]').val('')
                    $('#applied_to_tax1').prop('checked', false);
                } else if ($(this).val() == 2) {
                    $('.TaxDiv1').removeClass('d-none')
                    $('.TaxDiv2').removeClass('d-none')
                } else {
                    $('.TaxDiv1').addClass('d-none')
                    $('.TaxDiv2').addClass('d-none')
                    $('input[name=tax_label_2]').val('')
                    $('input[name=tax_rate_2]').val('')
                    $('#applied_to_tax1').prop('checked', false);
                    $('input[name=tax_label_1]').val('')
                    $('input[name=tax_rate_1]').val('')
                }
            })
            $(document).on('change', '.slider2', function(e) {
                if ($(this).val() == 1) {
                    $('.TaxDiv1_edit').removeClass('d-none')
                    $('.TaxDiv2_edit').addClass('d-none')
                    $('input[name=tax_label_2_edit]').val('')
                    $('input[name=tax_rate_2_edit]').val('')
                    $('#applied_to_tax1_edit').prop('checked', false);
                } else if ($(this).val() == 2) {
                    $('.TaxDiv1_edit').removeClass('d-none')
                    $('.TaxDiv2_edit').removeClass('d-none')
                } else {
                    $('.TaxDiv1_edit').addClass('d-none')
                    $('.TaxDiv2_edit').addClass('d-none')
                    $('input[name=tax_label_2_edit]').val('')
                    $('input[name=tax_rate_2_edit]').val('')
                    $('#applied_to_tax1_edit').prop('checked', false);
                    $('input[name=tax_label_1_edit]').val('')
                    $('input[name=tax_rate_1_edit]').val('')
                }
            })
            $('#type').change(function() {
                if ($(this).val() == 'Corporation') {
                    $('.CorporationDiv').removeClass('d-none');
                } else {
                    $('.CorporationDiv').addClass('d-none');
                }
            })
            $(document).on('change', 'select[name=type_edit]', function() {
                if ($(this).val() == 'Corporation') {
                    $('.CorporationDivEdit').removeClass('d-none');
                } else {
                    $('.CorporationDivEdit').addClass('d-none');
                }
            })
            $('#fiscal_start').change(function() {
                const selectedDate = moment($(this).val(), "YYYY-MM-DD");
                const lastMonth = selectedDate.clone().subtract(1, 'month').endOf('month');
                const monthName = lastMonth.format("MMMM");
                $('#fiscal_year_end').val(monthName + " " + lastMonth.date());
                $('.fiscalEnd').html(monthName + " " + lastMonth.date());
            })
            $(document).on('change', '.fiscal_start_edit', 'change', function() {
                const selectedDate = moment($(this).val(), "YYYY-MM-DD");
                const lastMonth = selectedDate.clone().subtract(1, 'month').endOf('month');
                const monthName = lastMonth.format("MMMM");
                $('#fiscal_year_end_edit').val(monthName + " " + lastMonth.date());
                $('.fiscalEnd').html(monthName + " " + lastMonth.date());
            })

            function validateFiscalStart(value) {
                //const selectedDate = new Date(value);
                selectedDate = value.split("-");
                console.log(selectedDate)
                if (selectedDate[2] == '1' || selectedDate[2] == '01') {
                    return 1;
                }
                return 0;
            }
            $('#form-add-tax').submit(function(e) {
                e.preventDefault();
                var firstname = $('input[name=firstname]').val();
                var lastname = $('input[name=lastname]').val()
                var company = $('input[name=company]').val()
                var type = $('#type').val()
                var business = $('#business').val()
                var federal_no = $('input[name=federal_no]').val()
                var provincial_no = $('input[name=provincial_no]').val()
                var email = $('input[name=email]').val()
                var fax = $('input[name=fax]').val()
                var telephone = $('input[name=telephone]').val()
                var corporation_no = $('input[name=corporation_no]').val()
                var country = $('select[name=country]').val()
                var address = $('textarea[name=address]').val()
                var city = $('input[name=city]').val()
                var province = $('select[name=province]').val()
                var postal_code = $('input[name=postal_code]').val()
                var fiscal_start = $('input[name=fiscal_start]').val()
                var default_province = $('select[name=default_province]').val();
                var federal_tax = $("select[name=federal_tax]").val();
                var provincial_tax = $("select[name=provincial_tax]").val();
                var dividends_account = $("select[name=dividends_account]").val();
                var check = 1;
                var email_regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var tele_regex = /^\d{10}$/;
                var postal_regex = /^[A-Za-z0-9]{3}\s?[A-Za-z0-9]{3}$/;
                var tele_regex1 = /^.{0,65}$/;
                var fisc_regex = /^[A-Z0-9\-]+$/;
                if (firstname == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Firstname.',
                        delay: 5000
                    });
                } else if (lastname == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Lastname.',
                        delay: 5000
                    });
                } else if (company == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Company.',
                        delay: 5000
                    });
                } else if (type == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Type.',
                        delay: 5000
                    });
                } else if (type == 'Corporation' && corporation_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Corporation #.',
                        delay: 5000
                    });
                } else if (business == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Business.',
                        delay: 5000
                    });
                } else if (federal_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Federal No.',
                        delay: 5000
                    });
                }
                // else if (!fisc_regex.test(federal_no) || federal_no.length > 18) {
                //     Dashmix.helpers('notify', {
                //         from: 'bottom',
                //         align: 'left',
                //         message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Federal No is not valid format.',
                //         delay: 5000
                //     });
                else if (federal_no.length > 20) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Federal No is not valid format.',
                        delay: 5000
                    });
                } else if (provincial_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Provincial No.',
                        delay: 5000
                    });
                } else if (provincial_no.length > 20) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Provincial No is not valid format.',
                        delay: 5000
                    });
                    // else if (!fisc_regex.test(provincial_no) || provincial_no.length > 18) {
                    //     Dashmix.helpers('notify', {
                    //         from: 'bottom',
                    //         align: 'left',
                    //         message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Provincial No is not valid format.',
                    //         delay: 5000
                    //     });
                } else if (email == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Email.',
                        delay: 5000
                    });
                } else if (!email_regex.test(email)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Email must be valid email format.',
                        delay: 5000
                    });
                } else if (telephone == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Telephone.',
                        delay: 5000
                    });
                } else if (!validatePhone(telephone)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Telephone, numeric and dashes only 14 digits.',
                        delay: 5000
                    });
                } else if (country == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Country.',
                        delay: 5000
                    });
                } else if (address == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Address.',
                        delay: 5000
                    });
                } else if (city == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for City.',
                        delay: 5000
                    });
                } else if (province == '' || province == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Province.',
                        delay: 5000
                    });
                } else if (postal_code == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Postal Code.',
                        delay: 5000
                    });
                } else if (!postal_regex.test(postal_code)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Postal Code, alpha numeric 7 character (3 char,space,3 char)',
                        delay: 5000
                    });
                } else if (fiscal_start == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Fiscal Start.',
                        delay: 5000
                    });
                } else if (validateFiscalStart(fiscal_start) == 0) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Fiscal start date must be the first of the month. ',
                        delay: 5000
                    });
                } else if (default_province == '' || default_province == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Fiscal Default Province.',
                        delay: 5000
                    });
                } else if (federal_tax == '' || federal_tax == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select value for Federal Tax.',
                        delay: 5000
                    });
                } else if (provincial_tax == '' || provincial_tax == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select value for Provincial Tax.',
                        delay: 5000
                    });
                } else if (dividends_account == '' || dividends_account == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select value for Dividends Account.',
                        delay: 5000
                    });
                } else {
                    $('#form-add-tax')[0].submit()
                }
            })
            var attachments_file = [];
            var commentArray = [];
            var comment_key_count = 0;
            var attachmentArray = [];
            var attachment_key_count = 0;
            $(document).on('click', '.btnEdit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var id = $(this).attr('data');
                $('.c-active').removeClass('c-active');
                if (id) {
                    $('.viewContent[data=' + id + ']').addClass('c-active');
                    $('.c4').css({
                        'backgroundColor': '#D9D9D9',
                        'color': '#7F7F7F',
                        'borderColor': '#7F7F7F'
                    })
                    $('.c4[data=' + id + ']').css({
                        'backgroundColor': '#97C0FF',
                        'color': '#595959',
                        'borderColor': '#595959'
                    })
                }
                $.ajax({
                    type: 'get',
                    data: {
                        id: id
                    },
                    url: '{{ url('get-clients-edit-content') }}?c=' + Math.random(),
                    beforeSend() {
                        Dashmix.layout('header_loader_on');
                    },
                    success: function(res) {
                        Dashmix.layout('header_loader_off');
                        //$("#JournalHeader").addClass('d-none');
                        attachments_file = []
                        commentArray = [];
                        comment_key_count = 0;
                        attachmentArray = [];
                        attachment_key_count = 0
                        $('#showData').html(res);
                        $('.tooltip').tooltip('hide');
                        showCommentsAjax(id)
                        Dashmix.helpers('rangeslider')
                        $('#fiscal_start_edit').flatpickr()
                        $('[data-toggle=tooltip]').tooltip();
                    }
                })
                getClientAccounts(id);
            })
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
                } else {}
            })

            function updateQueryStringParameter(uri, key, value) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                if (uri.match(re)) {
                    return uri.replace(re, '$1' + key + "=" + value + '$2');
                } else {
                    return uri + separator + key + "=" + value;
                }
            }
            $(document).on('click', '.viewContent', function() {
                var id = $(this).attr('data');
                var oldURL = window.location.href;
                var type = id;
                if (history.pushState) {
                    var newUrl = updateQueryStringParameter(oldURL, 'id', id)
                    window.history.pushState({
                        path: newUrl
                    }, '', newUrl);
                }
                showData(id);
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
            $(document).on('change', '#form-edit-client-gifi select[name=account_type]', function() {
                var val = $('#form-edit-client-gifi select[name=account_type]').val();
                var sub = $('#form-edit-client-gifi select[name=account_type]').attr('sub');
                $.ajax({
                    type: 'get',
                    data: {
                        account: val
                    },
                    url: '{{ url('get-sub-account') }}',
                    async: false,
                    success: function(res) {
                        var html = '';
                        for (var i = 0; i < res.length; i++) {
                            html += '<option value="' + res[i].sub_type + '" data-min="' + res[
                                    i].min + '" data-max="' + res[i].max + '" >' + res[i]
                                .sub_type + '</option>';
                        }
                        console.log(res)
                        $('#form-edit-client-gifi select[name=sub_account_type]').html(html);
                        $(`#form-edit-client-gifi select[name=sub_account_type] option[value='${sub}']`)
                            .prop('selected', true);
                    }
                })
            })
            $('#form-add-client-gifi select[name=account_type]').change(function() {
                var val = $(this).val();
                $.ajax({
                    type: 'get',
                    data: {
                        account: val
                    },
                    url: '{{ url('get-sub-account') }}',
                    async: false,
                    success: function(res) {
                        var html = '';
                        for (var i = 0; i < res.length; i++) {
                            html += '<option value="' + res[i].sub_type + '" data-min="' + res[
                                    i].min + '" data-max="' + res[i].max + '" >' + res[i]
                                .sub_type + '</option>';
                        }
                        $('#form-add-client-gifi select[name=sub_account_type]').html(html);
                    }
                })
            })
            @if (isset($_GET['advance_search']) && @$_GET['client_id'] != '')
                run('{{ $_GET['client_id'] }}', 'on')
                var site_id = '<?php echo isset($_GET['site_id']) ? implode(',', $_GET['site_id']) : ''; ?>';
                var operating_system_id = '<?php echo isset($_GET['cert_issuer']) ? implode(',', $_GET['cert_issuer']) : ''; ?>';
                getVendor('{{ @$_GET['client_id'] }}', site_id.split(','), 'on')
            @endif
            function run(id, on) {
                $.ajax({
                    type: 'get',
                    data: {
                        id: id
                    },
                    url: '{{ url('getSiteByClientId') }}',
                    async: false,
                    success: function(res) {
                        var html = '';
                        var check = '<?php echo @$_GET['site_id'] ? implode(',', $_GET['site_id']) : ''; ?>';;
                        check = check.split(',');
                        for (var i = 0; i < res.length; i++) {
                            if (on) {
                                if (check.includes(String(res[i].id))) {
                                    html += '<option value="' + res[i].id + '" selected>' + res[i]
                                        .site_name + '</option>';
                                } else {
                                    html += '<option value="' + res[i].id + '" >' + res[i].site_name +
                                        '</option>';
                                }
                            } else {
                                html += '<option value="' + res[i].id + '" >' + res[i].site_name +
                                    '</option>';
                            }
                        }
                        $('#site_id').html(html);
                        $('#site_id').selectpicker('refresh');
                    }
                })
            }
            $('#client_id').change(function() {
                var id = $(this).val()
                run(id)
                getVendor(id);
            })
            $('.ActionIcon').mouseover(function() {
                var data = $(this).attr('data-src');
                $(this).find('img').attr('src', data);
            })
            $('.ActionIcon').mouseout(function() {
                var data = $(this).attr('data-original-src');
                $(this).find('img').attr('src', data);
            })
            $('#site_id').change(function() {
                var site_id = $(this).val();
                var client_id = $('#client_id').val()
                getVendor(client_id, site_id)
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
                if (status == 1) {
                    $('.revokeText').html('Deactivate')
                } else {
                    $('.revokeText').html('Reactivate')
                }
                $('#EndModal').modal('show')
            })
            $('#showdata').on('click', '.btnEdit2', function() {
                var id = $(this).attr('data');
                $.ajax({
                    type: 'get',
                    data: {
                        id: id
                    },
                    url: '{{ url('show-sla') }}',
                    success: function(res) {
                        $('#viewData').modal('show');
                        $('#cert_hostname').html(res.hostname)
                        $('#cert_status').html(res.cert_status != null ? res.cert_status
                            .toUpperCase() : '')
                        $('#cert_notification').html(res.cert_notification == '1' ?
                            '<div class="badge badge-success">On</div>' :
                            '<div class="badge badge-danger">Off</div>')
                        $('#cert_type').html(res.cert_type != null ? res.cert_type
                            .toUpperCase() : '')
                        $('#cert_issuer').html(res.cert_issuer)
                        if (res.attachment != '' && res.attachment != null) {
                            ht = '';
                            var attachments = res.attachment.split(',');
                            for (var i = 0; i < attachments.length; i++) {
                                var icon = 'fa-file';
                                var fileExtension = attachments[i].split('.').pop();
                                if (fileExtension == 'pdf') {
                                    icon = 'fa-file-pdf';
                                } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                                    icon = 'fa-file-word'
                                } else if (fileExtension == 'txt') {
                                    icon = 'fa-file-alt';
                                } else if (fileExtension == 'csv' || fileExtension == 'xlsx' ||
                                    fileExtension == 'xlsm' || fileExtension == 'xlsb' ||
                                    fileExtension == 'xltx') {
                                    icon = 'fa-file-excel'
                                } else if (fileExtension == 'png' || fileExtension == 'jpeg' ||
                                    fileExtension == 'jpg' || fileExtension == 'gif' ||
                                    fileExtension == 'webp' || fileExtension == 'svg') {
                                    icon = 'fa-image'
                                }
                                ht += '<span class="attachmentDiv mr-2"><i class="fa ' + icon +
                                    ' text-danger"></i><a class="text-dark"  href="{{ asset('public/ssl_attachment') }}/' +
                                    attachments[i] + '" target="_blank"> ' + attachments[i] +
                                    '</a></span>';
                            }
                            $('#attachmentDisplay').html(ht)
                        } else {
                            $('#attachmentDisplay').html('')
                        }
                        $('#created_at').html(res.created_at)
                        $('#created_by').html(res.created_by != null ? res.created_firstname +
                            ' ' + res.created_lastname : '')
                        $('#updated_by').html(res.updated_by != null ? res.updated_firstname +
                            ' ' + res.updated_lastname : '')
                        $('#updated_at').html(res.updated_at)
                        $('#cert_name').html(res.cert_name)
                        $('#cert_email').html(res.cert_email)
                        $('#cert_company').html(res.cert_company)
                        $('#cert_department').html(res.cert_department)
                        $('#cert_city').html(res.cert_city)
                        $('#cert_state').html(res.cert_state)
                        $('#cert_country').html(res.cert_country)
                        $('#cert_san1_5').html(res.cert_san1_5)
                        $('#cert_ip_int').html(res.cert_ip_int)
                        $('#cert_ip_pub').html(res.cert_ip_pub)
                        $('#cert_edate').html(res.cert_edate)
                        $('#cert_csr').html(res.cert_csr)
                        $('#cert_process').html(res.cert_process)
                        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July",
                            "Aug", "Sep", "Oct", "Nov", "Dec"
                        ];
                        var cert_rdate = '';
                        if (res.cert_rdate == '' || res.cert_rdate == null) {
                            cert_rdate = '';
                        } else {
                            var cert_rdateObject = new Date(res.cert_rdate);
                            var cert_rdate = cert_rdateObject.getFullYear() + '-' + monthNames[
                                    cert_rdateObject.getMonth()] + '-' + cert_rdateObject
                                .getDate();
                        }
                        var cert_edate = '';
                        if (res.cert_edate == '' || res.cert_edate == null) {
                            cert_edate = '';
                        } else {
                            var cert_edateObject = new Date(res.cert_edate);
                            cert_edate = cert_edateObject.getFullYear() + '-' + monthNames[
                                    cert_edateObject.getMonth()] + '-' + cert_edateObject
                                .getDate();
                        }
                        var status = '';
                        var MyDate = new Date('<?php echo date('m/d/Y'); ?>');
                        const diffTime = Math.abs(cert_edate - MyDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        if (res.cert_status == 'Active') {
                            if (diffDays <= 30) {
                                status = 'upcoming.png';
                            } else {
                                status = 'active.png';
                            }
                        } else if (res.cert_status == 'Inactive') {
                            status = 'renewed.png';
                        } else if (res.cert_status == 'Expired/Ended') {
                            status = 'ended.png';
                        } else if (res.cert_status == 'Expired') {
                            status = 'expired.png';
                        } else {
                            status = 'active.png';
                        }
                        $('#hostnameDisplay').html(
                            '<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover"  src="' +
                            operating_system +
                            '"  alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><img class="  mr-3 atar48" width="30px"    src="{{ asset('public/img/') }}/' +
                            status + '" alt=""><b>' + res.cert_name +
                            '</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">' +
                            (res.cert_type != null ? res.cert_type.toUpperCase() : '') +
                            '</span></p></div></div>')
                        $('#clientLogo').html(
                            '<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{ asset('public/client_logos/') }}/' +
                            res.logo + '" alt="">');
                        if (res.comments == '' || res.comments == null) {
                            $('.commentsDiv').addClass('d-none')
                        } else {
                            $('.commentsDiv').removeClass('d-none')
                        }
                        if (res.attachment == '' || res.attachment == null) {
                            $('.attachmentsDiv').addClass('d-none')
                        } else {
                            $('.attachmentsDiv').removeClass('d-none')
                        }
                        $('#cert_rdate').html(cert_rdate)
                        $('#cert_msrp').html(res.cert_msrp)
                        $('#cert_edate').html(cert_edate)
                    }
                })
            })
            $(document).on('click', '.btnDelete', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var id = $(this).attr('data');
                var c = confirm("Are you sure want to delete this Clients");
                if (c) {
                    window.location.href = "{{ url('delete-clients') }}?id=" + id;
                }
            })
            let click = 0;
            $(document).on('keyup', 'input,textarea', function() {
                click = 1;
            })
            $(document).on('change', 'select', function() {
                click = 1;
            })
            $(document).on('click', '.btnClose', function() {
                var id = $(this).attr('data')
                if (click == 1) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Close window?  <a href="javascript:;" data="' + id +
                            '" data-notify="dismiss" class="  btn-notify btnCloseUndo ml-4" >Proceed</a>',
                        delay: 5000,
                        type: 'info alert-notify-desktop'
                    });
                } else {
                    $("#JournalHeader").removeClass('d-none');
                    showData($(this).attr('data'))
                }
            })
            $(document).on('click', '.btnCloseUndo', function() {
                $("#JournalHeader").removeClass('d-none');
                showData($(this).attr('data'))
            })
            $(document).on('change', '#form-edit-client-gifi select', function() {
                click_edit_account_form = 1;
            });
            $(document).on('key', '#form-edit-client-gifi  input, #form-edit-client-gifi textarea', function() {
                click_edit_account_form = 1;
            });
            $(document).on('click', '.btnCloseAccountEditModal', function() {
                var modal = $(this).attr('target-modal');
                if (click_edit_account_form == 1) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Close window?  <a href="javascript:;" data="' + modal +
                            '" data-notify="dismiss" class="  btn-notify btnCloseEditAccountUndo ml-4" >Proceed</a>',
                        delay: 5000,
                        type: 'info alert-notify-desktop'
                    });
                } else {
                    $(modal).modal('hide');
                    click_edit_account_form = 0;
                }
            });
            $(document).on('click', '.btnCloseEditAccountUndo', function() {
                var modal = $(this).attr('data');
                $(modal).modal('hide');
                click_edit_account_form = 0;
            });
            var content3_image = [];
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
                            url: '{{ url('uploadContractAttachment') }}',
                            method: 'POST',
                            headers: {
                                'x-customheader': 'Processing File'
                            },
                            onload: (response) => {
                                response = response.replaceAll('"', '');
                                content3_image.push(response);
                                var attachemnts = $('#attachment_array').val()
                                var attachment_array = attachemnts.split(',');
                                attachment_array.push(response);
                                $('#attachment_array').val(attachment_array.join(','));
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
                            var attachemnts = $('#attachment_array').val()
                            var attachment_array = attachemnts.split(',');
                            attachment_array = attachment_array.filter(function(ele) {
                                return ele != uniqueFileId;
                            });
                            $('#attachment_array').val(attachment_array.join(','));
                            fetch(`{{ url('revertContractAttachment') }}?key=${uniqueFileId}`, {
                                    method: "DELETE",
                                    body: formData,
                                })
                                .then(res => res.json())
                                .then(json => {
                                    // Should call the load method when done, no parameters required
                                    load();
                                })
                                .catch(err => {
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
            // Comment ARRAY
            $('#CommentSave').click(function() {
                var comment = $('textarea[name=comment]').val();
                if (comment == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter a value for Comment',
                        delay: 5000
                    });
                } else {
                    var l = commentArray.length;
                    if (l < 5) {
                        commentArray.push({
                            key: comment_key_count,
                            comment: comment,
                            date: '{{ date('Y-M-d') }}',
                            time: '{{ date('h:i:s A') }}',
                            name: '{{ Auth::user()->firstname . '' . Auth::user()->lastname }}',
                            photo: '{{ Auth::user()->user_image }}'
                        });
                        showComment()
                        $('#CommentModal').modal('hide')
                        $('textarea[name=comment]').val('')
                        comment_key_count++;
                        Dashmix.helpers('notify', {
                            from: 'bottom',
                            align: 'left',
                            message: 'Comment added <a href="javascript:;" type="added" data="' + (
                                    commentArray.length - 1) +
                                '" data-notify="dismiss" class="  btn-notify btnDeleteComment ml-4" >Undo</a>',
                            delay: 5000,
                            type: 'info alert-notify-desktop'
                        });
                    }
                }
            })
            $('#CommentSaveEdit').click(function() {
                var comment = $('textarea[name=comment_edit]').val();
                var id = $('input[name=comment_id_edit]').val();
                if (comment == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter a value for Comment',
                        delay: 5000
                    });
                } else {
                    var l = commentArray.length;
                    commentArray[id].comment = comment;
                    showComment()
                    $('#CommentModalEdit').modal('hide')
                    $('textarea[name=comment_edit]').val('')
                }
            })
            $(document).on('click', '.btnEditComment', function() {
                var id = $(this).attr('data');
                $('#CommentModalEdit').modal('show');
                $('input[name=comment_id_edit]').val(id);
                $('textarea[name=comment_edit]').val(commentArray[id].comment);
            })
            var temp_comment = [];
            $(document).on('click', '.btnDeleteComment', function() {
                var id = $(this).attr('data');
                var key = commentArray[id].key;
                temp_comment.push(commentArray[id]);
                commentArray.splice(id, 1);
                var type = $(this).attr('type');
                if (type != 'added') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Comment Deleted. <a href="javascript:;" data-notify="dismiss" class="  btn-notify btnCommentUndo ml-4" data1=' +
                            id + ' data=' + key + '>Undo</a>',
                        delay: 5000,
                        type: 'info alert-notify-desktop'
                    });
                }
                showComment();
            })
            $(document).on('click', '.btnCommentUndo', function() {
                var id = $(this).attr('data');
                var key = $(this).attr('data1');
                let index = temp_comment.filter(l => l.key == id);
                if (index[0]) {
                    commentArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only
                    temp_comment = temp_comment.filter(l => l.key != id);
                    showComment();
                }
            })

            function showComment() {
                var html = '';
                if (commentArray.length > 0) {
                    $('.commentDiv').removeClass('d-none');
                } else {
                    $('.commentDiv').addClass('d-none');
                }
                for (var i = 0; i < commentArray.length; i++) {
                    let profile_icon = ` <h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                <img width="30px" src="{{ asset('public/img/profile-white.png') }}"> </b></h1>`;
                    if (commentArray[i].photo != undefined && commentArray[i].photo != '' && commentArray[i]
                        .photo != null) {
                        profile_icon =
                            ` <h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="{{ asset('public/client_logos/') }}/${commentArray[i].photo}"> </b></h1>`;
                    }
                    html += `    <div class="js-task block block-rounded mb-2 animated fadeIn"   data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                        ${profile_icon}
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${commentArray[i].name}<br><span class="comments-subtext">On ${commentArray[i].date} at ${commentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                         <a type="button"  data="${i}" class="j btnEditComment btn btn-sm btn-link text-warning">
                                                         <img src="{{ url('public/icons2/icon-edit-grey.png') }}" width="24px"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button"   data="${i}" class="btnDeleteComment btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/icons2/icon-delete-grey.png') }}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ${commentArray[i].comment.replace(/\r?\n/g, '<br />')}
</p>
                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>
                                    </div>`;
                }
                $('#commentBlock').html(html)
            }
            // END Comment
            function showCommentsAjax(id) {
                $.ajax({
                    type: 'get',
                    'method': 'get',
                    data: {
                        id: id
                    },
                    url: "{{ url('get-comments-client') }}",
                    success: function(res) {
                        for (var i = 0; i < res.length; i++) {
                            var date = res[i].date;
                            var newDate = new Date(date);
                            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
                                "July", "Aug", "Sep", "Oct", "Nov", "Dec"
                            ];
                            var date1 = newDate.getFullYear() + '-' + monthNames[newDate.getMonth()] +
                                '-' + newDate.getDate();
                            var time = newDate.toLocaleString('en-US', {
                                hour: date1.getHours,
                                minute: date1.getSeconds,
                                hour12: true
                            });
                            commentArray.push({
                                key: i,
                                comment: res[i].comment,
                                date: date1,
                                time: time.split(',')[1],
                                name: res[i].name,
                                photo: res[i].user_image
                            });
                            comment_key_count = i;
                        }
                        showComment();
                    }
                })
                $.ajax({
                    type: 'get',
                    'method': 'get',
                    data: {
                        id: id
                    },
                    url: "{{ url('get-attachment-client') }}",
                    success: function(res) {
                        for (var i = 0; i < res.length; i++) {
                            var date = res[i].date;
                            var newDate = new Date(date);
                            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
                                "July", "Aug", "Sep", "Oct", "Nov", "Dec"
                            ];
                            var date1 = newDate.getFullYear() + '-' + monthNames[newDate.getMonth()] +
                                '-' + newDate.getDate();
                            var time = newDate.toLocaleString('en-US', {
                                hour: date1.getHours,
                                minute: date1.getSeconds,
                                hour12: true
                            });
                            attachmentArray.push({
                                key: i,
                                attachment: res[i].attachment,
                                date: date1,
                                time: time.split(',')[1],
                                name: res[i].name,
                                photo: res[i].user_image
                            });
                            attachment_key_count = i;
                        }
                        showAttachment();
                    }
                })
            }
            // Attachment ARRAY
            $('#AttachmentSave').click(function() {
                var attachment = content3_image;
                if (content3_image.length == 0) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1">  Add an attachment before saving.',
                        delay: 5000
                    });
                } else {
                    var l = attachmentArray.length;
                    var added_indexes = [];
                    for (var i = 0; i < attachment.length; i++) {
                        attachmentArray.push({
                            key: attachment_key_count,
                            attachment: attachment[i],
                            date: '{{ date('Y-M-d') }}',
                            time: '{{ date('h:i:s A') }}',
                            name: '{{ Auth::user()->firstname . '' . Auth::user()->lastname }}',
                            photo: '{{ Auth::user()->user_image }}'
                        });
                        attachment_key_count++;
                        added_indexes.push(attachmentArray.length - 1);
                    }
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Attachment added <a href="javascript:;" type="added" data="' + (
                                JSON.stringify(added_indexes)) +
                            '" data-notify="dismiss" class="  btn-notify btnDeleteAddedAttachment ml-4" >Undo</a>',
                        delay: 5000,
                        type: 'info alert-notify-desktop'
                    });
                    filePond.removeFiles();
                    content3_image = [];
                    showAttachment()
                    $('#AttachmentModal').modal('hide')
                }
            })
            var temp_attachment = [];
            $(document).on('click', '.btnDeleteAttachment', function() {
                var id = $(this).attr('data');
                var key = attachmentArray[id].key;
                temp_attachment.push(attachmentArray[id]);
                attachmentArray.splice(id, 1);
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: 'Attachment  Deleted. <a href="javascript:;" data-notify="dismiss" class="  btn-notify btnAttachmentUndo ml-4" data1=' +
                        id + ' data=' + key + '>Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
                showAttachment();
            })
            $(document).on('click', '.btnDeleteAddedAttachment', function() {
                var keys = JSON.parse($(this).attr('data'));
                keys.sort((a, b) => b - a);
                for (var i = 0; i < keys.length; i++) {
                    var id = keys[i];
                    var key = attachmentArray[id].key;
                    temp_attachment.push(attachmentArray[id]);
                    attachmentArray.splice(id, 1);
                }
                showAttachment();
            });
            $('#AttachmentClose').click(function() {
                temp_attachment = [];
                content3_image = [];
                filePond.removeFiles();
            })
            $(document).on('click', '.btnAttachmentUndo', function() {
                var id = $(this).attr('data');
                var key = $(this).attr('data1');
                let index = temp_attachment.filter(l => l.key == id);
                if (index[0]) {
                    attachmentArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only
                    temp_attachment = temp_attachment.filter(l => l.key != id);
                    showAttachment();
                }
            })

            function showAttachment() {
                var html = '';
                if (attachmentArray.length > 0) {
                    $('.attachmentDiv').removeClass('d-none');
                } else {
                    $('.attachmentDiv').addClass('d-none');
                }
                for (var i = 0; i < attachmentArray.length; i++) {
                    var fileExtension = attachmentArray[i].attachment.split('.').pop();
                    icon = 'attachment.png';
                    if (fileExtension == 'pdf') {
                        icon = 'attch-Icon-pdf.png';
                    } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                        icon = 'attch-word.png'
                    } else if (fileExtension == 'txt') {
                        icon = 'attch-word.png';
                    } else if (fileExtension == 'csv' || fileExtension == 'xlsx' || fileExtension == 'xlsm' ||
                        fileExtension == 'xlsb' || fileExtension == 'xltx') {
                        icon = 'attch-excel.png'
                    } else if (fileExtension == 'png' || fileExtension == 'gif' || fileExtension == 'webp' ||
                        fileExtension == 'svg') {
                        icon = 'attch-png icon.png';
                    } else if (fileExtension == 'jpeg' || fileExtension == 'jpg') {
                        icon = 'attch-jpg-icon.png';
                    } else if (fileExtension == 'potx' || fileExtension == 'pptx' || fileExtension == 'ppsx' ||
                        fileExtension == 'thmx') {
                        icon = 'attch-powerpoint.png';
                    }
                    let photo_icon =
                        ` <h1 class="mb-0 mr-3  text-white bg-dark rounded p-1" style=""><b>
                                                          <img width="30px" src="{{ asset('public/img/profile-white.png') }}"> </b></h1>`;
                    if (attachmentArray[i].photo != undefined && attachmentArray[i].photo != "" && attachmentArray[
                            i].photo != null) {
                        photo_icon =
                            ` <h1 class="mb-0 mr-3  text-white bg-dark rounded " style=""><b>
                                                          <img width="34px" height="34px" style="object-fit:cover;" class="rounded" src="{{ asset('public/client_logos/') }}/${attachmentArray[i].photo}"> </b></h1>`;
                    }
                    html += `   <div class="col-sm-6 px-0  attach-other-col">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         ${photo_icon}
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                           <h2 class="mb-0 comments-text">${attachmentArray[i].name}<br><span class="comments-subtext">On ${attachmentArray[i].date} at ${attachmentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                       <!-- -->
                                                        <a type="button"  class="  btnDeleteAttachment    btn btn-sm btn-link text-danger"  data="${i}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/icons2/icon-delete-grey.png') }}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-2"><p class="mb-2">
 <a href="{{ asset('public/temp_uploads/${attachmentArray[i].attachment}') }}" target="_blank"    class="   attachmentDivNew comments-section-text"><img src="{{ asset('public/img/${icon}') }}" width="25px"> &nbsp;${attachmentArray[i].attachment.substring(0,19)}...
</a></p>
                                                    </td>
                                                </tr>
                                        </tbody>
                                    </table>
                                        </div>
                                    </div>`;
                }
                $('#attachmentBlock').html(html)
            }
            // END Attachment
            $(document).on('click', '.saveContract', function() {
                $('.tooltip').tooltip('hide');
                $('.show').addClass('d-none');
                var data1 = $(this).attr('data');
                var firstname = $('input[name=firstname_edit]').val();
                var lastname = $('input[name=lastname_edit]').val()
                var company = $('input[name=company_edit]').val()
                var type = $('#type_edit').val()
                var corporation_no = $('input[name=corporation_no_edit]').val()
                var business = $('#business_edit').val()
                var federal_no = $('input[name=federal_no_edit]').val()
                var provincial_no = $('input[name=provincial_no_edit]').val()
                var email = $('input[name=email_edit]').val()
                var fax = $('input[name=fax_edit]').val()
                var telephone = $('input[name=telephone_edit]').val()
                var country = $('select[name=country_edit]').val()
                var address = $('textarea[name=address_edit]').val()
                var city = $('input[name=city_edit]').val()
                var province = $('select[name=province_edit]').val()
                var postal_code = $('input[name=postal_code_edit]').val()
                var fiscal_start = $('input[name=fiscal_start_edit]').val()
                var default_province = $("select[name=default_province_edit]").val()
                var federal_tax = $("select[name=federal_tax_edit]").val()
                var provincial_tax = $("select[name=provincial_tax_edit]").val()
                var dividends_account = $("select[name=dividends_account_edit]").val()
                var check = 1;
                var email_regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var tele_regex = /^\d{10}$/;
                var postal_regex = /^[A-Za-z0-9]{3}\s?[A-Za-z0-9]{3}$/;
                var tele_regex1 = /^.{0,65}$/;
                var fisc_regex = /^[A-Z0-9\-]+$/;
                if (firstname == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Firstname.',
                        delay: 5000
                    });
                } else if (lastname == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Lastname.',
                        delay: 5000
                    });
                } else if (company == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Company.',
                        delay: 5000
                    });
                } else if (type == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Type.',
                        delay: 5000
                    });
                } else if (type == 'Corporation' && corporation_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Corporation #.',
                        delay: 5000
                    });
                } else if (business == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Business.',
                        delay: 5000
                    });
                } else if (federal_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Federal No.',
                        delay: 5000
                    });
                } else if (federal_no.length > 20) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Federal No is not valid format.',
                        delay: 5000
                    });
                    // else if (!fisc_regex.test(federal_no) || federal_no.length > 18) {
                    //     Dashmix.helpers('notify', {
                    //         from: 'bottom',
                    //         align: 'left',
                    //         message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Federal No is not valid format.',
                    //         delay: 5000
                    //     });
                } else if (provincial_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Provincial No.',
                        delay: 5000
                    });
                } else if (provincial_no.length > 18) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Provincial No is not valid format.',
                        delay: 5000
                    });
                    // else if (!fisc_regex.test(provincial_no) || provincial_no.length > 18) {
                    //     Dashmix.helpers('notify', {
                    //         from: 'bottom',
                    //         align: 'left',
                    //         message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Provincial No is not valid format.',
                    //         delay: 5000
                    //     });
                } else if (email == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Email.',
                        delay: 5000
                    });
                } else if (!email_regex.test(email)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Email must be valid email format.',
                        delay: 5000
                    });
                } else if (telephone == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Telephone.',
                        delay: 5000
                    });
                } else if (!validatePhone(telephone)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Telephone, numeric & dashes only 14 digits.',
                        delay: 5000
                    });
                } else if (country == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Country.',
                        delay: 5000
                    });
                } else if (address == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Address.',
                        delay: 5000
                    });
                } else if (city == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for City.',
                        delay: 5000
                    });
                } else if (province == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Province.',
                        delay: 5000
                    });
                } else if (postal_code == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Postal Code.',
                        delay: 5000
                    });
                } else if (!postal_regex.test(postal_code)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Postal Code, alpha numeric 7 character (3 char,space,3 char)',
                        delay: 5000
                    });
                } else if (fiscal_start == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Fiscal Start.',
                        delay: 5000
                    });
                } else if (validateFiscalStart(fiscal_start) == 0) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Fiscal start date must be the first of the month. ',
                        delay: 5000
                    });
                } else if (default_province == '' || default_province == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Default Province.',
                        delay: 5000
                    });
                } else if (federal_tax == '' || federal_tax == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select value for Federal Tax.',
                        delay: 5000
                    });
                } else if (provincial_tax == '' || provincial_tax == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select value for Provincial Tax.',
                        delay: 5000
                    });
                } else if (dividends_account == '' || dividends_account == null) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select value for Dividends Account.',
                        delay: 5000
                    });
                } else {
                    var formData = new FormData(document.getElementById("form-1"));
                    for (var i = 0; i < attachmentArray.length; i++) {
                        formData.append('attachmentArray[]', JSON.stringify(attachmentArray[i]));
                    }
                    for (var i = 0; i < commentArray.length; i++) {
                        formData.append('commentArray[]', JSON.stringify(commentArray[i]));
                    }
                    $.ajax({
                        type: 'post',
                        data: formData,
                        'url': '{{ url('update-clients') }}',
                        dataType: 'json',
                        async: false,
                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(res) {
                            window.location.href = "{{ url('/clients') }}?id=" + data1;
                            //$("#JournalHeader").removeClass('d-none');
                            //Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: 'Clients successfully saved', delay: 5000});
                            //showData(data1)
                            click = 0;
                        }
                    })
                }
            })
        });
        $(document).on('click', '.show-add-client-gifi-modal', function() {
            $("#form-add-client-gifi")[0].reset();
            var id = $(this).attr('data-client-id');
            $("#form-add-client-gifi input[name=client_id]").val(id);
            $("#AddClientGifiModal").modal('show');
        });
        $(document).on('submit', '#form-add-client-gifi', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var action = $(this).attr('action');
            const client_id = $('#form-add-client-gifi input[name=client_id]').val();
            var account_no = $('#form-add-client-gifi input[name=account_no]').val();
            var description = $('#form-add-client-gifi input[name=description]').val();
            var account_type = $('#form-add-client-gifi select[name=account_type]').val();
            var sub_type = $('#form-add-client-gifi select[name=sub_account_type]').val();
            var min = parseInt($('option:selected', $('#form-add-client-gifi select[name=sub_account_type]')).attr(
                'data-min'))
            var max = parseInt($('option:selected', $('#form-add-client-gifi select[name=sub_account_type]')).attr(
                'data-max'))
            var check = 1;
            $.ajax({
                type: 'get',
                data: {
                    account_no: account_no,
                    client_id: client_id
                },
                url: '{{ url('check-client-gifi') }}',
                async: false,
                success: function(res) {
                    if (res == 1) {
                        check = 0;
                    }
                }
            })
            var tele_regex1 = /^.{0,65}$/;
            if (check == 0) {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > GIFI account entered already exists.',
                    delay: 5000
                });
            } else if (account_no == '') {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Account No.',
                    delay: 5000
                });
            } else if (description == '') {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Description.',
                    delay: 5000
                });
            } else if (!tele_regex1.test(description)) {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Description should be max 65 chars.',
                    delay: 5000
                });
            } else if (account_no < min || account_no > max) {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > AccountNo ' +
                        account_no + ' does not fall in range of GIFI standards for this ' + sub_type +
                        '. <a href="javascript:;" data="#form-add-client-gifi" action="' + action +
                        '" data-notify="dismiss" class="  btn-notify btnProceedSave ml-4" >Proceed</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
            } else {
                //getClientAccounts(id);
                var form_data = new FormData(document.querySelector('#form-add-client-gifi'));
                $.ajax({
                    type: "POST",
                    url: action,
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                }).done(function(response) {
                    getClientAccounts(response);
                    $("#AddClientGifiModal").modal('hide');
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Clients account created successfully.',
                        delay: 5000
                    });
                });
            }
        });
        $(document).on('click', '.btnProceedSave', function(e) {
            var data = $(this).attr('data');
            var action = $(this).attr('action');
            var form_data = new FormData(document.querySelector(data));
            $.ajax({
                type: "POST",
                url: action,
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
            }).done(function(response) {
                getClientAccounts(response);
                $("#AddClientGifiModal").modal('hide');
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: 'Clients account created successfully.',
                    delay: 5000
                });
            });
        });
        $(document).on('click', '.btnEditGifi', function(e) {
            $("#form-edit-client-gifi")[0].reset();
            $('#form-edit-client-gifi select[name=account_type]').attr('sub', '');
            var client_id = $(this).attr('data-client-id');
            var account_id = $(this).attr('data');
            var account_type = $(this).attr('account_type');
            var sub_type = $(this).attr('sub_type');
            var account_no = $(this).attr('account_no');
            var description = $(this).attr('description');
            var note = $(this).attr('note');
            $("#form-edit-client-gifi input[name=client_id]").val(client_id);
            $("#form-edit-client-gifi input[name=client_account_id]").val(account_id);
            $(`#form-edit-client-gifi select[name=account_type] option[value='${account_type}']`).prop('selected',
                true);
            $('#form-edit-client-gifi select[name=account_type]').attr('sub', sub_type);
            $('#form-edit-client-gifi select[name=account_type]').trigger('change');
            $(`#form-edit-client-gifi select[name=sub_account_type] option[value='${sub_type}']`).prop('selected',
                true);
            $("#form-edit-client-gifi input[name=account_no]").val(account_no);
            $("#form-edit-client-gifi input[name=account_no]").attr('default', account_no);
            $("#form-edit-client-gifi input[name=description]").val(description);
            $("#form-edit-client-gifi textarea[name=note]").val(note);
            click_edit_account_form = 0;
            $("#EditClientGifiModal").modal('show');
        });
        $(document).on('submit', '#form-edit-client-gifi', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var action = $(this).attr('action');
            const client_id = $("#form-edit-client-gifi input[name=client_id]").val();
            var account_no = $('#form-edit-client-gifi input[name=account_no]').val();
            var default_acc_no = $('#form-edit-client-gifi input[name=account_no]').attr('default');
            var description = $('#form-edit-client-gifi input[name=description]').val()
            var account_type = $('#form-edit-client-gifi select[name=account_type]').val();
            var sub_type = $('#form-edit-client-gifi select[name=sub_account_type]').val();
            var min = parseInt($('option:selected', $('#form-edit-client-gifi select[name=sub_account_type]')).attr(
                'data-min'))
            var max = parseInt($('option:selected', $('#form-edit-client-gifi select[name=sub_account_type]')).attr(
                'data-max'))
            var check = 1;
            if (account_no != default_acc_no) {
                $.ajax({
                    type: 'get',
                    data: {
                        account_no: account_no,
                        client_id
                    },
                    url: '{{ url('check-client-gifi') }}',
                    async: false,
                    success: function(res) {
                        if (res == 1) {
                            check = 0;
                        }
                    }
                })
            }
            var tele_regex1 = /^.{0,65}$/;
            if (check == 0) {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > GIFI account entered already exists.',
                    delay: 5000
                });
            } else if (account_no == '') {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Account No.',
                    delay: 5000
                });
            } else if (description == '') {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Description.',
                    delay: 5000
                });
            } else if (!tele_regex1.test(description)) {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Description should be max 65 chars.',
                    delay: 5000
                });
            } else if (account_no < min || account_no > max) {
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > AccountNo ' +
                        account_no + ' does not fall in range of GIFI standards for this ' + sub_type +
                        '. <a href="javascript:;" data="#form-edit-client-gifi" action="' + action +
                        '" data-notify="dismiss" class="  btn-notify btnProceedUpdate ml-4" >Proceed</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
            } else {
                //getClientAccounts(id);
                var form_data = new FormData(document.querySelector('#form-edit-client-gifi'));
                $.ajax({
                    type: "POST",
                    url: action,
                    data: form_data,
                    contentType: false,
                    cache: false,
                    processData: false,
                }).done(function(response) {
                    getClientAccounts(response);
                    $("#EditClientGifiModal").modal('hide');
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Clients account saved successfully.',
                        delay: 5000
                    });
                });
            }
        });
        $(document).on('click', '.btnProceedUpdate', function() {
            var data = $(this).attr('data');
            var action = $(this).attr('action');
            var form_data = new FormData(document.querySelector(data));
            $.ajax({
                type: "POST",
                url: action,
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
            }).done(function(response) {
                getClientAccounts(response);
                $("#EditClientGifiModal").modal('hide');
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: 'Clients account saved successfully.',
                    delay: 5000
                });
            });
        });
        $(document).on('click', '.btnDeleteGifi', function() {
            var client_account_id = $(this).attr('data');
            var client_id = $(this).attr('data-client-id');
            var c = confirm("Are you sure want to delete this Client Account?");
            if (c) {
                $.ajax({
                    type: "POST",
                    url: "{{ url('/DeleteClientGifi') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "client_id": client_id,
                        "client_account_id": client_account_id,
                    }
                }).done(function(response) {
                    if (response == 1) {
                        getClientAccounts(client_id);
                        Dashmix.helpers('notify', {
                            from: 'bottom',
                            align: 'left',
                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Client account deleted. <a href="javascript:;" data="' +
                                client_account_id + '" data-notify="dismiss" data-client-id="' +
                                client_id + '" class="  btn-notify UndoGifiDelete ml-4" >Undo</a>',
                            delay: 5000,
                            type: 'info alert-notify-desktop'
                        });
                    }
                });
            }
        });
        $(document).on('click', '.UndoGifiDelete', function() {
            var client_account_id = $(this).attr('data');
            var client_id = $(this).attr('data-client-id');
            $.ajax({
                type: "POST",
                url: "{{ url('/UndoDeleteClientGifi') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "client_account_id": client_account_id,
                    "client_id": client_id,
                }
            }).done(function(response) {
                if (response == 1) {
                    getClientAccounts(client_id);
                }
            });
        });
        $(document).on('keyup', 'input[name=gifi-search]', function(e) {
            var $this = $(this);
            var searchVal = $(this).val();
            var client_id = $(this).attr('data');
            if (e.which == 13) {
                getClientAccounts(client_id, searchVal);
                $this.focus();
            }
        });
        $(document).on('submit', '#filterClientAccountForm', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            //var formData = $(this).serialize(); // Serialize form data
            var client_id = $(`#btnFilterClientGifi`).attr('data-client-id');
            if ($(this).attr('method') === 'GET') {
                // var queryString = '?' + formData; // Construct query string
                // You can perform further actions with the query string here
                $("#gifi-clear-filter-1").attr('data-client-id', client_id);
                $("#filterClientAccountModal").modal('hide');
                //console.log(queryString);
                var formDataArray = $(this).serializeArray();
                var formDataObject = {};
                // Group fields with the same name into an array value
                formDataArray.forEach(function(field) {
                    if (formDataObject.hasOwnProperty(field.name)) {
                        if (!Array.isArray(formDataObject[field.name])) {
                            formDataObject[field.name] = [formDataObject[field.name]];
                        }
                        formDataObject[field.name].push(field.value);
                    } else {
                        formDataObject[field.name] = field.value;
                    }
                });
                getClientAccounts(client_id, '', formDataObject);
            }
        });
        $(document).on('click', "#gifi-clear-filter-1", function() {
            var client_id = $(this).attr('data-client-id');
            $("#filterClientAccountModal").modal('hide');
            $("#filterClientAccountForm")[0].reset();
            getClientAccounts(client_id, '', {});
        });
        $(document).on({
            mouseenter: function() {
                setTimeout(() => {
                    var $this = $(this);
                    if ($this.data('tooltipShown')) {
                        // $this.tooltip('hide');
                    } else {
                        const data_business = $this.attr('data-business'),
                            data_federal_no = $this.attr('data-federal-no'),
                            data_provincial_no = $this.attr('data-provincial-no'),
                            data_city = $this.attr('data-city'),
                            data_country = $this.attr('data-country'),
                            data_telephone = $this.attr('data-telephone'),
                            data_address = $this.attr('data-address'),
                            data_fax = $this.attr('data-fax'),
                            data_province = $this.attr('data-province'),
                            data_postalcode = $this.attr('data-postalcode');
                        var html = `
<p class="mb-0 tooltip-h6">Corporation</p>
<p class="text-muted mb-0">${data_business}</p>
<p class="text-muted mb-0">${data_federal_no}</p>
<p class="text-muted mb-0">${data_provincial_no}</p>
`;
                        $this.tooltip({
                            title: html,
                            html: true,
                            placement: 'bottom',
                            trigger: 'hover',
                            template: '<div class="tooltip custom-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                        });
                        $this.tooltip('show');
                    }
                    $this.data('tooltipShown', !$this.data('tooltipShown'));
                }, 200);
            },
            mouseleave: function() {
                setTimeout(() => {
                    var $this = $(this);
                    if ($this.data('tooltipShown')) {
                        $this.tooltip('hide');
                    }
                }, 200);
            },
        }, '.client-info')
        $(document).on({
            mouseenter: function() {
                var $this = $(this);
                if ($this.data('tooltipShown')) {
                    // $this.tooltip('hide');
                } else {
                    const data_business = $this.attr('data-business'),
                        data_federal_no = $this.attr('data-federal-no'),
                        data_provincial_no = $this.attr('data-provincial-no'),
                        data_city = $this.attr('data-city'),
                        data_country = $this.attr('data-country'),
                        data_telephone = $this.attr('data-telephone'),
                        data_address = $this.attr('data-address'),
                        data_fax = $this.attr('data-fax'),
                        data_province = $this.attr('data-province'),
                        data_postalcode = $this.attr('data-postcode');
                    var html = `
<p class="mb-0 tooltip-h6" >Address</p>
<p class="mb-0">${data_address}</p>
<p class="mb-0">${data_city + ',' + ' ' + data_province + ' ' + data_postalcode}</p>
<p class="mb-0">${data_country}</p>
<p class="mb-0">Tel:${data_telephone} Fax:${data_fax}</p>
`;
                    $this.tooltip({
                        title: html,
                        html: true,
                        placement: 'bottom',
                        trigger: 'hover',
                        template: '<div class="tooltip custom-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                    });
                    $this.tooltip('show');
                }
                $this.data('tooltipShown', !$this.data('tooltipShown'));
            },
            mouseleave: function() {
                var $this = $(this);
                if ($this.data('tooltipShown')) {
                    $this.tooltip('hide');
                }
            }
        }, '.client-info2')
        $(document).on('change', '#filterClientAccountForm select[name=account_type]', function() {
            var val = $(this).val();
            $.ajax({
                type: 'get',
                data: {
                    account: val
                },
                url: '{{ url('/get-gifi-account') }}',
                async: false,
                success: function(res) {
                    const sub_account_type = res.sub_accounts;
                    const accounts = res.accounts;
                    var html = '';
                    for (var i = 0; i < sub_account_type.length; i++) {
                        html += '<option value="' + sub_account_type[i].sub_type + '" data-min="' +
                            sub_account_type[i].min + '" data-max="' + sub_account_type[i].max + '" >' +
                            sub_account_type[i].sub_type + '</option>';
                    }
                    $('#filterClientAccountForm #sub_account_type').html(html);
                    $('#filterClientAccountForm #sub_account_type').selectpicker('refresh');
                    var html2 = '';
                    for (var i = 0; i < accounts.length; i++) {
                        html2 += '<option value="' + accounts[i].account_no + '">' + accounts[i]
                            .account_no + '</option>';
                    }
                    $('#filterClientAccountForm #account').html(html2);
                    $('#filterClientAccountForm #account').selectpicker('refresh');
                }
            })
        });
        $(document).ready(function() {
            $(document).on('click', '.undo-delete', function() {
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-clients') }}?id=" + id;
            });
        });
        $(document).ready(function() {
            $(document).on('shown.bs.modal', '#CommentModal', function() {
                $("#CommentModal textarea[name=comment]").focus();
            });
            $(document).on('shown.bs.modal', '#EndModal', function() {
                $('#EndModal textarea[name=reason]').focus();
            });
            $(document).on('shown.bs.modal', '#AddTaxModal', function() {
                $('#AddTaxModal input[name=firstname]').focus();
            });
        });
    </script>
