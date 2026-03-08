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
                @page {
                    size: 11in 8.5in;
                    margin: 0.5in;
                }

                .no-print {
                    display: none !important;
                }

                #showData {
                    height: 100% !important;

                }

                .content {
                    background: #F0F3F8;
                }

                table td {
                    font-size: 10pt !important;
                }

                .first-th {
                    width: 100px !important;
                    white-space: pre-wrap;
                }

                .first-td-text {
                    font-size: 10pt !important;
                }

                .table-div {
                    padding-left: 0rem !important;
                }

                table td {
                    padding-left: 3px !important;
                    padding-right: 3px !important;
                }
            }

            .table td,
            .table th {
                padding: .75rem .20rem !important;
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






            #scrollData::-webkit-scrollbar {
                width: 10px;
            }


            td,
            th {
                text-wrap: nowrap;
                overflow: hidden;
                border: 0;
            }

            .table td,
            .table th {
                border-top: 0 !important;
            }


            .td-month {
                font-family: Signika;
                background: #585858;
                color: #fff;
                padding: 3px 5px;
                border-radius: 3px;
                font-size: 11pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center;
            }

            .td-green {
                background: #ffffff;
                padding: 6px 7px;
                border-radius: 3px;
                color: #262626;
                border: 1px solid #595959;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
            }

            .td-green-new {
                background: #ffffff;
                padding: 6px 7px;
                border-radius: 3px;
                color: #262626;
                border: 1px solid #595959;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
            }

            .td-paid {
                font-family: Signika;
                background: #F5FCF3;
                padding: 6px 7px;
                border-radius: 3px;
                color: #4EA833;
                border: 1px solid #4EA833;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
                line-height: 1;
            }

            .td-not-paid {
                font-family: Signika;
                background: #F8B700;
                padding: 6px 7px;
                border-radius: 3px;
                color: #262626;
                border: none;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
            }

            .td-refund {
                font-family: Signika;
                background: #F4FAFF;
                padding: 6px 7px;
                border-radius: 3px;
                color: #0070DD;
                border: 1px solid #0070DD;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
            }

            .td-not-found {
                font-family: Signika;
                background: #FFF468;
                padding: 6px 7px;
                border-radius: 3px;
                color: #262626;
                border: none;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
            }

            .td-yellow {
                font-family: Signika;
                background: #fff468;
                padding: 8px;
                border-radius: 5px;
                color: #262626;
                border: none;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
                line-height: 1;
            }


            .td-grey-new {
                background: #d9d9d9;
                padding: 8px;
                border-radius: 3px;
                color: #7f7f7f;
                border: none;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
                line-height: 1;
                width: 130px;
            }

            .td-grey {
                background: #d7d7d7;
                /* padding: 6px 7px; */
                border-radius: 3px;
                color: #d7d7d7;
                border: 2px solid #d7d7d7;
                font-size: 8pt;
                /* width: fit-content; */
                width: 100%;
                text-align: center !important;
                height: 24px;
            }

            .td-remit {
                padding: 6px 10px;
                border-radius: 5px;
                border: 1px solid;
                font-size: 11pt;
                font-weight: bold;
                width: fit-content;
                background: #FBFBFB;
                color: #595959;
                border-color: #404040;
            }

            @media screen and (max-width: 1600px) {
                .td-green {
                    font-size: 7pt;
                }

                .td-green-new {
                    font-size: 7pt;
                }

                .td-paid {
                    font-size: 7pt;
                }

                .td-not-paid {
                    font-size: 7pt;
                }

                .td-refund {
                    font-size: 7pt;
                }

                .td-not-found {
                    font-size: 7pt;
                }

                .td-yellow {
                    font-size: 8pt;
                }


                .td-grey-new {
                    font-size: 8pt;
                }

                .td-grey {
                    font-size: 9pt;
                    height: 22.5px !important;
                }
            }

            /* .td-remit-m {
                                background: #4dd727;
                                color: #000;
                                border-color: #4dd727;
                            }

                            .td-remit-q {
                                background: #006fdd;
                                color: #fff;
                                border-color: #006fdd;
                            }

                            .td-remit-y {
                                background: #c41f3a;
                                color: #fff;
                                border-color: #c41f3a;
                            } */
        </style>


        <!-- Page Content -->
        <div class="con   no-print page-header py-2" id="">
            <!-- Full Table -->
            <div class="b   mb-0  ">

                <div class="block-content pt-0 mt-0">

                    <div class="TopArea"
                        style="position: sticky;
    padding-top: 8px;
    z-index: 1000;

    padding-bottom: 5px;">
                        <div class="row justify-content-end">



                            <div class="d-flex text-right col-lg-3 justify-content-end">

                                <a href="{{ url('remittances') }}" class="mr-3 text-dark d3   ">
                                    <img src="{{ asset('public/batch_icons/back.png') }}" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title=""
                                        data-original-title="Got to Remittances" width="23px"></a>

                                <a href="javascript:;" onclick="printDiv('printableArea')" class="mr-3 text-dark d3   "><img
                                        src="{{ asset('public/img/action-white-print.png') }}" width="23px"></a>

                                @if (@Auth::user()->role == 'admin')

                                    <a href="javascript:;" data-toggle="tooltip" data-title="Settings"
                                        class="mr-3 text-dark headerSetting d3   "><img
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
    @endsection



    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            let loadContent = () => {
                $.ajax({
                    type: "GET",
                    url: "{{ url('/remittance/report/remittance-status/get-content') }}",
                    data: {
                        "clients": JSON.stringify(JSON.parse(`{!! json_encode($clients) !!}`)),
                        "type": "{{ $type }}",
                        "month": "{{ $month }}",
                        "year": "{{ $year }}"
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

        });

        function printDiv(divId) {
            var content = document.getElementById(divId).innerHTML;
            var originalContent = document.body.innerHTML;

            var printStyle = `
        <style>
            @page {
                size: 8.5in 14in;
                margin: 0.5in 0.2cm;
            }
            body {
                margin: 0;
                padding: 0;
            }
        </style>
    `;

            document.body.innerHTML = printStyle + content;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
