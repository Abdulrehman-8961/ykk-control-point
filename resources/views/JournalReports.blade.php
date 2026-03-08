@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <div id="page-loader" class="bg-main-light ">
        <p class="text-center mt-5  section-header">Please wait while page is being loaded
        </p>
    </div>


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
                padding: 6px;
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
                color: #595959;


            }

            .cert_type_button label {

                width: 150px;

                border-color: #D9D9D9;
                color: #595959;
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
                color: #898989 !important;
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
                color: #595959 !important;
            }

            .bs-select-all:hover {
                background-color: #EEEEEE !important;
                color: #595959 !important;
            }

            .c1 {
                color: #3F3F3F;
                font-family: 'Calibri';
            }

            .c2 {
                color: #595959;
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
                color: #595959;


            }

            .cert_type_button label {

                width: 150px;

                border-color: #D9D9D9;
                color: #595959;
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
                color: #898989 !important;
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

            p {
                font-size: 13pt !important;
                font-weight: 600 !important;
            }

            p,
            td,
            th {
                color: #595959 !important;
                font-size: 13pt !important;
                font-family: Jura !important;
            }

            hr {
                margin-top: 11px !important;
                border-color: #595959 !important;
                border-top: 1px solid !important;
            }

            tfoot>tr>td {
                padding-top: 15px !important;
                font-size: 12pt !important;
            }

            .tags {
                font-size: 10pt !important;
                font-weight: 500 !important;
            }

            table {
                table-layout: fixed !important;
                border-spacing: 0 !important
            }

            .table-period>thead tr td:nth-child(1),
            .table-period>tbody tr td:nth-child(1) {
                /* width: 30px !important;
                    padding-right: 20px !important;
                    text-align: right !important; */
                width: 40px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-period>thead tr td:nth-child(2),
            .table-period>tbody tr td:nth-child(2) {
                width: 40px !important;
                /* padding-left: 20px !important; */
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-period>thead tr td:nth-child(3),
            .table-period>tbody tr td:nth-child(3) {
                width: 40px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-period>thead tr td:nth-child(4),
            .table-period>thead tr td:nth-child(4) {
                width: 40px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-period>thead tr td:nth-child(5),
            .table-period>tbody tr td:nth-child(5) {
                width: 50px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-period>thead tr td:nth-child(6),
            .table-period>tbody tr td:nth-child(6) {
                width: 50px !important;
                text-align: left !important;
                padding-bottom: 5px !important
            }

            .table-period>thead tr td:nth-child(7),
            .table-period>tbody tr td:nth-child(7) {
                width: 100px !important;
                text-align: left !important;
                padding-bottom: 5px !important;
            }

            .table-period>thead tr td:nth-child(8),
            .table-period>tbody tr td:nth-child(8) {
                width: 75px !important;
                text-align: right !important;
                padding-bottom: 5px !important;
            }
            .table-period>thead tr td:nth-child(9),
            .table-period>tbody tr td:nth-child(9) {
                width: 75px !important;
                text-align: right !important;
                padding-bottom: 5px !important;
            }

            .table-period>tfoot tr td:nth-child(1) {}

            .table-period>tfoot tr td:nth-child(2) {
                width: 100px !important;
                text-align: right !important;
                padding-bottom: 5px !important;

            }

            .table-period>tfoot tr td:nth-child(3) {
                width: 75px !important;
                text-align: right !important;
            }

            .table-period>tfoot tr td:nth-child(4) {
                width: 75px !important;
                text-align: right !important;
            }

            .filter-applied {
                line-height: 1 !important;
                font-size: 11pt !important;
            }


            .table-totals>tfoot tr td:nth-child(1) {
                width: 40px !important;
            }

            .table-totals>tfoot tr td:nth-child(2) {
                width: 40px !important;
            }

            .table-totals>tfoot tr td:nth-child(3) {
                width: 40px !important;
            }

            .table-totals>tfoot tr td:nth-child(4) {
                width: 40px !important;
            }

            .table-totals>tfoot tr td:nth-child(5) {
                width: 50px !important;
            }

            .table-totals>tfoot tr td:nth-child(6) {
                width: 50px !important;
                text-align: right !important;
            }

            .table-totals>tfoot tr td:nth-child(7) {
                width: 100px !important;
                text-align: right !important;
            }

            .table-totals>tfoot tr td:nth-child(8) {
                width: 75px !important;
                text-align: right !important;
            }
            .table-totals>tfoot tr td:nth-child(9) {
                width: 75px !important;
                text-align: right !important;
            }


            #scrollData::-webkit-scrollbar {
                width: 10px;
            }


            td,
            th {
                text-wrap: nowrap;
                overflow: hidden;
            }

            .text-red {
                color: #E54643 !important;
            }

            tfoot>tr:nth-child(2)>td {
                padding-top: 0px !important;
            }
        </style>

        <!-- Page Content -->
        <div class="con   no-print page-header py-2" id="">
            <!-- Full Table -->
            <div class="b   mb-0  ">

                <div class="block-content pt-0 mt-0">

                    <div class="TopArea"
                        style="position: sticky;
                 padding-top: 11px;
                 z-index: 1000;
">
                        <div class="row justify-content-end"
                            style="
    margin-top: -10px;
    padding-top: 5px;
    padding-bottom: 5px;
">



                            <div class="d-flex text-right col-lg-3 justify-content-end">
                                <a href="{{ url('journals') }}" class="text-dark d3 mr-3">
                                    <img src="{{ asset('public/batch_icons/back.png') }}" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Got to Journals" width="25px"></a>
                                <script>
                                    function printWholePage() {
                                        var printContents = document.getElementById('scrollData').innerHTML;
                                        var originalContents = document.body.innerHTML;
                                        document.body.innerHTML = printContents;
                                        window.print();
                                        document.body.innerHTML = originalContents;
                                    }

                                    function submitForm() {
                                        document.getElementById('myForm').submit();
                                    }
                                </script>

                                <a href="javascript:;" onclick="submitForm()" onclick="submitForm(); return false;"
                                    class="text-dark d3 mr-3"><img src="{{ asset('public/img/ui-icon-export.png') }}"
                                        width="25px" title="" data-original-title="Export"></a>
                                <a href="javascript:;" onclick="printWholePage()" class="text-dark d3 mr-3"><img
                                        src="{{ asset('public/img/action-white-print.png') }}" width="25px"></a>
                                <form action="{{ route('journal-reports.export') }}" method="GET" id="myForm">
                                    <input type="hidden" name="rollups" value="{{ $rollups }}">
                                    <input type="hidden" name="client_id" value="{{ $filters->client_id }}">
                                    <input type="hidden" name="fiscal_year" value="{{ $filters->fiscal_year }}">
                                    <input type="hidden" name="period" value="{{ json_encode($filters->period) }}">
                                    <input type="hidden" name="source" value="{{ json_encode($filters->source) }}">
                                    <input type="hidden" name="account" value="{{ json_encode($filters->account) }}">
                                </form>

                                @if (@Auth::user()->role == 'admin')

                                    <a href="javascript:;" data-toggle="tooltip" data-title="Settings"
                                        class="mr-3 text-dark headerSetting d3   mr-3 "><img
                                            src="{{ asset('public/img/ui-icon-settings.png') }}" width="25px"></a>
                                @endif
                                <!-- User Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <a type="button" class="mr-4" id="page-header-user-dropdown" data-toggle="dropdown"
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
                    <div class="col-lg-12    " id="showData" style="">
                        <div id="scrollData" style="overflow-y: auto;height:90vh;">
                            <div class="d-flex h-100 flex-column align-items-center justify-content-center">

                                <img src="{{ asset('public/spinner.gif') }}" width="50px">
                                <p>Please wait while loading report...</p>
                            </div>
                        </div><!--scroll div-->
                    </div>
                </div>



        </main>
        <!-- END Main Container -->
    @endsection('content')



    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
    <!-- Include XLSX library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>

    <script>
        function submitForm() {
            document.getElementById("myForm").submit();
        }
        $(document).ready(function() {
            let loadContent = () => {                
                $.ajax({
                    type: "GET",
                    url: "{{ url('/journal-reports/get-content') }}",
                    data: {
                        "rollups": "{!! $rollups !!}",
                        "client_id": "{!! $filters->client_id !!}",
                        "fiscal_year": "{!! $filters->fiscal_year !!}",
                        "period": JSON.stringify(JSON.parse(`{!! json_encode($filters->period) !!}`)),
                        "source": JSON.stringify(JSON.parse(`{!! json_encode($filters->source) !!}`)),
                        "account": JSON.stringify(JSON.parse(`{!! json_encode($filters->account) !!}`)),
                    },
                }).done(function(response) {
                    $("#scrollData").html(response);
                }).fail(function(response) {
                    $("#scrollData").html(`
                    <div class="d-flex h-100 flex-column align-items-center justify-content-center">
                        <p>An error occurred while loading report.</p>
                    </div>
                    `);
                });
            }

            loadContent();
            if ($("#exportBtn").length) {
                console.log("exportBtn exists.");
                if ($("#exportBtn").data("events") && $("#exportBtn").data("events").click) {
                    console.log("click event is bound to exportBtn.");
                } else {
                    console.log("click event is not bound to exportBtn.");
                    $("#exportBtn").click(function() {
                        var ws = XLSX.utils.table_to_sheet($("#scrollData table")[0]);
                        var wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
                        XLSX.writeFile(wb, 'journalReportsreport.xlsx');
                    });
                }
            } else {
                console.log("exportBtn does not exist.");
            }
        });
    </script>
