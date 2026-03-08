@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <?php

    use Carbon\Carbon;

    $userAccess = explode(',', Auth::user()->access_to_client);

    $limit = 10;
    $user_id = Auth::id();
    $no_check = DB::table('settings')->where('user_id', $user_id)->first();

    if (request()->filled('limit')) {
        $limit = request('limit');
        if ($no_check) {
            DB::table('settings')
                ->where('user_id', $user_id)
                ->update(['sample_tests' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => $user_id, 'sample_tests' => $limit]);
        }
    } elseif ($no_check && !empty($no_check->sample_tests)) {
        $limit = $no_check->sample_tests;
    }

    $field = request('field', 'st.id');
    $orderby = request('orderBy', 'desc');

    $search = request('search');

    $users = DB::table('users as i')->where('i.is_deleted', 0)->get();

    // Filters
    $filter_test_name = request('filter_test_name');
    $filter_item_cat = request('filter_item_cat');
    $filter_asset_no = request('filter_asset_no');
    $filter_workorder_no = request('filter_workorder_no');
    $filter_bosubi = request('filter_bosubi');
    $filter_sample_date = request('filter_sample_date');
    $filter_production_date = request('filter_production_date');
    $filter_user = request('filter_user');

    // Base query
    $qry = DB::table('sample_tests as st')->leftJoin('test_definitions as td', 'td.id', '=', 'st.test_name_id')->leftJoin('workorders as w', 'w.id', '=', 'st.workorder_id')->leftJoin('assets as a', 'a.id', '=', 'st.asset_id')->leftJoin('users as u', 'u.id', '=', 'st.created_by')->where('st.is_deleted', 0);

    // Apply filters safely
    if (!empty($filter_test_name)) {
        $qry->where('td.test_name', 'like', '%' . $filter_test_name . '%');
    }
    if (!empty($filter_item_cat)) {
        $qry->where('st.item_category', 'like', '%' . $filter_item_cat . '%');
    }
    if (!empty($filter_asset_no)) {
        $qry->where('a.asset_no', 'like', '%' . $filter_asset_no . '%');
    }
    if (!empty($filter_workorder_no)) {
        $qry->where('w.workorder_no', 'like', '%' . $filter_workorder_no . '%');
    }
    if (!empty($filter_bosubi)) {
        $qry->where('st.bosubi', 'like', '%' . $filter_bosubi . '%');
    }
    if (!empty($filter_sample_date)) {
        $qry->where('st.sample_date', Carbon::createFromFormat('d-M-Y', $filter_sample_date)->format('Y-m-d'));
    }
    if (!empty($filter_production_date)) {
        $qry->where('st.production_date', Carbon::createFromFormat('d-M-Y', $filter_production_date)->format('Y-m-d'));
    }
    if (!empty($filter_user)) {
        $qry->where(DB::raw("CONCAT(u.firstname, ' ', u.lastname)"), 'like', '%' . $filter_user . '%');
    }

    // Apply search filter
    if (!empty($search)) {
        $qry->where(function ($query) use ($search) {
            $query
                ->where('td.test_name', 'like', '%' . $search . '%')
                ->orWhere('st.item_category', 'like', '%' . $search . '%')
                ->orWhere('st.itemcode', 'like', '%' . $search . '%')
                ->orWhere('st.lot', 'like', '%' . $search . '%')
                ->orWhere('w.workorder_no', 'like', '%' . $search . '%')
                ->orWhere('a.asset_no', 'like', '%' . $search . '%');
        });
    }

    // Total count with DISTINCT tt.id
    $totalQuery = clone $qry;
    $totalRows = $totalQuery->select(DB::raw('COUNT(DISTINCT st.id) as aggregate'))->first()->aggregate;

    // Final results with grouping
    $qry = $qry
        ->select('st.*', 'td.test_name', 'td.uom', 'w.workorder_no', 'a.machine_no')
        ->distinct('st.id')
        ->orderBy($field, $orderby)
        ->paginate($limit)
        ->appends(request()->query());

    // Get ID from GET or fallback to first result
    $GETID = !empty($_GET['id']) ? $_GET['id'] : $qry[0]->id ?? null;

    ?>




    <main id="main-container pt-0">
        <!-- Hero -->


        <style type="text/css">
            .selected-items-container.loading {
                opacity: 0.6;
                pointer-events: none;
            }

            .summary-loading-spinner {
                display: none;
                text-align: center;
                padding: 5px;
            }

            .summary-loading-spinner img {
                animation: spin 1s linear infinite;
                width: 16px;
                height: 16px;
                opacity: 0.7;
            }

            .summary-loading-text {
                font-size: 11px;
                color: #6c757d;
                margin-top: 2px;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .test-standard1 {
                transform: rotate(271deg);
                font-family: 'Signika' !important;
                font-size: 9pt;
                font-weight: 500;
                color: #7F7F7F;
            }

            .label-new {
                font-size: 10pt;
            }

            .responsive-break {
                display: none;
            }

            @media (max-width: 800px) {
                .responsive-break {
                    display: block;
                }
            }

            .dropdown-menu {
                z-index: 100000 !important;
            }

            .select2-selection__clear {
                display: inline !important;
                font-size: 18px;
                padding-right: 6px;
                color: #dc3545;
                /* optional for red color */
                cursor: pointer;
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

            .final-sample-number {
                font-size: 7pt !important;
            }


            /* Galaxy Tab A9+ landscape optimization */
            @mdia only screen and (orientation: landscape) and ((device-width: 1340px) and (device-height: 800px)),
            (min-width: 890px) and (max-width: 900px) {
                .header-new-text {
                    font-size: 13pt !important;
                }

                .sample-data-div .col-sm-2 {
                    flex: 0 0 auto;
                    width: 25% !important;
                    /* same as col-sm-3 */
                    max-width: 25% !important;
                }

                .bubble-div.col-lg-4 {
                    flex: 0 0 36.333333% !important;
                    max-width: 36.333333% !important;
                }

                #showData.col-lg-8 {
                    flex: 0 0 63.666667% !important;
                    max-width: 63.666667% !important;
                }

                .col-md-4.bubble-header {
                    flex: 0 0 36.333333% !important;
                    max-width: 36.333333% !important;
                }

                .col-md-8.detail-header {
                    flex: 0 0 63.666667% !important;
                    max-width: 63.666667% !important;
                }

                .bubble-item-title {
                    font-size: 11pt !important;
                }

                .titillium-web-light.bubble-item-title {
                    font-size: 8pt !important;
                }

                .bubble-status-active {
                    font-size: 8pt !important;
                }

                .search-col.col-sm-4 {
                    -ms-flex: 0 0 33.333333% !important;
                    flex: 0 0 36.333333% !important;
                    max-width: 36.333333% !important;
                }

                .col-lg-8.pr-sm-4 {
                    -ms-flex: 0 0 63.666667% !important;
                    flex: 0 0 63.666667% !important;
                    max-width: 63.666667% !important;
                }

                .search-col .col-sm-1 {
                    flex: 0 0 12.333333% !important;
                    max-width: 12.333333% !important;
                }

                .qc-test-result .col-sm-2 {
                    -ms-flex: 0 0 18.666667% !important;
                    flex: 0 0 18.666667% !important;
                    max-width: 18.666667% !important;
                }
            }

            /* Galaxy Tab A9+ portrait optimization */
            @media only screen and (min-width: 530px) and (max-width: 540px) and (min-height: 890px) and (max-height: 900px) and (orientation: portrait) {
                .new-header-icon-div img {
                    width: 20px !important;
                }

                .header-new-text {
                    font-size: 11pt !important;
                    line-height: 14px !important;
                }

                .header-new-subtext {
                    line-height: 13px !important;
                }

                .sample-data-div .col-sm-2 {
                    flex: 0 0 auto;
                    width: 30% !important;
                    /* same as col-sm-3 */
                    max-width: 30% !important;
                }

                .bubble-div.col-lg-4 {
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }

                #showData.col-lg-8 {
                    flex: 0 0 59.666667% !important;
                    max-width: 59.666667% !important;
                }

                .col-md-4.bubble-header {
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }

                .col-md-8.detail-header {
                    flex: 0 0 59.666667% !important;
                    max-width: 59.666667% !important;
                }

                .bubble-item-title {
                    font-size: 10pt !important;
                }

                .titillium-web-light.bubble-item-title {
                    font-size: 7pt !important;
                }

                .rounded-circle-div {
                    width: 30px !important;
                    height: 30px !important;
                }

                .bubble-status-active {
                    font-size: 7pt !important;
                }

                .final-sample-value {
                    font-size: 7pt !important;
                }

                .final-sample-number {
                    font-size: 6pt !important;
                }

                .search-col.col-sm-4 {
                    -ms-flex: 0 0 40.333333% !important;
                    flex: 0 0 40.333333% !important;
                    max-width: 40.333333% !important;
                }

                .col-lg-8.pr-sm-4 {
                    -ms-flex: 0 0 59.666667% !important;
                    flex: 0 0 59.666667% !important;
                    max-width: 59.666667% !important;
                }

                .search-col .col-sm-1 {
                    flex: 0 0 14.333333% !important;
                    max-width: 14.333333% !important;
                }

                .header-image {
                    width: 30px !important;
                    height: 30px !important;
                }

                .header-new-subtext {
                    font-size: 10pt !important;
                }

                .qc-test-result .col-sm-2 {
                    -ms-flex: 0 0 28.666667% !important;
                    flex: 0 0 28.666667% !important;
                    max-width: 28.666667% !important;
                }

                .bubble-header-text {
                    padding-left: 10px !important;
                }

                .content-div {
                    height: 90vh !important;
                }

                .new-header-icon-div a {
                    padding-left: 6px !important;
                    margin-left: 0px !important;
                    margin-right: 0px !important;
                    padding-right: 6px !important;
                    padding-top: 5px !important;
                    margin-top: 2px !important;
                    padding-bottom: 5px !important;
                }

                /* #SelectedSamples::-webkit-scrollbar-button {
                                                                                height: 8px !important;
                                                                            } */
            }



            .report-card {
                cursor: pointer;
                transition: all 0.2s ease;
                border: 1px solid #C0C0C0;
            }

            .report-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
            }

            .report-card.selected {
                border-color: #007bff;
                background-color: rgba(0, 123, 255, 0.05);
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            #report-modal .modal-header-new-qc,
            #workOrderReportModal .modal-header-new-qc,
            #summaryModal .modal-header-new-qc,
            #summaryItemCategoryModal .modal-header-new-qc {
                width: 108% !important;
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
                                        <a class="btn btn-dual filterSampleTestModal d2 {{ (@$_GET['filter_test_name'] && $_GET['filter_test_name'] !== '') || !empty($_GET['filter_item_cat']) || !empty($_GET['filter_asset_no']) || !empty($_GET['filter_workorder_no']) || !empty($_GET['filter_bosubi']) || !empty($_GET['filter_sample_date']) || !empty($_GET['filter_production_date']) || !empty($_GET['filter_user']) ? 'filter-active' : '' }} "
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Filters"
                                            href="javascript:;" id="GeneralFilters">
                                            <img src="{{ asset('public/img/cf-menu-icons/header-filter.png') }}"
                                                width="20">
                                        </a>
                                    </div>
                                    <div class="push mb-0 col-sm-10 pl-0">
                                        <?php
                                        $filter = (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') . (isset($_GET['note']) ? '&note=' . $_GET['note'] : '') . (isset($_GET['filter_status']) ? '&filter_status=' . $_GET['filter_status'] : '') . (isset($_GET['filter_item_code']) ? '&filter_item_code=' . $_GET['filter_item_code'] : '') . (isset($_GET['description']) ? '&description=' . $_GET['description'] : '') . (isset($_GET['filter_item_category']) ? '&filter_item_category=' . $_GET['filter_item_category'] : '') . (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                                        ?>
                                        <form class="push mb-0" method="get" id="form-search"
                                            action="{{ url('sample-tests/') }}?{{ $filter }}">
                                            <div class="input-group main-search-input-group" id="search-container">
                                                <input type="text" value="{{ @$_GET['search'] }}"
                                                    class="form-control searchNew" name="search"
                                                    placeholder="Search Sample Tests" id="search-input">
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
                                        <!-- <a class="btn btn-dual filterSampleTestModal d2 {{ !empty($_GET['filter_item_category']) || !empty($_GET['filter_item_code']) || !empty($_GET['description']) ? 'filter-active' : '' }} " data-custom-class="header-tooltip"
                                                                                                                                                                                                                                                                                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                                                                                                                                                                                                                                                                data-original-title="Filters" href="javascript:;" id="GeneralFilters">
                                                                                                                                                                                                                                                                                                <img src="{{ asset('public/img/cf-menu-icons/header-filter.png') }}" width="20">
                                                                                                                                                                                                                                                                                            </a> -->
                                        <a class="btn btn-dual insert_sample_test d2 " data-custom-class="header-tooltip"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Add Sample Test" href="javascript:;" data-toggle="modal"
                                            data-target="#insert-sample-test-modal">
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
                                                data-url="{{ url('export-sample-tests') }}?{{ $_SERVER['QUERY_STRING'] }}">
                                                <img src="{{ asset('public/img/cf-menu-icons/header-export.png') }}"
                                                    width="20">
                                            </a>
                                            <a class="btn btn-dual  d2 "
                                                style="padding-bottom: 7px !important; padding-top: 4px !important;"
                                                href="javascript:;" data-custom-class="header-tooltip" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Import" id="ImportSampleTests">
                                                <img src="{{ asset('public/img/cf-menu-icons/header-import.png') }}"
                                                    width="20">
                                            </a>



                                        @endif
                                        {{-- change today --}}
                                        <a class="btn btn-dual d2"
                                            style="padding-bottom: 7px !important; padding-top: 4px !important;"
                                            href="javascript:;" data-custom-class="header-tooltip" data-toggle="tooltip"
                                            data-trigger="hover" data-placement="top" title=""
                                            data-original-title="Reports" id="reportBtn">
                                            <i class="fa-light fa-file-chart-column text-white"
                                                style="font-size: 18px;"></i>
                                        </a>
                                        {{-- ! change today --}}
                                    </div>
                                    <div class="col-auto mr-auto text-center" style="">
                                        {{ $qry->appends($_GET)->onEachSide(0)->links() }}
                                    </div>
                                    <form id="limit_form" class="ml-2 mb-0"
                                        action="{{ url('sample-tests') }}?{{ $_SERVER['QUERY_STRING'] }}">
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
                            $filter_test_name = isset($_GET['filter_test_name']) && trim($_GET['filter_test_name']) !== '';
                            $filter_item_cat = isset($_GET['filter_item_cat']) && trim($_GET['filter_item_cat']) !== '';
                            $filter_asset_no = isset($_GET['filter_asset_no']) && trim($_GET['filter_asset_no']) !== '';
                            $filter_workorder_no = isset($_GET['filter_workorder_no']) && trim($_GET['filter_workorder_no']) !== '';
                            $filter_bosubi = isset($_GET['filter_bosubi']) && trim($_GET['filter_bosubi']) !== '';
                            $filter_sample_date = isset($_GET['filter_sample_date']) && trim($_GET['filter_sample_date']) !== '';
                            $filter_production_date = isset($_GET['filter_production_date']) && trim($_GET['filter_production_date']) !== '';
                            $filter_user = isset($_GET['filter_user']) && trim($_GET['filter_user']) !== '';

                            $class = 'bubble-header-grey';
                            $get_text = '';
                            $get_text_before = '';
                            $get_text_display = 'd-none';

                            if ($search && ($filter_test_name || $filter_item_cat || $filter_asset_no || $filter_workorder_no || $filter_bosubi || $filter_sample_date || $filter_production_date || $filter_user)) {
                                $class = 'bubble-header-green';
                                $get_text = 'Filtered and Search Results:';
                                $get_text_before = '';
                                $get_text_display = 'd-block';
                            } elseif ($search) {
                                $class = 'bubble-header-yellow';
                                $get_text = '';
                                $get_text_before = 'Search Results';
                                $get_text_display = 'd-block';
                            } elseif ($filter_test_name || $filter_item_cat || $filter_asset_no || $filter_workorder_no || $filter_bosubi || $filter_sample_date || $filter_production_date || $filter_user) {
                                $class = 'bubble-header-blue';
                                $get_text = 'Filters Applied:';
                                $get_text_before = '';
                                $get_text_display = 'd-block';
                            }
                            ?>
                            <div class="col-1 {{ $class }}"></div>
                            <p class="col-11 bubble-header-text d-flex justify-content-between align-items-center">Quality
                                Control Tests
                                <span class="{{ $get_text_display }} text-right" style="line-height: 1.3;">
                                    <a class="clear-link" href="{{ url('/sample-tests') }}"
                                        data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                        data-placement="top" title="" data-original-title="Clear">
                                        <img class="nav-main-link-icon "
                                            src="{{ asset('public/img/cf-menu-icons/menu-icon-deactivate-grey.png') }}"
                                            data-default="{{ asset('public/img/cf-menu-icons/menu-icon-deactivate-grey.png') }}"
                                            data-hover="{{ asset('public/img/cf-menu-icons/3dot-deactivate.png') }}"
                                            width="16">
                                    </a>
                                    <a type="button" class="filterSampleTestModal bubble-a-tag"
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
                                    <img src="{{ asset('public/img/cf-menu-icons/main-menu-sampletests-white.png') }}"
                                        class="header-image" style="width: 36px; height: 36px;">
                                    <div class="" style="margin-left: 0.91rem;">
                                        <h4 class="mb-1 header-new-text header-item-code" style="line-height:22px">Quality
                                            Control Test</h4>
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
                                <div class="col-1 bubble-fil ter-ht {{ $class }}" style="height: 400px;"></div>
                                <form id="filterForm" method="GET" action="{{ url('/sample-tests') }}"
                                    class="mb-0 col-11 py-3 px-3 small-box small-box-400">
                                    <div class="d-flex justify-content-between align-items-center pl-2 mb-3">
                                        <span class="font-filter">Filters</span>
                                        <button type="button" class="close close-cross close-filter" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="block block-transparent mb-0">
                                        <div class="pl-3 pt-0 pb-0">
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Test Name</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    {{-- <input type="text" class="modal-input form-control"
                                                        name="filter_test_name" id="filter_test_name"> --}}
                                                    <select class="modal-input shadow-non e form-control select2"
                                                        name="filter_test_name" id="filter_test_name">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Item Category</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <input type="text" class="modal-input form-control"
                                                        style="box-shadow: none" name="filter_item_cat" id="filter_item_cat">
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Asset #</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <select class="modal-input shadow-non e form-control select2"
                                                        name="filter_asset_no" id="filter_asset_no">

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Workorder #</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <input type="text" class="modal-input form-control"
                                                        style="box-shadow: none" name="filter_workorder_no"
                                                        id="filter_workorder_no">
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Bosubi</label>
                                                {{-- <div class="col-sm-7 px-sm-0 form-group">
                                                    <input type="text" class="modal-input form-control"
                                                        name="filter_bosubi" id="filter_bosubi">
                                                </div> --}}
                                                <div class="col-sm-7 d-flex  px-sm-0 form-group">
                                                    <div class="col pl-0 pr-1">
                                                        <input type="radio" id="editN/A" name="filter_bosubi"
                                                            value="N/A" class="custom-radio">
                                                        <label for="editN/A" class="custom-radio-label">N/A</label>
                                                    </div>
                                                    <div class="col px-1">
                                                        <input type="radio" id="editBEFORE" name="filter_bosubi"
                                                            value="BEFORE" class="custom-radio">
                                                        <label for="editBEFORE" class="custom-radio-label">BEFORE</label>
                                                    </div>
                                                    <div class="col px-1">
                                                        <input type="radio" id="editAFTER" name="filter_bosubi"
                                                            value="AFTER" class="custom-radio">
                                                        <label for="editAFTER" class="custom-radio-label">AFTER</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Test Date</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <input type="text" name="filter_sample_date" id="filter_sample_date"
                                                        class="modal-input shadow-none form-control js-datepicker"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">Production Date</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <input type="text" name="filter_production_date"
                                                        id="filter_production_date"
                                                        class="modal-input shadow-none form-control js-datepicker"
                                                        autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="align-items-baseline row">
                                                <label class="col-sm-4 modal-label">User Name</label>
                                                <div class="col-sm-7 px-sm-0 form-group">
                                                    <select class="modal-input shadow-non e form-control select2"
                                                        name="filter_user" id="filter_user">

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="search" value="{{ @$_GET['search'] }}">
                                        <div class="block-content text-right pt-1 pr-0" style="padding-left: 9mm;">
                                            <a href="{{ url('/sample-tests') }}" class="btn btn-action mr-3">Clear</a>
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
                                                    <span class="titillium-web-black bubble-item-title"
                                                        style="background-color: transparent; border: none; color: #0070C0 !important; padding: 0px 10px 0px 0px !important">
                                                        {{ $q->test_name }}
                                                    </span>
                                                    <br class="responsive-break">
                                                    <span class="titillium-web-light bubble-item-title fw-300 ml-0"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="Item Category"
                                                        style="font-size: 9pt; border-color: #989698;">
                                                        {{ $q->item_category }}
                                                    </span>
                                                    <span class="titillium-web-light bubble-item-title fw-300 ml-2"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="UOM"
                                                        style="font-size: 9pt; border-color: #989698;">
                                                        {{ $q->uom }}
                                                    </span>
                                                </div>
                                                <div style="position: absolute;right: 10px;top: 10px;">
                                                    <span class="font-signika bubble-status-active" data-toggle="tooltip"
                                                        data-trigger="hover" data-placement="top" title=""
                                                        data-original-title="Workorder #">WO# {{ $q->workorder_no }}</span>
                                                </div>
                                            </div>
                                            @php
                                                $icons = [
                                                    'pass' => 'greencheck.png',
                                                    'fail' => 'redxcircle.png',
                                                    'warning' => 'yellowexclamationmark.png',
                                                ];
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-end"
                                                style="margin-top: 5px;">
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2 rounded-circle-div" style="width: 35px; height: 35px;"
                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                        title="" data-original-title="Total Samples">
                                                        <div
                                                            class="rounded-circle bg-primary text-white text-center align-content-center w-100 h-100">
                                                            {{ $q->sample_number }}
                                                        </div>
                                                    </div>
                                                    <div style="background: none;border: 1px solid #C0C0C0;padding: 3px 6px;border-radius: 10px;"
                                                        class="final-sample-entry d-flex justify-content-between align-items-center mx-1">
                                                        <div class="">
                                                            @if (isset($icons[$q->min_result]))
                                                                <img src="{{ asset('public/img/cf-menu-icons/' . $icons[$q->min_result]) }}"
                                                                    width="22">
                                                            @endif

                                                        </div>
                                                        <div class="d-flex justify-content-center align-items-center ml-1">
                                                            <div class="text-center">
                                                                <span
                                                                    class="final-sample-value">{{ $q->min }}</span><br>
                                                                <div class="final-sample-number text-center">MIN</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="background: none;border: 1px solid #C0C0C0;padding: 3px 6px;border-radius: 10px;"
                                                        class="final-sample-entry d-flex justify-content-between align-items-center mx-1">
                                                        <div class="">
                                                            @if (isset($icons[$q->avg_result]))
                                                                <img src="{{ asset('public/img/cf-menu-icons/' . $icons[$q->avg_result]) }}"
                                                                    width="22">
                                                            @endif

                                                        </div>
                                                        <div class="d-flex justify-content-center align-items-center ml-1">
                                                            <div class="text-center">
                                                                <span
                                                                    class="final-sample-value">{{ $q->avg }}</span><br>
                                                                <div class="final-sample-number text-center">AVG</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="background: none;border: 1px solid #C0C0C0;padding: 3px 6px;border-radius: 10px;"
                                                        class="final-sample-entry d-flex justify-content-between align-items-center mx-1">
                                                        <div class="">
                                                            @if (isset($icons[$q->max_result]))
                                                                <img src="{{ asset('public/img/cf-menu-icons/' . $icons[$q->max_result]) }}"
                                                                    width="22">
                                                            @endif

                                                        </div>
                                                        <div class="d-flex justify-content-center align-items-center ml-1">
                                                            <div class="text-center">
                                                                <span
                                                                    class="final-sample-value">{{ $q->max }}</span><br>
                                                                <div class="final-sample-number text-center">MAX</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0"
                                                            data-id="{{ $q->id }}" data-status="{{ $q->status }}"
                                                            href="#" role="button" data-toggle="dropdown"
                                                            aria-expanded="false">
                                                            {{-- <img src="{{ asset('public/img/cf-menu-icons/3dots.png') }}"
                                                                width="9"> --}}
                                                            {{-- <i class="fa-thin fa-ellipsis-vertical"></i> --}}
                                                            <i class="fa-light fa-ellipsis-vertical"
                                                                style="color: #262626; font-size: 22px;"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
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
            <style>
                .modal-header-insert {
                    font-size: 22pt;
                }

                .box-connector {
                    position: relative;
                    height: auto;
                }

                /* Common line class */
                .connector-line {
                    position: absolute;
                    width: 38px;
                    height: 26px;
                }

                /* Left L-curve */
                .left-connector {
                    top: -9px;
                    left: 0;
                    border-left: 1px solid #d7d5e2;
                    border-bottom: 1px solid #d7d5e2;
                    border-bottom-left-radius: 10px;
                }

                /* Right L-curve (flipped) */
                .right-connector {
                    top: -9px;
                    right: 0;
                    border-right: 1px solid #d7d5e2;
                    border-bottom: 1px solid #d7d5e2;
                    border-bottom-right-radius: 10px;
                }

                /* Center vertical line */
                .center-connector {
                    top: -35px;
                    left: 50%;
                    transform: rotate(90deg);
                    height: 103px;
                    width: 1px;
                    background: #d7d5e2;
                }
            </style>

            <form class="mb-0 pb-0" action="{{ url('insert-sample-test') }}" id="form-insert-sample-test" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="insert-sample-test-modal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-header-new-qc align-items-center mb-0 py-1 px-4">
                                <div>
                                    <h1 class="modal-header-insert  " style="margin-bottom: -5px !important;">
                                        NEW QC SAMPLE TEST

                                    </h1>
                                    <span class="modal-subheader">QUALITY CONTROL TEST</span>
                                </div>
                                <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="header-div-qc mx-auto px-0 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bubble-header-qc"></div>
                                            <div class="ml-3 mr-2">
                                                @if (Auth::user()->user_image == '')
                                                    <img class="rounded-circle"
                                                        src="{{ asset('public') }}/img/cf-menu-icons/user-icon.png"
                                                        alt="" width="45" height="45">
                                                @else
                                                    <img class="rounded-circle"
                                                        src="{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}"
                                                        alt="" width="45" height="45">

                                                @endif
                                            </div>
                                            <div class="text-capitalize qc-user-name">
                                                {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                                            </div>
                                            <div class="ml-auto qc-header-date">
                                                <span class="selected-date">{{ date('d-M-Y') }}</span>
                                                <input type="hidden" name="selected-date" value="">
                                                <a class="qc-calendar" href="#">
                                                    <img class="mx-3"
                                                        src="{{ asset('public') }}/img/cf-menu-icons/icon-calendar-3.png"
                                                        width="20" height="20">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 1 -->
                            <div class="step-1">
                                <div class="block block-transparent mb-0">
                                    <div class="block-content align-items-center pt-0 row mt-2">
                                        <label class="col-sm-3 d-flex align-items-center text-red modal-label"
                                            for="testNameSelect" style="color: #C41E3A!important;">Test Name</label>
                                        <div class="col-sm-5">
                                            <select class="modal-input shadow-non e form-control select2"
                                                name="testNameSelect" id="testNameSelect">
                                            </select>
                                        </div>
                                        <div class="col-sm-2 test-type-input pl-0" style="display: none;">
                                            <input type="text"
                                                class="form-control text-center px-1 green-box selectedTestType"
                                                name="selectedTestType" id="selectedTestType" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Test Type" readonly>
                                        </div>

                                        <div class="col-sm-1 perf-str-input pl-0" style="display: none;">
                                            <input type="text"
                                                class="form-control text-center px-1 green-box selectedCriteria"
                                                name="selectedCriteria" id="selectedCriteria" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Criteria" readonly>
                                        </div>
                                        <div class="col-sm-1 perf-str-input pl-0" style="display: none;">
                                            <!-- input type="text"
                                                                                                                        class="form-control text-center px-1 green-box selectedStandard"
                                                                                                                        name="selectedStandard" id="selectedStandard" data-toggle="tooltip"
                                                                                                                        data-trigger="hover" data-placement="top" title=""
                                                                                                                        data-original-title="Standard" readonly> -->
                                        </div>
                                        <input type="hidden" name="selectedUOM" value="">
                                    </div>
                                    <div class="block-content pt-0 row form-group mt-2">
                                        <label class="col-sm-3 d-flex align-items-center text-red modal-label"
                                            for="WorkOrderSelect" style="color: #C41E3A!important;">W/O #</label>
                                        <div class="col-sm-5">
                                            <select class="modal-input shadow-non e form-control WorkOrderSelect"
                                                name="WorkOrderSelect" id="WorkOrderSelect">

                                            </select>
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="block-content d-flex align-items-center pt-0 row mt-2">
                                        <label class="col-sm-3 modal-label fetched-items">Item Category</label>
                                        <div class="col-sm-3">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-item-category"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Item Category" data=""></div>
                                            <input type="hidden" name="fetched-item-category" value="">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="fw-600 label-new field-color shadow-non e fetched-items fetched-itemcode-desc"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Itemcode Description" data="">
                                            </div>
                                            <input type="hidden" name="fetched-itemcode-desc" value="">
                                        </div>
                                    </div>
                                    <div class="block-content d-flex align-items-center pt-0 row mt-2">
                                        <label class="col-sm-3 modal-label fetched-items">Itemcode</label>
                                        <div class="col-sm-3">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-itemcode"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Itemcode" data=""></div>
                                            <input type="hidden" name="fetched-itemcode" value="">
                                            <input type="hidden" name="fetched-itemcode-id" value="">
                                        </div>
                                        <label class="col-sm-1 modal-label fetched-items">Color</label>
                                        <div class="col-sm-2">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-itemcode-color"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Color" data=""></div>
                                            <input type="hidden" name="fetched-itemcode-color" value="">
                                        </div>
                                        <label class="col-sm-1 modal-label fetched-items">Length</label>
                                        <div class="col-sm-2">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-workorder-length"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Length" data=""></div>
                                            <input type="hidden" name="fetched-workorder-length" value="">
                                        </div>
                                        <input type="hidden" name="test_desc" value="">
                                    </div>
                                </div>

                                <div class="block-content block-content-full text-right my-3" style="padding-right: 9mm;">
                                    <button type="button" id="next-step-btn" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Continue
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                            <!-- End Step 1 -->

                            <!-- Step 2 -->
                            <div class="step-2">

                                <div class="container">
                                    <div class="row align-items-center">
                                        <div class="col-11 mx-auto px-0 mb-3 position-relative box-connector">
                                            <!-- LEFT LINE -->
                                            <div class="connector-line left-connector"></div>

                                            <!-- RIGHT LINE -->
                                            <div class="connector-line right-connector"></div>

                                            <!-- MIDDLE LINE -->
                                            <div class="connector-line center-connector"></div>

                                            <div class="row align-items-center">
                                                <div class="col-sm-5 mx-auto">
                                                    <div class="qc-step-2-header test-name text-center">
                                                        <span class="test-name-step-2">Test Name</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5 mx-auto">
                                                    <div class="qc-step-2-header workorder text-center">
                                                        <span class="workorder-step-2">Workorder #</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="block block-transparent mb-0">
                                    <div class="block-content pt-0 row mt-2">
                                        <label class="col-sm-2 d-flex align-items-center text-red modal-label"
                                            for="AssetSelect" style="color: #C41E3A!important;">Asset #</label>
                                        <div class="col-sm-4">
                                            <select class="modal-input shadow-non e form-control select2" name="AssetSelect"
                                                id="AssetSelect">

                                            </select>
                                        </div>
                                        <label class="col-sm-1 ml-auto d-flex align-items-center text-red modal-label"
                                            for="bosubi" style="color: #C41E3A!important;">Bosubi</label>
                                        <div class="col-sm-4 d-flex">
                                            <div class="col pl-0 pr-1">
                                                <input type="radio" id="N/A" name="bosubi" value="N/A"
                                                    class="custom-radio" checked>
                                                <label for="N/A" class="custom-radio-label">N/A</label>
                                            </div>
                                            <div class="col px-1">
                                                <input type="radio" id="BEFORE" name="bosubi" value="BEFORE"
                                                    class="custom-radio">
                                                <label for="BEFORE" class="custom-radio-label">BEFORE</label>
                                            </div>
                                            <div class="col px-1">
                                                <input type="radio" id="AFTER" name="bosubi" value="AFTER"
                                                    class="custom-radio">
                                                <label for="AFTER" class="custom-radio-label">AFTER</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="block-content pt-0 row form-group mt-2">
                                        <label class="col-sm-2 d-flex align-items-center text-red modal-label"
                                            for="WorkOrderSelect" style="color: #C41E3A!important;">Production Date</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="production_date" id="production_date"
                                                class="modal-input shadow-none form-control js-datepicker" autocomplete="off">
                                        </div>
                                        <label class="col-sm-1 ml-auto d-flex align-items-center text-red modal-label"
                                            for="lot" style="color: #C41E3A!important;">Lot</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="lot" id="lot"
                                                class="modal-input shadow-none form-control">
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="block-content position-absolute pt-0 text-right"
                                        style="right: 55px;z-index: 9;width: fit-content;">
                                        <button type="button" class="btn btn-yes" data-toggle="modal"
                                            data-target="#import-modal">Import</button>
                                    </div>
                                    <div class="block-content pt-0 row mt-0 mb-2 col-12 perf-weight-samples">
                                        <label class="col-sm-2 text-center modal-label fw-600 pl-5">BOSUBI</label>
                                        <label class="col-sm-6 text-center modal-label fw-600 pl-5">ENTER SAMPLES</label>
                                        <label class="col-sm-2 text-center modal-label fw-600 pl-5">UOM</label>
                                    </div>
                                    <div class="block-content pt-0 row mt-0 mb-2 col-11 perf-weight-samples">
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label fw-600"
                                            style="color: black!important;">
                                            Before
                                            <br>
                                            <br>
                                            After
                                        </label>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label selectedUOM fw-600"
                                            style="color: black!important;">UOM</label>
                                        <div class="col-sm align-content-center">
                                            <button type="button" class="btn btn-yes addSample" id="addSample">Add</button>
                                        </div>
                                    </div>

                                    <div class="block-content pt-0 row mt-2 col-11 non-perf-weight-samples">
                                        <label class="col-sm-2 text-center modal-label fw-600 pl-0">UOM</label>
                                        <label class="col-sm-8 text-center modal-label fw-600">ENTER SAMPLES</label>
                                    </div>
                                    <div class="block-content pt-0 row mt-2 col-11 non-perf-weight-samples">
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label selectedUOM fw-600"
                                            style="color: black!important;">UOM</label>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">

                                        </div>
                                    </div>
                                    <div class="block-content pt-0 row form-group mt-2 col-11 non-perf-weight-samples">
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label selectedUOM fw-600"
                                            style="color: black!important;">UOM</label>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm align-content-center">
                                            <button type="button" class="btn btn-yes addSample" id="addSample">Add</button>
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="col-sm-11 mx-auto">
                                        <div id="SelectedSamples" class="row small-arrow selected-items-container mt-3"
                                            style="max-width: 100%; height: 127px; max-height: 127px; overflow-y: auto;">
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="col-sm-11 mx-auto">
                                        <div id="SelectedSamplesConclusion"
                                            class="row align-items-center selected-items-container">
                                            <div class="col-sm-2 dimention-str" style="display: none;">
                                                <div
                                                    class="sample-entry d-flex justify-content-center align-items-center my-2 py-3">
                                                    <div class="">
                                                        <div class="text-center">
                                                            <span class="sample-value test-avg-value">0</span><br>
                                                            <div class="sample-number">-<span class="test-minus">0</span> /
                                                                +<span class="test-plus">0</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-2 perf-str" style="display: none;">
                                                <div
                                                    class="sample-entry perf-weight-css-center d-flex align-items-center my-2 py-3">
                                                    <div class="d-flex justify-content-center">
                                                        <div class="align-content-center "
                                                            style="    width: 29px;display: inline-block;
text-align: center;">
                                                            <div class="test-standard">YFS/YFGS</div>
                                                        </div>
                                                        <div class="ml-2 perf-weight-css-margin">
                                                            <input type="hidden" name="standard_value" id="standard_value">
                                                            <span class="sample-value standard-value">0</span><br>
                                                            <div class="sample-number safety-value text-center">+0</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 align-content-center perf-str" style="display: none;">
                                                <div class="rounded-circle bg-primary total-samples text-white mx-auto"
                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                    title="" data-original-title="# of Samples">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-sm-3 align-content-center nosample non-perf-str"
                                                style="display: none;">
                                                <div class="rounded-circle bg-primary total-samples text-white mx-auto"
                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                    title="" data-original-title="# of Samples">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div
                                                    class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div class="">
                                                        <img class="final-result-min-img-pass"
                                                            src="{{ asset('public/img/cf-menu-icons/greencheck.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-min-img-fail"
                                                            src="{{ asset('public/img/cf-menu-icons/redxcircle.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-min-img-warning"
                                                            src="{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}"
                                                            width="30" style="display: none;">
                                                        <input type="hidden" class="final-min-img" name="final-min-img"
                                                            value="">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value final-min-value">0</span><br>
                                                            <div class="sample-number">MIN (<span
                                                                    class="sample-uom">uom</span>)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div
                                                    class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div class="">
                                                        <img class="final-result-avg-img-pass"
                                                            src="{{ asset('public/img/cf-menu-icons/greencheck.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-avg-img-fail"
                                                            src="{{ asset('public/img/cf-menu-icons/redxcircle.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-avg-img-warning"
                                                            src="{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}"
                                                            width="30" style="display: none;">
                                                        <input type="hidden" class="final-avg-img" name="final-avg-img"
                                                            value="">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value final-avg-value">0</span><br>
                                                            <div class="sample-number">AVG (<span
                                                                    class="sample-uom">uom</span>)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div
                                                    class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div class="">
                                                        <img class="final-result-max-img-pass"
                                                            src="{{ asset('public/img/cf-menu-icons/greencheck.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-max-img-fail"
                                                            src="{{ asset('public/img/cf-menu-icons/redxcircle.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-max-img-warning"
                                                            src="{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}"
                                                            width="30" style="display: none;">
                                                        <input type="hidden" class="final-max-img" name="final-max-img"
                                                            value="">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value final-max-value">0</span><br>
                                                            <div class="sample-number">MAX (<span
                                                                    class="sample-uom">uom</span>)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 perf-str stdva-div" style="display:none;">
                                                <div class="sample-entry d-flex align-items-center my-2 py-3">
                                                    <div class="d-flex">
                                                        <div>
                                                            <img class="stdva-img"
                                                                src="{{ asset('public/img/cf-menu-icons/standard-deviation.png') }}"
                                                                width="30">
                                                        </div>
                                                        <div class="text-center ml-3">
                                                            <span class="sample-value stdva-value">0</span><br>
                                                            <div class="sample-number">STDV.A</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 non-perf-str" style="display:none;">
                                                <div
                                                    class="sample-entry d-flex justify-content-center align-items-center my-2 py-3">
                                                    <div class="">
                                                        <div class="text-center">
                                                            <span
                                                                class="sample-value final-avg-value final-avg-value1">0</span><br>
                                                            <div class="sample-number">-<span class="avg-minus">0</span> /
                                                                +<span class="avg-plus">0</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="insert-model-standard-div justify-content-center gap-2"
                                            style="display: none;">
                                            <small class=" d-flex align-items-center" for="insert_model_standard">Show results
                                                using standard:</small>
                                            <div class="d-flex ml-2">
                                                <div class="pl-0 pr-1" style="width: 55px">
                                                    <input type="radio" id="YFS" name="insert_model_standard"
                                                        value="YFS" class="custom-radio" checked>
                                                    <label for="YFS" class="custom-radio-label"
                                                        style="height: 25px;">YFS</label>
                                                </div>
                                                <div class="px-1" style="width: 55px">
                                                    <input type="radio" id="YFGS" name="insert_model_standard"
                                                        value="YFGS" class="custom-radio">
                                                    <label for="YFGS" class="custom-radio-label"
                                                        style="height: 25px;">YFGS</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="summary-loading-spinner" id="summaryLoadingSpinner">
                                        <img src="{{ asset('public/img/cf-menu-icons/gear.png') }}" alt="Loading">
                                        <div class="summary-loading-text">Calculating...</div>
                                    </div>
                                </div>

                                <div class="block-content block-content-full d-flex align-items-center justify-content-between my-3"
                                    style="padding-left: 9mm; padding-right: 9mm;">
                                    <button type="button" class="btn btn-yes historical"
                                        id="historical">Historical</button>
                                    <input type="text" name="comments" id="comments" class="modal-input form-control" placeholder="Add comments" style="max-width: 300px">
                                    <button type="button" id="submitSampleTest" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Create
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                            <!-- End Step 2 -->
                        </div>
                    </div>
                </div>
                </div>
            </form>
            <form class="mb-0 pb-0" action="{{ url('update-sample-test') }}" id="form-edit-sample-test" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="sample_test_id" id="edit_sample_test_id" value="">
                <div class="modal fade" id="edit-sample-test-modal" tabindex="-1" role="dialog"
                    data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-header-new-qc align-items-center mb-0 py-1 px-4">
                                <div>
                                    <h1 class="modal-header-insert  " style="margin-bottom: -5px !important;">
                                        EDIT QC SAMPLE TEST

                                    </h1>
                                    <span class="modal-subheader">QUALITY CONTROL TEST</span>
                                </div>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="header-div-qc mx-auto px-0 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="bubble-header-qc"></div>
                                            <div class="ml-3 mr-2">
                                                @if (Auth::user()->user_image == '')
                                                    <img class="rounded-circle"
                                                        src="{{ asset('public') }}/img/cf-menu-icons/user-icon.png"
                                                        alt="" width="45" height="45">
                                                @else
                                                    <img class="rounded-circle"
                                                        src="{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}"
                                                        alt="" width="45" height="45">

                                                @endif
                                            </div>
                                            <div class="text-capitalize qc-user-name">
                                                {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                                            </div>
                                            <div class="ml-auto qc-header-date">
                                                <span class="selected-date">{{ date('d-M-Y') }}</span>
                                                <input type="hidden" name="selected-date" value="">
                                                <a class="qc-calendar" href="#">
                                                    <img class="mx-3"
                                                        src="{{ asset('public') }}/img/cf-menu-icons/icon-calendar-3.png"
                                                        width="20" height="20">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 1 -->
                            <div class="step-1">
                                <div class="block block-transparent mb-0">
                                    <div class="block-content align-items-center pt-0 row mt-2">
                                        <label class="col-sm-3 d-flex align-items-center text-red modal-label"
                                            for="edittestNameSelect" style="color: #C41E3A!important;">Test Name</label>
                                        <div class="col-sm-5">
                                            <select class="modal-input shadow-non e form-control select2"
                                                name="testNameSelect" id="edittestNameSelect">
                                            </select>
                                        </div>
                                        <div class="col-sm-2 test-type-input pl-0" style="display: none;">
                                            <input type="text"
                                                class="form-control text-center px-1 green-box selectedTestType"
                                                name="selectedTestType" id="editselectedTestType" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Test Type" readonly>
                                        </div>
                                        <div class="col-sm-1 perf-str-input pl-0" style="display: none;">
                                            <input type="text"
                                                class="form-control text-center px-1 green-box selectedCriteria"
                                                name="selectedCriteria" id="editselectedCriteria" data-toggle="tooltip"
                                                data-trigger="hover" data-placement="top" title=""
                                                data-original-title="Criteria" readonly>
                                        </div>
                                        <div class="col-sm-1 perf-str-input pl-0" style="display: none;">
                                            <!--   <input type="text"
                                                                                                                        class="form-control text-center px-1 green-box selectedStandard"
                                                                                                                        name="selectedStandard" id="editselectedStandard" data-toggle="tooltip"
                                                                                                                        data-trigger="hover" data-placement="top" title=""
                                                                                                                        data-original-title="Standard" readonly> -->
                                        </div>
                                        <input type="hidden" name="selectedUOM" id="editselectedUOM" value="">
                                    </div>
                                    <div class="block-content pt-0 row form-group mt-2">
                                        <label class="col-sm-3 d-flex align-items-center text-red modal-label"
                                            for="editWorkOrderSelect" style="color: #C41E3A!important;">W/O #</label>
                                        <div class="col-sm-5">
                                            <select class="modal-input shadow-non e form-control select2"
                                                name="WorkOrderSelect" id="editWorkOrderSelect">

                                            </select>
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="block-content d-flex align-items-center pt-0 row mt-2">
                                        <label class="col-sm-3 modal-label fetched-items">Item Category</label>
                                        <div class="col-sm-3">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-item-category"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Item Category" data=""></div>
                                            <input type="hidden" name="fetched-item-category" value="">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="fw-600 label-new field-color shadow-non e fetched-items fetched-itemcode-desc"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Itemcode Description" data="">
                                            </div>
                                            <input type="hidden" name="fetched-itemcode-desc" value="">
                                        </div>
                                    </div>
                                    <div class="block-content d-flex align-items-center pt-0 row mt-2">
                                        <label class="col-sm-3 modal-label fetched-items">Itemcode</label>
                                        <div class="col-sm-3">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-itemcode"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Itemcode" data=""></div>
                                            <input type="hidden" name="fetched-itemcode" value="">
                                            <input type="hidden" name="fetched-itemcode-id" value="">
                                        </div>
                                        <label class="col-sm-1 modal-label fetched-items">Color</label>
                                        <div class="col-sm-2">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-itemcode-color"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Color" data=""></div>
                                            <input type="hidden" name="fetched-itemcode-color" value="">
                                        </div>
                                        <label class="col-sm-1 modal-label fetched-items">Length</label>
                                        <div class="col-sm-2">
                                            <div class="fw-600 label-new field-color shadow-non e text-center fetched-items fetched-workorder-length"
                                                data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                title="" data-original-title="Length" data=""></div>
                                            <input type="hidden" name="fetched-workorder-length" value="">
                                        </div>
                                        <input type="hidden" name="test_desc" value="">
                                    </div>
                                </div>

                                <div class="block-content block-content-full text-right my-3" style="padding-right: 9mm;">
                                    <button type="button" id="edit-next-step-btn" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Continue
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                            <!-- End Step 1 -->

                            <!-- Step 2 -->
                            <div class="step-2">

                                <div class="container">
                                    <div class="row align-items-center">
                                        <div class="col-11 mx-auto px-0 mb-3 position-relative box-connector">
                                            <!-- LEFT LINE -->
                                            <div class="connector-line left-connector"></div>

                                            <!-- RIGHT LINE -->
                                            <div class="connector-line right-connector"></div>

                                            <!-- MIDDLE LINE -->
                                            <div class="connector-line center-connector"></div>

                                            <div class="row align-items-center">
                                                <div class="col-sm-5 mx-auto">
                                                    <div class="qc-step-2-header test-name text-center">
                                                        <span class="test-name-step-2">Test Name</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5 mx-auto">
                                                    <div class="qc-step-2-header workorder text-center">
                                                        <span class="workorder-step-2">Workorder #</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="block block-transparent mb-0">
                                    <div class="block-content pt-0 row mt-2">
                                        <label class="col-sm-2 d-flex align-items-center text-red modal-label"
                                            for="editAssetSelect" style="color: #C41E3A!important;">Asset #</label>
                                        <div class="col-sm-4">
                                            <select class="modal-input shadow-non e form-control select2" name="AssetSelect"
                                                id="editAssetSelect">

                                            </select>
                                        </div>
                                        <label class="col-sm-1 ml-auto d-flex align-items-center text-red modal-label"
                                            for="bosubi" style="color: #C41E3A!important;">Bosubi</label>
                                        <div class="col-sm-4 d-flex">
                                            <div class="col pl-0 pr-1">
                                                <input type="radio" id="editN/A_new" name="bosubi" value="N/A"
                                                    class="custom-radio" checked>
                                                <label for="editN/A_new" class="custom-radio-label">N/A</label>
                                            </div>
                                            <div class="col px-1">
                                                <input type="radio" id="editBEFORE_new" name="bosubi" value="BEFORE"
                                                    class="custom-radio">
                                                <label for="editBEFORE_new" class="custom-radio-label">BEFORE</label>
                                            </div>
                                            <div class="col px-1">
                                                <input type="radio" id="editAFTER_new" name="bosubi" value="AFTER"
                                                    class="custom-radio">
                                                <label for="editAFTER_new" class="custom-radio-label">AFTER</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="block-content pt-0 row form-group mt-2">
                                        <label class="col-sm-2 d-flex align-items-center text-red modal-label"
                                            for="WorkOrderSelect" style="color: #C41E3A!important;">Production Date</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="production_date" id="editproduction_date"
                                                class="modal-input shadow-none form-control js-datepicker"
                                                autocomplete="off">
                                        </div>
                                        <label class="col-sm-1 ml-auto d-flex align-items-center text-red modal-label"
                                            for="lot" style="color: #C41E3A!important;">Lot</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="lot" id="editlot"
                                                class="modal-input shadow-none form-control">
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="block-content position-absolute pt-0 text-right"
                                        style="right: 55px;z-index: 9;width: fit-content;">
                                        <button type="button" class="btn btn-yes" data-toggle="modal"
                                            data-target="#import-modal-edit">Import</button>
                                    </div>
                                    <div class="block-content pt-0 row mt-0 mb-2 col-12 perf-weight-samples">
                                        <label class="col-sm-2 text-center modal-label fw-600 pl-5">BOSUBI</label>
                                        <label class="col-sm-6 text-center modal-label fw-600 pl-5">ENTER SAMPLES</label>
                                        <label class="col-sm-2 text-center modal-label fw-600 pl-5">UOM</label>
                                    </div>
                                    <div class="block-content pt-0 row mt-0 mb-2 col-11 perf-weight-samples">
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label fw-600"
                                            style="color: black!important;">
                                            Before
                                            <br>
                                            <br>
                                            After
                                        </label>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <div class="col-sm col-perf-weight">
                                            <input type="number" step="any" name="sample_before[]"
                                                class="modal-input shadow-non e form-control">
                                            <input type="number" step="any" name="sample_after[]"
                                                class="modal-input shadow-non e form-control mt-2">
                                        </div>
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label selectedUOM fw-600"
                                            style="color: black!important;">UOM</label>
                                        <div class="col-sm align-content-center">
                                            <button type="button" class="btn btn-yes editaddSample"
                                                id="editaddSample">Add</button>
                                        </div>
                                    </div>



                                    <div class="block-content pt-0 row mt-2 col-11 non-perf-weight-samples">
                                        <label class="col-sm-2 text-center modal-label fw-600 pl-0">UOM</label>
                                        <label class="col-sm-8 text-center modal-label fw-600">ENTER SAMPLES</label>
                                    </div>
                                    <div class="block-content pt-0 row mt-2 col-11 non-perf-weight-samples">
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label selectedUOM fw-600"
                                            style="color: black!important;">UOM</label>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                        </div>
                                    </div>
                                    <div class="block-content pt-0 row form-group mt-2 col-11 non-perf-weight-samples">
                                        <label
                                            class="col-sm d-flex align-items-center justify-content-center mb-0 modal-label selectedUOM fw-600"
                                            style="color: black!important;">UOM</label>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm">
                                            <input type="number" step="any" name="sample[]"
                                                class="modal-input shadow-non e form-control no-negative">
                                        </div>
                                        <div class="col-sm align-content-center">
                                            <button type="button" class="btn btn-yes editaddSample"
                                                id="editaddSample">Add</button>
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="col-sm-11 mx-auto">
                                        <div id="editSelectedSamples" class="row small-arrow selected-items-container mt-3"
                                            style="max-width: 100%; height: 127px; max-height: 127px; overflow-y: auto;">
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="col-sm-11 mx-auto">
                                        <div id="editSelectedSamplesConclusion"
                                            class="row align-items-center selected-items-container">
                                            <!--    <div class="col-sm-2 dimention-str" style="display: none;">
                                                                                                                   <div class="sample-entry d-flex justify-content-center align-items-center my-2 py-3">
                                                                                                                            <div class="">
                                                                                                                                <div class="text-center">
                                                                                                                                    <span class="sample-value test-avg-value">0</span><br>
                                                                                                                                    <div class="sample-number">-<span class="test-minus">0</span> /
                                                                                                                                        +<span class="test-plus">0</span></div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div> -->
                                            <div class="col-sm-2 perf-str" style="display: none;">
                                                <div
                                                    class="sample-entry perf-weight-css-center d-flex align-items-center my-2 py-3">
                                                    <div class="d-flex justify-content-center">
                                                        <div class="align-content-center "
                                                            style="    width: 29px;display: inline-block;
text-align: center;">
                                                            <div class="test-standard">YFS/YFGS</div>
                                                        </div>
                                                        <div class="ml-2 perf-weight-css-margin">
                                                            <input type="hidden" name="standard_value"
                                                                id="standard_value">
                                                            <span class="sample-value standard-value">0</span><br>
                                                            <div class="sample-number safety-value text-center">+0</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 align-content-center perf-str" style="display: none;">
                                                <div class="rounded-circle bg-primary total-samples text-white mx-auto"
                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                    title="" data-original-title="# of Samples">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-sm-3 align-content-center nosample non-perf-str"
                                                style="display: none;">
                                                <div class="rounded-circle bg-primary total-samples text-white mx-auto"
                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                                    title="" data-original-title="# of Samples">
                                                    0
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div
                                                    class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div class="">
                                                        <img class="final-result-min-img-pass"
                                                            src="{{ asset('public/img/cf-menu-icons/greencheck.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-min-img-fail"
                                                            src="{{ asset('public/img/cf-menu-icons/redxcircle.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-min-img-warning"
                                                            src="{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}"
                                                            width="30" style="display: none;">
                                                        <input type="hidden" class="final-min-img" name="final-min-img"
                                                            value="">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value final-min-value">0</span><br>
                                                            <div class="sample-number">MIN (<span
                                                                    class="sample-uom">uom</span>)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div
                                                    class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div class="">
                                                        <img class="final-result-avg-img-pass"
                                                            src="{{ asset('public/img/cf-menu-icons/greencheck.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-avg-img-fail"
                                                            src="{{ asset('public/img/cf-menu-icons/redxcircle.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-avg-img-warning"
                                                            src="{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}"
                                                            width="30" style="display: none;">
                                                        <input type="hidden" class="final-avg-img" name="final-avg-img"
                                                            value="">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value final-avg-value">0</span><br>
                                                            <div class="sample-number">AVG (<span
                                                                    class="sample-uom">uom</span>)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div
                                                    class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div class="">
                                                        <img class="final-result-max-img-pass"
                                                            src="{{ asset('public/img/cf-menu-icons/greencheck.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-max-img-fail"
                                                            src="{{ asset('public/img/cf-menu-icons/redxcircle.png') }}"
                                                            width="30" style="display: none;">
                                                        <img class="final-result-max-img-warning"
                                                            src="{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}"
                                                            width="30" style="display: none;">
                                                        <input type="hidden" class="final-max-img" name="final-max-img"
                                                            value="">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value final-max-value">0</span><br>
                                                            <div class="sample-number">MAX (<span
                                                                    class="sample-uom">uom</span>)</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 perf-str stdva-div" style="display:none;">
                                                <div class="sample-entry d-flex align-items-center my-2 py-3">
                                                    <div class="d-flex">
                                                        <div>
                                                            <img class="stdva-img"
                                                                src="{{ asset('public/img/cf-menu-icons/standard-deviation.png') }}"
                                                                width="30">
                                                        </div>
                                                        <div class="text-center ml-3">
                                                            <span class="sample-value stdva-value">0</span><br>
                                                            <div class="sample-number">STDV.A</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 non-perf-str" style="display:none;">
                                                <div
                                                    class="sample-entry d-flex justify-content-center align-items-center my-2 py-3">
                                                    <div class="">
                                                        <div class="text-center">
                                                            <span
                                                                class="sample-value final-avg-value final-avg-value1 ">0</span><br>
                                                            <div class="sample-number">-<span class="avg-minus">0</span> /
                                                                +<span class="avg-plus">0</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="edit-model-standard-div justify-content-center gap-2"
                                            style="display: none;">
                                            <small class=" d-flex align-items-center" for="edit_model_standard">Show results
                                                using standard:</small>
                                            <div class="d-flex ml-2">
                                                <div class="pl-0 pr-1" style="width: 55px">
                                                    <input type="radio" id="edit_YFS" name="edit_model_standard"
                                                        value="YFS" class="custom-radio" checked>
                                                    <label for="edit_YFS" class="custom-radio-label"
                                                        style="height: 25px;">YFS</label>
                                                </div>
                                                <div class="px-1" style="width: 55px">
                                                    <input type="radio" id="edit_YFGS" name="edit_model_standard"
                                                        value="YFGS" class="custom-radio">
                                                    <label for="edit_YFGS" class="custom-radio-label"
                                                        style="height: 25px;">YFGS</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="modal-hr">
                                    <div class="summary-loading-spinner" id="editSummaryLoadingSpinner">
                                        <img src="{{ asset('public/img/cf-menu-icons/gear.png') }}" alt="Loading">
                                        <div class="summary-loading-text">Calculating...</div>
                                    </div>

                                </div>

                                {{-- <div class="block-content block-content-full text-right my-3" style="padding-right: 9mm;">
                                    <button type="button" id="editsubmitSampleTest" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Update
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div> --}}
                                {{--  --}}
                                <div class="block-content block-content-full d-flex align-items-center justify-content-between my-3"
                                    style="padding-left: 9mm; padding-right: 9mm;">
                                    <div></div>
                                    <input type="text" name="comments" id="edit_comments" class="modal-input form-control" placeholder="Add comments" style="max-width: 300px">
                                    <button type="button" id="editsubmitSampleTest" class="btn btn-action">
                                        <span class="btn-action-gear d-none mr-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Update
                                        <span class="btn-action-gear d-none ml-2"><img
                                                src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                            <!-- End Step 2 -->
                        </div>
                    </div>
                </div>
                </div>
            </form>
            <div class="modal fade" id="date-modal" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header   ">
                                <span class="b e section-header">Test Date</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="block-content pt-0 row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Select Date</label>
                                <div class="col-sm-7">
                                    <input type="text" name="date"
                                        class="modal-input form-control sample-date js-datepicker" autocomplete="off"
                                        required>
                                </div>
                            </div>
                            <div class="block-content row block-content-full">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-yes" id="save-date">Save</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="import-modal" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header   ">
                                <span class="b e section-header">Import Instron Samples</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="block-content pt-0 row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Select CSV File</label>
                                <div class="col-sm-7">
                                    <input type="file" name="import_file" class="modal-input form-control"
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="block-content row block-content-full">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-yes" id="btn-import-sample">
                                        <span class="btn-action-gear d-none mr-2">
                                            <img src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Import
                                        <span class="btn-action-gear d-none ml-2">
                                            <img src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="import-modal-edit" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header   ">
                                <span class="b e section-header">Import Instron Samples</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="block-content pt-0 row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Select CSV File</label>
                                <div class="col-sm-7">
                                    <input type="file" name="import_file_edit" class="modal-input form-control"
                                        autocomplete="off" required>
                                </div>
                            </div>
                            <div class="block-content row block-content-full">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-yes" id="btn-import-sample-edit">
                                        <span class="btn-action-gear d-none mr-2">
                                            <img src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                        Import
                                        <span class="btn-action-gear d-none ml-2">
                                            <img src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="test-type-modal" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header   ">
                                <span class="section-header">Test Name</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="block-content pt-0 form-group row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Test Type</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedTestType"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Test Type" data=""
                                        style="font-weight: 500!important;"></div>
                                </div>
                            </div>
                            <div class="block-content pt-0 form-group row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Unit of Measure</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedUOM"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Test UOM" data="" style="font-weight: 500!important;">
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0 form-group row mt-2 perf-str-test-type" style="display: none;">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Criteria</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedCriteria"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Criteria" data="" style="font-weight: 500!important;">
                                    </div>
                                </div>
                            </div>
                            <!--   <div class="block-content pt-0 form-group row mt-2 perf-str-test-type" style="display: none;">
                                                                                                        <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Standard</label>
                                                                                                        <div class="col-sm-7">
                                                                                                            <div class="fw-600 label-new field-color shadow-non e text-center selectedStandard"
                                                                                                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                                                                                                data-original-title="Standard" data="" style="font-weight: 500!important;">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div> -->
                            <div class="block-content pt-0 form-group row mt-2">
                                <div class="col-sm-12 mb-3">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedDesc"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Description" data=""></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="workorder-modal" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header   ">
                                <span class="section-header">Workorder #</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="block-content pt-0 form-group row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Item Category</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedItemCategory"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Item Category" data=""
                                        style="font-weight: 500!important;"></div>
                                </div>
                            </div>
                            <div class="block-content pt-0 form-group row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Itemcode</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedItemcode"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Itemcode" data="" style="font-weight: 500!important;">
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0 form-group row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Color</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedColor"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Color" data="" style="font-weight: 500!important;">
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0 form-group row mt-2">
                                <label class="col-sm-4 d-flex align-items-center modal-label mb-0">Length</label>
                                <div class="col-sm-7">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedLength"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Length" data="" style="font-weight: 500!important;">
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0 form-group row mt-2">
                                <div class="col-sm-12 mb-3">
                                    <div class="fw-600 label-new field-color shadow-non e text-center selectedItemcodeDesc"
                                        data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Description" data=""></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <form class="mb-0 pb-0" action="{{ url('end-sample-test') }}" id="form-end-sample-test" method="post">
                @csrf
                <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Revoke</span> Sample
                                        Test</span>
                                    <div class="block-options">
                                    </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <!-- <textarea class="form-control" rows="5" required="" name="reason" id="reason"></textarea> -->
                                            <p class="fw-300">Are you sure you wish to <span
                                                    class="revokeText text-lowercase"></span> this Sample Test?</p>
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

            <form class="mb-0 pb-0" action="{{ url('delete-sample-test') }}" method="post">
                @csrf
                <div class="modal fade" id="DeleteModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header pb-0  ">
                                    <span class="b e section-header"><span class="revokeText">Delete Sample Test
                                            <div class="block-options">
                                            </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 ">
                                    <input type="hidden" id="delete_id" name="id">
                                    <div class="row">
                                        <div class="col-sm-12 text-center">
                                            <p class="fw-300">Are you sure you wish to delete this Sample Test?</p>
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

            <form class="mb-0 pb-0" action="{{ url('delete-attachment-sample-test') }}" method="post">
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
                                    <input type="hidden" id="del_sample_test_id" name="sample_test_id">
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

            <form class="mb-0 pb-0" action="{{ url('delete-comment-sample-test') }}" method="post">
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
                                    <input type="hidden" id="del_comment_sample_test_id" name="sample_test_id">
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

            <form action="{{ url('insert-comment-sample-test') }}" method="post">
                @csrf
                <input type="hidden" id="comment_id" name="id" value="{{ $GETID }}">
                <div class="modal fade" id="CommentModal" tabindex="-1" role="dialog"
                    aria-labelledby="modal-block-large" aria-hidden="true">
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

            <form action="{{ url('update-comment-sample-test') }}" method="post">
                @csrf
                <input type="hidden" id="edit_comment_id" name="id">
                <input type="hidden" id="edit_comment_sample_test_id" name="sample_test_id">
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

            <form id="insert-attachment-sample-test-form" action="{{ url('insert-attachment-sample-test') }}"
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

<!-- Import Result Modal (exact like screenshots) -->
<div class="modal fade" id="ImportResultModal" tabindex="-1" role="dialog" data-backdrop="static"
     aria-labelledby="ImportResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 620px;">
        <div class="modal-content" style="border-radius: 6px;">
            <div class="block block-transparent mb-0">
                <div class="block-header">
                    <span class="section-header" style="font-weight:600;">Import Sample Tests</span>
                    <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="block-content" style="padding: 22px 24px 10px 24px;">
                    @if(session('import_result') === 'success')
                        <p class="mb-0" style="color:#666; text-align:center;">
                            Import of {{ session('import_imported', 0) }} sample tests was successful.
                        </p>
                    @elseif(session('import_result') === 'partial')
                        <p class="mb-1" style="color:#666;">
                            {{ session('import_imported', 0) }} samples tests imported successfully.
                        </p>
                        <p class="mb-0" style="color:#666;">
                            {{ session('import_failed', 0) }} sample tests failed. Download report to see which rows failed.
                        </p>
                    @endif
                </div>

                <div class="block-content" style="padding: 10px 24px 18px 24px;">
                    <div class="d-flex justify-content-end">
                        @if(session('import_result') === 'partial' && session('import_report_url'))
                            <a href="{{ session('import_report_url') }}" class="btn btn-action"
                               style="font-weight:600; text-decoration:none;">
                                Download
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




            <form action="{{ url('/import-sample-tests') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Import Sample Tests</span>
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
                                            <a href="#" data-url="{{ asset('public/Qc-Test-Sample.xlsx') }}"
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
                <div class="modal fade" id="EditSampleValueModal" tabindex="-1" role="dialog" data-b
                    ackdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Edit Sample # <span
                                            class="edit-sample-heading"></span></span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: no ne;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label edit-label">Result</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="sample-value" id="edit-pen-sample-value"
                                            placeholder="Sample Value"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <input type="hidden" class="image-result" name="image-result" value="">
                                    <input type="hidden" class="img-result" name="img-result" value="">
                                    <input type="hidden" class="min-result" name="min-result" value="">
                                    <input type="hidden" class="max-result" name="max-result" value="">
                                    <input type="hidden" class="standard-result" name="standard-result" value="">
                                    <input type="hidden" class="safety-result" name="safety-result" value="">
                                    <input type="hidden" class="before-result" name="before-result" value="">
                                    <input type="hidden" class="after-result" name="after-result" value="">
                                    <input type="hidden" class="percentage-result" name="percentage-result"
                                        value="">
                                    <input type="hidden" class="absorption-result" name="absorption-result"
                                        value="">
                                    <div class="col-sm-2">
                                        <input type="text" name="uom" id="edit-pen-uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2 edit-pen-perf-weight"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">After Bosubi</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="sample-after" id="edit-pen-sample-value-after"
                                            placeholder="Sample Value"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2 edit-pen-perf-weight"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">% Absorption</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="percent-absorption" id="edit-pen-sample-percent"
                                            placeholder="% Absorption" readonly
                                            class="modal-input shadow-non e form-control text-center">
                                    </div>
                                </div>
                                <div class="block-content row block-content-full">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-yes" id="saveEditedSample">Save</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <form action="" id="editEditModuleForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal fade" id="editEditSampleValueModal" tabindex="-1" role="dialog" data-b
                    ackdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0">
                                <div class="block-header   ">
                                    <span class="b e section-header">Edit Sample # <span
                                            class="edit-sample-heading"></span></span>
                                    <button type="button" class="close close-cross" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2"
                                    style="display: no ne;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label edit-label">Result</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="sample-value" id="edit-pen-sample-value"
                                            placeholder="Sample Value"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <input type="hidden" class="image-result" name="image-result" value="">
                                    <input type="hidden" class="img-result" name="img-result" value="">
                                    <input type="hidden" class="min-result" name="min-result" value="">
                                    <input type="hidden" class="max-result" name="max-result" value="">
                                    <input type="hidden" class="standard-result" name="standard-result" value="">
                                    <input type="hidden" class="safety-result" name="safety-result" value="">
                                    <input type="hidden" class="before-result" name="before-result" value="">
                                    <input type="hidden" class="after-result" name="after-result" value="">
                                    <input type="hidden" class="percentage-result" name="percentage-result"
                                        value="">
                                    <input type="hidden" class="absorption-result" name="absorption-result"
                                        value="">
                                    <div class="col-sm-2">
                                        <input type="text" name="uom" id="edit-pen-uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2 edit-pen-perf-weight"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">After Bosubi</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="sample-after" id="edit-pen-sample-value-after"
                                            placeholder="Sample Value"
                                            class="modal-input shadow-non e form-control text-uppercase">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="uom"
                                            class="green-box edit-pen-uom form-control text-center px-1  "
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="UOM" readonly>
                                    </div>
                                </div>
                                <div class="edit-dimension-div edit-pen-divs block-content pt-0 row mt-2 edit-pen-perf-weight"
                                    style="display: none;">
                                    <label class="col-sm-4 d-flex align-items-center modal-label ">% Absorption</label>
                                    <div class="col-sm-3 pr-0  ">
                                        <input type="text" name="percent-absorption" id="edit-pen-sample-percent"
                                            placeholder="% Absorption" readonly
                                            class="modal-input shadow-non e form-control text-center">
                                    </div>
                                </div>
                                <div class="block-content row block-content-full">
                                    <div class="col-sm-12 text-right">
                                        <button type="button" class="btn btn-yes" id="editsaveEditedSample">Save</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal -->

            <div class="modal fade" id="barcodeModal" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header">
                                <span class="b e section-header">Barcode</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center mb-2">
                                <img id="modalBarcodeImage" src="" width="400" height="115"
                                    class=""><br>
                                <span class="field-color fw-600 barcode_workorder" style="font-size:12pt;"></span>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="historical-modal" tabindex="-1" role="dialog" data-b ackdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mod al-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header">
                                <span class="b e section-header">Historical for Machine</span>
                                <button type="button" class="close close-cross" data-dismiss="modal"
                                    aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center mb-2">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="fw-600 label-new field-color shadow-non e text-center history-machine label-new-history"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Machine #" data=""></div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="fw-600 label-new field-color shadow-non e text-center history-test label-new-history"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Test Name" data=""></div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="fw-600 label-new field-color shadow-non e text-center history-itemcat label-new-history"
                                            data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                            title="" data-original-title="Item Category" data=""></div>
                                    </div>
                                </div>
                                <table class="table table-sm text-center table-bordered mt-3">
                                    <thead>
                                        <tr class="">
                                            <th>SAMPLE DATE</th>
                                            <th>MIN</th>
                                            <th>MAX</th>
                                            <th>AVG</th>
                                            <th>STD</th>
                                            <th># SAMPLES</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- change today --}}
            <div class="modal fade" id="report-modal" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <!-- Removed modal-lg to make it narrower -->
                    <div class="modal-content">
                        <div class="modal-header modal-header-new-qc align-items-center mb-0 py-1 px-4">
                            <h1 class="modal-header-insert mb-0">REPORTING<br>
                                <span class="modal-subheader">CONTROLPOINT</span>
                            </h1>
                            <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body p-4">
                            <p class="mb-4">Measurement Data Reports</p>
                            <div class="row text-center mb-5">
                                <div class="col-4 mb-3"> <!-- Changed to col-4 for tighter fit on medium+ screens -->
                                    <div class="card report-card" data-report="by-wo">
                                        <div class="card-body py-3">
                                            <i class="fa-solid fa-barcode-read fa-3x text-dark mb-3"></i>
                                            <p class="mb-0 font-weight-medium">By WO#</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card report-card" data-report="by-itemcat">
                                        <div class="card-body py-3">
                                            <i class="fa-regular fa-sitemap fa-3x text-dark mb-3"></i>
                                            <p class="mb-0 font-weight-medium">By ItemCat</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="card report-card" data-report="by-test">
                                        <div class="card-body py-3">
                                            <i class="fa-solid fa-flask fa-3x text-dark mb-3"></i>
                                            <p class="mb-0 font-weight-medium">By Test</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="mb-4">Testing Summary</p>
                            <div class="row justify-content-start">
                                <div class="col-5 mb-3">
                                    <div class="card report-card" data-report="summaryByWO">
                                        <div class="card-body py-3 text-center">
                                            <i class="fa-solid fa-heart-pulse fa-3x text-dark mb-3"></i>
                                            <p class="mb-0 font-weight-medium">Testing Summary By WO#</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-5 mb-3">
                                    <div class="card report-card" data-report="summaryByItemCategory">
                                        <div class="card-body py-3 text-center">
                                            <i class="fa-solid fa-heart-pulse fa-3x text-dark mb-3"></i>
                                            <p class="mb-0 font-weight-medium">Testing Summary By ItemCat</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-action" id="selectReportBtn">
                                    <span class="btn-action-gear d-none mr-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                    <span class="">Select</span>
                                    <span class="btn-action-gear d-none ml-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <!-- Removed modal-lg to make it narrower -->
                    <div class="modal-content">
                        <div class="modal-header modal-header-new-qc align-items-center mb-0 py-1 px-4">
                            <h1 class="modal-header-insert mb-0">TESTING SUMMARY<br>
                                <span class="modal-subheader">CONTROLPOINT</span>
                            </h1>
                            <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body p-4">
                            <div style="min-height: 150px">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">WO No.</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="wo_no" id="wo_no"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Date Range</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" class="js-flatpickr form-control bg-white"
                                            id="example-flatpickr-range" name="daterange"
                                            placeholder="Select Date Range" data-mode="range" data-alt-input="true"
                                            data-date-format="Y-m-d" data-alt-format="d-M-Y">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-action" id="generateReportBtn">
                                    <span class="btn-action-gear d-none mr-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                    <span class="btn-text">Run</span>
                                    <span class="btn-action-gear d-none ml-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="summaryItemCategoryModal" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <!-- Removed modal-lg to make it narrower -->
                    <div class="modal-content">
                        <div class="modal-header modal-header-new-qc align-items-center mb-0 py-1 px-4">
                            <h1 class="modal-header-insert mb-0">TESTING SUMMARY<br>
                                <span class="modal-subheader">CONTROLPOINT</span>
                            </h1>
                            <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body p-4">
                            <div style="min-height: 150px">
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Item
                                        Category</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <select name="item_category_summery" id="item_category_summery"
                                            class="modal-input shadow-none form-control"></select>
                                    </div>
                                </div>
                                <div class="block-content pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Date Range</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" class="js-flatpickr form-control bg-white"
                                            id="example-flatpickr-range" name="daterange"
                                            placeholder="Select Date Range" data-mode="range" data-alt-input="true"
                                            data-date-format="Y-m-d" data-alt-format="d-M-Y">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-action" id="generateReportBtn">
                                    <span class="btn-action-gear d-none mr-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                    <span class="btn-text">Run</span>
                                    <span class="btn-action-gear d-none ml-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="workOrderReportModal" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <!-- Removed modal-lg to make it narrower -->
                    <div class="modal-content">
                        <div class="modal-header modal-header-new-qc align-items-center mb-0 py-1 px-4">
                            <h1 class="modal-header-insert mb-0">WORKORDER REPORT<br>
                                <span class="modal-subheader">CONTROLPOINT</span>
                            </h1>
                            <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <input type="hidden" name="report_name" id="report_name">
                        <div class="modal-body p-4">
                            <div style="min-height: 250px" class="mb-3">
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">MFG Dept.</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="mfg_dept" id="mfg_dept"
                                            class="modal-input shadow-none form-control text-uppercase"
                                            value="YKK CANADA INC." disabled>
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2 hidden-field wo_no d-none">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">WO No.</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <select name="wo_no" id="wo_no"
                                            class="modal-input shadow-none form-control"></select>
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2 hidden-field item_category d-none">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Item
                                        Category</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <select name="item_category" id="item_category"
                                            class="modal-input shadow-none form-control"></select>
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2 hidden-field test d-none">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Test Name</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <select name="test_name" id="test_name"
                                            class="modal-input shadow-none form-control"></select>
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2 hidden-field item_category date d-none">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Production Start
                                        Date</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="prduction_start_date" id="prduction_start_date"
                                            class="modal-input shadow-none form-control js-datepicker">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2 hidden-field item_category date d-none">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Production End
                                        Date</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="prduction_end_date" id="prduction_end_date"
                                            class="modal-input shadow-none form-control js-datepicker">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Total
                                        Years</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="total_years" id="total_years"
                                            class="modal-input shadow-none form-control text-uppercase"
                                            placeholder="YYYY/MM" maxlength="7">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label mandatory">Sample No.</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="number" name="sample_no" id="sample_no"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Comment on Plan</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <select name="comment_on_plan" id="comment_on_plan" class="form-control">
                                            <option value="vs">VS</option>
                                            <option value="pf">PF</option>
                                            <option value="mf">MF</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Open-end separators</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="open_end_separators" id="open_end_separators"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Chain : special</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="chain_special" id="chain_special"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Closed-end zippers</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="closed_end_zippers" id="closed_end_zippers"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Closed-end/Open-end :
                                        Special</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="closed_end_open_end" id="closed_end_open_end"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Monthly Report No.</label>
                                    <div class="col-sm-5 pr-0  ">
                                        <input type="text" name="monthly_report_no" id="monthly_report_no"
                                            class="modal-input shadow-none form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="block-content px-0 pt-0 row mt-2">
                                    <label class="col-sm-4 d-flex align-items-center modal-label">Sample Remarks.</label>
                                    <div class="col-sm-8 pr-0  ">
                                        <textarea name="sample_remarks" id="sample_remarks" rows="3"
                                            class="modal-input shadow-none form-control text-uppercase"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-action" id="generateReportBtn">
                                    <span class="btn-action-gear d-none mr-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                    <span class="btn-text">Run</span>
                                    <span class="btn-action-gear d-none ml-2"><img
                                            src="{{ asset('public/img/cf-menu-icons/gear.png') }}"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="reportLoaderModal" tabindex="-1" role="dialog" data-backdrop="static"
                data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-center p-4">
                        <div class="align-items-center d-flex">
                            <div class="spinner-border text-primary" role="status"></div>
                            <h5 class="mb-0 ml-3">Please wait while your report is being generated…</h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- change today end --}}
        </main>
        <!-- END Main Container -->
    @endsection('content')


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


    @section('js')
        <script type="text/javascript">
            $(document).ready(function() {
                // Define fetchItems function
                function fetchItems_new(query = '') {
                    $.post('{{ url('/fetch-test-names') }}', {
                        query: query,
                    }, function(response) {
                        const $select = $('#workOrderReportModal #test_name');
                        console.log($select.length);

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

                setTimeout(() => {
                    fetchItems_new();
                    $('#item_category').select2({
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
                                const optionEl = $('#item_category').find("option[value='" +
                                    data
                                    .text + "']");
                            }
                            return data.text || data.workorder_no;
                        }
                    });
                    $('#item_category_summery').select2({
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
                                const optionEl = $('#item_category_summery').find("option[value='" +
                                    data
                                    .text + "']");
                            }
                            return data.text || data.workorder_no;
                        }
                    });
                }, 200);

                $(document).on('keydown', '.no-negative', function(e) {
                    if (e.key === '-' || e.key === 'Subtract') {
                        e.preventDefault();
                    }
                });

                $(document).on('input', '.no-negative', function() {
                    if (this.value < 0) {
                        this.value = '';
                    }
                });


                $(document).on('click', '.barcode-click', function(e) {
                    e.preventDefault();
                    const barcodeSrc = $(this).data('barcode');
                    const barcodeWO = $(this).data('workorder');
                    $('#modalBarcodeImage').attr('src', barcodeSrc);
                    $('.barcode_workorder').text('*' + barcodeWO + '*');
                    $('#barcodeModal').modal('show');
                });


                let sampleCounter = 1;
                let currentThresholds = null;
                let skipvalidation = false;

                function showTestThreshold() {
                    var selectedTestType = $('#selectedTestType').val();
                    const testNameVal = $('#testNameSelect option:selected').val();
                    const selectedItemCategory = $('[name=fetched-item-category]').val();


                    $.ajax({
                        url: "{{ url('/fetch-threshold') }}",
                        type: 'POST',
                        data: {
                            test_name_id: testNameVal,
                            item_category_name: selectedItemCategory
                        },
                        success: function(threshold) {



                            currentThresholds = threshold;

                        }
                    })
                }


                function showTestThresholdEdit() {
                    var selectedTestType = $('#editselectedTestType').val();
                    const testNameVal = $('#edittestNameSelect option:selected').val();
                    const selectedItemCategory = $('#edit-sample-test-modal [name=fetched-item-category]').val();


                    $.ajax({
                        url: "{{ url('/fetch-threshold') }}",
                        type: 'POST',
                        data: {
                            test_name_id: testNameVal,
                            item_category_name: selectedItemCategory
                        },
                        success: function(threshold) {



                            currentThresholds = threshold;

                        }
                    })
                }



                $('.addSample').on('click', function() {
                    var selectedTestType = $('#selectedTestType').val();
                    var selectedCriteria = $('#selectedCriteria').val();
                    var selectedStandard = $('#selectedStandard').val();
                    const testNameVal = $('#testNameSelect option:selected').val();
                    const selectedItemCategory = $('[name=fetched-item-category]').val();
                    var standardType = $('input[name=insert_model_standard]:checked').val();
                    if (standardType == undefined && standardType == '') {
                        standardType = 'YFS';
                    }


                    let sampleValues = [];
                    let inputs = null;

                    if (selectedTestType !== 'Perf-Weight') {
                        inputs = $('input[name="sample[]"]');

                        inputs.each(function() {
                            const value = $(this).val().trim();
                            if (value !== '' && parseFloat(value) !== 0) {
                                sampleValues.push(value);
                            }
                        });

                        if (skipvalidation == false) {
                            if (sampleValues.length === 0) {
                                var message = `Please enter at least one non-zero sample value.`;
                                showCustomWarningNotification(message, "300px");
                                return;
                            }
                        }
                    }

                    $.ajax({
                        url: "{{ url('/fetch-threshold') }}",
                        type: 'POST',
                        data: {
                            test_name_id: testNameVal,
                            item_category_name: selectedItemCategory
                        },
                        success: function(threshold) {
                            if (!threshold || threshold.min == null || threshold.max == null) {
                                // alert("Threshold not found or incomplete.");
                                showCustomWarningNotification("Threshold not found or incomplete",
                                    "300px");
                                return;
                            }


                            currentThresholds = threshold;

                            let addedCount = 0;

                            const greenCheck =
                                "{{ asset('public/img/cf-menu-icons/greencheck.png') }}";
                            const redX = "{{ asset('public/img/cf-menu-icons/redxcircle.png') }}";
                            const warning_img =
                                "{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}";

                            if (selectedTestType !== 'Perf-Weight') {
                                sampleValues.forEach(function(value) {
                                    const numericValue = parseFloat(value);

                                    if (selectedTestType === 'Dimension') {

                                        let currentSample = sampleCounter;
                                        sampleCounter++;

                                        var result = (numericValue >= threshold.min &&
                                                numericValue <= threshold.max) ? 'pass' :
                                            'fail';

                                        $('#SelectedSamples').append(`
                            <div class="col-sm-20">
                                <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                    <div>
                                        <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                        <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="text-right mr-2">
                                            <span class="sample-value">${parseFloat(value).toFixed(3)}</span><br>
                                            <span class="sample-number">SAMPLE #${currentSample}</span>
                                        </div>
                                        <div>
                                            <div class="dropdown dropdown-3dot">
                                                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                    <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-3dot">
                                                    <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample" data-min="${threshold.min}" data-max="${threshold.max}" data-sample="${currentSample}" data-value="${parseFloat(value).toFixed(3)}" data-img="${result}" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                    <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample" data-sample="${currentSample}" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                                        // Initialize tooltip for the newly added element
                                        $('#SelectedSamples').find(
                                            '[data-toggle="tooltip"]:last').tooltip();

                                    } else if (selectedTestType === 'Perf-Str' &&
                                        selectedCriteria === 'Min') {
                                        appendSampleResult(standardType, threshold.YFS,
                                            threshold.YFGS, standardType,
                                            numericValue, threshold);
                                        // if (selectedStandard === 'YFS') {
                                        //     appendSampleResult('YFS', threshold.YFS, 'YFS',
                                        //         numericValue, threshold);
                                        // } else if (selectedStandard === 'YFGS') {
                                        //     appendSampleResult('YFGS', threshold.YFGS,
                                        //         'YFGS', numericValue, threshold);
                                        // }
                                        // renderSamplesBasedOnStandard($(
                                        //     'input[name="insert_model_standard"]:checked'
                                        // ).val());
                                    } else if (selectedTestType === 'Perf-Str' &&
                                        selectedCriteria === 'Max') {
                                        // const standardValue = selectedStandard === 'YFS' ?
                                        //     threshold.YFS : threshold.YFGS;
                                        const standardValue = standardType === 'YFS' ?
                                            threshold.YFS : threshold.YFGS;
                                        let result = (numericValue <= standardValue) ?
                                            'pass' : 'fail';

                                        let currentSample = sampleCounter;
                                        sampleCounter++;

                                        $('#SelectedSamplesConclusion .standard-value')
                                            .text(standardValue);
                                        $('#SelectedSamplesConclusion #standard_value')
                                            .val(threshold.YFS);
                                        $('#SelectedSamplesConclusion .safety-value')
                                            .hide();

                                        $('#SelectedSamples').append(`
                                    <div class="col-sm-20">
                                        <div data-sample="${currentSample}"
       data-raw="${numericValue}" data-yfs="${threshold.YFS}" data-yfgs="${threshold.YFGS}" data-criteria="max" class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                            <div>
                                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="text-right mr-2">
                                                    <span class="sample-value">${parseFloat(value).toFixed(2)}</span><br>
                                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                               data-standard="${standardValue}"
                                                               data-sample="${currentSample}"
                                                               data-img="${result}"
                                                               data-value="${parseFloat(value).toFixed(2)}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                               data-sample="${currentSample}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                        // Initialize tooltip for the newly added element
                                        $('#SelectedSamples').find(
                                            '[data-toggle="tooltip"]:last').tooltip();

                                        const stdDev = calculateStandardDeviation();
                                        $('.stdva-value').text(stdDev.roundedWhole);

                                    }

                                    addedCount++;
                                });


                            }

                            // ✅ PERF-WEIGHT HANDLING
                            else if (selectedTestType === 'Perf-Weight') {


                                $('#SelectedSamplesConclusion .standard-value').text(threshold
                                    .absorption.toFixed(2) + '%');
                                $('#SelectedSamplesConclusion .safety-value').text("MAX ABSORB %");

                                const beforeInputs = $('input[name="sample_before[]"]');
                                const afterInputs = $('input[name="sample_after[]"]');
                                let pairs = [];

                                for (let i = 0; i < beforeInputs.length; i++) {
                                    const beforeVal = $(beforeInputs[i]).val().trim();
                                    const afterVal = $(afterInputs[i]).val().trim();

                                    if (
                                        beforeVal !== '' && afterVal !== '' &&
                                        parseFloat(beforeVal) !== 0 && parseFloat(afterVal) !== 0
                                    ) {
                                        pairs.push({
                                            before: parseFloat(beforeVal),
                                            after: parseFloat(afterVal)
                                        });
                                    }
                                }

                                if (pairs.length < 1 || pairs.length > 5) {
                                    if (skipvalidation == false) {
                                        var message =
                                            `Please enter between 1 and 5 valid Perf-Weight samples (Before + After).`;
                                        showCustomWarningNotification(message, "350px");
                                    }
                                    return;
                                }

                                const absorptionThreshold = parseFloat(threshold.absorption);

                                pairs.forEach(function(pair) {

                                    let currentSample = sampleCounter;
                                    sampleCounter++;

                                    const diff = pair.after - pair.before;
                                    const rawResult = diff / pair.before;
                                    const result = parseFloat(rawResult.toFixed(4));
                                    const percentage = parseFloat((result * 100).toFixed(
                                        2));

                                    const isPass = percentage <= absorptionThreshold;

                                    $('#SelectedSamples').append(`
                        <div class="col-sm-20">
                            <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                <div>
                                    <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${isPass ? 'inline' : 'none'};">
                                    <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${!isPass ? 'inline' : 'none'};">
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-right mr-2">
                                        <span class="sample-value">${percentage}%</span><br>
                                        <span class="sample-number">SAMPLE #${currentSample}</span>
                                    </div>
                                    <div>
                                        <div class="dropdown dropdown-3dot">
                                            <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-3dot">
                                                <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                   data-sample="${currentSample}"
                                                   data-before="${pair.before}"
                                                   data-after="${pair.after}"
                                                   data-result="${result}"
                                                   data-percentage="${percentage}"
                                                   data-absorption="${absorptionThreshold}"
                                                   data-img="${isPass ? 'pass' : 'fail'}"
                                                   href="#">
                                                   <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                   data-sample="${currentSample}" href="#">
                                                   <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                                    // Initialize tooltip for the newly added element
                                    $('#SelectedSamples').find(
                                        '[data-toggle="tooltip"]:last').tooltip();

                                    addedCount++;
                                });

                                $('input[name="sample_before[]"], input[name="sample_after[]"]')
                                    .val('');
                            }

                            var totalSamples = $('#SelectedSamples .sample-entry').length;
                            $('#SelectedSamplesConclusion .total-samples').text(totalSamples);



                            var message = `${addedCount} sample value(s) added`;
                            showCustomWarningNotification(message, "300px");

                            function appendSampleResult(type, thresholdValueYFS, thresholdValueYFGS,
                                thresholdName,
                                numericValue, threshold) {
                                var thresholdValue = type == "YFS" ? thresholdValueYFS :
                                    thresholdValueYFGS;

                                let result;

                                if (numericValue < thresholdValue) {
                                    result = 'fail';
                                } else if (numericValue >= thresholdValue && numericValue <= (
                                        thresholdValue + threshold.safety_threshold)) {
                                    result = 'warning';
                                } else if (numericValue > thresholdValue) {
                                    result = 'pass';
                                }

                                let currentSample = sampleCounter;
                                sampleCounter++;

                                $('#SelectedSamplesConclusion .standard-value').text(
                                    thresholdValue);
                                $('#SelectedSamplesConclusion #standard_value')
                                    .val(thresholdValueYFS);
                                $('#SelectedSamplesConclusion .safety-value').text('+' + threshold
                                    .safety_threshold);

                                $('#SelectedSamples').append(`
                    <div class="col-sm-20">
                        <div data-sample="${currentSample}"
       data-raw="${numericValue}" data-safety-value="${threshold.safety_threshold}" data-yfs="${thresholdValueYFS}" data-yfgs="${thresholdValueYFGS}"  data-criteria="min" class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                            <div>
                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                <img class="result-img-warning-${currentSample}" src="${warning_img}" width="30" style="display: ${result === 'warning' ? 'inline' : 'none'};">
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-right mr-2">
                                    <span class="sample-value">${parseFloat(numericValue).toFixed(2)}</span><br>
                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                </div>
                                <div>
                                    <div class="dropdown dropdown-3dot">
                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-3dot">
                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                               data-standard="${thresholdValue}"
                                               data-safety="${threshold.safety_threshold}"
                                               data-sample="${currentSample}"
                                               data-img="${result}"
                                               data-value="${parseFloat(numericValue).toFixed(2)}" href="#">
                                               <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                               data-sample="${currentSample}" href="#">
                                               <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                                // Initialize tooltip for the newly added element
                                $('#SelectedSamples').find('[data-toggle="tooltip"]:last')
                                    .tooltip();

                                const stdDev = calculateStandardDeviation();
                                $('.stdva-value').text(stdDev.roundedDecimal);

                            }

                            updateFinalMinValue();
                            updateFinalMaxValue();
                            updateFinalAvgValue();

                            function calculateStandardDeviation() {
                                const values = [];

                                $('#SelectedSamples .sample-value').each(function() {
                                    const val = parseFloat($(this).text());
                                    if (!isNaN(val)) {
                                        values.push(val);
                                    }
                                });

                                if (values.length < 2) {
                                    return {
                                        roundedDecimal: 0,
                                        roundedWhole: 0
                                    };
                                }

                                const total = values.reduce((sum, val) => sum + val, 0);
                                const avg = total / values.length;

                                let sumOfSquaredDiffs = 0;
                                values.forEach(val => {
                                    const diff = val - avg;
                                    sumOfSquaredDiffs += diff * diff;
                                });

                                const variance = sumOfSquaredDiffs / (values.length - 1);
                                const stdDev = Math.sqrt(variance);

                                return {
                                    roundedDecimal: parseFloat(stdDev.toFixed(2)),
                                    roundedWhole: Math.round(stdDev)
                                };
                            }



                        },
                        error: function(xhr) {
                            let msg = "Unknown error";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                msg = xhr.responseJSON.error;
                            }
                            // alert("Error: " + msg);
                            showCustomWarningNotification("Error: " + msg, "300px");
                        }
                    });

                });

                // $(document).on('change', 'input[name="insert_model_standard"]', function() {
                //     if (!currentThresholds) return; // if no data yet, skip
                //     const selectedStandard = $(this).val(); // "YFS" or "YFGS"
                //     renderSamplesBasedOnStandard(selectedStandard);
                // });
                $(document).on('change', 'input[name="insert_model_standard"]', async function() {
                    const $summarySection = $('#SelectedSamplesConclusion');
                    const $loadingSpinner = $('#summaryLoadingSpinner');
                    const $radioButtons = $('input[name="insert_model_standard"]');

                    // Disable radio buttons during loading
                    $radioButtons.prop('disabled', true);

                    // Greyout summary section and show subtle loading spinner
                    $summarySection.addClass('loading');
                    $loadingSpinner.show();

                    try {
                        // Fetch new thresholds for the selected standard
                        await showTestThreshold();

                        // Wait for the system to load
                        await new Promise(resolve => setTimeout(resolve, 2000));

                        var selectedStandard = $('input[name="insert_model_standard"]:checked').val();
                        recalcSamples(selectedStandard);

                    } catch (error) {
                        console.error('Error changing standard:', error);
                        showCustomWarningNotification(
                            '<img src="${warningIcon}" width="24px" class="mt-n1"> Error loading standard data.',
                            "500px"
                        );
                    } finally {
                        // Re-enable radio buttons and remove loading state
                        $radioButtons.prop('disabled', false);
                        $summarySection.removeClass('loading');
                        $loadingSpinner.hide();
                    }
                });

                function recalcSamples(standard) {
                    const safety = currentThresholds.safety_threshold;
                    console.log(standard)

                    $('#SelectedSamples .sample-entry').each(function() {
                        const numericValue = parseFloat($(this).data('raw'));
                        const yfsValue = parseFloat($(this).attr('data-yfs'));
                        const yfgsValue = parseFloat($(this).attr('data-yfgs'));
                        const safetyvalue = parseFloat($(this).attr('data-safety-value'));
                        const criteria = $(this).attr('data-criteria');



                        // pick threshold depending on toggle
                        const thresholdValue = (standard === "YFS") ? yfsValue : yfgsValue;

                        let result;

                        if (criteria == "min") {
                            if (numericValue < thresholdValue) {
                                result = 'fail';
                            } else if (numericValue >= thresholdValue && numericValue <= (thresholdValue +
                                    (safetyvalue ? safetyvalue : 0))) {
                                result = 'warning';
                            } else {
                                result = 'pass';
                            }
                        } else {
                            result = (numericValue <= thresholdValue) ? 'pass' : 'fail';
                        }

                        // Update icons
                        const sampleId = $(this).data('sample');
                        $(this).find(`.result-img-pass-${sampleId}`).css('display', 'none');
                        $(this).find(`.result-img-fail-${sampleId}`).css('display', 'none');
                        $(this).find(`.result-img-warning-${sampleId}`).css('display', 'none');
                        $(this).find(`.edit-pen-sample`).attr('data-standard', thresholdValue);
                        if (result == "pass") {
                            $(this).find(`.result-img-pass-${sampleId}`).css('display', 'inline');
                        }
                        if (result == "warning") {
                            $(this).find(`.result-img-warning-${sampleId}`).css('display', 'inline');
                        }
                        if (result == "fail") {
                            $(this).find(`.result-img-fail-${sampleId}`).css('display', 'inline');
                        }
                    });

                    // Update summary values
                    const values = $('#SelectedSamples .sample-entry').map(function() {
                        return parseFloat($(this).data('raw'));
                    }).get();

                    if (values.length > 0) {
                        const min = Math.min(...values);
                        const max = Math.max(...values);
                        const avg = values.reduce((a, b) => a + b, 0) / values.length;
                        const stdDev = calculateStdDev(values);



                        $('#SelectedSamplesConclusion .min-value').text(min.toFixed(2));
                        $('#SelectedSamplesConclusion .max-value').text(max.toFixed(2));
                        $('#SelectedSamplesConclusion .avg-value').text(avg.toFixed(2));
                        $('.stdva-value').text(stdDev.roundedDecimal);
                    }
                    $('#SelectedSamplesConclusion .standard-value')
                        .text((standard === "YFS" ? currentThresholds.YFS ?? currentThresholds.standard_value :
                                currentThresholds.YFGS ?? currentThresholds.standard_value) + '(' + currentThresholds
                            .uom + ')');
                    // $('#SelectedSamplesConclusion #standard_value').val(currentThresholds.YFS);
                    $('#SelectedSamplesConclusion #standard_value')
                        .val(standard === "YFS" ? currentThresholds.YFS ?? currentThresholds.standard_value :
                            currentThresholds.YFGS ?? currentThresholds.standard_value);
                    $('#SelectedSamplesConclusion .test-standard')
                        .text(standard === "YFS" ? 'YFS' : "YFGS");
                    $('#SelectedSamplesConclusion .safety-value').text("+" + safety);

                    updateFinalMinValue();
                    updateFinalMaxValue();
                    updateFinalAvgValue();
                }

                function calculateStdDev(values) {
                    if (values.length < 2) return {
                        roundedDecimal: 0,
                        roundedWhole: 0
                    };

                    const avg = values.reduce((a, b) => a + b, 0) / values.length;
                    const variance = values.reduce((sum, v) => sum + Math.pow(v - avg, 2), 0) / (values.length - 1);
                    const stdDev = Math.sqrt(variance);

                    return {
                        roundedDecimal: parseFloat(stdDev.toFixed(2)),
                        roundedWhole: Math.round(stdDev)
                    };
                }
                $(document).on('change', 'input[name="edit_model_standard"]', async function() {
                    const $editSummarySection = $(
                        '#editSelectedSamples'); // Replace with your actual edit summary section ID
                    const $loadingSpinner = $('#editSummaryLoadingSpinner');
                    const $radioButtons = $('input[name="edit_model_standard"]');

                    // Disable radio buttons during loading
                    $radioButtons.prop('disabled', true);

                    // Greyout summary section and show loading spinner
                    $editSummarySection.addClass('loading');
                    $loadingSpinner.show();

                    try {
                        // Fetch new thresholds for the selected standard
                        await showTestThresholdEdit();

                        // Wait for the system to load
                        await new Promise(resolve => setTimeout(resolve, 2000));

                        const selectedStandard = $(this).val();
                        editrecalcSamples(selectedStandard);

                    } catch (error) {
                        console.error('Error changing edit standard:', error);
                        showCustomWarningNotification(
                            '<img src="${warningIcon}" width="24px" class="mt-n1"> Error loading standard data.',
                            "500px"
                        );
                    } finally {
                        // Re-enable radio buttons and remove loading state
                        $radioButtons.prop('disabled', false);
                        $editSummarySection.removeClass('loading');
                        $loadingSpinner.hide();
                    }
                });

                function editrecalcSamples(standard) {

                    const safety = currentThresholds.safety_threshold;

                    $('#editSelectedSamples .sample-entry').each(function() {
                        const numericValue = parseFloat($(this).data('raw'));
                        const yfsValue = parseFloat($(this).attr('data-yfs'));
                        const yfgsValue = parseFloat($(this).attr('data-yfgs'));
                        const safetyvalue = parseFloat($(this).attr('data-safety-value'));
                        const criteria = $(this).attr('data-criteria');



                        // pick threshold depending on toggle
                        const thresholdValue = (standard === "YFS") ? yfsValue : yfgsValue;

                        let result;
                        // if (numericValue > thresholdValue) {
                        //     result = 'fail';
                        // } else if (numericValue >= thresholdValue && numericValue <= (thresholdValue +
                        //         safety)) {
                        //     result = 'warning';
                        // } else {
                        //     result = 'pass';
                        // }

                        if (criteria == "min") {
                            if (numericValue < thresholdValue) {
                                // if (numericValue > thresholdValue) {
                                result = 'fail';
                            } else if (numericValue >= thresholdValue && numericValue <= (thresholdValue +
                                    (safetyvalue ? safetyvalue : 0))) {
                                result = 'warning';
                            } else {
                                result = 'pass';
                            }
                        } else {
                            result = (numericValue <= thresholdValue) ? 'pass' : 'fail';
                        }

                        // Update icons
                        const sampleId = $(this).data('sample');
                        $(this).find(`.result-img-pass-${sampleId}`).css('display', 'none');
                        $(this).find(`.result-img-fail-${sampleId}`).css('display', 'none');
                        $(this).find(`.result-img-warning-${sampleId}`).css('display', 'none');
                        $(this).find(`.edit-pen-sample`).attr('data-standard', thresholdValue);
                        if (result == "pass") {
                            $(this).find(`.result-img-pass-${sampleId}`).css('display', 'inline');
                        }
                        if (result == "warning") {
                            $(this).find(`.result-img-warning-${sampleId}`).css('display', 'inline');
                        }
                        if (result == "fail") {
                            $(this).find(`.result-img-fail-${sampleId}`).css('display', 'inline');
                        }
                    });

                    // Update summary values
                    const values = $('#editSelectedSamples .sample-entry').map(function() {
                        return parseFloat($(this).data('raw'));
                    }).get();

                    if (values.length > 0) {
                        const min = Math.min(...values);
                        const max = Math.max(...values);
                        const avg = values.reduce((a, b) => a + b, 0) / values.length;
                        const stdDev = editcalculateStdDev(values);



                        $('#editSelectedSamplesConclusion .min-value').text(min.toFixed(2));
                        $('#editSelectedSamplesConclusion .max-value').text(max.toFixed(2));
                        $('#editSelectedSamplesConclusion .avg-value').text(avg.toFixed(2));
                        $('.stdva-value').text(stdDev.roundedDecimal);
                    }
                    console.log(currentThresholds)
                    $('#editSelectedSamplesConclusion .standard-value')
                        .text((standard === "YFS" ? currentThresholds.YFS ?? currentThresholds.standard_value :
                                currentThresholds.YFGS ?? currentThresholds.standard_value) + '(' + currentThresholds
                            .uom + ')');
                    $('#editSelectedSamplesConclusion #standard_value')
                        .val(currentThresholds.YFS ?? currentThresholds.standard_value);
                    $('#editSelectedSamplesConclusion .test-standard')
                        .text(standard === "YFS" ? 'YFS' : "YFGS");
                    $('#editSelectedSamplesConclusion .safety-value').text("+" + safety);
                    editUpdateFinalMinValue();
                    editUpdateFinalMaxValue();
                    editUpdateFinalAvgValue();
                }

                function editcalculateStdDev(values) {
                    if (values.length < 2) return {
                        roundedDecimal: 0,
                        roundedWhole: 0
                    };

                    const avg = values.reduce((a, b) => a + b, 0) / values.length;
                    const variance = values.reduce((sum, v) => sum + Math.pow(v - avg, 2), 0) / (values.length - 1);
                    const stdDev = Math.sqrt(variance);

                    return {
                        roundedDecimal: parseFloat(stdDev.toFixed(2)),
                        roundedWhole: Math.round(stdDev)
                    };
                }

                $(document).on('click', '#btn-import-sample', function() {
                    const fileInput = $('input[name="import_file"]')[0];
                    if (!fileInput.files || fileInput.files.length === 0) {
                        showCustomWarningNotification("Please select a CSV file", "300px");
                        return;
                    }

                    const file = fileInput.files[0];
                    if (file.type !== "text/csv" && !file.name.endsWith(".csv")) {
                        showCustomWarningNotification("Invalid file type. Please select a CSV.", "300px");
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const contents = e.target.result;
                        const rows = contents.split(/\r?\n/);
                        let importedSamples = [];
                        let summaryStats = {
                            mean: null,
                            minimum: null,
                            maximum: null,
                            stdDev: null
                        };

                        rows.forEach((row, index) => {
                            if (!row.trim()) return;

                            const cols = row.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
                            if (!cols[0]) return;

                            const firstCol = cols[0].replace(/"/g, '').trim().toLowerCase();

                            if (/^0*\d+$/.test(firstCol)) {
                                // const force = parseFloat(cols[1] ? cols[1].replace(/"/g, '')
                                //     .trim() : NaN);
                                // const displacement = parseFloat(cols[2] ? cols[2].replace(/"/g, '')
                                //     .trim() : NaN);

                                // if (!isNaN(displacement)) {
                                //     importedSamples.push({
                                //         force,
                                //         displacement
                                //     });
                                // }
                                const force = parseFloat(cols[1] ? cols[1].replace(/"/g, '').trim() : NaN); // Column B (N)

                                if (!isNaN(force)) {
                                    importedSamples.push({ value: force }); // store as value
                                }
                            } else if (firstCol === "mean") {
                                summaryStats.mean = parseFloat(cols[1] ? cols[1].replace(/"/g,'').trim() : NaN); // Column B
                            } else if (firstCol === "minimum") {
                                summaryStats.minimum = parseFloat(cols[1] ? cols[1].replace(/"/g,'').trim() : NaN); // Column B
                            } else if (firstCol === "maximum") {
                                summaryStats.maximum = parseFloat(cols[1] ? cols[1].replace(/"/g,'').trim() : NaN); // Column B
                            } else if (firstCol === "standard deviation") {
                                summaryStats.stdDev = parseFloat(cols[1] ? cols[1].replace(/"/g,'').trim() : NaN); // Column B
                                return false;
                            }
                        });

                        if (importedSamples.length === 0) {
                            showCustomWarningNotification("CSV file is invalid", "300px");
                            return;
                        }

                        // Clear only dynamically added sample inputs, preserve form inputs
                        $('input[name="sample[]"]').not('.non-perf-weight-samples input').remove();

                        // Add new hidden input fields for imported samples
                        importedSamples.forEach((sample, index) => {
                            $('#SelectedSamples').append(
                                `<input type="text" name="sample[]" class="d-none">`);
                            // $(`input[name="sample[]"]:eq(${index})`).val(sample.displacement)
                            //     .trigger('change');
                            $(`input[name="sample[]"]:eq(${index})`).val(sample.value).trigger('change');

                        });

                        var $button = $('#btn-import-sample');
                        $button.prop('disabled', true);
                        $button.find('.btn-action-gear').removeClass('d-none');
                        $button.find('.btn-action-gear img').addClass('rotating');

                        var selectedTestType = $('#selectedTestType').val();
                        var selectedCriteria = $('#selectedCriteria').val();
                        var selectedStandard = $('#selectedStandard').val();
                        const testNameVal = $('#testNameSelect option:selected').val();
                        const selectedItemCategory = $('[name=fetched-item-category]').val();
                        var standardType = $('input[name=insert_model_standard]:checked').val() || 'YFS';

                        let sampleValues = [];
                        if (selectedTestType !== 'Perf-Weight') {
                            $('input[name="sample[]"]').each(function() {
                                const value = $(this).val().trim();
                                if (value !== '' && parseFloat(value) !== 0) {
                                    sampleValues.push(value);
                                }
                            });

                            if (sampleValues.length === 0) {
                                showCustomWarningNotification(
                                    "Please enter at least one non-zero sample value.", "300px");
                                resetButtonState($button);
                                return;
                            }
                        }

                        setTimeout(() => {
                            $.ajax({
                                url: "{{ url('/fetch-threshold') }}",
                                type: 'POST',
                                data: {
                                    test_name_id: testNameVal,
                                    item_category_name: selectedItemCategory
                                },
                                success: function(threshold) {
                                    if (!threshold || threshold.min == null || threshold
                                        .max == null) {
                                        showCustomWarningNotification(
                                            "Threshold not found or incomplete",
                                            "300px");
                                        resetButtonState($button);
                                        return;
                                    }

                                    let addedCount = 0;
                                    const greenCheck =
                                        "{{ asset('public/img/cf-menu-icons/greencheck.png') }}";
                                    const redX =
                                        "{{ asset('public/img/cf-menu-icons/redxcircle.png') }}";
                                    const warning_img =
                                        "{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}";

                                    // Clear existing samples in UI
                                    $('#SelectedSamples').empty();

                                    // Show non-perf-str divs for Dimension tests
                                    if (selectedTestType === 'Dimension') {
                                        $('#SelectedSamplesConclusion .non-perf-str')
                                            .show();
                                        $('#SelectedSamplesConclusion .perf-str')
                                            .hide();
                                        $('#SelectedSamplesConclusion .sample-uom')
                                            .text('mm');
                                    }

                                    if (selectedTestType !== 'Perf-Weight') {
                                        sampleValues.forEach(function(value, index) {
                                            const numericValue = parseFloat(
                                                value);

                                            if (selectedTestType ===
                                                'Dimension') {
                                                let currentSample =
                                                    sampleCounter;
                                                sampleCounter++;

                                                var result = (numericValue >=
                                                        threshold.min &&
                                                        numericValue <=
                                                        threshold.max) ?
                                                    'pass' : 'fail';

                                                $('#SelectedSamples').append(`
                                    <div class="col-sm-20">
                                        <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                            <div>
                                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="text-right mr-2">
                                                    <span class="sample-value">${numericValue.toFixed(3)}</span><br>
                                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample" data-min="${threshold.min}" data-max="${threshold.max}" data-sample="${currentSample}" data-value="${numericValue.toFixed(3)}" data-img="${result}" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample" data-sample="${currentSample}" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                                $('#SelectedSamples').find(
                                                    '[data-toggle="tooltip"]:last'
                                                ).tooltip();
                                            } else if (selectedTestType ===
                                                'Perf-Str' &&
                                                selectedCriteria === 'Min') {
                                                appendSampleResult(standardType,
                                                    threshold.YFS, threshold
                                                    .YFGS, standardType,
                                                    numericValue, threshold);
                                            } else if (selectedTestType ===
                                                'Perf-Str' &&
                                                selectedCriteria === 'Max') {
                                                const standardValue =
                                                    standardType === 'YFS' ?
                                                    threshold.YFS : threshold
                                                    .YFGS;
                                                let result = (numericValue <=
                                                        standardValue) ?
                                                    'pass' : 'fail';

                                                let currentSample =
                                                    sampleCounter;
                                                sampleCounter++;

                                                $('#SelectedSamplesConclusion .standard-value')
                                                    .text(standardValue);
                                                $('#SelectedSamplesConclusion #standard_value')
                                                    .val(threshold.YFS);
                                                $('#SelectedSamplesConclusion .safety-value')
                                                    .hide();

                                                $('#SelectedSamples').append(`
                                    <div class="col-sm-20">
                                        <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                            <div>
                                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="text-right mr-2">
                                                    <span class="sample-value">${numericValue.toFixed(2)}</span><br>
                                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                               data-standard="${standardValue}"
                                                               data-sample="${currentSample}"
                                                               data-img="${result}"
                                                               data-value="${numericValue.toFixed(2)}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                               data-sample="${currentSample}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                                $('#SelectedSamples').find(
                                                    '[data-toggle="tooltip"]:last'
                                                ).tooltip();

                                                const stdDev =
                                                    calculateStandardDeviation();
                                                $('.stdva-value').text(stdDev
                                                    .roundedWhole);
                                            }

                                            addedCount++;
                                        });

                                    } else if (selectedTestType === 'Perf-Weight') {
                                        $('#SelectedSamplesConclusion .standard-value')
                                            .text(threshold.absorption.toFixed(2) +
                                                '%');
                                        $('#SelectedSamplesConclusion .safety-value')
                                            .text("MAX ABSORB %");

                                        const beforeInputs = $(
                                            'input[name="sample_before[]"]');
                                        const afterInputs = $(
                                            'input[name="sample_after[]"]');
                                        let pairs = [];

                                        for (let i = 0; i < beforeInputs.length; i++) {
                                            const beforeVal = $(beforeInputs[i]).val()
                                                .trim();
                                            const afterVal = $(afterInputs[i]).val()
                                                .trim();

                                            if (beforeVal !== '' && afterVal !== '' &&
                                                parseFloat(beforeVal) !== 0 &&
                                                parseFloat(afterVal) !== 0) {
                                                pairs.push({
                                                    before: parseFloat(
                                                        beforeVal),
                                                    after: parseFloat(afterVal)
                                                });
                                            }
                                        }

                                        if (pairs.length < 1 || pairs.length > 5) {
                                            showCustomWarningNotification(
                                                "Please enter between 1 and 5 valid Perf-Weight samples (Before + After).",
                                                "350px");
                                            resetButtonState($button);
                                            return;
                                        }

                                        const absorptionThreshold = parseFloat(threshold
                                            .absorption);

                                        pairs.forEach(function(pair) {
                                            let currentSample = sampleCounter;
                                            sampleCounter++;

                                            const diff = pair.after - pair
                                                .before;
                                            const rawResult = diff / pair
                                                .before;
                                            const result = parseFloat(rawResult
                                                .toFixed(4));
                                            const percentage = parseFloat((
                                                result * 100).toFixed(
                                                2));

                                            const isPass = percentage <=
                                                absorptionThreshold;

                                            $('#SelectedSamples').append(`
                                <div class="col-sm-20">
                                    <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                        <div>
                                            <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${isPass ? 'inline' : 'none'};">
                                            <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${!isPass ? 'inline' : 'none'};">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="text-right mr-2">
                                                <span class="sample-value">${percentage}%</span><br>
                                                <span class="sample-number">SAMPLE #${currentSample}</span>
                                            </div>
                                            <div>
                                                <div class="dropdown dropdown-3dot">
                                                    <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                        <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-3dot">
                                                        <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                           data-sample="${currentSample}"
                                                           data-before="${pair.before}"
                                                           data-after="${pair.after}"
                                                           data-result="${result}"
                                                           data-percentage="${percentage}"
                                                           data-absorption="${absorptionThreshold}"
                                                           data-img="${isPass ? 'pass' : 'fail'}"
                                                           href="#">
                                                           <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                        <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                           data-sample="${currentSample}" href="#">
                                                           <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                                            $('#SelectedSamples').find(
                                                '[data-toggle="tooltip"]:last'
                                            ).tooltip();

                                            addedCount++;
                                        });

                                        $('input[name="sample_before[]"], input[name="sample_after[]"]')
                                            .val('');
                                    }

                                    // Update total samples
                                    const totalSamples = $(
                                        '#SelectedSamples .sample-entry').length;
                                    $('#SelectedSamplesConclusion .total-samples').text(
                                        totalSamples);


                                    const stdDev = calculateStandardDeviation();
                                    $('.stdva-value').text(stdDev.roundedWhole);
                                    updateFinalMinValue();
                                    updateFinalMaxValue();
                                    updateFinalAvgValue();

                                    $('input[name="sample[]"]').each(function() {
                                        $(this).val('');


                                    });

                                    showCustomWarningNotification(
                                        `${addedCount} sample value(s) added`,
                                        "300px");

                                    function appendSampleResult(type, thresholdValueYFS,
                                        thresholdValueYFGS, thresholdName, numericValue,
                                        threshold) {
                                        var thresholdValue = type === "YFS" ?
                                            thresholdValueYFS : thresholdValueYFGS;

                                        let result;
                                        if (numericValue < thresholdValue) {
                                            result = 'fail';
                                        } else if (numericValue >= thresholdValue &&
                                            numericValue <= (thresholdValue + threshold
                                                .safety_threshold)) {
                                            result = 'warning';
                                        } else if (numericValue > thresholdValue) {
                                            result = 'pass';
                                        }

                                        let currentSample = sampleCounter;
                                        sampleCounter++;

                                        $('#SelectedSamplesConclusion .standard-value')
                                            .text(thresholdValue);
                                        $('#SelectedSamplesConclusion #standard_value')
                                            .val(thresholdValueYFS);
                                        $('#SelectedSamplesConclusion .safety-value')
                                            .text('+' + threshold.safety_threshold);

                                        $('#SelectedSamples').append(`
                            <div class="col-sm-20">
                                <div data-sample="${currentSample}"
                                     data-raw="${numericValue}"
                                     data-yfs="${thresholdValueYFS}"
                                     data-safety-value="${threshold.safety_threshold}"
                                     data-yfgs="${thresholdValueYFGS}"
                                     class="sample-entry d-flex justify-content-between align-items-center my-2"
                                     data-toggle="tooltip"
                                     data-trigger="hover"
                                     data-placement="top"
                                     title=""
                                     data-original-title="SAMPLE #${currentSample}">
                                    <div>
                                        <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                        <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                        <img class="result-img-warning-${currentSample}" src="${warning_img}" width="30" style="display: ${result === 'warning' ? 'inline' : 'none'};">
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="text-right mr-2">
                                            <span class="sample-value">${numericValue.toFixed(2)}</span><br>
                                            <span class="sample-number">SAMPLE #${currentSample}</span>
                                        </div>
                                        <div>
                                            <div class="dropdown dropdown-3dot">
                                                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                    <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-3dot">
                                                    <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                       data-standard="${thresholdValue}"
                                                       data-safety="${threshold.safety_threshold}"
                                                       data-sample="${currentSample}"
                                                       data-img="${result}"
                                                       data-value="${numericValue.toFixed(2)}" href="#">
                                                       <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                    <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                       data-sample="${currentSample}" href="#">
                                                       <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);

                                        $('#SelectedSamples').find(
                                                '[data-toggle="tooltip"]:last')
                                            .tooltip();

                                        const stdDev = calculateStandardDeviation();
                                        $('.stdva-value').text(stdDev.roundedDecimal);
                                    }

                                    function calculateStandardDeviation() {
                                        const values = [];

                                        $('#SelectedSamples .sample-value').each(
                                            function() {
                                                const val = parseFloat($(this)
                                                    .text());
                                                if (!isNaN(val)) {
                                                    values.push(val);
                                                }
                                            });

                                        if (values.length < 2) {
                                            return {
                                                roundedDecimal: 0,
                                                roundedWhole: 0
                                            };
                                        }

                                        const total = values.reduce((sum, val) => sum +
                                            val, 0);
                                        const avg = total / values.length;

                                        let sumOfSquaredDiffs = 0;
                                        values.forEach(val => {
                                            const diff = val - avg;
                                            sumOfSquaredDiffs += diff * diff;
                                        });

                                        const variance = sumOfSquaredDiffs / (values
                                            .length - 1);
                                        const stdDev = Math.sqrt(variance);

                                        return {
                                            roundedDecimal: parseFloat(stdDev.toFixed(
                                                2)),
                                            roundedWhole: Math.round(stdDev)
                                        };
                                    }
                                },
                                error: function(xhr) {
                                    let msg = xhr.responseJSON?.error ||
                                        "Unknown error";
                                    showCustomWarningNotification("Error: " + msg,
                                        "300px");
                                    resetButtonState($button);
                                }
                            });

                            resetButtonState($button);
                            $('#import-modal').modal('hide');
                            showCustomWarningNotification(
                                `${importedSamples.length} sample value(s) imported`, "300px");
                        }, 200);
                    };

                    reader.readAsText(file);
                });
                $('#btn-import-sample-edit').on('hidden.bs.modal', function() {
                    $('input[name="import_file_edit"]').val(''); // clear file input
                });
                $('#btn-import-sample').on('hidden.bs.modal', function() {
                    $('input[name="import_file"]').val(''); // clear file input
                });
                $(document).on('click', '#btn-import-sample-edit', function() {
                    const fileInput = $('input[name="import_file_edit"]')[0];
                    if (!fileInput.files || fileInput.files.length === 0) {
                        showCustomWarningNotification("Please select a CSV file", "300px");
                        return;
                    }

                    const file = fileInput.files[0];
                    if (file.type !== "text/csv" && !file.name.endsWith(".csv")) {
                        showCustomWarningNotification("Invalid file type. Please select a CSV.", "300px");
                        return;
                    }

                    // Ensure editSampleCounter is defined
                    if (typeof editSampleCounter === 'undefined') {
                        window.editSampleCounter = 1;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const contents = e.target.result;
                        const rows = contents.split(/\r?\n/);
                        let importedSamples = [];
                        let summaryStats = {
                            mean: null,
                            minimum: null,
                            maximum: null,
                            stdDev: null
                        };

                        rows.forEach((row, index) => {
                            if (!row.trim()) return;

                            const cols = row.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
                            if (!cols[0]) return;

                            const firstCol = cols[0].replace(/"/g, '').trim().toLowerCase();

                            if (/^0*\d+$/.test(firstCol)) {
                                const force = parseFloat(cols[1] ? cols[1].replace(/"/g, '')
                                    .trim() : NaN);
                                const displacement = parseFloat(cols[2] ? cols[2].replace(/"/g, '')
                                    .trim() : NaN);

                                if (!isNaN(displacement)) {
                                    importedSamples.push({
                                        force,
                                        displacement
                                    });
                                }
                            } else if (firstCol === "mean") {
                                summaryStats.mean = parseFloat(cols[2] ? cols[2].replace(/"/g, '')
                                    .trim() : NaN);
                            } else if (firstCol === "minimum") {
                                summaryStats.minimum = parseFloat(cols[2] ? cols[2].replace(/"/g,
                                    '').trim() : NaN);
                            } else if (firstCol === "maximum") {
                                summaryStats.maximum = parseFloat(cols[2] ? cols[2].replace(/"/g,
                                    '').trim() : NaN);
                            } else if (firstCol === "standard deviation") {
                                summaryStats.stdDev = parseFloat(cols[2] ? cols[2].replace(/"/g, '')
                                    .trim() : NaN);
                                return false;
                            }
                        });

                        console.log("Imported Samples (Edit):", importedSamples);
                        console.log("Summary Stats (Edit):", summaryStats);

                        if (importedSamples.length === 0) {
                            showCustomWarningNotification("CSV file is invalid", "300px");
                            return;
                        }

                        // Clear only dynamically added sample inputs
                        $('input[name="sample[]"]').not('.non-perf-weight-samples input').remove();

                        // Add new hidden input fields for imported samples
                        importedSamples.forEach((sample, index) => {
                            $('#editSelectedSamples').append(
                                `<input type="text" name="sample[]" class="d-none">`);
                            $(`input[name="sample[]"]:eq(${index})`).val(sample.displacement)
                                .trigger('change');
                        });

                        var $button = $('#btn-import-sample-edit');
                        $button.prop('disabled', true);
                        $button.find('.btn-action-gear').removeClass('d-none');
                        $button.find('.btn-action-gear img').addClass('rotating');

                        var selectedTestType = $('#editselectedTestType').val();
                        var selectedCriteria = $('#editselectedCriteria').val();
                        var selectedStandard = $('#editselectedStandard').val();
                        const testNameVal = $('#edittestNameSelect option:selected').val();
                        const selectedItemCategory = $('#edit-sample-test-modal .fetched-item-category')
                            .text();
                        var standardType = $('input[name=edit_model_standard]:checked').val() || 'YFS';

                        let sampleValues = [];
                        if (selectedTestType !== 'Perf-Weight') {
                            $('input[name="sample[]"]').each(function() {
                                const value = $(this).val().trim();
                                if (value !== '' && parseFloat(value) !== 0) {
                                    sampleValues.push(value);
                                }
                            });

                            if (sampleValues.length === 0) {
                                showCustomWarningNotification(
                                    "Please enter at least one non-zero sample value.", "300px");
                                resetButtonState($button);
                                return;
                            }
                        }

                        setTimeout(() => {
                            $.ajax({
                                url: "{{ url('/fetch-threshold') }}",
                                type: 'POST',
                                data: {
                                    test_name_id: testNameVal,
                                    item_category_name: selectedItemCategory
                                },
                                success: function(threshold) {
                                    if (!threshold || threshold.min == null || threshold
                                        .max == null) {
                                        showCustomWarningNotification(
                                            "Threshold not found or incomplete",
                                            "300px");
                                        resetButtonState($button);
                                        return;
                                    }

                                    let addedCount = 0;
                                    const greenCheck =
                                        "{{ asset('public/img/cf-menu-icons/greencheck.png') }}";
                                    const redX =
                                        "{{ asset('public/img/cf-menu-icons/redxcircle.png') }}";
                                    const warning_img =
                                        "{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}";

                                    // Clear existing samples in UI
                                    $('#editSelectedSamples').empty();

                                    // Show non-perf-str divs for Dimension tests
                                    if (selectedTestType === 'Dimension') {
                                        $('#editSelectedSamplesConclusion .non-perf-str')
                                            .show();
                                        $('#editSelectedSamplesConclusion .perf-str')
                                            .hide();
                                        $('#editSelectedSamplesConclusion .sample-uom')
                                            .text('mm');
                                    }

                                    if (selectedTestType !== 'Perf-Weight') {
                                        sampleValues.forEach(function(value, index) {
                                            const numericValue = parseFloat(
                                                value);

                                            if (selectedTestType ===
                                                'Dimension') {
                                                let currentSample =
                                                    editSampleCounter;
                                                editSampleCounter++;

                                                var result = (numericValue >=
                                                        threshold.min &&
                                                        numericValue <=
                                                        threshold.max) ?
                                                    'pass' : 'fail';

                                                $('#editSelectedSamples')
                                                    .append(`
                                    <div class="col-sm-20">
                                        <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                            <div>
                                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="text-right mr-2">
                                                    <span class="sample-value">${numericValue.toFixed(3)}</span><br>
                                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample" data-min="${threshold.min}" data-max="${threshold.max}" data-sample="${currentSample}" data-value="${numericValue.toFixed(3)}" data-img="${result}" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample" data-sample="${currentSample}" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                                $('#editSelectedSamples').find(
                                                    '[data-toggle="tooltip"]:last'
                                                ).tooltip();
                                            } else if (selectedTestType ===
                                                'Perf-Str' &&
                                                selectedCriteria === 'Min') {
                                                editAppendSampleResult(
                                                    standardType, threshold
                                                    .YFS, threshold.YFGS,
                                                    standardType,
                                                    numericValue, threshold);
                                            } else if (selectedTestType ===
                                                'Perf-Str' &&
                                                selectedCriteria === 'Max') {
                                                const standardValue =
                                                    standardType === 'YFS' ?
                                                    threshold.YFS : threshold
                                                    .YFGS;
                                                let result = (numericValue <=
                                                        standardValue) ?
                                                    'pass' : 'fail';

                                                let currentSample =
                                                    editSampleCounter;
                                                editSampleCounter++;

                                                $('#editSelectedSamplesConclusion .standard-value')
                                                    .text(standardValue);
                                                $('#editSelectedSamplesConclusion #standard_value')
                                                    .val(threshold.YFS);
                                                $('#editSelectedSamplesConclusion .safety-value')
                                                    .hide();

                                                $('#editSelectedSamples')
                                                    .append(`
                                    <div class="col-sm-20">
                                        <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                            <div>
                                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="text-right mr-2">
                                                    <span class="sample-value">${numericValue.toFixed(2)}</span><br>
                                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                               data-standard="${standardValue}"
                                                               data-sample="${currentSample}"
                                                               data-img="${result}"
                                                               data-value="${numericValue.toFixed(2)}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                               data-sample="${currentSample}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);
                                                $('#editSelectedSamples').find(
                                                    '[data-toggle="tooltip"]:last'
                                                ).tooltip();

                                                const stdDev =
                                                    editCalculateStandardDeviation();
                                                $('.stdva-value').text(stdDev
                                                    .roundedWhole);
                                            }

                                            addedCount++;
                                        });


                                    } else if (selectedTestType === 'Perf-Weight') {
                                        $('#editSelectedSamplesConclusion .standard-value')
                                            .text(threshold.absorption.toFixed(2) +
                                                '%');
                                        $('#editSelectedSamplesConclusion .safety-value')
                                            .text("MAX ABSORB %");

                                        const beforeInputs = $(
                                            'input[name="sample_before[]"]');
                                        const afterInputs = $(
                                            'input[name="sample_after[]"]');
                                        let pairs = [];

                                        for (let i = 0; i < beforeInputs.length; i++) {
                                            const beforeVal = $(beforeInputs[i]).val()
                                                .trim();
                                            const afterVal = $(afterInputs[i]).val()
                                                .trim();

                                            if (beforeVal !== '' && afterVal !== '' &&
                                                parseFloat(beforeVal) !== 0 &&
                                                parseFloat(afterVal) !== 0) {
                                                pairs.push({
                                                    before: parseFloat(
                                                        beforeVal),
                                                    after: parseFloat(afterVal)
                                                });
                                            }
                                        }

                                        if (pairs.length < 1 || pairs.length > 5) {
                                            showCustomWarningNotification(
                                                "Please enter between 1 and 5 valid Perf-Weight samples (Before + After).",
                                                "350px");
                                            resetButtonState($button);
                                            return;
                                        }

                                        const absorptionThreshold = parseFloat(threshold
                                            .absorption);

                                        pairs.forEach(function(pair) {
                                            let currentSample =
                                                editSampleCounter;
                                            editSampleCounter++;

                                            const diff = pair.after - pair
                                                .before;
                                            const rawResult = diff / pair
                                                .before;
                                            const result = parseFloat(rawResult
                                                .toFixed(4));
                                            const percentage = parseFloat((
                                                result * 100).toFixed(
                                                2));

                                            const isPass = percentage <=
                                                absorptionThreshold;

                                            $('#editSelectedSamples').append(`
                                <div class="col-sm-20">
                                    <div class="sample-entry d-flex justify-content-between align-items-center my-2" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="SAMPLE #${currentSample}">
                                        <div>
                                            <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${isPass ? 'inline' : 'none'};">
                                            <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${!isPass ? 'inline' : 'none'};">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="text-right mr-2">
                                                <span class="sample-value">${percentage}%</span><br>
                                                <span class="sample-number">SAMPLE #${currentSample}</span>
                                            </div>
                                            <div>
                                                <div class="dropdown dropdown-3dot">
                                                    <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                        <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-3dot">
                                                        <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                           data-sample="${currentSample}"
                                                           data-before="${pair.before}"
                                                           data-after="${pair.after}"
                                                           data-result="${result}"
                                                           data-percentage="${percentage}"
                                                           data-absorption="${absorptionThreshold}"
                                                           data-img="${isPass ? 'pass' : 'fail'}"
                                                           href="#">
                                                           <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                        <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                           data-sample="${currentSample}" href="#">
                                                           <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                                            $('#editSelectedSamples').find(
                                                '[data-toggle="tooltip"]:last'
                                            ).tooltip();

                                            addedCount++;
                                        });

                                        $('input[name="sample_before[]"], input[name="sample_after[]"]')
                                            .val('');
                                    }

                                    // Update total samples
                                    const totalSamples = $(
                                        '#editSelectedSamples .sample-entry').length;
                                    $('#editSelectedSamplesConclusion .total-samples')
                                        .text(totalSamples);
                                    console.log("Total Samples Updated (Edit):",
                                        totalSamples);

                                    // Update summary statistics for Dimension tests
                                    editUpdateFinalMinValue();
                                    editUpdateFinalMaxValue();
                                    editUpdateFinalAvgValue();
                                    const stdDev = editCalculateStandardDeviation();
                                    $('.stdva-value').text(stdDev.roundedWhole);
                                    showCustomWarningNotification(
                                        `${addedCount} sample value(s) added`,
                                        "300px");
                                },
                                error: function(xhr) {
                                    let msg = xhr.responseJSON?.error ||
                                        "Unknown error";
                                    showCustomWarningNotification("Error: " + msg,
                                        "300px");
                                    resetButtonState($button);
                                }
                            });

                            resetButtonState($button);
                            $('#import-modal-edit').modal('hide');
                            showCustomWarningNotification(
                                `${importedSamples.length} sample value(s) imported`, "300px");
                        }, 200);
                    };

                    reader.readAsText(file);
                });


                function updateFinalMinValue() {
                    const testType = $('#selectedTestType').val();
                    const criteria = $('#selectedCriteria').val();

                    let min = null;

                    $('#SelectedSamples .sample-value').each(function() {
                        const val = parseFloat($(this).text());
                        if (!isNaN(val)) {
                            if (min === null || val < min) min = val;
                        }
                    });

                    $('.final-min-value').text(min !== null ? min : '0');
                    $('.final-result-min-img-pass, .final-result-min-img-fail, .final-result-min-img-warning').hide();
                    if (min === null) return;

                    const $first = $('.edit-pen-sample').first();

                    if (testType === 'Dimension') {
                        $('.final-min-value').text(min !== null ? parseFloat(min).toFixed(3) : '0.000');
                        const thresholdMin = parseFloat($first.attr('data-min'));
                        if (!isNaN(thresholdMin)) {
                            min >= thresholdMin ?
                                $('.final-result-min-img-pass').show() :
                                $('.final-result-min-img-fail').show();
                            min >= thresholdMin ?
                                $('.final-min-img').val('pass') :
                                $('.final-min-img').val('fail');
                        }
                    } else if (testType === 'Perf-Str') {
                        $('.final-min-value').text(min !== null ? parseFloat(min).toFixed(2) : '0.00');
                        const standard = parseFloat($first.attr('data-standard'));
                        const safety = parseFloat($first.attr('data-safety'));
                        if (!isNaN(standard)) {
                            if (criteria === 'Min') {
                                if (min < standard) {
                                    $('.final-result-min-img-fail').show();
                                    $('.final-min-img').val('fail');
                                } else if (min >= standard && min <= (standard + safety)) {
                                    $('.final-result-min-img-warning').show();
                                    $('.final-min-img').val('warning');
                                } else {
                                    $('.final-result-min-img-pass').show();
                                    $('.final-min-img').val('pass');
                                }
                            } else if (criteria === 'Max') {
                                min <= standard ?
                                    $('.final-result-min-img-pass').show() :
                                    $('.final-result-min-img-fail').show();
                                min <= standard ?
                                    $('.final-min-img').val('pass') :
                                    $('.final-min-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Weight') {
                        $('.final-min-value').text(min !== null ? min + '%' : '0.0000');
                        const thresholdMin = parseFloat($first.attr('data-absorption'));
                        if (!isNaN(thresholdMin)) {
                            min <= thresholdMin ?
                                $('.final-result-min-img-pass').show() :
                                $('.final-result-min-img-fail').show();
                            min <= thresholdMin ?
                                $('.final-min-img').val('pass') :
                                $('.final-min-img').val('fail');
                        }
                    }
                }

                function updateFinalMaxValue() {
                    const testType = $('#selectedTestType').val();
                    const criteria = $('#selectedCriteria').val();

                    let max = null;

                    $('#SelectedSamples .sample-value').each(function() {
                        const val = parseFloat($(this).text());
                        if (!isNaN(val)) {
                            if (max === null || val > max) max = val;
                        }
                    });

                    $('.final-max-value').text(max !== null ? max : '0');
                    $('.final-result-max-img-pass, .final-result-max-img-fail, .final-result-max-img-warning').hide();
                    if (max === null) return;

                    const $first = $('.edit-pen-sample').first();

                    if (testType === 'Dimension') {
                        $('.final-max-value').text(max !== null ? parseFloat(max).toFixed(3) : '0.000');
                        const thresholdMax = parseFloat($first.attr('data-max'));
                        if (!isNaN(thresholdMax)) {
                            max <= thresholdMax ?
                                $('.final-result-max-img-pass').show() :
                                $('.final-result-max-img-fail').show();
                            max <= thresholdMax ?
                                $('.final-max-img').val('pass') :
                                $('.final-max-img').val('fail');
                        }
                    } else if (testType === 'Perf-Str') {
                        $('.final-max-value').text(max !== null ? parseFloat(max).toFixed(2) : '0.00');
                        const standard = parseFloat($first.attr('data-standard'));
                        const safety = parseFloat($first.attr('data-safety'));
                        if (!isNaN(standard)) {
                            if (criteria === 'Min') {
                                if (max < standard) {
                                    $('.final-result-max-img-fail').show();
                                    $('.final-max-img').val('fail');
                                } else if (max >= standard && max <= (standard + safety)) {
                                    $('.final-result-max-img-warning').show();
                                    $('.final-max-img').val('warning');
                                } else {
                                    $('.final-result-max-img-pass').show();
                                    $('.final-max-img').val('pass');
                                }
                            } else if (criteria === 'Max') {
                                max <= standard ?
                                    $('.final-result-max-img-pass').show() :
                                    $('.final-result-max-img-fail').show();
                                max <= standard ?
                                    $('.final-max-img').val('pass') :
                                    $('.final-max-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Weight') {
                        $('.final-max-value').text(max !== null ? max + '%' : '0.0000');
                        const thresholdMin = parseFloat($first.attr('data-absorption'));
                        if (!isNaN(thresholdMin)) {
                            max <= thresholdMin ?
                                $('.final-result-max-img-pass').show() :
                                $('.final-result-max-img-fail').show();
                            max <= thresholdMin ?
                                $('.final-max-img').val('pass') :
                                $('.final-max-img').val('fail');
                        }
                    }
                }

                function updateFinalAvgValue() {
                    const testType = $('#selectedTestType').val();
                    const criteria = $('#selectedCriteria').val();

                    let total = 0,
                        count = 0,
                        min = null,
                        max = null;

                    $('#SelectedSamples .sample-value').each(function() {
                        const val = parseFloat($(this).text());
                        if (!isNaN(val)) {
                            total += val;
                            count++;
                            if (min === null || val < min) min = val;
                            if (max === null || val > max) max = val;
                        }
                    });

                    $('.final-result-avg-img-pass, .final-result-avg-img-fail, .final-result-avg-img-warning').hide();

                    if (count === 0) {
                        $('.final-avg-value').text('0');
                        $('.avg-minus, .avg-plus').text('0');
                        return;
                    }
                    var uom = $('input[name=selectedUOM]').val()
                    const avg = total / count;
                    $('.final-avg-value').text(avg.toFixed(2));
                    $('.avg-minus').text((avg - min).toFixed(2));
                    $('.avg-plus').text((max - avg).toFixed(2) + ' (' + uom + ')');


                    const $first = $('.edit-pen-sample').first();

                    if (testType === 'Dimension') {
                        $('.final-avg-value').text(avg !== null ? parseFloat(avg).toFixed(3) : '0.000');
                        $('.final-avg-value1').text(avg !== null ? parseFloat(avg).toFixed(3) : '0.000');


                        const minT = parseFloat($first.attr('data-min'));
                        const maxT = parseFloat($first.attr('data-max'));
                        if (!isNaN(minT) && !isNaN(maxT)) {
                            if (avg >= minT && avg <= maxT) {
                                $('.final-result-avg-img-pass').show();
                                $('.final-avg-img').val('pass');
                            } else {
                                $('.final-result-avg-img-fail').show();
                                $('.final-avg-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Str') {

                        $('.final-avg-value').text(avg !== null ? parseFloat(avg).toFixed(2) : '0.00');
                        const standard = parseFloat($first.attr('data-standard'));
                        const safety = parseFloat($first.attr('data-safety'));
                        if (!isNaN(standard)) {
                            if (criteria === 'Min') {
                                if (avg < standard) {
                                    $('.final-result-avg-img-fail').show();
                                    $('.final-avg-img').val('fail');
                                } else if (avg >= standard && avg <= (standard + safety)) {
                                    $('.final-result-avg-img-warning').show();
                                    $('.final-avg-img').val('warning');
                                } else {
                                    $('.final-result-avg-img-pass').show();
                                    $('.final-avg-img').val('pass');
                                }
                            } else if (criteria === 'Max') {
                                // if(avg <= standard) {
                                //     $('.final-result-avg-img-pass').show()
                                //     console.log('pass');
                                // } else {
                                //     $('.final-result-avg-img-fail').show()
                                //     console.log('fail');
                                // }
                                avg <= standard ?
                                    $('.final-result-avg-img-pass').show() :
                                    $('.final-result-avg-img-fail').show();
                                avg <= standard ?
                                    $('.final-avg-img').val('pass') :
                                    $('.final-avg-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Weight') {
                        $('.final-avg-value').text(avg.toFixed(2) + '%');
                        const thresholdMin = parseFloat($first.attr('data-absorption'));
                        if (!isNaN(thresholdMin)) {
                            avg <= thresholdMin ?
                                $('.final-result-avg-img-pass').show() :
                                $('.final-result-avg-img-fail').show();
                            avg <= thresholdMin ?
                                $('.final-avg-img').val('pass') :
                                $('.final-avg-img').val('fail');
                        }
                    }
                }


                function calculatePercentageChange() {
                    let before = parseFloat($('#edit-pen-sample-value').val());
                    let after = parseFloat($('#edit-pen-sample-value-after').val());

                    if (!isNaN(before) && before !== 0 && !isNaN(after)) {
                        let result = ((after - before) / before);
                        let rounded = (result * 100).toFixed(2) + '%';

                        $('#edit-pen-sample-percent').val(rounded);
                        $('.before-result').val(before);
                        $('.after-result').val(after);
                        $('.percentage-result').val(rounded);
                    } else {
                        $('#edit-pen-sample-percent').val('');
                        $('.percentage-result').val('');
                    }
                }

                // Run calculation on focus out
                $('#edit-pen-sample-value, #edit-pen-sample-value-after').on('focusout', calculatePercentageChange);



                $('#SelectedSamples').on('click', '.edit-pen-sample', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var selectedUOM = $('[name=selectedUOM]').val();
                    $('.edit-pen-uom').val(selectedUOM);

                    var sample = $(this).data('sample');
                    var value = $(this).data('value');

                    $('#edit-pen-sample-value').val(value);
                    $('.edit-sample-heading').text(sample);
                    $('.edit-label').text('Result');
                    $('.edit-pen-perf-weight').hide();

                    // Show/hide fields based on test type
                    var selectedTestType = $('#selectedTestType').val();
                    var criteria = $('#selectedCriteria').val();

                    if (selectedTestType === 'Dimension') {
                        var min = $(this).data('min');
                        var max = $(this).data('max');
                        var result = $(this).data('img');

                        $('.min-result').val(min);
                        $('.max-result').val(max);
                        $('.img-result').val(result);
                    } else if (selectedTestType === 'Perf-Str') {
                        var standard = $(this).data('standard');
                        var safety = $(this).data('safety');
                        var result = $(this).data('img');

                        $('.standard-result').val(standard);
                        $('.safety-result').val(safety);
                        $('.img-result').val(result);
                    } else if (selectedTestType === 'Perf-Weight') {
                        $('.edit-label').text('Before Bosubi');
                        $('.edit-pen-perf-weight').show();
                        var result = $(this).data('img');

                        var before = $(this).data('before');
                        var after = $(this).data('after');
                        var percentage = $(this).data('percentage');
                        var absorption = $(this).data('absorption');

                        $('.img-result').val(result);

                        $('.before-result').val(before);
                        $('.after-result').val(after);
                        $('.percentage-result').val(percentage);
                        $('.absorption-result').val(absorption);
                        $('#edit-pen-sample-value').val(before);
                        $('#edit-pen-sample-value-after').val(after);
                        $('#edit-pen-sample-percent').val(percentage + '%');
                    }

                    $('#EditSampleValueModal').modal('show');
                });

                $('#saveEditedSample').on('click', function() {
                    var newValue = $('#EditSampleValueModal #edit-pen-sample-value').val().trim();
                    const sampleName = $('#EditSampleValueModal .edit-sample-heading').text().trim();

                    if (!newValue) {
                        alert('Please enter a sample value.');
                        return;
                    }

                    const numericValue = parseFloat(newValue);
                    const selectedTestType = $('#selectedTestType').val();
                    const criteria = $('#selectedCriteria').val();

                    let result = 'fail';

                    const $sampleBlock = $('#insert-sample-test-modal .sample-number').filter(function() {
                        return $(this).text().trim() === 'SAMPLE #' + sampleName;
                    }).closest('#insert-sample-test-modal .sample-entry');

                    if (!$sampleBlock.length) {
                        alert('Could not find the sample to update.');
                        return;
                    }

                    if (selectedTestType === 'Dimension') {
                        newValue = parseFloat(newValue).toFixed(3);
                        $sampleBlock.find('.sample-value').text(newValue);
                        $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value',
                            newValue);
                        const min = parseFloat($('.min-result').val());
                        const max = parseFloat($('.max-result').val());

                        result = (numericValue >= min && numericValue <= max) ? 'pass' : 'fail';

                        $sampleBlock.find('.edit-pen-sample')
                            .attr('data-min', min)
                            .attr('data-max', max)
                            .attr('data-img', result)
                            .data('min', min)
                            .data('max', max)
                            .data('img', result);
                    } else if (selectedTestType === 'Perf-Str') {
                        newValue = parseFloat(newValue).toFixed(2);
                        $sampleBlock.find('.sample-value').text(newValue);
                        $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value',
                            newValue);
                        const standard = parseFloat($('.standard-result').val());
                        const safety = parseFloat($('.safety-result').val());

                        if (criteria === 'Min') {
                            if (numericValue < standard) {
                                result = 'fail';
                            } else if (numericValue >= standard && numericValue <= (standard + safety)) {
                                result = 'warning';
                            } else {
                                result = 'pass';
                            }
                        } else if (criteria === 'Max') {
                            result = numericValue <= standard ? 'pass' : 'fail';
                        }

                        $sampleBlock.find('.edit-pen-sample')
                            .attr('data-standard', standard)
                            .attr('data-safety', safety)
                            .attr('data-img', result)
                            .data('standard', standard)
                            .data('safety', safety)
                            .data('img', result);
                    } else if (selectedTestType === 'Perf-Weight') {
                        const before = parseFloat($('.before-result').val());
                        const after = parseFloat($('.after-result').val());
                        const percentage = parseFloat($('.percentage-result').val());
                        const absorption = parseFloat($('.absorption-result').val());

                        newValue = percentage + '%';

                        result = (percentage <= absorption) ? 'pass' : 'fail';

                        $sampleBlock.find('.edit-pen-sample')
                            .attr('data-before', before)
                            .attr('data-after', after)
                            .attr('data-percentage', percentage)
                            .attr('data-img', result)
                            .data('before', before)
                            .data('after', after)
                            .data('percentage', percentage)
                            .data('img', result);

                        $sampleBlock.find('.sample-value').text(newValue);
                        $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value',
                            newValue);
                    }

                    // Toggle icons
                    $sampleBlock.find('[class^="result-img-"]').hide();
                    $sampleBlock.find('.result-img-' + result + '-' + sampleName).show();

                    // $sampleBlock.find('.sample-value').text(newValue);
                    // $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value', newValue);

                    updateFinalMinValue();
                    updateFinalMaxValue();
                    updateFinalAvgValue();

                    $('#EditSampleValueModal').modal('hide');
                });


                $('#SelectedSamples').on('click', '.del-pen-sample', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const sampleId = $(this).data('sample');

                    const $sampleBlock = $('.sample-number').filter(function() {
                        return $(this).text().trim() === 'SAMPLE #' + sampleId;
                    }).closest('.col-sm-20');

                    if ($sampleBlock.length) {
                        // ✅ Save before removing
                        lastRemovedItem = {
                            html: $sampleBlock.prop('outerHTML'),
                            index: $sampleBlock.index()
                        };

                        $sampleBlock.find('[data-toggle="tooltip"]').tooltip('dispose');

                        $sampleBlock.remove();
                    }

                    // Renumber samples
                    let newSampleCounter = 1;
                    $('#SelectedSamples .sample-entry').each(function() {
                        const $entry = $(this);
                        const $numberSpan = $entry.find('.sample-number');
                        const newSampleNumber = newSampleCounter++;

                        $numberSpan.text('SAMPLE #' + newSampleNumber);
                        $entry.find('[class^="result-img-pass-"]').attr('class', 'result-img-pass-' +
                            newSampleNumber);
                        $entry.find('[class^="result-img-fail-"]').attr('class', 'result-img-fail-' +
                            newSampleNumber);
                        $entry.find('.edit-pen-sample, .del-pen-sample')
                            .attr('data-sample', newSampleNumber)
                            .data('sample', newSampleNumber);
                    });

                    sampleCounter = $('#SelectedSamples .sample-entry').length + 1;

                    var totalSamples = $('#SelectedSamples .sample-entry').length;
                    $('#SelectedSamplesConclusion .total-samples').text(totalSamples);
                    updateFinalMinValue();
                    updateFinalMaxValue();
                    updateFinalAvgValue();

                    var message =
                        `Sample value deleted <a href="javascript:;" class="btn-notify undo-remove-sample ml-4">Undo</a>`;
                    showCustomWarningNotification(message, "300px");
                });


                // Handle undo click from notification
                $(document).on('click', '.undo-remove-sample', function() {
                    if (lastRemovedItem) {
                        const $itemsContainer = $('#SelectedSamples');
                        const itemCount = $itemsContainer.children().length;

                        // Restore item at original position
                        const $restoredItem = $(lastRemovedItem.html);
                        if (lastRemovedItem.index >= itemCount) {
                            $itemsContainer.append($restoredItem);
                        } else {
                            $itemsContainer.children().eq(lastRemovedItem.index).before($restoredItem);
                        }

                        // Clear lastRemovedItem
                        lastRemovedItem = null;

                        // ✅ Renumber all samples and update sampleCounter
                        renumberSamples();

                        // ✅ Close any open 3-dot dropdowns
                        // Close any open 3-dot dropdowns manually
                        $('.dropdown-menu.show').removeClass('show').removeAttr('style');
                        $('.dropdown-toggle.show').removeClass('show').attr('aria-expanded', 'false');

                        var totalSamples = $('#SelectedSamples .sample-entry').length;
                        $('#SelectedSamplesConclusion .total-samples').text(totalSamples);
                        updateFinalMinValue();
                        updateFinalMaxValue();
                        updateFinalAvgValue();

                        var message = `Sample value restored`;
                        showCustomWarningNotification(message, "300px");

                    }
                });

                function renumberSamples() {
                    let count = 1;

                    $('#SelectedSamples .sample-entry').each(function() {
                        const $entry = $(this);

                        // Update text like "SAMPLE #1"
                        $entry.find('.sample-number').text('SAMPLE #' + count);

                        // Update result image classes
                        $entry.find('[class^="result-img-pass-"]').attr('class', 'result-img-pass-' + count);
                        $entry.find('[class^="result-img-fail-"]').attr('class', 'result-img-fail-' + count);

                        // Update data-sample attributes
                        $entry.find('.edit-pen-sample, .del-pen-sample')
                            .attr('data-sample', count)
                            .data('sample', count);

                        count++;
                    });

                    // ✅ Update global counter to next number
                    sampleCounter = count;
                }


                $('.select2').select2({
                    placeholder: "Select Test Name",
                    allowClear: true,
                    minimumResultsForSearch: 0 // Optional: show search always
                });
                // $('#filterForm .select2').select2({
                //     allowClear: true,
                //     minimumResultsForSearch: 0
                // });

                $('#insert-sample-test-modal').on('hidden.bs.modal', function() {
                    var $modal = $(this);

                    // Clear all input fields
                    $modal.find('input[type="text"],input[type="number"]').val('');

                    // Clear all textarea fields
                    $modal.find('textarea').val('');

                    // Reset all select dropdowns
                    $modal.find('select').prop('selectedIndex', 0);

                    // Reset checkboxes and radio buttons
                    $modal.find('input[type=checkbox], input[type=radio]').prop('checked', false);

                    // Clear Select2 fields
                    $modal.find('select.select2').val(null).trigger('change');
                    $modal.find('#WorkOrderSelect').val(null).trigger('change');

                    // Clear #SelectedSamplesConclusion contents (remove all dynamically added elements)
                    // $modal.find('#SelectedSamplesConclusion').empty();
                    $modal.find('#SelectedSamples').empty();

                    // ✅ Reset sample counter
                    sampleCounter = 1;

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




                let editSampleCounter = 1;

                // Function to load test data for editing
                function loadSampleTestForEdit(sampleTestId) {
                    $.ajax({
                        url: '{{ url('get-sample-test') }}',
                        method: 'POST',
                        data: {
                            id: sampleTestId
                        },
                        success: async function(response) {
                            console.log(response.test);

                            if (response.success) {
                                const test = response.test;
                                const samples = response.samples;

                                await fetchEditWorkorders(test.workorder_no)

                                currentThresholds = test
                                // Populate form fields
                                $('#edit_sample_test_id').val(test.id);
                                $('.selected-date').text(test.sample_date);

                                // Set test name and related fields
                                $('#editAssetSelect').val(test.asset_id).trigger('change');
                                $('#edittestNameSelect').val(test.test_name_id).trigger('change');
                                $('#editselectedTestType').val(test.test_type);
                                $('#editselectedCriteria').val(test.criteria);
                                $('#editselectedStandard').val(test.test_standard);
                                $('#editselectedUOM').val(test.uom);
                                $('#edittestDesc').val(test.test_desc);
                                $('#edit_comments').val(test.comments);
                                $('input[name=edit_model_standard][value="' + test.test_standard + '"]')
                                    .prop('checked', true)
                                if (test.test_type == 'Perf-Str') {
                                    $('.edit-model-standard-div').addClass('d-flex').show();
                                } else {
                                    $('.edit-model-standard-div').removeClass('d-flex').hide();
                                }


                                if (test.test_type == 'Perf-Weight') {

                                    $('#editBEFORE_new').parent().hide();

                                    $('#editAFTER_new').parent().hide();

                                } else {
                                    $('#editBEFORE_new').parent().show();

                                    $('#editAFTER_new').parent().show();

                                }

                                // Set work order and related fields
                                setTimeout(() => {
                                    $('#editWorkOrderSelect').val(test.workorder_id).trigger(
                                        'change');
                                    $('.fetched-item-category').text(test.item_category);
                                    $('.fetched-itemcode').text(test.itemcode);
                                    $('.fetched-itemcode-desc').text(test.itemcode_desc);
                                    $('.fetched-itemcode-color').text(test.color);
                                    $('.fetched-workorder-length').text(test.length + '"');
                                }, 500);

                                // Set step 2 fields
                                $('#edit-sample-test-modal input[name="bosubi"]').prop('checked',
                                    false); // clear all radios first
                                $(`#edit-sample-test-modal input[name="bosubi"][value="${test.bosubi}"]`)
                                    .prop('checked', true); // set from data

                                $('#editlot').val(test.lot);
                                $('#editproduction_date').val(test.production_date);

                                // Set UOM
                                $('#editSelectedSamplesConclusion .sample-uom').text(test.uom);

                                // Set conclusion values
                                if (test.test_type == 'Perf-Weight') {
                                    $('.final-min-value').text(test.min);
                                    $('.final-avg-value').text(test.avg + '(' + test.uom + ')');
                                    $('.final-max-value').text(test.max);
                                } else if (test.test_type == 'Perf-Str') {
                                    $('.final-min-value').text(parseFloat(test.min).toFixed(2));
                                    $('.final-avg-value').text(parseFloat(test.avg).toFixed(2) + '(' + test
                                        .uom + ')');
                                    $('.final-max-value').text(parseFloat(test.max).toFixed(2));
                                } else if (test.test_type == 'Dimension') {

                                    $('.final-min-value').text(parseFloat(test.min).toFixed(3));
                                    $('.final-avg-value').text((parseFloat(test.avg).toFixed(3)) + '(' +
                                        test.uom + ')');
                                    $('.final-max-value').text(parseFloat(test.max).toFixed(3));
                                }

                                $('.avg-minus').text(test.avg_minus);
                                $('.avg-plus').text(test.avg_plus);
                                $('#editSelectedSamplesConclusion .stdva-value').text(test.stdva_value);
                                $('.standard-value').text(test.standard_value + '(' + test.uom + ')');
                                $('#edit-sample-test-modal #standard_value').val(test.standard_value);
                                $('.safety-value').text('+' + test.safety_threshold);
                                $('.test-standard').text(test.test_standard);

                                // Set result icons
                                $('.final-result-min-img-pass, .final-result-min-img-fail, .final-result-min-img-warning')
                                    .hide();
                                $('.final-result-avg-img-pass, .final-result-avg-img-fail, .final-result-avg-img-warning')
                                    .hide();
                                $('.final-result-max-img-pass, .final-result-max-img-fail, .final-result-max-img-warning')
                                    .hide();

                                $(`.final-result-min-img-${test.min_result}`).show();
                                $(`.final-result-avg-img-${test.avg_result}`).show();
                                $(`.final-result-max-img-${test.max_result}`).show();

                                $('.final-min-img').val(test.min_result);
                                $('.final-avg-img').val(test.avg_result);
                                $('.final-max-img').val(test.max_result);

                                // Load samples
                                $('#editSelectedSamples').empty();
                                editSampleCounter = 1;


                                samples.forEach(sample => {
                                    const greenCheck =
                                        "{{ asset('public/img/cf-menu-icons/greencheck.png') }}";
                                    const redX =
                                        "{{ asset('public/img/cf-menu-icons/redxcircle.png') }}";
                                    const warning_img =
                                        "{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}";

                                    let sampleHtml = '';
                                    let resultImg = '';
                                    let dataAttributes = '';
                                    let result = 'fail'; // default

                                    if (test.test_type === 'Perf-Weight') {

                                        $('#edit-sample-test-modal input[name="bosubi"][value="AFTER"], #edit-sample-test-modal input[name="bosubi"][value="BEFORE"]')
                                            .prop('disabled', true);


                                        const percentage = sample.sample_value;
                                        const absorption = test.absorption_value.replace('%', '');
                                        const isPass = percentage <= parseFloat(absorption);
                                        result = isPass ? 'pass' : 'fail';

                                        resultImg = `
        <img class="result-img-pass-${editSampleCounter}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
        <img class="result-img-fail-${editSampleCounter}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
        <img class="result-img-warning-${editSampleCounter}" src="${warning_img}" width="30" style="display: none;">
    `;

                                        dataAttributes =
                                            `data-before="${sample.sample_before}" data-after="${sample.sample_after}" ` +
                                            `data-percentage="${percentage}" data-absorption="${absorption}" ` +
                                            `data-img="${result}"`;

                                    } else if (test.test_type === 'Perf-Str' && test.criteria ===
                                        'Min') {
                                        const standard = parseFloat(test.standard_value);
                                        const safety = parseFloat(test.safety_threshold);

                                        if (sample.sample_value < standard) {
                                            result = 'fail';
                                        } else if (sample.sample_value >= standard && sample
                                            .sample_value <= (standard + safety)) {
                                            result = 'warning';
                                        } else {
                                            result = 'pass';
                                        }

                                        resultImg = `
        <img class="result-img-pass-${editSampleCounter}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
        <img class="result-img-fail-${editSampleCounter}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
        <img class="result-img-warning-${editSampleCounter}" src="${warning_img}" width="30" style="display: ${result === 'warning' ? 'inline' : 'none'};">
    `;

                                        dataAttributes =
                                            `data-standard="${standard}" data-safety="${safety}" data-img="${result}"`;

                                    } else if (test.test_type === 'Dimension') {
                                        const min = parseFloat(test.standard_min);
                                        const max = parseFloat(test.standard_max);
                                        const isPass = sample.sample_value >= min && sample
                                            .sample_value <= max;
                                        result = isPass ? 'pass' : 'fail';

                                        resultImg = `
        <img class="result-img-pass-${editSampleCounter}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
        <img class="result-img-fail-${editSampleCounter}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
        <img class="result-img-warning-${editSampleCounter}" src="${warning_img}" width="30" style="display: none;">
    `;

                                        dataAttributes =
                                            `data-min="${min}" data-max="${max}" data-img="${result}"`;

                                    } else if (test.test_type === 'Perf-Str' && test.criteria ===
                                        'Max') {
                                        const standard = parseFloat(test.standard_value);
                                        const isPass = sample.sample_value <= standard;
                                        result = isPass ? 'pass' : 'fail';

                                        resultImg = `
        <img class="result-img-pass-${editSampleCounter}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
        <img class="result-img-fail-${editSampleCounter}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
        <img class="result-img-warning-${editSampleCounter}" src="${warning_img}" width="30" style="display: none;">
    `;

                                        dataAttributes =
                                            `data-standard="${standard}" data-img="${result}"`;
                                    }


                                    sampleHtml = `
                        <div class="col-sm-20">
                            <div class="sample-entry d-flex justify-content-between align-items-center my-2">
                                <div>
                                    ${resultImg}
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-right mr-2">
                                        <span class="sample-value">${
                                            test.test_type === 'Perf-Weight'
                                                ? sample.sample_value + '%'
                                                : (
                                                    test.test_type === 'Perf-Str'
                                                        ? parseFloat(sample.sample_value).toFixed(2) // exactly 2 decimals
                                                        : parseFloat(sample.sample_value).toFixed(3) // exactly 3 decimals
                                                )
                                        }</span>
                                        <br>
                                        <span class="sample-number">SAMPLE #${editSampleCounter}</span>
                                    </div>
                                    <div>
                                        <div class="dropdown dropdown-3dot">
                                            <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-3dot">
                                                <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                   data-sample="${editSampleCounter}"
                                                       data-value="${
                                                            test.test_type === 'Perf-Weight'
                                                                ? sample.sample_value
                                                                : (
                                                                    test.test_type === 'Perf-Str'
                                                                        ? parseFloat(sample.sample_value).toFixed(2) // exactly 2 decimals
                                                                        : parseFloat(sample.sample_value).toFixed(3) // exactly 3 decimals
                                                                )
                                                        }"
                                                   ${dataAttributes}
                                                   href="#">
                                                   <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                   data-sample="${editSampleCounter}"
                                                   href="#">
                                                   <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                                    $('#editSelectedSamples').append(sampleHtml);
                                    editSampleCounter++;
                                });

                                // Update total samples count
                                $('#editSelectedSamplesConclusion .total-samples').text(samples.length);

                                // Show the appropriate sections based on test type
                                $('.info-circle, .test-type-input, .perf-str-input, .perf-weight-input, .dimension-input, .test-standard, .safety-value')
                                    .hide();
                                $('#editSelectedSamplesConclusion .non-perf-str, .non-perf-weight-samples, .perf-str, .perf-weight-samples,.dimention-str')
                                    .hide();
                                $('.nosample').removeClass('col-sm-2').addClass('col-sm-3');

                                if (test.test_type === 'Dimension') {
                                    $('.test-type-input, .dimension-input,.dimention-str').show();
                                    $('.nosample').removeClass('col-sm-3').addClass('col-sm-2');

                                    $('#editSelectedSamplesConclusion .non-perf-str, .non-perf-weight-samples')
                                        .show();
                                    $('input[name=standard_value]').val(test.standard_value)



                                } else if (test.test_type === 'Perf-Str' && test.criteria === 'Min') {
                                    $('.test-type-input, .perf-str-input').show();
                                    $('.safety-value').show().text("+" + test.safety_threshold);
                                    $('.test-standard').show().text(test.test_standard);
                                    $('#editSelectedSamplesConclusion .perf-str, .non-perf-weight-samples')
                                        .show();
                                } else if (test.test_type === 'Perf-Str' && test.criteria === 'Max') {
                                    $('.test-type-input, .perf-str-input').show();
                                    $('.test-standard').show().text(test.test_standard);
                                    $('#editSelectedSamplesConclusion .perf-str, .non-perf-weight-samples')
                                        .show();
                                } else if (test.test_type === 'Perf-Weight') {
                                    $('.test-type-input, .perf-weight-input').show();
                                    $('.safety-value').show().text("MAX ABSORB %");
                                    $('#editSelectedSamplesConclusion .perf-str, .perf-weight-samples').not(
                                        '.stdva-div').show();
                                }

                                // // Show step 2 directly since we're editing
                                // $('.step-1').hide();
                                // $('.step-2').show();

                                $('.test-name-step-2').text($('#edittestNameSelect option:selected')
                                    .text());

                                setTimeout(() => {
                                    $('.workorder-step-2').text('WO# ' + $(
                                        '#editWorkOrderSelect option:selected').text());

                                }, 500);

                                // Disable qc-calendar clicks
                                $('.qc-calendar').css('pointer-events', 'none');

                                // Show the modal
                                $('#edit-sample-test-modal').modal('show');

                                editrecalcSamples(test.test_standard)

                            } else {
                                alert('Failed to load test data: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            alert('Error loading test data: ' + xhr.responseText);
                        }
                    });
                }


                // Edit form add sample functionality
                $('.editaddSample').on('click', function() {
                    var selectedTestType = $('#editselectedTestType').val();
                    var selectedCriteria = $('#editselectedCriteria').val();
                    var selectedStandard = $('#editselectedStandard').val();
                    const testNameVal = $('#edittestNameSelect option:selected').val();
                    const selectedItemCategory = $('#edit-sample-test-modal .fetched-item-category').text();
                    var standardType = $('input[name=edit_model_standard]:checked').val();
                    if (standardType == undefined && standardType == '') {
                        standardType = 'YFS';
                    }

                    let sampleValues = [];
                    let inputs = null;

                    if (selectedTestType !== 'Perf-Weight') {
                        inputs = $('input[name="sample[]"]');

                        inputs.each(function() {
                            const value = $(this).val().trim();
                            if (value !== '' && parseFloat(value) !== 0) {
                                sampleValues.push(value);
                            }
                        });

                        if (sampleValues.length === 0) {
                            var message = `Please enter at least one non-zero sample value.`;
                            showCustomWarningNotification(message, "300px");
                            return;
                        }
                    }

                    $.ajax({
                        url: "{{ url('/fetch-threshold') }}",
                        type: 'POST',
                        data: {
                            test_name_id: testNameVal,
                            item_category_name: selectedItemCategory
                        },
                        success: function(threshold) {
                            if (!threshold || threshold.min == null || threshold.max == null) {
                                alert("Threshold not found or incomplete.");
                                return;
                            }
                            var sampleuom = $('.sample-uom').text()
                            let addedCount = 0;

                            currentThresholds = threshold;

                            const greenCheck =
                                "{{ asset('public/img/cf-menu-icons/greencheck.png') }}";
                            const redX = "{{ asset('public/img/cf-menu-icons/redxcircle.png') }}";
                            const warning_img =
                                "{{ asset('public/img/cf-menu-icons/yellowexclamationmark.png') }}";

                            if (selectedTestType !== 'Perf-Weight') {
                                sampleValues.forEach(function(value) {
                                    const numericValue = parseFloat(value);

                                    if (selectedTestType === 'Dimension') {
                                        let currentSample = editSampleCounter;
                                        editSampleCounter++;

                                        var result = (numericValue >= threshold.min &&
                                                numericValue <= threshold.max) ? 'pass' :
                                            'fail';

                                        $('#editSelectedSamples').append(`
                                            <div class="col-sm-20">
                                                <div class="sample-entry d-flex justify-content-between align-items-center my-2">
                                                    <div>
                                                        <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                        <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-right mr-2">
                                                            <span class="sample-value">${parseFloat(value).toFixed(3)}</span><br>
                                                            <span class="sample-number">SAMPLE #${currentSample}</span>
                                                        </div>
                                                        <div>
                                                            <div class="dropdown dropdown-3dot">
                                                                <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                                    <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-3dot">
                                                                    <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample" data-min="${threshold.min}" data-max="${threshold.max}" data-sample="${currentSample}" data-value="${parseFloat(value).toFixed(3)}" data-img="${result}" href="#"><img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                                    <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample" data-sample="${currentSample}" href="#"><img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `);
                                    } else if (selectedTestType === 'Perf-Str' &&
                                        selectedCriteria === 'Min') {
                                        editAppendSampleResult(standardType, threshold.YFS,
                                            threshold.YFGS, standardType,
                                            numericValue, threshold);

                                        // if (selectedStandard === 'YFS') {
                                        //     editAppendSampleResult('YFS', threshold.YFS,
                                        //         'YFS', numericValue, threshold);
                                        // } else if (selectedStandard === 'YFGS') {
                                        //     editAppendSampleResult('YFGS', threshold.YFGS,
                                        //         'YFGS', numericValue, threshold);
                                        // }
                                    } else if (selectedTestType === 'Perf-Str' &&
                                        selectedCriteria === 'Max') {
                                        const standardValue = standardType === 'YFS' ?
                                            threshold.YFS : threshold.YFGS;

                                        let result = (numericValue <= standardValue) ?
                                            'pass' : 'fail';

                                        let currentSample = editSampleCounter;
                                        editSampleCounter++;

                                        $('#editSelectedSamplesConclusion .standard-value')
                                            .text(standardValue + '<small>(' + sampleuom +
                                                ')</small>');
                                        $('#editSelectedSamplesConclusion #standard_value')
                                            .val(threshold.YFS + '<small>(' + sampleuom +
                                                ')</small>');
                                        $('#editSelectedSamplesConclusion .safety-value')
                                            .hide();

                                        $('#editSelectedSamples').append(`
                                    <div class="col-sm-20">
                                        <div class="sample-entry d-flex justify-content-between align-items-center my-2">
                                            <div>
                                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="text-right mr-2">
                                                    <span class="sample-value">${parseFloat(value).toFixed(2)}</span><br>
                                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                                </div>
                                                <div>
                                                    <div class="dropdown dropdown-3dot">
                                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-3dot">
                                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                               data-standard="${standardValue}"
                                                               data-sample="${currentSample}"
                                                               data-img="${result}"
                                                               data-value="${parseFloat(value).toFixed(2)}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                               data-sample="${currentSample}" href="#">
                                                               <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `);

                                        const stdDev = editCalculateStandardDeviation();
                                        $('.stdva-value').text(stdDev.roundedWhole);
                                    }

                                    addedCount++;
                                });


                            }
                            // PERF-WEIGHT HANDLING
                            else if (selectedTestType === 'Perf-Weight') {
                                $('#editSelectedSamplesConclusion .standard-value').text(threshold
                                    .absorption.toFixed(2) + '%');
                                $('#editSelectedSamplesConclusion .safety-value').text(
                                    "MAX ABSORB %");

                                const beforeInputs = $('input[name="sample_before[]"]');
                                const afterInputs = $('input[name="sample_after[]"]');
                                let pairs = [];

                                for (let i = 0; i < beforeInputs.length; i++) {
                                    const beforeVal = $(beforeInputs[i]).val().trim();
                                    const afterVal = $(afterInputs[i]).val().trim();

                                    if (
                                        beforeVal !== '' && afterVal !== '' &&
                                        parseFloat(beforeVal) !== 0 && parseFloat(afterVal) !== 0
                                    ) {
                                        pairs.push({
                                            before: parseFloat(beforeVal),
                                            after: parseFloat(afterVal)
                                        });
                                    }
                                }

                                if (pairs.length < 1 || pairs.length > 5) {
                                    var message =
                                        `Please enter between 1 and 5 valid Perf-Weight samples (Before + After).`;
                                    showCustomWarningNotification(message, "350px");
                                    return;
                                }

                                const absorptionThreshold = parseFloat(threshold.absorption);

                                pairs.forEach(function(pair) {
                                    let currentSample = editSampleCounter;
                                    editSampleCounter++;

                                    const diff = pair.after - pair.before;
                                    const rawResult = diff / pair.before;
                                    const result = parseFloat(rawResult.toFixed(4));
                                    const percentage = parseFloat((result * 100).toFixed(
                                        2));

                                    const isPass = percentage <= absorptionThreshold;

                                    $('#editSelectedSamples').append(`
                        <div class="col-sm-20">
                            <div class="sample-entry d-flex justify-content-between align-items-center my-2">
                                <div>
                                    <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${isPass ? 'inline' : 'none'};">
                                    <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${!isPass ? 'inline' : 'none'};">
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="text-right mr-2">
                                        <span class="sample-value">${percentage}%</span><br>
                                        <span class="sample-number">SAMPLE #${currentSample}</span>
                                    </div>
                                    <div>
                                        <div class="dropdown dropdown-3dot">
                                            <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                                <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-3dot">
                                                <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                                   data-sample="${currentSample}"
                                                   data-before="${pair.before}"
                                                   data-after="${pair.after}"
                                                   data-result="${result}"
                                                   data-percentage="${percentage}"
                                                   data-absorption="${absorptionThreshold}"
                                                   data-img="${isPass ? 'pass' : 'fail'}"
                                                   href="#">
                                                   <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                                <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                                   data-sample="${currentSample}" href="#">
                                                   <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);

                                    addedCount++;
                                });

                                $('input[name="sample_before[]"], input[name="sample_after[]"]')
                                    .val('');
                            }

                            var totalSamples = $('#editSelectedSamples .sample-entry').length;
                            $('#editSelectedSamplesConclusion .total-samples').text(totalSamples);

                            editUpdateFinalMinValue();
                            editUpdateFinalMaxValue();
                            editUpdateFinalAvgValue();

                            var message = `${addedCount} sample value(s) added`;
                            showCustomWarningNotification(message, "300px");

                            function editAppendSampleResult(type, thresholdValueYFS,
                                thresholdValueYFGS,
                                thresholdName,
                                numericValue, threshold) {
                                let result;
                                var thresholdValue = type == "YFS" ? thresholdValueYFS :
                                    thresholdValueYFGS;
                                if (numericValue < thresholdValue) {
                                    result = 'fail';
                                } else if (numericValue >= thresholdValue && numericValue <=
                                    (
                                        thresholdValue + threshold.safety_threshold)) {
                                    result = 'warning';
                                } else if (numericValue > thresholdValue) {
                                    result = 'pass';
                                }

                                let currentSample = editSampleCounter;
                                editSampleCounter++;

                                $('#editSelectedSamplesConclusion .standard-value').text(
                                    thresholdValue);
                                $('#editSelectedSamplesConclusion #standard_value')
                                    .val(thresholdValueYFS);
                                $('#editSelectedSamplesConclusion .safety-value').text('+' +
                                    threshold.safety_threshold);

                                $('#editSelectedSamples').append(`
                    <div class="col-sm-20">
                        <div data-yfs="${thresholdValueYFS}" data-yfgs="${thresholdValueYFGS}" data-safety-value="${threshold.safety_threshold}" class="sample-entry d-flex justify-content-between align-items-center my-2">
                            <div>
                                <img class="result-img-pass-${currentSample}" src="${greenCheck}" width="30" style="display: ${result === 'pass' ? 'inline' : 'none'};">
                                <img class="result-img-fail-${currentSample}" src="${redX}" width="30" style="display: ${result === 'fail' ? 'inline' : 'none'};">
                                <img class="result-img-warning-${currentSample}" src="${warning_img}" width="30" style="display: ${result === 'warning' ? 'inline' : 'none'};">
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-right mr-2">
                                    <span class="sample-value">${parseFloat(numericValue).toFixed(2)}</span><br>
                                    <span class="sample-number">SAMPLE #${currentSample}</span>
                                </div>
                                <div>
                                    <div class="dropdown dropdown-3dot">
                                        <a class="dropdown-toggle action-dots border-0 bg-transparent px-0" href="#" role="button" data-toggle="dropdown">
                                            <img src="public/img/cf-menu-icons/3dots.png" width="10">
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-3dot">
                                            <a class="dropdown-item dropdown-item-3dot edit-dot-icon edit-pen-sample"
                                               data-standard="${thresholdValue}"
                                               data-safety="${threshold.safety_threshold}"
                                               data-sample="${currentSample}"
                                               data-img="${result}"
                                               data-value="${parseFloat(numericValue).toFixed(2)}" href="#">
                                               <img src="public/img/cf-menu-icons/3dot-edit.png"> Edit</a>
                                            <a class="dropdown-item dropdown-item-3dot delete-dot-icon del-pen-sample"
                                               data-sample="${currentSample}" href="#">
                                               <img src="public/img/cf-menu-icons/3dot-delete.png"> Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                                const stdDev = editCalculateStandardDeviation();
                                $('.stdva-value').text(stdDev.roundedDecimal);
                            }
                        },
                        error: function(xhr) {
                            let msg = "Unknown error";
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                msg = xhr.responseJSON.error;
                            }
                            alert("Error: " + msg);
                        }
                    });
                });

                function editCalculateStandardDeviation() {
                    const values = [];

                    $('#editSelectedSamples .sample-value').each(function() {
                        const valText = $(this).text().replace('%', '');
                        const val = parseFloat(valText);
                        if (!isNaN(val)) {
                            values.push(val);
                        }
                    });

                    if (values.length < 2) {
                        return {
                            roundedDecimal: 0,
                            roundedWhole: 0
                        };
                    }

                    const total = values.reduce((sum, val) => sum + val, 0);
                    const avg = total / values.length;

                    let sumOfSquaredDiffs = 0;
                    values.forEach(val => {
                        const diff = val - avg;
                        sumOfSquaredDiffs += diff * diff;
                    });

                    const variance = sumOfSquaredDiffs / (values.length - 1);
                    const stdDev = Math.sqrt(variance);

                    return {
                        roundedDecimal: parseFloat(stdDev.toFixed(2)),
                        roundedWhole: Math.round(stdDev)
                    };
                }

                function editUpdateFinalMinValue() {
                    const testType = $('#editselectedTestType').val();
                    const criteria = $('#editselectedCriteria').val();

                    let min = null;

                    $('#editSelectedSamples .sample-value').each(function() {
                        const valText = $(this).text().replace('%', '');
                        const val = parseFloat(valText);
                        if (!isNaN(val)) {
                            if (min === null || val < min) min = val;
                        }
                    });

                    $('.final-min-value').text(min !== null ? min : '0');
                    $('.final-result-min-img-pass, .final-result-min-img-fail, .final-result-min-img-warning').hide();
                    if (min === null) return;

                    const $first = $('.edit-pen-sample').first();

                    if (testType === 'Dimension') {
                        $('.final-min-value').text(min !== null ? parseFloat(min).toFixed(3) : '0.000');
                        const thresholdMin = parseFloat($first.attr('data-min'));
                        if (!isNaN(thresholdMin)) {
                            min >= thresholdMin ?
                                $('.final-result-min-img-pass').show() :
                                $('.final-result-min-img-fail').show();
                            min >= thresholdMin ?
                                $('.final-min-img').val('pass') :
                                $('.final-min-img').val('fail');
                        }
                    } else if (testType === 'Perf-Str') {
                        $('.final-min-value').text(min !== null ? parseFloat(min).toFixed(2) : '0.00');
                        const standard = parseFloat($first.attr('data-standard'));
                        const safety = parseFloat($first.attr('data-safety'));
                        if (!isNaN(standard)) {
                            if (criteria === 'Min') {
                                if (min < standard) {
                                    $('.final-result-min-img-fail').show();
                                    $('.final-min-img').val('fail');
                                } else if (min >= standard && min <= (standard + safety)) {
                                    $('.final-result-min-img-warning').show();
                                    $('.final-min-img').val('warning');
                                } else {
                                    $('.final-result-min-img-pass').show();
                                    $('.final-min-img').val('pass');
                                }
                            } else if (criteria === 'Max') {
                                min <= standard ?
                                    $('.final-result-min-img-pass').show() :
                                    $('.final-result-min-img-fail').show();
                                min <= standard ?
                                    $('.final-min-img').val('pass') :
                                    $('.final-min-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Weight') {
                        $('.final-min-value').text(min !== null ? min + '%' : '0');
                        const thresholdMin = parseFloat($first.attr('data-absorption'));
                        if (!isNaN(thresholdMin)) {
                            min <= thresholdMin ?
                                $('.final-result-min-img-pass').show() :
                                $('.final-result-min-img-fail').show();
                            min <= thresholdMin ?
                                $('.final-min-img').val('pass') :
                                $('.final-min-img').val('fail');
                        }
                    }
                }

                function editUpdateFinalMaxValue() {
                    const testType = $('#editselectedTestType').val();
                    const criteria = $('#editselectedCriteria').val();

                    let max = null;

                    $('#editSelectedSamples .sample-value').each(function() {
                        const valText = $(this).text().replace('%', '');
                        const val = parseFloat(valText);
                        if (!isNaN(val)) {
                            if (max === null || val > max) max = val;
                        }
                    });

                    $('.final-max-value').text(max !== null ? max : '0');
                    $('.final-result-max-img-pass, .final-result-max-img-fail, .final-result-max-img-warning').hide();
                    if (max === null) return;

                    const $first = $('.edit-pen-sample').first();

                    if (testType === 'Dimension') {
                        $('.final-max-value').text(max !== null ? parseFloat(max).toFixed(3) : '0.000');
                        const thresholdMax = parseFloat($first.attr('data-max'));
                        if (!isNaN(thresholdMax)) {
                            max <= thresholdMax ?
                                $('.final-result-max-img-pass').show() :
                                $('.final-result-max-img-fail').show();
                            max <= thresholdMax ?
                                $('.final-max-img').val('pass') :
                                $('.final-max-img').val('fail');
                        }
                    } else if (testType === 'Perf-Str') {
                        $('.final-max-value').text(max !== null ? parseFloat(max).toFixed(2) : '0.00');
                        const standard = parseFloat($first.attr('data-standard'));
                        const safety = parseFloat($first.attr('data-safety'));
                        if (!isNaN(standard)) {
                            if (criteria === 'Min') {
                                if (max < standard) {
                                    $('.final-result-max-img-fail').show();
                                    $('.final-max-img').val('fail');
                                } else if (max >= standard && max <= (standard + safety)) {
                                    $('.final-result-max-img-warning').show();
                                    $('.final-max-img').val('warning');
                                } else {
                                    $('.final-result-max-img-pass').show();
                                    $('.final-max-img').val('pass');
                                }
                            } else if (criteria === 'Max') {
                                max <= standard ?
                                    $('.final-result-max-img-pass').show() :
                                    $('.final-result-max-img-fail').show();
                                max <= standard ?
                                    $('.final-max-img').val('pass') :
                                    $('.final-max-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Weight') {
                        $('.final-max-value').text(max !== null ? max + '%' : '0');
                        const thresholdMin = parseFloat($first.attr('data-absorption'));
                        if (!isNaN(thresholdMin)) {
                            max <= thresholdMin ?
                                $('.final-result-max-img-pass').show() :
                                $('.final-result-max-img-fail').show();
                            max <= thresholdMin ?
                                $('.final-max-img').val('pass') :
                                $('.final-max-img').val('fail');
                        }
                    }
                }

                function editUpdateFinalAvgValue() {
                    const testType = $('#editselectedTestType').val();
                    const criteria = $('#editselectedCriteria').val();

                    let total = 0,
                        count = 0,
                        min = null,
                        max = null;

                    $('#editSelectedSamples .sample-value').each(function() {
                        const valText = $(this).text().replace('%', '');
                        const val = parseFloat(valText);
                        if (!isNaN(val)) {
                            total += val;
                            count++;
                            if (min === null || val < min) min = val;
                            if (max === null || val > max) max = val;
                        }
                    });

                    $('.final-result-avg-img-pass, .final-result-avg-img-fail, .final-result-avg-img-warning').hide();

                    if (count === 0) {
                        $('.final-avg-value').text('0');
                        $('.avg-minus, .avg-plus').text('0');
                        return;
                    }

                    const avg = total / count;
                    var uom = $('#editselectedUOM').val()
                    $('.final-avg-value').text(avg.toFixed(2));

                    $('.avg-minus').text((avg - min).toFixed(2));
                    $('.avg-plus').text((max - avg).toFixed(2) + ' (' + uom + ')');

                    const $first = $('.edit-pen-sample').first();

                    if (testType === 'Dimension') {
                        $('.final-avg-value').text(avg !== null ? parseFloat(avg).toFixed(3) : '0.000');

                        $('.final-avg-value1').text(avg !== null ? parseFloat(avg).toFixed(3) : '0.000');

                        const minT = parseFloat($first.attr('data-min'));
                        const maxT = parseFloat($first.attr('data-max'));
                        if (!isNaN(minT) && !isNaN(maxT)) {
                            if (avg >= minT && avg <= maxT) {
                                $('.final-result-avg-img-pass').show();
                                $('.final-avg-img').val('pass');
                            } else {
                                $('.final-result-avg-img-fail').show();
                                $('.final-avg-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Str') {
                        $('.final-avg-value').text(avg !== null ? parseFloat(avg).toFixed(2) : '0.00');
                        const standard = parseFloat($first.attr('data-standard'));
                        const safety = parseFloat($first.attr('data-safety'));
                        if (!isNaN(standard)) {
                            if (criteria === 'Min') {
                                if (avg < standard) {
                                    $('.final-result-avg-img-fail').show();
                                    $('.final-avg-img').val('fail');
                                } else if (avg >= standard && avg <= (standard + safety)) {
                                    $('.final-result-avg-img-warning').show();
                                    $('.final-avg-img').val('warning');
                                } else {
                                    $('.final-result-avg-img-pass').show();
                                    $('.final-avg-img').val('pass');
                                }
                            } else if (criteria === 'Max') {
                                avg <= standard ?
                                    $('.final-result-avg-img-pass').show() :
                                    $('.final-result-avg-img-fail').show();
                                avg <= standard ?
                                    $('.final-avg-img').val('pass') :
                                    $('.final-avg-img').val('fail');
                            }
                        }
                    } else if (testType === 'Perf-Weight') {
                        $('.final-avg-value').text(avg.toFixed(2) + '%');
                        const thresholdMin = parseFloat($first.attr('data-absorption'));
                        if (!isNaN(thresholdMin)) {
                            avg <= thresholdMin ?
                                $('.final-result-avg-img-pass').show() :
                                $('.final-result-avg-img-fail').show();
                            avg <= thresholdMin ?
                                $('.final-avg-img').val('pass') :
                                $('.final-avg-img').val('fail');
                        }
                    }
                }

                // Edit sample functionality
                $('#editSelectedSamples').on('click', '.edit-pen-sample', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var selectedUOM = $('#editselectedUOM').val();
                    $('#editEditSampleValueModal .edit-pen-uom').val(selectedUOM);

                    var sample = $(this).data('sample');
                    var value = $(this).data('value');

                    $('#editEditSampleValueModal #edit-pen-sample-value').val(value);
                    $('.edit-sample-heading').text(sample);
                    $('.edit-label').text('Result');
                    $('.edit-pen-perf-weight').hide();

                    // Show/hide fields based on test type
                    var selectedTestType = $('#editselectedTestType').val();
                    var criteria = $('#editselectedCriteria').val();

                    if (selectedTestType === 'Dimension') {
                        var min = $(this).data('min');
                        var max = $(this).data('max');
                        var result = $(this).data('img');

                        $('.min-result').val(min);
                        $('.max-result').val(max);
                        $('.img-result').val(result);
                    } else if (selectedTestType === 'Perf-Str') {
                        var standard = $(this).data('standard');
                        var safety = $(this).data('safety');
                        var result = $(this).data('img');

                        $('.standard-result').val(standard);
                        $('.safety-result').val(safety);
                        $('.img-result').val(result);
                    } else if (selectedTestType === 'Perf-Weight') {
                        $('.edit-label').text('Before Bosubi');
                        $('.edit-pen-perf-weight').show();
                        var result = $(this).data('img');

                        var before = $(this).data('before');
                        var after = $(this).data('after');
                        var percentage = $(this).data('percentage');
                        var absorption = $(this).data('absorption');

                        $('.img-result').val(result);

                        $('.before-result').val(before);
                        $('.after-result').val(after);
                        $('.percentage-result').val(percentage);
                        $('.absorption-result').val(absorption);
                        $('#editEditSampleValueModal #edit-pen-sample-value').val(before);
                        $('#editEditSampleValueModal #edit-pen-sample-value-after').val(after);
                        $('#editEditSampleValueModal #edit-pen-sample-percent').val(percentage + '%');
                    }

                    $('#editEditSampleValueModal').modal('show');
                });

                $('#editsaveEditedSample').on('click', function() {
                    var newValue = $('#editEditSampleValueModal #edit-pen-sample-value').val().trim();
                    const sampleName = $('#editEditSampleValueModal .edit-sample-heading').text().trim();

                    if (!newValue) {
                        alert('Please enter a sample value.');
                        return;
                    }

                    const numericValue = parseFloat(newValue);
                    const selectedTestType = $('#editselectedTestType').val();
                    const criteria = $('#editselectedCriteria').val();

                    let result = 'fail';

                    const $sampleBlock = $('#edit-sample-test-modal .sample-number').filter(function() {
                        return $(this).text().trim() === 'SAMPLE #' + sampleName;
                    }).closest('#edit-sample-test-modal .sample-entry');

                    if (!$sampleBlock.length) {
                        alert('Could not find the sample to update.');
                        return;
                    }

                    if (selectedTestType === 'Dimension') {
                        newValue = parseFloat(newValue).toFixed(3);
                        $sampleBlock.find('.sample-value').text(newValue);
                        $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value',
                            newValue);

                        const min = parseFloat($('.min-result').val());
                        const max = parseFloat($('.max-result').val());

                        result = (numericValue >= min && numericValue <= max) ? 'pass' : 'fail';

                        $sampleBlock.find('.edit-pen-sample')
                            .attr('data-min', min)
                            .attr('data-max', max)
                            .attr('data-img', result)
                            .data('min', min)
                            .data('max', max)
                            .data('img', result);
                    } else if (selectedTestType === 'Perf-Str') {
                        newValue = parseFloat(newValue).toFixed(2);
                        $sampleBlock.find('.sample-value').text(newValue);
                        $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value',
                            newValue);

                        const standard = parseFloat($('.standard-result').val());
                        const safety = parseFloat($('.safety-result').val());

                        if (criteria === 'Min') {
                            if (numericValue < standard) {
                                result = 'fail';
                            } else if (numericValue >= standard && numericValue <= (standard + safety)) {
                                result = 'warning';
                            } else {
                                result = 'pass';
                            }
                            var stdDev = editCalculateStandardDeviation();
                            $('.stdva-value').text(stdDev.roundedWhole);
                        } else if (criteria === 'Max') {
                            result = numericValue <= standard ? 'pass' : 'fail';

                            var stdDev = editCalculateStandardDeviation();
                            $('.stdva-value').text(stdDev.roundedDecimal);
                        }

                        $sampleBlock.find('.edit-pen-sample')
                            .attr('data-standard', standard)
                            .attr('data-safety', safety)
                            .attr('data-img', result)
                            .data('standard', standard)
                            .data('safety', safety)
                            .data('img', result);
                    } else if (selectedTestType === 'Perf-Weight') {
                        const before = parseFloat($('.before-result').val());
                        const after = parseFloat($('.after-result').val());
                        const percentage = parseFloat($('.percentage-result').val());
                        const absorption = parseFloat($('.absorption-result').val());

                        newValue = percentage + '%';

                        result = (percentage <= absorption) ? 'pass' : 'fail';

                        $sampleBlock.find('.edit-pen-sample')
                            .attr('data-before', before)
                            .attr('data-after', after)
                            .attr('data-percentage', percentage)
                            .attr('data-img', result)
                            .data('before', before)
                            .data('after', after)
                            .data('percentage', percentage)
                            .data('img', result);

                        $sampleBlock.find('.sample-value').text(newValue);
                        $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value',
                            newValue);
                    }

                    // Toggle icons
                    $sampleBlock.find('[class^="result-img-"]').hide();
                    $sampleBlock.find('.result-img-' + result + '-' + sampleName).show();

                    // $sampleBlock.find('.sample-value').text(newValue);
                    // $sampleBlock.find('.edit-pen-sample').attr('data-value', newValue).data('value', newValue);

                    editUpdateFinalMinValue();
                    editUpdateFinalMaxValue();
                    editUpdateFinalAvgValue();

                    $('#editEditSampleValueModal').modal('hide');
                });

                function editcalculatePercentageChange() {
                    let before = parseFloat($('#editEditSampleValueModal #edit-pen-sample-value').val());
                    let after = parseFloat($('#editEditSampleValueModal #edit-pen-sample-value-after').val());

                    if (!isNaN(before) && before !== 0 && !isNaN(after)) {
                        let result = ((after - before) / before);
                        let rounded = (result * 100).toFixed(2) + '%';

                        $('#editEditSampleValueModal #edit-pen-sample-percent').val(rounded);
                        $('.before-result').val(before);
                        $('.after-result').val(after);
                        $('.percentage-result').val(rounded);
                    } else {
                        $('#editEditSampleValueModal #edit-pen-sample-percent').val('');
                        $('.percentage-result').val('');
                    }
                }

                // Run calculation on focus out
                $('#editEditSampleValueModal #edit-pen-sample-value, #editEditSampleValueModal #edit-pen-sample-value-after')
                    .on('focusout', editcalculatePercentageChange);

                $('#editSelectedSamples').on('click', '.del-pen-sample', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const sampleId = $(this).data('sample');

                    const $sampleBlock = $('#editSelectedSamples .sample-number').filter(function() {
                        return $(this).text().trim() === 'SAMPLE #' + sampleId;
                    }).closest('.col-sm-20');

                    if ($sampleBlock.length) {
                        // ✅ Save before removing
                        lastRemovedItem = {
                            html: $sampleBlock.prop('outerHTML'),
                            index: $sampleBlock.index()
                        };

                        $sampleBlock.find('[data-toggle="tooltip"]').tooltip('dispose');

                        $sampleBlock.remove();
                    }

                    // Renumber samples
                    let newSampleCounter = 1;
                    $('#editSelectedSamples .sample-entry').each(function() {
                        const $entry = $(this);
                        const $numberSpan = $entry.find('.sample-number');
                        const newSampleNumber = newSampleCounter++;

                        $numberSpan.text('SAMPLE #' + newSampleNumber);
                        $entry.find('[class^="result-img-pass-"]').attr('class', 'result-img-pass-' +
                            newSampleNumber);
                        $entry.find('[class^="result-img-fail-"]').attr('class', 'result-img-fail-' +
                            newSampleNumber);
                        $entry.find('.edit-pen-sample, .del-pen-sample')
                            .attr('data-sample', newSampleNumber)
                            .data('sample', newSampleNumber);
                    });

                    sampleCounter = $('#editSelectedSamples .sample-entry').length + 1;

                    var totalSamples = $('#editSelectedSamples .sample-entry').length;
                    $('#editSelectedSamplesConclusion .total-samples').text(totalSamples);
                    editUpdateFinalMinValue();
                    editUpdateFinalMaxValue();
                    editUpdateFinalAvgValue();

                    var selectedTestType = $('#editselectedTestType').val();
                    var criteria = $('#editselectedCriteria').val();
                    if (selectedTestType === 'Perf-Str') {

                        if (criteria === 'Min') {
                            var stdDev = editCalculateStandardDeviation();
                            $('.stdva-value').text(stdDev.roundedWhole);
                        } else if (criteria === 'Max') {
                            var stdDev = editCalculateStandardDeviation();
                            $('.stdva-value').text(stdDev.roundedDecimal);
                        }
                    }


                    var message =
                        `Sample value deleted <a href="javascript:;" class="btn-notify edit-undo-remove-sample ml-4">Undo</a>`;
                    showCustomWarningNotification(message, "300px");
                });


                // Handle undo click from notification
                $(document).on('click', '.edit-undo-remove-sample', function() {
                    if (lastRemovedItem) {
                        const $itemsContainer = $('#editSelectedSamples');
                        const itemCount = $itemsContainer.children().length;

                        // Restore item at original position
                        const $restoredItem = $(lastRemovedItem.html);
                        if (lastRemovedItem.index >= itemCount) {
                            $itemsContainer.append($restoredItem);
                        } else {
                            $itemsContainer.children().eq(lastRemovedItem.index).before($restoredItem);
                        }

                        // Clear lastRemovedItem
                        lastRemovedItem = null;

                        // ✅ Renumber all samples and update sampleCounter
                        editrenumberSamples();

                        // ✅ Close any open 3-dot dropdowns
                        // Close any open 3-dot dropdowns manually
                        $('.dropdown-menu.show').removeClass('show').removeAttr('style');
                        $('.dropdown-toggle.show').removeClass('show').attr('aria-expanded', 'false');

                        var totalSamples = $('#editSelectedSamples .sample-entry').length;
                        $('#editSelectedSamplesConclusion .total-samples').text(totalSamples);
                        editUpdateFinalMinValue();
                        editUpdateFinalMaxValue();
                        editUpdateFinalAvgValue();

                        var selectedTestType = $('#editselectedTestType').val();
                        var criteria = $('#editselectedCriteria').val();
                        if (selectedTestType === 'Perf-Str') {

                            if (criteria === 'Min') {
                                var stdDev = editCalculateStandardDeviation();
                                $('.stdva-value').text(stdDev.roundedWhole);
                            } else if (criteria === 'Max') {
                                var stdDev = editCalculateStandardDeviation();
                                $('.stdva-value').text(stdDev.roundedDecimal);
                            }
                        }

                        var message = `Sample value restored`;
                        showCustomWarningNotification(message, "300px");

                    }
                });

                function editrenumberSamples() {
                    let count = 1;

                    $('#editSelectedSamples .sample-entry').each(function() {
                        const $entry = $(this);

                        // Update text like "SAMPLE #1"
                        $entry.find('.sample-number').text('SAMPLE #' + count);

                        // Update result image classes
                        $entry.find('[class^="result-img-pass-"]').attr('class', 'result-img-pass-' + count);
                        $entry.find('[class^="result-img-fail-"]').attr('class', 'result-img-fail-' + count);

                        // Update data-sample attributes
                        $entry.find('.edit-pen-sample, .del-pen-sample')
                            .attr('data-sample', count)
                            .data('sample', count);

                        count++;
                    });

                    // ✅ Update global counter to next number
                    sampleCounter = count;
                }




                // Correct way to use the asset helper in Blade with JavaScript:
                var warningIcon = "{{ asset('public/img/warning-yellow.png') }}";


                // Function to load category data
                function loadCategoryData(categoryId) {
                    $.ajax({
                        url: '{{ url('get-sample-test') }}',
                        method: 'POST',
                        data: {
                            id: categoryId
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#edit_sample_test_id').val(response.data.id);
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

                $(document).on('click', '.edit-icon, .edit-dot-icon', function() {
                    const sampleTestId = $(this).attr('data-item-id');
                    loadSampleTestForEdit(sampleTestId);
                    // loadCategoryData(categoryId);

                    // Hide all fetched fields
                    $('.step-2').show();
                    $('.fetched-items').hide();
                    $('.step-1').hide();

                    $('#edit-sample-test-modal input[name="bosubi"]').prop('disabled', false); // enable others

                    // Enable qc-calendar again
                    $('.qc-calendar').css({
                        'pointer-events': 'auto',
                        'opacity': '1',
                        'cursor': 'pointer'
                    });

                    // Show modal
                    $('#edit-sample-test-modal').modal('show');
                });


                // Add selected item to the list on button click or Enter key
                $('#edit-addItem').on('click', edit_addSelectedItem);

                // Refactored add item function
                function edit_addSelectedItem() {
                    var selectedTestType = $('#edit_selectedTestType').val().trim();
                    var selectedCriteria = $('#editselectedCriteria').val().trim();
                    var minVal = $('#edit-min').val().trim();
                    var maxVal = $('#edit-max').val().trim();
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

                    var avg = (!isNaN(min) && !isNaN(max)) ? parseFloat(((min + max) / 2).toFixed(3)) : '';

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
                $('#form-edit-sample-test').on('submit', function(e) {
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
                            resetFormAndReload(form, '#edit-sample-test-modal',
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
                $('#edit-sample-test-modal').on('keypress', function(e) {
                    // Check if Enter key was pressed (key code 13)
                    if (e.which === 13) {
                        // Check if there are selected items
                        const hasSelectedItems = $('#editSelectedItems .selected-item').length > 0;

                        // Only submit if there are selected items and we're not in a textarea or input[type="text"]
                        const $target = $(e.target);
                        if (hasSelectedItems && !$target.is('textarea') && !$target.is('input[type="text"]')) {
                            e.preventDefault();
                            $('#form-edit-sample-test').submit();
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

                // });



                // $(function() {



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
                        var $select = $('#testNameSelect');
                        var $selectFilter = $('#filter_test_name');

                        // Update the options
                        $select.html(response.options);
                        $selectFilter.html(response.options);

                        // Reset the value to the empty one
                        $select.val('').trigger('change');
                        $selectFilter.val('').trigger('change');

                        // Re-initialize or refresh Select2
                        $select.select2({
                            placeholder: "Select Test Name",
                            allowClear: true,
                            minimumResultsForSearch: 0
                        });
                        $selectFilter.select2({
                            placeholder: "Select Test Name",
                            allowClear: true,
                            minimumResultsForSearch: 0,
                            dropdownParent: $('#filterForm')
                        });
                    }).fail(function(xhr) {
                        console.error(xhr.responseText);
                    });
                }

                // Initial load: fetch all items
                fetchItems();
                // Define fetchItems function
                function fetchEditItems(query = '') {
                    $.post('{{ url('/fetch-test-names') }}', {
                        query: query,
                    }, function(response) {
                        var $select = $('#edittestNameSelect');

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
                fetchEditItems();


                // Track the latest query and active AJAX request

                $('#WorkOrderSelect').select2({
                    placeholder: "Select Workorder #",
                    allowClear: true,
                    ajax: {
                        url: "{{ route('fetch_workorders') }}",
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
                                        text: item.workorder_no,
                                        data_id: item.id,
                                        workorder: item.workorder_no,
                                        itemcategory: item.item_category,
                                        itemcodeid: item.item_code_id,
                                        itemcode: item.item_code,
                                        color: item.itemcode_color_id,
                                        length: item.length,
                                        description: item.description
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        return $('<span>' + data.text + '</span>');
                    },
                    templateSelection: function(data) {
                        return data.text || data.workorder_no;
                    }
                });
                $('#workOrderReportModal #wo_no').select2({
                    placeholder: "Select Workorder #",
                    allowClear: true,
                    ajax: {
                        url: "{{ route('fetch_workorders') }}",
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
                                        text: item.workorder_no,
                                        data_id: item.id,
                                        workorder: item.workorder_no,
                                        itemcategory: item.item_category,
                                        itemcodeid: item.item_code_id,
                                        itemcode: item.item_code,
                                        color: item.itemcode_color_id,
                                        length: item.length,
                                        description: item.description
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        return $('<span>' + data.text + '</span>');
                    },
                    templateSelection: function(data) {
                        return data.text || data.workorder_no;
                    }
                });

                // // Event handler for when a selection is made
                // $('#WorkOrderSelect').on('select2:select', function(e) {
                //     var data = e.params.data;
                //     var detailsHtml = `
        //         <p><strong>ID:</strong> ${data.data_id || 'N/A'}</p>
        //         <p><strong>Workorder #:</strong> ${data.workorder || 'N/A'}</p>
        //         <p><strong>Item Category:</strong> ${data.itemcategory || 'N/A'}</p>
        //         <p><strong>Item Code:</strong> ${data.itemcode || 'N/A'}</p>
        //         <p><strong>Color ID:</strong> ${data.color || 'N/A'}</p>
        //         <p><strong>Length:</strong> ${data.length || 'N/A'}</p>
        //         <p><strong>Description:</strong> ${data.description || 'N/A'}</p>
        //     `;
                //     $('#workorder-details').html(detailsHtml).show();
                // });

                // // Clear details when selection is cleared
                // $('#WorkOrderSelect').on('select2:clear', function() {
                //     $('#workorder-details').html('').hide();
                // });



                //   // Define fetchItems function
                // function fetchWorkorders(query = '') {
                //   $.post('{{ url('/fetch_workorders') }}', {
                //     query: query,
                //   }, function (response) {
                //     var $select = $('#WorkOrderSelect');

                //     // Update the options
                //     $select.html(response.options);

                //     // Reset the value to the empty one
                //     $select.val('').trigger('change');

                //     // Re-initialize or refresh Select2
                //     $select.select2({
                //       placeholder: "Select Workorder #",
                //       allowClear: true,
                //       minimumResultsForSearch: 0
                //     });
                //   }).fail(function (xhr) {
                //     console.error(xhr.responseText);
                //   });
                // }

                //   // Initial load: fetch all items
                //   fetchWorkorders();

                //   // Define fetchItems function
                // function fetchEditWorkorders(query = '') {
                //   $.post('{{ url('/fetch_workorders') }}', {
                //     query: query,
                //   }, function (response) {
                //     var $select = $('#editWorkOrderSelect');

                //     // Update the options
                //     $select.html(response.options);

                //     // Reset the value to the empty one
                //     $select.val('').trigger('change');

                //     // Re-initialize or refresh Select2
                //     $select.select2({
                //       placeholder: "Select Workorder #",
                //       allowClear: true,
                //       minimumResultsForSearch: 0
                //     });
                //   }).fail(function (xhr) {
                //     console.error(xhr.responseText);
                //   });
                // }

                //   // Initial load: fetch all items
                //   fetchEditWorkorders();

                $('#editWorkOrderSelect').select2({
                    placeholder: "Select Workorder #",
                    allowClear: true,
                    minimumInputLength: 0
                }).on('select2:select', function(e) {
                    const selectedData = e.params.data;
                });

                // Modified fetchEditWorkorders with query tracking
                let editlatestQuery = null;
                let editactiveRequest = null;

                async function fetchEditWorkorders(query = '', limit = 10) {
                    // Store the current query as the latest
                    editlatestQuery = query;

                    // Cancel any ongoing request
                    if (editactiveRequest) {
                        editactiveRequest.abort();
                    }

                    // Create new AJAX request
                    editactiveRequest = $.ajax({
                        url: '{{ url('/fetch_workorders_edit') }}',
                        method: 'GET',
                        data: {
                            query: query,
                            limit: limit
                        },
                        success: function(response) {
                            // Only update if this response matches the latest query
                            // if (query === editlatestQuery) {
                            //     var $select = $('#editWorkOrderSelect');
                            //     const previouslySelected = $select.val(); // Store current selection

                            //     // Update options without reinitializing Select2
                            //     $select.html(response.options);
                            //     console.log(response);

                            //     // Restore previous selection if it exists in the new options
                            //     if (previouslySelected && $select.find(
                            //             `option[value="${previouslySelected}"]`).length) {
                            //         $select.val(previouslySelected);
                            //     } else {
                            //         $select.val(
                            //             null); // Clear selection if previous value is no longer valid
                            //     }

                            //     // Trigger change to update Select2's internal state
                            //     $select.trigger('change');
                            // }
                            if (query === editlatestQuery) {
                                var $select = $('#editWorkOrderSelect');
                                const previouslySelected = $select.val(); // Store current selection

                                // Clear old options
                                $select.empty();

                                // Append new options from response (assuming response is JSON array)
                                $.each(response, function(index, item) {
                                    let option = $('<option>', {
                                        value: item.id,
                                        text: item.workorder_no,
                                        'data-id': item.id,
                                        'data-workorder': item.workorder_no,
                                        'data-itemcategory': item.item_category,
                                        'data-itemcodeid': item.item_code_id,
                                        'data-itemcode': item.item_code,
                                        'data-color': item.itemcode_color_id,
                                        'data-length': item.length,
                                        'data-description': item.description
                                    });

                                    $select.append(option);
                                });

                                // Restore previous selection if it exists in the new options
                                if (previouslySelected && $select.find(
                                        `option[value="${previouslySelected}"]`).length) {
                                    $select.val(previouslySelected).trigger('change');
                                } else {
                                    // $select.val(null).trigger('change'); // Clear if invalid
                                }
                            }
                        },
                        error: function(xhr) {
                            if (xhr.statusText !== 'abort') {
                                console.error(xhr.responseText);
                            }
                        },
                        complete: function() {
                            // Clear the active request
                            editactiveRequest = null;
                        }
                    });
                }

                // Debounce search input
                let editdebounceTimer;
                $(document).on('select2:open', '#editWorkOrderSelect', function() {
                    const $search = $('.select2-container--open .select2-search__field');

                    // Remove previous input event listeners to prevent duplicates
                    // $search.off('input.select2-search').on('input.select2-search', function() {
                    //     clearTimeout(editdebounceTimer);
                    //     editdebounceTimer = setTimeout(() => {
                    //         const term = $(this).val().trim();
                    //         await fetchEditWorkorders(term, term ? 0 : 10);
                    //     }, 300);
                    // });
                    $search.off('input.select2-search').on('input.select2-search', function() {
                        clearTimeout(editdebounceTimer);

                        editdebounceTimer = setTimeout(async () => {
                            const term = $(this).val().trim();
                            await fetchEditWorkorders(term, term ? 50 :
                                10); // for example: 50 when searching
                        }, 300);
                    });

                });

                // Handle select2:select to ensure selection persists
                $('#editWorkOrderSelect').on('select2:select', function(e) {
                    const selectedValue = e.params.data.id;
                    $(this).val(selectedValue).trigger('change');
                });
                // Initial load
                fetchEditWorkorders('', 10);
                fetchUser();

                // Define fetchItems function
                function fetchUser(query = '') {
                    $.post('{{ url('/fetch-users') }}', {
                        query: query,
                    }, function(response) {

                        var $select = $('#filter_user');

                        // Update the options
                        $select.html(response.options);

                        // Reset the value to the empty one
                        $select.val('').trigger('change');

                        // Re-initialize or refresh Select2
                        $select.select2({
                            placeholder: "Select User",
                            allowClear: true,
                            minimumResultsForSearch: 0,
                            dropdownParent: $('#filterForm')
                        });
                    }).fail(function(xhr) {
                        console.error(xhr.responseText);
                    });
                }
                // Define fetchItems function
                function fetchAssets(query = '') {
                    $.post('{{ url('/fetch-assets') }}', {
                        query: query,
                    }, function(response) {
                        var $select = $('#AssetSelect');
                        var $filterselect = $('#filter_asset_no');

                        // Update the options
                        $select.html(response.options);
                        $filterselect.html(response.options);

                        // Reset the value to the empty one
                        $select.val('').trigger('change');
                        $filterselect.val('').trigger('change');

                        // Re-initialize or refresh Select2
                        $select.select2({
                            placeholder: "Select Asset #",
                            allowClear: true,
                            minimumResultsForSearch: 0
                        });
                        $filterselect.select2({
                            placeholder: "Select Asset #",
                            allowClear: true,
                            minimumResultsForSearch: 0,
                            dropdownParent: $('#filterForm')
                        });
                    }).fail(function(xhr) {
                        console.error(xhr.responseText);
                    });
                }

                // Initial load: fetch all items
                fetchAssets();

                // Define fetchItems function
                function fetchEditAssets(query = '') {
                    $.post('{{ url('/fetch-assets') }}', {
                        query: query,
                    }, function(response) {
                        var $select = $('#editAssetSelect');

                        // Update the options
                        $select.html(response.options);

                        // Reset the value to the empty one
                        $select.val('').trigger('change');

                        // Re-initialize or refresh Select2
                        $select.select2({
                            placeholder: "Select Asset #",
                            allowClear: true,
                            minimumResultsForSearch: 0
                        });
                    }).fail(function(xhr) {
                        console.error(xhr.responseText);
                    });
                }

                // Initial load: fetch all items
                fetchEditAssets();



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


                $(document).on('click', '.qc-calendar', function() {
                    $('#date-modal').modal('show');
                });
                $('#date-modal, #test-type-modal, #workorder-modal, #EditSampleValueModal, #editEditSampleValueModal, #historical-modal')
                    .modal({
                        backdrop: false,
                        show: false
                    });
                $(document).on('hidden.bs.modal', function() {
                    if ($('.modal.show').length) {
                        $('body').addClass('modal-open');
                    }
                });

                $('.js-datepicker').datepicker({
                    format: 'dd-M-yyyy',
                    autoclose: true,
                    todayHighlight: true
                }).datepicker('setDate', new Date()); // Set today's date as default

                // Handle Save Button Click
                $('#save-date').on('click', function() {
                    var selectedDate = $('.sample-date').val();

                    if (selectedDate) {
                        $('.selected-date').text(selectedDate);
                        $('[name=selected-date]').val(selectedDate);
                        $('#date-modal').modal('hide'); // Close the modal
                    }
                });

                $(document).on('click', '.insert_sample_test', function() {
                    // Hide all fetched fields
                    $('.step-2').hide();
                    $('.fetched-items').hide();
                    $('.step-1').show();
                    $('.test-type-input').hide();
                    $('.perf-str-input').hide();

                    var $modal = $('#insert-sample-test-modal #SelectedSamplesConclusion');


                    $('#next-step-btn').prop('disabled', false).html(`Continue`);

                    $modal.find('.test-standard').text('');
                    $modal.find('.sample-uom').text('uom');
                    $modal.find(
                        '.standard-value, .safety-value, .final-min-value, .final-avg-value, .final-max-value, .stdva-value, .avg-minus, .avg-plus'
                    ).text(0);
                    $modal.find('[class^="final-result-"]').hide();
                    // Enable qc-calendar again
                    $('.qc-calendar').css({
                        'pointer-events': 'auto',
                        'opacity': '1',
                        'cursor': 'pointer'
                    });

                    $('#SelectedSamples').empty();
                    $('#SelectedSamplesConclusion .total-samples').text('0');

                    $('#insert-sample-test-modal').modal('show');
                });

                $(document).on('click', '.qc-step-2-header.test-name', function() {
                    const isEdit = $(this).closest('#edit-sample-test-modal').length > 0;

                    const prefix = isEdit ? '#edit' : '#';
                    const testNameText = $(prefix + 'testNameSelect option:selected').text();
                    const selectedTestType = $(prefix + 'selectedTestType').val();
                    const selectedCriteria = $(prefix + 'selectedCriteria').val();
                    const selectedStandard = $(prefix + 'selectedStandard').val();
                    const selectedUOM = $('[name=selectedUOM]').val();
                    const selectedDesc = $('[name=test_desc]').val();

                    $('#test-type-modal .section-header').text(testNameText);
                    $('#test-type-modal .selectedTestType').text(selectedTestType);
                    $('#test-type-modal .selectedUOM').text(selectedUOM);
                    $('#test-type-modal .selectedCriteria').text(selectedCriteria);
                    $('#test-type-modal .selectedStandard').text(selectedStandard);
                    $('#test-type-modal .selectedDesc').text(selectedDesc);

                    $('.perf-str-test-type').hide();

                    if (selectedTestType === 'Perf-Str') {
                        $('.perf-str-test-type').show();
                    }

                    $('#test-type-modal').modal('show');
                });


                $(document).on('click', '.qc-step-2-header.workorder', function() {
                    var isEdit = $(this).closest('#edit-sample-test-modal').length > 0;
                    var prefix = isEdit ? '#edit' : '#';

                    var WorkorderText = $(prefix + 'WorkOrderSelect option:selected').text();
                    var selectedItemCategory = $('[name=fetched-item-category]').val();
                    var selectedItemcode = $('[name=fetched-itemcode]').val();
                    var selectedColor = $('[name=fetched-itemcode-color]').val();
                    var selectedLength = $('[name=fetched-workorder-length]').val();
                    var selectedItemcodeDesc = $('[name=fetched-itemcode-desc]').val();



                    $('#workorder-modal .section-header').text(WorkorderText);
                    $('#workorder-modal .selectedItemCategory').text(selectedItemCategory);
                    $('#workorder-modal .selectedItemcode').text(selectedItemcode);
                    $('#workorder-modal .selectedColor').text(selectedColor);
                    $('#workorder-modal .selectedLength').text(selectedLength);
                    $('#workorder-modal .selectedItemcodeDesc').text(selectedItemcodeDesc);

                    $('#workorder-modal').modal('show');
                });

                // Show description when an item is selected
                $('#testNameSelect').on('change', function() {
                    const selectedOption = $(this).find(':selected');
                    const test_type = selectedOption.data('test-type') || '';
                    const criteria = selectedOption.data('criteria') || '';
                    const uom = selectedOption.data('uom') || '';
                    const standard = selectedOption.data('standard') || '';
                    const description = selectedOption.data('description') || '';

                    $('#selectedTestType').val(test_type);
                    $('#selectedCriteria').val(criteria);
                    $('#selectedStandard').val(standard);
                    $('[name=selectedUOM]').val(uom);
                    $('.step-2 .selectedUOM').text(uom);
                    $('[name=test_desc]').val(description);

                    // Reset all visible elements first
                    $('.info-circle, .test-type-input, .perf-str-input, .perf-weight-input, .dimension-input, .test-standard, .safety-value')
                        .hide();
                    $('#SelectedSamplesConclusion .non-perf-str, .non-perf-weight-samples, .perf-str, .perf-weight-samples')
                        .hide();

                    // Cache selectors
                    const $safetyValue = $('#SelectedSamplesConclusion .safety-value');
                    const $testStandard = $('#SelectedSamplesConclusion .test-standard');
                    const $finalAvgValue = $('#SelectedSamplesConclusion .final-avg-value');

                    if (test_type === 'Dimension') {
                        $('.test-type-input, .dimension-input').show();
                        $('#SelectedSamplesConclusion .non-perf-str, .non-perf-weight-samples').show();

                    } else if (test_type === 'Perf-Str' && criteria === 'Min') {
                        $('.test-type-input, .perf-str-input').show();
                        $safetyValue.show().text("+ 0");
                        $testStandard.show().text(standard);
                        $finalAvgValue.text('0');
                        $('#SelectedSamplesConclusion .perf-str, .non-perf-weight-samples').show();
                    } else if (test_type === 'Perf-Str' && criteria === 'Max') {
                        $('.test-type-input, .perf-str-input').show();
                        $testStandard.show().text(standard);
                        $finalAvgValue.text('0');
                        $('#SelectedSamplesConclusion .perf-str, .non-perf-weight-samples').show();
                    } else if (test_type === 'Perf-Weight') {
                        $('#SelectedSamplesConclusion .perf-weight-css-center').addClass(
                            'justify-content-center');
                        $('#SelectedSamplesConclusion .perf-weight-css-margin').removeClass('ml-4').addClass(
                            'text-center');

                        $safetyValue.show().text("MAX ABSORB %");
                        $('.test-type-input, .perf-weight-input').show();
                        $('#SelectedSamplesConclusion .perf-str, .perf-weight-samples').not('.stdva-div')
                            .show();
                    }
                });
                // Show description when an item is selected
                $('#edittestNameSelect').on('change', function() {
                    const selectedOption = $(this).find(':selected');
                    const test_type = selectedOption.data('test-type') || '';
                    const criteria = selectedOption.data('criteria') || '';
                    const uom = selectedOption.data('uom') || '';
                    const standard = selectedOption.data('standard') || '';
                    const description = selectedOption.data('description') || '';

                    $('#editselectedTestType').val(test_type);
                    $('#editselectedCriteria').val(criteria);
                    $('#editselectedStandard').val(standard);
                    $('[name=selectedUOM]').val(uom);
                    $('.step-2 .selectedUOM').text(uom);
                    $('[name=test_desc]').val(description);

                    // Reset all visible elements first
                    $('.info-circle, .test-type-input, .perf-str-input, .perf-weight-input, .dimension-input, .test-standard, .safety-value')
                        .hide();
                    $('#editSelectedSamplesConclusion .non-perf-str, .non-perf-weight-samples, .perf-str, .perf-weight-samples')
                        .hide();

                    // Cache selectors
                    const $safetyValue = $('#editSelectedSamplesConclusion .safety-value');
                    const $testStandard = $('#editSelectedSamplesConclusion .test-standard');
                    const $finalAvgValue = $('#editSelectedSamplesConclusion .final-avg-value');

                    if (test_type === 'Dimension') {
                        $('.test-type-input, .dimension-input').show();
                        $('#editSelectedSamplesConclusion .non-perf-str, .non-perf-weight-samples').show();
                    } else if (test_type === 'Perf-Str' && criteria === 'Min') {
                        $('.test-type-input, .perf-str-input').show();
                        $safetyValue.show().text("+ 0");
                        $testStandard.show().text(standard);
                        $finalAvgValue.text('0');
                        $('#editSelectedSamplesConclusion .perf-str, .non-perf-weight-samples').show();
                    } else if (test_type === 'Perf-Str' && criteria === 'Max') {
                        $('.test-type-input, .perf-str-input').show();
                        $testStandard.show().text(standard);
                        $finalAvgValue.text('0');
                        $('#editSelectedSamplesConclusion .perf-str, .non-perf-weight-samples').show();
                    } else if (test_type === 'Perf-Weight') {
                        $('#editSelectedSamplesConclusion .perf-weight-css-center').addClass(
                            'justify-content-center');
                        $('#editSelectedSamplesConclusion .perf-weight-css-margin').removeClass('ml-4')
                            .addClass('text-center');

                        $safetyValue.show().text("MAX ABSORB %");
                        $('.test-type-input, .perf-weight-input').show();
                        $('#editSelectedSamplesConclusion .perf-str, .perf-weight-samples').not('.stdva-div')
                            .show();
                    }
                });


                $(document).on('change', '#WorkOrderSelect', function() {
                    var selectedData = $(this).select2('data')[0]; // get object from select2
                    if (!selectedData || !selectedData.id) {
                        $('.fetched-items').hide();
                        return;
                    }

                    var workorder = selectedData.workorder || '-';
                    var itemcategory = selectedData.itemcategory || '-';
                    var itemcode = selectedData.itemcode || '-';
                    var color = selectedData.color || '-';
                    var length = selectedData.length || '-';
                    var description = selectedData.description || '-';
                    var itemcodeid = selectedData.itemcodeid || '-';


                    $('.fetched-items').show();
                    $('.fetched-item-category').text(itemcategory);
                    $('.fetched-itemcode').text(itemcode);
                    $('.fetched-itemcode-color').text(color);
                    $('.fetched-workorder-length').text(length + '"');
                    $('.fetched-itemcode-desc').text(description);

                    $('[name=fetched-item-category]').val(itemcategory);
                    $('[name=fetched-itemcode]').val(itemcode);
                    $('[name=fetched-itemcode-id]').val(itemcodeid);
                    $('[name=fetched-itemcode-color]').val(color);
                    $('[name=fetched-workorder-length]').val(length);
                    $('[name=fetched-itemcode-desc]').val(description);
                });

                // $(document).on('change','#WorkOrderSelect', function() {
                //     var selectedOption = $(this).find(':selected');
                //     var selectedValue = selectedOption.val();

                //     // If no workorder is selected, hide and return
                //     if (!selectedValue) {
                //         $('.fetched-items').hide();
                //         return;
                //     }
                //     var workorder = selectedOption.data('workorder') || '-';
                //     var itemcategory = selectedOption.data('itemcategory') || '-';
                //     var itemcode = selectedOption.data('itemcode') || '-';
                //     var color = selectedOption.data('color') || '-';
                //     var length = selectedOption.data('length') || '-';
                //     var description = selectedOption.data('description') || '-';
                //     $('.fetched-items').show();
                //     $('.fetched-item-category').text(itemcategory);
                //     $('.fetched-itemcode').text(itemcode);
                //     $('.fetched-itemcode-color').text(color);
                //     $('.fetched-workorder-length').text(length + '"');
                //     $('.fetched-itemcode-desc').text(description);

                //     $('[name=fetched-item-category]').val(itemcategory);
                //     $('[name=fetched-itemcode]').val(itemcode);
                //     $('[name=fetched-itemcode-color]').val(color);
                //     $('[name=fetched-workorder-length]').val(length);
                //     $('[name=fetched-itemcode-desc]').val(description);

                // });

                $(document).on('change', '#editWorkOrderSelect', function() {
                    var selectedOption = $(this).find(':selected');
                    var workorder = selectedOption.data('workorder') || '-';
                    var itemcategory = selectedOption.data('itemcategory') || '-';
                    var itemcode = selectedOption.data('itemcode') || '-';
                    var color = selectedOption.data('color') || '-';
                    var length = selectedOption.data('length') || '-';
                    var description = selectedOption.data('description') || '-';
                    $('.fetched-items').show();
                    $('.fetched-item-category').text(itemcategory);
                    $('.fetched-itemcode').text(itemcode);
                    $('.fetched-itemcode-color').text(color);
                    $('.fetched-workorder-length').text(length + '"');
                    $('.fetched-itemcode-desc').text(description);

                    $('[name=fetched-item-category]').val(itemcategory);
                    $('[name=fetched-itemcode]').val(itemcode);
                    $('[name=fetched-itemcode-color]').val(color);
                    $('[name=fetched-workorder-length]').val(length);
                    $('[name=fetched-itemcode-desc]').val(description);

                });


                //                 $(document).on('click', '#next-step-btn', async function() {
                //                     const testName = $('#testNameSelect').val();
                //                     const workOrder = $('#WorkOrderSelect').val();

                //                     const testNameText = $('#testNameSelect option:selected').text();
                //                     const workOrderText = $('#WorkOrderSelect option:selected').text();

                //                     var item_category = $('#form-insert-sample-test input[name="fetched-item-category"]')
                //                         .val();
                //                     var item_code_id = $('#form-insert-sample-test input[name="fetched-itemcode-id"]')
                //                         .val();


                //                     if (!item_category || item_category == '-') {
                //                         showCustomWarningNotification(
                //                             `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category not found.`,
                //                             "300px"
                //                         );
                //                         return;
                //                     }

                //                     var itemCategoryExists = await checkItemCategory(item_code_id, item_category);

                //                     if (!itemCategoryExists) {
                //                         showCustomWarningNotification(
                //                             `<img src="${warningIcon}" width="24px" class="mt-n1"> The WO# you have selected does not have any Testing Thresholds defined for sample testing. Please select a different WO#, or contact your QC Administrator.`,
                //                             "300px"
                //                         );
                //                         return;
                //                     }


                //                     var selectedUOM = $('[name=selectedUOM]').val();
                //                     $('.sample-entry .sample-number .sample-uom').text(selectedUOM);

                //                     var selectedTestType = $('#selectedTestType').val();

                //                     if (selectedTestType == 'Perf-Weight') {
                //                         $('input[name="bosubi"]').prop('disabled', true); // disable others
                //                         $('input[name="bosubi"][id="N/A"]').prop('disabled', false); // disable others
                //                         $('input[name="bosubi"][id="N/A"]').prop('checked',
                //                             true); // check N/A last so it sticks
                //                     } else {
                //                         $('input[name="bosubi"]').prop('disabled', false); // disable others
                //                         $('input[name="bosubi"][id="N/A"]').prop('checked',
                //                             true); // check N/A last so it sticks
                //                         if (selectedTestType == 'Perf-Str') {
                //                             $('.insert-model-standard-div').addClass('d-flex').show();
                //                         } else {
                //                             $('.insert-model-standard-div').removeClass('d-flex').hide();
                //                         }
                //                     }


                //                     if (testName && workOrder) {
                //                         $('.step-1').hide();
                //                         $('.step-2').show();

                //                         $('.test-name-step-2').text(testNameText);
                //                         $('.workorder-step-2').text('WO# ' + workOrderText);


                //                         // Disable qc-calendar clicks
                //                         $('.qc-calendar').css('pointer-events', 'none');

                //                  if(selectedTestType=='Perf-Weight'){
                //                     $('input[name=bosubi]').slice(1,3).parent().hide();

                //                  }else{
                //               $('input[name=bosubi]').slice(1,3).parent().show();

                //                  }
                //                  skipvalidation=false;
                //                              if(selectedTestType=='Perf-Str'){
                //                           setTimeout(function(){
                //                             $('#YFS').val('YFS')
                //                             $('#YFGS').val('YFGS')

                // $('input[name="insert_model_standard"][value="YFS"]').prop('checked',1).trigger('change')
                // },2000)
                //                       }else{

                //                              if(selectedTestType=='Perf-Weight'){
                //                         skipvalidation=true
                //                               $('.addSample').trigger('click')
                //                               setTimeout(function(){
                //                              skipvalidation=false;
                //                           },2000)
                //                           }
                //                       }

                //                         $('.nosample').removeClass('col-sm-2').addClass('col-sm-3');
                //  $('.dimention-str').hide()
                // if(selectedTestType=='Dimension'){
                //  $('.nosample').addClass('col-sm-2').removeClass('col-sm-3');
                //  $('.dimention-str').show()

                //                              showTestThreshold()


                //   setTimeout(function(){
                // $('.test-avg-value').text(currentThresholds.avg);
                // $('.test-minus').text(currentThresholds.min);
                // $('.test-plus').text(currentThresholds.max);
                // $('input[name=standard_value]').val(currentThresholds.avg)
                //  },2000)
                // }


                //                     } else {
                //                         showCustomWarningNotification(
                //                             `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select both Test Name and Work Order.`,
                //                             "500px"
                //                         );
                //                         return;
                //                     }
                //             })

                $(document).on('click', '#next-step-btn', async function() {
                    const $continueBtn = $(this);
                    const originalBtnText = $continueBtn.html();

                    // Show animation and disable button
                    $continueBtn.prop('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    `);

                    // Lock the entire form
                    $('#form-insert-sample-test').css('pointer-events', 'none').addClass('form-loading');

                    try {
                        const testName = $('#testNameSelect').val();
                        const workOrder = $('#WorkOrderSelect').val();

                        const testNameText = $('#testNameSelect option:selected').text();
                        const workOrderText = $('#WorkOrderSelect option:selected').text();

                        var item_category = $(
                            '#form-insert-sample-test input[name="fetched-item-category"]').val();
                        var item_code_id = $('#form-insert-sample-test input[name="fetched-itemcode-id"]')
                            .val();

                        if (!item_category || item_category == '-') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category not found.`,
                                "300px"
                            );
                            $continueBtn.prop('disabled', false).html(`Continue`);

                            // Lock the entire form
                            $('#form-insert-sample-test').css('pointer-events', '').removeClass(
                                'form-loading');

                            return;
                        }


                        var itemCategoryExists = await checkItemCategory(item_code_id, item_category);

                        if (!itemCategoryExists) {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> The WO# you have selected does not have any Testing Thresholds defined for sample testing. Please select a different WO#, or contact your QC Administrator.`,
                                "300px"
                            );
                            $continueBtn.prop('disabled', false).html(originalBtnText);

                            // Lock the entire form
                            $('#form-insert-sample-test').css('pointer-events', '').removeClass(
                                'form-loading');

                            return;
                        }

                        var selectedUOM = $('[name=selectedUOM]').val();
                        $('.sample-entry .sample-number .sample-uom').text(selectedUOM);

                        var selectedTestType = $('#selectedTestType').val();

                        if (selectedTestType == 'Perf-Weight') {
                            $('input[name="bosubi"]').prop('disabled', true);
                            $('input[name="bosubi"][id="N/A"]').prop('disabled', false);
                            $('input[name="bosubi"][id="N/A"]').prop('checked', true);
                        } else {
                            $('input[name="bosubi"]').prop('disabled', false);
                            $('input[name="bosubi"][id="N/A"]').prop('checked', true);
                            if (selectedTestType == 'Perf-Str') {
                                $('.insert-model-standard-div').addClass('d-flex').show();
                            } else {
                                $('.insert-model-standard-div').removeClass('d-flex').hide();
                            }
                        }

                        if (testName && workOrder) {


                            $('.test-name-step-2').text(testNameText);
                            $('.workorder-step-2').text('WO# ' + workOrderText);

                            // Disable qc-calendar clicks
                            $('.qc-calendar').css('pointer-events', 'none');

                            if (selectedTestType == 'Perf-Weight') {
                                $('input[name=bosubi]').slice(1, 3).parent().hide();
                            } else {
                                $('input[name=bosubi]').slice(1, 3).parent().show();
                            }

                            skipvalidation = false;

                            if (selectedTestType == 'Perf-Str') {
                                setTimeout(function() {
                                    $('#YFS').val('YFS')
                                    $('#YFGS').val('YFGS')
                                    $('input[name="insert_model_standard"][value="YFS"]').prop(
                                        'checked', 1).trigger('change')

                                    // Re-enable form and button after operations complete
                                    // unlockForm($continueBtn, originalBtnText);
                                }, 2000);
                            } else {
                                if (selectedTestType == 'Perf-Weight') {
                                    skipvalidation = true;
                                    $('.addSample').trigger('click');
                                    setTimeout(function() {

                                        skipvalidation = false;
                                        // Re-enable form and button after operations complete
                                        // unlockForm($continueBtn, originalBtnText);
                                    }, 2000);
                                } else {

                                    // For other test types, re-enable immediately
                                    //   unlockForm($continueBtn, originalBtnText);
                                }
                            }

                            $('.nosample').removeClass('col-sm-2').addClass('col-sm-3');
                            $('.dimention-str').hide();

                            if (selectedTestType == 'Dimension') {
                                $('.nosample').addClass('col-sm-2').removeClass('col-sm-3');
                                $('.dimention-str').show();

                                showTestThreshold();

                                setTimeout(function() {
                                    $('.test-avg-value').text(currentThresholds.avg);
                                    $('.test-minus').text(currentThresholds.min);
                                    $('.test-plus').text(currentThresholds.max);
                                    $('input[name=standard_value]').val(currentThresholds.avg);

                                    // Re-enable form and button after dimension operations complete

                                }, 2000);
                            }
                            setTimeout(function() {
                                $('.step-1').hide();
                                $('.step-2').show();
                            }, 2000)
                        } else {
                            // alert('5')
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select both Test Name and Work Order.`,
                                "500px"
                            );

                            // Re-enable form and button on error

                            unlockForm($continueBtn, originalBtnText);

                            return;
                        }

                    } catch (error) {
                        console.error('Error in next-step-btn click handler:', error);
                        // Re-enable form and button on error
                        unlockForm($continueBtn, originalBtnText);
                    }
                });

                // Helper function to unlock form and reset button
                function unlockForm($button, originalHtml) {
                    $button.prop('disabled', false).html(originalHtml);
                    $('#form-insert-sample-test').css('pointer-events', 'auto').removeClass('form-loading');
                }
                // === Drop-in: thresholds UI apply helper ===
                // === helper: safe UI apply ===
                function applyThresholdsToUI(testType, t) {
                    if (!t) return;
                    if (t.avg !== undefined) $('.test-avg-value').text(t.avg);
                    if (t.min !== undefined) $('.test-minus').text(t.min);
                    if (t.max !== undefined) $('.test-plus').text(t.max);
                    if (t.avg !== undefined) $('input[name=standard_value]').val(t.avg);

                    if (testType === 'Perf-Str') {
                        const showValOrDash = (v) => (v && Number(v) !== 0 ? v : '—');
                        $('.yfs-value').text(showValOrDash(t.YFS));
                        $('.yfgs-value').text(showValOrDash(t.YFGS));
                    } else {
                        $('.yfs-value').text('—');
                        $('.yfgs-value').text('—');
                    }
                    if (t.uom) $('.threshold-uom').text(t.uom);
                }

                $(document).on('click', '#next-step-btn22', async function() {
                    const $btn = $(this);
                    const originalHtml = $btn.html();

                    // tiny helpers
                    const wait = (ms) => new Promise(r => setTimeout(r, ms));
                    async function waitFor(check, timeout = 8000, step = 120) {
                        const s = Date.now();
                        while (Date.now() - s < timeout) {
                            try {
                                if (check()) return true;
                            } catch (e) {}
                            await wait(step);
                        }
                        return false;
                    }

                    // lock
                    $btn.prop('disabled', true).html(
                        `<span class="btn-action-gear mr-2"><img src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>` +
                        `Loading...` +
                        `<span class="btn-action-gear ml-2"><img src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>`
                    );
                    $('#form-insert-sample-test').addClass('form-locked');
                    $('.modal-content').css('pointer-events', 'none');

                    try {
                        const testName = $('#testNameSelect').val();
                        const workOrder = $('#WorkOrderSelect').val();
                        const testNameText = $('#testNameSelect option:selected').text();
                        const workOrderText = $('#WorkOrderSelect option:selected').text();
                        const item_category = $(
                            '#form-insert-sample-test input[name="fetched-item-category"]').val();
                        const item_code_id = $('#form-insert-sample-test input[name="fetched-itemcode-id"]')
                            .val();

                        if (!item_category || item_category === '-') {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category not found.`,
                                "300px"
                            );
                            return;
                        }

                        const hasCat = await checkItemCategory(item_code_id, item_category);
                        if (!hasCat) {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> The WO# you have selected does not have any Testing Thresholds defined for sample testing. Please select a different WO#, or contact your QC Administrator.`,
                                "300px"
                            );
                            $btn.prop('disabled', false).html(originalHtml);

                            // Lock the entire form
                            $('#form-insert-sample-test').css('pointer-events', '').removeClass(
                                'form-locked');
                            $('.modal-content').css('pointer-events', '');
                            return;
                        }

                        if (!testName || !workOrder) {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select both Test Name and Work Order.`,
                                "500px"
                            );
                            return;
                        }

                        const selectedUOM = $('[name=selectedUOM]').val();
                        const selectedTestType = $('#selectedTestType').val();
                        $('.sample-entry .sample-number .sample-uom').text(selectedUOM);

                        // === show Step 2 FIRST (like your working version) ===
                        $('.step-1').hide();
                        $('.step-2').show();
                        $('.test-name-step-2').text(testNameText);
                        $('.workorder-step-2').text('WO# ' + workOrderText);
                        $('.qc-calendar').css('pointer-events', 'none');

                        // BOSUBI state + model standard panel (same as before)
                        if (selectedTestType === 'Perf-Weight') {
                            $('input[name="bosubi"]').prop('disabled', true);
                            $('input[name="bosubi"][id="N/A"]').prop('disabled', false).prop('checked',
                                true);
                        } else {
                            $('input[name="bosubi"]').prop('disabled', false);
                            $('input[name="bosubi"][id="N/A"]').prop('checked', true);
                            if (selectedTestType === 'Perf-Str') {
                                $('.insert-model-standard-div').addClass('d-flex').show();
                            } else {
                                $('.insert-model-standard-div').removeClass('d-flex').hide();
                            }
                        }

                        if (selectedTestType === 'Perf-Weight') {
                            $('input[name=bosubi]').slice(1, 3).parent().hide();
                        } else {
                            $('input[name=bosubi]').slice(1, 3).parent().show();
                        }

                        window.skipvalidation = false;

                        // layout (same as before)
                        $('.nosample').removeClass('col-sm-2').addClass('col-sm-3');
                        $('.dimention-str').hide();

                        // === now trigger the async threshold flows AFTER step-2 is visible ===
                        if (selectedTestType === 'Perf-Str') {
                            // exactly like the working code: delay then set + trigger change
                            setTimeout(function() {
                                $('#YFS').val('YFS');
                                $('#YFGS').val('YFGS');
                                $('input[name="insert_model_standard"][value="YFS"]').prop(
                                    'checked', true).trigger('change');
                            }, 0);

                            // wait until thresholds hit either global or UI
                            await waitFor(() => {
                                const g = window.currentThresholds;
                                if (g && g.avg !== undefined && g.min !== undefined && g.max !==
                                    undefined) return true;
                                const a = $('.test-avg-value').text().trim();
                                const n = $('.test-minus').text().trim();
                                const p = $('.test-plus').text().trim();
                                return (a && a !== '0') || (n && n !== '0') || (p && p !== '0');
                            }, 9000, 120);

                            // apply safely (no zeros for YFS/YFGS)
                            applyThresholdsToUI('Perf-Str', window.currentThresholds || {
                                avg: $('.test-avg-value').text().trim(),
                                min: $('.test-minus').text().trim(),
                                max: $('.test-plus').text().trim(),
                                YFS: Number($('.yfs-value').text().trim()) || 0,
                                YFGS: Number($('.yfgs-value').text().trim()) || 0,
                                uom: $('.threshold-uom').text().trim() || undefined
                            });

                        } else if (selectedTestType === 'Perf-Weight') {
                            window.skipvalidation = true;
                            $('.addSample').trigger('click');
                            setTimeout(() => {
                                window.skipvalidation = false;
                            }, 1500);

                        } else if (selectedTestType === 'Dimension') {
                            $('.nosample').addClass('col-sm-2').removeClass('col-sm-3');
                            $('.dimention-str').show();

                            // call your original fetch then apply after a short wait (like your setTimeout(2000))
                            if (typeof showTestThreshold === 'function') {
                                try {
                                    showTestThreshold();
                                } catch (e) {}
                            }

                            await wait(2000); // mimic your working delay
                            // prefer global if present, else read whatever UI has
                            const t = window.currentThresholds || {
                                avg: $('.test-avg-value').text().trim(),
                                min: $('.test-minus').text().trim(),
                                max: $('.test-plus').text().trim(),
                                uom: $('.threshold-uom').text().trim() || undefined
                            };
                            applyThresholdsToUI('Dimension', t);
                        }

                    } catch (e) {
                        console.error('next-step error', e);
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> An error occurred while processing your request.`,
                            "500px"
                        );
                    } finally {
                        $btn.prop('disabled', false).html(originalHtml);
                        $('#form-insert-sample-test').removeClass('form-locked');
                        $('.modal-content').css('pointer-events', 'auto');
                    }
                });

                async function checkItemCategory(item_code_id, item_category) {
                    try {
                        const res = await $.ajax({
                            type: 'get',
                            url: `{{ url('check-item-category') }}`,
                            data: {
                                item_code_id: item_code_id,
                                item_category: item_category
                            }
                        });
                        return res.exists;
                    } catch (error) {
                        return false;
                    }
                }

                $(document).on('click', '#edit-next-step-btn', function() {
                    const testName = $('#edittestNameSelect').val();
                    const workOrder = $('#editWorkOrderSelect').val();

                    const testNameText = $('#edittestNameSelect option:selected').text();
                    const workOrderText = $('#editWorkOrderSelect option:selected').text();

                    var item_category = $('#form-edit-sample-test input[name="fetched-item-category"]').val();

                    if (!item_category || item_category == '-') {
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category not found.`,
                            "300px"
                        );
                        return;
                    }

                    var selectedUOM = $('[name=selectedUOM]').val();
                    $('.sample-entry .sample-number .sample-uom').text(selectedUOM);

                    var selectedTestType = $('#editselectedTestType').val();

                    if (selectedTestType == 'Perf-Str') {
                        $('.edit-model-standard-div').addClass('d-flex').show();
                    } else {
                        $('.edit-model-standard-div').removeClass('d-flex').hide();
                    }

                    if (testName && workOrder) {
                        $('.step-1').hide();
                        $('.step-2').show();

                        $('.test-name-step-2').text(testNameText);
                        $('.workorder-step-2').text('WO# ' + workOrderText);

                        // Disable qc-calendar clicks
                        $('.qc-calendar').css('pointer-events', 'none');
                    } else {
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please select both Test Name and Work Order.`,
                            "500px"
                        );
                        return;
                    }
                });





                $('#historical').on('click', function(e) {
                    e.preventDefault();

                    var form = $('#form-insert-sample-test');

                    var asset_id = form.find('[name="AssetSelect"]').val();
                    var test_name_id = form.find('[name="testNameSelect"]').val();
                    var item_category = form.find('[name="fetched-item-category"]').val();

                    if (!asset_id || !test_name_id || !item_category || item_category == '-') {
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill all required fields to fetch history.`,
                            "300px"
                        );
                        return;
                    }


                    // Send ajax request
                    $.ajax({
                        url: '{{ url('/fetch-historical') }}',
                        method: 'POST',
                        data: {
                            asset_id: asset_id,
                            test_name_id: test_name_id,
                            item_category: item_category,
                            _token: '{{ csrf_token() }}' // Required for POST in Laravel
                        },
                        success: function(response) {

                            if (!response || response.length === 0) {
                                showCustomWarningNotification(
                                    `<img src="${warningIcon}" width="24px" class="mt-n1"> No historical records found.`,
                                    "300px"
                                );
                                return; // stop execution here
                            }

                            $('.history-machine').text(response[0].asset_no);
                            $('.history-test').text(response[0].test_name);
                            $('.history-itemcat').text(response[0].item_category);

                            var tableBody = $('#historical-modal table tbody');
                            tableBody.empty();

                            if (response.length > 0) {
                                $.each(response, function(index, row) {
                                    tableBody.append(`
                    <tr>
                        <td>${row.sample_date}</td>
                        <td>${row.min}</td>
                        <td>${row.max}</td>
                        <td>${row.avg}</td>
                        <td>${row.stdva_value}</td>
                        <td>${row.sample_number}</td>
                    </tr>
                `);
                                });
                            } else {
                                tableBody.append(
                                    `<tr><td colspan="6">No historical records found.</td></tr>`
                                );
                            }
                            $('#historical-modal').modal('show');
                        },
                        error: function() {
                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Failed to fetch sample test history.`,
                                "300px"
                            );
                        }
                    });

                });



                $('#submitSampleTest').off('click').on('click', function(e) {
                    e.preventDefault();

                    var form = $('#form-insert-sample-test'); // Use your insert form's ID here

                    // Validation
                    var selected_samples = $('#SelectedSamples .sample-entry').length;
                    const asset = form.find('[name="AssetSelect"]').val();
                    const lot = form.find('[name="lot"]').val();
                    const itemCategory = form.find('[name="fetched-item-category"]').val().trim();


                    if (!itemCategory || itemCategory == '' || itemCategory == '-') {
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Item Category not found.`,
                            "300px"
                        );
                        return;
                    }
                    if (selected_samples === 0 || !asset || !lot) {
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill all required fields.`,
                            "300px"
                        );
                        return;
                    }

                    // Get selected test type
                    var selectedTestType = $('#selectedTestType').val();

                    // Default values for standard_min and standard_max
                    let standardMin = '';
                    let standardMax = '';

                    if (selectedTestType === 'Dimension') {
                        // Grab from any `.edit-pen-sample` inside #SelectedSamples
                        const editPen = $('#SelectedSamples .edit-pen-sample').first();
                        standardMin = editPen.data('min') || '';
                        standardMax = editPen.data('max') || '';
                    }
                    // Gather and sanitize form values
                    const formData = {
                        test_name_id: form.find('[name="testNameSelect"]').val(),
                        workorder_id: form.find('[name="WorkOrderSelect"]').val(),
                        item_category: form.find('[name="fetched-item-category"]').val(),
                        itemcode: form.find('[name="fetched-itemcode"]').val(),
                        itemcode_desc: form.find('[name="fetched-itemcode-desc"]').val(),
                        color: form.find('[name="fetched-itemcode-color"]').val(),
                        length: form.find('[name="fetched-workorder-length"]').val(),
                        asset_id: form.find('[name="AssetSelect"]').val(),
                        bosubi: form.find('[name="bosubi"]:checked').val() || null,
                        lot: form.find('[name="lot"]').val(),
                        production_date: form.find('[name="production_date"]').val(),
                        min_result: form.find('[name="final-min-img"]').val(),
                        avg_result: form.find('[name="final-avg-img"]').val(),
                        max_result: form.find('[name="final-max-img"]').val(),
                        sample_date: form.find('.selected-date').text().trim(),
                        comments: form.find('input[name=comments]').val().trim(),
                        min: $('.final-min-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        avg: $('.final-avg-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        max: $('.final-max-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        avg_minus: $('.avg-minus').first().text().trim().replace(/[^0-9.]/g, ''),
                        avg_plus: $('.avg-plus').first().text().trim().replace(/[^0-9.]/g, ''),
                        stdva_value: $('.stdva-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        // standard_value: $('.standard-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        safety_threshold: $('.safety-value').first().text().replace('+', '').trim().replace(
                            /[^0-9.]/g, ''),
                        test_standard: $('.test-standard').first().text().trim(),
                        absorption_value: '',

                        // NEW FIELDS
                        standard_min: standardMin,
                        standard_max: standardMax
                    };

                    // Optional field based on test type
                    if (selectedTestType === 'Perf-Weight') {
                        formData.absorption_value = $('.standard-value').first().text().trim().replace(
                            /[^0-9.]/g, '');
                        formData.safety_threshold = 'MAX ABSORB %';
                    }
                    if (selectedTestType === 'Perf-Str') {
                        formData.standard_value = $('#SelectedSamplesConclusion #standard_value').val().trim()
                            .replace(/[^0-9.]/g, '');
                    } else if (selectedTestType === 'Dimension') {
                        formData.standard_value = $('#SelectedSamplesConclusion #standard_value').val();
                    } else {
                        formData.standard_value = $('.standard-value').first().text().trim().replace(/[^0-9.]/g,
                            '');
                    }

                    // Total samples count
                    formData.sample_number = selected_samples;

                    // Send insert request
                    $.ajax({
                        url: '{{ url('insert-sample-test') }}',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            const insertedTestId = response.inserted_id;

                            // Insert samples after main record is created
                            insertSamples(insertedTestId, selectedTestType);
                        },
                        error: function(xhr, status, error) {
                            console.error('XHR:', xhr.responseText);
                            console.error('Status:', status);
                            console.error('Error:', error);

                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Failed to insert sample test data.`,
                                "300px"
                            );
                        }
                    });
                });

                function insertSamples(sampleTestId, selectedTestType) {
                    const $button = $('.btn-action');
                    const form = $('#form-insert-sample-test');
                    const editButtons = $('#SelectedSamples .edit-pen-sample').toArray();

                    // Sort by sample number (ascending)
                    editButtons.sort((a, b) => parseFloat($(a).data('sample')) - parseFloat($(b).data('sample')));

                    let insertedCount = 0;
                    const totalSamples = editButtons.length;

                    editButtons.forEach(function(btn) {
                        const $editBtn = $(btn);
                        const sampleData = {
                            sample_test_id: sampleTestId,
                            is_deleted: 0,
                            sample_number: $editBtn.data('sample'),
                            sample_result: $editBtn.data('img')
                        };

                        if (selectedTestType === 'Perf-Weight') {
                            sampleData.sample_before = $editBtn.data('before');
                            sampleData.sample_after = $editBtn.data('after');
                            sampleData.sample_value = $editBtn.data('percentage');
                        } else {
                            sampleData.sample_value = $editBtn.data('value');
                        }

                        $.ajax({
                            url: '{{ url('insert-sample-test-sample') }}',
                            method: 'POST',
                            data: sampleData,
                            success: function(res) {
                                insertedCount++;

                                // Only show once when all samples are inserted
                                if (insertedCount === totalSamples) {
                                    showCustomNotification("QC Test inserted successfully",
                                        "300px");
                                    resetFormAndReload(form);
                                }
                            },
                            error: function() {
                                showCustomWarningNotification(
                                    `<img src="${warningIcon}" width="24px" class="mt-n1"> Failed to insert sample ${sampleData.sample_number}`,
                                    "300px"
                                );
                            }
                        });
                    });

                    resetFormState(form, $button);
                }


                function resetFormAndReload(form) {
                    $('#insert-sample-test-modal').modal('hide');
                    form[0].reset();
                    $('#SelectedSamples').empty();
                    setTimeout(() => location.reload(), 1200);
                }

                function resetButtonState($button) {
                    $button.prop('disabled', false);
                    $button.find('.btn-action-gear img').removeClass('rotating');
                    $button.find('.btn-action-gear').addClass('d-none');
                }

                function resetFormState(form, $button) {
                    resetButtonState($button);
                }


                $('#editsubmitSampleTest').off('click').on('click', function(e) {
                    e.preventDefault();

                    const form = $('#form-edit-sample-test');

                    // Validation
                    var selected_samples = $('#editSelectedSamples .sample-entry').length;
                    const asset = form.find('[name="AssetSelect"]').val();
                    const lot = form.find('[name="lot"]').val();

                    if (selected_samples === 0 || !asset || !lot) {
                        showCustomWarningNotification(
                            `<img src="${warningIcon}" width="24px" class="mt-n1"> Please fill all required fields.`,
                            "300px"
                        );
                        return;
                    }

                    // Gather and sanitize form values
                    const formData = {
                        id: form.find('[name="sample_test_id"]').val(),
                        asset_id: form.find('[name="AssetSelect"]').val(),
                        bosubi: form.find('[name="bosubi"]:checked').val() || null,
                        lot: form.find('[name="lot"]').val(),
                        production_date: form.find('[name="production_date"]').val(),
                        min_result: form.find('[name="final-min-img"]').val(),
                        avg_result: form.find('[name="final-avg-img"]').val(),
                        max_result: form.find('[name="final-max-img"]').val(),
                        test_standard: form.find('[name="edit_model_standard"]:checked').val(),
                        standard_value: form.find('[name="standard_value"]').val(),
                        comments: form.find('[name=comments]').val(),

                        min: $('.final-min-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        avg: $('.final-avg-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        max: $('.final-max-value').first().text().trim().replace(/[^0-9.]/g, ''),
                        avg_minus: $('.avg-minus').first().text().trim().replace(/[^0-9.]/g, ''),
                        avg_plus: $('.avg-plus').first().text().trim().replace(/[^0-9.]/g, ''),
                        stdva_value: $('.stdva-value').first().text().trim().replace(/[^0-9.]/g, ''),
                    };


                    // Total samples count
                    formData.sample_number = selected_samples;

                    // Send update request
                    $.ajax({
                        url: '{{ url('update-sample-test') }}',
                        method: 'POST',
                        data: formData,
                        success: function(response) {
                            var updatedTestId = response.updated_id;

                            // If needed, you can handle sample updates here
                            updateSamples(updatedTestId, selectedTestType);
                        },
                        error: function(xhr, status, error) {
                            console.error('XHR:', xhr.responseText);
                            console.error('Status:', status);
                            console.error('Error:', error);

                            showCustomWarningNotification(
                                `<img src="${warningIcon}" width="24px" class="mt-n1"> Failed to update sample test data.`,
                                "300px"
                            );
                        }
                    });
                });

                function updateSamples(sampleTestId, selectedTestType) {
                    var $button = $('.btn-action');
                    var form = $('#form-edit-sample-test');
                    var editButtons = $('#editSelectedSamples .edit-pen-sample').toArray();

                    // Sort by sample number (ascending)
                    editButtons.sort((a, b) => parseFloat($(a).data('sample')) - parseFloat($(b).data('sample')));

                    let insertedCount = 0;
                    const totalSamples = editButtons.length;

                    editButtons.forEach(function(btn) {
                        const $editBtn = $(btn);
                        const sampleData = {
                            sample_test_id: sampleTestId,
                            is_deleted: 0,
                            sample_number: $editBtn.data('sample'),
                            sample_result: $editBtn.data('img')
                        };

                        if (selectedTestType === 'Perf-Weight') {
                            sampleData.sample_before = $editBtn.data('before');
                            sampleData.sample_after = $editBtn.data('after');
                            sampleData.sample_value = $editBtn.data('percentage');
                        } else {
                            sampleData.sample_value = $editBtn.data('value');
                        }

                        $.ajax({
                            url: '{{ url('update-sample-test-sample') }}',
                            method: 'POST',
                            data: sampleData,
                            success: function(res) {
                                insertedCount++;

                                // Only show once when all samples are inserted
                                if (insertedCount === totalSamples) {
                                    showCustomNotification("QC Test updated successfully", "300px");
                                    editresetFormAndReload(form);
                                }
                            },
                            error: function() {
                                showCustomWarningNotification(
                                    `<img src="${warningIcon}" width="24px" class="mt-n1"> Failed to update sample ${sampleData.sample_number}`,
                                    "300px"
                                );
                            }
                        });
                    });

                    editresetFormState(form, $button);
                }


                function editresetFormAndReload(form) {
                    $('#edit-sample-test-modal').modal('hide');
                    form[0].reset();
                    $('#editSelectedSamples').empty();
                    setTimeout(() => location.reload(), 1200);
                }

                function editresetButtonState($button) {
                    $button.prop('disabled', false);
                    $button.find('.btn-action-gear img').removeClass('rotating');
                    $button.find('.btn-action-gear').addClass('d-none');
                }

                function editresetFormState(form, $button) {
                    editresetButtonState($button);
                }



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
                                url: '{{ url('uploadSampleTestAttachment') }}',
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

                                fetch(`{{ url('revertSampleTestAttachment') }}?key=${uniqueFileId}`, {
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
                        $('#insert-attachment-sample-test-form').submit(); // Corrected selector and method
                    }
                });

                $('#download-template').on('click', function(e) {
                    e.preventDefault(); // prevent default navigation

                    const url = $(this).data('url');

                    // create a temporary <a> to trigger download
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'QC-Test-Sample.xlsx';
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
                    var sample_test_id = $(this).attr('data-item-id');
                    var id = $(this).attr('data-id');
                    $('#del_attachment_id').val(id);
                    $('#del_sample_test_id').val(sample_test_id);
                    $('#DelAttachmentModal').modal('show');
                });

                $(document).on('click', '.delete-comment', function() {
                    var sample_test_id = $(this).attr('data-item-id');
                    var id = $(this).attr('data-id');
                    $('#del_comment_id').val(id);
                    $('#del_comment_sample_test_id').val(sample_test_id);
                    $('#DelCommentModal').modal('show');
                });

                $(document).on('click', '.edit-comment', function() {
                    var sample_test_id = $(this).attr('data-item-id');
                    var id = $(this).attr('data-id');
                    var comment = $(this).attr('data-comment');
                    $('#edit_comment_id').val(id);
                    $('#edit_comment_sample_test_id').val(sample_test_id);
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




                // $(document).on('click', '.filterSampleTestModal', function() {
                //     $('#filterSampleTestModal').modal('show');
                // })
                $(document).on('click', '.filterSampleTestModal', function(e) {
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
                    if (!$(e.target).closest('.filter-dropdown-container, .filterSampleTestModal').length) {
                        $('.filter-dropdown-container').removeClass('open');
                    }
                });
                Dashmix.helpers('rangeslider')
                @if (Session::has('success'))
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: `{!! Session::get('success') !!}`,
                    delay: 8000
                });
                @endif

                @if (Session::has('error'))
                Dashmix.helpers('notify', {
                    from: 'bottom',
                    align: 'left',
                    message: `{{ Session::get('error') }}`,
                    delay: 8000
                });
                @endif



                @if (Session::has('alert-delete'))
                    const alertStr = {!! json_encode(Session::get('alert-delete')) !!}; // ensures it's a proper string
                    const parts = alertStr.split("|");
                    const message = parts[0];
                    const id = parts[1];
                    const sample_test_id = parts[2];

                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: message + ' <a href="javascript:;" data="' + id + '" data-test-def="' +
                            sample_test_id +
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
                    const sample_test_id = parts[2];

                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: message + ' <a href="javascript:;" data="' + id + '" data-test-def="' +
                            sample_test_id +
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

                function updateQueryStringParameter(uri, key, value) {
                    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                    if (uri.match(re)) {
                        return uri.replace(re, '$1' + key + "=" + value + '$2');
                    } else {
                        return uri + separator + key + "=" + value;
                    }
                }

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
                        url: '{{ url('get-sample-test-content') }}',
                        dataType: 'json',
                        beforeSend() {
                            Dashmix.layout('header_loader_on');

                        },
                        success: function(res) {
                            Dashmix.layout('header_loader_off');
                            $(`.viewContent[data='${id}']`).html(res.viewContent);
                            $('.header-item-code').text('WO# ' + res.workorder_no + ' - ' + res.test_name +
                                ' ' + res.item_category);
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
                $("#form-end-sample-test textarea[name=reason]").val('');
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





            $(document).on('click', '#ImportSampleTests', function() {
                $("#ImportModal").modal('show');
            });
            // change today
            $(document).on('click', '#reportBtn', function() {
                $("#report-modal").modal('show');
            });
            let selectedReport = null;

            $('.report-card').on('click', function() {
                $('.report-card').removeClass('selected');
                $(this).addClass('selected');
                selectedReport = $(this).data('report');
                $('#selectReportBtn').prop('disabled', false);
            });

            $('#selectReportBtn').on('click', function() {
                if (selectedReport && (selectedReport == "by-wo" || selectedReport == "by-itemcat" || selectedReport ==
                        "by-test")) {
                    $("#workOrderReportModal .hidden-field").addClass('d-none');
                    var sub_text = '';
                    if (selectedReport == "by-wo") {
                        sub_text = 'BY WORKORDER'
                        $("#workOrderReportModal .hidden-field.wo_no").removeClass('d-none');
                    }
                    if (selectedReport == "by-itemcat") {
                        sub_text = 'BY ITEM CATEGORY'
                        $("#workOrderReportModal .hidden-field.item_category").removeClass('d-none');
                    }
                    if (selectedReport == "by-test") {
                        sub_text = 'BY TEST'
                        $("#workOrderReportModal .hidden-field.item_category.date").removeClass('d-none');
                        $("#workOrderReportModal .hidden-field.test").removeClass('d-none');
                    }
                    $("#report-modal").modal('hide');
                    $("#workOrderReportModal .modal-header .modal-subheader").text(sub_text);
                    $("#workOrderReportModal #report_name").val(selectedReport);
                    $("#workOrderReportModal").modal('show');
                } else if (selectedReport && selectedReport == "summaryByWO") {
                    $("#report-modal").modal('hide');
                    $("#summaryModal").modal('show');
                } else if (selectedReport && selectedReport == "summaryByItemCategory") {
                    $("#report-modal").modal('hide');
                    $("#summaryItemCategoryModal").modal('show');
                } else {
                    console.log('Report not selected')
                }
            });
            $(document).on('click', '#summaryModal #generateReportBtn', function() {
                var btn = $(this);
                btn.prop('disabled', true);

                var wo_no = $('#summaryModal #wo_no').val();
                var daterange = $('#summaryModal input[name=daterange]').val();
                if (!wo_no) {
                    showCustomWarningNotification('Please enter WO No.', "300px");
                    return;
                }

                $('#summaryModal').modal('hide');
                $('#reportLoaderModal').modal('show');

                setTimeout(() => {
                    $.ajax({
                        url: '{{ url('/reports/testing-summary') }}',
                        method: 'POST',
                        data: {
                            wo_no: wo_no,
                            daterange: daterange,
                            _token: '{{ csrf_token() }}'
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(blob, status, xhr) {
                            const contentType = xhr.getResponseHeader('Content-Type');
                            if (contentType && contentType.includes('application/json')) {
                                const reader = new FileReader();
                                reader.onload = function() {
                                    const json = JSON.parse(reader.result);
                                    showCustomWarningNotification(json.message, "300px");
                                    $('#reportLoaderModal').modal('hide');
                                    btn.prop('disabled', false);
                                };
                                reader.readAsText(blob);
                                return;
                            }
                            // Get filename from headers
                            $('#reportLoaderModal').modal('hide');
                            resetModalFields('#summaryModal');
                            let filename = 'testing_summary.xlsx';
                            const disposition = xhr.getResponseHeader('Content-Disposition');
                            if (disposition && disposition.indexOf('filename=') !== -1) {
                                filename = disposition.split('filename=')[1].replace(/"/g, '');
                            }

                            // Create download link
                            const link = document.createElement('a');
                            const url = window.URL.createObjectURL(blob);

                            link.href = url;
                            link.download = filename;
                            document.body.appendChild(link);
                            link.click();

                            // Cleanup
                            link.remove();
                            window.URL.revokeObjectURL(url);

                            btn.prop('disabled', false);
                        },
                        error: function() {
                            $('#reportLoaderModal').modal('hide');
                            alert('Failed to generate report.');
                            resetModalFields('#summaryModal');
                            btn.prop('disabled', false);
                        }
                    });
                }, 300);
            });
            $(document).on('click', '#summaryItemCategoryModal #generateReportBtn', function() {
                var btn = $(this);
                btn.prop('disabled', true);

                var item_category = $('#summaryItemCategoryModal #item_category_summery').val();
                var daterange = $('#summaryItemCategoryModal input[name=daterange]').val();
                if (!item_category) {
                    showCustomWarningNotification('Please select Item Category.', "300px");
                    return;
                }

                $('#summaryItemCategoryModal').modal('hide');
                $('#reportLoaderModal').modal('show');

                setTimeout(() => {
                    $.ajax({
                        url: '{{ url('/reports/testing-summary-by-item-category') }}',
                        method: 'POST',
                        data: {
                            item_category: item_category,
                            daterange: daterange,
                            _token: '{{ csrf_token() }}'
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(blob, status, xhr) {
                            const contentType = xhr.getResponseHeader('Content-Type');
                            if (contentType && contentType.includes('application/json')) {
                                const reader = new FileReader();
                                reader.onload = function() {
                                    const json = JSON.parse(reader.result);
                                    showCustomWarningNotification(json.message, "300px");
                                    $('#reportLoaderModal').modal('hide');
                                    btn.prop('disabled', false);
                                };
                                reader.readAsText(blob);
                                return;
                            }
                            // Get filename from headers
                            $('#reportLoaderModal').modal('hide');
                            resetModalFields('#summaryItemCategoryModal');
                            let filename = 'testing_summary.xlsx';
                            const disposition = xhr.getResponseHeader('Content-Disposition');
                            if (disposition && disposition.indexOf('filename=') !== -1) {
                                filename = disposition.split('filename=')[1].replace(/"/g, '');
                            }

                            // Create download link
                            const link = document.createElement('a');
                            const url = window.URL.createObjectURL(blob);

                            link.href = url;
                            link.download = filename;
                            document.body.appendChild(link);
                            link.click();

                            // Cleanup
                            link.remove();
                            window.URL.revokeObjectURL(url);

                            btn.prop('disabled', false);
                        },
                        error: function() {
                            $('#reportLoaderModal').modal('hide');
                            alert('Failed to generate report.');
                            resetModalFields('#summaryItemCategoryModal');
                            btn.prop('disabled', false);
                        }
                    });
                }, 300);
            });
            $(document).on('click', '#workOrderReportModal #generateReportBtn', async function () {
  const btn = $(this);
  btn.prop('disabled', true);

  try {
    // Get report type
    const report_name = $('#workOrderReportModal #report_name').val();
    const mfg_dept = $('#workOrderReportModal #mfg_dept').val();

    let wo_no = '';
    let wo_no_name = '';
    let item_category = '';
    let item_category_name = '';
    let test_name = '';
    let test_name_text = '';
    let prduction_start_date = '';
    let prduction_end_date = '';

    const total_years = $('#workOrderReportModal #total_years').val();
    const sample_no = $('#workOrderReportModal #sample_no').val();
    const comment_on_plan = $('#workOrderReportModal #comment_on_plan option:selected').val();
    const open_end_separators = $('#workOrderReportModal #open_end_separators').val();
    const chain_special = $('#workOrderReportModal #chain_special').val();
    const closed_end_zippers = $('#workOrderReportModal #closed_end_zippers').val();
    const closed_end_open_end = $('#workOrderReportModal #closed_end_open_end').val();
    const monthly_report_no = $('#workOrderReportModal #monthly_report_no').val();
    const sample_remarks = $('#workOrderReportModal #sample_remarks').val();

    // Common validations
    if (!total_years) {
      showCustomWarningNotification('Please enter total year.', "300px");
      btn.prop('disabled', false);
      return;
    }
    if (!sample_no) {
      showCustomWarningNotification('Please enter sample no.', "300px");
      btn.prop('disabled', false);
      return;
    }

    // Report-specific validations
    if (report_name === "by-wo") {
      wo_no = $('#workOrderReportModal #wo_no option:selected').val();
      wo_no_name = $('#workOrderReportModal #wo_no option:selected').text();

      if (!wo_no) {
        showCustomWarningNotification('Please select WO No.', "300px");
        btn.prop('disabled', false);
        return;
      }

    } else if (report_name === "by-itemcat") {
      prduction_start_date = $('#workOrderReportModal #prduction_start_date').val();
      prduction_end_date = $('#workOrderReportModal #prduction_end_date').val();
      item_category = $('#workOrderReportModal #item_category option:selected').val();
      item_category_name = $('#workOrderReportModal #item_category option:selected').text().trim();

      if (!item_category) {
        showCustomWarningNotification('Please select Item Category.', "300px");
        btn.prop('disabled', false);
        return;
      }

      const startDate = new Date(prduction_start_date);
      const endDate = new Date(prduction_end_date);
      if (startDate > endDate) {
        showCustomWarningNotification('Production Start Date cannot be greater than Production End Date.', "300px");
        btn.prop('disabled', false);
        return;
      }

    } else if (report_name === "by-test") {
      prduction_start_date = $('#workOrderReportModal #prduction_start_date').val();
      prduction_end_date = $('#workOrderReportModal #prduction_end_date').val();
      test_name = $('#workOrderReportModal #test_name option:selected').val();
      test_name_text = $('#workOrderReportModal #test_name option:selected').text().trim();

      if (!prduction_start_date || !prduction_end_date) {
        showCustomWarningNotification('Please enter both Production Start Date and Production End Date.', "300px");
        btn.prop('disabled', false);
        return;
      }

      const startDate = new Date(prduction_start_date);
      const endDate = new Date(prduction_end_date);
      if (startDate > endDate) {
        showCustomWarningNotification('Production Start Date cannot be greater than Production End Date.', "300px");
        btn.prop('disabled', false);
        return;
      }

      if (!test_name) {
        showCustomWarningNotification('Please select Test Name.', "300px");
        btn.prop('disabled', false);
        return;
      }
    }

    // Existence check
    const isExist = await checkExistance(
      report_name,
      total_years,
      sample_no,
      wo_no,
      item_category,
      test_name,
      prduction_start_date,
      prduction_end_date
    );

    if (!isExist) {
      btn.prop('disabled', false);
      return;
    }

    // ---- loader show/hide safety (fix "too quick" + stuck backdrop) ----
    let loaderShown = false;
    let hideRequested = false;

    $('#reportLoaderModal')
      .off('shown.bs.modal.hidden.bs.modal') // important: avoid multiple handlers
      .on('shown.bs.modal', function () {
        loaderShown = true;
        if (hideRequested) {
          $('#reportLoaderModal').modal('hide');
        }
      })
      .on('hidden.bs.modal', function () {
        // hard cleanup in case backdrop sticks
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
      });

    function safeHideLoader() {
      if (loaderShown) {
        $('#reportLoaderModal').modal('hide');
      } else {
        hideRequested = true; // hide as soon as it finishes opening
      }
    }

    // Hide main modal and show loader
    $('#workOrderReportModal').modal('hide');

    // show loader with static options
    loaderShown = false;
    hideRequested = false;
    $('#reportLoaderModal').modal({ backdrop: 'static', keyboard: false, show: true });

    // Fire the request (no need for setTimeout, but you can keep it if you want)
    $.ajax({
      url: '{{ url('/reports/work-order') }}',
      method: 'POST',
      data: {
        report_name,
        mfg_dept,
        wo_no,
        wo_no_name,
        item_category,
        item_category_name,
        test_name,
        test_name_text,
        prduction_start_date,
        prduction_end_date,
        total_years,
        sample_no,
        comment_on_plan,
        open_end_separators,
        chain_special,
        closed_end_zippers,
        closed_end_open_end,
        monthly_report_no,
        sample_remarks,
        _token: '{{ csrf_token() }}'
      },
      xhrFields: { responseType: 'blob' },

      success: function (blob, status, xhr) {
        const contentType = (xhr.getResponseHeader('Content-Type') || '').toLowerCase();

        // JSON response packed as blob
        if (contentType.includes('application/json')) {
          const reader = new FileReader();
          reader.onload = function () {
            try {
              const json = JSON.parse(reader.result);
              showCustomWarningNotification(json.message, "300px");
            } catch (e) {
              showCustomWarningNotification('Unexpected server response.', "300px");
            }
          };
          reader.readAsText(blob);
          return;
        }

        resetModalFields('#workOrderReportModal');

        // filename
        let filename = 'testing_summary.xlsx';
        const disposition = xhr.getResponseHeader('Content-Disposition') || '';
        const match = disposition.match(/filename\*?=(?:UTF-8''|")?([^\";]+)"?/i);
        if (match && match[1]) filename = decodeURIComponent(match[1]);

        // download
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();

        setTimeout(() => {
          link.remove();
          window.URL.revokeObjectURL(url);
        }, 0);
      },

      error: function () {
        alert('Failed to generate report.');
        resetModalFields('#workOrderReportModal');
      },

      complete: function () {
        // ALWAYS runs, even if success returned early
        safeHideLoader();
        btn.prop('disabled', false);
      }
    });

  } catch (e) {
    // any unexpected JS error
    btn.prop('disabled', false);
    try {
      $('#reportLoaderModal').modal('hide');
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css('padding-right', '');
    } catch (_) {}
  }
});

            // $(document).on('click', '#workOrderReportModal #generateReportBtn', async function() {
            //     var btn = $(this);
            //     btn.prop('disabled', true);

            //     // Get report type
            //     var report_name = $('#workOrderReportModal #report_name').val();
            //     var mfg_dept = $('#workOrderReportModal #mfg_dept').val();
            //     var wo_no = '';
            //     var wo_no_name = '';
            //     var item_category = '';
            //     var item_category_name = '';
            //     var test_name = '';
            //     var test_name_text = '';
            //     var prduction_start_date = '';
            //     var prduction_end_date = '';
            //     var total_years = $('#workOrderReportModal #total_years').val();
            //     var sample_no = $('#workOrderReportModal #sample_no').val();
            //     var comment_on_plan = $('#workOrderReportModal #comment_on_plan option:selected').val();
            //     var open_end_separators = $('#workOrderReportModal #open_end_separators').val();
            //     var chain_special = $('#workOrderReportModal #chain_special').val();
            //     var closed_end_zippers = $('#workOrderReportModal #closed_end_zippers').val();
            //     var closed_end_open_end = $('#workOrderReportModal #closed_end_open_end').val();
            //     var monthly_report_no = $('#workOrderReportModal #monthly_report_no').val();
            //     var sample_remarks = $('#workOrderReportModal #sample_remarks').val();

            //     // Common validations
            //     if (!total_years) {
            //         showCustomWarningNotification('Please enter total year.', "300px");
            //         btn.prop('disabled', false);
            //         return;
            //     }
            //     if (!sample_no) {
            //         showCustomWarningNotification('Please enter sample no.', "300px");
            //         btn.prop('disabled', false);
            //         return;
            //     }

            //     // Report-specific validations
            //     if (report_name == "by-wo") {
            //         wo_no = $('#workOrderReportModal #wo_no option:selected').val();
            //         wo_no_name = $('#workOrderReportModal #wo_no option:selected').text();

            //         if (!wo_no) {
            //             showCustomWarningNotification('Please select WO No.', "300px");
            //             btn.prop('disabled', false);
            //             return;
            //         }
            //     } else if (report_name == "by-itemcat") {
            //         prduction_start_date = $('#workOrderReportModal #prduction_start_date').val();
            //         prduction_end_date = $('#workOrderReportModal #prduction_end_date').val();
            //         item_category = $('#workOrderReportModal #item_category option:selected').val();
            //         item_category_name = $('#workOrderReportModal #item_category option:selected').text().trim();

            //         if (!item_category) {
            //             showCustomWarningNotification('Please select Item Category.', "300px");
            //             btn.prop('disabled', false);
            //             return;
            //         }
            //         // Validate start date cannot be greater than end date
            //         var startDate = new Date(prduction_start_date);
            //         var endDate = new Date(prduction_end_date);

            //         if (startDate > endDate) {
            //             showCustomWarningNotification(
            //                 'Production Start Date cannot be greater than Production End Date.', "300px");
            //             btn.prop('disabled', false);
            //             return;
            //         }
            //     } else if (report_name == "by-test") {
            //         prduction_start_date = $('#workOrderReportModal #prduction_start_date').val();
            //         prduction_end_date = $('#workOrderReportModal #prduction_end_date').val();
            //         test_name = $('#workOrderReportModal #test_name option:selected').val();
            //         test_name_text = $('#workOrderReportModal #test_name option:selected').text().trim();

            //         // Validate dates
            //         if (!prduction_start_date || !prduction_end_date) {
            //             showCustomWarningNotification(
            //                 'Please enter both Production Start Date and Production End Date.', "300px");
            //             btn.prop('disabled', false);
            //             return;
            //         }

            //         // Validate start date cannot be greater than end date
            //         var startDate = new Date(prduction_start_date);
            //         var endDate = new Date(prduction_end_date);

            //         if (startDate > endDate) {
            //             showCustomWarningNotification(
            //                 'Production Start Date cannot be greater than Production End Date.', "300px");
            //             btn.prop('disabled', false);
            //             return;
            //         }

            //         if (!test_name) {
            //             showCustomWarningNotification('Please select Test Name.', "300px");
            //             btn.prop('disabled', false);
            //             return;
            //         }
            //     }

            //     var isExist = await checkExistance(
            //             report_name,
            //             total_years,
            //             sample_no,
            //             wo_no,
            //             item_category,
            //             test_name,
            //             prduction_start_date,
            //             prduction_end_date
            //         );

            //         if (!isExist) {
            //             btn.prop('disabled', false);
            //             return;
            //         }

            //     $('#workOrderReportModal').modal('hide');
            //     $('#reportLoaderModal').modal('show');

            //     setTimeout(() => {
            //         $.ajax({
            //             url: '{{ url('/reports/work-order') }}',
            //             method: 'POST',
            //             data: {
            //                 report_name: report_name,
            //                 mfg_dept: mfg_dept,
            //                 wo_no: wo_no,
            //                 wo_no_name: wo_no_name,
            //                 item_category: item_category,
            //                 item_category_name: item_category_name,
            //                 test_name: test_name,
            //                 test_name_text: test_name_text,
            //                 prduction_start_date: prduction_start_date,
            //                 prduction_end_date: prduction_end_date,
            //                 total_years: total_years,
            //                 sample_no: sample_no,
            //                 comment_on_plan: comment_on_plan,
            //                 open_end_separators: open_end_separators,
            //                 chain_special: chain_special,
            //                 closed_end_zippers: closed_end_zippers,
            //                 closed_end_open_end: closed_end_open_end,
            //                 monthly_report_no: monthly_report_no,
            //                 sample_remarks: sample_remarks,
            //                 _token: '{{ csrf_token() }}'
            //             },
            //             xhrFields: {
            //                 responseType: 'blob'
            //             },
            //             success: function(blob, status, xhr) {
            //                 const contentType = xhr.getResponseHeader('Content-Type');
            //                 if (contentType && contentType.includes('application/json')) {
            //                     const reader = new FileReader();
            //                     reader.onload = function() {
            //                         const json = JSON.parse(reader.result);
            //                         showCustomWarningNotification(json.message, "300px");
            //                         $('#reportLoaderModal').modal('hide');
            //                         btn.prop('disabled', false);
            //                     };
            //                     reader.readAsText(blob);
            //                     return;
            //                 }
            //                 // Get filename from headers
            //                 $('#reportLoaderModal').modal('hide');
            //                 resetModalFields('#workOrderReportModal');
            //                 let filename = 'testing_summary.xlsx';
            //                 const disposition = xhr.getResponseHeader('Content-Disposition');
            //                 if (disposition && disposition.indexOf('filename=') !== -1) {
            //                     filename = disposition.split('filename=')[1].replace(/"/g, '');
            //                 }

            //                 // Create download link
            //                 const link = document.createElement('a');
            //                 const url = window.URL.createObjectURL(blob);

            //                 link.href = url;
            //                 link.download = filename;
            //                 document.body.appendChild(link);
            //                 link.click();

            //                 // Cleanup
            //                 link.remove();
            //                 window.URL.revokeObjectURL(url);

            //                 btn.prop('disabled', false);
            //             },
            //             error: function() {
            //                 $('#reportLoaderModal').modal('hide');
            //                 resetModalFields('#workOrderReportModal');
            //                 alert('Failed to generate report.');
            //                 btn.prop('disabled', false);
            //             }
            //         });
            //     }, 300);
            // });

            async function checkExistance(
                report_name,
                total_years,
                sample_no,
                wo_no,
                item_category,
                test_name,
                prduction_start_date,
                prduction_end_date
            ) {
                return new Promise((resolve) => {
                    $.ajax({
                        url: '{{ url('/reports/check-work-order') }}',
                        method: 'POST',
                        data: {
                            report_name: report_name,
                            total_years: total_years,
                            sample_no: sample_no,
                            wo_no: wo_no,
                            item_category: item_category,
                            test_name: test_name,
                            prduction_start_date: prduction_start_date,
                            prduction_end_date: prduction_end_date,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            // SUCCESS = allow continue
                            resolve(true);
                        },
                        error: function (xhr) {
                            // ERROR = stop and show message
                            let msg = 'Something went wrong.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }

                            showCustomWarningNotification(msg, "300px");
                            resolve(false);
                        }
                    });
                });
            }

            $('#total_years').on('keydown', function(e) {
                const val = this.value;

                // If backspace and cursor is just after "/", remove it
                if (e.key === 'Backspace' && val.length === 5) {
                    this.value = val.slice(0, 4);
                    e.preventDefault();
                }
            });

            $('#total_years').on('input', function() {
                let val = this.value.replace(/[^0-9]/g, '');

                if (val.length > 4) {
                    val = val.slice(0, 4) + '/' + val.slice(4, 6);
                }

                this.value = val;
            });
            $('#summaryModal, #workOrderReportModal').on('hidden.bs.modal', function() {
                resetModalFields(this);
                // Reset report card selection
                $('.report-card').removeClass('selected');
                selectedReport = null;

                // Disable select button
                $('#selectReportBtn').prop('disabled', true);
            });
            $('#report-modal').on('hidden.bs.modal', function() {
                // Reset report card selection
                $('.report-card').removeClass('selected');
                selectedReport = null;

                // Disable select button
                $('#selectReportBtn').prop('disabled', true);
            });

            function resetModalFields(modalSelector) {
                const modal = $(modalSelector);

                // Reset inputs & textareas
                modal.find('input[type="text"]:not(#mfg_dept), input[type="number"], textarea').val('');

                // Reset selects
                modal.find('select').each(function() {
                    this.selectedIndex = 0;

                    // Trigger Select2 change event if it's a Select2 element
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).val('').trigger('change');
                    } else {
                        $(this).trigger('change');
                    }
                });

                // Enable buttons
                modal.find('button').prop('disabled', false);
            }

            // change today end
            function resetButtonState($button) {
                $button.prop('disabled', false);
                $button.find('.btn-action-gear img').removeClass('rotating');
                $button.find('.btn-action-gear').addClass('d-none');
            }

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
                    var sample_test_id = $(this).attr('data-test-def');
                    var id = $(this).attr('data');
                    window.location.href = "{{ url('undo-delete-comment-sample-test') }}?id=" + id +
                        "&sample_test_id=" + sample_test_id;
                });
                $(document).on('click', '.undo-delete-attachment', function() {
                    var sample_test_id = $(this).attr('data-test-def');
                    var id = $(this).attr('data');
                    window.location.href = "{{ url('undo-delete-attachment-sample-test') }}?id=" + id +
                        "&sample_test_id=" + sample_test_id;
                });
                $(document).on('click', '.undo-delete-category', function() {
                    var id = $(this).attr('data');
                    window.location.href = "{{ url('undo-delete-sample-test') }}?id=" + id;
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
        @if(session('import_result'))
        <script>
            $(document).ready(function () {
                $('#ImportResultModal').modal('show');
            });
        </script>
        @endif

    @endsection('js')
