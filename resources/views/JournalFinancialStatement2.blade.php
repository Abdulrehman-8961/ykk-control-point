@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')



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





        td, th {
            text-wrap: nowrap;
            overflow: hidden;
        }

        .page-footer {
            margin-bottom: 0 !important;
            font-size: 18px !important;
    font-weight: 500 !important;
        }
        .sheet-page {
            min-height: 92vh;

        }

        .sheet-page .page-block {
            display: flex;
            flex-direction: column;
            min-height: 92vh;
        }
/*
        .sheet-page .page-block {
              display: flex;
              flex-direction: column;

              min-height: 95vh;
              box-sizing: border-box;
            flex: 1;
            }

            .sheet-page {

                overflow-y: hidden;
                box-sizing: border-box;
                display: flex;
            } */

        @media only print {
            .sheet-page .page-block, .sheet-page {
                min-height: 100vh !important;
            }


.page-footer {
    padding-bottom: 3rem !important;
    margin-bottom: 0 !important;
}

            /* body {
                margin: 0;
            }
            .sheet-page {
                min-height: 95vh !important;
                page-break-before: always;
                margin: 0;
            }
            .sheet-page .page-block {
                min-height: 95vh !important;
            } */

            /* .sheet-page {
                page-break-before: always;
                min-height: 95vh !important;
            }

            .sheet-page .page-block {
                min-height: 95vh !important;

            } */

/*
            .sheet-page {
    page-break-inside: avoid;

  } */

  /* .sheet-page:not(:last-child) {
    page-break-after: always;
  } */

        }
    </style>


    <!-- Page Content -->
    <div class="con   no-print page-header py-2" id="">
        <!-- Full Table -->
        <div class="b   mb-0  ">

            <div class="block-content pt-0 mt-0">

                <div class="TopArea" style="position: sticky;
    padding-top: 8px;
    z-index: 1000;

    padding-bottom: 5px;">
                    <div class="row justify-content-end">



                        <div class="d-flex text-right col-lg-3 justify-content-end">
                            <a class="btn btn-dual  d2 "
                            href="{{url('/export-excel-financial-statement')}}?{{$_SERVER['QUERY_STRING']}}"
                            id="ExportJournals">
                            <img src="{{asset('public/img/ui-icon-export.png')}}" data-toggle="tooltip"
                                data-trigger="hover" data-placement="top" title="" data-original-title="Export"
                                style="width:25px">
                        </a>
                            <a href="javascript:;" onclick="window.print()" class="mr-3 text-dark d3   "><img
                                    src="{{asset('public/img/action-white-print.png')}}"
                                    <img src="{{asset('public/img/ui-icon-export.png')}}" data-toggle="tooltip"
                                        data-trigger="hover" data-placement="top" title="" data-original-title="Print" width="23px"></a>

                            @if(@Auth::user()->role=='admin')

                            <a href="javascript:;" data-toggle="tooltip" data-title="Settings"
                                class="mr-3 text-dark headerSetting d3   "><img
                                    src="{{asset('public/img/ui-icon-settings.png')}}" width="23px"></a>

                            @endif
                            <!-- User Dropdown -->
                            <div class="dropdown d-inline-block">
                                <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">


                                    @if(Auth::user()->user_image=='')
                                    <img class="img-avatar imgAvatar img-avatar48"
                                        src="{{asset('public')}}/dashboard_assets/media/avatars/avatar2.jpg" alt="">
                                    @else
                                    <img class="img-avatar imgAvatar img-avatar48"
                                        src="{{asset('public/client_logos/')}}/{{Auth::user()->user_image}}" alt="">

                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right p-0"
                                    aria-labelledby="page-header-user-dropdown">

                                    <div class="p-2">
                                        @auth
                                        <a class="dropdown-item" href="{{url('change-password')}}">
                                            <i class="far fa-fw fa-user mr-1"></i> My Profile
                                        </a>






                                        <!-- END Side Overlay -->
                                        <form id="logout-form" class="mb-0" method="post" action="{{url('logout')}}">
                                            @csrf
                                        </form>
                                        <div role="separator" class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="javascript:;"
                                            onclick="document.getElementById('logout-form').submit()">
                                            <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                        </a>
                                        @else
                                        <a class="dropdown-item" href="{{url('/login')}}">
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














    <?php

function getFiscalYearEnd($fiscalStart)
    {
        $parts = explode('-', $fiscalStart);
        $year = intval($parts[0]);
        $month = intval($parts[1]);
        $day = intval($parts[2]);

        $fiscalYearEnd = new DateTime(($year + 1) . '-' . $month . '-' . $day);
        $fiscalYearEnd->modify('-1 day');

        $fiscalYear = $fiscalYearEnd->format('Y');
        $fiscalMonth = $fiscalYearEnd->format('n');
        $fiscalDay = $fiscalYearEnd->format('j');

        //$fiscalYearEndFormatted = 'Fiscal Year End ' . $this->monthToStringShort($fiscalMonth) . ' ' . $fiscalYear;
        return $fiscalYear . '-' . $fiscalMonth . '-' . $fiscalDay ;
    }


    function rounding($number, $rounding)
    {
        if($rounding === "0") {
            return round($number);
        }
        if($rounding === "000") {
        // Move floating point 3 places left
        $moved = $number/1000;

    // Round to nearest hundred
$rounded = round($moved);

return $rounded;
        }
        if($rounding === "000000") {

        // Move floating point 3 places left
        $moved = $number/1000000;

    // Round to nearest hundred
$rounded = round($moved);

return $rounded;
        }
        return $number;
    }

    function format($number)
    {
        $formatted = number_format($number, 2, '.', '');
        if($formatted == (int)$formatted) {
            return number_format($number);
        }
        return number_format($number, 2);
    }


    $total_assets_previous_fyear = 0;
    $total_assets_current_fyear = 0;
    $total_liabilities_previous_fyear = 0;
    $total_liabilities_current_fyear = 0;


?>



    <div class="content  ">
        <!-- Page Content -->
        <div class="row px-0">


            <div class="col-lg-12    sheet-page d-flex flex-column" id="showData1" >
                <div class="block new-block position-relative  5 page-block" >

                    <div class="d-flex text-nowrap flex-column h-100 align-items-center justify-content-center">
                        <p class=" text-center mb-0 section-header" style="font-size: 24px !important;">Consultation AmaltiTEK</p>
                        <p class=" text-center mb-0 " style="font-size: 24px !important;font-weight: normal !important">Financial Statements</p>
                        <p class=" text-center mb-0 section-header" style="font-size: 20px !important;">{{date("F d, Y",
                            strtotime(getFiscalYearEnd($client->fiscal_start)))}}</p>
                    </div>

                    <!-- page footer --->
                    <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>
        </div>

    </div><!--cover page-->




    <div class="col-lg-12    sheet-page d-flex flex-column" id="showData1" >
        <div class="block new-block position-relative  5 page-block"  style="padding-left: 7%;padding-right: 7%;padding-top: 7% !important;">

            <div class="d-flex text-nowrap flex-column align-items-center justify-content-center" style="padding-bottom: 9% !important;">
                <p class=" text-center mb-0 section-header" style="font-size: 18px !important;">{{$system_settings->company}}</p>
                <p class=" text-center mb-0 " style="font-size: 17px !important;font-weight: 500 !important">{{$system_settings->firstname . ' ' . $system_settings->lastname}}</p>
                <p class=" text-center mb-0 section-header" style="font-size: 17px !important;font-weight: 500 !important;white-space: pre-line;">{{$system_settings->address}}</p>
                <p class=" text-center mb-0 section-header" style="font-size: 17px !important;font-weight: 500 !important;">Tel: {{$system_settings->telephone}} Fax: {{$system_settings->fax}}</p>
            </div>
            <div class="">
                <p class=" mb-0 section-header" style="font-size: 18px !important;">Notice to reader:</p>
                <p class="mb-2 section-header" style="font-size: 17px !important;font-weight:500 !important;;">We have compiled the balance sheet of <b class="text-danger">{{$client->company}}</b> as at <b class="text-danger">{{date("F d, Y",
                    strtotime(getFiscalYearEnd($client->fiscal_start)))}}</b> and the statements of income and
                    reattained earnings and changes in the financial position for the year then ended from information provided by the director of
                    the company. We have not audited, reviewed or otherwise attempted to verify the accuracy of completeness of such
                    information. Accordingly, readers are cautioned that these statements may not be appropriate for their purposes</p>
                    <p class=" section-header mb-5" style="font-size: 17px !important;font-weight:500 !important;">{{$system_settings->firstname . ' ' . $system_settings->lastname . ' ' . $system_settings->designation}}</p>
            </div>

            <div>
                <p class=" mb-0 section-header pl-2" style="font-size: 17px !important;">{{$system_settings->city}}, {{$system_settings->province}}</p>
                <p class=" mb-0 section-header pl-2" style="font-size: 17px !important;font-weight:500 !important;">{{date("F d, Y")}}</p>
            </div>


</div>

</div><!--notice to reader page-->




            <div class="col-lg-12    sheet-page d-flex flex-column" id="showData1" >
                <div class="block new-block position-relative  5 page-block" >
                    <div class="block-header py-0 d-flex justify-content-between align-items-start"
                        style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

                        <div>
                            <a class="  section-header">{{$client->company}}
                            </a>
                            <p style="font-size: 11pt !important;">{{date("d-M-Y",
                                strtotime(getFiscalYearEnd($client->fiscal_start)))}}</p>
                        </div>


                        <a class="  section-header">Unaudited Balance Sheet
                        </a>
                    </div>
                    <div class="block-content pb-0   "
                        style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                            <div class="col-sm-12"><!--Current Assets-->
                                <div class="d-flex text-nowrap">
                                    <div>
                                        <p class=" pr-1 mb-0 ">CURRENT ASSETS</p>
                                    </div>
                                    <hr class="w-100" style="border-color: #595959!important">
                                </div>
                                <div class="row">
                                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                        <table class="table border-0 table-period">
                                            <thead>
                                                <tr>
                                                    <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $total_current_assets_previous_fyear = 0;
                                                    $total_current_assets_current_fyear = 0;
                                                    ?>
                                                @foreach ($current_assets as $j)
                                                <?php
                                                   $amnt_previous_fyear = 0.00;
                                                   $amnt_current_fyear = 0.00;
                                                   if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                                    $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                                   } else {
                                                    $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                                   }
                                                   if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                                    $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                                   } else {
                                                    $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                                   }
                                                   $total_current_assets_previous_fyear += $amnt_previous_fyear;
                                                   $total_current_assets_current_fyear += $amnt_current_fyear;

                                                ?>
                                                <tr>
                                                    <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">
                                                        {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">
                                                        {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <?php
                                                    $total_assets_previous_fyear += $total_current_assets_previous_fyear;
                                                    $total_assets_current_fyear += $total_current_assets_current_fyear;
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td style="border-top: 0 !important;font-weight:600;">
                                                        Total Current Assets
                                                    </td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">

                                                        <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_assets_previous_fyear)}}</span>
                                                    </td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border: 0 !important;">
                                                        <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_assets_current_fyear)}}</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div><!--Current Assets-->


                            <div class="col-sm-12"><!--Capital Assets-->
                                <div class="d-flex text-nowrap">
                                    <div>
                                        <p class=" pr-1 mb-0 ">CAPITAL ASSETS</p>
                                    </div>
                                    <hr class="w-100" style="border-color: #595959!important">
                                </div>
                                <div class="row">
                                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                        <table class="table border-0 table-period">
                                            <thead>
                                                <tr>
                                                    <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $total_capital_assets_previous_fyear = 0;
                                                    $total_capital_assets_current_fyear = 0;
                                                    ?>
                                                @foreach ($capital_assets as $j)
                                                <?php
                                                   $amnt_previous_fyear = 0.00;
                                                   $amnt_current_fyear = 0.00;
                                                   if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                                    $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                                   } else {
                                                    $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                                   }
                                                   if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                                    $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                                   } else {
                                                    $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                                   }
                                                   $total_capital_assets_previous_fyear += $amnt_previous_fyear;
                                                   $total_capital_assets_current_fyear += $amnt_current_fyear;
                                                ?>
                                                <tr>
                                                    <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">
                                                        {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">
                                                        {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear )}}
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <?php
                                                    $total_assets_previous_fyear += $total_capital_assets_previous_fyear;
                                                    $total_assets_current_fyear += $total_capital_assets_current_fyear;
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td style="border-top: 0 !important;font-weight:600;">
                                                        Total Capital Assets
                                                    </td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">

                                                        <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_capital_assets_previous_fyear)}}</span>
                                                    </td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border: 0 !important;">
                                                        <span
                                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_capital_assets_current_fyear)}}</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div><!--Capital Assets-->


                            <div class="col-sm-12"><!--Long Term Assets-->
                                <div class="d-flex text-nowrap">
                                    <div>
                                        <p class=" pr-1 mb-0 ">LONG-TERM ASSETS</p>
                                    </div>
                                    <hr class="w-100" style="border-color: #595959!important">
                                </div>
                                <div class="row">
                                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                        <table class="table border-0 table-period">
                                            <thead>
                                                <tr>
                                                    <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $total_long_term_assets_previous_fyear = 0;
                                                    $total_long_term_assets_current_fyear = 0;
                                                    ?>
                                                @foreach ($long_term_assets as $j)
                                                <?php
                                                   $amnt_previous_fyear = 0.00;
                                                   $amnt_current_fyear = 0.00;
                                                   if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                                    $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                                   } else {
                                                    $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                                   }
                                                   if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                                    $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                                   } else {
                                                    $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                                   }
                                                   $total_current_assets_previous_fyear += $amnt_previous_fyear;
                                                   $total_current_assets_current_fyear += $amnt_current_fyear;
                                                ?>
                                                <tr>
                                                    <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">
                                                        {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">
                                                        {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <?php
                                                    $total_assets_previous_fyear += $total_long_term_assets_previous_fyear;
                                                    $total_assets_current_fyear += $total_long_term_assets_current_fyear;
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td style="border-top: 0 !important;font-weight:600;">
                                                        Total Long-Term Assets
                                                    </td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">
                                                        <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_assets_previous_fyear)}}</span>
                                                    </td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border: 0 !important;">
                                                        <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_assets_current_fyear)}}</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div><!--Long Term Assets-->


                            <div class="col-sm-12"><!--Total Assets-->

                                <div class="row">
                                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                        <table class="table border-0 table-period">

                                            <tfoot>
                                                <tr>
                                                    <td style="border-top: 0 !important;font-weight:600;">
                                                        Total Assets
                                                    </td>

                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border:0 !important;">

                                                             <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                                <span
                                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_assets_previous_fyear)}}</span>
                                                            </div>
                                                    </td>
                                                    <td class="text-right"
                                                        style="font-weight:600;padding:0;border: 0 !important;">
                                                        <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                            <span
                                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_assets_current_fyear)}}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div><!--Long Total Assets-->

                    </div>

                    <!-- page footer --->
                    <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>
        </div>

    </div><!--Balance sheet assets-->



    <div class="col-lg-12   sheet-page" id="showData2" >
        <div class="block new-block position-relative  5 page-block" >
            <div class="block-header py-0 d-flex justify-content-between align-items-start"
                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

                <div>
                    <a class="  section-header">{{$client->company}}
                    </a>
                    <p style="font-size: 11pt !important;">{{date("d-M-Y",
                        strtotime(getFiscalYearEnd($client->fiscal_start)))}}</p>
                </div>


                <a class="  section-header">Unaudited Balance Sheet
                </a>
            </div>
            <div class="block-content pb-0   "
                style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                    <div class="col-sm-12"><!--Current Liabilities-->
                        <div class="d-flex text-nowrap">
                            <div>
                                <p class=" pr-1 mb-0 ">CURRENT LIABILITIES</p>
                            </div>
                            <hr class="w-100" style="border-color: #595959!important">
                        </div>
                        <div class="row">
                            <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                <table class="table border-0 table-period">
                                    <thead>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $total_current_liabilities_previous_fyear = 0;
                                            $total_current_liabilities_current_fyear = 0;
                                            ?>
                                        @foreach ($current_liabilities as $j)
                                        <?php
                                           $amnt_previous_fyear = 0.00;
                                           $amnt_current_fyear = 0.00;
                                           if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                            $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                           } else {
                                            $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                           }
                                           if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                            $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                           } else {
                                            $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                           }
                                           $total_current_liabilities_previous_fyear += $amnt_previous_fyear;
                                           $total_current_liabilities_current_fyear += $amnt_current_fyear;
                                        ?>
                                        <tr>
                                            <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                            </td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <?php
                                            $total_liabilities_previous_fyear += $total_current_liabilities_previous_fyear;
                                            $total_liabilities_current_fyear += $total_current_liabilities_current_fyear;
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-top: 0 !important;font-weight:600;">
                                                Total Current Liabilities
                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">

                                                <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_liabilities_previous_fyear)}}</span>
                                            </td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border: 0 !important;">
                                                <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_liabilities_current_fyear)}}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div><!--Current Liabilities-->


                    <div class="col-sm-12"><!--Capital Assets-->
                        <div class="d-flex text-nowrap">
                            <div>
                                <p class=" pr-1 mb-0 ">LONG-TERM LIABILITIES</p>
                            </div>
                            <hr class="w-100" style="border-color: #595959!important">
                        </div>
                        <div class="row">
                            <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                <table class="table border-0 table-period">
                                    <thead>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $total_long_term_liabilities_previous_fyear = 0;
                                            $total_long_term_liabilities_current_fyear = 0;
                                            ?>
                                        @foreach ($long_term_liabilities as $j)
                                        <?php
                                           $amnt_previous_fyear = 0.00;
                                           $amnt_current_fyear = 0.00;
                                           if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                            $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                           } else {
                                            $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                           }
                                           if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                            $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                           } else {
                                            $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                           }
                                           $total_long_term_liabilities_previous_fyear += $amnt_previous_fyear;
                                           $total_long_term_liabilities_current_fyear += $amnt_current_fyear;
                                        ?>
                                        <tr>
                                            <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                            </td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <?php
                                            $total_liabilities_previous_fyear += $total_long_term_liabilities_previous_fyear;
                                            $total_liabilities_current_fyear += $total_long_term_liabilities_current_fyear;
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-top: 0 !important;font-weight:600;">
                                                Total Long-Term Liabilities
                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">

                                                <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_liabilities_previous_fyear)}}</span>
                                            </td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border: 0 !important;">
                                                <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_liabilities_current_fyear)}}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div><!--Capital Assets-->


                    <div class="col-sm-12"><!--Equity-->
                        <div class="d-flex text-nowrap">
                            <div>
                                <p class=" pr-1 mb-0 ">EQUITY</p>
                            </div>
                            <hr class="w-100" style="border-color: #595959!important">
                        </div>
                        <div class="row">
                            <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                <table class="table border-0 table-period">
                                    <thead>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $total_equity_previous_fyear = 0;
                                            $total_equity_current_fyear = 0;
                                            ?>
                                        @foreach ($equity as $j)
                                        <?php
                                           $amnt_previous_fyear = 0.00;
                                           $amnt_current_fyear = 0.00;
                                           if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                            $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                           } else {
                                            $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                           }
                                           if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                            $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                           } else {
                                            $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                           }
                                           $total_equity_previous_fyear += $amnt_previous_fyear;
                                           $total_equity_current_fyear += $amnt_current_fyear;
                                        ?>
                                        <tr>
                                            <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                            </td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <?php
                                            $total_liabilities_previous_fyear += $total_equity_previous_fyear;
                                            $total_liabilities_current_fyear += $total_equity_current_fyear;
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td style="border-top: 0 !important;font-weight:600;">
                                                Total Equity
                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">

                                                <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_equity_previous_fyear)}}</span>
                                            </td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border: 0 !important;">
                                                <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_equity_current_fyear)}}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div><!--Equity-->


                    <div class="col-sm-12"><!--Total Liabilities-->

                        <div class="row">
                            <div class="col-md-12 " style="    padding-left: 4rem!important;">
                                <table class="table border-0 table-period">

                                    <tfoot>
                                        <tr>
                                            <td style="border-top: 0 !important;font-weight:600;">
                                                Total Liabilities
                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">

                                                     <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                        <span
                                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_previous_fyear)}}</span>
                                                    </div>
                                            </td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border: 0 !important;">
                                                <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_current_fyear)}}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div><!--Total Liabilities-->

            </div>
 <!-- page footer --->
 <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>

</div>

</div><!--Balance sheet liability-->






<div class="col-lg-12   sheet-page " id="showData3" >
    <div class="block new-block position-relative  5 page-block" >
        <div class="block-header py-0 d-flex justify-content-between align-items-start"
            style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

            <div>
                <a class="  section-header">{{$client->company}}
                </a>
                <p style="font-size: 11pt !important;">{{date("d-M-Y",
                    strtotime(getFiscalYearEnd($client->fiscal_start)))}}</p>
            </div>


            <a class="  section-header">Unaudited Balance Sheet
            </a>
        </div>
        <div class="block-content pb-0   "
            style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                <div class="col-sm-12"><!--Retained Earnings-->
                    <div class="d-flex text-nowrap">
                        <div>
                            <p class=" pr-1 mb-0 ">RETAINED EARNINGS</p>
                        </div>
                        <hr class="w-100" style="border-color: #595959!important">
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">
                                <thead>
                                    <tr>
                                        <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_retained_earnings_previous_fyear =0 ;
                                        $total_retained_earnings_current_fyear =0;
                                        ?>
                                    @foreach ($retained_earnings as $j)
                                    <?php
                                           $amnt_previous_fyear = 0.00;
                                           $amnt_current_fyear = 0.00;
                                           if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                            $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                           } else {
                                            $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                           }
                                           if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                            $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                           } else {
                                            $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                           }
                                           $total_retained_earnings_previous_fyear += $amnt_previous_fyear;
                                           $total_retained_earnings_current_fyear += $amnt_current_fyear;
                                        ?>
                                        <tr>
                                            <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                            </td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Total retained earnings
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_retained_earnings_previous_fyear)}}</span>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_retained_earnings_current_fyear)}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!--Total Retained Earnings-->


                <div class="col-sm-12"><!--Total Total Retained Earnings + Liabilities-->

                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">

                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Total retained earnings and Liabilities
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                                 <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_previous_fyear + $total_retained_earnings_previous_fyear)}}</span>
                                                </div>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_current_fyear + $total_retained_earnings_current_fyear)}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!--Total Retained Earnings + Liabilities-->

        </div>
        <div class="d-flex flex-column " style="padding-left: 7%;margin-top: 15rem !important;">
            <div style="width:200px;">
                <p class="mb-5" style="font-weight: 600!important;font-family:Signika;color:#595959 !important;border-top:1px solid #595959 !important;padding-top:1rem;">
                    Approved By
                </p>
            </div>
            <div style="width: 200px;">
                <p class="mt-4" style="font-weight: 600!important;font-family:Signika;color:#595959 !important;border-top:1px solid #595959 !important;padding-top:1rem;">
                    Date
                </p>
            </div>
        </div>
 <!-- page footer --->
 <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>

</div>

</div><!--Balance sheet Retained Earnings-->








<div class="col-lg-12   sheet-page " id="showData4" >
    <div class="block new-block position-relative  5 page-block" >
        <div class="block-header py-0 d-flex justify-content-between align-items-start"
            style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

            <div>
                <a class="  section-header">{{$client->company}}
                </a>
                <p style="font-size: 11pt !important;">{{date("d-M-Y",
                    strtotime(getFiscalYearEnd($client->fiscal_start)))}}</p>
            </div>


            <a class="  section-header">Unaudited Statement of Income
            </a>
        </div>
        <div class="block-content pb-0   "
            style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                <div class="col-sm-12"><!--Revenue-->
                    <div class="d-flex text-nowrap">
                        <div>
                            <p class=" pr-1 mb-0 ">REVENUE</p>
                        </div>
                        <hr class="w-100" style="border-color: #595959!important">
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">
                                <thead>
                                    <tr>
                                        <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_revenue_previous_fyear = 0;
                                        $total_revenue_current_fyear = 0;
                                        ?>
                                    @foreach ($revenue as $j)
                                    <?php
                                       $amnt_previous_fyear = 0.00;
                                       $amnt_current_fyear = 0.00;
                                       if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                        $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                       } else {
                                        $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                       }
                                       if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                        $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                       } else {
                                        $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                       }
                                       $total_revenue_previous_fyear += $amnt_previous_fyear;
                                       $total_revenue_current_fyear += $amnt_current_fyear;
                                    ?>
                                    <tr>
                                        <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                        </td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Total Revenue
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_previous_fyear)}}</span>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_current_fyear)}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!--Revenue-->


                <div class="col-sm-12"><!--Cost of Sales-->
                    <div class="d-flex text-nowrap">
                        <div>
                            <p class=" pr-1 mb-0 ">COST OF SALES</p>
                        </div>
                        <hr class="w-100" style="border-color: #595959!important">
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">
                                <thead>
                                    <tr>
                                        <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_cost_of_sales_previous_fyear = 0;
                                        $total_cost_of_sales_current_fyear = 0;
                                        ?>
                                    @foreach ($cost_of_sales as $j)
                                    <?php
                                       $amnt_previous_fyear = 0.00;
                                       $amnt_current_fyear = 0.00;
                                       if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                        $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                       } else {
                                        $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                       }
                                       if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                        $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                       } else {
                                        $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                       }
                                       $total_cost_of_sales_previous_fyear += $amnt_previous_fyear;
                                       $total_cost_of_sales_current_fyear += $amnt_current_fyear;
                                    ?>
                                    <tr>
                                        <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                        </td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Total Cost of Sales
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_cost_of_sales_previous_fyear)}}</span>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_cost_of_sales_current_fyear)}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">
                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Gross profit (loss)
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                                 <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_previous_fyear - $total_cost_of_sales_previous_fyear)}}</span>
                                                </div>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_current_fyear - $total_cost_of_sales_current_fyear)}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div><!-- Gross profile -->
                </div><!--Cost of sales-->


                <div class="col-sm-12"><!--Expensed-->
                    <div class="d-flex text-nowrap">
                        <div>
                            <p class=" pr-1 mb-0 ">EXPENSES</p>
                        </div>
                        <hr class="w-100" style="border-color: #595959!important">
                    </div>
                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">
                                <thead>
                                    <tr>
                                        <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear-1}}</td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">{{$fyear}}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $total_expenses_previous_fyear = 0;
                                        $total_expenses_current_fyear = 0;
                                        ?>
                                    @foreach ($operating_expenses as $j)
                                    <?php
                                       $amnt_previous_fyear = 0.00;
                                       $amnt_current_fyear = 0.00;
                                       if($j->total_debits_previous_fyear > $j->total_credits_previous_fyear) {
                                        $amnt_previous_fyear = rounding($j->total_debits_previous_fyear - $j->total_credits_previous_fyear, $rounding);
                                       } else {
                                        $amnt_preivous_fyear = rounding($j->total_credits_previous_fyear - $j->total_debits_previous_fyear, $rounding);
                                       }
                                       if($j->total_debits_current_fyear > $j->total_credits_current_fyear) {
                                        $amnt_current_fyear = rounding($j->total_debits_current_fyear - $j->total_credits_current_fyear, $rounding);
                                       } else {
                                        $amnt_current_fyear = rounding($j->total_credits_current_fyear - $j->total_debits_current_fyear, $rounding);
                                       }
                                       $total_expenses_previous_fyear += $amnt_previous_fyear;
                                       $total_expenses_current_fyear += $amnt_current_fyear;
                                    ?>
                                    <tr>
                                        <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                        </td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Total operating expenses
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_previous_fyear)}}</span>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_current_fyear)}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Total expenses
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_previous_fyear + $total_cost_of_sales_previous_fyear)}}</span>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_current_fyear + $total_cost_of_sales_current_fyear)}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!--Expenses-->


                <div class="col-sm-12"><!--net income-->

                    <div class="row">
                        <div class="col-md-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">

                                <tfoot>
                                    <tr>
                                        <td style="border-top: 0 !important;font-weight:600;">
                                            Net Income
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">

                                                 <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_previous_fyear - ($total_expenses_previous_fyear + $total_cost_of_sales_previous_fyear))}}</span>
                                                </div>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_current_fyear - ($total_expenses_current_fyear + $total_cost_of_sales_current_fyear))}}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div><!--Net Income-->

        </div>
 <!-- page footer --->
 <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>

</div>

</div><!--Statement of Income-->







    </div>














    <form class="mb-0 pb-0" action="{{url('end-gifi')}}" method="post">
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
                                    <textarea class="form-control" rows="5" required="" name="reason"
                                        id="reason"></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="block-content block-content-full   pt-4"
                            style="padding-left: 9mm;padding-right: 9mm">
                            <button type="submit" class="btn mr-3 btn-new  ">Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>

                        </div>
                    </div>

                </div>
            </div>
        </div>


    </form>









    <form class="mb-0 pb-0" id="form-add-tax" action="{{url('insert-gifi')}}" method="post">
        @csrf
        <div class="modal fade" id="AddTaxModal" tabindex="-1" role="dialog" data-backdrop="static"
            aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Add Gifi Account</span>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="block-content new-block-content pt-0 pb-0 ">



                            <div class="row justify-content- form-group  push">


                                <div class="col-lg-3">
                                    <label class="col-form-label mandatory">Account Type</label>
                                </div>

                                <div class="col-lg-6">

                                    <select type="" name="account_type" class="form-control" placeholder="">
                                        <option value="Asset">Asset</option>
                                        <option value="Liability">Liability</option>
                                        <option value="Retained Earning">Retained Earning</option>
                                        <option value="Income">Income</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row form-group  ">
                                <div class="col-lg-3">

                                    <label class="col-form-label mandatory">Sub Type</label>

                                </div>

                                <div class="col-lg-6 ">

                                    <select type="" name="sub_account_type" class="form-control" placeholder="">
                                        <?php
    $sub_account_type=DB::Table('sub_account_type')->where('account_type','Asset')->get();
foreach($sub_account_type as $s){
  echo '<option value="'.$s->sub_type.'"  data-min="'.$s->min.'"  data-max="'.$s->max.'">'.$s->sub_type.'</option>';
}
?>

                                    </select>
                                </div>

                            </div>
                            <div class="row form-group  ">
                                <div class="col-lg-3">

                                    <label class="col-form-label mandatory">Account No.</label>

                                </div>

                                <div class="col-lg-4 ">

                                    <input type="" name="account_no" class="form-control"
                                        placeholder="4-digit numeric code">

                                </div>

                            </div>

                            <div class="row form-group  ">
                                <div class="col-lg-3">

                                    <label class="col-form-label mandatory">Description</label>

                                </div>

                                <div class="col-lg-6 ">

                                    <input type="" name="description" class="form-control"
                                        placeholder="Account description">

                                </div>

                            </div>

                            <div class="row form-group  ">
                                <div class="col-lg-3">

                                    <label class="col-form-label  ">Note</label>

                                </div>

                                <div class="col-lg-6 ">

                                    <textarea type="" name="note" class="form-control" rows="5"
                                        placeholder="Gifi note"></textarea>

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
                        <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

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
                        <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

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
                            <input type="file" class="  attachment" multiple="" style="" id="attachment"
                                name="attachment" placeholder="">
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



    <form id="filterJournalForm" method="GET" action="{{url('/journals')}}" class="mb-0 pb-0">
        <div class="modal fade" id="filterJournalModal" tabindex="-1" role="dialog" data-backdrop="static"
            aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Filters</span>
                            <div class="block-options">
                                <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button> -->
                            </div>
                        </div>

                        <div class="block-content new-block-content pt-0 pb-0 ">




                            <div class="row">


                                <label class="col-sm-3">Account Type</label>
                                <div class="col-sm-9 form-group">

                                    <select type="" name="filter_account_type" class="form-control" placeholder="">
                                        <option value="Asset">Asset</option>
                                        <option value="Liability">Liability</option>
                                        <option value="Retained Earning">Retained Earning</option>
                                        <option value="Income">Income</option>
                                    </select>
                                </div>



                                <label class="col-sm-3">Sub Type</label>
                                <div class="col-sm-9 form-group">
                                    <select type="" name="filter_sub_account_type" class="form-control" placeholder="">
                                        <?php
                                               $sub_account_type=DB::Table('sub_account_type')->where('account_type','Asset')->get();
                                           foreach($sub_account_type as $s){
                                             echo '<option value="'.$s->sub_type.'"  data-min="'.$s->min.'"  data-max="'.$s->max.'">'.$s->sub_type.'</option>';
                                           }
                                           ?>

                                    </select>
                                </div>

                                <label class="col-sm-3">Fiscal Year</label>
                                <div class="col-sm-9 form-group">


                                    <select class="form-control" id="filter_fiscal_year" name="filter_fiscal_year">

                                    </select>
                                </div>
                                <label class="col-sm-3" for="example-hf-email">Period</label>
                                <div class="col-sm-9 form-group">

                                    <select type="" class="form-control    selectpicker " id="filter-period"
                                        data-style="btn-outline-light border text-dark" data-actions-box="true"
                                        data-live-search="true" title="All" value="" name="filter_period[]" multiple="">

                                    </select>
                                </div>
                                <label class="  col-sm-3 " for="example-hf-email">Source</label>
                                <div class="col-sm-9 form-group">

                                    <select type="" class="form-control    selectpicker " id="filter-source"
                                        data-style="btn-outline-light border text-dark" data-actions-box="true"
                                        data-live-search="true" title="All" value="" name="filter_source[]" multiple="">

                                    </select>
                                </div>
                                <label class="  col-sm-3 " for="example-hf-email">Ref No.</label>
                                <div class="col-sm-9 form-group">

                                    <select type="" class="form-control    selectpicker " id="filter-ref"
                                        data-style="btn-outline-light border text-dark" data-actions-box="true"
                                        data-live-search="true" title="All" value="" name="filter_ref[]" multiple="">

                                    </select>
                                </div>

                                <label class=" col-sm-3  " for="example-hf-email">Account No</label>
                                <div class="col-sm-9 form-group">

                                    <select type="" class="form-control    selectpicker " id="filter_account"
                                        data-style="btn-outline-light border text-dark" data-actions-box="true"
                                        data-live-search="true" title="All" value="" name="filter_account[]"
                                        multiple="">


                                    </select>
                                </div>







                            </div>

                        </div>
                        <div class="block-content block-content-full   pt-4"
                            style="padding-left: 9mm;padding-right: 9mm">
                            <button type="submit" class="btn mr-3 btn-new">Apply</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>

                            <a href="{{url('/journals')}}" class="btn     btn-new-secondary float-right d-none" style="background: black;
                                    color: goldenrod;">Clear Filters</a>

                            <a href="{{url('/journals')}}" class="btn     btn-new-secondary float-right" style="">Clear
                                Filters</a>
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
<script src="{{asset('public/dashboard_assets/js/dashmix.app.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>



<script type="text/javascript">
    $(function(){
    Dashmix.helpers('rangeslider')
   @if(Session::has('success'))
  Dashmix.helpers('notify', {align: 'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> {{Session::get('success')}}', delay: 5000});
             @endif

function showData(id){
    $('.c-active').removeClass('c-active');
    if(id){
        $('.viewContent[data='+id+']').addClass('c-active');
    $('.c4').css({'backgroundColor':'#D9D9D9','color':'#595959','borderColor':'#595959'})
        $('.c4[data='+id+']').css({'backgroundColor':'#97C0FF','color':'#595959','borderColor':'#595959'})
        }
        $.ajax({
            type:'get',
            data:{id:id},
            url:'{{url('get-gifi-content')}}',
                dataType:'json',
    beforeSend() {
                      Dashmix.layout('header_loader_on');

                    },

        success:function(res){

             Dashmix.layout('header_loader_off');
                        $('#showData').html(res); $('.tooltip').tooltip('hide');


$('[data-toggle=tooltip]').tooltip();
            }
        })
}

$('#slider1').change(function(){
if($(this).val()==1){
$('.TaxDiv1').removeClass('d-none')
$('.TaxDiv2').addClass('d-none')
$('input[name=tax_label_2]').val('')
$('input[name=tax_rate_2]').val('')
$('#applied_to_tax1').prop('checked',false);
}
else if($(this).val()==2){
$('.TaxDiv1').removeClass('d-none')
$('.TaxDiv2').removeClass('d-none')
}
else{
$('.TaxDiv1').addClass('d-none')
$('.TaxDiv2').addClass('d-none')
$('input[name=tax_label_2]').val('')
$('input[name=tax_rate_2]').val('')
$('#applied_to_tax1').prop('checked',false);
$('input[name=tax_label_1]').val('')
$('input[name=tax_rate_1]').val('')
}
})
$(document).on('change','.slider2',function(e){
if($(this).val()==1){
$('.TaxDiv1_edit').removeClass('d-none')
$('.TaxDiv2_edit').addClass('d-none')
$('input[name=tax_label_2_edit]').val('')
$('input[name=tax_rate_2_edit]').val('')
$('#applied_to_tax1_edit').prop('checked',false);
}
else if($(this).val()==2){
$('.TaxDiv1_edit').removeClass('d-none')
$('.TaxDiv2_edit').removeClass('d-none')
}
else{
$('.TaxDiv1_edit').addClass('d-none')
$('.TaxDiv2_edit').addClass('d-none')
$('input[name=tax_label_2_edit]').val('')
$('input[name=tax_rate_2_edit]').val('')
$('#applied_to_tax1_edit').prop('checked',false);
$('input[name=tax_label_1_edit]').val('')
$('input[name=tax_rate_1_edit]').val('')
}
})


$('#form-add-tax').submit(function(e){
    e.preventDefault();

    var account_no=$('input[name=account_no]').val();
var description=$('input[name=description]').val()
 var min=parseInt($('option:selected',$('select[name=sub_account_type]')).attr('data-min'))
  var max=parseInt($('option:selected',$('select[name=sub_account_type]')).attr('data-max'))
  var check=1;
$.ajax({
    type:'get',
    data:{account_no:account_no},
    url:'{{url('check-gifi')}}',
    async:false,
    success:function(res){
        if(res==1){

                check=0;
        }
    }
})

var tele_regex1 =/^.{0,65}$/;
if(check==0){
        Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > GIFI account entered already exists.', delay: 5000});
}
else if(account_no==''){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > Please enter value for Account No.', delay: 5000});
}
 else if(account_no<min || account_no>max ){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > GIFI account number not in valid range.', delay: 5000});
}
 else if(description=='' ){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > Please enter value for Description.', delay: 5000});
}
else if(!tele_regex1.test(description)  ){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > Description should be max 65 chars.', delay: 5000});
}


else{
$('#form-add-tax')[0].submit()
}
})


var attachments_file=[];

var commentArray=[];
var comment_key_count=0;

var attachmentArray=[];
var attachment_key_count=0;
$(document).on('click','.btnEdit',function(){
var id=$(this).attr('data');
  $('.c-active').removeClass('c-active');
    if(id){
        $('.viewContent[data='+id+']').addClass('c-active');
    $('.c4').css({'backgroundColor':'#D9D9D9','color':'#595959','borderColor':'#595959'})
        $('.c4[data='+id+']').css({'backgroundColor':'#97C0FF','color':'#595959','borderColor':'#595959'})
        }
        $.ajax({
            type:'get',
            data:{id:id},
            url:'{{url('get-gifi-edit-content')}}',

    beforeSend() {
                      Dashmix.layout('header_loader_on');

                    },

        success:function(res){

             Dashmix.layout('header_loader_off');
             attachments_file=[]
             commentArray=[];
             comment_key_count=0 ;
             attachmentArray=[];
             attachment_key_count=0
                        $('#showData').html(res);
                         $('.tooltip').tooltip('hide');
                        showCommentsAjax(id)
 Dashmix.helpers('rangeslider')
$('[data-toggle=tooltip]').tooltip();
            }
        })


})


$(document).on('click','#btnExport',function(){
        var col=$('#columns').val();

        if(col!=''){
    var form=$('#exportform');
   if (form.attr("action") === undefined){
        throw "form does not have action attribute"
    }


    let url = form.attr("action");
var action='';
    if (url.includes("?") === false) {
   let index = url.indexOf("?");
        action = url
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}


        }
 else{

    let index = url.indexOf("?");
      action = url.slice(0, index)
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}
}
    form.attr("action", action)
 Dashmix.helpers('notify', {align: 'center', message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Export Complete.  ', delay: 5000});
    form.submit();
    $('#ExportModal').modal('hide')
     }
     else{

     }
})




function updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}

$(document).on('click','.viewContent',function() {
var id=$(this).attr('data');
  var oldURL = window.location.href;
            var type = id;

            if (history.pushState) {

var newUrl=updateQueryStringParameter(oldURL,'id',id)
                window.history.pushState({ path: newUrl }, '', newUrl);
            }


showData(id);



})
             $('select[name=limit]').change(function(){
    var form=$('#limit_form');
   if (form.attr("action") === undefined){
        throw "form does not have action attribute"
    }


    let url = form.attr("action");
    if (url.includes("?") === false) return false;

    let index = url.indexOf("?");
    let action = url.slice(0, index)
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}
    form.attr("action", action)

    form.submit();
})

$('select[name=account_type_edit]').change(function(){
    var val=$(this).val();
      $.ajax({
        type:'get',
        data:{account:val},
        url:'{{url('get-sub-account')}}',
        async:false,
        success:function(res){
            var html='';
                 for(var i=0;i<res.length;i++){

   html+='<option value="'+res[i].sub_type+'" data-min="'+res[i].min+'" data-max="'+res[i].max+'" >'+res[i].sub_type+'</option>';

                      }
$('select[name=sub_account_type_edit]').html(html);
}
                  })
})
$('select[name=account_type]').change(function(){
    var val=$(this).val();
      $.ajax({
        type:'get',
        data:{account:val},
        url:'{{url('get-sub-account')}}',
        async:false,
        success:function(res){
                             var html='';
                 for(var i=0;i<res.length;i++){

   html+='<option value="'+res[i].sub_type+'" data-min="'+res[i].min+'" data-max="'+res[i].max+'" >'+res[i].sub_type+'</option>';

                      }
$('select[name=sub_account_type]').html(html);
}
                  })
})


@if(isset($_GET['advance_search']) && @$_GET['client_id']!='')

run('{{$_GET['client_id']}}','on')
var site_id='<?php echo isset($_GET['site_id'])?implode(',',$_GET['site_id']):''?>';
var operating_system_id='<?php echo isset($_GET['cert_issuer'])?implode(',',$_GET['cert_issuer']):''?>';

getVendor('{{@$_GET['client_id']}}',site_id.split(','),'on')

@endif

function run(id,on){
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getSiteByClientId')}}',
        async:false,
        success:function(res){
            var html='';
                         var check='<?php echo @$_GET['site_id']?implode(',',$_GET['site_id']):''   ?>';;
                        check=check.split(',');
            for(var i=0;i<res.length;i++){
                if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].site_name+'</option>';
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
                }
            }

            $('#site_id').html(html);
            $('#site_id').selectpicker('refresh');
        }
    })
}



$('#client_id').change(function(){
    var id=$(this).val()

    run(id)
       getVendor(id);

})

 $('.ActionIcon').mouseover(function() {
var data=$(this).attr('data-src');
$(this).find('img').attr('src',data);
})
$('.ActionIcon').mouseout(function() {
  var data=$(this).attr('data-original-src');
$(this).find('img').attr('src',data);
})
$('#site_id').change(function(){
    var site_id=$(this).val();
    var client_id=$('#client_id').val()

    getVendor(client_id,site_id)
})



   $('#form-search').submit(function(e){
    e.preventDefault();
    })
  $('input[name=search]').keyup(function(e){

var val=$(this).val();
    if(e.which==13){
     var form=$('#form-search');

   let url = form.attr("action");
        url+='&search='+val;
  window.location.href=url
}
  })


$(document).on('click','.btnEnd',function(){
    var id=$(this).attr('data-id');
    var status=$(this).attr('data')
    $('input[name=id]').val(id);
if(status==1){
    $('.revokeText').html('Deactivate')
}
else{
 $('.revokeText').html('Reactivate')
}
$('#EndModal').modal('show')

})


               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-sla')}}',
                    success:function(res){
                        $('#viewData').modal('show');



                            $('#cert_hostname').html(res.hostname)
                            $('#cert_status').html(res.cert_status!=null?res.cert_status.toUpperCase():'')
                           $('#cert_notification').html(res.cert_notification=='1'?'<div class="badge badge-success">On</div>':'<div class="badge badge-danger">Off</div>')
                            $('#cert_type').html(res.cert_type!=null?res.cert_type.toUpperCase():'')
                            $('#cert_issuer').html(res.cert_issuer)




     if(res.attachment!=''  && res.attachment!=null)
                              {
                                  ht='';
                                    var attachments=res.attachment.split(',');
                                    for(var i=0;i<attachments.length;i++){
                                    var icon='fa-file';

                                        var fileExtension = attachments[i].split('.').pop();

                                            if(fileExtension=='pdf'){
                                                icon='fa-file-pdf';
                                            }
                                            else if(fileExtension=='doc' || fileExtension=='docx'){
                                                icon='fa-file-word'
                                            }
                                            else if(fileExtension=='txt'){
                                                icon='fa-file-alt';

                                            }
                                            else if(fileExtension=='csv' || fileExtension=='xlsx' || fileExtension=='xlsm' || fileExtension=='xlsb' || fileExtension=='xltx'){
                                                    icon='fa-file-excel'
                                            }
                                            else if(fileExtension=='png' || fileExtension=='jpeg' || fileExtension=='jpg' || fileExtension=='gif' || fileExtension=='webp' || fileExtension=='svg' ){
                                                icon='fa-image'
                                            }
                                            ht+='<span class="attachmentDiv mr-2"><i class="fa '+icon+' text-danger"></i><a class="text-dark"  href="{{asset('public/ssl_attachment')}}/'+attachments[i]+'" target="_blank"> '+attachments[i]+'</a></span>';
                                   }
                                   $('#attachmentDisplay').html(ht)
                                     }
                                     else{
                                        $('#attachmentDisplay').html('')
                                     }


                              $('#created_at').html(res.created_at)
                               $('#created_by').html(res.created_by!=null?res.created_firstname+' '+res.created_lastname:'')
                                  $('#updated_by').html(res.updated_by!=null?res.updated_firstname+' '+res.updated_lastname:'')
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

                                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June","July", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        var cert_rdate='';
                        if(res.cert_rdate=='' ||  res.cert_rdate==null){
                                 cert_rdate='';
                        }
                        else{

                    var cert_rdateObject=new Date(res.cert_rdate);
                    var cert_rdate=cert_rdateObject.getFullYear()+'-'+monthNames[cert_rdateObject.getMonth()]+'-'+cert_rdateObject.getDate();
                    }

                     var cert_edate='';
                        if(res.cert_edate=='' ||  res.cert_edate==null){
                                 cert_edate='';
                        }
                        else{
                    var cert_edateObject=new Date(res.cert_edate);
                     cert_edate=cert_edateObject.getFullYear()+'-'+monthNames[cert_edateObject.getMonth()]+'-'+cert_edateObject.getDate();
                }

var status='';
    var MyDate=new Date('<?php echo date('m/d/Y') ?>');

const diffTime = Math.abs(cert_edate - MyDate);
const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

       if(res.cert_status=='Active'){

              if(diffDays<=30 ){
                                                        status='upcoming.png';

                                                        }else{
                                                        status='active.png';
                                                        }


                                                            }
                                                else if(res.cert_status=='Inactive'){
                                                status='renewed.png';

                                                }else if(res.cert_status=='Expired/Ended'){
                                                status='ended.png';
                                               }

                                                else if(res.cert_status=='Expired'){
                                                status='expired.png';
                                               }

                                                else{
                                                    status='active.png';
                                                }




                       $('#hostnameDisplay').html('<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover"  src="'+operating_system+'"  alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><img class="  mr-3 atar48" width="30px"    src="{{asset('public/img/')}}/'+status+'" alt=""><b>'+res.cert_name+'</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">'+(res.cert_type!=null?res.cert_type.toUpperCase():'')+'</span></p></div></div>')


                            $('#clientLogo').html('<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{asset('public/client_logos/')}}/'+res.logo+'" alt="">');





  if(res.comments=='' || res.comments==null){
                                                    $('.commentsDiv').addClass('d-none')
                                              }
                                              else{
                                               $('.commentsDiv').removeClass('d-none')
                                              }


  if(res.attachment=='' || res.attachment==null){
                                                    $('.attachmentsDiv').addClass('d-none')
                                              }
                                              else{
                                               $('.attachmentsDiv').removeClass('d-none')
                                              }
                    $('#cert_rdate').html(cert_rdate)
                            $('#cert_msrp').html(res.cert_msrp)
                            $('#cert_edate').html(cert_edate)

                      }
                })

               })


          $(document).on('click','.btnDelete',function(){
                    var id=$(this).attr('data');

                    var c=confirm("Are you sure want to delete this Gifi Account");
                    if(c){
                        window.location.href="{{url('delete-gifi')}}?id="+id;
                    }
                            })



   let click=0;
$('input,textarea').on('keyup',function(){
click=1;

})

$('select').on('change',function(){
click=1;

})





          $(document).on('click','.btnClose',function(){
 var id=$(this).attr('data')
 if(click==1){
   Dashmix.helpers('notify', {message: 'Close window?  <a href="javascript:;" data="'+id+'" class="  btn-notify btnCloseUndo ml-4" >Proceed</a>', delay: 5000});
 }else{
       showData($(this).attr('data'))
 }

})
$(document).on('click','.btnCloseUndo',function(){
        showData($(this).attr('data'))
})


var content3_image=[];



  let filePond =  FilePond.create(
        document.querySelector('.attachment'),
        {
          name: 'attachment',
            allowMultiple: true,
            allowImagePreview:true,

 imagePreviewFilterItem:false,
 imagePreviewMarkupFilter:false,

        dataMaxFileSize:"2MB",



          // server
          server: {
               process: {
            url: '{{url('uploadContractAttachment')}}',
            method: 'POST',
             headers: {
              'x-customheader': 'Processing File'
            },
            onload: (response) => {

              response =  response.replaceAll('"','') ;
            content3_image.push(response);

                var attachemnts=$('#attachment_array').val()
                var attachment_array=attachemnts.split(',');
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

        content3_image=content3_image.filter(function(ele){
            return ele != uniqueFileId;
        });

                var attachemnts=$('#attachment_array').val()
                var attachment_array=attachemnts.split(',');
                       attachment_array=  attachment_array.filter(function(ele){
            return ele != uniqueFileId;
        });

                    $('#attachment_array').val(attachment_array.join(','));


            fetch(`{{url('revertContractAttachment')}}?key=${uniqueFileId}`, {
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
   content3_image=content3_image.filter(function(ele){
            return ele != uniqueFileId;
        });


            // Should call the load method when done, no parameters required
            load();
        },

    }
        }
      );






// Comment ARRAY

$('#CommentSave').click(function(){
var comment=$('textarea[name=comment]').val();
     if(comment==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Comment', delay: 5000});

    }
    else{

           var l=commentArray.length;
                        if(l<5){
           commentArray.push({key:comment_key_count,comment:comment,date:'{{date('Y-M-d')}}',time:'{{date('h:i:s A')}}',name:'{{Auth::user()->firstname.''.Auth:: user()->lastname}}'});
           showComment()
           $('#CommentModal').modal('hide')
           $('textarea[name=comment]').val('')
           comment_key_count++;
        }
    }
})


$('#CommentSaveEdit').click(function(){
var comment=$('textarea[name=comment_edit]').val();
var id=$('input[name=comment_id_edit]').val();
     if(comment==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Comment', delay: 5000});

    }
    else{

           var l=commentArray.length;

           commentArray[id].comment=comment;
           showComment()
           $('#CommentModalEdit').modal('hide')
           $('textarea[name=comment_edit]').val('')

    }
})

$(document).on('click','.btnEditComment',function(){
        var id=$(this).attr('data');
        $('#CommentModalEdit').modal('show');
                $('input[name=comment_id_edit]').val(id);
        $('textarea[name=comment_edit]').val(commentArray[id].comment);

})
var temp_comment=[];
$(document).on('click','.btnDeleteComment',function(){
    var id=$(this).attr('data');
            var key=commentArray[id].key;
     temp_comment.push(commentArray[id]);

     commentArray.splice(id,1);

    Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Comment Deleted. <a href="javascript:;" class="  btn-notify btnCommentUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000});
showComment();

})

$(document).on('click','.btnCommentUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_comment.filter(l=>l.key==id);

if (index[0]) {
  commentArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_comment= temp_comment.filter(l=>l.key!=id);



    showComment();
    }
    })

function showComment(){
    var html='';
    if(commentArray.length>0){
        $('.commentDiv').removeClass('d-none');
    }
    else{
     $('.commentDiv').addClass('d-none');
    }
    for(var i=0;i<commentArray.length;i++){
        html+=`    <div class="js-task block block-rounded mb-2 animated fadeIn"   data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{asset('public/img/profile-white.png')}}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${commentArray[i].name}<br><span class="comments-subtext">On ${commentArray[i].date} at ${commentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                         <a type="button"  data="${i}" class="j btnEditComment btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button"   data="${i}" class="btnDeleteComment btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
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



 function showCommentsAjax(id){


$.ajax({
    type:'get',
    'method':'get',
    data:{id:id},
    url:"{{url('get-comments-gifi')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){
            var date=res[i].date;
            var newDate=new Date(date);
     const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];
    var date1=newDate.getFullYear()+'-'+monthNames[newDate.getMonth()]+'-'+newDate.getDate();
    var time=newDate.toLocaleString('en-US', { hour: date1.getHours, minute:date1.getSeconds, hour12: true }) ;

        commentArray.push({key:i,comment:res[i].comment,date:date1,time:time.split(',')[1],name:res[i].name});
        comment_key_count=i;
        }
        showComment();

    }
})



$.ajax({
    type:'get',
    'method':'get',
    data:{id:id},
    url:"{{url('get-attachment-gifi')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){
            var date=res[i].date;
            var newDate=new Date(date);
     const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];
    var date1=newDate.getFullYear()+'-'+monthNames[newDate.getMonth()]+'-'+newDate.getDate();
    var time=newDate.toLocaleString('en-US', { hour: date1.getHours, minute:date1.getSeconds, hour12: true }) ;

        attachmentArray.push({key:i,attachment:res[i].attachment,date:date1,time:time.split(',')[1],name:res[i].name});
        attachment_key_count=i;
        }
        showAttachment();

    }
})
}






// Attachment ARRAY

$('#AttachmentSave').click(function(){
var attachment=content3_image;
     if(content3_image.length==0){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1">  Add an attachment before saving.', delay: 5000});

    }
    else{

           var l=attachmentArray.length;



           for(var i=0;i<attachment.length;i++){
           attachmentArray.push({key:attachment_key_count,attachment:attachment[i],date:'{{date('Y-M-d')}}',time:'{{date('h:i:s A')}}',name:'{{Auth::user()->firstname.''.Auth:: user()->lastname}}'});
     attachment_key_count++;
       }

       filePond.removeFiles();
       content3_image=[];
           showAttachment()
           $('#AttachmentModal').modal('hide')



    }
})

var temp_attachment=[];
$(document).on('click','.btnDeleteAttachment',function(){
    var id=$(this).attr('data');
            var key=attachmentArray[id].key;
     temp_attachment.push(attachmentArray[id]);

     attachmentArray.splice(id,1);

    Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Attachment  Deleted. <a href="javascript:;" class="  btn-notify btnAttachmentUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000});
showAttachment();

})


$('#AttachmentClose').click(function(){
    temp_attachment=[];
    content3_image=[];
       filePond.removeFiles();
})

$(document).on('click','.btnAttachmentUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_attachment.filter(l=>l.key==id);

if (index[0]) {
  attachmentArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_attachment= temp_attachment.filter(l=>l.key!=id);



    showAttachment();
    }
    })

function showAttachment(){
    var html='';
    if(attachmentArray.length>0){
        $('.attachmentDiv').removeClass('d-none');
    }
    else{
     $('.attachmentDiv').addClass('d-none');
    }
    for(var i=0;i<attachmentArray.length;i++){
           var fileExtension = attachmentArray[i].attachment.split('.').pop();
                                         icon='attachment.png';
                                          if(fileExtension=='pdf'){
                                                icon='attch-Icon-pdf.png';
                                            }
                                            else if(fileExtension=='doc' || fileExtension=='docx'){
                                                icon='attch-word.png'
                                            }
                                            else if(fileExtension=='txt'){
                                                icon='attch-word.png';

                                            }
                                            else if(fileExtension=='csv' || fileExtension=='xlsx' || fileExtension=='xlsm' || fileExtension=='xlsb' || fileExtension=='xltx'){
                                                    icon='attch-excel.png'
                                            }
                                            else if(fileExtension=='png'  || fileExtension=='gif' || fileExtension=='webp' || fileExtension=='svg' ){
                                                icon='attch-png icon.png';
                                            }
                                              else if(  fileExtension=='jpeg' || fileExtension=='jpg'  ){
                                                icon='attch-jpg-icon.png';
                                            }
                                               else if(  fileExtension=='potx' || fileExtension=='pptx' || fileExtension=='ppsx' || fileExtension=='thmx'  ){
                                                icon='attch-powerpoint.png';
                                            }


        html+=`   <div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{asset('public/img/profile-white.png')}}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                           <h2 class="mb-0 comments-text">${attachmentArray[i].name}<br><span class="comments-subtext">On ${attachmentArray[i].date} at ${attachmentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                       <!-- -->

                                                        <a type="button"  class="  btnDeleteAttachment    btn btn-sm btn-link text-danger"  data="${i}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-2"><p class="mb-2">
 <a href="{{asset('public/temp_uploads/${attachmentArray[i].attachment}')}}" target="_blank"    class="   attachmentDivNew comments-section-text"><img src="{{asset('public/img/${icon}')}}" width="25px"> &nbsp;${attachmentArray[i].attachment.substring(0,19)}...
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





$(document).on('click','.saveContract',function(){
    $('.tooltip').tooltip('hide');
         $('.show').addClass('d-none');
var data1=$(this).attr('data');


    var account_no=$('input[name=account_no_edit]').val();
var description=$('input[name=description_edit]').val()
 var min=parseInt($('option:selected',$('select[name=sub_account_type_edit]')).attr('data-min'))
  var max=parseInt($('option:selected',$('select[name=sub_account_type_edit]')).attr('data-max'))
var tele_regex1 =/^.{0,65}$/;
if(account_no==''){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > Please enter value for Account No.', delay: 5000});
}
 else if(account_no<min || account_no>max ){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > GIFI account number not in valid range.', delay: 5000});
}
 else if(description=='' ){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > Please enter value for Description.', delay: 5000});
}
else if(!tele_regex1.test(description)  ){
Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px"  > Description should be max 65 chars.', delay: 5000});
}



    else{
var formData=new FormData(document.getElementById("form-1"));

for(var i=0;i<attachmentArray.length;i++){
    formData.append('attachmentArray[]',JSON.stringify(attachmentArray[i]));
}
for(var i=0;i<commentArray.length;i++){
    formData.append('commentArray[]',JSON.stringify(commentArray[i]));
}


$.ajax({
    type:'post',
    data:formData,
    'url':'{{url('update-gifi')}}',
    dataType:'json',
    async:false,

        contentType:false,
        processData:false,
        cache:false,
    success:function(res) {

        Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Gifi Account successfully saved', delay: 5000});
        showData(data1)
        click=0;

    }
})

    }

})









           });









           $(document).on('change', 'select[name=filter_account_type]', function(){
    var val=$(this).val();
      $.ajax({
        type:'get',
        data:{account:val},
        url:'{{url('/get-gifi-account')}}',
        async:false,
        success:function(res){
                             var html='';
                 for(var i=0;i<res.length;i++){

                        html+='<option value="'+res[i].sub_type+'" data-min="'+res[i].min+'" data-max="'+res[i].max+'" >'+res[i].sub_type+'</option>';

                }
                $('select[name=filter_sub_account_type]').html(html);
        }
    })
})
</script>
