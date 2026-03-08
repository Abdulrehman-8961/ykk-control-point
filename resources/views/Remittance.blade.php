@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')


    <?php
    function monthToStringShort($month)
    {
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];
        return $months[$month];
    }
    $userAccess = explode(',', Auth::user()->access_to_client);
    
    $limit = 10;
    $no_check = DB::Table('settings')->where('user_id', Auth::id())->first();
    if (isset($_GET['limit']) && $_GET['limit'] != '') {
        $limit = $_GET['limit'];
    
        if ($no_check != '') {
            DB::table('settings')
                ->where('user_id', Auth::id())
                ->update(['remittance' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => Auth::id(), 'remittance' => $limit]);
        }
    } else {
        if ($no_check != '') {
            if ($no_check->remittance != '') {
                $limit = $no_check->remittance;
            }
        }
    }
    
    if (sizeof($_GET) > 0) {
        $orderby = 'desc';
        $field = 'id';
        if (isset($_GET['orderBy'])) {
            $orderby = $_GET['orderBy'];
            $field = $_GET['field'];
        }
    
        //  $qry=DB::table('gifi')->where('is_deleted',0)->where(function($query){
        //      $query->Orwhere('sub_type','like','%'.@$_GET['search'].'%');
        // $query->Orwhere('account_type','like','%'.@$_GET['search'].'%');
        //     $query->Orwhere('account_no','like','%'.@$_GET['search'].'%');
        //         $query->Orwhere('description','like','%'.@$_GET['search'].'%');
        //             $query->Orwhere('note','like','%'.@$_GET['search'].'%');
    
        //  }) ->orderBy($field,$orderby)->paginate($limit);
    } else {
    }
    $qry = DB::table('remittances as r')
        ->where('r.is_deleted', 0)
    
        ->join('clients as c', function ($join) {
            $join->on('c.id', '=', 'r.client');
            $join->where('c.is_deleted', 0);
            if (@$_GET['search']) {
                $join->where(function ($query) {
                    $query->orWhere('c.firstname', 'like', '%' . @$_GET['search'] . '%');
                    $query->orWhere('c.lastname', 'like', '%' . @$_GET['search'] . '%');
                    $query->orWhere('c.company', 'like', '%' . @$_GET['search'] . '%');
                });
            }
        })
        ->leftJoin('cities as p', function ($join) {
            $join->on('c.province', '=', 'p.state_name');
            $join->where('p.state_name', '=', 'c.province');
            $join->limit(1);
        })
        ->select('r.*', 'c.firstname', 'c.company', 'c.display_name', 'c.lastname', 'c.federal_tax', 'c.provincial_tax', 'c.tax_remittance', 'p.state_code as province_code', 'c.logo', 'c.fiscal_start')
        ->orderByDesc('r.id')
        ->paginate($limit);
    
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
                top: 10px !important;
                right: 15px !important;
                opacity: 1 !important;
                font-weight: 200 !important;
                width: 33px;
                padding-bottom: 3px;
            }

            .alert-info .close:hover {
                background-color: white !important;
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

                padding: 0px;
                margin: .25rem .5rem;
            }

            .btn-link:hover {
                box-shadow: -1px 2px 4px 3px #99dff9;
                background: #99dff9;
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
                border: 1px solid transparent !important;
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
                background-color: white !important;
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

                padding: 0px;
                margin: .25rem .5rem;
            }

            .btn-link:hover {
                box-shadow: -1px 2px 4px 3px #99dff9;
                background: #99dff9;
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


            .table-tax-summary td {
                color: #595959 !important;
                font-size: 12pt !important;
                font-family: Jura !important;
            }


            .table-tax-summary {
                table-layout: fixed !important;
                border-spacing: 0 !important
            }

            .table-tax-summary>thead tr td {
                font-weight: 600;
                padding: 0;
                border: 0 !important;
            }

            .table-tax-summary>tbody tr td {
                padding: 0;
                border: 0 !important;
            }

            .table-tax-summary>thead tr td:nth-child(1),
            .table-tax-summary>tbody tr td:nth-child(1) {
                width: 100px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-tax-summary>thead tr td:nth-child(2),
            .table-tax-summary>tbody tr td:nth-child(2) {
                width: 100px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-tax-summary>thead tr td:nth-child(3),
            .table-tax-summary>tbody tr td:nth-child(3) {
                width: 100px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-tax-summary>thead tr td:nth-child(4),
            .table-tax-summary>thead tr td:nth-child(4) {
                width: 100px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }


            .table-tax-summary>thead tr td:nth-child(5),
            .table-tax-summary>tbody tr td:nth-child(5) {
                width: 200px !important;
                text-align: left !important;
                padding-bottom: 5px !important
            }

            .table-tax-summary>thead tr td:nth-child(6),
            .table-tax-summary>tbody tr td:nth-child(6) {
                width: 100px !important;
                text-align: right !important;
                padding-bottom: 5px !important;
            }

            .table-tax-summary>thead tr td:nth-child(7),
            .table-tax-summary>tbody tr td:nth-child(7) {
                width: 100px !important;
                text-align: right !important;
                padding-bottom: 5px !important;
            }

            .range-scrollable {
                overflow-x: scroll;
                padding-bottom: 1px !important;
            }

            .range-scrollable::-webkit-scrollbar,
            .range-scrollable::-webkit-scrollbar-thumb {

                height: 5px !important;
            }




            .range-capsule {
                border: 1px solid #595959;
                font-size: 10pt;
                font-family: Jura;
                color: #262626;
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

            .dt-account-description-tag {
                border: 1px solid black;
                border-radius: 5px;
                color: black;
                font-family: "Signika";
                font-size: 10pt;
                padding: 5px 10px;
                width: fit-content;
                margin-top: -3px;
            }

            .dt-account-description-tag:hover {
                background-color: #F2F2F2;
                box-shadow: 0 0 2px 2px rgba(127, 127, 127, 0.3);
                transition: background-color 0.3s, box-shadow 0.3s;
            }

            .spinner {
                color: #fff !important;
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

            .expanded {
                padding-right: 40px;
                width: auto;
                transition: width 0.3s ease-in-out;
            }


            @media print {
                .break-before {
                    page-break-before: always;
                }
            }

            .ribbon {
                position: absolute;
                top: 66px;
                left: -22px;
                text-align: center;
                width: 120px;
                color: white;
                padding: 5px;
                font-size: 14px;
                transform: rotate(-46deg);
                transform-origin: top left;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
                min-height: fit-content !important;
            }

            .bubble-ribbon {
                position: absolute;
                top: 39px;
                left: -13px;
                text-align: center;
                width: 70px;
                color: white;
                padding: 0px;
                font-size: 7pt;
                transform: rotate(-46deg);
                transform-origin: top left;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
                min-height: fit-content !important;
            }

            .ribbon-blue {
                background: #0070DD;
            }

            .ribbon-orange {
                background: #F8B700;
                color: #000000;
            }

            .ribbon-green {
                background: #4EA833;
            }

            .payment-method input[type=radio] {
                visibility: hidden;
                position: absolute;
            }

            /* .payment-method label {
                                                    border: 1px solid #D7D5E2;
                                                    font-size: 10pt !important;
                                                    font-family: Jura;
                                                    color: #595959;
                                                    background: transparent;
                                                }

                                                .payment-method label:hover {
                                                    border: 1px solid #4194F6;
                                                    font-size: 10pt !important;
                                                    font-family: Jura;
                                                    color: #4194F6 !important;
                                                    box-shadow: 0 0 2px 2px rgba(65, 149, 246, 0.6);
                                                    background: transparent;
                                                }
                                                .payment-method input[type=radio]:checked {
                                                    border: 1px solid #4194F6;
                                                    font-size: 10pt !important;
                                                    font-family: Jura;
                                                    color: #4194F6 !important;
                                                    background: #F4F9FF;
                                                }
                                                .payment-method input[type=radio]:checked:hover {
                                                    border: 1px solid #4194F6;
                                                    font-size: 10pt !important;
                                                    font-family: Jura;
                                                    color: #4194F6 !important;
                                                    box-shadow: 0 0 2px 2px rgba(65, 149, 246, 0.6);
                                                    background: #D5E8FF;
                                                } */

            .payment-method label {
                border: 1px solid #D7D5E2;
                font-size: 10pt !important;
                font-family: Jura, sans-serif;
                color: #595959;
                background: transparent;
                padding: 8px 12px;
                display: inline-block;
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 8px;
            }

            .payment-method label:hover {
                border-color: #4194F6;
                color: #4194F6 !important;
                box-shadow: 0 0 2px 2px rgba(65, 149, 246, 0.6);
            }

            .payment-method input[type=radio] {
                display: none;
            }

            .payment-method input[type=radio]:checked+label {
                border-color: #4194F6;
                color: #4194F6 !important;
                background: #F4F9FF;
                box-shadow: 0 0 2px 2px rgba(65, 149, 246, 0.6);
            }

            .payment-method input[type=radio]:checked+label:hover {
                background: #D5E8FF;
            }

            .bubble_credit:hover {
                background-color: #FCE7EB !important;
                box-shadow:
                    0 0 2px rgba(196, 30, 58, 0.6),
                    0 0 6px rgba(196, 30, 58, 0.6);
            }

            .bubble_debit:hover {
                background-color: #F4F9FF !important;
                box-shadow:
                    0 0 2px rgba(65, 148, 246, 0.6),
                    0 0 6px rgba(65, 148, 246, 0.6);
            }

            .bubble_period:hover {
                background-color: #F2F2F2 !important;
                box-shadow:
                    0 0 2px rgba(166, 166, 166, 0.6),
                    0 0 6px rgba(166, 166, 166, 0.6);
            }

            .bubble-account:hover {
                background-color: #E9E9E9 !important;
                box-shadow: 0 0 2px rgba(89, 89, 89, 0.6), 0 0 6px rgba(89, 89, 89, 0.6);
            }

            .bubble_period {
                white-space: nowrap !important;
            }

            .spinner {
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

            .expanded {
                padding-right: 40px;
                width: auto;
                transition: width 0.3s ease-in-out;
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
                        <div class="row">
                            <div class="col-sm-9 search-col row">
                                <div class="push mb-0 col-sm-4 pr-0">

                                    <?php $filter = (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') . (isset($_GET['note']) ? '&note=' . $_GET['note'] : '') . (isset($_GET['account_type']) ? '&account_type=' . $_GET['account_type'] : '') . (isset($_GET['sub_type']) ? '&sub_type=' . $_GET['sub_type'] : '') . (isset($_GET['account_no']) ? '&account_no=' . $_GET['account_no'] : '') . (isset($_GET['description']) ? '&description=' . $_GET['description'] : '') . (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                                    ?>

                                    <form class="push mb-0" method="get" id="form-search"
                                        action="{{ url('remittances/') }}?{{ $filter }}">

                                        <div class="input-group main-search-input-group" style="max-width: 74.375%;">
                                            <input type="text" value="{{ @$_GET['search'] }}"
                                                class="form-control searchNew" name="search"
                                                placeholder="Search Remittance">
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
                                </div>
                                <div class="col-sm-2  1" style="margin-top: 2px;">
                                    {{-- @if (Auth::user()->role != 'read')

                                <a class="btn btn-dual  d2 " href="javascript:;" data-toggle="modal"
                                    data-target="#AddTaxModal">
                                    <img src="{{asset('public/img/ui-icon-add.png')}}" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" data-custom-class="header-tooltip" title=""
                                        data-original-title="Add Gifi Account" width="19px" height="19px">
                                </a>

                                @endif --}}
                                    @if (Auth::user()->role != 'read')

                                        <a class="btn btn-dual c-remittance-btn d2 " data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Create Remittance" data-custom-class="header-tooltip"
                                            style="margin-top:2px;" href="javascript:;" data-toggle="modal"
                                            data-target="#AddTaxModal">
                                            <img src="{{ asset('public/icons2/icon-remit-white.png') }}" style="width:15px">
                                        </a>
                                        <a class="btn btn-dual rep-btn d2 " data-toggle="tooltip"
                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Report" href="javascript:;"
                                            style="position: absolute;top: 0; left: 60px;">
                                            <img src="{{ asset('public/icons2/icon-report-white.png') }}"
                                                style="width:15px">
                                        </a>

                                    @endif
                                </div>
                                <div class="col-lg-5 pr-3">
                                    <div class="input-group" data-toggle="tooltip" data-custom-class="header-tooltip"
                                        data-trigger="hover" data-placement="bottom" title=""
                                        data-original-title="Current Client and Fiscal Year">
                                        <input type="text" class="form-control searchNew" name="" id=""
                                            value="{{ $defaultClient }}" readonly>
                                        @if ($defaultFyear != 0)
                                            <div class="input-group-append"
                                                style="width: 60px; height: 28px !important; font-size: 0.9rem !important; font-weight: 400 !important;">
                                                <span class="input-group-text text-white">
                                                    {{ $defaultFyear }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {{-- <div class="col-sm-3 pl-0">
                                {{$qry->appends($_GET)->onEachSide(0)->links()}}
                            </div> --}}
                            </div>

                            <div class="d-flex text-right col-lg-3 justify-content-end">
                                {{ $qry->appends($_GET)->onEachSide(0)->links() }}
                                <form id="limit_form" class="ml-2 mb-0"
                                    action="{{ url('remittances') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                    <select name="limit" class="float-right form-control mr-3   px-0" style="width:auto">
                                        <option value="10" {{ @$limit == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ @$limit == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ @$limit == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ @$limit == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </form>

                                @if (@Auth::user()->role == 'admin')

                                    <a href="javascript:;" data-toggle="tooltip" data-custom-class="header-tooltip"
                                        data-title="Settings" class="mr-3 text-dark headerSetting d3   "><img
                                            src="{{ asset('public/img/ui-icon-settings.png') }}"></a>

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

                    <div class="col-lg-4 no-print" style="overflow-y: auto;height: 90vh;">
                        <div style="overflow-y: auto;height: 90vh;">
                            <?php
                            function getFiscalYearEnd($period, $month, $year)
                            {
                                $monthCalendar = [$month];
                                while ($period <= 12) {
                                    $month = $month + 1;
                                    if ($month == 13) {
                                        $month = 1;
                                    }
                                    array_push($monthCalendar, $month);
                                    if (count($monthCalendar) == 12) {
                                        break;
                                    }
                                    $period++;
                                }
                                foreach ($monthCalendar as $key => $m) {
                                    if ($m == 1 && $key != 0) {
                                        $year++;
                                    }
                                }
                                return $year;
                            }
                            
                            function findPeriod($fiscalStart, $dateString)
                            {
                                $startDate = strtotime($fiscalStart);
                                $endDate = strtotime($dateString);
                            
                                $diffMonths = (date('Y', $endDate) - date('Y', $startDate)) * 12 + date('n', $endDate) - date('n', $startDate) + 1;
                                $period = $diffMonths > 0 ? $diffMonths : 12 - abs($diffMonths % 12);
                            
                                if ($period > 12) {
                                    return '';
                                }
                            
                                $periodString = str_pad($period, 2, '0', STR_PAD_LEFT);
                                return $periodString;
                            }
                            function remittanceCalender($remittance, $month_no, $year)
                            {
                                $result = [];
                                if ($remittance == 'Quarterly') {
                                    for ($i = 0; $i < 3; $i++) {
                                        $result[] = $month_no . '-' . $year;
                                        if ($month_no == 1) {
                                            $month_no = 12;
                                            $year--;
                                        } else {
                                            $month_no--;
                                        }
                                    }
                                } elseif ($remittance == 'Yearly') {
                                    for ($i = 0; $i < 12; $i++) {
                                        $result[] = $month_no . '-' . $year;
                                        if ($month_no == 1) {
                                            $month_no = 12;
                                            $year--;
                                        } else {
                                            $month_no--;
                                        }
                                    }
                                } elseif ($remittance == 'Monthly') {
                                    $result[] = $month_no . '-' . $year;
                                }
                            
                                return $result;
                            }
                            ?>
                            @foreach ($qry as $q)
                                <?php
                                $calender = remittanceCalender($q->tax_remittance, $q->month, $q->year);
                                $calender_month = [];
                                $calender_year = [];
                                
                                foreach ($calender as $c) {
                                    $arr = explode('-', $c);
                                    array_push($calender_month, intval($arr[0]));
                                    array_push($calender_year, intval($arr[1]));
                                }
                                
                                $calender_month = array_values(array_unique($calender_month));
                                $calender_year = array_values(array_unique($calender_year));
                                
                                $month = $q->month;
                                $year = $q->year;
                                
                                $fs = $q->fiscal_start;
                                $fiscal_start = explode('-', $fs);
                                $fiscal_start = $fiscal_start[0];
                                $period = findPeriod($fs, $fiscal_start . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01');
                                $fiscal_year_end = getFiscalYearEnd(intval($period), intval($month), intval($year));
                                
                                $tax_remittance = $q->tax_remittance;
                                $taxes = $q->taxes;
                                $federal_tax = $q->federal_tax;
                                $provincial_tax = $q->provincial_tax;
                                $federal_credit = 0;
                                $federal_rev_credit = 0;
                                $federal_exp_credit = 0;
                                $federal_debit = 0;
                                $federal_rev_debit = 0;
                                $federal_exp_debit = 0;
                                $federal_remit = 0;
                                $federal_rev_remit = 0;
                                $federal_exp_remit = 0;
                                $provincial_credit = 0;
                                $provincial_rev_credit = 0;
                                $provincial_exp_credit = 0;
                                $provincial_debit = 0;
                                $provincial_rev_debit = 0;
                                $provincial_exp_debit = 0;
                                $provincial_remit = 0;
                                $provincial_rev_remit = 0;
                                $provincial_exp_remit = 0;
                                $total_remittance = 0;
                                $total_rev_remittance = 0;
                                $total_exp_remittance = 0;
                                
                                $whereClauses = [['j.client', $q->client], ['j.is_deleted', 0]];
                                
                                // $_federal_debit = DB::table('journals as j')
                                //     ->where($whereClauses)
                                //     ->where('j.debit', '>', 0)
                                //     ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
                                //         if ($tax_remittance == 'Monthly') {
                                //             $query->where('j.month', $month)->where('j.year', $year);
                                //         }
                                //         if ($tax_remittance == 'Quarterly') {
                                //             $query->where(function ($subquery) use ($calender) {
                                //                 foreach ($calender as $key => $range) {
                                //                     $_e = explode('-', $range);
                                //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));
                                //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));
                                //                     if ($key == 0) {
                                //                         $subquery->where(function ($q) use ($m, $y) {
                                //                             $q->where('j.month', $m)->where('j.year', $y);
                                //                         });
                                //                     } else {
                                //                         $subquery->orWhere(function ($q) use ($m, $y) {
                                //                             $q->where('j.month', $m)->where('j.year', $y);
                                //                         });
                                //                     }
                                //                 }
                                //             });
                                //         }
                                //         if ($tax_remittance == 'Yearly') {
                                //             $query->where('j.fyear', $fiscal_year_end);
                                //         }
                                //         $query->where('j.account_no', $federal_tax);
                                //     })
                                //     ->leftJoin('source_code as sc', function ($join) {
                                //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                                //     })
                                //     ->select('j.*', 'sc.source_code')
                                //     ->orderBy('j.editNo', 'asc')
                                //     ->orderBy('j.debit', 'asc')
                                //     ->get();
                                
                                // $_federal_credit = DB::table('journals as j')
                                //     ->where($whereClauses)
                                //     ->where('j.credit', '>', 0)
                                //     ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
                                //         if ($tax_remittance == 'Monthly') {
                                //             $query->where('j.month', $month)->where('j.year', $year);
                                //         }
                                //         if ($tax_remittance == 'Quarterly') {
                                //             $query->where(function ($subquery) use ($calender) {
                                //                 foreach ($calender as $key => $range) {
                                //                     $_e = explode('-', $range);
                                //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));
                                //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));
                                //                     if ($key == 0) {
                                //                         $subquery->where(function ($q) use ($m, $y) {
                                //                             $q->where('j.month', $m)->where('j.year', $y);
                                //                         });
                                //                     } else {
                                //                         $subquery->orWhere(function ($q) use ($m, $y) {
                                //                             $q->where('j.month', $m)->where('j.year', $y);
                                //                         });
                                //                     }
                                //                 }
                                //             });
                                //         }
                                //         if ($tax_remittance == 'Yearly') {
                                //             $query->where('j.fyear', $fiscal_year_end);
                                //         }
                                //         $query->where('j.account_no', $federal_tax);
                                //     })
                                //     ->leftJoin('source_code as sc', function ($join) {
                                //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                                //     })
                                //     ->select('j.*', 'sc.source_code')
                                //     ->orderBy('j.editNo', 'asc')
                                //     ->orderBy('j.credit', 'asc')
                                //     ->get();
                                $_federal_debit = DB::table('remit_federal_debit')->where('remit_id', $q->id)->get();
                                $_federal_credit = DB::table('remit_federal_credit')->where('remit_id', $q->id)->get();
                                
                                $_provincial_debit = [];
                                $_provincial_credit = [];
                                if ($taxes == 'Both') {
                                    $_provincial_debit = DB::table('remit_provincial_debit')->where('remit_id', $q->id)->get();
                                    $_provincial_credit = DB::table('remit_provincial_credit')->where('remit_id', $q->id)->get();
                                    // $_provincial_debit = DB::table('journals as j')
                                    //     ->where($whereClauses)
                                    //     ->where('j.debit', '>', 0)
                                    //     ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
                                    //         if ($tax_remittance == 'Monthly') {
                                    //             $query->where('j.month', $month)->where('j.year', $year);
                                    //         }
                                    //         if ($tax_remittance == 'Quarterly') {
                                    //             $query->where(function ($subquery) use ($calender) {
                                    //                 foreach ($calender as $key => $range) {
                                    //                     $_e = explode('-', $range);
                                    //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));
                                    //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));
                                    //                     if ($key == 0) {
                                    //                         $subquery->where(function ($q) use ($m, $y) {
                                    //                             $q->where('j.month', $m)->where('j.year', $y);
                                    //                         });
                                    //                     } else {
                                    //                         $subquery->orWhere(function ($q) use ($m, $y) {
                                    //                             $q->where('j.month', $m)->where('j.year', $y);
                                    //                         });
                                    //                     }
                                    //                 }
                                    //             });
                                    //         }
                                    //         if ($tax_remittance == 'Yearly') {
                                    //             $query->where('j.fyear', $fiscal_year_end);
                                    //         }
                                    //         $query->where('j.account_no', $provincial_tax);
                                    //     })
                                    //     ->leftJoin('source_code as sc', function ($join) {
                                    //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                                    //     })
                                    //     ->select('j.*', 'sc.source_code')
                                    //     ->orderBy('j.editNo')
                                    //     ->orderBy('j.debit')
                                    //     ->get();
                                
                                    // $_provincial_credit = DB::table('journals as j')
                                    //     ->where($whereClauses)
                                    //     ->where('j.credit', '>', 0)
                                    //     ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
                                    //         if ($tax_remittance == 'Monthly') {
                                    //             $query->where('j.month', $month)->where('j.year', $year);
                                    //         }
                                    //         if ($tax_remittance == 'Quarterly') {
                                    //             $query->where(function ($subquery) use ($calender) {
                                    //                 foreach ($calender as $key => $range) {
                                    //                     $_e = explode('-', $range);
                                    //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));
                                    //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));
                                    //                     if ($key == 0) {
                                    //                         $subquery->where(function ($q) use ($m, $y) {
                                    //                             $q->where('j.month', $m)->where('j.year', $y);
                                    //                         });
                                    //                     } else {
                                    //                         $subquery->orWhere(function ($q) use ($m, $y) {
                                    //                             $q->where('j.month', $m)->where('j.year', $y);
                                    //                         });
                                    //                     }
                                    //                 }
                                    //             });
                                    //         }
                                    //         if ($tax_remittance == 'Yearly') {
                                    //             $query->where('j.fyear', $fiscal_year_end);
                                    //         }
                                    //         $query->where('j.account_no', $provincial_tax);
                                    //     })
                                    //     ->leftJoin('source_code as sc', function ($join) {
                                    //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                                    //     })
                                    //     ->select('j.*', 'sc.source_code')
                                    //     ->orderBy('j.editNo')
                                    //     ->orderBy('j.credit')
                                    //     ->get();
                                    // $_provincialRev_debit = DB::table('journals as j')
                                    //     ->where($whereClauses)
                                    //     ->where('j.debit', '>', 0)
                                    //     ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
                                    //         if ($tax_remittance == 'Monthly') {
                                    //             $query->where('j.month', $month)->where('j.year', $year);
                                    //         }
                                    //         if ($tax_remittance == 'Quarterly') {
                                    //             $query->where(function ($subquery) use ($calender) {
                                    //                 foreach ($calender as $key => $range) {
                                    //                     $_e = explode('-', $range);
                                    //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));
                                    //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));
                                    //                     if ($key == 0) {
                                    //                         $subquery->where(function ($q) use ($m, $y) {
                                    //                             $q->where('j.month', $m)->where('j.year', $y);
                                    //                         });
                                    //                     } else {
                                    //                         $subquery->orWhere(function ($q) use ($m, $y) {
                                    //                             $q->where('j.month', $m)->where('j.year', $y);
                                    //                         });
                                    //                     }
                                    //                 }
                                    //             });
                                    //         }
                                    //         if ($tax_remittance == 'Yearly') {
                                    //             $query->where('j.fyear', $fiscal_year_end);
                                    //         }
                                    //         $query->where('j.account_no', $provincial_tax);
                                    //     })
                                    //     ->Join('clients_gifi as cg', function ($join) {
                                    //         $join->on('j.account_no', '=', 'cg.account_no')->where('cg.is_deleted', 0)->where('cg.sub_type', 'Revenue');
                                    //     })
                                    //     ->leftJoin('source_code as sc', function ($join) {
                                    //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                                    //     })
                                    //     ->select('j.*', 'sc.source_code')
                                    //     ->get();
                                
                                    $_provincialRev_debit = DB::table('remit_rev_debit')->where('remit_id', $q->id)->get();
                                }
                                foreach ($_federal_debit as $f) {
                                    $federal_debit += $f->debit;
                                }
                                foreach ($_federal_credit as $f) {
                                    $federal_credit += $f->credit;
                                }
                                $federal_remit = $federal_credit - $federal_debit;
                                
                                foreach ($_provincial_debit as $p) {
                                    $provincial_debit += $p->debit;
                                }
                                foreach ($_provincial_credit as $p) {
                                    $provincial_credit += $p->credit;
                                }
                                $provincial_remit = $provincial_credit - $provincial_debit;
                                
                                $remittance = $federal_remit + $provincial_remit;
                                $total_debit = $federal_debit + $provincial_debit;
                                $total_credit = $federal_credit + $provincial_credit;
                                
                                ?>
                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent mr-2"
                                    data="{{ $q->id }}" style="cursor:pointer;">
                                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative"
                                        style="overflow: hidden;">
                                        @php
                                            if ($q->remit_status == 'paid') {
                                                $remit_class = 'ribbon-green';
                                            } elseif ($q->remit_status == 'not paid') {
                                                $remit_class = 'ribbon-orange';
                                            } else {
                                                $remit_class = 'ribbon-blue';
                                            }
                                        @endphp
                                        <div class="bubble-ribbon {{ $remit_class }}">{{ ucFirst($q->remit_status) }}</div>
                                        <div class=" justify-content-center align-items-center  d-flex mr-1"
                                            style="width: 20%;float: left;padding: 7px;">
                                            {{-- @if ($q->logo != '')
                                                <img src="{{ asset('/public') }}/client_logos/{{ $q->logo }}"
                                                    class="rounded-circle  "
                                                    style=" object-fit: cover;width: 70px;height: 70px;">
                                            @else --}}
                                            <img src="{{ asset('public/icons/remittance-icon.png') }}" class=""
                                                style="object-fit: cover;width: 65px;height: 65px;">
                                            {{-- @endif --}}
                                        </div>
                                        <div class=" d-flex justify-content-between" style="width: 70%;">
                                            <div class="d-flex flex-column " style="width: calc(100% - 40px);">
                                                <span class="font-12pt mb-0 text-truncate font-w600 c1"
                                                    style="font-family: Calibri;color:#626262 !important;">Sales Tax
                                                    Remitance</span>
                                                {{-- <span
                                                    style="overflow: hidden;
                                                        border-style: dashed !important;
                                                        text-overflow: ellipsis;
                                                        white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                                                        color: #262626;
                                                        min-width: 100%;
                                                        border:1px solid #262626;
                                                        background-color: #BFBFBF;
                                                        border-radius: 2px;
                                                        line-height: 1.6;
                                                        padding-top: 2px;
                                                        padding-bottom: 2px;
                                                        padding-left: 5px;
                                                        padding-right: 5px;">{{ $q->display_name }}</span> --}}
                                                <div class="d-flex flex-row" style="padding-top: 3px;">
                                                    <span class="bubble-account"
                                                        style="
                                                            text-overflow: ellipsis;
                                                            white-space: nowrap;
                                                            font-size:9pt;
                                                            width: fit-content;
                                                            font-family: Signika;
                                                            font-weight: 600;
                                                            color: #626262;
                                                            border:1px solid #595959;
                                                            /* background-color: #4194F6; */
                                                            border-radius: 5px;
                                                            line-height: 1.6;
                                                            /* padding-top: 2px;
                                                            padding-bottom: 2px; */
                                                            padding-left: 10px;
                                                            padding-right: 10px;
                                                            margin-right: 0.375rem;
                                                            ">
                                                        {{-- #{{ $q->editNo }} --}}
                                                        {{ $q->remit_end ? date('d-M-Y', strtotime($q->remit_end)) : '' }}
                                                    </span>
                                                    <span
                                                        style="overflow: hidden;
                                                                min-width: calc(100% - 160px);
                                                                /* border-style: dashed !important; */
                                                                text-overflow: ellipsis;
                                                                white-space: nowrap;font-size:10pt;
                                                                width: fit-content;
                                                                font-family: Calibri;
                                                                color: #626262;
                                                                /* border:1px solid #262626; */
                                                                /* background-color: #BFBFBF; */
                                                                /* border-radius: 2px; */
                                                                line-height: 1.6;
                                                                /* padding-top: 2px;
                                                                padding-bottom: 2px; */
                                                                padding-left: 5px;
                                                                padding-right: 5px;
                                                                ">
                                                        {{-- {{ $q->fyear }} - Period {{ $q->period }} --}}
                                                        {{ $q->display_name }}
                                                    </span>
                                                </div>
                                                <div class="d-flex  flex-nowrap overflow- hidden" style="padding-top: 5px;">

                                                    @if ($q->tax_remittance == 'Yearly')
                                                        @if (count($calender) > 0)
                                                            @if ($calender[0] == $calender[count($calender) - 1])
                                                                <div class="bubble_period"
                                                                    style="line-height: 1.6;
                                                                font-family: Calibri;
                                                                width: fit-content;
                                                                font-size: 9pt;
                                                                color:#989898;
                                                                border:1px solid #A6A6A6;
                                                                border-radius: 5px;

                                                                margin-right: 0.675rem;
                                                                                                    ">
                                                                    <span
                                                                        class="px-2">{{ date('M-Y', strtotime(explode('-', $calender[0])[1] . '-' . explode('-', $calender[0])[0])) }}</span>
                                                                </div>
                                                            @else
                                                                <div class="bubble_period"
                                                                    style="line-height: 1.6;
                                                                font-family: Calibri;
                                                                width: fit-content;
                                                                font-size: 9pt;
                                                                color:#989898;
                                                                border:1px solid #A6A6A6;
                                                                border-radius: 5px;

                                                                margin-right: 0.675rem;
                                                                                            ">
                                                                    <span
                                                                        class="px-2">{{ date('M-Y', strtotime(explode('-', $calender[count($calender) - 1])[1] . '-' . explode('-', $calender[count($calender) - 1])[0])) }}
                                                                        to
                                                                        {{ date('M-Y', strtotime(explode('-', $calender[0])[1] . '-' . explode('-', $calender[0])[0])) }}</span>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @php
                                                            if ($q->tax_remittance == 'Quarterly') {
                                                                $calender = array_reverse($calender);
                                                            }
                                                        @endphp
                                                        @foreach ($calender as $range)
                                                            <?php
                                                            $_e = explode('-', $range);
                                                            $date = date('M-Y', strtotime($_e[1] . '-' . $_e[0]));
                                                            
                                                            ?>
                                                            <div class="bubble_period"
                                                                style="line-height: 1.6;
                                                            font-family: Calibri;
                                                            width: fit-content;
                                                            font-size: 9pt;
                                                            color:#989898;
                                                            border:1px solid #A6A6A6;
                                                            border-radius: 5px;

                                                            margin-right: 0.675rem;
                                                                ">
                                                                <span class="px-2">{{ $date }}</span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $amount_clr = '';
                                                $text_clr = '';
                                                $amount = 0;
                                                $symbol = '';
                                                if ($remittance == 0) {
                                                    $amount_clr = '#BDBDBE ';
                                                    $text_clr = '#7F7F7F';
                                                } elseif ($remittance > 0) {
                                                    $amount_clr = '#E54643';
                                                    $text_clr = '#FFF';
                                                    $symbol = 'CR';
                                                } else {
                                                    $amount_clr = '#4194F6';
                                                    $text_clr = '#FFF';
                                                    $symbol = 'DR';
                                                }
                                            @endphp
                                            <div style="position: absolute;right: 10px;top: 10px;">
                                                @if ($remittance > 0)
                                                    <span class="px-2 bubble_credit"
                                                        style="float:right;
                                line-height: 1.6;
                                        font-family: Calibri;
                                        width: fit-content;
                                        font-size: 9pt;
                                        color:#C41E3A;
                                        border:1px solid #C41E3A;
                                        border-radius: 5px;">{{ $total_debit > $total_credit ? '($' . number_format(abs($remittance), 2) . ')' : '$' . number_format($remittance, 2) }}</span>
                                                @else
                                                    <span class="px-2 bubble_debit"
                                                        style="float:right;
                                line-height: 1.6;
                                        font-family: Calibri;
                                        width: fit-content;
                                        font-size: 9pt;
                                        color:#4194F6;
                                        border:1px solid #4194F6;
                                        border-radius: 5px;">{{ $total_debit > $total_credit ? '($' . number_format(abs($remittance), 2) . ')' : '$' . number_format($remittance, 2) }}</span>
                                                @endif
                                                {{-- <span
                                                    style="float:right;
                                font-family: Calibri;
                                line-height: 1.5 !important;
                                font-weight: 600!important;
                                border: 1px solid transparent;
                                color: {{ $text_clr }};
                                background-color: {{ $amount_clr }};
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
                            font-size: 11pt;">{{ $total_debit > $total_credit ? '($' . number_format(abs($remittance), 2) . ')' : '$' . number_format($remittance, 2) }}</span> --}}
                                            </div>
                                            <div class="d-flex flex-row justify-content-end"
                                                style="margin-top: 20px;position: absolute;right: 10px;bottom: 4px;">
                                                <?php     if(Auth::check()){
                                if(@Auth::user()->role!='read'){ ?>

                                                <div class="ActionIcon  ml-1    " style="border-radius: 1rem">
                                                    <a href="javascript:;" data="{{ $q->id }}" class="btnEdit ">
                                                        <img src="{{ asset('public') }}/icons2/icon-edit-grey.png?cache=1"
                                                            width="26px">
                                                    </a>
                                                </div>
                                                <div class="ActionIcon ml-2  " style="border-radius: 1rem">
                                                    <a href="javascript:;" class="btnDelete" data="{{ $q->id }}">
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
                    </div>


                    <form class="mb-0 pb-0" action="{{ url('end-gifi') }}" method="post">
                        @csrf
                        <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header"><span class="revokeText">Revoke</span> Gifi
                                                Account</span>
                                            <div class="block-options">
                                                <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                            <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                        </button> -->
                                            </div>
                                        </div>

                                        <div class="block-content new-block-content pt-0 pb-0 ">


                                            <input type="hidden" name="id">

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <textarea class="form-control" rows="5" required="" name="reason" id="reason"></textarea>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="block-content block-content-full   pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            <button type="submit" class="btn mr-3 btn-new  ">Save</button>
                                            <button type="button" class="btn     btn-new-secondary"
                                                data-dismiss="modal">Cancel</button>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </form>









                    <form class="mb-0 pb-0 insert-remittance" id="insert-remittance" action="{{ url('insert-remittance') }}"
                        method="post">
                        @csrf
                        <div class="modal fade" id="AddTaxModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac "
                                style="max-width: 650px !important;" role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Create Sales Tax Remittance</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="block-content new-block-content pt-0 pb-0 ">


                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label ">Client</label>
                                                </div>
                                                <div class="col-lg-9 ">
                                                    <select type="" name="client" class="form-control select2"
                                                        placeholder="">
                                                        <option value="" selected>Select Client</option>
                                                        @foreach ($clients as $c)
                                                            <option value="{{ $c->id }}"
                                                                {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}
                                                                data-fiscal-year-end="{{ $c->fiscal_year_end }}"
                                                                data-tax-remittance="{{ $c->tax_remittance }}">
                                                                {{ $c->display_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Fiscal Year End</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" name="fiscal_year_end"
                                                        placeholder="Fiscal Year End" disabled>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Remit Frequency</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" name="remit_frequency"
                                                        placeholder="Remit Frequency" disabled>
                                                </div>
                                            </div>

                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Year</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <select name="year" class="form-control select2" id="yearly">
                                                        <option value="" selected>Select Year</option>
                                                        @for ($y = intval(date('Y')) + 3; $y >= 1930; $y--)
                                                            <option value="{{ $y }}"
                                                                @if ($y == date('Y')) selected @endif>
                                                                {{ $y }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Month</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <select class="form-control select2" name="month" id="month">
                                                        <option value="" selected>Select Month</option>
                                                        @for ($m = 1; $m <= 12; $m++)
                                                            <option value="{{ $m }}"
                                                                @if ($m == intval(date('m')) - 1) selected @endif>
                                                                {{ monthToStringShort($m) }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" id="quarter">

                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Remittance Period</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="remit_start"
                                                        name="remit_start" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y">
                                                </div>
                                                <div class="col-lg-1">
                                                    <label class="col-form-label">to</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="remit_end"
                                                        name="remit_end" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y">
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Due Date</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="due_date"
                                                        name="due_date" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y">
                                                </div>
                                            </div>

                                            <div class="row justify-content- form-group  push">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label ">Taxes</label>
                                                </div>
                                                <div class="col-lg-2">
                                                    <input class="js-rangeslider" id="taxes" name="taxes"
                                                        data-values="Federal, Both" data-from="1">
                                                </div>
                                            </div>





                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            {{-- <a href="{{ url()->current() }}" class="btn mr-3 btn-new ">Clear</a> --}}
                                            <button type="submit" class="btn mr-3 btn-new btn-create">Create</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form class="mb-0 pb-0" id="update-remittance" action="{{ url('update-remittance') }}" method="post">
                        @csrf
                        <div class="modal fade" id="editTaxModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac "
                                style="max-width: 650px !important;" role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Edit Sales Tax Remittance</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="update_id" id="update_id">
                                        <div class="block-content new-block-content pt-0 pb-0 ">


                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label ">Client</label>
                                                </div>
                                                <div class="col-lg-9 ">
                                                    <input type="text" class="form-control" id="client_edit" disabled>
                                                </div>
                                            </div>

                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Year</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input type="text" class="form-control" name="year_edit"
                                                        id="year_edit" disabled>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Month</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input type="text" class="form-control" name="month_edit"
                                                        id="month_edit" disabled>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Taxes</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input type="text" class="form-control" name="taxes_edit"
                                                        id="taxes_edit" disabled>
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Remittance Period</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="remit_start_edit"
                                                        name="remit_start_edit" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y">
                                                </div>
                                                <div class="col-lg-1">
                                                    <label class="col-form-label">to</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="remit_end_edit"
                                                        name="remit_end_edit" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y">
                                                </div>
                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-3">
                                                    <label class="col-form-label">Due Date</label>
                                                </div>
                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="due_date_edit"
                                                        name="due_date_edit" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            {{-- <a href="{{ url()->current() }}" class="btn mr-3 btn-new ">Clear</a> --}}
                                            <button type="submit" class="btn mr-3 btn-new ">Update</button>
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





                    <form class="mb-0 pb-0 create-remittance-report" id="form-remittance-report"
                        action="{{ url('/remitance/report/remittance-status') }}" method="get">
                        <div class="modal fade" id="RemittanceReportModal" tabindex="-1" role="dialog"
                            data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Sales Tax Remittance Status</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="block-content new-block-content pt-0 pb-0 ">

                                            <div class="row justify-content- form-group  push">


                                                <div class="col-sm-3">
                                                    <label class="col-form-label ">Type</label>
                                                </div>

                                                <div class="col-sm-1">

                                                    <input class="js-rangeslider" id="report_type" name="report_type"
                                                        data-values="By Year,By Month" data-from="0">
                                                </div>
                                                <div class="col-sm-7">
                                                    <div class="dt-account-description-tag">By Year</div>
                                                </div>

                                            </div>

                                            <div class="row form-group">
                                                <div class="col-sm-3">
                                                    <label class="col-form-label ">Client</label>
                                                </div>
                                                <div class="col-sm-6 ">
                                                    <select type="" class="form-control    selectpicker "
                                                        id="report_client" data-style="btn-outline-light border text-dark"
                                                        data-actions-box="true" data-live-search="true" title="All"
                                                        value="" name="report_client[]" multiple="">
                                                        @foreach ($clients as $c)
                                                            <option value="{{ $c->id }}"
                                                                {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>
                                                                {{ $c->display_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row justify-content- form-group  push d-none " id="report_month_row">
                                                <div class="col-sm-3">
                                                    <label class="col-form-label ">Month</label>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select class="form-control select2" id="report_month"
                                                        name="report_month">
                                                        <option value="1">January</option>
                                                        <option value="2">February</option>
                                                        <option value="3">March</option>
                                                        <option value="4">April</option>
                                                        <option value="5">May</option>
                                                        <option value="6">June</option>
                                                        <option value="7">July</option>
                                                        <option value="8">August</option>
                                                        <option value="9">September</option>
                                                        <option value="10">October</option>
                                                        <option value="11">November</option>
                                                        <option value="12">December</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row justify-content- form-group  push ">
                                                <div class="col-sm-3">
                                                    <label class="col-form-label ">Year</label>
                                                </div>
                                                <div class="col-sm-2">
                                                    <select class="form-control select2" id="report_year" name="report_year">
                                                        @for ($i = intval(date('Y') + 3); $i >= 1940; $i--)
                                                            <option value="{{ $i }}"
                                                                {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;">
                                            <button type="button" class="btn  mr-3   btn-new-secondary" style=""
                                                id="clear_report_filters">Clear</button>
                                            <button type="submit" class="btn btn-new ">Run</button>


                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>


                    </form>

                    <form class="mb-0 pb-0" id="markAsPaidForm" action="{{ url('/mark-as-paid') }}" method="post">
                        @csrf
                        <div class="modal fade" id="markAsPaidModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " style="max-width: 750px;"
                                role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Mark as Paid</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="update_remit_id" id="update_remit_id">

                                        <div class="block-content new-block-content pt-0 pb-0 ">



                                            <div class="row justify-content- form-group  push">


                                                <div class="col-lg-4">
                                                    <label class="col-form-label">Payment Method</label>
                                                </div>

                                                <div class="col-lg-8 " style="padding-right: 1.75rem !important;">
                                                    <div class="d-flex flex-wrap justify-content-between">
                                                        <div class="payment-method">
                                                            <input type="radio" id="payment_method" name="payment_method"
                                                                checked="" value="Cheque">
                                                            <label class="btn btn-new" for="payment_method"
                                                                checked="">Cheque</label>
                                                        </div>
                                                        <div class="payment-method">
                                                            <input type="radio" id="payment_method0" name="payment_method"
                                                                value="Bank Transfer">
                                                            <label class="btn btn-new ml-1" for="payment_method0">Bank
                                                                Transfer</label>
                                                        </div>
                                                        <div class="payment-method">
                                                            <input type="radio" id="payment_method5" name="payment_method"
                                                                value="Other">
                                                            <label class="btn btn-new ml-1"
                                                                for="payment_method5">Other</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row form-group  ">
                                                <div class="col-lg-4">

                                                    <label class="col-form-label">Reference No</label>

                                                </div>

                                                <div class="col-lg-8 " style=" padding-right: 1.75rem !important;">
                                                    <input type="text" class="form-control" name="ref_no" id="ref_no"
                                                        style="text-transform: uppercase;" maxlength="10">
                                                </div>

                                            </div>
                                            <div class="row form-group  ">
                                                <div class="col-lg-4">

                                                    <label class="col-form-label">Date</label>

                                                </div>

                                                <div class="col-lg-4 ">
                                                    <input class="form-control js-flatpickr bg-white" id="date"
                                                        name="date" placeholder="" data-alt-input="true"
                                                        data-date-format="d-M-Y" data-alt-format="d-M-Y"
                                                        value="{{ date('d-M-Y') }}">
                                                </div>

                                            </div>

                                        </div>
                                        <div class="block-content block-content-full text-right  pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            <button type="button" class="btn mr-3 btn-new markaspaid">OK</button>
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
        $(function() {
            $(document).on('click', '.mark-as-paid', function() {
                var remit_id = $(this).attr('data');
                $('#update_remit_id').val(remit_id);
                $('#markAsPaidModal').modal('show');
            })
            $(document).on('click', '#markAsPaidForm .markaspaid', function() {
                var payment_method = $('input[name=payment_method]:checked').val();
                var ref_no = $('input[name=ref_no]').val();
                var date = $('input[name=date]').val();
                var remit_id = $('input[name=update_remit_id]').val();
                if (remit_id == "") {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Remittance not found.',
                        delay: 5000
                    });
                    return;
                }
                if (payment_method == undefined) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select Payment Method.',
                        delay: 5000
                    });
                    return;
                }
                if (ref_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Reference No.',
                        delay: 5000
                    });
                    return;
                }
                if (date == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Date.',
                        delay: 5000
                    });
                    return;
                }
                $('#markAsPaidForm .btn-new').prop('disabled', true);
                $('#markAsPaidForm').submit();

                // $.ajax({
                //     type: 'get',
                //     data: {
                //         payment_method: payment_method,
                //         ref_no: ref_no,
                //         date: date,
                //         remit_id: remit_id
                //     },
                //     url: '{{ url('/mark-as-paid') }}',
                //     dataType: 'json',
                //     beforeSend() {
                //     },

                //     success: function(res) {
                //         $('#markAsPaidForm .btn-new').prop('disabled', false);
                //         $('#markAsPaidModal').modal('hide');
                //         if (res == "success") {
                //             Dashmix.helpers('notify', {
                //                 from: 'bottom',
                //                 align: 'left',
                //                 message: `Sales Tax Remittance Marked as paid by ${payment_method} ${ref_no}.  `,
                //                 delay: 5000
                //             });

                //         } else {
                //             Dashmix.helpers('notify', {
                //                 from: 'bottom',
                //                 align: 'left',
                //                 message: 'Update Failed.',
                //                 delay: 5000
                //             });

                //         }
                //         resetfields();
                //         showData('{{ @$GETID }}');
                //         $('[data-toggle=tooltip]').tooltip();
                //     }
                // })
            })

            function resetfields() {
                $('#payment_method').prop('checked', true);
                $('#ref_no').val('');
                if ($("#markAsPaidForm #date").length) {
                    $("#markAsPaidForm #date").flatpickr().setDate(new Date());
                }
            }

            $(document).on('click', '.btn-update', function() {
                var remit_id = $(this).attr('data');

                if (remit_id) {
                    var $btn = $(this);
                    if ($btn.hasClass('disabled')) {
                        return;
                    }
                    $btn.addClass('expanded disabled');
                    $btn.css('color', '#fff');
                    $btn.html(
                        '<i class="fa fa-cog spinner"></i> Updating data...');
                    setTimeout(() => {
                        $.ajax({
                            type: 'get',
                            data: {
                                remit_id: remit_id
                            },
                            url: '{{ url('update-tax-content') }}',
                            dataType: 'json',
                            beforeSend() {
                                Dashmix.layout('header_loader_on');

                            },

                            success: function(res) {

                                if (res == "success") {
                                    Dashmix.helpers('notify', {
                                        from: 'bottom',
                                        align: 'left',
                                        message: 'Sales Tax Remittance updated.  ',
                                        delay: 5000
                                    });

                                } else {
                                    Dashmix.helpers('notify', {
                                        from: 'bottom',
                                        align: 'left',
                                        message: 'Update Failed.  ',
                                        delay: 5000
                                    });

                                }
                                showData(remit_id);

                                resetButton($btn);
                                $('[data-toggle=tooltip]').tooltip();
                            }
                        })
                    }, 1000);
                }
            })

            function resetButton(button) {
                button.removeClass('expanded disabled');
                button.html('<img src="{{ asset('public') }}/icons/rotate-right.png" width="20px">');
            }
            $(document).on('change', '.create-remittance-report input[name=report_type]', function() {
                if ($(this).val() == 'By Month') {
                    $('.dt-account-description-tag').html('By Month');
                    $(".create-remittance-report #report_month_row").removeClass('d-none')
                } else {
                    $('.dt-account-description-tag').html('By Year');
                    $(".create-remittance-report #report_month_row").addClass('d-none')
                }
            })
            $(document).on('change', '.create-progress-report input[name=report_type]', function() {
                if ($(this).val() == 'By Period') {
                    $(".create-progress-report #report_fiscal_years_row").addClass('d-none')
                    $(".create-progress-report #report_fiscal_year_row").removeClass('d-none')
                } else {
                    $(".create-progress-report #report_fiscal_year_row").addClass('d-none')
                    $(".create-progress-report #report_fiscal_years_row").removeClass('d-none')
                }
            })
            $(document).on('click', '.rep-btn', function() {
                $('#RemittanceReportModal').modal('show');
            })
            $(document).on('click', '.c-remittance-btn', function() {
                $('#AddTaxModal').modal('show');
            })
            Dashmix.helpers('rangeslider')
            @if (Session::has('success'))
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> {{ Session::get('success') }}',
                    delay: 5000
                });
            @endif

            // $(document).on('change', '#insert-remittance #yearly, #insert-remittance #month', function() {
            //     let year = $('#insert-remittance #yearly option:selected').val();
            //     let month = $('#insert-remittance #month option:selected').val();

            //     if (year && month) {
            //         // Convert month name to month number
            //         let date = new Date(Date.parse(month + " 1, " + year));
            //         let monthNumber = date.getMonth(); // 0-based index
            //         let monthAbbr = date.toLocaleString('en-US', {
            //             month: 'short'
            //         }); // Get abbreviated month name (e.g., "Jan")

            //         // Get the first day of the month
            //         let remitStartDate = `01-${monthAbbr}-${year}`;

            //         // Get the last day of the selected month
            //         let lastDay = new Date(year, monthNumber + 1, 0).getDate();
            //         let remitEndDate = `${lastDay}-${monthAbbr}-${year}`;

            //         // Calculate RemitDueDate (Last day of next month)
            //         let nextMonth = new Date(year, monthNumber + 1, 1); // Move to next month
            //         let nextMonthAbbr = nextMonth.toLocaleString('en-US', {
            //             month: 'short'
            //         }); // Get next month name
            //         let nextYear = nextMonth.getFullYear(); // Handle year change (Dec → Jan)

            //         let dueLastDay = new Date(nextYear, nextMonth.getMonth() + 1, 0)
            //     .getDate(); // Last day of next month
            //         let remitDueDate = `${dueLastDay}-${nextMonthAbbr}-${nextYear}`;

            //         // Update Flatpickr inputs
            //         let startPicker = document.getElementById('remit_start')._flatpickr;
            //         let endPicker = document.getElementById('remit_end')._flatpickr;
            //         let duePicker = document.getElementById('due_date');

            //         if (startPicker) startPicker.setDate(remitStartDate, true);
            //         if (endPicker) endPicker.setDate(remitEndDate, true);
            //         if (duePicker) duePicker._flatpickr.setDate(remitDueDate,
            //         true); // Ensure Flatpickr updates the due date
            //     }
            // });

            // $(document).on('change', '#remit_end', function() {
            //     let remitEndVal = $(this).val();

            //     if (remitEndVal) {
            //         // Parse selected date
            //         let parts = remitEndVal.split('-'); // Expected format: "DD-MMM-YYYY"
            //         let day = parseInt(parts[0], 10);
            //         let monthAbbr = parts[1];
            //         let year = parseInt(parts[2], 10);

            //         // Convert month abbreviation to month number
            //         let date = new Date(Date.parse(monthAbbr + " 1, " + year));
            //         let monthNumber = date.getMonth(); // 0-based index

            //         // Move to next month
            //         let nextMonth = new Date(year, monthNumber + 1, 1);
            //         let nextMonthAbbr = nextMonth.toLocaleString('en-US', { month: 'short' });
            //         let nextYear = nextMonth.getFullYear(); // Handle year change

            //         // Get the last day of the next month
            //         let dueLastDay = new Date(nextYear, nextMonth.getMonth() + 1, 0).getDate();
            //         let remitDueDate = `${dueLastDay}-${nextMonthAbbr}-${nextYear}`;

            //         // Update Flatpickr input for remit_due
            //         let duePicker = document.getElementById('due_date')._flatpickr;
            //         if (duePicker) duePicker.setDate(remitDueDate, true);
            //     }
            // });




            showData('{{ @$GETID }}');

            function showData(id) {
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
                    url: '{{ url('get-remittance-content') }}',
                    dataType: 'json',
                    beforeSend() {
                        Dashmix.layout('header_loader_on');

                    },

                    success: function(res) {

                        Dashmix.layout('header_loader_off');
                        $('#showData').html(res);
                        $('.tooltip').tooltip('hide');


                        $('[data-toggle=tooltip]').tooltip();
                    }
                })
            }

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


            $('#form-add-tax').submit(function(e) {
                e.preventDefault();

                var account_no = $('input[name=account_no]').val();
                var description = $('input[name=description]').val()
                var min = parseInt($('option:selected', $('select[name=sub_account_type]')).attr(
                    'data-min'))
                var max = parseInt($('option:selected', $('select[name=sub_account_type]')).attr(
                    'data-max'))
                var check = 1;
                $.ajax({
                    type: 'get',
                    data: {
                        account_no: account_no
                    },
                    url: '{{ url('check-gifi') }}',
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
                } else if (account_no < min || account_no > max) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > GIFI account number not in valid range.',
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
                } else {
                    $('#form-add-tax')[0].submit()
                }
            })


            var attachments_file = [];

            var commentArray = [];
            var comment_key_count = 0;

            var attachmentArray = [];
            var attachment_key_count = 0;
            $(document).on('click', '.btnEdit', function() {
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
                    url: '{{ url('get-remit-content') }}',

                    beforeSend() {
                        Dashmix.layout('header_loader_on');

                    },

                    success: function(res) {
                        const month_name = {
                            '1': 'January',
                            '2': 'February',
                            '3': 'March',
                            '4': 'April',
                            '5': 'May',
                            '6': 'June',
                            '7': 'July',
                            '8': 'August',
                            '9': 'September',
                            '10': 'October',
                            '11': 'November',
                            '12': 'December',
                        }

                        const month_short_name = {
                            '01': 'Jan',
                            '02': 'Feb',
                            '03': 'Mar',
                            '04': 'Apr',
                            '05': 'May',
                            '06': 'Jun',
                            '07': 'Jul',
                            '08': 'Aug',
                            '09': 'Sep',
                            '10': 'Oct',
                            '11': 'Nov',
                            '12': 'Dec'
                        };

                        $('#update-remittance #update_id').val(res.id);
                        $('#client_edit').val(res.display_name);
                        $('#year_edit').val(res.year);
                        $('#month_edit').val(month_name[res.month]);
                        $('#taxes_edit').val(res.taxes);

                        function formatDate(dateString) {
                            let parts = dateString.split('-');
                            return `${parts[2]}-${month_short_name[parts[1]]}-${parts[0]}`;
                        }

                        // Convert database date format to DD-MM-YYYY
                        var remitStartFormatted = '';
                        var remitEndFormatted = '';
                        var dueDateFormatted = '';
                        if (res.remit_start) {
                            remitStartFormatted = formatDate(res.remit_start);
                        }
                        if (res.remit_end) {
                            remitEndFormatted = formatDate(res.remit_end);
                        }
                        if (res.due_date) {
                            dueDateFormatted = formatDate(res.due_date);
                        }

                        // console.log(remitStartFormatted, remitEndFormatted, dueDateFormatted);
                        $('#remit_start_edit').flatpickr().setDate(remitStartFormatted, true);
                        $('#remit_end_edit').flatpickr().setDate(remitEndFormatted, true);
                        $('#due_date_edit').flatpickr().setDate(dueDateFormatted, true);

                        $('#editTaxModal').modal('show');
                    }
                })


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
                        message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Export Complete.  ',
                        delay: 5000
                    });
                    form.submit();
                    $('#ExportModal').modal('hide')
                } else {

                }
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

            $('select[name=account_type_edit]').change(function() {
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
                        $('select[name=sub_account_type_edit]').html(html);
                    }
                })
            })
            $('select[name=account_type]').change(function() {
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
                        $('select[name=sub_account_type]').html(html);
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


            // $('#showdata').on('click', '.btnEdit', function() {
            //     var id = $(this).attr('data');
            //     $.ajax({
            //         type: 'get',
            //         data: {
            //             id: id
            //         },
            //         url: '{{ url('show-sla') }}',
            //         success: function(res) {
            //             $('#viewData').modal('show');



            //             $('#cert_hostname').html(res.hostname)
            //             $('#cert_status').html(res.cert_status != null ? res.cert_status
            //                 .toUpperCase() : '')
            //             $('#cert_notification').html(res.cert_notification == '1' ?
            //                 '<div class="badge badge-success">On</div>' :
            //                 '<div class="badge badge-danger">Off</div>')
            //             $('#cert_type').html(res.cert_type != null ? res.cert_type
            //                 .toUpperCase() : '')
            //             $('#cert_issuer').html(res.cert_issuer)




            //             if (res.attachment != '' && res.attachment != null) {
            //                 ht = '';
            //                 var attachments = res.attachment.split(',');
            //                 for (var i = 0; i < attachments.length; i++) {
            //                     var icon = 'fa-file';

            //                     var fileExtension = attachments[i].split('.').pop();

            //                     if (fileExtension == 'pdf') {
            //                         icon = 'fa-file-pdf';
            //                     } else if (fileExtension == 'doc' || fileExtension == 'docx') {
            //                         icon = 'fa-file-word'
            //                     } else if (fileExtension == 'txt') {
            //                         icon = 'fa-file-alt';

            //                     } else if (fileExtension == 'csv' || fileExtension == 'xlsx' ||
            //                         fileExtension == 'xlsm' || fileExtension == 'xlsb' ||
            //                         fileExtension == 'xltx') {
            //                         icon = 'fa-file-excel'
            //                     } else if (fileExtension == 'png' || fileExtension == 'jpeg' ||
            //                         fileExtension == 'jpg' || fileExtension == 'gif' ||
            //                         fileExtension == 'webp' || fileExtension == 'svg') {
            //                         icon = 'fa-image'
            //                     }
            //                     ht += '<span class="attachmentDiv mr-2"><i class="fa ' + icon +
            //                         ' text-danger"></i><a class="text-dark"  href="{{ asset('public/ssl_attachment') }}/' +
            //                         attachments[i] + '" target="_blank"> ' + attachments[i] +
            //                         '</a></span>';
            //                 }
            //                 $('#attachmentDisplay').html(ht)
            //             } else {
            //                 $('#attachmentDisplay').html('')
            //             }


            //             $('#created_at').html(res.created_at)
            //             $('#created_by').html(res.created_by != null ? res.created_firstname +
            //                 ' ' + res.created_lastname : '')
            //             $('#updated_by').html(res.updated_by != null ? res.updated_firstname +
            //                 ' ' + res.updated_lastname : '')
            //             $('#updated_at').html(res.updated_at)

            //             $('#cert_name').html(res.cert_name)
            //             $('#cert_email').html(res.cert_email)
            //             $('#cert_company').html(res.cert_company)
            //             $('#cert_department').html(res.cert_department)

            //             $('#cert_city').html(res.cert_city)
            //             $('#cert_state').html(res.cert_state)
            //             $('#cert_country').html(res.cert_country)
            //             $('#cert_san1_5').html(res.cert_san1_5)
            //             $('#cert_ip_int').html(res.cert_ip_int)
            //             $('#cert_ip_pub').html(res.cert_ip_pub)
            //             $('#cert_edate').html(res.cert_edate)
            //             $('#cert_csr').html(res.cert_csr)
            //             $('#cert_process').html(res.cert_process)

            //             const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July",
            //                 "Aug", "Sep", "Oct", "Nov", "Dec"
            //             ];
            //             var cert_rdate = '';
            //             if (res.cert_rdate == '' || res.cert_rdate == null) {
            //                 cert_rdate = '';
            //             } else {

            //                 var cert_rdateObject = new Date(res.cert_rdate);
            //                 var cert_rdate = cert_rdateObject.getFullYear() + '-' + monthNames[
            //                         cert_rdateObject.getMonth()] + '-' + cert_rdateObject
            //                     .getDate();
            //             }

            //             var cert_edate = '';
            //             if (res.cert_edate == '' || res.cert_edate == null) {
            //                 cert_edate = '';
            //             } else {
            //                 var cert_edateObject = new Date(res.cert_edate);
            //                 cert_edate = cert_edateObject.getFullYear() + '-' + monthNames[
            //                         cert_edateObject.getMonth()] + '-' + cert_edateObject
            //                     .getDate();
            //             }

            //             var status = '';
            //             var MyDate = new Date('<?php echo date('m/d/Y'); ?>');

            //             const diffTime = Math.abs(cert_edate - MyDate);
            //             const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            //             if (res.cert_status == 'Active') {

            //                 if (diffDays <= 30) {
            //                     status = 'upcoming.png';

            //                 } else {
            //                     status = 'active.png';
            //                 }


            //             } else if (res.cert_status == 'Inactive') {
            //                 status = 'renewed.png';

            //             } else if (res.cert_status == 'Expired/Ended') {
            //                 status = 'ended.png';
            //             } else if (res.cert_status == 'Expired') {
            //                 status = 'expired.png';
            //             } else {
            //                 status = 'active.png';
            //             }




            //             $('#hostnameDisplay').html(
            //                 '<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover"  src="' +
            //                 operating_system +
            //                 '"  alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><img class="  mr-3 atar48" width="30px"    src="{{ asset('public/img/') }}/' +
            //                 status + '" alt=""><b>' + res.cert_name +
            //                 '</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">' +
            //                 (res.cert_type != null ? res.cert_type.toUpperCase() : '') +
            //                 '</span></p></div></div>')


            //             $('#clientLogo').html(
            //                 '<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{ asset('public/client_logos/') }}/' +
            //                 res.logo + '" alt="">');





            //             if (res.comments == '' || res.comments == null) {
            //                 $('.commentsDiv').addClass('d-none')
            //             } else {
            //                 $('.commentsDiv').removeClass('d-none')
            //             }


            //             if (res.attachment == '' || res.attachment == null) {
            //                 $('.attachmentsDiv').addClass('d-none')
            //             } else {
            //                 $('.attachmentsDiv').removeClass('d-none')
            //             }
            //             $('#cert_rdate').html(cert_rdate)
            //             $('#cert_msrp').html(res.cert_msrp)
            //             $('#cert_edate').html(cert_edate)

            //         }
            //     })

            // })


            $(document).on('click', '.btnDelete', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var id = $(this).attr('data');

                var c = confirm("Are you sure want to delete this Remittance");
                if (c) {
                    window.location.href = "{{ url('delete-remittance') }}?id=" + id;
                }
            })



            let click = 0;
            $('input,textarea').on('keyup', function() {
                click = 1;

            })

            $('select').on('change', function() {
                click = 1;

            })





            $(document).on('click', '.btnClose', function() {
                var id = $(this).attr('data')
                if (click == 1) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: 'Close window?  <a href="javascript:;" data-notify="dismiss" data="' +
                            id + '" class="  btn-notify btnCloseUndo ml-4" >Proceed</a>',
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
                            name: '{{ Auth::user()->firstname . '' . Auth::user()->lastname }}'
                        });
                        showComment()
                        $('#CommentModal').modal('hide')
                        $('textarea[name=comment]').val('')
                        comment_key_count++;
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

                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Comment Deleted. <a href="javascript:;" data-notify="dismiss" class="  btn-notify btnCommentUndo ml-4" data1=' +
                        id + ' data=' + key + '>Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
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
                    html += `    <div class="js-task block block-rounded mb-2 animated fadeIn"   data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{ asset('public/img/profile-white.png') }}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${commentArray[i].name}<br><span class="comments-subtext">On ${commentArray[i].date} at ${commentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                         <a type="button"  data="${i}" class="j btnEditComment btn btn-sm btn-link text-warning">
                                                         <img src="{{ url('public/img/editing.png') }}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button"   data="${i}" class="btnDeleteComment btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/img/trash--v1.png') }}" width="24px">
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
                    url: "{{ url('get-comments-gifi') }}",
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
                                name: res[i].name
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
                    url: "{{ url('get-attachment-gifi') }}",
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
                                name: res[i].name
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



                    for (var i = 0; i < attachment.length; i++) {
                        attachmentArray.push({
                            key: attachment_key_count,
                            attachment: attachment[i],
                            date: '{{ date('Y-M-d') }}',
                            time: '{{ date('h:i:s A') }}',
                            name: '{{ Auth::user()->firstname . '' . Auth::user()->lastname }}'
                        });
                        attachment_key_count++;
                    }

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
                    message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Attachment  Deleted. <a href="javascript:;" data-notify="dismiss" class="  btn-notify btnAttachmentUndo ml-4" data1=' +
                        id + ' data=' + key + '>Undo</a>',
                    delay: 5000,
                    type: 'info alert-notify-desktop'
                });
                showAttachment();

            })


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


                    html += `   <div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{ asset('public/img/profile-white.png') }}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                           <h2 class="mb-0 comments-text">${attachmentArray[i].name}<br><span class="comments-subtext">On ${attachmentArray[i].date} at ${attachmentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                       <!-- -->

                                                        <a type="button"  class="  btnDeleteAttachment    btn btn-sm btn-link text-danger"  data="${i}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/img/trash--v1.png') }}" width="24px">
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


                var account_no = $('input[name=account_no_edit]').val();
                var description = $('input[name=description_edit]').val()
                var min = parseInt($('option:selected', $('select[name=sub_account_type_edit]')).attr(
                    'data-min'))
                var max = parseInt($('option:selected', $('select[name=sub_account_type_edit]')).attr(
                    'data-max'))
                var tele_regex1 = /^.{0,65}$/;
                if (account_no == '') {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Account No.',
                        delay: 5000
                    });
                } else if (account_no < min || account_no > max) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > GIFI account number not in valid range.',
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
                        'url': '{{ url('update-gifi') }}',
                        dataType: 'json',
                        async: false,

                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(res) {

                            Dashmix.helpers('notify', {
                                from: 'bottom',
                                align: 'left',
                                message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Gifi Account successfully saved',
                                delay: 5000
                            });
                            showData(data1)
                            click = 0;

                        }
                    })

                }

            })









        });



        /**
         * Insert Remittance
         */

        $(document).ready(function() {
            let showRemitted = 0,
                remitsThisMonth = 0;

            function getRemittedClients() {
                const remits_this_month = remitsThisMonth;
                const show_remitted = showRemitted;
                $.ajax({
                    type: "GET",
                    url: "{{ url('/remittances/get-clients') }}",
                    data: {
                        "remits_this_month": remits_this_month,
                        "show_remitted": show_remitted
                    }
                }).done(function(response) {
                    console.log(response);
                    let html = ``;
                    response.forEach(element => {
                        html +=
                            `<option value="${element.id}">${element.company}</option>`;
                    });
                    $(".insert-remittance select[name=client]").html(html);
                });
            }
            $(".insert-remittance #remits_this_month").ionRangeSlider({
                skin: "round",
                type: "single",
                onChange: function(data) {
                    remitsThisMonth = data.from;
                    if (remitsThisMonth == 1) {
                        $(".insert-remittance #show_remitted").parent().parent().removeClass('d-none');
                        $(".insert-remittance #month").parent().parent().addClass('d-none');
                        $(".insert-remittance #yearly").parent().parent().addClass('d-none');
                        $(".insert-remittance #remit_year").parent().parent().addClass('d-none');
                        $(".insert-remittance #remit_month").parent().parent().addClass('d-none');
                        getRemittedClients()
                    } else {
                        $(".insert-remittance #show_remitted").parent().parent().addClass('d-none');
                        $(".insert-remittance #yearly").parent().parent().removeClass('d-none');
                        $(".insert-remittance #month").parent().parent().removeClass('d-none');
                        $(".insert-remittance #remit_year").parent().parent().removeClass('d-none');
                        $(".insert-remittance #remit_month").parent().parent().removeClass('d-none');
                        getRemittedClients()
                    }
                }
            });
            $(".insert-remittance #show_remitted").ionRangeSlider({
                skin: "round",
                type: "single",
                onChange: function(data) {
                    showRemitted = data.from;
                    getRemittedClients()
                }
            });

        });

        $(document).ready(function() {
            function calculateRemittanceMonths(fiscalYearEndMonth) {
                const monthNumbers = ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];
                const fiscalYearEndIndex = monthNumbers.indexOf(fiscalYearEndMonth);
                let remittanceMonths = [];
                for (let i = 0; i < 4; i++) {
                    let remittanceIndex = (fiscalYearEndIndex - i * 3 + 12) % 12;
                    remittanceMonths.push(remittanceIndex + 1);
                }
                return remittanceMonths;
            }

            function remittanceCalendar(remittance, month_no, year) {
                var result = [];
                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July",
                    "Aug", "Sep", "Oct", "Nov", "Dec"
                ];
                if (remittance === "Quarterly") {
                    for (var i = 0; i < 3; i++) {
                        result.push(monthNames[month_no - 1] + '-' + year);
                        if (month_no === 1) {
                            month_no = 12;
                            year--;
                        } else {
                            month_no--;
                        }
                    }
                } else if (remittance === "Yearly") {
                    for (var i = 0; i < 12; i++) {
                        result.push(monthNames[month_no - 1] + '-' + year);
                        if (month_no === 1) {
                            month_no = 12;
                            year--;
                        } else {
                            month_no--;
                        }
                    }
                } else if (remittance === "Monthly") {
                    result.push(monthNames[month_no - 1] + '-' + year);
                }

                return result.reverse();
            }

            function findQuarter() {
                const tax_remittance = $('.insert-remittance select[name=client]').find('option:selected').attr(
                        'data-tax-remittance'),
                    year = parseInt($(".insert-remittance select[name=year]").val()),
                    month = parseInt($(".insert-remittance select[name=month]").val());


                let html = ``
                if (!isNaN(year) && !isNaN(month)) {
                    if (tax_remittance == 'Quarterly') {
                        const calendar = remittanceCalendar('Quarterly', month, year)

                        html += `<div class="col-lg-3">
                            <label class="col-form-label">Quarter</label>
                            </div><div class="col-lg-9 d-flex ">`

                        calendar.forEach(c => {
                            html += `<input type="text" class="form-control mr-2" value="${c}" disabled>`
                        })

                        html += `</div>`
                    }
                    if (tax_remittance == 'Yearly') {
                        const calendar = remittanceCalendar('Yearly', month, year)
                        html += ` <div class="col-lg-3">
                                <label class="col-form-label">Quarter</label>
                            </div><div class="col-lg-7 d-flex align-items-center">`

                        if (calendar.length > 0) {
                            html += `
                                    <input type="text" class="form-control" value="${calendar[0]}" disabled>
                                                    <span class="px-3">to</span>
                                                    <input type="text" class="form-control" value="${calendar[calendar.length - 1]}" disabled>
                                    `
                        }

                        html += `</div>`
                    }
                    if (tax_remittance == 'Monthly' || tax_remittance == 'Quarterly' || tax_remittance ==
                        'Yearly') {
                        const calendar = remittanceCalendar(tax_remittance, month, year);

                        if (calendar.length > 0) {
                            let firstMonthYear = calendar[0].split('-');
                            let lastMonthYear = calendar[calendar.length - 1].split('-');

                            let firstMonth = firstMonthYear[0];
                            let firstYear = firstMonthYear[1];

                            let lastMonth = lastMonthYear[0];
                            let lastYear = lastMonthYear[1];

                            let remitStartDate = `01-${firstMonth}-${firstYear}`;

                            let lastMonthDate = new Date(Date.parse(lastMonth + " 1, " +
                                lastYear));
                            let lastMonthIndex = lastMonthDate.getMonth();
                            let lastDay = new Date(lastYear, lastMonthIndex + 1, 0).getDate();
                            let remitEndDate = `${lastDay}-${lastMonth}-${lastYear}`;

                            let dueMonthDate = new Date(lastYear, lastMonthIndex + 1, 1);
                            let dueMonthAbbr = dueMonthDate.toLocaleString('en-US', {
                                month: 'short'
                            });
                            let dueYear = dueMonthDate.getFullYear();
                            let dueLastDay = new Date(dueYear, dueMonthDate.getMonth() + 1, 0).getDate();
                            let remitDueDate = `${dueLastDay}-${dueMonthAbbr}-${dueYear}`;

                            $('#remit_start').flatpickr().setDate(remitStartDate, true);
                            $('#remit_end').flatpickr().setDate(remitEndDate, true);
                            $('#due_date').flatpickr().setDate(remitDueDate, true);
                        }
                    }
                }

                $(".insert-remittance #quarter").html(html)
                $(".insert-remittance #quarter").addClass('form-group')
            }
            $(document).on('change', '.insert-remittance select[name=client]', function() {
                const fiscal_year_end = $(this).find('option:selected').attr('data-fiscal-year-end'),
                    tax_remittance = $(this).find('option:selected').attr('data-tax-remittance')
                $('.insert-remittance input[name=fiscal_year_end]').val(fiscal_year_end)
                $('.insert-remittance input[name=remit_frequency]').val(tax_remittance)
                findQuarter()
            })
            $(document).on('change', '.insert-remittance select[name=month]', function() {
                findQuarter()
            })
            $(document).on('change', '.insert-remittance select[name=year]', function() {
                findQuarter()
            })

            $('#insert-remittance').on('submit', function(e) {
                e.stopImmediatePropagation()
                e.preventDefault()

                const
                    client = $(".insert-remittance select[name=client]").val(),
                    tax_remittance = $('.insert-remittance select[name=client]').find('option:selected')
                    .attr('data-tax-remittance'),
                    fiscal_year_end = $('.insert-remittance select[name=client]').find('option:selected')
                    .attr('data-fiscal-year-end'),
                    year = parseInt($(".insert-remittance select[name=year]").val()),
                    month = parseInt($(".insert-remittance select[name=month]").val());

                if (client == "") {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > You must select client.',
                        delay: 5000
                    });
                    return false;
                }

                if (isNaN(year) || isNaN(month)) {
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > You must select year and month.',
                        delay: 5000
                    });
                    return false;
                }

                if (tax_remittance == 'Quarterly') {
                    if (fiscal_year_end != "") {
                        let fiscal_year_end_month = fiscal_year_end.split(" ")[0]
                        let quarters = calculateRemittanceMonths(fiscal_year_end_month)
                        if (!quarters.includes(month)) {
                            Dashmix.helpers('notify', {
                                from: 'bottom',
                                align: 'left',
                                message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"> Remittance month does not match clients year end <a href="javascript:;" data-notify="dismiss" class="  btn-notify proceed-pre-insert-remittance ml-4" >Proceed</a>',
                                delay: 5000,
                                type: 'info alert-notify-desktop'
                            });
                            return false;
                        }
                    }
                }
                if (tax_remittance == 'Yearly') {
                    if (fiscal_year_end != "") {
                        const monthNumbers = ["January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"
                        ];
                        let fiscal_year_end_month = fiscal_year_end.split(" ")[0].trim()
                        fiscal_year_end_month = monthNumbers.indexOf(fiscal_year_end_month) + 1
                        if (month != fiscal_year_end_month) {
                            Dashmix.helpers('notify', {
                                from: 'bottom',
                                align: 'left',
                                message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"> Remittance month does not match clients year end <a href="javascript:;" data-notify="dismiss" class="  btn-notify proceed-pre-insert-remittance ml-4" >Proceed</a>',
                                delay: 5000,
                                type: 'info alert-notify-desktop'
                            });
                            return false;
                        }
                    }
                }
                var btn = $('.btn-create');
                
                if (btn.hasClass('disabled')) {
                    return;
                }
                // Add spinner and expand button
                btn.addClass('expanded disabled'); // Disable the button and expand it
                btn.html('<i class="fa fa-cog spinner"></i> Processing...');
                setTimeout(() => {
                    preInsertRemittance()
                }, 500);
                
            })

            function resetButton_() {
                $('.btn-create').removeClass('expanded disabled');
                $('.btn-create').html('Create');
            }

            $(document).on('click', '.proceed-pre-insert-remittance', function() {
                preInsertRemittance()
            })

            $(document).on('click', '.proceed-insert-remittance', function() {
                $("#insert-remittance")[0].submit();
            })

            function preInsertRemittance() {
                var fd = $("#insert-remittance").serialize()
                $.ajax({
                    type: "POST",
                    url: "{{ url('/pre-insert-remittance') }}",
                    data: fd,
                }).done(function(response) {
                    if (response.status == "success") {
                        $("#insert-remittance")[0].submit();
                    } else {
                        Dashmix.helpers('notify', {
                            from: 'bottom',
                            align: 'left',
                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"> ' +
                                response.message +
                                ' <a href="javascript:;" data-notify="dismiss" class="  btn-notify proceed-insert-remittance ml-4" >Proceed</a>',
                            delay: 5000,
                            type: 'info alert-notify-desktop'
                        });
                    }
                    setTimeout(() => {
                        resetButton_();
                    }, 1000);
                })
            }
            $(document).on('shown.bs.modal', '#CommentModal', function() {
                $("#CommentModal textarea[name=comment]").focus();
            });

            $(document).on('shown.bs.modal', '#EndModal', function() {
                $('#EndModal textarea[name=reason]').focus();
            });
        });
        @if (Session::has('error'))
            Dashmix.helpers('notify', {
                from: 'bottom',
                align: 'left',
                message: `{{ Session::get('error') }}`,
                delay: 5000
            });
        @endif
    </script>
