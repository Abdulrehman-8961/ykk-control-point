@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <?php $userAccess = explode(',', Auth::user()->access_to_client); ?>
    <style type="text/css">
        .dropdown-menu {
            z-index: 100000 !important;
        }

        .pagination {
            margin-bottom: 0px;
        }

        .ActionIcon {

            border-radius: 50%;
            padding: 6px;
        }

        .ActionIcon:hover {

            background: #dadada;
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

        .contract_type_button label,
        .contract_type_button input {}

        .contract_type_button {
            float: left;
        }

        .contract_type_button input[type="radio"] {
            opacity: 0.011;
            z-index: 100;

            position: absolute;
        }

        .contract_type_button input[type="radio"]:checked+label {
            background: #4194F6;
            font-weight: bold;
            color: white;
        }

        .contract_type_button label:hover {



            background-color: #EEEEEE;
            color: #7F7F7F;


        }

        .contract_type_button label {

            width: 150px;

            border-color: #D9D9D9;
            color: #7F7F7F;
            font-size: 12pt;


        }

        .modal-backdrop {
            background-color: #00000080 !important;
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

        .attachmentDiv {
            border: 1px solid lightgrey;
            padding: 7px;
            font-size: 10px;
            border-radius: 32px;
            color: grey;
            width: 50px;
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

        .contract_type_button label,
        .contract_type_button input {}

        .contract_type_button {
            float: left;
        }

        .contract_type_button input[type="radio"] {
            opacity: 0.011;
            z-index: 100;

            position: absolute;
        }

        .contract_type_button input[type="radio"]:checked+label {
            background: #4194F6;
            font-weight: bold;
            color: white;
        }

        .contract_type_button label:hover {



            background-color: #EEEEEE;
            color: #7F7F7F;


        }

        .contract_type_button label {

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


        .headerSetting {
            padding: 5px 10px !important;
        }
    </style>
    <style type="text/css">
        .cardText {
            font-size: 20px;
            font-weight: bold
        }

        .columnblock {
            box-shadow: 5px 4px 10px lightgrey;
            border-radius: 8px !important;
        }
    </style>

    <style>
        .my-card {
            background: #ffffff;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 18px 18px 14px;
            height: 100%;
            position: relative;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 10px 15px -3px, rgba(0, 0, 0, 0.05) 0px 4px 6px -2px;
        }

        .my-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .my-title {
            font-weight: 700;
            font-size: 20px;
            letter-spacing: 0.1px;
            color: black
        }

        .my-subtitle {
            font-size: 15px;
            color: #6e6e6e;
            margin-top: 2px;
        }

        .my-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .my-select {
            appearance: none;
            border: 1px solid #bcbcbc;
            border-radius: 10px;
            padding: 4px 28px 4px 12px;
            font-weight: 500;
            font-size: 14px;
            background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 9l6 6l6 -6" /></svg>') no-repeat right 8px center/14px;
            /* background: #fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%233c3c3c'><path d='M4 6l4 4 4-4z'/></svg>") no-repeat right 8px center/14px; */
        }

        .my-download {
            border: none;
            background: transparent;
            padding: 2px;
            cursor: pointer;
        }

        .my-download svg {
            width: 22px;
            height: 22px;
            fill: #5f5f5f;
        }

        .my-search {
            margin-top: 14px;
        }

        .my-search input {
            width: 72%;
            border: none;
            border-bottom: 2px solid #c8c8c8;
            padding: 6px 0;
            font-size: 11px;
            letter-spacing: 0.6px;
            color: #4c4c4c;
            text-transform: uppercase;
            outline: none;
        }

        .my-search input::placeholder {
            color: #9b9b9b;
        }

        .my-list {
            margin-top: 14px;
            padding-right: 6px;
            max-height: 24vh;
            overflow-y: auto;
            scrollbar-gutter: stable;
        }

        @media (max-height: 800px) {
            .my-list {
                max-height: 17vh;
            }

            .search-text {
                margin-bottom: 10px !important;
            }
        }

        @media (max-height: 650px) {
            .my-list {
                max-height: 14vh;
            }
        }

        .my-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            padding: 3px 0;
        }

        .my-row span:first-child {
            color: #24354f;
            letter-spacing: 0.2px;
        }

        .my-rate {
            color: #C41E3A;
            font-weight: 600;
        }

        .my-list::-webkit-scrollbar {
            width: 16px;
        }

        .my-list::-webkit-scrollbar-track {
            background: #ffffff;
        }

        .my-list::-webkit-scrollbar-thumb {
            background: #bdbdbd;
            border-radius: 999px;
            border: 3px solid #ffffff;
        }

        ::-webkit-scrollbar {
            background-color: #FFFFFF;
            border: 1px solid transparent;
        }

        .my-list::-webkit-scrollbar-button:single-button:vertical:decrement {
            border: none;
            background-size: 25px 25px;
        }

        .my-list::-webkit-scrollbar-button:single-button:vertical:increment {
            background-size: 25px 25px;
            border: none;
        }


        @media (max-width: 991px) {
            .my-search input {
                width: 100%;
            }
        }


        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 1200px;
        }

        th,
        td {
            border: 1px solid #000;
            /* padding: 8px 12px; */
            text-align: center;
            font-size: 14px;
        }

        th {
            font-weight: bold;
            background: #f5f5f5;
        }
    </style>

    <!-- Main Container -->
    <main id="main-container">
        <!-- Hero -->
        <div class="content">
            <div class="row g-4 mb-3">

                <div class="col-lg-4">
                    <div class="my-card">
                        <div class="my-header">
                            <div>
                                <div class="my-title">by Test</div>
                                <div class="my-subtitle">Top Failures</div>
                            </div>

                        </div>

                        <div class="my-search d-flex justify-content-between align-items-center mb-2 gap-2">
                            <input type="text" class="mr-2" id="testlist_search" placeholde r="Search..." />
                            <div class="my-actions">
                                <select class="my-select" aria-label="Rows" id="testlist_pagination">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <button class="my-download" id="testlist_download" type="button" aria-label="Download">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 3v10.2l3.1-3.1 1.4 1.4-5.5 5.5-5.5-5.5 1.4-1.4 3.1 3.1V3z"></path>
                                        <path d="M4 20h16v1.6H4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-muted mt-0 search-text" style="font-size: 10px;">SEARCH TEST NAME</p>

                        <div class="my-list" id="testlist">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="my-card">
                        <div class="my-header">
                            <div>
                                <div class="my-title">by Item Category</div>
                                <div class="my-subtitle">Top Failures</div>
                            </div>

                        </div>

                        <div class="my-search d-flex justify-content-between align-items-center mb-2 gap-2">
                            <input type="text" class="mr-2" id="itemcategorylist_search" placeholde r="Search..." />
                            <div class="my-actions">
                                <select class="my-select" aria-label="Rows"
                                    id=
                                    "itemcategorylist_pagination">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <button class="my-download" id="itemcategorylist_download" type="button" aria-label="Download">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 3v10.2l3.1-3.1 1.4 1.4-5.5 5.5-5.5-5.5 1.4-1.4 3.1 3.1V3z"></path>
                                        <path d="M4 20h16v1.6H4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-muted mt-0 search-text" style="font-size: 10px;">SEARCH ITEM CATEGORY</p>

                        <div class="my-list" id="itemcategorylist">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="my-card">
                        <div class="my-header">
                            <div>
                                <div class="my-title">by Asset</div>
                                <div class="my-subtitle">Top Failures</div>
                            </div>

                        </div>

                        <div class="my-search d-flex justify-content-between align-items-center mb-2 gap-2">
                            <input type="text" class="mr-2" id="assetlist_search" placeholde r="Search..." />
                            <div class="my-actions">
                                <select class="my-select" aria-label="Rows" id="assetlist_pagination">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <button class="my-download" id="assetlist_download" type="button" aria-label="Download">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 3v10.2l3.1-3.1 1.4 1.4-5.5 5.5-5.5-5.5 1.4-1.4 3.1 3.1V3z"></path>
                                        <path d="M4 20h16v1.6H4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-muted mt-0 search-text" style="font-size: 10px;">SEARCH ASSET</p>

                        <div class="my-list" id="assetlist">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="my-card">
                        <div class="my-header">
                            <div>
                                <div class="my-title">Historical Sample Tests</div>
                                <div class="my-subtitle">Top Failures</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_date_range" data-mode="range"
                                        data-date-format="Y-m-d" data-alt-input="true" data-alt-format="d-M-Y" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">PRODUCTION DATE RANGE
                                </p>
                            </div>
                            <div class="col-md-3">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_test_date_range"
                                        data-mode="range" data-date-format="Y-m-d" data-alt-input="true"
                                        data-alt-format="d-M-Y" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">TEST DATE RANGE
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_test_name_search" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">FILTER BY TEST NAME
                                </p>
                            </div>
                            <div class="col-md-2">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_asset_search" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">FILTER BY ASSET
                                </p>
                            </div>
                            <div class="col-md-2">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_item_category_search" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">FILTER BY ITEM
                                    CATEGORY
                                </p>
                            </div>
                            <div class="col-md-2">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_item_code_search" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">FILTER BY ITEM CODE
                                </p>
                            </div>
                            <div class="col-md-2">
                                <div class="my-search my-0 d-flex justify-content-between align-items-center mb-2">
                                    <input type="text" class="mr-2 w-100" id="historical_wo_search" />
                                </div>
                                <p class="text-muted mt-0 my-0 search-text" style="font-size: 10px;">FILTER BY WO#
                                </p>
                            </div>
                            <div class="col-md-2">
                                <div class="my-actions" style="justify-self: right;">
                                    <select class="my-select" aria-label="Rows" id="historical-sample-tests_pagination">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <button class="my-download" id="historical-sample-tests_download" aria-label="Download">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 3v10.2l3.1-3.1 1.4 1.4-5.5 5.5-5.5-5.5 1.4-1.4 3.1 3.1V3z"></path>
                                            <path d="M4 20h16v1.6H4z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="my-list">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Test Date</th>
                                        <th>WO#</th>
                                        <th>Test Name</th>
                                        <th>Asset#</th>
                                        <th>Prod Date</th>
                                        <th>Item Cate</th>
                                        <th>Item Code</th>
                                        <th>MIN</th>
                                        <th>MAX</th>
                                        <th>AVG</th>
                                        <th>STD</th>
                                        <th style="width: 10%"># Samples</th>
                                    </tr>
                                </thead>
                                <tbody id="historical-sample-tests">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Page Content -->
        <div class="content px-4">

        </div>
        </div>

        </div>
        </div>
        <!-- END Latest Orders + Stats -->
        </div>
        <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript">
        $(function() {
            @if (Session::has('success'))

                Dashmix.helpers('notify', {
                    align: 'center',
                    message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> {{ Session::get('success') }}',
                    delay: 5000
                });
            @endif

            $('#historical_date_range').flatpickr({
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd-M-Y',
                altInputClass: 'mr-2 w-100'
            });

            $('#historical_test_date_range').flatpickr({
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd-M-Y',
                altInputClass: 'mr-2 w-100'
            });
        })

        function loadWorkorders() {
            const search = $('#workorderlist_search').val();
            const per_page = $('#workorderlist_pagination').val();

            $.ajax({
                type: 'GET',
                url: "{{ url('dashboards/top-failures/workorders') }}",
                data: {
                    search: search,
                    per_page: per_page
                },
                success: function(response) {
                    let html = '';

                    // response.data if paginate, warna response direct array
                    const rows = response.data ? response.data : response;

                    rows.forEach(function(item) {
                        html += `
                    <div class="my-row">
                        <span>${item.workorder_no ?? ''}</span>
                        <span class="my-rate pr-3">${((item.avg_failure_rate ?? 0) * 100).toFixed(4)}%</span>
                    </div>
                `;
                    });

                    $('#workorderlist').html(html || `<div class="text-muted p-2">No results</div>`);
                },
                error: function() {
                    $('#workorderlist').html(`<div class="text-danger p-2">Error loading data</div>`);
                }
            });
        }

        // initial load
        loadWorkorders();

        // per_page change
        $('#workorderlist_pagination').on('change', function() {
            loadWorkorders();
        });

        // search typing (debounce)
        var woTimer = null;
        $('#workorderlist_search').on('input', function() {
            clearTimeout(woTimer);
            woTimer = setTimeout(() => loadWorkorders(), 300);
        });










        function loaditemcategories() {
            const search = $('#itemcategorylist_search').val();
            const per_page = $('#itemcategorylist_pagination').val();

            $.ajax({
                type: 'GET',
                url: "{{ url('dashboards/top-failures/itemcategories') }}",
                data: {
                    search: search,
                    per_page: per_page
                },
                success: function(response) {
                    let html = '';

                    // response.data if paginate, warna response direct array
                    const rows = response.data ? response.data : response;

                    rows.forEach(function(item) {
                        html += `
                    <div class="my-row">
                        <span>${item.item_category ?? ''}</span>
                        <span class="my-rate pr-3">${((item.avg_failure_rate ?? 0) * 100).toFixed(4)}%</span>
                    </div>
                `;
                    });

                    $('#itemcategorylist').html(html || `<div class="text-muted p-2">No results</div>`);
                },
                error: function() {
                    $('#itemcategorylist').html(`<div class="text-danger p-2">Error loading data</div>`);
                }
            });
        }

        // initial load
        loaditemcategories();

        // per_page change
        $('#itemcategorylist_pagination').on('change', function() {
            loaditemcategories();
        });

        // search typing (debounce)
        var woTimer = null;
        $('#itemcategorylist_search').on('input', function() {
            clearTimeout(woTimer);
            woTimer = setTimeout(() => loaditemcategories(), 300);
        });








        function loadasset() {
            const search = $('#assetlist_search').val();
            const per_page = $('#assetlist_pagination').val();

            $.ajax({
                type: 'GET',
                url: "{{ url('dashboards/top-failures/assets') }}",
                data: {
                    search: search,
                    per_page: per_page
                },
                success: function(response) {
                    let html = '';

                    // response.data if paginate, warna response direct array
                    const rows = response.data ? response.data : response;

                    rows.forEach(function(item) {
                        html += `
                    <div class="my-row">
                        <span>${item.asset_no ?? ''}</span>
                        <span class="my-rate pr-3">${((item.avg_failure_rate ?? 0) * 100).toFixed(4)}%</span>
                    </div>
                `;
                    });

                    $('#assetlist').html(html || `<div class="text-muted p-2">No results</div>`);
                },
                error: function() {
                    $('#assetlist').html(`<div class="text-danger p-2">Error loading data</div>`);
                }
            });
        }

        // initial load
        loadasset();

        // per_page change
        $('#assetlist_pagination').on('change', function() {
            loadasset();
        });

        // search typing (debounce)
        var woTimer = null;
        $('#assetlist_search').on('input', function() {
            clearTimeout(woTimer);
            woTimer = setTimeout(() => loadasset(), 300);
        });






        function loadtest() {
            const search = $('#testlist_search').val();
            const per_page = $('#testlist_pagination').val();

            $.ajax({
                type: 'GET',
                url: "{{ url('dashboards/top-failures/tests') }}",
                data: {
                    search: search,
                    per_page: per_page
                },
                success: function(response) {
                    let html = '';

                    // response.data if paginate, warna response direct array
                    const rows = response.data ? response.data : response;

                    rows.forEach(function(item) {
                        html += `
                    <div class="my-row">
                        <span>${item.test_name ?? ''}</span>
                        <span class="my-rate pr-3">${((item.avg_failure_rate ?? 0) * 100).toFixed(4)}%</span>
                    </div>
                `;
                    });

                    $('#testlist').html(html || `<div class="text-muted p-2">No results</div>`);
                },
                error: function() {
                    $('#testlist').html(`<div class="text-danger p-2">Error loading data</div>`);
                }
            });
        }

        // initial load
        loadtest();

        // per_page change
        $('#testlist_pagination').on('change', function() {
            loadtest();
        });

        // search typing (debounce)
        var woTimer = null;
        $('#testlist_search').on('input', function() {
            clearTimeout(woTimer);
            woTimer = setTimeout(() => loadtest(), 300);
        });

        $('#testlist_download').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const params = new URLSearchParams({
                search: $('#testlist_search').val() || '',
                per_page: $('#testlist_pagination').val() || 10,
                export: 'csv'
            });

            window.location.href = "{{ url('dashboards/top-failures/tests') }}?" + params.toString();
        });

        $('#workorderlist_download').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const params = new URLSearchParams({
                search: $('#workorderlist_search').val() || '',
                per_page: $('#workorderlist_pagination').val() || 10,
                export: 'csv'
            });

            window.location.href = "{{ url('dashboards/top-failures/workorders') }}?" + params.toString();
        });

        $('#itemcategorylist_download').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const params = new URLSearchParams({
                search: $('#itemcategorylist_search').val() || '',
                per_page: $('#itemcategorylist_pagination').val() || 10,
                export: 'csv'
            });

            window.location.href = "{{ url('dashboards/top-failures/itemcategories') }}?" + params.toString();
        });

        $('#assetlist_download').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const params = new URLSearchParams({
                search: $('#assetlist_search').val() || '',
                per_page: $('#assetlist_pagination').val() || 10,
                export: 'csv'
            });

            window.location.href = "{{ url('dashboards/top-failures/assets') }}?" + params.toString();
        });










        function loadhistoricaltest() {
            const test_name = $('#historical_test_name_search').val();
            const asset_no = $('#historical_asset_search').val();
            const item_category = $('#historical_item_category_search').val();
            const item_code = $('#historical_item_code_search').val();
            const workorder_no = $('#historical_wo_search').val();
            const prod_date_range = $('#historical_date_range').val();
            const test_date_range = $('#historical_test_date_range').val();
            const per_page = $('#historical-sample-tests_pagination').val();

            $.ajax({
                type: 'GET',
                url: "{{ url('dashboards/historical-sample-tests') }}",
                data: {
                    test_name: test_name,
                    asset_no: asset_no,
                    item_category: item_category,
                    item_code: item_code,
                    workorder_no: workorder_no,
                    date_range: prod_date_range,
                    prod_date_range: prod_date_range,
                    test_date_range: test_date_range,
                    per_page: per_page
                },
                success: function(response) {
                    let html = '';
                    const formatDate = (value) => {
                        if (!value) {
                            return '';
                        }

                        const dt = new Date(value);
                        return Number.isNaN(dt.getTime()) ? value : dt.toLocaleDateString('en-GB');
                    };

                    // response.data if paginate, warna response direct array
                    const rows = response.data ? response.data : response;

                    rows.forEach(function(item) {
                        const testDate = item.test_date ?? item.testing_date ?? '';
                        const prodDate = item.production_date ?? item.prod_date ?? '';
                        const woNo = item.workorder_no ?? item.wo_no ?? '';
                        const itemCode = item.item_code ?? item.code ?? '';

                        html += `
                        <tr>
                            <td>${formatDate(testDate)}</td>
                            <td>${woNo}</td>
                            <td>${item.test_name ?? ''}</td>
                            <td>${item.asset_no ?? ''}</td>
                            <td>${formatDate(prodDate)}</td>
                            <td>${item.item_category ?? ''}</td>
                            <td>${itemCode}</td>
                            <td>${item.min ?? 0}</td>
                            <td>${item.max ?? 0}</td>
                            <td>${item.avg ?? 0}</td>
                            <td>${item.stdva_value ?? 0}</td>
                            <td>${item.sample_number ?? 0}</td>
                        </tr>
                `;
                    });

                    $('#historical-sample-tests').html(html || `
                        <tr>
                            <td colspan="12" class="text-center">No historical sample tests available</td>
                        </tr>
                    `);
                },
                error: function() {
                    $('#historical-sample-tests').html(`
                        <tr>
                            <td colspan="12" class="text-center">No historical sample tests available</td>
                        </tr>
                    `);
                }
            });
        }

        // initial load
        loadhistoricaltest();

        // per_page change
        $('#historical-sample-tests_pagination').on('change', function() {
            loadhistoricaltest();
        });

        // search typing (debounce)
        var historicalTestTimer = null;
        $('#historical_test_name_search').on('input', function() {
            clearTimeout(historicalTestTimer);
            historicalTestTimer = setTimeout(() => loadhistoricaltest(), 300);
        });

        var historicalAssetTimer = null;
        $('#historical_asset_search').on('input', function() {
            clearTimeout(historicalAssetTimer);
            historicalAssetTimer = setTimeout(() => loadhistoricaltest(), 300);
        });

        var historicalItemCategoryTimer = null;
        $('#historical_item_category_search').on('input', function() {
            clearTimeout(historicalItemCategoryTimer);
            historicalItemCategoryTimer = setTimeout(() => loadhistoricaltest(), 300);
        });

        var historicalItemCodeTimer = null;
        $('#historical_item_code_search').on('input', function() {
            clearTimeout(historicalItemCodeTimer);
            historicalItemCodeTimer = setTimeout(() => loadhistoricaltest(), 300);
        });

        var historicalWoTimer = null;
        $('#historical_wo_search').on('input', function() {
            clearTimeout(historicalWoTimer);
            historicalWoTimer = setTimeout(() => loadhistoricaltest(), 300);
        });

        $('#historical_date_range').on('change', function() {
            loadhistoricaltest();
        });

        $('#historical_test_date_range').on('change', function() {
            loadhistoricaltest();
        });

        $('#historical-sample-tests_download').on('click', function() {
            const params = new URLSearchParams({
                test_name: $('#historical_test_name_search').val() || '',
                asset_no: $('#historical_asset_search').val() || '',
                item_category: $('#historical_item_category_search').val() || '',
                item_code: $('#historical_item_code_search').val() || '',
                workorder_no: $('#historical_wo_search').val() || '',
                date_range: $('#historical_date_range').val() || '',
                prod_date_range: $('#historical_date_range').val() || '',
                test_date_range: $('#historical_test_date_range').val() || '',
                per_page: $('#historical-sample-tests_pagination').val() || 10,
                export: 'csv'
            });

            window.location.href = "{{ url('dashboards/historical-sample-tests') }}?" + params.toString();
        });
    </script>
@endsection('content')
