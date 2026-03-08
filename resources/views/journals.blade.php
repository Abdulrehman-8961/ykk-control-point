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

                ->update(['journal' => $limit]);

        } else {

            DB::table('settings')->insert(['user_id' => Auth::id(), 'journal' => $limit]);

        }

    } else {

        if ($no_check != '') {

            if ($no_check->journal != '') {

                $limit = $no_check->journal;

            }

        }

    }

    

    if (sizeof($_GET) > 0) {

        $edit_no = @$_GET['filter_edit_no'];

        $client = @$_GET['filter_client'] ?? [];

        $fiscal_year = @$_GET['filter_fiscal_year'] ?? [];

        $period = @$_GET['filter_period'] ?? [];

        $source = @$_GET['filter_source'] ?? [];

        $ref = @$_GET['filter_ref'] ?? [];

        $account = @$_GET['filter_account'] ?? [];

        $orderby = 'desc';

        $field = 'edit_no';

        if (isset($_GET['orderBy'])) {

            $orderby = $_GET['orderBy'];

            $field = $_GET['field'];

        }

    

        // $qry = DB::table('journals as j')

        //     ->where('j.is_deleted', 0)

        //     ->leftJoin('clients as c', function ($join) {

        //         $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);

        //     })

        //     ->leftJoin('source_code as sc', function ($join) {

        //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //     })

        //     ->where(function ($query) use ($edit_no, $client, $fiscal_year, $period, $source, $ref, $account) {

        //         if (!empty($edit_no)) {

        //             $query->where('j.editNo', $edit_no);

        //         }

        //         if (!empty($client)) {

        //             $query->whereIn('j.client', $client);

        //         }

        //         if (!empty($fiscal_year)) {

        //             $query->whereIn('j.fyear', $fiscal_year);

        //         }

        //         if (count($period) > 0) {

        //             $query->whereIn('j.period', $period);

        //         }

        //         if (count($source) > 0) {

        //             $query->whereIn('j.source', $source);

        //         }

        //         if (count($ref) > 0) {

        //             $query->whereIn('j.ref_no', $ref);

        //         }

        //         if (count($account) > 0) {

        //             $query->whereIn('j.account_no', $account);

        //         }

        //         $query->where('j.description', 'like', '%' . @$_GET['search'] . '%')

        //         ->orWhere('j.editNo', 'like', '%' . @$_GET['search'] . '%');

        //     })

        //     ->select('j.*', 'c.firstname', 'c.lastname', 'c.display_name', 'c.logo', 'sc.source_code', 'c.company')

        //     ->orderBy($field, $orderby)

        //     ->paginate($limit);

        $qry = DB::table('journals as j')

            ->where('j.is_deleted', 0)

            ->leftJoin('clients as c', function ($join) {

                $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);

            })

            ->leftJoin('source_code as sc', function ($join) {

                $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

            })

            ->where(function ($query) use ($edit_no, $client, $fiscal_year, $period, $source, $ref, $account) {

                if (!empty($edit_no)) {

                    $query->where('j.editNo', $edit_no);

                }

                if (!empty($client)) {

                    $query->whereIn('j.client', $client);

                }

                if (!empty($fiscal_year)) {

                    $query->whereIn('j.fyear', $fiscal_year);

                }

                if (!empty($period)) {

                    $query->whereIn('j.period', $period);

                }

                if (!empty($source)) {

                    $query->whereIn('j.source', $source);

                }

                if (!empty($ref)) {

                    $query->whereIn('j.ref_no', $ref);

                }

                if (!empty($account)) {

                    $query->whereIn('j.account_no', $account);

                }

            })

            ->where(function ($query) {

                if (!empty($_GET['search'])) {

                    $search = '%' . $_GET['search'] . '%';

                    $query->where('j.description', 'like', $search)->orWhere('j.editNo', 'like', $search);

                }

            })

            ->select('j.*', 'c.firstname', 'c.lastname', 'c.display_name', 'c.logo', 'sc.source_code', 'c.company')

            ->orderBy($field, $orderby)

            ->paginate($limit);

    } else {

        $qry = DB::table('journals as j')

            ->where('j.is_deleted', 0)

            ->leftJoin('clients as c', function ($join) {

                $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);

            })

            ->leftJoin('source_code as sc', function ($join) {

                $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

            })

            ->select('j.*', 'c.firstname', 'c.lastname', 'c.display_name', 'c.logo', 'sc.source_code', 'c.company')

            ->orderBy('j.edit_no', 'desc')

            ->paginate($limit);

    }

    if (isset($_GET['id'])) {

        $GETID = $_GET['id'];

    } else {

        $GETID = @$qry[0]->edit_no;

    }

    

    function translatedDate()

    {

        $currentDate = date('Y-m-d');

        $lastDateOfMonth = date('d-M-Y', strtotime(date('Y-m-t', strtotime($currentDate))));

        $lastDateOfMonth = explode('-', $lastDateOfMonth);

        return $lastDateOfMonth[0] . '-' . $lastDateOfMonth[1] . '-' . substr($lastDateOfMonth[2], -2);

    }

    

    ?>









    <main id="main-container pt-0" style="overflow: hidden">

        <!-- Hero -->





        <style type="text/css">

            #showData::-webkit-scrollbar {

                width: 14px;

            }



            body {

                overflow: hidden;

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



            .form-debit,

            .form-credit,

            .form-taxes {

                color: #3F3F3F;

            }



            .viewContent {

                padding-left: 15px !important;

                padding-right: 10px !important;

            }



            #net {

                text-align: right !important;

                padding-left: 12px !important;

                padding-right: 12px !important;

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



            @media screen and (max-width: 1900px) {

                .client-journal-description {

                    padding-right: 10px;

                }

            }



            @media screen and (max-width: 1687px) {

                .client-journal-description {

                    padding-right: 25px;

                }

            }





            .posting-period-client-stats>.col-sm-1 {

                max-width: 13.333333%;

                min-width: 12.875%;

            }



            .posting-period-client-stats>.col-sm-3 {

                max-width: 20.333333%;

                padding-left: 0.585rem;



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





            .tooltip .arrow::before {

                border-top-color: #3A3B42 !important;

                border-bottom-color: #3A3B42 !important;

            }







            .GREEN {

                background: #92D050 !important;

                color: black !important;

            }



            .YELLOW {

                background: #FFFF00 !important;

                color: black !important;

            }



            .GREY {

                background: #D9D9D9 !important;

                color: #A5A5A5 !important;

            }









            .info-tooltip-close {

                width: fit-content !important;

                box-shadow: none !important;

                background: none !important;

                border: none !important;

                position: absolute !important;

                top: 15px !important;

                right: 10px !important;

            }





            .view-------Content.journal-view.selected {

                border-radius: 10px !important;

                border: 1px solid #C2DBFF !important;

                background: #C2DBFF !important;

                box-shadow: 3px 3px 4px #40404057 !important;

            }



            .journal-view {

                user-select: none !important;

            }







            .select-report-type {

                min-width: 200px;

                border-radius: 10px;

                margin-bottom: 15px;

            }



            .select-report-type:hover {

                -webkit-box-shadow: 0px 0px 1px 1px rgba(36, 133, 232, 1);

                -moz-box-shadow: 0px 0px 1px 1px rgba(36, 133, 232, 1);

                box-shadow: 0px 0px 1px 1px rgba(36, 133, 232, 1);

            }



            .select-report-type[status="1"] {

                -webkit-box-shadow: 0px 0px 6px 3px rgba(36, 133, 232, 1);

                -moz-box-shadow: 0px 0px 6px 3px rgba(36, 133, 232, 1);

                box-shadow: 0px 0px 6px 3px rgba(36, 133, 232, 1);

            }





            #FinancialStatementModal .irs-single {

                word-spacing: -3px;

            }



            .client-journals-pagination {

                position: relative;

            }



            .client-journals-pagination .page-item.active .page-link:before {

                content: "";

                position: absolute;

                width: 100%;

                height: 32px;

                background-color: #408DFB;

                color: white;

                text-align: center;

                line-height: 50px;

                left: 0;

                z-index: -1;

                bottom: 0;

            }



            .client-journals-pagination .page-item.active .page-link:after {

                content: "";

                position: absolute;

                width: 100%;

                height: 32px;

                background-color: #408DFB;

                color: white;

                text-align: center;

                line-height: 50px;

                left: 0;

                z-index: -1;

                top: 0;

            }



            .account-item {

                box-shadow: none !important;

                border: 1px solid #595959 !important;

                border-radius: 5px !important;

            }



            .account-item:hover {

                box-shadow: none !important;

                border: 1px solid #595959 !important;

                border-radius: 5px !important;

                background-color: #fff !important;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(89, 89, 89, 0.3) !important;

                -moz-box-shadow: 0px 0px 1px 5px rgba(89, 89, 89, 0.3) !important;

                box-shadow: 0px 0px 1px 5px rgba(89, 89, 89, 0.3) !important;

            }



            .account-item.c-active.account-item-active {

                background-color: rgba(89, 89, 89, 0.2) !important;

            }



            .account-item.c-active.account-item-active:hover {

                box-shadow: none !important;

                border: 1px solid #595959 !important;

                border-radius: 5px !important;

                background-color: rgba(89, 89, 89, 0.2) !important;

            }



            /* .modal-content2 {

                                                                                    position: absolute;

                                                                                    top: 11px;

                                                                                    right: 11px;

                                                                                    z-index: 99;

                                                                                } */



            .modal-content2 {

                position: absolute;

                top: 5px;

                right: 34px;

                z-index: 99;

            }



            .modal-content2 .btn-block-option {

                border-radius: 1rem;

                color: #A5A5A5;

                background: #fff;

            }



            .modal-content2 .btn-block-option:hover {

                background: #BFBFBF;

                color: #A5A5A5;

            }



            .indicator {

                font-family: Signika;

                font-size: 11pt;

                padding-left: 9px;

                padding-right: 9px;

                border-radius: 3px;

                padding-top: 2px;

                padding-bottom: 2px;

                cursor: pointer;

            }



            .indicator-frequency-monthly {

                background: #4DD827;

                color: #000;

            }



            .indicator-frequency-monthly:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(77, 216, 39, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(77, 216, 39, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(77, 216, 39, 0.3);

            }



            .indicator-frequency-quarterly {

                background: #0070DD;

                color: #fff;

            }



            .indicator-frequency-quarterly:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

            }



            .indicator-frequency-yearly {

                background: #E54643;

                color: #fff;

            }



            .indicator-frequency-yearly:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3);

            }



            .indicator-period-balance {

                background: transparent;

                color: #4EA833;

                border: 1px solid #4EA833;

                margin-left: 3px;

                margin-right: 3px;

            }



            .indicator-period-balance:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3);

            }



            .indicator-period-not-balance {

                background: transparent;

                color: #FFFF00;

                border: 1px solid #FFFF00;

                margin-left: 3px;

                margin-right: 3px;

            }



            .indicator-period-not-balance:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(255, 255, 0, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(255, 255, 0, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(255, 255, 0, 0.3);

            }



            .indicator-period-no-journals {

                background: transparent;

                color: #A5A5A5;

                border: 1px solid #A5A5A5;

                margin-left: 3px;

                margin-right: 3px;

            }



            .indicator-period-no-journals:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(165, 165, 165, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(165, 165, 165, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(165, 165, 165, 0.3);

            }



            .topbar-change-client {

                border-radius: 3px;

                padding-left: 2px;

                padding-right: 2px;

                padding-top: 2px;

                padding-bottom: 2px;

            }



            .topbar-change-client:hover {

                background: #0070DD;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

            }



            .topbar-button:hover {

                background: #0070DD !important;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

            }



            .edit-no-bulb {

                border-radius: 10px;

                border: 1px solid #7F7F7F;

                padding: 7px 12px;

            }



            .edit-no-bulb:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(127, 127, 127, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(127, 127, 127, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(127, 127, 127, 0.3);

            }



            .balance-label {

                font-size: 12pt;

                font-family: Signika;

                font-weight: bold;

            }



            .cr-balance {

                margin-left: 1rem;

                border: 1px solid #E54643;

                border-radius: 10px;

                padding: 3px 15px;

                width: 140px;

                color: #E54643;

                text-align: right;

            }



            .cr-balance:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3);

            }



            .dr-balance {

                margin-left: 1rem;

                border: 1px solid #0070DD;

                border-radius: 10px;

                padding: 3px 15px;

                width: 140px;

                color: #0070DD;

                text-align: right;

            }



            .dr-balance:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(0, 112, 221, 0.3);

            }



            .acct-balance {

                margin-left: 1rem;

                border: 1px solid #4EA833;

                border-radius: 10px;

                padding: 3px 15px;

                width: 140px;

                color: #4EA833;

                text-align: right;

            }



            .acct-balance:hover {

                -webkit-box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3);

                -moz-box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3);

                box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3);

            }



            .client-journals-view {

                overflow-y: auto;

                height: 82vh;

                padding-left: 0px;

                padding-right: 5px;

            }







            .client-journals-view::-webkit-scrollbar {

                width: 12px;

            }



            .journal-view-pagination-footer {

                position: absolute;

                bottom: -15.5%;

                left: -4px;

            }



            @media (max-height: 950px) {

                .journal-view-pagination-footer {

                    position: absolute;

                    bottom: -14.5%;

                    left: -4px;

                }

            }



            @media (max-height: 817px) {

                .journal-view-pagination-footer {

                    position: absolute;

                    bottom: -14%;

                    left: -4px;

                }

            }



            @media (max-height: 740px) {

                .journal-view-pagination-footer {

                    position: absolute;

                    bottom: -13.5%;

                    left: -4px;

                }

            }







            .form-debit {

                background: #fff !important;

                color: #4194F6 !important;

                border-color: #C0DBFC !important;

                border: 1px solid;

            }



            .form-debit:hover:not(:focus) {

                background: #fff !important;

                color: #4194F6 !important;

                border-color: #4194F6 !important;

                -webkit-box-shadow: none !important;

                -moz-box-shadow: none !important;

                box-shadow: none !important;

            }



            .form-debit:focus {

                background: #fff !important;

                color: #4194F6 !important;

                border-color: #C0DBFC !important;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(65, 148, 246, 0.3) !important;

                -moz-box-shadow: 0px 0px 1px 5px rgba(65, 148, 246, 0.3) !important;

                box-shadow: 0px 0px 1px 5px rgba(65, 148, 246, 0.3) !important;

            }



            .form-credit {

                background: #fff !important;

                color: #E54643 !important;

                border-color: #F3A9A7 !important;

                border: 1px solid;

            }



            .form-credit:hover:not(:focus) {

                background: #fff !important;

                color: #E54643 !important;

                border-color: #E54643 !important;

                -webkit-box-shadow: none !important;

                -moz-box-shadow: none !important;

                box-shadow: none !important;

            }



            .form-credit:focus {

                background: #fff !important;

                color: #E54643 !important;

                border-color: #F3A9A7 !important;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3) !important;

                -moz-box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3) !important;

                box-shadow: 0px 0px 1px 5px rgba(229, 70, 67, 0.3) !important;

            }



            .form-taxes {

                background: #fff !important;

                color: #4EA833 !important;

                border-color: #A0DD8E !important;

                border: 1px solid;

            }



            .form-taxes:hover:not(:focus) {

                background: #fff !important;

                color: #4EA833 !important;

                border-color: #4EA833 !important;

                -webkit-box-shadow: none !important;

                -moz-box-shadow: none !important;

                box-shadow: none !important;

            }



            .form-taxes:focus {

                background: #fff !important;

                color: #4EA833 !important;

                border-color: #A0DD8E !important;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3) !important;

                -moz-box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3) !important;

                box-shadow: 0px 0px 1px 5px rgba(78, 168, 51, 0.3) !important;

            }



            .form-net {

                background: #D9D9D9 !important;

                color: #7F7F7F !important;

                border-color: #D9D9D9 !important;

                border: 1px solid;

            }



            .form-net:hover {

                background: #D9D9D9 !important;

                color: #7F7F7F !important;

                border-color: #7F7F7F !important;

                -webkit-box-shadow: none !important;

                -moz-box-shadow: none !important;

                box-shadow: none !important;

            }



            .form-net:focus,

            .form-net:active {

                background: #D9D9D9 !important;

                color: #7F7F7F !important;

                border-color: #D9D9D9 !important;

                -webkit-box-shadow: 0px 0px 1px 5px rgba(127, 127, 127, 0.3) !important;

                -moz-box-shadow: 0px 0px 1px 5px rgba(127, 127, 127, 0.3) !important;

                box-shadow: 0px 0px 1px 5px rgba(127, 127, 127, 0.3) !important;

            }



            .bubble-account:hover {

                background-color: #E9E9E9 !important;

                box-shadow:

                    0 0 2px rgba(89, 89, 89, 0.6),

                    0 0 6px rgba(89, 89, 89, 0.6);

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



            .bubble_edit_no:hover {

                background-color: #EDF9EA !important;

                box-shadow:

                    0 0 2px rgba(78, 168, 51, 0.6),

                    0 0 6px rgba(78, 168, 51, 0.6);

            }



            .tooltip-inner {

                max-width: none;

                color: #FFFFFF;

                font-family: 'Signika', sans-serif;

                font-size: 8pt !important;

                font-weight: 300;

                border-radius: 10px;

                padding: 6px 10px;

                text-align: center;

            }



            /* Add a class for the spinning gear icon */

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



            /* Class to expand the button */

            .expanded {

                padding-right: 40px;

                width: auto;

                transition: width 0.3s ease-in-out;

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



            .change-label:hover {

                color: #262626 !important;

                border-color: #262626 !important;

                background-color: #F2F2F2;

                box-shadow: 0 0 2px 2px rgba(127, 127, 127, 0.3);

                transition: background-color 0.3s, box-shadow 0.3s;

            }



            .change-label {

                font-weight: 500 !important;

            }

        </style>





        <!-- Page Content -->

        <div class="con   no-print page-header " id="JournalHeader">

            <!-- Full Table -->

            <div class="b mb-0  ">



                <div class="block-content pt-0 mt-0">



                    <div class="TopArea" style="position: sticky; padding-top: 14px; z-index: 1000; padding-bottom: 11px;">

                        <div class="row">

                            <div class="col-sm-5 search-col row">

                                <div class="push mb-0 col-sm-6 pr-0">



                                    <?php

                                    $filter = (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') . (isset($_GET['note']) ? '&note=' . $_GET['note'] : '') . (isset($_GET['account_type']) ? '&account_type=' . $_GET['account_type'] : '') . (isset($_GET['sub_type']) ? '&sub_type=' . $_GET['sub_type'] : '') . (isset($_GET['account_no']) ? '&account_no=' . $_GET['account_no'] : '') . (isset($_GET['description']) ? '&description=' . $_GET['description'] : '') . (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');

                                    ?>



                                    <form class="push mb-0" method="get" id="form-search"

                                        action="{{ url('journals/') }}?{{ $_SERVER['QUERY_STRING'] }}">



                                        <div class="input-group main-search-input-group" style="max-width: 74.375%;">

                                            <input type="text" value="{{ @$_GET['search'] }}"

                                                class="form-control searchNew" name="search" placeholder="Search Journal">

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

                                {{-- {{ dd($qry->total()) }} --}}

                                <div class="col-sm-6  1" style="position: relative;">

                                    @if (Auth::user()->role != 'read')

                                        <a class="btn btn-dual  d2 " data-toggle="tooltip"

                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                            title="" data-original-title="Filters" href="javascript:;"

                                            id="GeneralFilters" style="position:absolute;top: 0;left: 20px;">

                                            <img src="{{ asset('public/img/ui-icon-filters.png') }}" style="width:19px">

                                        </a>

                                        <a class="btn btn-dual  d2 " href="javascript:;" data-toggle="tooltip"

                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                            title="" data-original-title="Add Journals" id="expand-add-journal-view"

                                            style="position: absolute;top: 1px;left: 65px;">

                                            <img src="{{ asset('public/img/ui-icon-add.png') }}" style="width:15px">

                                        </a>

                                        <a class="btn btn-dual  d2 " data-toggle="tooltip"

                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                            title="" data-original-title="Export" href="javascript:void()"

                                            id="ExportJournals" style="position: absolute;top: -2px;left: 103px;">

                                            <img src="{{ asset('public/new-gl-icons-dec/export-icon-white-2.png') }}"

                                                style="width:19px" data-toggle="modal" data-target="#ExportModalNew">

                                        </a>

                                        <a class="btn btn-dual  d2 " href="javascript:;" data-toggle="tooltip"

                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                            title="" data-original-title="Import"

                                            style="    padding-bottom: 7px !important;

                                                padding-top: 4px !important;position: absolute;top: 0; left: 146px;"

                                            id="ImportJournals">

                                            <img src="{{ asset('public/new-gl-icons-dec/import-icon-white2.png') }}"

                                                style="width:18px">

                                        </a>

                                        <a class="btn btn-dual rep-btn d2 " data-toggle="tooltip"

                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                            title="" data-original-title="Reports" href="javascript:;"

                                            style="position: absolute;top: 0; left: 188px;">

                                            <img src="{{ asset('public/icons2/icon-report-white.png') }}"

                                                style="width:15px">

                                        </a>

                                        <a class="btn btn-dual  d2 JournalFYReIndexBtn" data-toggle="tooltip"

                                            data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                            title="" data-original-title="Reindex" href="javascript:;"

                                            id="Reindex" style="position: absolute; top: 0; left: 226px;">

                                            <img src="{{ asset('public/new-gl-icons-dec/reindex-icon-white.png') }}"

                                                style="width:18px">

                                        </a>



                                    @endif

                                </div>

                                {{-- <div class="col-sm-3 pl-0">

                                    {{$qry->appends($_GET)->onEachSide(0)->links()}}

                                </div> --}}

                            </div>

                            <div class="col-lg-3 pr-0">

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



                            <div class="d-flex text-right col-lg-4 justify-content-end pr-0">

                                {{ $qry->appends($_GET)->onEachSide(0)->links() }}



                                <form id="limit_form" class="ml-2 mb-0"

                                    action="{{ url('journals') }}?{{ $_SERVER['QUERY_STRING'] }}">

                                    <select name="limit" class="float-right form-control mr-1   px-0"

                                        style="width:auto; height: 28px;">

                                        <option value="10" {{ @$limit == 10 ? 'selected' : '' }}>10</option>

                                        <option value="25" {{ @$limit == 25 ? 'selected' : '' }}>25</option>

                                        <option value="50" {{ @$limit == 50 ? 'selected' : '' }}>50</option>

                                        <option value="100" {{ @$limit == 100 ? 'selected' : '' }}>100</option>

                                    </select>

                                </form>



                                @if (@Auth::user()->role == 'admin')



                                    <a href="javascript:;" data-toggle="tooltip" data-custom-class="header-tooltip"

                                        data-title="Settings" class="mr-1 text-dark headerSetting d3   "><img

                                            src="{{ asset('public/img/ui-icon-settings.png') }}" width="23px"></a>



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









            <div class="con   no-print page-header d-none" id="AddJournalHeader">

                <div class="b   mb-0  ">

                    <div class="block-content pt-0 mt-0">

                        <div class="TopArea" style="position: sticky;padding-top: 14px;z-index: 1000;padding-bottom: 11px;">

                            <div class="row">

                                <div class="col-sm-9 search-col row">

                                    <div class="col-sm-auto  1" style="position: relative;">

                                        <img src="{{ asset('public') }}/icons_2024_02_24/icon-journal-white.png"

                                            style="position: absolute;left: 0;top: -8px;width: 40px;height: 40px;">

                                        <h6 class="text-white mb-0" style="margin-left: 40px;margin-top: 3px;">Add Journals

                                        </h6>

                                    </div>

                                </div>

                                <div class="d-flex text-right col-lg-3 justify-content-end">

                                    @if (@Auth::user()->role == 'admin')

                                        <a href="javascript:;" data-toggle="tooltip" data-custom-class="header-tooltip"

                                            data-title="Settings" class="mr-3 text-dark headerSetting d3   "><img

                                                src="{{ asset('public/img/ui-icon-settings.png') }}" width="23px"></a>

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



                <div class="con   no-print page-header d-none" id="ClientJournalHeader">

                    <div class="b   mb-0  ">

                        <div class="block-content pt-0 mt-0">

                            <div class="TopArea" style="position: sticky;padding-top: 14px;z-index: 1000;padding-bottom: 11px;">

                                <div class="container-fluid row px-0">

                                    <div class="float-left search-col row d-flex flex-nowrap"

                                        style="width: 69%; justify-content: space-between">

                                        <div class="col-sm-auto" style="position: relative;">

                                            <img src="{{ asset('public/img/gl-menu-icons/gl-menu-clients-removebg-preview-white.png') }}"

                                                style="position: absolute;left: 0;top: -8px;width: 40px;height: 40px;">

                                            <h5 class="text-white mb-0" style="margin-left: 40px;margin-top: 3px;"

                                                id="add-journal-h1"></h5>

                                        </div>

                                        <div class="col-sm-auto d-flex justify-content-center align-items-center "

                                            style="padding-left: 17px;">

                                            <div>

                                                <span id="return-to-posting-period" class="topbar-change-client"

                                                    data-toggle="tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-custom-class="header-tooltip"

                                                    data-original-title="Change Client">

                                                    <img src="{{ asset('public/icons_2024_02_24/icon-change-client-white.png') }}"

                                                        width="22px">

                                                </span>

                                            </div>

                                        </div>



                                        <div class="col-sm-auto d-flex justify-content-center align-items-center pl-0 pr-4">

                                            <div>

                                                <span class="indicator indicator-frequency-monthly"

                                                    id="journal-client-remittance-frequency" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-title=""

                                                    data-original-title=""></span>

                                            </div>

                                        </div>



                                        <div class="col-sm-auto d-flex justify-content-center align-items-center px-2">

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-1" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P1</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-2" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P2</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-3" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P3</span>

                                            </div>

                                        </div>



                                        <div class="col-sm-auto d-flex justify-content-center align-items-center px-2">

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-4" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P4</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-5" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P5</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-6" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P6</span>

                                            </div>

                                        </div>



                                        <div class="col-sm-auto d-flex justify-content-center align-items-center px-2">

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-7" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P7</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-8" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P8</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-9" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P9</span>

                                            </div>

                                        </div>



                                        <div class="col-sm-auto d-flex justify-content-center align-items-center px-2">

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-10" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P10</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-11" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P11</span>

                                            </div>

                                            <div>

                                                <span class="indicator indicator-period-no-journals js-tooltip-enabled"

                                                    id="journal-client-pp-period-12" data-toggle="tooltip"

                                                    data-custom-class="header-tooltip" data-trigger="hover" data-placement="top"

                                                    title="" data-original-title="0">P12</span>

                                            </div>

                                        </div>



                                    </div>

                                    <div style="width: 30%;" class="d-flex text-right float-right justify-content-end ml-auto ">

                                        <div class="container-fluid">

                                            <div class="row">

                                                <div class="col-8 pl-0 d-flex align-items-center">

                                                    <div class="input-group main-search-input-group" style="max-width: 74.375%;">

                                                        <input type="text" class="form-control searchNew"

                                                            style="height:29px !important;" name="client-journal-search"

                                                            value="" data="6" placeholder="Search Journals">

                                                        <div class="input-group-append" style="width: 35px !important;">

                                                            <span class="input-group-text">

                                                                <img src="{{ url('public/img/ui-icon-search.png') }}"

                                                                    width="15px">

                                                            </span>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="col-4 pl-0 pr-0 d-flex align-items-center" style="">

                                                    <span data-toggle="modal" id="btnFilterClientJournals" data-client-id="6"

                                                        data-bs-target="#filterClientJournalModal"

                                                        data-target="#filterClientJournalModal">

                                                        <button type="button" class="btn btn-dual d1 topbar-button  "

                                                            style="padding-left: 10px;padding-right: 10px;" data-toggle="tooltip"

                                                            data-custom-class="header-tooltip" data-trigger="hover"

                                                            data-placement="top" title=""

                                                            data-original-title="Filter Journals">

                                                            <img src="{{ asset('public/img/ui-icon-filters.png') }}"

                                                                width="18px">

                                                        </button>

                                                    </span>

                                                    <span>

                                                        <button type="button"

                                                            class="btn btn-dual d1 topbar-button  batch-selection"

                                                            style="padding-left: 10px;padding-right: 10px;" data-selected="0"

                                                            data-toggle="tooltip" data-custom-class="header-tooltip"

                                                            data-trigger="hover" data-placement="top" title=""

                                                            data-original-title="Select All Journals">

                                                            <img src="{{ asset('public/batch_icons/icon-journals-select-all.png') }}"

                                                                width="18px">

                                                        </button>

                                                    </span>

                                                    <span>

                                                        <button type="button"

                                                            class="btn btn-dual d1 topbar-button  batch-update d-none"

                                                            style="padding-left: 10px;padding-right: 10px;" data-toggle="tooltip"

                                                            data-custom-class="header-tooltip" data-trigger="hover"

                                                            data-placement="top" title=""

                                                            data-original-title="Batch Update">

                                                            <img src="{{ url('public/batch_icons/icon-journals-batchupdate.png') }}"

                                                                width="18px">

                                                        </button>

                                                    </span>

                                                    <select name="client-journal-limit"

                                                        class="float-right form-control ml-auto px-0"

                                                        style="width: 42px;height: 29px;">

                                                        <option value="10" selected="">10</option>

                                                        <option value="25">25</option>

                                                        <option value="50">50</option>

                                                        <option value="100">100</option>

                                                        <option value="200">200</option>

                                                        <option value="300">300</option>

                                                        <option value="400">400</option>

                                                        <option value="500">500</option>

                                                    </select>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>





























                <div class="content  " id="contentDiv">

                    <!-- Page Content -->

                    <div class="row px-0 d-none" id="addJournalView">

                        @include('add-journal-form')

                    </div>

                    <div class="row px-0 " id="viewDiv">

                        <div class="col-lg-8  " id="showData" style="overflow-y: auto;height:90vh;">

                        </div>

                        <div class="col-lg-4    no-print" att="overflow-y: auto;height: 90vh;">

                            <div style="overflow-y: auto;height: 90vh;">

                                @foreach ($qry as $q)

                                    <?php

                                    $amount_clr = '';

                                    $amount = 0;

                                    $symbol = '';

                                    if ($q->debit > $q->credit) {

                                        $amount = $q->debit;

                                        if ($amount == 0) {

                                            $amount_clr = '#BDBDBE ';

                                            $text_clr = '#7F7F7F';

                                        } else {

                                            $amount_clr = '#4194F6';

                                            $text_clr = '#FFF';

                                        }

                                        $symbol = 'DR';

                                    } else {

                                        $amount = $q->credit;

                                        if ($amount == 0) {

                                            $amount_clr = '#BDBDBE ';

                                            $text_clr = '#7F7F7F';

                                        } else {

                                            $amount_clr = '#E54643';

                                            $text_clr = '#FFF';

                                        }

                                        $symbol = 'CR';

                                    }

                                    ?>

                                    <div class="block block-rounded   table-block-new mb-2 mr-2 pb-0  -  viewContent"

                                        data="{{ $q->edit_no }}"

                                        style="cursor:pointer;padding-left: 0px !important;padding-right: 0px !important;">



                                        <div class="block-content pt-1 pb-1 pl-1 d-flex  position-relative" style="">

                                            <div class=" justify-content-center align-items-center  d-flex mr-1"

                                                style="width: 20%;float:left;padding: 7px;">

                                                @if ($q->logo != '')

                                                    {{-- <img src="{{ asset('/public') }}/client_logos/{{ $q->logo }}"

                                                        class="rounded-circle  "

                                                        style="object-fit: cover;width: 65px;height: 65px;"> --}}

                                                    <img src="{{ asset('public/img/icon-bubble-journal.png') }}" class=""

                                                        style="object-fit: cover;width: 65px;height: 65px;">

                                                @else

                                                    <img src="{{ asset('public/img/icon-bubble-journal.png') }}" class=""

                                                        style="object-fit: cover;width: 65px;height: 65px;">

                                                @endif

                                            </div>

                                            <div class="w- 100 d-flex justify-content-between" style="width: 70%;">

                                                <div class="d-flex flex-column" style="width: calc(100% - 15px);">

                                                    <div style="overflow: hidden; text-overflow: ellipsis;">



                                                        <span class="font-12pt mb-0 text-truncate font-w600 c1"

                                                            style="font-family: Calibri;color:#626262 !important;">{{ $q->display_name }}</span>

                                                        {{-- <span class="font-12pt mb-0 text-truncate font-w600 c1"

                                                            style="font-family: Calibri;color:#4194F6 !important;">{{ substr($q->ref_no, 0, 8) }}

                                                            / {{ date('d-M-Y', strtotime($q->gl_date)) }}</span> --}}

                                                    </div>

                                                    <div class="d-flex flex-row" style="padding-top: 3px;">

                                                        <span class="bubble-account" data-toggle="tooltip" data-trigger="hover"

                                                            data-placement="top" title="" data-original-title="Account #"

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

                                                            {{ $q->account_no }}

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

                                                            {{ $q->description }}

                                                        </span>

                                                    </div>



                                                    <div class="d-flex flex-row" style="padding-top: 6px;">

                                                        <div>

                                                            <div data-toggle="tooltip" data-trigger="hover" data-placement="top"

                                                                title="" data-original-title="Ref #"

                                                                style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: 75px;

                                        text-align: center;

                                        font-size: 9pt;

                                        color:#989898;

                                        border:1px solid #A6A6A6;

                                        border-radius: 5px;



                                        margin-right: 0.675rem;

                                        white-space: nowrap;

           overflow: hidden;

           text-overflow: ellipsis;"

                                                                class="px-2 bubble_period">

                                                                {{ $q->ref_no }}

                                                            </div>

                                                        </div>

                                                        <div>

                                                            <div data-toggle="tooltip" data-trigger="hover" data-placement="top"

                                                                title="" data-original-title="Fiscal Year"

                                                                style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#989898;

                                        border:1px solid #A6A6A6;

                                        border-radius: 5px;



                                        margin-right: 0.675rem;"

                                                                class="px-2 bubble_period">

                                                                {{-- {{ $q->account_no }} --}}

                                                                {{ $q->fyear }}

                                                            </div>

                                                        </div>

                                                        <div>

                                                            <div data-toggle="tooltip" data-trigger="hover" data-placement="top"

                                                                title="" data-original-title="Period"

                                                                style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#989898;

                                        border:1px solid #A6A6A6;

                                        border-radius: 5px;



                                        margin-right: 0.675rem;"

                                                                class="px-2 bubble_period">

                                                                {{-- {{ $q->account_no }} --}}

                                                                {{ $q->period }}

                                                            </div>

                                                        </div>

                                                        {{-- @if ($q->credit > 0)

                                                            <div>

                                                                <div style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#C41E3A;

                                        border:1px solid #C41E3A;

                                        border-radius: 5px;



                                        margin-right: 0.675rem;"

                                                                    class="px-2 bubble_credit">

                                                                    {{ '$ ' . number_format($amount, 2) }}

                                                                </div>

                                                            </div>

                                                        @else

                                                            <div>

                                                                <div style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#4194F6;

                                        border:1px solid #4194F6;

                                        border-radius: 5px;



                                        margin-right: 0.675rem;"

                                                                    class="px-2 bubble_debit">

                                                                    {{ '$ ' . number_format($amount, 2) }}

                                                                </div>

                                                            </div>

                                                        @endif --}}

                                                    </div>

                                                </div>

                                                <div style="position: absolute;right: 10px;top: 6px;">

                                                    <span class="bubble_edit_no"

                                                        style="

                                                        float:right;

                                font-family: Calibri;

                                line-height: 1.5 !important;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#4EA833;

                                        border:1px solid #4EA833;

                                        border-radius: 5px;

                                        padding-left: 10px;padding-right: 10px;">#{{ $q->editNo }}</span>

                                                </div>

                                                <div class="d-flex flex-row justify-content-end"

                                                    style="margin-top: 20px;position: absolute;right: 10px;bottom: 3px;">

                                                    <?php     if(Auth::check()){

                                                        if(@Auth::user()->role!='read'){ ?>

                                                    <div class="ml-1 " style="display: flex;align-items: center;">

                                                        @if ($q->credit > 0)

                                                            <div>

                                                                <div style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#C41E3A;

                                        border:1px solid #C41E3A;

                                        border-radius: 5px;"

                                                                    class="px-2 bubble_credit">

                                                                    {{-- {{ $q->account_no }} --}}

                                                                    {{ '$ ' . number_format($amount, 2) }}

                                                                </div>

                                                            </div>

                                                        @else

                                                            <div>

                                                                <div style="line-height: 1.6;

                                        font-family: Calibri;

                                        width: fit-content;

                                        font-size: 9pt;

                                        color:#4194F6;

                                        border:1px solid #4194F6;

                                        border-radius: 5px;"

                                                                    class="px-2 bubble_debit">

                                                                    {{-- {{ $q->account_no }} --}}

                                                                    {{ '$ ' . number_format($amount, 2) }}

                                                                </div>

                                                            </div>

                                                        @endif

                                                    </div>

                                                    @php

                                                        $date_new = trim($q->date);



                                                        $formattedDate = \Carbon\Carbon::createFromFormat(

                                                            'dmY',

                                                            $date_new,

                                                        )->format('Y-m-d');

                                                        $formattedDateOutput = \Carbon\Carbon::parse(

                                                            $formattedDate,

                                                        )->format('d/m/Y');

                                                    @endphp

                                                    <div class="ActionIcon    ml-1" style="border-radius: 1rem"

                                                        data-toggle="tooltip" data-trigger="hover" data-placement="top"

                                                        title="" data-original-title="{{ $formattedDateOutput }}">

                                                        <a href="javascript:;" data="{{ $q->edit_no }}" class=" ">

                                                            <img src="{{ asset('public') }}/img/icon-bubble-date.png?cache=1"

                                                                width="28px">

                                                        </a>

                                                    </div>

                                                    {{-- <div class="ActionIcon    ml-1" style="border-radius: 1rem">

                                                        <a href="javascript:;" data="{{ $q->edit_no }}" class="btnEdit ">

                                                            <img src="{{ asset('public') }}/icons2/icon-edit-grey.png?cache=1"

                                                                width="25px">

                                                        </a>

                                                    </div>



                                                    <div class="ActionIcon  ml-1 " style="border-radius: 1rem;">

                                                        <a href="javascript:;" class=" btnDelete" data="{{ $q->edit_no }}">

                                                            <img src="{{ asset('public') }}/icons2/icon-delete-grey.png?cache=1"

                                                                width="25px">

                                                        </a>



                                                    </div> --}}

                                                    <div class="ActionIcon px-0 ml-2    " style="border-radius: 5px">

                                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"

                                                            aria-expanded="false" href="javascript:;" c>



                                                            <img src="{{ asset('public') }}/img/dots.png?cache=1">

                                                        </a>

                                                        <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary"

                                                            style="min-width: 9rem;">



                                                            <a href="javascript:;" data="{{ $q->edit_no }}"

                                                                class="dropdown-item d-flex align-items-center px-0 btnEdit ">

                                                                <div style="width: 32;  padding-left: 2px"><img

                                                                        src="{{ asset('public') }}/icons2/icon-edit-grey.png?cache=1"

                                                                        width="25px"> Edit</div>

                                                            </a>

                                                            <a href="javascript:;"

                                                                class="dropdown-item d-flex align-items-center px-0 btnDelete"

                                                                data="{{ $q->edit_no }}">

                                                                <div style="width: 32;  padding-left: 2px"><img

                                                                        src="{{ asset('public') }}/icons2/icon-delete-grey.png?cache=1"

                                                                        width="25px"> Delete</div>

                                                            </a>

                                                        </div>

                                                    </div>

                                                    <?php } }?>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endforeach



                            </div>

                        </div>



                    </div>



                    <div class="modal fade" id="ExportModalNew" tabindex="-1" role="dialog" data-backdrop="static"

                        aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Export Journals</span>

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

                                            <div class="col-sm-12">

                                                <p>Are you sure you wish to export all journals from the current view </p>

                                                <p>Total no of journals to export: {{ $qry->total() }} </p>

                                            </div>

                                        </div>



                                    </div>

                                    <div class="block-content block-content-full   pt-4"

                                        style="padding-left: 9mm;padding-right: 9mm">

                                        <a class="btn-export"

                                            data-url="{{ url('/export-excel-journals') }}?{{ $_SERVER['QUERY_STRING'] }}"

                                            href="javascript:void();" class="btn mr-3 btn-new  ">Ok</a>

                                        <button type="button" class="btn     btn-new-secondary"

                                            data-dismiss="modal">Cancel</button>



                                    </div>

                                </div>



                            </div>

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



















                    <form class="mb-0 pb-0 form-journals" id="form-edit-client-journal" method="post">

                        @csrf

                        <div class="modal fade" id="EditClientJournalModal" tabindex="-1" role="dialog"

                            data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                                <div class="modal-content">

                                    <div class="block  block-transparent mb-0" style="padding-top: 24px !important;">

                                        <div class="block-header pb-0  ">

                                            <span class="b e section-header">Edit Journal</span>

                                            <div class="block-options">

                                                <button type="button" class="btn-block-option" id="close-edit-journal-modal">

                                                    <i class="fa fa-fw fa-times"></i>

                                                </button>

                                            </div>







                                        </div>



                                        <div class="block-content new-block-content pt-0 pb-0 ">



                                        </div>

                                    </div>



                                </div>

                            </div>

                        </div>





                    </form>





                    <form id="ImportJournalForm" action="" method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" data-backdrop="static"

                            aria-labelledby="modal-block-large" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">

                                <div class="modal-content">

                                    <div class="block  block-transparent mb-0">

                                        <div class="block-header   ">

                                            <span class="b e section-header">Import Journals</span>

                                            <div class="block-options">

                                                <button type="button" class="btn-block-option" data-dismiss="modal"

                                                    aria-label="Close">

                                                    <i class="fa fa-fw fa-times"></i>

                                                </button>

                                            </div>

                                        </div>



                                        <div class="block-content pt-0  mt-2">

                                            <div class="row form-group">

                                                <label class="col-sm-4">Client</label>

                                                <div class="col-sm-7">

                                                    <select type="text" class="form-control select2" name="client">

                                                        <option value="" selected>Client</option>

                                                        @foreach ($clients as $c)

                                                            <option value="{{ $c->id }}"

                                                                {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>

                                                                {{ $c->display_name }}

                                                            </option>

                                                        @endforeach

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="row form-group">

                                                <label class="col-sm-4">Import Type</label>

                                                <div class="col-sm-7">

                                                    <select class="form-control" name="import_type">

                                                        <option value="Standard" selected>Standard</option>

                                                        <option value="Translated">Translated</option>

                                                    </select>

                                                    <a href="javascript:;"

                                                        style="position: absolute;

                                    right: -20px;

                                    top: 10px;"

                                                        class="import-type-info"

                                                        data-standard="Imports exactly what is in the CSV"

                                                        data-translated="Imports data usually coming from bank statements and extracts taxes and portionting, fiscal year and period based on month and year of transaction">

                                                        <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"

                                                            width="20px">

                                                    </a>

                                                </div>

                                            </div>

                                            <div class="row form-group">

                                                <label class="col-sm-4">Upload Csv</label>

                                                <div class="col-sm-7    p      ">

                                                    <input type="file" name="file" class="form-control"

                                                        accept=".csv,.xlsx">

                                                </div>

                                            </div>





                                        </div>

                                        <div class="block-content block-content-full  d-flex justify-content-between "

                                            style="padding-left: 9mm;">

                                            <a href="{{ asset('public/standard-import.csv') }}" download=""

                                                id="import-example-sheet" class="comments-section-text mr-2"

                                                style="color: #E54643 !important;"> Example CSV

                                            </a>

                                            <button type="submit" class="btn  btn-new">Proceed</button>





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









                    <form id="filterClientJournalForm" method="GET" class="mb-0 pb-0">

                        <div class="modal fade" id="filterClientJournalModal" tabindex="-1" role="dialog"

                            data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">

                                <div class="modal-content">

                                    <div class="block  block-transparent mb-0">

                                        <div class="block-header pb-0  ">

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

                                                <label class="col-sm-4">Date Created</label>

                                                <div class="col-sm-4 form-group">

                                                    <input type="text" class="form-control js-flatpickr bg-white"

                                                        name="client_mode_filter_date_created" placeholder="Date Created">

                                                </div>

                                            </div>



                                            <div class="row">



                                                <label class=" col-sm-4  " for="example-hf-email">Period</label>



                                                <div class="col-sm-4 form-group">



                                                    <select type="" class="form-control    selectpicker "

                                                        id="client-mode-filter-period"

                                                        data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                        data-live-search="true" title="All" value=""

                                                        name="client_mode_filter_period[]" multiple="">

                                                        @for ($p = 1; $p <= 12; $p++)

                                                            <option value="{{ $p }}">{{ $p }}</option>

                                                        @endfor

                                                    </select>

                                                </div>

                                            </div>

                                            <div class="row">

                                                <label class="  col-sm-4 " for="example-hf-email">Source</label>

                                                <div class="col-sm-4 form-group">



                                                    <select type="" class="form-control    selectpicker "

                                                        id="client-mode-filter-source"

                                                        data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                        data-live-search="true" title="All" value=""

                                                        name="client_mode_filter_source[]" multiple="">

                                                        @foreach ($sources as $sc)

                                                            <option value="{{ $sc->id }}">{{ $sc->source_code }}</option>

                                                        @endforeach

                                                    </select>

                                                </div>

                                            </div>



                                            <div class="row">

                                                <label class="  col-sm-4 " for="example-hf-email">Account No</label>

                                                <div class="col-sm-4 form-group">



                                                    <select type="" class="form-control    selectpicker "

                                                        id="client-mode-filter-account"

                                                        data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                        data-live-search="true" title="All" value=""

                                                        name="client_mode_filter_account[]" multiple="">



                                                    </select>

                                                </div>





                                            </div>





                                            <div class="row">

                                                <label class="   col-sm-4" for="example-hf-email">Ref No.</label>

                                                <div class="col-sm-4 form-group">



                                                    <select type="" class="form-control    selectpicker "

                                                        id="client-mode-filter-ref"

                                                        data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                        data-live-search="true" title="All" value=""

                                                        name="client_mode_filter_ref[]" multiple="">



                                                    </select>

                                                </div>



                                            </div>











                                        </div>



                                    </div>

                                    <div class="block-content block-content-full  text-right pt-4" style="padding-left: 9mm;">



                                        <a href="javascript:;" id="gifi-clear-filter-1" class="btn  mr-3   btn-new-secondary "

                                            style="">Clear</a>

                                        <button type="submit" class="btn btn-new">Apply</button>



                                    </div>

                                </div>



                            </div>

                        </div>

                </div>

                </form>













                <form id="filterJournalForm" method="GET" action="{{ url('/journals') }}??{{ $_SERVER['QUERY_STRING'] }}"

                    class="mb-0 pb-0">

                    <input type="hidden" name="_applied" value="1">

                    <div class="modal fade" id="filterJournalModal" tabindex="-1" role="dialog" data-backdrop="static"

                        aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " style="max-width: 625px"

                            role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Filters</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 ">









                                        {{-- <div class="row">

                                <label class="col-sm-4">Edit No</label>

                                <div class="col-sm-7 form-group">



                                    <?php

                                    //$edits = DB::table('journals')->where('is_deleted', 0)->orderByDesc('edit_no')->pluck('edit_no')->toArray();

                                    ?>

                                    <select class="form-control" id="filter_edit_no" name="filter_edit_no">

                                        <option value="">Select</option>

                                        @foreach ($edits as $e)

                                        <option value="{{$e}}" @if (@$_GET['filter_edit_no'] == $e) selected @endif>

                                            {{$e}}</option>

                                        @endforeach

                                    </select>

                                </div>

                            </div> --}}

                                        {{--    <div class="row">

                                        <label class="col-sm-4">Client</label>

                                        <div class="col-sm-7 form-group">



                                            <select class="form-control select2" id="filter_client" name="filter_client">

                                                <option value="" selected>Select</option>

                                                @foreach ($clients as $c)

                                                    <option value="{{ $c->id }}"

                                                        @if (@$_GET['filter_client'] == $c->id) selected @endif> {{ $c->company }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                    </div> --}}





                                        <div class="row">

                                            <label class="  col-sm-4 " for="example-hf-email">Client</label>

                                            <div class="col-sm-7 form-group">



                                                <select type="" class="form-control    selectpicker " id="filter-client"

                                                    data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                    data-live-search="true" title="All" value="" name="filter_client[]"

                                                    multiple="">

                                                    <?php

                                                    

                                                    $clientsArr = [];

                                                    if (@$_GET['filter_client']) {

                                                        if (count($_GET['filter_client']) > 0) {

                                                            $clientsArr = $_GET['filter_client'];

                                                        }

                                                    }

                                                    ?>

                                                    @foreach ($clients as $c)

                                                        <option value="{{ $c->id }}"

                                                            @if (in_array($c->id, $clientsArr) || Auth::user()->default_client == $c->id) selected @endif>

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>







                                        {{-- <div class="row">

                                        <label class="col-sm-4">Fiscal Year</label>

                                        <div class="col-sm-7 form-group">



                                            <?php

                                            $fiscal_years = DB::table('journals')->where('is_deleted', 0)->distinct('fyear')->orderByDesc('fyear')->pluck('fyear')->toArray('fyear');

                                            ?>

                                            <select class="form-control select2" id="filter_fiscal_year"

                                                name="filter_fiscal_year">

                                                <option value="" selected>Select</option>

                                                @foreach ($fiscal_years as $fy)

                                                    <option value="{{ $fy }}"

                                                        @if (@$_GET['filter_fiscal_year'] == $fy) selected @endif>

                                                        {{ $fy }}</option>

                                                @endforeach

                                            </select>

                                        </div>

                                    </div> --}}







                                        <div class="row">

                                            <label class="  col-sm-4 " for="example-hf-email">Fiscal Year</label>

                                            <div class="col-sm-7 form-group">



                                                <select type="" class="form-control    selectpicker "

                                                    id="filter_fiscal_year" data-style="btn-outline-light border text-dark"

                                                    data-actions-box="true" data-live-search="true" title="All"

                                                    value="" name="filter_fiscal_year[]" multiple="">

                                                    <?php

                                                    

                                                    $fiscalYearArr = [];

                                                    if (@$_GET['filter_fiscal_year']) {

                                                        if (count($_GET['filter_fiscal_year']) > 0) {

                                                            $fiscalYearArr = $_GET['filter_fiscal_year'];

                                                        }

                                                    }

                                                    $fiscal_years = DB::table('journals')->whereNotNull('fyear')->where('is_deleted', 0)->distinct('fyear')->orderByDesc('fyear')->pluck('fyear')->toArray('fyear');

                                                    ?>

                                                    @foreach ($fiscal_years as $fy)

                                                        <option value="{{ $fy }}"

                                                            @if (in_array($fy, $fiscalYearArr) || (empty($fiscalYearArr) && Auth::user()->default_fiscal_year == $fy)) selected @endif>

                                                            {{ $fy }}</option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>







                                        <div class="row">

                                            <label class="col-sm-4" for="example-hf-email">Period</label>

                                            <div class="col-sm-7 form-group">



                                                <select type="" class="form-control    selectpicker " id="filter-period"

                                                    data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                    data-live-search="true" title="All" value="" name="filter_period[]"

                                                    multiple="">

                                                    <?php

                                                    $periodsArr = [];

                                                    if (@$_GET['filter_period']) {

                                                        if (count($_GET['filter_period']) > 0) {

                                                            $periodsArr = $_GET['filter_period'];

                                                        }

                                                    }

                                                    ?>

                                                    @for ($p = 1; $p <= 12; $p++)

                                                        <option value="{{ $p }}"

                                                            @if (in_array($p, $periodsArr)) selected @endif>{{ $p }}

                                                        </option>

                                                    @endfor

                                                </select>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <label class="  col-sm-4 " for="example-hf-email">Source</label>

                                            <div class="col-sm-7 form-group">



                                                <select type="" class="form-control    selectpicker " id="filter-source"

                                                    data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                    data-live-search="true" title="All" value="" name="filter_source[]"

                                                    multiple="">

                                                    <?php

                                                    

                                                    $sourcesArr = [];

                                                    if (@$_GET['filter_source']) {

                                                        if (count($_GET['filter_source']) > 0) {

                                                            $sourcesArr = $_GET['filter_source'];

                                                        }

                                                    }

                                                    ?>

                                                    @foreach ($sources as $sc)

                                                        <option value="{{ $sc->id }}"

                                                            @if (in_array($sc->id, $sourcesArr)) selected @endif>

                                                            {{ $sc->source_code }}</option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <label class="  col-sm-4 " for="example-hf-email">Ref No.</label>

                                            <div class="col-sm-7 form-group">

                                                <?php

                                                $refs = DB::table('journals')->where('is_deleted', 0)->distinct('ref_no')->orderBy('ref_no', 'ASC')->pluck('ref_no')->toArray();

                                                $refsArr = [];

                                                if (@$_GET['filter_ref']) {

                                                    if (count($_GET['filter_ref']) > 0) {

                                                        $refsArr = $_GET['filter_ref'];

                                                    }

                                                }

                                                ?>

                                                <select type="" class="form-control    selectpicker " id="filter-ref"

                                                    data-style="btn-outline-light border text-dark" data-actions-box="true"

                                                    data-live-search="true" title="All" value=""

                                                    name="filter_ref[]" multiple="">

                                                    @foreach ($refs as $r)

                                                        <option value="{{ $r }}"

                                                            @if (in_array($r, $refsArr)) selected @endif>{{ $r }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>

                                        <div class="row">



                                            <label class=" col-sm-4  " for="example-hf-email">Account No</label>

                                            <div class="col-sm-7 form-group">



                                                <select type="" class="form-control    selectpicker "

                                                    id="filter_account" data-style="btn-outline-light border text-dark"

                                                    data-actions-box="true" data-live-search="true" title="All"

                                                    value="" name="filter_account[]" multiple="">

                                                    <?php

                                                    $gifiArr = [];

                                                    if (@$_GET['filter_account']) {

                                                        if (count($_GET['filter_account']) > 0) {

                                                            $gifiArr = $_GET['filter_account'];

                                                        }

                                                    }

                                                    ?>

                                                    @foreach ($gifis as $g)

                                                        <option value="{{ $g->account_no }}"

                                                            @if (in_array($g->account_no, $gifiArr)) selected @endif>{{ $g->account_no }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>

                                        <div class="row">



                                            <label class=" col-sm-4  " for="example-hf-email">Edit No.</label>

                                            <div class="col-sm-7 form-group">

                                                <input class="form-control" type="number" name="filter_edit_no"

                                                    value="{{ @$_GET['filter_edit_no'] }}" id="filter_edit_no">



                                            </div>

                                        </div>



                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <a href="{{ url('/journals') }}" class="btn mr-3    btn-new-secondary float-right"

                                            style="">Clear</a>

                                        <button type="submit" class="btn btn-new">Apply</button>



                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>

                </form>







                <div class="modal fade" id="AccountChartModal" tabindex="-1" role="dialog" data-backdrop="static"

                    aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " style="max-width: 475px !important;"

                        role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Accounts Chart</span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"

                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 " style="height: 68vh;overflow-y:scroll;">

                                    <div class="row client-accounts">



                                    </div>



                                </div>

                                <div class="block-content block-content-full  text-right pt-4 d-none "

                                    id="select-account-no-block" style="padding-left: 9mm;">





                                    <button type="button" id="select-account-no" class="btn btn-new">Select</button>



                                </div>

                            </div>



                        </div>

                    </div>

                </div>

                <div class="modal fade" id="sourceModal" tabindex="-1" role="dialog" data-backdrop="static"

                    aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " style="max-width: 475px !important;"

                        role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Sources</span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"

                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 " style="height: 68vh;overflow-y:scroll;">

                                    <div class="row">

                                        @foreach ($sources as $sj)

                                            <div class="col-md-12">

                                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  account-item "

                                                    style="cursor:pointer;" account-no="{{ $sj->id }}"

                                                    data-name="{{ $sj->source_code }}">



                                                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">





                                                        <div class="  " style="width:100%">



                                                            <div class="d-flex p-2">







                                                                <p class="mr-3  mb-0 "

                                                                    style="font-family: Signika; font-size: 12pt; color: #595959">

                                                                    {{ $sj->source_code }}</p>

                                                                <p class=" text-truncate mb-0"

                                                                    style="font-family: Signika; font-size: 12pt;font-weight: 100;color: #595959"

                                                                    data="4">{{ $sj->source_description }}</p>

                                                            </div>







                                                        </div>



                                                    </div>

                                                </div>



                                            </div>

                                        @endforeach

                                    </div>



                                </div>

                                <div class="block-content block-content-full  text-right pt-4" id="select-account-no-block"

                                    style="padding-left: 9mm;">





                                    <button type="button" id="select-source" class="btn btn-new">Select</button>



                                </div>

                            </div>



                        </div>

                    </div>

                </div>

                <div class="modal fade" id="editSourceModel" tabindex="-1" role="dialog" data-backdrop="static"

                    aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " style="max-width: 475px !important;"

                        role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Sources</span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"

                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 " style="height: 68vh;overflow-y:scroll;">

                                    <div class="row">

                                        @foreach ($sources as $sj)

                                            <div class="col-md-12">

                                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  account-item "

                                                    style="cursor:pointer;" account-no="{{ $sj->id }}"

                                                    data-name="{{ $sj->source_code }}">



                                                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">





                                                        <div class="  " style="width:100%">



                                                            <div class="d-flex p-2">







                                                                <p class="mr-3  mb-0 "

                                                                    style="font-family: Signika; font-size: 12pt; color: #595959">

                                                                    {{ $sj->source_code }}</p>

                                                                <p class=" text-truncate mb-0"

                                                                    style="font-family: Signika; font-size: 12pt;font-weight: 100;color: #595959"

                                                                    data="4">{{ $sj->source_description }}</p>

                                                            </div>







                                                        </div>



                                                    </div>

                                                </div>



                                            </div>

                                        @endforeach

                                    </div>



                                </div>

                                <div class="block-content block-content-full  text-right pt-4" id="select-account-no-block"

                                    style="padding-left: 9mm;">





                                    <button type="button" id="select-source-edit" class="btn btn-new">Select</button>



                                </div>

                            </div>



                        </div>

                    </div>

                </div>





                <form class="mb-0 pb-0 create-journal-report" id="form-journal-report" action="{{ url('/journal-reports') }}"

                    method="get">

                    @csrf

                    <div class="modal fade" id="JournalReportModal" tabindex="-1" role="dialog" data-backdrop="static"

                        aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Journal Report</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 ">



                                        <div class="row form-group  ">

                                            <div class="col-sm-3">



                                                <label class="col-form-label ">Client</label>



                                            </div>



                                            <div class="col-sm-6 ">



                                                <select type="" name="report_client" id="report_client"

                                                    class="form-control select2" placeholder="">

                                                    <option value="" selected>Select</option>

                                                    @foreach ($clients as $c)

                                                        <option value="{{ $c->id }}"

                                                            {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>



                                        <div class="row justify-content- form-group  push ">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Fiscal Year</label>

                                            </div>



                                            <div class="col-sm-4">



                                                <?php

                                                $fiscal_years = DB::table('journals')->where('is_deleted', 0)->distinct('fyear')->orderByDesc('fyear')->pluck('fyear')->toArray('fyear');

                                                ?>

                                                <select class="form-control select2" id="report_fiscal_year"

                                                    name="report_fiscal_year">

                                                    <option value="" selected>Select</option>

                                                    @foreach ($fiscal_years as $fy)

                                                        <option value="{{ $fy }}"

                                                            {{ Auth::user()->default_fiscal_year == $fy ? 'selected' : '' }}>

                                                            {{ $fy }}</option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>



                                        <div class="row justify-content- form-group  push ">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Period</label>

                                            </div>



                                            <div class="col-sm-4">



                                                <select type="" class="form-control    selectpicker "

                                                    id="report_period" data-style="btn-outline-light border text-dark"

                                                    data-actions-box="true" data-live-search="true" title="All"

                                                    value="" name="report_period[]" multiple="">

                                                    @for ($p = 1; $p <= 12; $p++)

                                                        <option value="{{ $p }}">{{ $p }}</option>

                                                    @endfor

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row form-group  ">

                                            <div class="col-sm-3">



                                                <label class="col-form-label">Source</label>



                                            </div>



                                            <div class="col-sm-4 ">



                                                <select type="" class="form-control    selectpicker "

                                                    id="report_source" data-style="btn-outline-light border text-dark"

                                                    data-actions-box="true" data-live-search="true" title="All"

                                                    value="" name="report_source[]" multiple="">

                                                    @foreach ($sources as $sc)

                                                        <option value="{{ $sc->id }}">{{ $sc->source_code }}</option>

                                                    @endforeach

                                                </select>



                                            </div>



                                        </div>



                                        <div class="row form-group  ">

                                            <div class="col-sm-3">



                                                <label class="col-form-label">Account No</label>



                                            </div>



                                            <div class="col-sm-4 ">

                                                <select type="" class="form-control    selectpicker "

                                                    id="report_account" data-style="btn-outline-light border text-dark"

                                                    data-actions-box="true" data-live-search="true" title="All"

                                                    value="" name="report_account[]" multiple="">

                                                    @foreach ($gifis as $g)

                                                        <option value="{{ $g->account_no }}">{{ $g->account_no }}</option>

                                                    @endforeach

                                                </select>



                                            </div>



                                        </div>



                                        <div class="row justify-content- form-group  push">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Rollup</label>

                                            </div>



                                            <div class="col-sm-3">



                                                <input class="js-rangeslider" id="report_rollups" name="report_rollups"

                                                    data-values="None, Source, Account" data-from="0">

                                            </div>



                                        </div>



                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="button" class="btn  mr-3   btn-new-secondary" style=""

                                            id="clear_report_filters">Clear</button>

                                        <button type="submit" class="btn btn-new ">Apply</button>





                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>













                <form class="mb-0 pb-0 create-remittance-report" id="form-remittance-report"

                    action="{{ url('/journals/report/remittance-status') }}" method="get">

                    <div class="modal fade" id="RemittanceReportModal" tabindex="-1" role="dialog"

                        data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Remittance Report</span>

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

                                                <select class="form-control select2" id="report_month" name="report_month">

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

                                            <div class="col-sm-4">

                                                <select class="form-control select2" id="report_year" name="report_year">

                                                    @for ($i = intval(date('Y') + 3); $i >= 1940; $i--)

                                                        <option value="{{ $i }}">{{ $i }}</option>

                                                    @endfor

                                                </select>

                                            </div>

                                        </div>





                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="button" class="btn  mr-3   btn-new-secondary" style=""

                                            id="clear_report_filters">Clear</button>

                                        <button type="submit" class="btn btn-new ">Run</button>





                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>









                <form class="mb-0 pb-0 create-progress-report" id="form-progress-report"

                    action="{{ url('/journals/report/progress') }}" method="get">

                    <div class="modal fade" id="ProgressReportModal" tabindex="-1" role="dialog" data-backdrop="static"

                        aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Progress Report</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 ">



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

                                                            {{ isset(Auth::user()->default_client) ? (Auth::user()->default_client == $c->id ? 'selected' : '') : '' }}>

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>

                                        </div>



                                        <div class="row justify-content- form-group  push">

                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Type</label>

                                            </div>

                                            <div class="col-sm-1">



                                                <input class="js-rangeslider" id="report_type" name="report_type"

                                                    data-values="By Fiscal Year,By Period" data-from="0">

                                            </div>



                                        </div>



                                        <div class="row justify-content- form-group  push d-none" id="report_fiscal_year_row">

                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Fiscal Year</label>

                                            </div>

                                            <div class="col-sm-4">

                                                <select class="form-control select2" id="report_fiscal_year"

                                                    name="report_fiscal_year">

                                                    @for ($i = intval(date('Y') + 3); $i >= 1940; $i--)

                                                        <option value="{{ $i }}"

                                                            {{ Auth::user()->default_fiscal_year == $i ? 'selected' : '' }}>

                                                            {{ $i }}</option>

                                                    @endfor

                                                </select>

                                            </div>

                                        </div>



                                        <div class="row justify-content- form-group  push " id="report_fiscal_years_row">

                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Fiscal Years</label>

                                            </div>

                                            <div class="col-sm-4">

                                                <select type="" class="form-control    selectpicker "

                                                    id="report_fiscal_years" data-style="btn-outline-light border text-dark"

                                                    data-actions-box="false" data-max-options="7" data-live-search="true"

                                                    title="All" value="" name="report_fiscal_years[]"

                                                    multiple="">

                                                    @for ($i = intval(date('Y') + 3); $i >= 1940; $i--)

                                                        <option value="{{ $i }}"

                                                            {{ Auth::user()->default_fiscal_year == $i ? 'selected' : '' }}>

                                                            {{ $i }}</option>

                                                    @endfor

                                                </select>

                                            </div>

                                        </div>



                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="button" class="btn  mr-3   btn-new-secondary" style=""

                                            id="clear_report_filters">Clear</button>

                                        <button type="submit" class="btn btn-new ">Run</button>





                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>











                <form class="mb-0 pb-0 " id="form-journal-batch-update" action="" method="POST">

                    @csrf

                    <div class="modal fade" id="JournalBatchUpdateModal" tabindex="-1" role="dialog"

                        data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content" style="width: 65%;">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Batch Journal Updates</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 " style="min-height: 70vh">

                                        <h5 class="text-info total-selected-batch" style="font-family: Signika;">0 journals

                                            selected</h5>

                                        <div class="row justify-content- form-group  push">





                                            <div class="col-sm-3">

                                                <label class="col-form-label">Mod Type</label>

                                            </div>



                                            <div class="col-sm-2">

                                                <input class="js-rangeslider" id="batch_update_mod_type"

                                                    name="batch_update_mod_type" data-values="Update, Delete" data-from="0">

                                            </div>



                                            <style>

                                                .label-delete {

                                                    display: inline-block;

                                                    padding: 5px 10px;

                                                    border: 1px solid red;

                                                    color: red;

                                                    border-radius: 20px;

                                                    cursor: pointer;

                                                    text-align: center;

                                                }



                                                .label-delete:hover {

                                                    background-color: red;

                                                    color: white;

                                                }

                                            </style>

                                            <div class="col-sm-2">

                                                <label class="label-delete d-none" id="deleteLabel">DELETE</label>

                                            </div>



                                        </div>

                                        <div class="row form-group  form-group-edit">



                                            <div class="col-sm-3">



                                                <label class="col-form-label ">Set Client</label>

                                            </div>

                                            <div class="col-sm-6 ">

                                                @php

                                                    $all_active_clients = DB::table('clients')

                                                        ->where('is_deleted', 0)

                                                        ->where('client_status', 1)

                                                        ->orderBy('display_name', 'asc')

                                                        ->get();

                                                @endphp

                                                <select type="" name="batch_update_client" id="batch_update_client"

                                                    class="form-control" placeholder="">

                                                    <option value="" selected>No Change</option>

                                                    @foreach ($all_active_clients as $c)

                                                        <option value="{{ $c->id }}">

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row justify-content- form-group  push form-group-edit">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Set Month</label>

                                            </div>



                                            <div class="col-sm-6">

                                                <select class="form-control" id="batch_update_month"

                                                    name="batch_update_month">

                                                    <option value="" selected>No Change</option>

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





                                        <div class="row justify-content- form-group  push form-group-edit">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Set Year</label>

                                            </div>



                                            <div class="col-sm-6">

                                                <select class="form-control" id="batch_update_year" name="batch_update_year">

                                                    <option value="" selected>No change</option>

                                                    @for ($fy = 2000; $fy <= 2100; $fy++)

                                                        <option value="{{ $fy }}">{{ $fy }}</option>

                                                    @endfor

                                                </select>

                                            </div>



                                        </div>



                                        <div class="row justify-content- form-group  push form-group-edit">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Set Period</label>

                                            </div>



                                            <div class="col-sm-6">

                                                <select class="form-control" id="batch_update_period"

                                                    name="batch_update_period">

                                                    <option value="" selected>No change</option>

                                                    @for ($fy = 1; $fy <= 12; $fy++)

                                                        <option value="{{ $fy }}">Period {{ $fy }}

                                                        </option>

                                                    @endfor

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row justify-content- form-group  push form-group-edit">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Set Fiscal Year</label>

                                            </div>



                                            <div class="col-sm-6">

                                                <select class="form-control" id="batch_update_fiscal_year"

                                                    name="batch_update_fiscal_year">

                                                    <option value="" selected>No change</option>

                                                    @for ($fy = 2000; $fy <= 2100; $fy++)

                                                        <option value="{{ $fy }}">

                                                            {{ $fy }}</option>

                                                    @endfor

                                                </select>

                                            </div>



                                        </div>







                                        <div class="row form-group  form-group-edit">

                                            <div class="col-sm-3">



                                                <label class="col-form-label">Set Source</label>



                                            </div>



                                            <div class="col-sm-6 ">

                                                <select class="form-control" id="batch_update_source"

                                                    name="batch_update_source">

                                                    <option value="" selected>No change</option>

                                                    @foreach ($sources as $sc)

                                                        <option value="{{ $sc->id }}">{{ $sc->source_code }}</option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="reset" class="btn  mr-3   btn-new-secondary"

                                            style="">Clear</button>

                                        <button type="submit" class="btn btn-new ">Update</button>

                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>











                <form class="mb-0 pb-0 " id="form-reindex-fiscal-year-journals"

                    action="{{ url('/journals/fyear/reindex') }}" method="POST">

                    @csrf

                    <div class="modal fade" id="JournalFYReIndexModal" tabindex="-1" role="dialog"

                        data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Reindex Fiscal Year Journals</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 ">

                                        <p class="" style="font-family: Signika;">Reindexing will reset the edit no on all

                                            journals for the Client and Fiscal Year selected</p>

                                        <div class="row form-group  ">



                                            <div class="col-sm-3">



                                                <label class="col-form-label ">Client</label>



                                            </div>



                                            <div class="col-sm-6 ">



                                                <select type="" name="reindex_client" id="reindex_client"

                                                    class="form-control select2" placeholder="">

                                                    <option value="" selected>Select Client</option>

                                                    @foreach ($all_active_clients as $c)

                                                        <option value="{{ $c->id }}"

                                                            {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row justify-content- form-group  push ">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Fiscal Year</label>

                                            </div>



                                            <div class="col-sm-6">

                                                <select class="form-control select2" id="reindex_fiscal_year"

                                                    name="reindex_fiscal_year">

                                                    <option value="" selected>Select Fiscal</option>



                                                </select>

                                            </div>



                                        </div>

                                        <p class=" found-journals pl-5 mb-0" style="font-family: Signika;"></p>



                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="submit" class="btn btn-new ">Proceed</button>

                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>







                <div class="modal fade" id="BatchJournalUpdatesConfirmationModal" tabindex="-1" role="dialog"

                    data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Batch Journal Updates - Confirmation</span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"

                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 confirmation-container">





                                </div>

                                <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                    <button type="button" id="cancel-batch-update" class="btn  mr-3   btn-new-secondary"

                                        style="">Cancel</button>

                                    <button type="button" id="proceed-batch-update" class="btn btn-new ">Proceed</button>

                                </div>

                            </div>



                        </div>

                    </div>

                </div>



                <div class="modal fade" id="BatchJournalDeleteConfirmationModal" tabindex="-1" role="dialog"

                    data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-bac " role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Batch Journal Deletetion - Confirmation</span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"

                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 confirmation-container">





                                </div>

                                <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                    <button type="button" id="cancel-batch-delete" class="btn  mr-3   btn-new-secondary"

                                        style="">Cancel</button>

                                    <button type="button" id="proceed-batch-delete" class="btn btn-new ">Proceed</button>

                                </div>

                            </div>



                        </div>

                    </div>

                </div>







                <form class="mb-0 pb-0 " id="form-trial-balance-report" action="{{ url('/journals/report/trial-balance') }}"

                    method="GET">



                    <div class="modal fade" id="TrialBalanceModal" tabindex="-1" role="dialog" data-backdrop="static"

                        aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Trial Balance</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 ">



                                        <div class="row form-group  ">



                                            <div class="col-sm-3">



                                                <label class="col-form-label ">Client</label>



                                            </div>



                                            <div class="col-sm-6 ">



                                                <select type="" name="tb_client" id="trial_balance_client"

                                                    class="form-control select2" placeholder="">

                                                    <option value="" selected>Select Client</option>

                                                    @foreach ($all_active_clients as $c)

                                                        <option value="{{ $c->id }}"

                                                            {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row justify-content- form-group  push ">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Fiscal Year</label>

                                            </div>



                                            <div class="col-sm-4">

                                                <select class="form-control select2" id="trial_balance_fiscal_year"

                                                    name="tb_fiscal_year">

                                                    <option value="" selected>Select Fiscal</option>

                                                    @foreach ($fiscal_years as $fy)

                                                        <option value="{{ $fy }}"

                                                            {{ Auth::user()->default_fiscal_year == $fy ? 'selected' : '' }}>

                                                            {{ $fy }}</option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="reset" class="btn  mr-3   btn-new-secondary"

                                            style="">Clear</button>

                                        <button type="submit" class="btn btn-new ">Run</button>

                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>













                <form class="mb-0 pb-0 " id="form-financial-statement"

                    action="{{ url('/journals/report/financial-statement') }}" method="GET">



                    <div class="modal fade" id="FinancialStatementModal" tabindex="-1" role="dialog"

                        data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">

                            <div class="modal-content">

                                <div class="block  block-transparent mb-0">

                                    <div class="block-header pb-0  ">

                                        <span class="b e section-header">Financial Statement</span>

                                        <div class="block-options">

                                            <button type="button" class="btn-block-option" data-dismiss="modal"

                                                aria-label="Close">

                                                <i class="fa fa-fw fa-times"></i>

                                            </button>

                                        </div>

                                    </div>



                                    <div class="block-content new-block-content pt-0 pb-0 ">



                                        <div class="row form-group  ">



                                            <div class="col-sm-3">



                                                <label class="col-form-label ">Client</label>



                                            </div>



                                            <div class="col-sm-6 ">



                                                <select type="" name="fs_client" id="financial_statement_client"

                                                    class="form-control select2" placeholder="">

                                                    <option value="" selected>Select Client</option>

                                                    @foreach ($all_active_clients as $c)

                                                        <option value="{{ $c->id }}"

                                                            {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>

                                                            {{ $c->display_name }}

                                                        </option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row justify-content- form-group  push ">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Fiscal Year</label>

                                            </div>



                                            <div class="col-sm-4">

                                                <select class="form-control select2" id="financial_statement_fiscal_year"

                                                    name="fs_fyear">

                                                    <option value="" selected>Select Fiscal</option>

                                                    @foreach ($fiscal_years as $fy)

                                                        <option value="{{ $fy }}"

                                                            {{ Auth::user()->default_fiscal_year == $fy ? 'selected' : '' }}>

                                                            {{ $fy }}</option>

                                                    @endforeach

                                                </select>

                                            </div>



                                        </div>





                                        <div class="row justify-content- form-group  push">





                                            <div class="col-sm-3">

                                                <label class="col-form-label ">Rounding</label>

                                            </div>



                                            <div class="col-sm-3">



                                                <input class="js-rangeslider" id="financial_statement_rounding"

                                                    data-values="O f f,0,0 0 0,0 0 0 0 0 0" name="fs_rounding">

                                            </div>



                                        </div>



                                    </div>

                                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">

                                        <button type="reset" class="btn  mr-3   btn-new-secondary"

                                            style="">Clear</button>

                                        <button type="submit" class="btn btn-new ">Run</button>

                                    </div>

                                </div>



                            </div>

                        </div>

                    </div>





                </form>









                <div class="modal fade" id="ReportTypeModal" tabindex="-1" role="dialog" data-backdrop="static"

                    aria-labelledby="modal-block-large" aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">

                        <div class="modal-content">

                            <div class="block  block-transparent mb-0">

                                <div class="block-header pb-0  ">

                                    <span class="b e section-header">Select Report</span>

                                    <div class="block-options">

                                        <button type="button" class="btn-block-option" data-dismiss="modal"

                                            aria-label="Close">

                                            <i class="fa fa-fw fa-times"></i>

                                        </button>

                                    </div>

                                </div>



                                <div class="block-content new-block-content pt-0 pb-0 ">

                                    <div class="row form-group">

                                        <div class="col-md-12 d-flex flex-wrap">

                                            <div class="d-flex flex-column justify-content-center align-items-center p-2 mx-2 select-report-type"

                                                status="0" data-id="1">

                                                <img width="145px" class="mx-auto"

                                                    src="{{ asset('public/batch_icons/report-journal.png') }}">

                                                <p class="mt-4 mb-0"

                                                    style="    font-size: 22px;

                                font-family: Jura;

                                line-height: 24px;

                                text-align: center;">

                                                    Journal Report</p>

                                            </div>

                                            <div class="d-flex flex-column justify-content-center align-items-center p-2 mx-2 select-report-type"

                                                status="0" data-id="2">

                                                <img width="145px" class="mx-auto"

                                                    src="{{ asset('public/batch_icons/report-trial-balance.png') }}">

                                                <p class="mt-4 mb-0"

                                                    style="    font-size: 22px;

                                font-family: Jura;

                                line-height: 24px;

                                text-align: center;">

                                                    Trial Balance</p>

                                            </div>

                                            <div class="d-flex flex-column justify-content-center align-content-center p-2 mx-2 select-report-type"

                                                status="0" data-id="3">

                                                <img width="145px" class="mx-auto"

                                                    src="{{ asset('public/batch_icons/report-financial-statement.png') }}">

                                                <p class="mt-4 mb-0"

                                                    style="    font-size: 22px;

                                font-family: Jura;

                                line-height: 24px;

                                text-align: center;">

                                                    Financial Statement</p>

                                            </div>



                                            {{-- <div class="d-flex flex-column justify-content-center align-content-center p-2 mx-2 select-report-type"

                                                status="0" data-id="4">

                                                <img width="160px" class="mx-auto"

                                                    src="{{ asset('public/icons_2024_02_24/icon-report-remit.png') }}">

                                                <p class="mt-4 mb-0"

                                                    style="    font-size: 22px;

                            font-family: Jura;

                            line-height: 24px;

                            text-align: center;">

                                                    Remittance Status</p>

                                            </div> --}}



                                            <div class="d-flex flex-column justify-content-center align-content-center p-2 mx-2 select-report-type"

                                                status="0" data-id="5">

                                                <img width="180px" class="mx-auto"

                                                    src="{{ asset('public/icons_2024_02_24/icon-report-progress.png') }}">

                                                <p class="mt-4 mb-0"

                                                    style="    font-size: 22px;

                        font-family: Jura;

                        line-height: 24px;

                        text-align: center;">

                                                    Progress</p>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">



                                    <button type="button" id="proceed-to-report" class="btn btn-new ">Continue</button>

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

            var batch_update = false;

            var mode = 'view';

            var click_edit_journal_modal = 0;



            function numberFormat(number, decimals = 2) {

                const roundedNumber = Number.parseFloat(number).toFixed(decimals);

                const options = {

                    minimumFractionDigits: decimals,

                    maximumFractionDigits: decimals,

                    useGrouping: true

                };

                const formattedNumber = Number.parseFloat(roundedNumber).toLocaleString('en-US', options);

                return formattedNumber;

            }

            $(function() {



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

                            $.ajax({

                                type: 'get',

                                // data: {

                                //     id: id

                                // },

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

                                    $('#ExportModalNew').modal('hide');

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



                $('.btnEdit').on('click', function() {

                    $(this).closest('.dropdown-menu').removeClass('show');

                    $(this).closest('.dropdown').find('.dropdown-toggle').dropdown('hide');

                });



                // Event listener for delete button

                $('.btnDelete').on('click', function() {

                    $(this).closest('.dropdown-menu').removeClass('show');

                    $(this).closest('.dropdown').find('.dropdown-toggle').dropdown('hide');

                });



                $(document).on('click', '.rep-btn', function() {

                    $('#ReportTypeModal').modal('show');

                })

                $(document).on('click', '.JournalFYReIndexBtn', function() {

                    $('#JournalFYReIndexModal').modal('show');

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

                        url: '{{ url('/get-journal-content') }}',

                        dataType: 'json',

                        beforeSend() {

                            Dashmix.layout('header_loader_on');



                        },



                        success: function(res) {

                            Dashmix.layout('header_loader_off');

                            $('#h_client_name').val(res.client);

                            $('.h_fiscal_year').html('&nbsp;' + res.fiscalYear + '&nbsp;');

                            $('#showData').html(res.html);

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

                        url: '{{ url('/get-journal-edit-content') }}',



                        beforeSend() {

                            Dashmix.layout('header_loader_on');



                        },



                        success: function(res) {



                            Dashmix.layout('header_loader_off');

                            // $("#JournalHeader").addClass('d-none');

                            attachments_file = []

                            commentArray = [];

                            comment_key_count = 0;

                            attachmentArray = [];

                            attachment_key_count = 0

                            $('#showData').html(res);

                            $('.tooltip').tooltip('hide');

                            showCommentsAjax(id)

                            Dashmix.helpers('rangeslider')

                            $('[data-toggle=tooltip]').tooltip();

                            // $(".form-journals select[name=dt_source_code_edit]").select2();

                            $(".select2").select2();

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

                            message: 'Export Complete.  ',

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

                    // var form = $('#limit_form');

                    // if (form.attr("action") === undefined) {

                    //     throw "form does not have action attribute"

                    // }





                    // let url = form.attr("action");

                    // if (url.includes("?") === false) return false;



                    // let index = url.indexOf("?");

                    // let action = url.slice(0, index)

                    // let params = url.slice(index);

                    // url = new URLSearchParams(params);

                    // for (param of url.keys()) {

                    //     if (param != 'limit') {

                    //         let paramValue = url.get(param);



                    //         let attrObject = {

                    //             "type": "hidden",

                    //             "name": param,

                    //             "value": paramValue

                    //         };

                    //         let hidden = $("<input>").attr(attrObject);

                    //         form.append(hidden);

                    //     }

                    // }

                    // form.attr("action", action)



                    // form.submit();

                    var currentUrl = window.location.href;

                    var url = new URL(currentUrl);

                    var limitValue = $(this).val();



                    url.searchParams.set('limit', limitValue);



                    window.location.href = url.toString();

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



                    var c = confirm("Are you really want to delete this journal?");

                    if (c) {

                        window.location.href = "{{ url('/delete-journal') }}?id=" + id;

                    }

                })







                let edit_journal_click = 0;

                $(document).on('keyup', '#edit-journal input,#edit-journal textarea', function() {

                    edit_journal_click = 1;

                });



                $(document).on('change', '#edit-journal select', function() {

                    edit_journal_click = 1;

                });



                $(document).on('click', '.btnClose', function() {

                    var id = $(this).attr('data')

                    if (edit_journal_click == 1) {

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

                                                       <p class="px-2 mb-0 comments-section-text">  ${commentArray[i].comment.replace(/\r?\n/g, '<br />')}

</p>

                                                    </td>



                                                </tr>



                                        </tbody>

                                    </table>



                                    </div>`;

                    }

                    $('#showData #commentBlock').html(html)

                }



                // END Comment







                function showCommentsAjax(id) {





                    $.ajax({

                        type: 'get',

                        'method': 'get',

                        data: {

                            id: id

                        },

                        url: "{{ url('get-comments-journal') }}",

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

                        url: "{{ url('get-attachment-journal') }}",

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



                        html += `   <div class="col-sm-6 px-0  attach-other-col ">

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

                    $('#showData #attachmentBlock').html(html)

                }



                // END Attachment





                $(document).on('submit', '#edit-journal', function(e) {

                    e.preventDefault();

                    e.stopImmediatePropagation();

                });





                $(document).on('click', '.saveContract', function() {

                    // $('.tooltip').tooltip('hide');

                    // $('.show').addClass('d-none');

                    $('#ajax-overlay').fadeIn();

                    $('[data-toggle="tooltip"]').each(function() {

                        $(this).tooltip('hide'); // Hide tooltip

                        $(this).removeAttr('aria-describedby'); // Remove tooltip reference

                    });

                    //   $('.show').addClass('d-none');

                    var $btn = $(this); // Save the button reference

                    if ($btn.hasClass('disabled')) {

                        return; // Prevent multiple clicks

                    }

                    // Add spinner and expand button

                    $btn.addClass('expanded disabled'); // Disable the button and expand it

                    $btn.html(

                        '<i class="fa fa-cog spinner text-white"></i> Saving...'); // Add spinner icon and text

                    var data1 = $(this).attr('data');





                    const client = $(".form-journals select[name=pp_client_edit]").val();

                    const year = $(".form-journals select[name=pp_year_edit]").val();

                    const month = $(".form-journals select[name=pp_month_edit]").val();

                    const period = $(".form-journals input[name=pp_period_edit]").val();

                    const fyear = $(".form-journals input[name=pp_fyear_edit]").val();

                    const account_no = $('.form-journals input[name=dt_account_edit]').val();

                    const account_valid = $('.form-journals input[name=dt_account_edit]').attr('account');

                    const source = $(".form-journals input[name=dt_source_code_edit]").val();

                    const ref = $(".form-journals input[name=dt_ref_edit]").val();

                    const date = $('.form-journals input[name=dt_date_edit]').val();

                    const translation = $('.form-journals input[name=translation_edit]').val();

                    const description = $('.form-journals input[name=dt_description_edit]').val();

                    const debit = $('.form-journals input[name=amnt_debit_edit]').val();

                    const credit = $('.form-journals input[name=amnt_credit_edit]').val();

                    const scrollMargin = 60;







                    if (client == '' || client == null || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for client.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_client_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        return false;

                    } else if (year == '' || year == null || year == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for year.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_year_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        return false;

                    } else if (month == '' || month == null || month == undefined) {

                        const Field = $(".form-journals select[name=pp_month_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for month.',

                            delay: 5000

                        });



                        return false;

                    } else if (period == '' || period == null || period == undefined || period == "NaN") {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_month_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        return false;

                    } else if (fyear == '' || fyear == null || fyear == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_month_edit]");

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        return false;

                    } else if (!validateAccNo(account_no) || account_no == '' || account_no == null ||

                        account_no == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter valid value for account no.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_account_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        return false;

                    } else if (account_valid == '0') {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Account number ' +

                                account_no + ' not found.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_account_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();



                        return false;

                    } else if (source == '' || source == null || source == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for source.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_source_code_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();

                        return false;

                    } else if (ref == '' || ref == null || ref == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for ref.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_ref_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();

                        return false;

                    } else if (date == '' || date == null || date == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for date.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_date_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();

                        return false;

                    } else if (translation == '' || translation == null || translation == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_date_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();

                        return false;

                    } else if (description == '' || description == null || description == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for description.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_description_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();

                        return false;

                    } else if ((debit == '' && credit == '') || debit == null || credit == null || debit ==

                        NaN || credit == NaN || debit == undefined || credit == undefined) {



                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for debit or credit.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=amnt_debit_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        resetButton($btn);

                        $('#ajax-overlay').fadeOut();

                        return false;

                    } else {



                        $("#edit-journal").append(

                            `<input type="hidden" name="_token" value='{{ csrf_token() }}'>`);

                        $("#form-edit-client-journal").append(

                            `<input type="hidden" name="_submit_type" value='0'>`);

                        var formData = new FormData(document.getElementById("edit-journal"));



                        for (var i = 0; i < attachmentArray.length; i++) {

                            formData.append('attachmentArray[]', JSON.stringify(attachmentArray[i]));

                        }

                        for (var i = 0; i < commentArray.length; i++) {

                            formData.append('commentArray[]', JSON.stringify(commentArray[i]));

                        }



                        setTimeout(() => {

                            $.ajax({

                                type: 'post',

                                data: formData,

                                'url': '{{ url('update-journal') }}',

                                dataType: 'json',

                                async: false,



                                contentType: false,

                                processData: false,

                                cache: false,

                                success: function(res) {

                                    // resetButton($btn);

                                    // $('#ajax-overlay').fadeOut();

                                    @if (@$_GET['_applied'] == 1)

                                        var currentUrl = window.location.href;

                                        var url = new URL(currentUrl);

                                        url.searchParams.set('id', data1);

                                        window.location.href = url.toString();

                                    @else

                                        window.location.href =

                                            "{{ url('/journals') }}?id=" + data1;

                                    @endif

                                    //$("#JournalHeader").removeClass('d-none');

                                    //Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: 'Journal successfully saved', delay: 5000});

                                    //showData(data1)

                                    click = 0;



                                },

                                error: function() {

                                    // Handle error and reset button

                                    resetButton($btn);

                                    $('#ajax-overlay').fadeOut();

                                }

                            })



                        }, 500);



                    }



                })





                function resetButton(button) {

                    button.removeClass('expanded disabled');

                    button.html('<img src="{{ url('public') }}/icons2/icon-save-white.png" width="24px">');

                    $('#ajax-overlay').fadeOut();

                    $('[data-toggle=tooltip]').tooltip();

                }



            });



            var j_client_data = [];



            function journal_client_data(page = 1) {

                const client_id = $(".form-journals select[name=pp_client]").val();

                const month = $(".form-journals select[name=pp_month]").val();

                const year = $(".form-journals select[name=pp_year]").val();

                const fyear = $('.form-journals input[name=pp_fyear]').val();

                const period = $('.form-journals input[name=pp_period]').val();

                const searchVal = $('input[name=client-journal-search]').val();

                const limitVal = $('select[name=client-journal-limit]').val();

                const dateCreated = $("#filterClientJournalForm input[name=client_mode_filter_date_created]").val();

                const periodsArr = $("#filterClientJournalForm #client-mode-filter-period").val();

                const sourcesArr = $("#filterClientJournalForm #client-mode-filter-source").val();

                const refsArr = $("#filterClientJournalForm #client-mode-filter-ref").val();

                const accountsArr = $("#filterClientJournalForm #client-mode-filter-account").val();

                $.ajax({

                    type: "POST",

                    url: "{{ url('/journals/client/get-data') }}",

                    data: {

                        "_token": "{{ csrf_token() }}",

                        "journalPage": page,

                        "client_id": client_id,

                        "month": month,

                        "year": year,

                        "fyear": fyear,

                        "dateCreated": dateCreated,

                        "period": period,

                        "searchVal": searchVal,

                        'limitVal': limitVal,

                        "periodsArr": periodsArr,

                        "sourcesArr": sourcesArr,

                        "refsArr": refsArr,

                        "accountsArr": accountsArr,

                    },

                    beforeSend: function() {

                        $("#view-client-journals").html(`

            <div class="row px-0">

            <div class="col-md-12" style="min-height: 90vh !important;">

                <div class="d-flex h-100 flex-column align-items-center justify-content-center">

                    <img src="{{ asset('public/spinner.gif') }}" width="50px">

                    <p>Please wait while loading journals...</p>

                </div>

            </div>

        </div>

            `);

                    },

                }).done(function(response) {

                    j_client_data = response.data;

                    const p1_journals = j_client_data.p1_journals,

                        p2_journals = j_client_data.p2_journals,

                        p3_journals = j_client_data.p3_journals,

                        p4_journals = j_client_data.p4_journals,

                        p5_journals = j_client_data.p5_journals,

                        p6_journals = j_client_data.p6_journals,

                        p7_journals = j_client_data.p7_journals,

                        p8_journals = j_client_data.p8_journals,

                        p9_journals = j_client_data.p9_journals,

                        p10_journals = j_client_data.p10_journals,

                        p11_journals = j_client_data.p11_journals,

                        p12_journals = j_client_data.p12_journals;

                    const p1_indicator = j_client_data.p1_indicator,

                        p2_indicator = j_client_data.p2_indicator,

                        p3_indicator = j_client_data.p3_indicator,

                        p4_indicator = j_client_data.p4_indicator,

                        p5_indicator = j_client_data.p5_indicator,

                        p6_indicator = j_client_data.p6_indicator,

                        p7_indicator = j_client_data.p7_indicator,

                        p8_indicator = j_client_data.p8_indicator,

                        p9_indicator = j_client_data.p9_indicator,

                        p10_indicator = j_client_data.p10_indicator,

                        p11_indicator = j_client_data.p11_indicator,

                        p12_indicator = j_client_data.p12_indicator;

                    $("#journal-client-pp-period-1").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p1_indicator).attr('data-original-title', p1_journals);

                    $("#journal-client-pp-period-2").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p2_indicator).attr('data-original-title', p2_journals);

                    $("#journal-client-pp-period-3").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p3_indicator).attr('data-original-title', p3_journals);

                    $("#journal-client-pp-period-4").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p4_indicator).attr('data-original-title', p4_journals);

                    $("#journal-client-pp-period-5").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p5_indicator).attr('data-original-title', p5_journals);

                    $("#journal-client-pp-period-6").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p6_indicator).attr('data-original-title', p6_journals);

                    $("#journal-client-pp-period-7").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p7_indicator).attr('data-original-title', p7_journals);

                    $("#journal-client-pp-period-8").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p8_indicator).attr('data-original-title', p8_journals);

                    $("#journal-client-pp-period-9").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p9_indicator).attr('data-original-title', p9_journals);

                    $("#journal-client-pp-period-10").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p10_indicator).attr('data-original-title', p10_journals);

                    $("#journal-client-pp-period-11").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p11_indicator).attr('data-original-title', p11_journals);

                    $("#journal-client-pp-period-12").removeClass('indicator-period-balance').removeClass(

                            'indicator-not-period-balance').removeClass('indicator-period-no-journals')

                        .addClass(p12_indicator).attr('data-original-title', p12_journals);

                    let period_balance_debit = j_client_data.period_balance_debit,

                        period_balance_credit = j_client_data.period_balance_credit,

                        period_balance = 0;

                    let fyear_balance_debit = j_client_data.fyear_balance_debit,

                        fyear_balance_credit = j_client_data.fyear_balance_credit,

                        fyear_balance = 0;

                    if (parseFloat(period_balance_debit) > parseFloat(period_balance_credit)) {

                        period_balance = parseFloat(period_balance_debit) - parseFloat(period_balance_credit);

                        $(".form-journals #period_balance").removeClass('acct-balance').removeClass(

                            'cr-balance').addClass('dr-balance');

                    } else {

                        period_balance = parseFloat(period_balance_credit) - parseFloat(period_balance_debit);

                        $(".form-journals #period_balance").removeClass('acct-balance').removeClass(

                            'dr-balance').addClass('cr-balance');

                    }

                    if (parseFloat(fyear_balance_debit) > parseFloat(fyear_balance_credit)) {

                        fyear_balance = parseFloat(fyear_balance_debit) - parseFloat(fyear_balance_credit);

                        $(".form-journals #fiscal_year_balance").removeClass('cr-balance').removeClass(

                            'acct-balance').addClass('dr-balance');

                    } else {

                        fyear_balance = parseFloat(fyear_balance_credit) - parseFloat(fyear_balance_debit);

                        $(".form-journals #fiscal_year_balance").removeClass('dr-balance').removeClass(

                            'acct-balance').addClass('cr-balance');

                    }

                    if (period_balance == 0) {

                        $(".form-journals #period_balance").removeClass('cr-balance').removeClass(

                            'dr-balance').addClass('acct-balance');

                    }

                    if (fyear_balance == 0) {

                        $(".form-journals #fiscal_year_balance").removeClass('cr-balance').removeClass(

                            'dr-balance').addClass('acct-balance');

                    }

                    $(".form-journals #fiscal_year_balance").html(numberFormat(fyear_balance, 2));

                    $(".form-journals #period_balance").html(numberFormat(period_balance, 2));



                    $("#view-client-journals").html(j_client_data.journals);

                    $('[data-toggle="tooltip"]').tooltip();

                    let html1 = ``,

                        htmlD = ``,

                        htmlChart = ``;

                    for (var i = 0; i < (j_client_data.client_gifi).length; i++) {

                        let selected = '';

                        if ($.inArray(j_client_data.client_gifi[i]['account_no'], j_client_data.accountsArr) !== -1) {

                            selected = 'selected';

                        }

                        html1 +=

                            `<option value="${j_client_data.client_gifi[i]['account_no']}" ${selected}>${j_client_data.client_gifi[i]['account_no']}</option>`;

                        htmlD += `<option value="${j_client_data.client_gifi[i]['account_no']}"/>`;

                        htmlChart += `

            <div class="col-md-12">

                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  account-item "

                                    style="cursor:pointer;" account-no="${j_client_data.client_gifi[i]['account_no']}"

                                    description="{{ $g->description }}">



                                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">





                                        <div class="  " style="width:100%">



                                            <div class="d-flex p-2">







                                                <p class="mr-3  mb-0 " style="font-family: Signika; font-size: 12pt; color: #595959">

                                                    ${j_client_data.client_gifi[i]['account_no']}</p>

                                                <p class=" text-truncate mb-0" style="font-family: Signika; font-size: 12pt;font-weight: 100;color: #595959" data="4">${j_client_data.client_gifi[i]['description']}

                                                </p>

                                            </div>







                                        </div>



                                    </div>

                                </div>



                            </div>

            `;

                    }

                    $("#AccountChartModal .client-accounts").html(htmlChart);

                    $(".form-journals #dt_account_description_list").html(htmlD)

                    $("#client-mode-filter-account").selectpicker('destroy');

                    $("#client-mode-filter-account").html(html1);

                    $("#client-mode-filter-account").selectpicker();

                    let html2 = ``;

                    for (var i = 0; i < (j_client_data.client_refs).length; i++) {

                        let selected = '';

                        if ($.inArray(j_client_data.client_refs[i], j_client_data.refsArr) !== -1) {

                            selected = 'selected';

                        }

                        html2 +=

                            `<option value="${j_client_data.client_refs[i]}" ${selected}>${j_client_data.client_refs[i]}</option>`;

                    }

                    $("#client-mode-filter-ref").selectpicker('destroy')

                    $("#client-mode-filter-ref").html(html2);

                    $("#client-mode-filter-ref").selectpicker();

                    $(".form-journals #edit_no").html('#' + (j_client_data.fyearLatestEditNo).toString().padStart(5,

                        '0'));

                }).fail(function() {

                    $("#view-client-journals").html(`

            <div class="row px-0">

            <div class="col-md-12" style="min-height: 90vh !important;">

                <div class="d-flex h-100 flex-column align-items-center justify-content-center">



<p>An error occurred while loading journals</p>

</div>

            </div>

        </div>

            `);

                });

            }

            $(document).on('change', 'input[name=client-journal-search]', function() {

                //var searchVal = $(this).val();

                $("#filterClientJournalForm")[0].reset();

                $("#filterClientJournalForm #client-mode-filter-ref").val('');

                $("#filterClientJournalForm #client-mode-filter-source").val('');

                $("#filterClientJournalForm #client-mode-filter-account").val('');

                $("#filterClientJournalForm #client-mode-filter-period").val('');

                journal_client_data();

            });

            $(document).on('click', '.journal-page-link', function() {



                const page = $(this).attr('page-no');

                journal_client_data(page);

            })

            $(document).on('change', 'select[name=client-journal-limit]', function() {

                journal_client_data();

            });

            $(document).on('input', '.sanitize', function() {

                var inputValue = $(this).val();

                var sanitizedValue = inputValue.replace(/[^-0-9.]/g, '');

                $(this).val(sanitizedValue);

            });



            function formatValue(value) {

                if (value % 1 === 0) {

                    if (value.includes('.')) {

                        return value;

                    }



                    return (parseFloat(value) / 100).toFixed(2);

                } else {

                    return parseFloat(value);

                }

            }



            function validateAccNo(val) {

                return /^\d{4}$/.test(val)

            }



            function calcNet() {

                // let net = 0, amount = 0;

                // const debit = parseFloat($(".form-journals input[name=amnt_debit]").val());

                // const credit = parseFloat($('.form-journals input[name=amnt_credit]').val());

                // if(debit > 0) {

                //     amount = debit;

                // } else if(credit > 0) {

                //     amount = credit;

                // }

                // const tax1 = parseFloat($(".form-journals input[name=amnt_tax1]").val());

                // const tax2 = parseFloat($(".form-journals input[name=amnt_tax2]").val());

                // if($(".form-journals input[name=amnt_taxable]").is(":checked")) {

                //     net = amount - (tax1 + tax2);

                // } else {

                //     net = amount

                // }



                // $(".form-journals input[name=net]").val(net);

                // $(".form-journals #net").html(net.toFixed(2));



            }



            function calcTaxes() {

                let p_tx1 = $('.form-journals input[name=tx_gst]').val().replace('%', '');

                let p_tx2 = $('.form-journals input[name=tx_pst]').val().replace('%', '');



                if (p_tx1 != '') {

                    p_tx1 = parseFloat(p_tx1) / 100;

                }

                if (p_tx2 != '') {

                    p_tx2 = parseFloat(p_tx2) / 100;

                }

                let gross = 0,

                    net = 0;

                const debit = parseFloat($(".form-journals input[name=amnt_debit]").val());

                const credit = parseFloat($('.form-journals input[name=amnt_credit]').val());

                console.log(debit, credit);



                if (debit > 0) {

                    gross = debit;

                } else if (credit > 0) {

                    gross = credit;

                }

                let tax1 = 0,

                    tax2 = 0;

                // tax1 = amount * (p_tx1/100);

                // tax2 = amount * (p_tx2/100);

                const applied_to_tax1 = $(".form-journals input[name=amnt_taxable]").attr('applied-to-tax1');

                // if(applied_to_tax1 == 1) {

                //     tax2 = (amount + tax1) * (p_tx2/100);

                // }







                if (applied_to_tax1 == 1) {





                    /**

                     * CASE3

                     * Net = gross / ((1+tax1%) + tax2% * (1+tax1%))

                     * Tax1Amount = Net * Tax1%

                     * Tax2Amount = (Net+tax1Amount) * Tax2%

                     * Validation: Gross = net + tax1Amount + tax2Amount

                     */



                    net = gross / ((1 + p_tx1) + p_tx2 * (1 + p_tx1));

                    tax1 = net * p_tx1;

                    tax2 = (net + tax1) * p_tx2;



                } else {

                    if (p_tx2 == '') {



                        /**

                         * CASE1

                         * Net = Gross / (1 + Tax1%)

                         * Tax1Amount = Net * Tax1%

                         * Validation: Gross = net + tax1Amount

                         */

                        net = gross / (1 + p_tx1);

                        tax1 = net * p_tx1;

                    } else {



                        /**

                         * CASE2

                         * Net = Gross / (1 + Tax1% + Tax2%)

                         * Tax1Amount = Net * Tax1%

                         * Tax2 Amount = Net * Tax2%

                         * Validation: Gross = net + tax1Amount + tax2Amount

                         */

                        net = gross / (1 + p_tx1 + p_tx2);

                        tax1 = net * p_tx1;

                        tax2 = net * p_tx2;

                    }

                }











                $(".form-journals input[name=amnt_tax1]").val(tax1.toFixed(2));

                $(".form-journals input[name=amnt_tax2]").val(tax2.toFixed(2));





                //calcNet();

                $(".form-journals input[name=net]").val(net);

                $(".form-journals #net").html(net.toLocaleString(undefined, {

                    minimumFractionDigits: 2,

                    maximumFractionDigits: 2

                }));

            }



            function translatedDate(date = '', cmonth, cyear) { //calender month+year

                if (date === '') {

                    var currentDate = new Date();

                    var lastDateOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

                    var day = lastDateOfMonth.getDate();

                    //var monthName = monthToStringShort(lastDateOfMonth.getMonth() + 1);

                    //var year = lastDateOfMonth.getFullYear().toString().slice(2);



                    return day + '-' + monthToStringShort(parseInt(cmonth)) + '-' + cyear.slice(2);

                }



                // Extract day, month, and year from the input date string

                var day = date.slice(0, 2);

                var month = date.slice(2, 4);

                var year = date.slice(4);



                // Convert the month number to a month name in short format

                var monthName = monthToStringShort(parseInt(month));



                // Format the date as "dd-Month-YY" or "dd-Month-YYYY" based on the length of the year component

                var formattedDate = day + '-' + monthName + '-' + (year.length === 2 ? year : year.slice(2));



                return formattedDate;

            }





            function findPeriod(fiscalStart, dateString) {

                var startDate = moment(fiscalStart);

                var endDate = moment(dateString);



                var diffMonths = endDate.diff(startDate, 'months') + 1;

                var period = (diffMonths > 0) ? diffMonths : 12 - Math.abs(diffMonths % 12);



                if (period > 12) {

                    return "";

                }



                var periodString = "Period " + ("0" + period).slice(-2);

                return periodString;

            }









            /*       function findPeriod(fiscalStart, dateString) {





              var startDate = new Date(fiscalStart);

              var endDate = new Date(dateString);



              var diffMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12 + (endDate.getMonth() - startDate.getMonth()) + 1;

              var period = (diffMonths > 0) ? diffMonths : 12 - Math.abs(diffMonths % 12);

              if(period > 12) {

                return "";

              }

              var periodString = "Period " + ("0" + period).slice(-2);



              return periodString;

            }**/







            function getFiscalYearEnd(fmonth, period, month, year) {

                if (parseInt(fmonth) == 1) {

                    var fiscal_end = parseInt(year);

                    return fiscal_end;

                }

                const monthCalendar = [month];

                while (period <= 12) {

                    month = month + 1;

                    if (month == 13) {

                        month = 1;

                    }

                    monthCalendar.push(month);

                    if (monthCalendar.length == 12) {

                        break;

                    }

                    period++;

                }

                monthCalendar.forEach((m, i) => {



                    if (m == 1 && i != 0) {

                        year++;

                    }

                });

                return year;

            }

            // function getFiscalYearEnd(period, month, year) {

            //     const monthCalendar = [month];

            //     console.log(monthCalendar, period, month, year);

            //     while (period <= 12) {

            //         month = month + 1;

            //         if (month == 13) {

            //             month = 1;

            //         }

            //         monthCalendar.push(month);

            //         if (monthCalendar.length == 12) {

            //             break;

            //         }

            //         period++;

            //     }

            //     monthCalendar.forEach((m, i) => {

            //         console.log(m, i);



            //         if (m == 1 && i != 0) {

            //             year++;

            //         }

            //     });

            //     return year;

            // }









            function monthToString(month) {

                var months = [

                    'January', 'February', 'March', 'April', 'May', 'June',

                    'July', 'August', 'September', 'October', 'November', 'December'

                ];

                return months[month - 1];

            }



            function monthToStringShort(month) {

                var months = [

                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',

                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'

                ];

                return months[month - 1];

            }



            function generateFormattedFrequencies(startDate) {

                var currentDate = new Date(startDate);

                var endDate = new Date(currentDate.getFullYear() + 1, 7,

                    31); // 1 year forward from startDate, assuming fiscal year ends on August 31



                var frequencies = [];

                var formattedFrequencies = [];



                while (currentDate <= endDate) {

                    var year = currentDate.getFullYear();

                    var month = currentDate.getMonth();

                    var lastDay = new Date(year, month + 1, 0).getDate(); // Get the last day of the month

                    var monthName = currentDate.toLocaleString('en-US', {

                        month: 'short'

                    });

                    var formattedDate = lastDay + '-' + monthName + '-' + year;



                    frequencies.push(currentDate);

                    formattedFrequencies.push(formattedDate);



                    // Increment the date by 3 months

                    currentDate.setMonth(currentDate.getMonth() + 3);

                }



                return formattedFrequencies;

            }



            function checkFourDigitString(string) {

                var pattern = /^\d{4}$/;

                return pattern.test(string);

            }





            // Function to validate the input value

            function validateInputValue(value, dateString, currentMonth, currentYear) {

                // Remove any non-numeric characters

                value = value.replace(/\D/g, '');



                // Clear input if length is less than 2 or more than 8

                if (value.length < 2 || value.length > 8) {

                    return false;

                }

                // Get current date and month

                var currentDate = new Date(dateString);



                //var currentYear = currentDate.getFullYear().toString().slice(-2);

                //var currentMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0');

                var day, month, year;



                if (value.length === 2) {

                    // Case: 2 digits entered (e.g., 03)

                    day = value;

                    month = currentMonth;

                    year = currentYear;

                } else if (value.length === 4) {

                    // Case: 4 digits entered (e.g., 0205)

                    day = value.slice(0, 2);

                    month = value.slice(2, 4);

                    year = currentYear;

                } else if (value.length === 6) {

                    // Case: 6 digits entered (e.g., 030523)

                    day = value.slice(0, 2);

                    month = value.slice(2, 4);

                    year = value.slice(4, 6);

                } else if (value.length === 8) {

                    // Case: 8 digits entered (e.g., 31052022)

                    day = value.slice(0, 2);

                    month = value.slice(2, 4);

                    year = value.slice(4, 8);

                }



                // Validate day, month, and year

                var lastDayOfMonth = new Date(year, month, 0).getDate();

                if (parseInt(day) > lastDayOfMonth || isNaN(parseInt(day))) {

                    return false;

                }

                if (parseInt(month) < 1 || parseInt(month) > 12 || isNaN(parseInt(month))) {

                    return false;

                }



                // Append full year if the year component is 4 digits and greater than current year

                // if (year.length === 2 && parseInt(year) > currentYear % 100) {

                //     year = currentYear + Math.floor(parseInt(year) / 100) * 100;

                // }



                // Update input value with padded and validated values

                return day + month + year;

            }





            //.form-journals

            // $(document).on('keydown',function(event) {

            //     if (event.which === 32 || event.keyCode === 32) { // Spacebar key code is 32

            //       event.preventDefault(); // Prevents the default action of the spacebar key press

            //     //   $('.form-journals input[name="amnt_taxable"]', this).prop('checked', function(_, currentVal) {

            //     //     return !currentVal; // Toggles the checked/unchecked state of the checkbox

            //     //   });

            //       $('.form-journals input[name=amnt_taxable]').trigger('click');

            //     }

            //   });



            $(document).ready(function() {

                $(document).on("change", '.form-journals #pp_client', function() {

                    var client_id = $(this).val();

                    const default_prov = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'default-province');



                    $('.form-journals select[name=tx_province]').val(default_prov).trigger('change');



                    var remittance = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'remittance');

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'fiscal-start');



                    const fs = fiscal_start;

                    fiscal_start = fiscal_start.split('-');

                    const fyear = fiscal_start[0];

                    const fmonth = fiscal_start[1];



                    const month = $('.form-journals select[name=pp_month]').val();

                    const year = $('.form-journals select[name=pp_year]').val();

                    var period = findPeriod(fs, fyear + "-" + month + "-01");



                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $(".form-journals .pp_fyear").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period").html(period);

                    $(".form-journals input[name=pp_fyear]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period]').val(parseInt((period.trim()).slice(-2)));

                });



                $(document).on('click', '.change-fy', function() {

                    const change_fyear = $('select[name=change_pp_year]').val();

                    if (change_fyear == "" || change_fyear == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select Posting Year.',

                            delay: 5000

                        });

                        return false;

                    }

                    $(".form-journals select[name=pp_year]").val(change_fyear);

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'fiscal-start');

                    var month = parseInt($(`.form-journals select[name=pp_month]`).val());

                    var year_ = $('.form-journals #pp_year option:selected').val();



                    if (month < 10) {

                        month = '0' + month;

                    }

                    const fs = fiscal_start;

                    const fyear = fiscal_start.split('-')[0];

                    const fmonth = fiscal_start.split('-')[1];

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year_));

                    $(".form-journals .pp_fyear").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period").html(period);

                    $(".form-journals input[name=pp_fyear]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period]').val(parseInt((period.trim()).slice(-2)));

                    //var fiscal_end = getFiscalYearEnd(fiscal_start);

                    // $("#add-journal-h1").html(fiscal_end);

                    const dt_date = $('.form-journals input[name=dt_date]').val();

                    $('.form-journals input[name=dt_date]').val(dt_date[0] + dt_date[1]);

                    $('.form-journals input[name=dt_date]').trigger('change');

                    AddJournalChangeClient();

                    $("#chaneFiscalYearModal").modal('hide');

                });

                $(document).on('click', '.change-month', function() {

                    const change_month = $('select[name=change_pp_month]').val();

                    const change_month_text = $("select[name=change_pp_month] option:selected").text().trim();

                    if (change_month == "" || change_month == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select Posting Month.',

                            delay: 5000

                        });

                        return false;

                    }

                    $(".form-journals select[name=pp_month]").val(change_month);



                    // $('.form-journals #pp_month').change()

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'fiscal-start');

                    var month = parseInt($('#pp_month option:selected').val());

                    var year = $(`.form-journals select[name=pp_year]`).val();

                    if (month < 10) {

                        month = '0' + month;

                    }

                    const fs = fiscal_start

                    fiscal_start = fiscal_start.split('-');

                    const fyear = fiscal_start[0];

                    const fmonth = fiscal_start[1];

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $(".form-journals .pp_fyear").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period").html(period);

                    $(".form-journals input[name=pp_fyear]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period]').val(parseInt((period.trim()).slice(-2)));

                    //$("#add-journal-h1").html(fiscal_end);

                    const dt_date = $('.form-journals input[name=dt_date]').val();

                    $('.form-journals input[name=dt_date]').val(dt_date[0] + dt_date[1]);

                    $('.form-journals input[name=dt_date]').trigger('change');

                    AddJournalChangeClient();

                    $("#chaneMonthModal").modal('hide');

                });

                $(document).on('click', '.selected-posting-period', function() {

                    var $btn = $(this);

                    if ($btn.hasClass('disabled')) {

                        return;

                    }

                    // Add spinner and expand button

                    $btn.addClass('expanded disabled'); // Disable the button and expand it

                    $btn.html(

                        '<i class="fa fa-cog spinner"></i> Processing...'); // Add spinner icon and text

                    const client = $('.form-journals  select[name=pp_client]').val()

                    if (client == "" || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select client.',

                            delay: 5000

                        });

                        return false;

                    }

                    setTimeout(() => {

                        AddJournalChangeClient();

                        resetButton($btn);

                        $('#dt_account').focus();

                    }, 100);

                });



                function resetButton(button) {

                    button.removeClass('expanded disabled');

                    button.html('Continue');



                }



                function AddJournalChangeClient() {

                    journal_client_data();

                    // const company = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                    //     'client-company');

                    const company = $('option:selected', $('.form-journals select[name=pp_client]')).text();



                    const remittance = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'remittance');

                    $("#add-journal-h1").html(company);

                    $("#journal-client-remittance-frequency").html(remittance[0]);

                    $("#journal-client-remittance-frequency").removeClass('indicator-frequency-monthly')

                    $("#journal-client-remittance-frequency").removeClass('indicator-frequency-quarterly')

                    $("#journal-client-remittance-frequency").removeClass('indicator-frequency-yearly')

                    if (remittance == 'Monthly') {

                        $("#journal-client-remittance-frequency").addClass('indicator-frequency-monthly')

                    }

                    if (remittance == 'Quarterly') {

                        $("#journal-client-remittance-frequency").addClass('indicator-frequency-quarterly')

                    }

                    if (remittance == 'Yearly') {

                        $("#journal-client-remittance-frequency").addClass('indicator-frequency-yearly')

                    }

                    $("#journal-client-remittance-frequency").attr('data-original-title', remittance);

                    const period = $('.form-journals input[name=pp_period]').val();

                    const fyear = $('.form-journals input[name=pp_fyear]').val();

                    const month = $(".form-journals select[name=pp_month] option:selected").text().trim();

                    const year = $(".form-journals select[name=pp_year]").val();





                    $("#journal-client-pp-period").html("Period " + period);

                    $("#journal-client-pp-period_month").html(month);

                    $("#change-pp-month").val(month);

                    // $("#journal-client-pp-period").attr('data-original-title', "Period "+period);

                    $("#journal-client-pp-fyear").html("F/Y " + fyear);

                    $("#journal-client-pp-fyear-year").html(year);

                    $("#change_pp_year").val(year);

                    // $("#journal-client-pp-fyear").attr('data-original-title', fyear);

                    $("#add-journal-h2").removeClass('d-none');

                    $(".form-journals #form-add-journal-posting-period").addClass('d-none');

                    $(".form-journals #form-add-journal-client-stats").removeClass('d-none');

                    $(".form-journals #form-add-journal-details").removeClass('d-none');

                    $(".form-journals #form-add-journal-amount").removeClass('d-none');

                    $("#JournalHeader").addClass('d-none')

                    $("#AddJournalHeader").addClass('d-none')

                    $("#ClientJournalHeader").removeClass('d-none')

                }





                /*$(document).on("change", '.form-journals select[name=pp_client_edit]', function () {

                    var client_id = $(this).val();

                  var remittance = $('option:selected',$('.form-journals select[name=pp_client_edit]')).attr('remittance');

                    let fiscal_start = $('option:selected',$('.form-journals select[name=pp_client_edit]')).attr('fiscal-start');

                const fs = fiscal_start

                    fiscal_start = fiscal_start.split('-');

                const fyear = fiscal_start[0];

                    const month = $(".form-journals select[name=pp_month_edit]").val();

                    const year = $(".form-journals select[name=pp_year_eidt]").val();

                    var period = findPeriod(fs, fyear +"-"+month+"-01");

                    var fiscal_end = getFiscalYearEnd(year+"-"+("0"+fiscal_start[1]).slice(-2)+"-01", period);

                    $(".form-journals .pp_fyear_edit").html("Fiscal Year "+(fiscal_end.trim()).slice(-4));

                    $(".form-journals .pp_period_edit").html(period);

                   $(".form-journals input[name=pp_fyear_edit]").val(parseInt((fiscal_end.trim()).slice(-4)));

                   $('.form-journals input[name=pp_period_edit]').val(parseInt((period.trim()).slice(-2)));

                   const dt_date = $('.form-journals input[name=dt_date]').val();

                    $('.form-journals input[name=dt_date_edit]').val(dt_date[0]+dt_date[1]);

                    $('.form-journals input[name=dt_date_edit]').trigger('change');



                });**/





                $(document).on("change", '#edit-journal select[name=pp_client_edit]', function() {



                    var client_id = $(this).val();

                    var remittance = $('option:selected', $('#edit-journal select[name=pp_client_edit]')).attr(

                        'remittance');

                    let fiscal_start = $('option:selected', $('#edit-journal select[name=pp_client_edit]'))

                        .attr('fiscal-start');

                    const fs = fiscal_start;

                    fiscal_start = fiscal_start.split('-');

                    const fyear = fiscal_start[0];

                    const fmonth = fiscal_start[1];

                    const month = $("#edit-journal select[name=pp_month_edit]").val();

                    const year = $("#edit-journal select[name=pp_year_edit]").val();

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $("#edit-journal .pp_fyear_edit").html("Fiscal Year " + fiscal_end);

                    $("#edit-journal .pp_period_edit").html(period);

                    $("#edit-journal input[name=pp_fyear_edit]").val(parseInt(fiscal_end));

                    $('#edit-journal input[name=pp_period_edit]').val(parseInt((period.trim()).slice(-2)));

                    const dt_date = $('#edit-journal input[name=dt_date_edit]').val();

                    $('#edit-journal input[name=dt_date_edit]').val(dt_date[0] + dt_date[1]);

                    $('#edit-journal input[name=dt_date_edit]').trigger('change');

                    getClientGifi(client_id, "dt_account_description_list2");



                });



                function getClientGifi(client_id, datalist) {

                    $.ajax({

                        type: "GET",

                        url: "{{ url('/journals/clients/gifi') }}",

                        data: {

                            "client_id": client_id,

                        }

                    }).done(function(response) {

                        var html = ``,

                            htmlChart = ``;

                        response.forEach(item => {

                            html += `<option value="${item['account_no']}"/>`;

                            htmlChart += `

            <div class="col-md-12">

                                <div class="block block-rounded   table-block-new mb-2 pb-0  -  account-item "

                                    style="cursor:pointer;" account-no="${item['account_no']}"

                                    description="{{ $g->description }}">



                                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">





                                        <div class="  " style="width:100%">



                                            <div class="d-flex p-2">







                                                <p class=" mr-3  mb-0 " style="font-family: Signika;font-size: 12pt;color: #595959;">

                                                    ${item['account_no']}</p>

                                                <p class="  text-truncate mb-0" style="font-family: Signika;font-size: 12pt;font-weight: 100;color: #595959" data="4">${item['description']}

                                                </p>

                                            </div>







                                        </div>



                                    </div>

                                </div>



                            </div>

            `;

                        });







                        $("#AccountChartModal .client-accounts").html(htmlChart);





                        $("#edit-journal #" + datalist).html(html);

                    });

                }

                $(document).on('change', '.form-journals #pp_month', function() {

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'fiscal-start');

                    var month = parseInt($(this).val());

                    var year = $(`.form-journals select[name=pp_year]`).val();

                    if (month < 10) {

                        month = '0' + month;

                    }

                    const fs = fiscal_start

                    fiscal_start = fiscal_start.split('-');

                    const fyear = fiscal_start[0];

                    const fmonth = fiscal_start[1];

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $(".form-journals .pp_fyear").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period").html(period);

                    $(".form-journals input[name=pp_fyear]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period]').val(parseInt((period.trim()).slice(-2)));

                    //$("#add-journal-h1").html(fiscal_end);

                    const dt_date = $('.form-journals input[name=dt_date]').val();

                    $('.form-journals input[name=dt_date]').val(dt_date[0] + dt_date[1]);

                    $('.form-journals input[name=dt_date]').trigger('change');

                    //journal_client_data()

                });





                $(document).on('change', '.form-journals select[name=pp_month_edit]', function() {

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client_edit]'))

                        .attr('fiscal-start');



                    var month = parseInt($(this).val());

                    var year = $(`.form-journals select[name=pp_year_edit]`).val();

                    if (month < 10) {

                        month = '0' + month;

                    }

                    const fs = fiscal_start;

                    fiscal_start = fiscal_start.split('-');

                    const fyear = fiscal_start[0];

                    const fmonth = fiscal_start[1];

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $(".form-journals .pp_fyear_edit").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period_edit").html(period);

                    $(".form-journals input[name=pp_fyear_edit]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period_edit]').val(parseInt((period.trim()).slice(-2)));

                    const dt_date = $('.form-journals input[name=dt_date_edit]').val();

                    $('.form-journals input[name=dt_date_edit]').val(dt_date[0] + dt_date[1]);

                    $('.form-journals input[name=dt_date_edit]').trigger('change');

                });



                $(document).on('change', '.form-journals #pp_year', function() {

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client]')).attr(

                        'fiscal-start');

                    var month = parseInt($(`.form-journals select[name=pp_month]`).val());

                    var year = $(this).val();

                    if (month < 10) {

                        month = '0' + month;

                    }

                    const fs = fiscal_start;

                    const fyear = fiscal_start.split('-')[0];

                    const fmonth = fiscal_start.split('-')[1];

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $(".form-journals .pp_fyear").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period").html(period);

                    $(".form-journals input[name=pp_fyear]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period]').val(parseInt((period.trim()).slice(-2)));

                    //var fiscal_end = getFiscalYearEnd(fiscal_start);

                    // $("#add-journal-h1").html(fiscal_end);

                    const dt_date = $('.form-journals input[name=dt_date]').val();

                    $('.form-journals input[name=dt_date]').val(dt_date[0] + dt_date[1]);

                    $('.form-journals input[name=dt_date]').trigger('change');

                    //journal_client_data()

                });



                $(document).on('change', '.form-journals select[name=pp_year_edit]', function() {

                    let fiscal_start = $('option:selected', $('.form-journals select[name=pp_client_edit]'))

                        .attr('fiscal-start');



                    var month = parseInt($(`.form-journals select[name=pp_month_edit]`).val());

                    var year = $(this).val();

                    if (month < 10) {

                        month = '0' + month;

                    }

                    const fs = fiscal_start;

                    const fyear = fiscal_start.split('-')[0];

                    const fmonth = fiscal_start.split('-')[1];

                    var period = findPeriod(fs, fyear + "-" + month + "-01");

                    var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),

                        parseInt(month),

                        parseInt(year));

                    $(".form-journals .pp_fyear_edit").html("Fiscal Year " + fiscal_end);

                    $(".form-journals .pp_period_edit").html(period);

                    $(".form-journals input[name=pp_fyear_edit]").val(parseInt(fiscal_end));

                    $('.form-journals input[name=pp_period_edit]').val(parseInt((period.trim()).slice(-2)));

                    //var fiscal_end = getFiscalYearEnd(fiscal_start);

                    const dt_date = $('.form-journals input[name=dt_date_edit]').val();

                    $('.form-journals input[name=dt_date_edit]').val(dt_date[0] + dt_date[1]);

                    $('.form-journals input[name=dt_date_edit]').trigger('change');

                });





                $(document).on('change', '.form-journals input[name=dt_account]', function() {

                    var account_no = $(this).val();

                    if (/^\d{3}$/.test(account_no)) {

                        account_no += '0';

                    }

                    $(this).val(account_no);



                    var $this = $(this);

                    const $descEl = $(".form-journals .dt-account-description");

                    if (account_no != '') {

                        if (validateAccNo(account_no)) {

                            const client_id = $('.form-journals select[name=pp_client]').val();

                            const fyear = $('.form-journals input[name=pp_fyear]').val();

                            const period = $('.form-journals input[name=pp_period]').val();

                            $.ajax({

                                type: "GET",

                                url: "{{ url('/journals/get-gifi-client-accounts') }}",

                                data: {

                                    "account_no": account_no,

                                    "client_id": client_id,

                                    "fyear": fyear,

                                    "period": period,

                                }

                            }).done(function(response) {

                                const account = response.account;

                                if (account == null || response.status == "not found") {

                                    $('.form-journals input[name=dt_account]').focus();

                                    Dashmix.helpers('notify', {

                                        from: 'bottom',

                                        align: 'left',

                                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Account number ' +

                                            account_no + ' not found.',

                                        delay: 5000

                                    });



                                    $descEl.html(`<div class="px-2" style="font-size: 10pt; background: #F9EFEE; font-family: Signika; color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; width: fit-content;">

                                                INVALID ACCOUNT#

                                            </div>`);

                                } else {

                                    if (account) {

                                        $this.attr('account', 1);

                                        $descEl.html(

                                            `<div class="dt-account-description-tag">${account.description}</div>`

                                        );

                                    } else {

                                        $this.attr('account', 0);

                                    }

                                    // $("#journal-client-pp-current-account").html("Acct# " + account_no);

                                    $("#journal-client-account-no").css('border', '1px solid #a5a5a5');

                                    $("#journal-client-account-no").html(account_no);

                                    let fyear_balance_debit = response.fyear_balance_debit,

                                        fyear_balance_credit = response.fyear_balance_credit,

                                        account_balance = 0;

                                    if (parseFloat(fyear_balance_debit) > parseFloat(

                                            fyear_balance_credit)) {

                                        account_balance = parseFloat(fyear_balance_debit) - parseFloat(

                                            fyear_balance_credit);

                                        $(".form-journals #account_balance").removeClass(

                                                'acct-balance').removeClass('cr-balance')

                                            .addClass('dr-balance');

                                    } else {

                                        account_balance = parseFloat(fyear_balance_credit) - parseFloat(

                                            fyear_balance_debit);

                                        $(".form-journals #account_balance").removeClass(

                                                'acct-balance').removeClass('dr-balance')

                                            .addClass('cr-balance');

                                    }

                                    if (account_balance == 0) {



                                        $(".form-journals #account_balance").removeClass(

                                                'dr-balance').removeClass('cr-balance')

                                            .addClass('acct-balance');

                                    }

                                    $(".form-journals #account_balance").html(numberFormat(

                                        account_balance,

                                        2));

                                }



                            });

                        } else {

                            $this.focus();

                            $descEl.html("Invalid Account Number");

                        }

                    } else {

                        $this.focus();

                        $descEl.html("");

                    }

                });

                $(document).on('input', '.form-journals input[name=dt_account]', function() {

                    var account_no = $(this).val().replace(/[^0-9]/g, '');

                    $(this).val(account_no);

                });

                $(document).on('input', '.form-journals input[name=dt_account_edit]', function() {

                    var account_no = $(this).val().replace(/[^0-9]/g, '');

                    $(this).val(account_no);

                });



                $(document).on('change', '.form-journals input[name=dt_account_edit]', function() {

                    var account_no = $(this).val();

                    if (/^\d{3}$/.test(account_no)) {

                        account_no += '0';

                    }

                    $(this).val(account_no);

                    const $this = $(this);

                    const $descEl = $(".form-journals .dt-account-description-edit");





                    if (account_no != '') {

                        if (validateAccNo(account_no)) {

                            const client_id = $('.form-journals select[name=pp_client_edit]').val();

                            const fyear = $('.form-journals input[name=pp_fyear_edit]').val();

                            $.ajax({

                                type: "GET",

                                url: "{{ url('/journals/get-gifi-client-accounts') }}",

                                data: {

                                    "account_no": account_no,

                                    "client_id": client_id,

                                    "fyear": fyear,

                                }

                            }).done(function(response) {

                                const account = response.account;

                                if (account) {

                                    $this.attr('account', 1);

                                } else {

                                    $this.attr('account', 0);

                                }

                                if (account.account_no != undefined) {

                                    $this.focus();

                                    $descEl.html(account.description);

                                }



                            });

                        } else {

                            $this.focus();

                            $descEl.html("Invalid Account Number");

                        }

                    } else {

                        $this.focus();

                        $descEl.html("");

                    }

                });





                $(document).on('change', '.edit-journals input[name=dt_account_edit]', function() {

                    var account_no = $(this).val();

                    const $this = $(this);

                    const $descEl = $(".edit-journals .dt-account-description-edit");



                    if (account_no != '') {

                        if (validateAccNo(account_no)) {

                            const client_id = $('.edit-journals select[name=pp_client_edit]').val();

                            const fyear = $('.edit-journals input[name=pp_fyear_edit]').val();

                            $.ajax({

                                type: "GET",

                                url: "{{ url('/journals/get-gifi-client-accounts') }}",

                                data: {

                                    "account_no": account_no,

                                    "client_id": client_id,

                                    "fyear": fyear,

                                }

                            }).done(function(response) {

                                const account = response.account;

                                if (account) {

                                    $this.attr('account', 1);

                                } else {

                                    $this.attr('account', 0);

                                }

                                if (account.account_no != undefined) {

                                    $this.focus();

                                    $descEl.html(account.description);

                                }



                            });

                        } else {

                            $this.focus();

                            $descEl.html("Invalid Account Number");

                        }

                    } else {

                        $this.focus();

                        $descEl.html("");

                    }

                });





                $(document).on('change', '.form-journals input[name=dt_date]', function() {

                    var value = $(this).val().trim();

                    var month = ("0" + $('.form-journals select[name=pp_month]').val()).slice(-2);

                    var year = $('.form-journals select[name=pp_year]').val();

                    // Validate the input value

                    var isValid = validateInputValue(value, year + "-" + month + "-01", month, year);

                    if (!isValid) {

                        value = '';

                    } else {

                        value = isValid;

                    }

                    $(this).val(value);

                    $(".form-journals input[name=translation]").val(translatedDate(value, month, year));

                    if (value != "") {

                        $(".form-journals .translation").html(

                            `<div class="dt-account-description-tag">${translatedDate(value, month, year)}</div>`

                        );

                    } else {

                        $(".form-journals .translation").html("");

                    }

                });





                $(document).on('change', '.form-journals input[name=dt_date_edit]', function() {

                    var value = $(this).val().trim();

                    var month = ("0" + $('.form-journals select[name=pp_month_edit]').val()).slice(-2);

                    var year = $('.form-journals select[name=pp_year_edit]').val();

                    // Validate the input value

                    var isValid = validateInputValue(value, year + "-" + month + "-01", month, year);

                    if (!isValid) {

                        value = '';

                    } else {

                        value = isValid;

                    }

                    $(this).val(value);

                    $(".form-journals input[name=translation_edit]").val(translatedDate(value, month, year));

                    $(".form-journals .translation-edit").html(

                        `<div class="dt-account-description-tag">${translatedDate(value, month, year)}</div>`

                    );

                });





                $(document).on('input', '.form-journals input[name=dt_ref], .form-journals input[name=dt_ref_edit]',

                    function() {

                        var inputText = $(this).val();

                        if (inputText.length > 12) {

                            $(this).val(inputText.substring(0, 12));

                        }

                    });



                $(document).on('input',

                    '.form-journals input[name=dt_description], .form-journals input[name=dt_description_edit]',

                    function() {

                        var inputText = $(this).val();

                        if (inputText.length > 100) {

                            $(this).val(inputText.substring(0, 100));

                        }

                    });

                $(document).on('change', '.form-journals input[name=amnt_tax1],.form-journals input[name=amnt_tax2]',

                    function() {

                        if ($(this).val() != '') {

                            $(this).val(formatValue($(this).val()));

                        }

                        //calcNet()

                        const debit = parseFloat($(".form-journals input[name=amnt_debit]").val());

                        const credit = parseFloat($('.form-journals input[name=amnt_credit]').val());

                        const amnt_tax1 = parseFloat($('.form-journals input[name=amnt_tax1]').val());

                        const amnt_tax2 = parseFloat($('.form-journals input[name=amnt_tax2]').val());

                        if (debit > 0) {

                            gross = debit;

                        } else if (credit > 0) {

                            gross = credit;

                        }

                        var net = gross - (parseFloat(amnt_tax1) + parseFloat(amnt_tax2))

                        $(".form-journals input[name=net]").val(net);

                        $(".form-journals #net").html(net.toLocaleString(undefined, {

                            minimumFractionDigits: 2,

                            maximumFractionDigits: 2

                        }));

                        // calcTaxes();

                    });

                $(document).on('change', '.form-journals input[name=amnt_tax1]', function() {

                    const amnt_tax1 = parseFloat($('.form-journals input[name=amnt_tax1]').val());

                    let p_tx1 = $('.form-journals input[name=tx_gst]').val().replace('%', '');

                    let p_tx2 = $('.form-journals input[name=tx_pst]').val().replace('%', '');



                    if (p_tx1 != '') {

                        p_tx1 = parseFloat(p_tx1) / 100;

                    }

                    if (p_tx2 != '') {

                        p_tx2 = parseFloat(p_tx2) / 100;

                    }

                    var newVal = parseFloat(amnt_tax1) / p_tx1 * p_tx2;

                    $(".form-journals input[name=amnt_tax2]").val(newVal.toFixed(2));

                });

                $(document).on('change', '.form-journals input[name=amnt_tax2]', function() {

                    const amnt_tax2 = parseFloat($('.form-journals input[name=amnt_tax2]').val());

                    let p_tx1 = $('.form-journals input[name=tx_gst]').val().replace('%', '');

                    let p_tx2 = $('.form-journals input[name=tx_pst]').val().replace('%', '');



                    if (p_tx1 != '') {

                        p_tx1 = parseFloat(p_tx1) / 100;

                    }

                    if (p_tx2 != '') {

                        p_tx2 = parseFloat(p_tx2) / 100;

                    }

                    var newVal = parseFloat(amnt_tax2) / p_tx2 * p_tx1;

                    $(".form-journals input[name=amnt_tax1]").val(newVal.toFixed(2));

                });

                $(document).on('change', '.form-journals input[name=amnt_debit]', function() {

                    if ($(this).val() != '') {

                        $(this).val(formatValue($(this).val()));

                    }

                    let debit = parseFloat($('.form-journals input[name=amnt_debit]').val()) || 0;

                    let credit = parseFloat($('.form-journals input[name=amnt_credit]').val()) || 0;

                    if (debit != '' && credit != '') {

                        if (debit > credit) {

                            let diff = parseFloat(debit) - parseFloat(credit);

                            $('.form-journals input[name=amnt_debit]').val(diff.toFixed(2));

                            $('.form-journals input[name=amnt_credit]').val('');

                        } else {

                            let diff = parseFloat(credit) - parseFloat(debit);

                            $('.form-journals input[name=amnt_debit]').val('');

                            $('.form-journals input[name=amnt_credit]').val(diff.toFixed(2));

                        }

                    }

                    calcTaxes();

                });





                $(document).on('change', '.form-journals input[name=amnt_debit_edit]', function() {

                    if ($(this).val() != '') {

                        // $(this).val(formatValue($(this).val()));

                    }

                    let debit = parseFloat($('.form-journals input[name=amnt_debit_edit]').val()) || 0;

                    let credit = parseFloat($('.form-journals input[name=amnt_credit_edit]').val()) || 0;

                    if (debit != '' && credit != '') {

                        if (debit > credit) {

                            let diff = parseFloat(debit) - parseFloat(credit);

                            $('.form-journals input[name=amnt_debit_edit]').val(diff.toFixed(2));

                            $('.form-journals input[name=amnt_credit_edit]').val('');

                        } else {

                            let diff = parseFloat(credit) - parseFloat(debit);

                            $('.form-journals input[name=amnt_debit_edit]').val('');

                            $('.form-journals input[name=amnt_credit_edit]').val(diff.toFixed(2));

                        }

                    }

                });



                $(document).on('change', '.edit-journal input[name=amnt_debit_edit]', function() {

                    if ($(this).val() != '') {

                        // $(this).val(formatValue($(this).val()));

                    }

                    let debit = parseFloat($('.form-journals input[name=amnt_debit_edit]').val()) || 0;

                    let credit = parseFloat($('.form-journals input[name=amnt_credit_edit]').val()) || 0;

                    if (debit != '' && credit != '') {

                        if (debit > credit) {

                            let diff = parseFloat(debit) - parseFloat(credit);

                            $('.edit-journal input[name=amnt_debit_edit]').val(diff.toFixed(2));

                            $('.edit-journal input[name=amnt_credit_edit]').val('');

                        } else {

                            let diff = parseFloat(credit) - parseFloat(debit);

                            $('.edit-journal input[name=amnt_debit_edit]').val('');

                            $('.edit-journal input[name=amnt_credit_edit]').val(diff.toFixed(2));

                        }

                    }

                });





                $(document).on('change', '.form-journals input[name=amnt_credit]', function() {

                    if ($(this).val() != '') {

                        $(this).val(formatValue($(this).val()));

                    }

                    let debit = parseFloat($('.form-journals input[name=amnt_debit]').val()) || 0;

                    let credit = parseFloat($('.form-journals input[name=amnt_credit]').val()) || 0;

                    if (debit != '' && credit != '') {

                        if (debit > credit) {

                            let diff = parseFloat(debit) - parseFloat(credit);

                            $('.form-journals input[name=amnt_debit]').val(diff.toFixed(2));

                            $('.form-journals input[name=amnt_credit]').val('');

                        } else {

                            let diff = parseFloat(credit) - parseFloat(debit);

                            $('.form-journals input[name=amnt_debit]').val('');

                            $('.form-journals input[name=amnt_credit]').val(diff.toFixed(2));

                        }

                    }

                    calcTaxes();

                });





                $(document).on('change', '.form-journals input[name=amnt_credit_edit]', function() {

                    if ($(this).val() != '') {

                        // $(this).val(formatValue($(this).val()));

                    }

                    let debit = parseFloat($('.form-journals input[name=amnt_debit_edit]').val()) || 0;

                    let credit = parseFloat($('.form-journals input[name=amnt_credit_edit]').val()) || 0;

                    if (debit != '' && credit != '') {

                        if (debit > credit) {

                            let diff = parseFloat(debit) - parseFloat(credit);

                            $('.form-journals input[name=amnt_debit_edit]').val(diff.toFixed(2));

                            $('.form-journals input[name=amnt_credit]').val('');

                        } else {

                            let diff = parseFloat(credit) - parseFloat(debit);

                            $('.form-journals input[name=amnt_debit]').val('');

                            $('.form-journals input[name=amnt_credit_edit]').val(diff.toFixed(2));

                        }



                    }

                });





                $(document).on('change', '.edit-journal input[name=amnt_credit_edit]', function() {

                    if ($(this).val() != '') {

                        // $(this).val(formatValue($(this).val()));

                    }

                    let debit = parseFloat($('.edit-journal input[name=amnt_debit_edit]').val()) || 0;

                    let credit = parseFloat($('edit-juurnal input[name=amnt_credit_edit]').val()) || 0;

                    if (debit != '' && credit != '') {

                        if (debit > credit) {

                            let diff = parseFloat(debit) - parseFloat(credit);

                            $('.edit-journal input[name=amnt_debit_edit]').val(diff.toFixed(2));

                            $('.edit-journal input[name=amnt_credit]').val('');

                        } else {

                            let diff = parseFloat(credit) - parseFloat(debit);

                            $('.edit-journal input[name=amnt_debit]').val('');

                            $('.edit-journal input[name=amnt_credit_edit]').val(diff.toFixed(2));

                        }



                    }

                });



                $(document).on('change', '.form-journals input[name=amnt_taxable]', function() {

                    if ($(this).is(":checked")) {

                        $(".form-journals .taxation").removeClass('d-none');

                        $(".form-journals #net-col").removeClass('d-none');

                    } else {

                        $(".form-journals #net-col").addClass('d-none');

                        $(".form-journals .taxation").addClass('d-none');

                        $('.form-journals input[name=amnt_tax1]').val(0);

                        $('.form-journals input[name=amnt_tax2]').val(0);

                    }

                    calcTaxes();

                });

                $(document).on('change', '.form-journals select[name=tx_province]', function() {



                    var province = $(this).val();

                    $.ajax({

                        type: "GET",

                        url: "{{ url('/journals/get-tax-rate-by-province') }}",

                        data: {

                            "province": province,

                        }

                    }).done(function(response) {



                        if (response.tax_rate_1) {

                            $(".form-journals input[name=tx_gst]").val(response.tax_rate_1 + '%');



                            $(".form-journals .amnt_label1").html(response.tax_label_1);

                        } else {

                            $(".form-journals input[name=tx_gst]").val('0.00%');

                            $(".form-journals .amnt_label1").html('Tax1');

                        }









                        if (response.tax_rate_2) {

                            $(".form-journals input[name=tx_pst]").val(response.tax_rate_2 + '%');

                            $(".form-journals .amnt_label2").html(response.tax_label_2);



                        } else {

                            $(".form-journals input[name=tx_pst]").val('0.00%');

                            $(".form-journals .amnt_label2").html('Tax2');



                        }



                        if (response.applied_to_tax1 == 1) {

                            $(".form-journals input[name=amnt_taxable]").attr('applied-to-tax1',

                                response.applied_to_tax1 == 1 ? 1 : 0);

                        } else {

                            $('.form-journals input[name=amnt_taxable]').attr('applied-to-tax1', '');

                        }

                        if (response.tax_label_1) {

                            $(".form-journals #txR1Label").html(response.tax_label_1);

                        } else {

                            $(".form-journals #txR1Label").html('');

                        }

                        if (response.tax_label_2) {

                            $(".form-journals #txR2Label").html(response.tax_label_2);

                        } else {

                            $(".form-journals #txR2Label").html('');

                        }



                        calcTaxes();

                    });

                });



                $(document).on('submit', '#form-add-journal', function(e) {



                    e.preventDefault();

                    e.stopImmediatePropagation();

                    const client = $(".form-journals select[name=pp_client]").val();

                    const year = $(".form-journals select[name=pp_year]").val();

                    const month = $(".form-journals select[name=pp_month]").val();

                    const period = $(".form-journals input[name=pp_period]").val();

                    const fyear = $(".form-journals input[name=pp_fyear]").val();

                    const account_no = $('.form-journals input[name=dt_account]').val();

                    const account_valid = $('.form-journals input[name=dt_account]').attr('account');

                    const source = $(".form-journals input[name=dt_source_code]").val();

                    const ref = $(".form-journals input[name=dt_ref]").val();

                    const date = $('.form-journals input[name=dt_date]').val();

                    const translation = $('.form-journals input[name=translation]').val();

                    const description = $('.form-journals input[name=dt_description]').val();

                    const debit = parseFloat($('.form-journals input[name=amnt_debit]').val()) || 0;

                    const credit = parseFloat($('.form-journals input[name=amnt_credit]').val()) || 0;

                    const taxable = $(".form-journals input[name=amnt_taxable]").is(":checked") ? 1 : 0;

                    let amnt_tax1 = $(".form-journals input[name=amnt_tax1]").val();

                    let amnt_tax2 = $(".form-journals input[name=amnt_tax2]").val();

                    const net = $('.form-journals input[name=net]').val();

                    const province = $('.form-journals select[name=tx_province]').val();

                    const gst = $('.form-journals input[name=tx_gst]').val();

                    const pst = $('.form-journals input[name=tx_pst]').val();

                    const portion = $('.form-journals input[name=portion]').val();

                    const scrollMargin = 60;





                    if (client == '' || client == null || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for client.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_client]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        //$("#add-journal").scrollTop(Field.offset().top);

                        //$(".form-journals select[name=pp_client]").get(0).scrollIntoView();

                        return false;

                    } else if (year == '' || year == null || year == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for year.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_year]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        //$("#add-journal").scrollTop(Field.offset().top);

                        //$(".form-journals select[name=pp_year]").get(0).scrollIntoView();

                        return false;

                    } else if (month == '' || month == null || month == undefined) {

                        const Field = $(".form-journals select[name=pp_month]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        //$("#add-journal").scrollTop(Field.offset().top);

                        //$(".form-journals select[name=pp_month]").get(0).scrollIntoView();

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for month.',

                            delay: 5000

                        });



                        return false;

                    } else if (period == '' || period == null || period == undefined || period == "NaN") {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_month]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        //$("#add-journal").scrollTop(Field.offset().top);

                        //$(".form-journals select[name=pp_month]").get(0).scrollIntoView();

                        return false;

                    } else if (fyear == '' || fyear == null || fyear == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_month]");

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value

                        //Field.focus();$("#add-journal").scrollTop(Field.offset().top);

                        //$(".form-journals select[name=pp_month]").get(0).scrollIntoView();

                        return false;

                    } else if (!validateAccNo(account_no) || account_no == '' || account_no == null ||

                        account_no == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter valid value for account no.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_account]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=dt_account]").get(0).scrollIntoView();

                        return false;

                    } else if (account_valid == '0') {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Account number ' +

                                account_no + ' not found.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_account]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=dt_account]").get(0).scrollIntoView();

                        return false;

                    } else if (source == '' || source == null || source == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for source.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_source_code]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals select[name=dt_source_code]").get(0).scrollIntoView();

                        return false;

                    } else if (ref == '' || ref == null || ref == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for ref.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_ref]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=dt_ref]").get(0).scrollIntoView();

                        return false;

                    } else if (date == '' || date == null || date == undefined) {



                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for date.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_date]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=dt_date]").get(0).scrollIntoView();

                        return false;

                    } else if (translation == '' || translation == null || translation == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_date]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=dt_date]").get(0).scrollIntoView();

                        return false;

                    } else if (description == '' || description == null || description == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for description.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_description]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=dt_description]").get(0).scrollIntoView();

                        return false;

                    } else if (((debit == '' || debit == 0) && (credit == '' || credit == 0)) || debit ==

                        null || credit == null || debit ==

                        NaN || credit == NaN || debit == undefined || credit == undefined) {



                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for debit or credit.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=amnt_debit]");

                        Field.focus();

                        $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                        //$(".form-journals input[name=amnt_debit]").get(0).scrollIntoView();

                        return false;

                    } else {

                        var $btn = $('.submit-add-journal');

                        if ($btn.hasClass('disabled')) {

                            return;

                        }

                        // Add spinner and expand button

                        $btn.addClass('expanded disabled'); // Disable the button and expand it

                        $btn.html(

                            '<i class="fa fa-cog spinner mr-2"></i> Processing...');

                        if (taxable == 0) {

                            amnt_tax1 = 0;

                            amnt_tax2 = 0;

                        }

                        let data = {

                            "_token": "{{ csrf_token() }}",

                            "client": client,

                            "month": month,

                            "year": year,

                            "fyear": fyear,

                            "period": period,

                            "account_no": account_no,

                            "source": source,

                            "ref_no": ref,

                            "description": description,

                            "gl_date": translation,

                            "date": date,

                            "debit": debit,

                            "credit": credit,

                            "taxable": taxable,

                            "original_debit": debit,

                            "original_credit": credit,

                            "net": net,

                            "tax1": amnt_tax1,

                            "tax2": amnt_tax2,

                            "province": province,

                            "pr_tax1": gst,

                            "pr_tax2": pst,

                            "portion": portion,

                        };



                        $.ajax({

                            type: "POST",

                            url: "{{ url('/insert-journal') }}",

                            data: data,

                        }).done(function(response) {

                            const lastInserted = response.lastInserted;

                            $(".form-journals #edit_no").html((parseInt(lastInserted) + 1).toString()

                                .padStart(5, '0'));



                            //$(".form-journals select[name=dt_source_code]").val($(".form-journals select[name=dt_source_code] option:first").val());

                            //$(".form-journals .dt-source-description").html($(".form-journals select[name=dt_source_code] option:first").attr('description'));



                            $(".form-journals .dt-account-description").html(" " + "&nbsp;")

                            $(".form-journals input[name=dt_account]").val('');

                            $(".form-journals input[name=dt_ref]").val();

                            //$('.form-journals input[name=dt_date]').val('');

                            //$('.form-journals input[name=translation]').val('{{ translatedDate() }}');

                            //$('.form-journals input[name=dt_description]').val('');

                            // $('.form-journals .translation').html("{{ translatedDate() }}");

                            $('.form-journals input[name=amnt_taxable]').prop("checked", false);

                            $(".form-journals .taxation").addClass('d-none');

                            $(".form-journals #net-col").addClass('d-none');

                            $('.form-journals input[name=amnt_debit]').val('0.00');

                            $('.form-journals input[name=amnt_credit]').val('0.00');

                            $('.form-journals input[name=amnt_tax1]').val('');

                            $('.form-journals input[name=amnt_tax2]').val('');

                            $(".account_balance").html("0.00");

                            $(".form-journals #net").html("0.00");

                            $(".form-journals input[name=net]").val(0);

                            $(".form-journals input[name=portion]").val('100%');

                            const default_prov = $('option:selected', $(

                                '.form-journals select[name=pp_client]')).attr('default-province');

                            $('.form-journals select[name=tx_province]').val(default_prov).trigger(

                                'change');

                            $(".form-journals #account_balance").html(numberFormat(0.00, 2));

                            $(".form-journals #account_balance").removeClass('dr-balance')

                                .removeClass('cr-balance').addClass('acct-balance');

                            const Field = $(".form-journals input[name=dt_account]");

                            Field.focus();

                            $("#add-journal").scrollTop(Field.offset().top - scrollMargin);

                            journal_client_data();

                            Dashmix.helpers('notify', {

                                from: 'bottom',

                                align: 'left',

                                message: '' + response.count +

                                    ' journals were added successfully <a href="javascript:;" data="' +

                                    JSON.stringify(response.edits) +

                                    '" data-notify="dismiss" class="  btn-notify undo-journals ml-4" >Undo</a>',

                                delay: 5000,

                                type: 'info alert-notify-desktop'

                            });

                            resetButton($btn);

                        });

                    }

                });

                $(document).on('click', '#expand-add-journal-view', function() {

                    const html = `@include('add-journal-form')`;

                    $("#addJournalView").html(html);

                    mode = "add";

                    $("#viewDiv").addClass('d-none');

                    $('#addJournalView').removeClass('d-none');

                    $("#JournalHeader").addClass('d-none');

                    $("#ClientJournalHeader").addClass('d-none');

                    $("#AddJournalHeader").removeClass('d-none');

                    $('.form-journals  select[name=pp_client]').select2()

                    // $(".form-journals select[name=dt_source_code]").select2()

                    //$('.form-journals  select[name=pp_client]').select2('open');

                    $('.tooltip').tooltip('hide');

                    $('[data-toggle=tooltip]').tooltip();

                    $('.form-journals #pp_client').change();

                });

                $(document).on('click', '#btnCloseAJ', function() {

                    if (batch_update) {

                        window.location.reload();

                    }

                    mode = "view";

                    $('#addJournalView').addClass('d-none');

                    $("#viewDiv").removeClass('d-none');

                    $("#AddJournalHeader").addClass('d-none');

                    $("#ClientJournalHeader").addClass('d-none');

                    $("#JournalHeader").removeClass('d-none');



                });

                $(document).on('click', '.undo-journals', function() {

                    var edits = $(this).attr('data');

                    $.ajax({

                        type: "POST",

                        url: "{{ url('/journals/undo') }}",

                        data: {

                            "_token": "{{ csrf_token() }}",

                            "edits": edits,

                        }

                    }).done(function(response) {

                        journal_client_data();

                    });

                });



                $(document).on('click', '.btnEditClientJournal', function() {

                    const journal_id = $(this).attr('data'),

                        client_id = $(this).attr('data-client-id');

                    $.ajax({

                        type: "GET",

                        url: "{{ url('/journals/find/get-client-journal-edit-content') }}/" +

                            journal_id,

                    }).done(function(response) {

                        $("#EditClientJournalModal .new-block-content").html(response);

                        $('.tooltip').tooltip('hide');

                        $('[data-toggle=tooltip]').tooltip();

                        $("#EditClientJournalModal").modal("show");

                    });

                });



                $(document).on('change', '#form-edit-client-journal select', function() {

                    click_edit_journal_modal = 1;

                });

                $(document).on('keyup', '#form-edit-client-journal input, #form-edit-client-journal textarea',

                    function() {

                        click_edit_journal_modal = 1;

                    });



                $(document).on('click', '#close-edit-journal-modal', function() {

                    if (click_edit_journal_modal == 1) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: 'Close window? <a href="javascript:;" data="" data-notify="dismiss" class="  btn-notify proceed-to-close-edit-journal-modal ml-4" >Proceed</a>',

                            delay: 5000,

                            type: 'info alert-notify-desktop'

                        });

                    } else {

                        $("#EditClientJournalModal .new-block-content").html('');

                        $("#EditClientJournalModal").modal("hide");

                        click_edit_journal_modal = 0;

                    }

                });

                $(document).on('click', '.proceed-to-close-edit-journal-modal', function() {

                    $("#EditClientJournalModal .new-block-content").html('');

                    click_edit_journal_modal = 0;

                    $("#EditClientJournalModal").modal("hide");

                });

                $(document).on('submit', '#form-edit-client-journal', function(e) {

                    e.preventDefault();

                    e.stopImmediatePropagation();

                    const client = $(".form-journals select[name=pp_client_edit]").val();

                    const year = $(".form-journals select[name=pp_year_edit]").val();

                    const month = $(".form-journals select[name=pp_month_edit]").val();

                    const period = $(".form-journals input[name=pp_period_edit]").val();

                    const fyear = $(".form-journals input[name=pp_fyear_edit]").val();

                    const account_no = $('.form-journals input[name=dt_account_edit]').val();

                    const account_valid = $(".form-journals input[name=dt_account_edit]").attr('account');

                    const source = $(".form-journals input[name=dt_source_code_edit]").val();

                    const ref = $(".form-journals input[name=dt_ref_edit]").val();

                    const date = $('.form-journals input[name=dt_date_edit]').val();

                    const translation = $('.form-journals input[name=translation_edit]').val();

                    const description = $('.form-journals input[name=dt_description_edit]').val();

                    const debit = $('.form-journals input[name=amnt_debit_edit]').val();

                    const credit = $('.form-journals input[name=amnt_credit_edit]').val();

                    const scrollMargin = 60;





                    if (client == '' || client == null || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for client.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_client_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (year == '' || year == null || year == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for year.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_year_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (month == '' || month == null || month == undefined) {

                        const Field = $(".form-journals select[name=pp_month_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for month.',

                            delay: 5000

                        });



                        return false;

                    } else if (period == '' || period == null || period == undefined || period == "NaN") {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_month_edit]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (fyear == '' || fyear == null || fyear == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals select[name=pp_month_edit]");

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (!validateAccNo(account_no) || account_no == '' || account_no == null ||

                        account_no == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a valid value for account.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_account_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);



                        return false;

                    } else if (account_valid == '0') {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Account no ' +

                                account_no + ' not found.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_account_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);



                        return false;

                    } else if (source == '' || source == null || source == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for source.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_source_code_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        return false;

                    } else if (ref == '' || ref == null || ref == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for ref.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_ref_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        return false;

                    } else if (date == '' || date == null || date == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for date.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_date_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        return false;

                    } else if (translation == '' || translation == null || translation == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_date_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        return false;

                    } else if (description == '' || description == null || description == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for description.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=dt_description_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        return false;

                    } else if ((debit == '' && credit == '') || debit == null || credit == null || debit ==

                        NaN || credit == NaN || debit == undefined || credit == undefined) {



                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for debit or credit.',

                            delay: 5000

                        });

                        const Field = $(".form-journals input[name=amnt_debit_edit]");

                        Field.focus();

                        $("#edit-journal").scrollTop(Field.offset().top - scrollMargin);

                        return false;

                    } else {

                        $("#form-edit-client-journal").append(

                            `<input type="hidden" name="_token" value='{{ csrf_token() }}'>`);

                        $("#form-edit-client-journal").append(

                            `<input type="hidden" name="_submit_type" value='1'>`);





                        var formData = new FormData(document.getElementById("form-edit-client-journal"));

                        $.ajax({

                            type: 'post',

                            data: formData,

                            'url': '{{ url('update-journal') }}',

                            dataType: 'json',

                            async: false,



                            contentType: false,

                            processData: false,

                            cache: false,

                            success: function(res) {

                                journal_client_data();

                                click_edit_journal_modal = 0;

                                $("#EditClientJournalModal").modal('hide');

                                Dashmix.helpers('notify', {

                                    from: 'bottom',

                                    align: 'left',

                                    message: 'Journal successfully saved',

                                    delay: 5000

                                });





                            }

                        })

                    }

                });





                $(document).on('click', '.btnDeleteClientJournal', function() {

                    var journal_id = $(this).attr('data');

                    var client_id = $(this).attr('data-client-id');

                    var c = confirm("Are you really want to delete this journal?");

                    if (c) {

                        $.ajax({

                            type: "POST",

                            url: "{{ url('/delete-journal') }}",

                            data: {

                                "_token": "{{ csrf_token() }}",

                                "journal_id": journal_id,

                                "type": "client"

                            }

                        }).done(function(response) {

                            journal_client_data();

                            Dashmix.helpers('notify', {

                                from: 'bottom',

                                align: 'left',

                                message: 'Journal deleted <a href="javascript:;" data="' +

                                    journal_id +

                                    '" data-notify="dismiss" class="  btn-notify undo-delete-client-journal ml-4" >Undo</a>',

                                delay: 5000,

                                type: 'info alert-notify-desktop'

                            });

                            //Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Journal deleted.', delay: 5000});

                        });

                    }

                });

                $(document).on('mouseenter', '#view-accounts-chart', function() {

                    $(this).find('img').attr('src',

                        '{{ asset('public') }}/icons_2024_02_24/icon-change-account-grey-on.png')

                })

                $(document).on('mouseleave', '#view-accounts-chart', function() {

                    $(this).find('img').attr('src',

                        '{{ asset('public') }}/icons_2024_02_24/icon-change-account-grey-off.png')

                })



                $(document).on('click', '.undo-delete-client-journal', function() {

                    const journal_id = $(this).attr('data');

                    $.ajax({

                        type: "POST",

                        url: "{{ url('undo-delete-journal') }}",

                        data: {

                            "_token": "{{ csrf_token() }}",

                            "journal_id": journal_id,

                        }

                    }).done(function(response) {

                        journal_client_data();

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Journal Undeleted Successfully.',

                            delay: 5000

                        });

                    });

                });

                $(document).on('submit', '#filterClientJournalForm', function(e) {

                    e.preventDefault();

                    e.stopImmediatePropagation();



                    $("#filterClientJournalModal").modal('hide');

                    journal_client_data();

                });

                $(document).on('click', '#gifi-clear-filter-1', function() {

                    $("#filterClientJournalModal").modal('hide');

                    $("#filterClientJournalForm #client-mode-filter-ref").selectpicker('destroy');

                    $("#filterClientJournalForm #client-mode-filter-source").selectpicker('destroy');

                    $("#filterClientJournalForm #client-mode-filter-account").selectpicker('destroy');

                    $("#filterClientJournalForm #client-mode-filter-period").selectpicker('destroy');

                    $("#filterClientJournalForm")[0].reset();



                    $("#filterClientJournalForm #client-mode-filter-ref").val('');

                    $("#filterClientJournalForm #client-mode-filter-source").val('');

                    $("#filterClientJournalForm #client-mode-filter-account").val('');

                    $("#filterClientJournalForm #client-mode-filter-period").val('');



                    $("#filterClientJournalForm #client-mode-filter-ref").selectpicker();

                    $("#filterClientJournalForm #client-mode-filter-source").selectpicker();

                    $("#filterClientJournalForm #client-mode-filter-account").selectpicker();

                    $("#filterClientJournalForm #client-mode-filter-period").selectpicker();

                    journal_client_data();

                });

                $(document).on('click', "#GeneralFilters", function() {

                    $("#filterJournalModal").modal('show');

                });

                $("#filterJournalModal").on('hidden.bs.modal', function() {

                    @if (@$_GET['_applied'] != 1)

                        $('#filter-client, #filter_fiscal_year, #filter-period, #filter-source, #filter-ref, #filter_account')

                            .selectpicker('val', '');

                        $('#filter-client, #filter_fiscal_year, #filter-period, #filter-source, #filter-ref, #filter_account')

                            .selectpicker('refresh');

                        $('#filter_edit_no').val("")

                    @endif

                })

                $(document).on('click', '#ImportJournals', function() {

                    $("#ImportModal").modal('show');

                });



                $(document).on('click', '#Reports', function() {

                    $("#JournalReportModal").modal('show');

                });

                $(document).on('click', '.create-journal-report #clear_report_filters', function() {

                    $(".create-journal-report")[0].reset();

                    $(".create-journal-report #report_source").val('');

                    $(".create-journal-report .filter-option-inner-inner").html("");

                    $(".create-journal-report #report_account").val('');

                    $(".create-journal-report .filter-option-inner-inner").html("");

                    $(".create-journal-report #report_period").val('');

                    $(".create-journal-report .filter-option-inner-inner").html("");

                });



                $(document).on('change', '.form-journals input[name=dt_source_code_]', function() {

                    var dt_source_code = $(this).val();

                    const $descEl = $(".form-journals .dt-source-description");

                    if (dt_source_code != '') {

                        $.ajax({

                            type: "GET",

                            url: "{{ url('/journals/get-source-code') }}",

                            data: {

                                "dt_source_code": dt_source_code

                            }

                        }).done(function(response) {

                            const res = response.data;

                            if (res == null || response.status == "not found") {

                                $('.form-journals input[name=dt_source_code]').focus();

                                Dashmix.helpers('notify', {

                                    from: 'bottom',

                                    align: 'left',

                                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Source not found.',

                                    delay: 5000

                                });



                                $descEl.html(`<div class="px-2" style="font-size: 10pt; background: #F9EFEE; font-family: Signika; color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; width: fit-content;">

                                                INVALID SOURCE

                                            </div>`);

                            } else {

                                $descEl.html(

                                    `<div class="dt-account-description-tag">${res.source_description}</div>`

                                );

                                $(this).val(res.source_code);

                                $('#dt_source_code').val(res.id);

                            }



                        });

                    } else {

                        $('#dt_source_code').val('');

                        $(this).focus();

                        $descEl.html("");

                    }

                    // $(".form-journals .dt-source-description").html(

                    //     `<div class="dt-account-description-tag">${description}</div>`);

                });

                $(document).on('change', '.form-journals input[name=dt_source_code_edit_]', function() {

                    var dt_source_code = $(this).val();

                    const $descEl = $(".form-journals .dt-source-description-edit");

                    if (dt_source_code != '') {

                        $.ajax({

                            type: "GET",

                            url: "{{ url('/journals/get-source-code') }}",

                            data: {

                                "dt_source_code": dt_source_code

                            }

                        }).done(function(response) {

                            const res = response.data;

                            if (res == null || response.status == "not found") {

                                $('.form-journals input[name=dt_source_code]').focus();

                                Dashmix.helpers('notify', {

                                    from: 'bottom',

                                    align: 'left',

                                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Source not found.',

                                    delay: 5000

                                });



                                $descEl.html(`<div class="px-2" style="font-size: 10pt; background: #F9EFEE; font-family: Signika; color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; width: fit-content;">

                                                INVALID SOURCE

                                            </div>`);

                            } else {

                                $descEl.html(res.source_description);

                                $(this).val(res.source_code);

                                $('.form-journals #dt_source_code_edit').val(res.id);

                            }



                        });

                    } else {

                        $('.form-journals #dt_source_code_edit').val('');

                        $(this).focus();

                        $descEl.html("");

                    }

                    // $(".form-journals .dt-source-description").html(

                    //     `<div class="dt-account-description-tag">${description}</div>`);

                });

                $(document).on('change', '#edit-journal input[name=dt_source_code_edit_]', function() {

                    var dt_source_code = $(this).val();

                    const $descEl = $("#edit-journal .dt-source-description-edit");

                    if (dt_source_code != '') {

                        $.ajax({

                            type: "GET",

                            url: "{{ url('/journals/get-source-code') }}",

                            data: {

                                "dt_source_code": dt_source_code

                            }

                        }).done(function(response) {

                            const res = response.data;

                            if (res == null || response.status == "not found") {

                                $('.form-journals input[name=dt_source_code]').focus();

                                Dashmix.helpers('notify', {

                                    from: 'bottom',

                                    align: 'left',

                                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Source not found.',

                                    delay: 5000

                                });



                                $descEl.html(`<div class="px-2" style="font-size: 10pt; background: #F9EFEE; font-family: Signika; color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; width: fit-content;">

                                                INVALID SOURCE

                                            </div>`);

                            } else {

                                $descEl.html(res.source_description);

                                $(this).val(res.source_code);

                                $('#edit-journal #dt_source_code_edit').val(res.id);

                            }



                        });

                    } else {

                        $('#edit-journal #dt_source_code_edit').val('');

                        $(this).focus();

                        $descEl.html("");

                    }

                    // $(".form-journals .dt-source-description").html(

                    //     `<div class="dt-account-description-tag">${description}</div>`);

                });



                function getSource(name, field, field2, field3 = ".dt-source-description") {

                    var dt_source_code = name;

                    const $descEl = $(field3);

                    if (dt_source_code != '') {

                        $.ajax({

                            type: "GET",

                            url: "{{ url('/journals/get-source-code') }}",

                            data: {

                                "dt_source_code": dt_source_code

                            }

                        }).done(function(response) {

                            const res = response.data;

                            if (res == null || response.status == "not found") {

                                $('.form-journals input[name=dt_account]').focus();

                                Dashmix.helpers('notify', {

                                    from: 'bottom',

                                    align: 'left',

                                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Account number ' +

                                        account_no + ' not found.',

                                    delay: 5000

                                });



                                $descEl.html(`<div class="px-2" style="font-size: 10pt; background: #F9EFEE; font-family: Signika; color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; width: fit-content;">

                                                INVALID SOURCE

                                            </div>`);

                            } else {

                                $descEl.html(res.source_description);

                                $('#' + field).val(res.source_code);

                                $('#' + field2).val(res.id);

                            }



                        });

                    } else {

                        $('#' + field2).val('');

                        $('#' + field).focus();

                        $descEl.html("");

                    }

                }

                // $(document).on('change', '.form-journals select[name=dt_source_code_edit]', function() {

                //     var description = $('option:selected', $(this)).attr('description');

                //     $(".form-journals .dt-source-description-edit").html(

                //         `<div class="dt-account-description-tag">${description}</div>`);

                // });

                // $(document).on('change', '.edit-journals select[name=dt_source_code_edit]', function() {

                //     var description = $('option:selected', $(this)).attr('description');

                //     $(".edit-journals .dt-source-description-edit").html(

                //         `<div class="dt-account-description-tag">${description}</div>`);

                // });







                document.addEventListener('keydown', function(event) {

                    if (mode == "add") {

                        // Check if the Alt key and Enter key are both pressed

                        if (event.ctrlKey && event.key === 'Enter') {



                            // Perform your desired action here

                            $(".submit-add-journal").click();

                        }





                        if (event.key === ' ') {

                            // Perform your desired action here

                            var activeElement = document.activeElement;

                            var tagName = activeElement.tagName.toLowerCase();

                            var saveButton = document.querySelector('.submit-add-journal');

                            // Check if none of the input, select, or textarea elements are focused

                            if (tagName !== 'input' && tagName !== 'select' && tagName !== 'textarea' &&

                                activeElement != saveButton) {

                                // Perform your desired action here

                                const $el = $(".form-journals #amnt_taxable");

                                $el.click();

                            }

                        }



                    }



                });





                $(document).on('click', '#AccountChartModal .account-item', function() {

                    $("#AccountChartModal .account-item").each(function(index) {

                        $(this).removeClass('c-active').removeClass('account-item-active');

                    });

                    $(this).addClass('c-active').addClass('account-item-active');

                    $("#select-account-no-block").removeClass('d-none');

                });



                $(document).on('click', "#select-account-no", function() {

                    const account_no = $("#AccountChartModal  .account-item-active").attr('account-no');



                    if (account_no != undefined && account_no != '') {

                        $(".form-journals input[name=dt_account]").val(account_no);

                        $(".form-journals input[name=dt_account]").trigger('change');

                        $(".form-journals input[name=dt_account_edit]").val(account_no);

                        $(".form-journals input[name=dt_account_edit]").trigger('change');

                    }

                    $("#AccountChartModal").modal('hide');

                });

                $(document).on('click', "#select-source", function() {

                    const id = $("#sourceModal  .account-item-active").attr('account-no');

                    const name = $("#sourceModal  .account-item-active").attr('data-name');

                    getSource(name, 'dt_source_code_', 'dt_source_code');



                    // if (id != undefined && id != '') {

                    //     $(".form-journals input[name=dt_account]").val(account_no);

                    //     $(".form-journals input[name=dt_account]").trigger('change');

                    //     $(".form-journals input[name=dt_account_edit]").val(account_no);

                    //     $(".form-journals input[name=dt_account_edit]").trigger('change');

                    // }

                    $("#sourceModal").modal('hide');

                });

                $(document).on('click', "#select-source-edit", function() {

                    const id = $("#editSourceModel  .account-item-active").attr('account-no');

                    const name = $("#editSourceModel  .account-item-active").attr('data-name');

                    getSource(name, 'dt_source_code_edit_', 'dt_source_code_edit',

                        '.dt-source-description-edit');



                    // if (id != undefined && id != '') {

                    //     $(".form-journals input[name=dt_account]").val(account_no);

                    //     $(".form-journals input[name=dt_account]").trigger('change');

                    //     $(".form-journals input[name=dt_account_edit]").val(account_no);

                    //     $(".form-journals input[name=dt_account_edit]").trigger('change');

                    // }

                    $("#editSourceModel").modal('hide');

                });



                $(document).on('click', '#sourceModal .account-item', function() {

                    $("#sourceModal .account-item").each(function(index) {

                        $(this).removeClass('c-active').removeClass('account-item-active');

                    });

                    $(this).addClass('c-active').addClass('account-item-active');

                    $("#select-account-no-block").removeClass('d-none');

                });

                $(document).on('click', '#editSourceModel .account-item', function() {

                    $("#editSourceModel .account-item").each(function(index) {

                        $(this).removeClass('c-active').removeClass('account-item-active');

                    });

                    $(this).addClass('c-active').addClass('account-item-active');

                    $("#select-account-no-block").removeClass('d-none');

                });





                $(document).on('click', '.view-accounts-chart-edit', function() {

                    var client_id = $("#edit-journal select[name=pp_client_edit]").val();



                    getClientGifi(client_id, "dt_account_description_list2");

                    $("#AccountChartModal").modal('show');

                });

                $(document).on('click', '.view-accounts-chart-edit-1', function() {

                    var client_id = $(this).attr('client-id');



                    getClientGifi(client_id, "dt_account_description_list2");

                    $("#AccountChartModal").modal('show');

                });





            });



            $(document).ready(function() {

                $(document).on('shown.bs.modal', '#CommentModal', function() {

                    $("#CommentModal textarea[name=comment]").focus();

                });

                $(document).on('shown.bs.modal', '#EndModal', function() {

                    $('#EndModal textarea[name=reason]').focus();

                });





                $(document).on('change', 'input[name=tx_pst], input[name=tx_gst]', function() {

                    /**

                     * ONCHANGE TAXES ReCALCULATE GROSS/TAXES/NET

                     */

                    calcTaxes();



                });





                $(document).keydown(function(event) {

                    if (event.keyCode === 9) { // Tab key code is 9

                        setTimeout(() => {

                            var focusedElement = $(':focus');

                            // if (focusedElement.hasClass("select2-selection")) {

                            //     const $select = $("select[name=dt_source_code]");

                            //     $select.select2('open');

                            // }

                        }, 100);

                    }

                });



                $(document).on('click', '#return-to-posting-period', function() {

                    if ($(".form-journals select[name=pp_client]").hasClass('select2-hidden-accessible')) {

                        const $select = $(".form-journals select[name=pp_client]");

                        $select.select2('close');

                        setTimeout(() => {

                            $select.select2('open');

                        }, 200);

                    } else {

                        $(".form-journals select[name=pp_client]").focus();

                    }

                    $(".client-journals-view").html('');

                    $(".form-journals .dt-account-description").html(" " + "&nbsp;")

                    $(".form-journals input[name=dt_account]").val('').trigger('change');

                    $("#dt_source_code").val('').trigger('change');

                    $("#dt-source-description").val('');

                    $(".form-journals #account_balance").removeClass(

                            'acct-balance').removeClass('cr-balance')

                        .addClass('dr-balance');

                    $(".form-journals #account_balance").html('&nbsp;');

                    $(".batch-update").addClass('d-none')

                    $(".journal-view").removeClass('selected');

                    $('.batch-selection').attr('data-selected', 0);

                    $(".batch-selection").attr('data-original-title', 'Select All Journals');

                    $('.batch-selection').find("img").attr('src',

                        '{{ asset('public/batch_icons/icon-journals-select-all.png') }}');

                    $(".form-journals #form-add-journal-posting-period").removeClass('d-none');

                    $(".form-journals #form-add-journal-client-stats").addClass('d-none');

                    $(".form-journals #form-add-journal-details").addClass('d-none');

                    $(".form-journals #form-add-journal-amount").addClass('d-none');



                    $(".form-journals select[name=pp_client]").val('').change();

                    $(".form-journals select[name=pp_year]").val('').change();

                    $(".form-journals select[name=pp_month]").val('').change();





                });



                $(document).on({

                    mouseenter: function() {

                        var $this = $(this);

                        if ($this.data('tooltipShown')) {

                            // $this.tooltip('hide');

                        } else {







                            const data_business = $this.attr('data-notes');

                            const line1 = $this.attr('data-line1');

                            const line2 = $this.attr('data-line2');

                            const line3 = $this.attr('data-line3');



                            var html = `

                                <p class="mb-0 tooltip-h6">Portioning</p>

                                <p class="text-muted mb-0">${line1}</p>

                                <p class="text-muted mb-0">${line2}</p>

                                <p class="text-muted mb-0">${line3}</p>

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

                    },

                }, '.portion-info');







                // $(document).on({

                //     mouseenter: function () {

                //         var $this = $(this);

                //         if ($this.data('tooltipShown')) {

                //          // $this.tooltip('hide');

                //         } else

                //    {







                //    const data_standard=$this.attr('data-standard'),

                //    data_translated=$this.attr('data-translated');

                // var html = `

        // <p class="tooltip-h6" style="font-weight: 600;">Import type</p>

        // <p class="mb-2 "><span class="tooltip-h6">Standard:</span> <span>${data_standard}</span></p>

        // <p class="mb-2 "><span class="tooltip-h6">Translated:</span> <span>${data_translated}</span></p>

        // `;



                //     $this.tooltip({

                //           title: html,

                //           html: true,

                //           placement: 'bottom',

                //           trigger: 'manual',

                //             template: '<div class="tooltip custom-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div> <div class="modal-content info-tooltip-close"><div class="btn-block-option"><i class="fa fa-fw fa-times"></i></div></div></div>'

                //         });

                //         $this.tooltip('show');

                //    }

                //   $this.data('tooltipShown', !$this.data('tooltipShown'));

                //     },

                //     mouseleave: function () {

                //         var $this = $(this);

                //         if ($this.data('tooltipShown')) {

                //           //$this.tooltip('hide');

                //         }

                //     },

                // }, '.import-type-info');



                let import_type_tooltip;

                $(document).on('click', '.import-type-info', function() {

                    const $this = $(this);

                    import_type_tooltip = $this;



                    const data_standard = $this.attr('data-standard'),

                        data_translated = $this.attr('data-translated');

                    var html = `<p class="tooltip-h6" style="font-weight: 600;">Import type</p>

                                    <p class="mb-2 "><span class="tooltip-h6">Standard:</span> <span>${data_standard}</span></p>

                                    <p class="mb-2 "><span class="tooltip-h6">Translated:</span> <span>${data_translated}</span></p>

                                    `;



                    $this.tooltip({

                        title: html,

                        html: true,

                        placement: 'bottom',

                        trigger: 'manual',

                        template: '<div class="tooltip custom-tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div> <div class="modal-content info-tooltip-close"><div class="btn-block-option"><i class="fa fa-fw fa-times"></i></div></div></div>'

                    });

                    $this.tooltip('show');

                    $this.data('tooltipShown', !$this.data('tooltipShown'));

                });

                $(document).on('click', '.info-tooltip-close', function() {

                    import_type_tooltip.tooltip('hide');

                })



            });









            $(document).ready(function() {

                $(document).on('click', '.undo-delete', function() {

                    var id = $(this).attr('data');

                    window.location.href = "{{ url('undo-delete-journal-on-relaod') }}?id=" + id;

                });

            });



            $(document).ready(function() {

                $("#ImportJournalForm").on('submit', function(e) {

                    e.preventDefault();

                    e.stopImmediatePropagation();

                    const client = $("#ImportJournalForm select[name=client]").val();

                    const import_type = $("#ImportJournalForm select[name=import_type]").val();

                    const file = $("#ImportJournalForm input[name=file]")[0].files.length;





                    const scrollMargin = 60;







                    if (client == '' || client == null || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for client.',

                            delay: 5000

                        });

                        const Field = $("#ImportJournalForm select[name=client]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (import_type == '' || import_type == null || import_type == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for import type.',

                            delay: 5000

                        });

                        const Field = $("#ImportJournalForm select[name=import_type]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (file == 0) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please upload csv.',

                            delay: 5000

                        });

                        const Field = $("#ImportJournalForm input[name=file]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else {

                        if (import_type == 'Standard') {

                            $("#ImportJournalForm").attr('action',

                                '{{ url('/import-excel-standard-journals') }}');

                        } else {

                            $("#ImportJournalForm").attr('action', '{{ url('/import-excel-journals') }}');

                        }

                        $("#ImportModal").modal('hide');

                        setTimeout(() => {

                            $("#ImportJournalForm")[0].submit();



                            $("#contentDiv").html(`

    <div class="row px-0">

            <div class="col-md-12" style="min-height: 90vh !important;">

                <div class="d-flex h-100 flex-column align-items-center justify-content-center">



<img src="{{ asset('public/spinner.gif') }}" width="50px">

<p>Please wait while importing journals...</p>

</div>

            </div>

        </div>

    `);



                        }, 200);

                    }



                });



                $("#ImportJournalForm select[name=import_type]").on('change', function() {

                    if ($(this).val() == 'Standard') {

                        $("#import-example-sheet").attr('href', '{{ asset('public/standard-import.csv') }}');

                    } else {

                        $("#import-example-sheet").attr('href',

                            '{{ asset('public/translated-import.csv') }}');

                    }

                });



            });



            /**

             * BATCH UPDATE

             */

            $(document).ready(function() {

                $(document).on('click', '.journal-view', function(e) {

                    if (e.ctrlKey && e.shiftKey) {

                        e.preventDefault();

                        $(this).addClass('selected');

                        $(".batch-update").removeClass('d-none')

                        $(".batch-selection").attr('data-selected', 1);

                        $(".batch-selection").attr('data-original-title', 'Deselect Journals');

                        $(".batch-selection").find("img").attr('src',

                            '{{ asset('public/batch_icons/icon-journals-deselect-all.png') }}');

                    }

                });

                $(document).on('change', "#batch_update_mod_type", function() {

                    $("#JournalBatchUpdateModal .form-group-edit").removeClass('d-none')

                    if ($(this).val().trim() == 'Delete') {

                        $("#JournalBatchUpdateModal .form-group-edit").addClass('d-none')

                        $("#deleteLabel").removeClass('d-none')

                    } else {

                        $("#deleteLabel").addClass('d-none')

                    }

                })

                $(document).on('click', '.batch-selection', function() {

                    if ($(this).attr('data-selected') === '1') {

                        $(".batch-update").addClass('d-none')

                        $(".journal-view").removeClass('selected');

                        $(this).attr('data-selected', 0);

                        $(".batch-selection").attr('data-original-title', 'Select All Journals');

                        $(this).find("img").attr('src',

                            '{{ asset('public/batch_icons/icon-journals-select-all.png') }}');

                    } else {

                        $(".batch-update").removeClass('d-none')

                        $(".journal-view").addClass('selected');

                        $(this).attr('data-selected', 1);

                        $(".batch-selection").attr('data-original-title', 'Deselect Journals');

                        $(this).find("img").attr('src',

                            '{{ asset('public/batch_icons/icon-journals-deselect-all.png') }}');

                    }

                    $('[data-toggle="tooltip"]').tooltip();

                });



                $(document).on('click', '.journal-view', function(e) {

                    if (e.ctrlKey) {

                        if ($(this).hasClass('selected')) {

                            // $(".batch-update").addClass('d-none');

                            $(this).removeClass('selected');

                            // $(".batch-selection").attr('data-selected', 0);

                            // $(".batch-selection").attr('data-original-title', 'Select Journals');

                            // $(".batch-selection").find("img").attr('src',

                            //     '{{ asset('public/batch_icons/icon-journals-select-all.png') }}');

                        } else {

                            // $(".batch-update").removeClass('d-none');

                            $(this).addClass('selected');

                            // $(".batch-selection").attr('data-selected', 1);

                            // $(".batch-selection").attr('data-original-title', 'Deselect Journals');

                            // $(".batch-selection").find("img").attr('src',

                            //     '{{ asset('public/batch_icons/icon-journals-deselect-all.png') }}');

                        }

                        $('[data-toggle="tooltip"]').tooltip();

                        const allSelected = $('.journal-view:visible').length === $(

                            '.journal-view:visible.selected').length;



                        if (allSelected) {

                            $(".batch-selection").attr('data-selected', 1);

                            $(".batch-selection").attr('data-original-title', 'Deselect Journals');

                            $(".batch-selection").find("img").attr('src',

                                '{{ asset('public/batch_icons/icon-journals-deselect-all.png') }}');

                        } else {

                            $(".batch-selection").attr('data-selected', 0);

                            $(".batch-selection").attr('data-original-title', 'Select All Journals');

                            $(".batch-selection").find("img").attr('src',

                                '{{ asset('public/batch_icons/icon-journals-select-all.png') }}');

                        }



                        if ($('.journal-view:visible.selected').length > 0) {

                            $(".batch-update").removeClass('d-none');

                        } else {

                            $(".batch-update").addClass('d-none');

                        }

                    }

                });

                let batch_journals = [];

                $(document).on('click', '.batch-update', function() {

                    batch_journals = [];

                    $(".journal-view").each(function() {

                        if ($(this).hasClass('selected')) {

                            batch_journals.push($(this).attr('data'));

                        }

                    });

                    if (batch_journals.length == 0) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select journals to update.',

                            delay: 5000

                        });

                    } else {

                        $(".total-selected-batch").html(batch_journals.length + " journals selected");

                        $("#JournalBatchUpdateModal").modal('show');

                    }

                });

                $("#form-journal-batch-update").on('submit', function(e) {

                    e.preventDefault();

                    e.stopImmediatePropagation();

                    const

                        mod_type = $("#form-journal-batch-update input[name=batch_update_mod_type]").val()

                        .trim(),

                        client = $("#form-journal-batch-update select[name=batch_update_client]"),

                        month = $("#form-journal-batch-update select[name=batch_update_month]"),

                        year = $("#form-journal-batch-update select[name=batch_update_year]"),

                        period = $("#form-journal-batch-update select[name=batch_update_period]"),

                        fyear = $("#form-journal-batch-update select[name=batch_update_fiscal_year]"),

                        source = $("#form-journal-batch-update select[name=batch_update_source]");

                    if (mod_type == 'Update') {

                        if (client.val() == "" && month.val() == "" && year.val() == "" && period.val() == "" &&

                            fyear.val() == "" && source.val() == "") {

                            Dashmix.helpers('notify', {

                                from: 'bottom',

                                align: 'left',

                                message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > No change detected.',

                                delay: 5000

                            });

                        } else {

                            let html =

                                `<p class="" style="font-family: Signika;">The following changes will be performed on all <b>${batch_journals.length} journals</b> selected:</p>`;

                            if (client.val() != "") {

                                html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label ">Client</label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">${client.find('option:selected').text()}</label>

                            </div>

                        </div>`;

                            }

                            if (month.val() != "") {

                                html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label ">Month</label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">${month.find('option:selected').text()}</label>

                            </div>

                        </div>`;

                            }

                            if (year.val() != "") {

                                html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label ">Year</label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">${year.find('option:selected').text()}</label>

                            </div>

                        </div>`;

                            }

                            if (period.val() != "") {

                                html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label ">Period</label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">${period.find('option:selected').text()}</label>

                            </div>

                        </div>`;

                            }

                            if (fyear.val() != "") {

                                html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label ">Fiscal Year</label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">${fyear.find('option:selected').text()}</label>

                            </div>

                        </div>`;

                            }

                            if (source.val() != "") {

                                html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label ">Source</label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">${source.find('option:selected').text()}</label>

                            </div>

                        </div>`;

                            }

                            $("#JournalBatchUpdateModal").modal('hide');

                            $("#BatchJournalUpdatesConfirmationModal").find('.confirmation-container').html(html);

                            $("#BatchJournalUpdatesConfirmationModal").modal('show');

                        }

                    } else {

                        let html =

                            `<p class="" style="font-family: Signika;">The following changes will be performed on all <b>${batch_journals.length} journals</b> selected:</p>`;

                        html += `<div class="row form-group  ">

                            <div class="col-sm-3">

                                <label class="col-form-label "></label>

                            </div>

                            <div class="col-sm-6 ">

                                <label class="col-form-label text-danger">Delete</label>

                            </div>

                        </div>`;

                        $("#JournalBatchUpdateModal").modal('hide');

                        $("#BatchJournalDeleteConfirmationModal").find('.confirmation-container').html(html);

                        $("#BatchJournalDeleteConfirmationModal").modal('show');

                    }

                });

                $("#cancel-batch-update").on('click', function() {

                    $("#BatchJournalUpdatesConfirmationModal").modal('hide');

                });

                $("#cancel-batch-delete").on('click', function() {

                    $("#BatchJournalDeleteConfirmationModal").modal('hide');

                });

                $("#proceed-batch-update").on('click', function() {

                    const client = $("#form-journal-batch-update select[name=batch_update_client]").val(),

                        month = $("#form-journal-batch-update select[name=batch_update_month]").val(),

                        year = $("#form-journal-batch-update select[name=batch_update_year]").val(),

                        period = $("#form-journal-batch-update select[name=batch_update_period]").val(),

                        fyear = $("#form-journal-batch-update select[name=batch_update_fiscal_year]").val(),

                        source = $("#form-journal-batch-update select[name=batch_update_source]").val();

                    batch_update = true;



                    let undoTimeout;

                    const undoBatchUpdate = function() {

                        clearTimeout(undoTimeout);

                        $.ajax({

                            type: "POST",

                            url: "{{ url('/journals/undo-batch-update') }}",

                            data: {

                                "_token": "{{ csrf_token() }}",

                                "client": client,

                                "month": month,

                                "year": year,

                                "period": period,

                                "fyear": fyear,

                                "source": source,

                                "journals": JSON.stringify(batch_journals),

                            }

                        }).done(function(response) {

                            journal_client_data();

                            batch_journals = [];

                            $(".journal-view").removeClass('selected');

                            $('.batch-selection').attr('data-selected', 0);

                            $('.batch-selection').find("img").attr('src',

                                '{{ asset('public/batch_icons/icon-journals-select-all.png') }}'

                            );

                            $("#BatchJournalUpdatesConfirmationModal").modal('hide');

                            Dashmix.helpers('notify', {

                                from: 'bottom',

                                align: 'left',

                                type: 'success',

                                message: 'Batch update undone',

                                delay: 1000

                            });



                            debugger;

                        });

                    };



                    $.ajax({

                        type: "POST",

                        url: "{{ url('/journals/batch-update') }}",

                        data: {

                            "_token": "{{ csrf_token() }}",

                            "client": client,

                            "month": month,

                            "year": year,

                            "period": period,

                            "fyear": fyear,

                            "source": source,

                            "journals": JSON.stringify(batch_journals),

                        }

                    }).done(function(response) {

                        journal_client_data();

                        batch_journals = [];

                        $(".journal-view").removeClass('selected');

                        $('.batch-selection').attr('data-selected', 0);

                        $('.batch-selection').find("img").attr('src',

                            '{{ asset('public/batch_icons/icon-journals-select-all.png') }}');

                        $("#BatchJournalUpdatesConfirmationModal").modal('hide');



                        const notify = Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: `${response} journals updated <button id="undo-batch-update" class="btn btn-link">UNDO</button>`,

                            delay: 120000

                        });



                        undoTimeout = setTimeout(function() {}, 120000);



                        $('#undo-batch-update').on('click', function() {

                            // alert('ok');

                            undoBatchUpdate();

                            notify.close();

                        });

                    });

                });



                $("#proceed-batch-delete").on('click', function() {

                    const c = confirm(

                        "The following journals delete will be permanent! Please confirm deletion.");

                    if (c) {

                        batch_update = true;

                        $.ajax({

                            type: "POST",

                            url: "{{ url('/journals/batch-delete') }}",

                            data: {

                                "_token": "{{ csrf_token() }}",

                                "journals": JSON.stringify(batch_journals)

                            }

                        }).done(function(response) {

                            journal_client_data();

                            batch_journals = [];

                            $(".journal-view").removeClass('selected');

                            $('.batch-selection').attr('data-selected', 0);

                            $('.batch-selection').find("img").attr('src',

                                '{{ asset('public/batch_icons/icon-journals-select-all.png') }}');

                            $("#BatchJournalDeleteConfirmationModal").modal('hide');

                            Dashmix.helpers('notify', {

                                from: 'bottom',

                                align: 'left',

                                message: response + ' journals deleted',

                                delay: 5000

                            });

                        });

                    }

                });



            });

            /**

             *

             * Reindex Fiscal Year

             **/

            $(document).ready(function() {

                $(document).on('change', '#form-reindex-fiscal-year-journals select[name=reindex_client]', function() {

                    const client = $(this).val();

                    $.ajax({

                        type: "POST",

                        url: "{{ url('/journals/client/get-fyears') }}",

                        data: {

                            "_token": "{{ csrf_token() }}",

                            "client": client,

                        }

                    }).done(function(response) {

                        $("#form-reindex-fiscal-year-journals select[name=reindex_client]").select2(

                            'destroy');

                        $("#form-reindex-fiscal-year-journals select[name=reindex_fiscal_year]").html(

                            `<option value="" selected>Select Fiscal</option>`);



                        if (response.length > 0) {

                            response.forEach((ele) => {



                                $("#form-reindex-fiscal-year-journals select[name=reindex_fiscal_year]")

                                    .append(`<option value="${ele}">${ele}</option>`);

                            });

                        }

                        $("#form-reindex-fiscal-year-journals select[name=reindex_client]").select2();

                    });

                });

                $(document).on('change', '#form-reindex-fiscal-year-journals select[name=reindex_fiscal_year]',

                    function() {

                        const fyear = $(this).val(),

                            client = $("#form-reindex-fiscal-year-journals select[name=reindex_client]").val();

                        $.ajax({

                            type: "POST",

                            url: "{{ url('/journals/client/count-fy-journals') }}",

                            data: {

                                "_token": "{{ csrf_token() }}",

                                "client": client,

                                "fyear": fyear,

                            }

                        }).done(function(response) {

                            $("#form-reindex-fiscal-year-journals .found-journals").html("Found " +

                                response + " journals to reindex.");

                        });

                    });

                $("#form-reindex-fiscal-year-journals").on('submit', function() {

                    const client = $("#form-reindex-fiscal-year-journals select[name=reindex_client]").val(),

                        fyear = $("#form-reindex-fiscal-year-journals select[name=reindex_fiscal_year]").val();

                    const scrollMargin = 60;



                    if (client == '' || client == null || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for client.',

                            delay: 5000

                        });

                        const Field = $("#form-reindex-fiscal-year-journals select[name=reindex_client]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else if (fyear == '' || fyear == null || fyear == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for fyear.',

                            delay: 5000

                        });

                        const Field = $("#form-reindex-fiscal-year-journals select[name=reindex_fiscal_year]");

                        Field.focus();

                        Field[0].scrollIntoView({

                            block: 'start',

                            behavior: 'smooth'

                        });

                        window.scrollBy(0, -

                            scrollMargin); // Optional: Adjust the scroll position by the negative margin value



                        return false;

                    } else {



                    }

                });

            });







            $(document).ready(function() {

                $(document).on('change', '.create-remittance-report input[name=report_type]', function() {

                    if ($(this).val() == 'By Month') {

                        $(".create-remittance-report #report_month_row").removeClass('d-none')

                    } else {

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

                $(document).on('change', '#form-journal-report select[name=report_client]', function() {

                    const id = $(this).val()

                    if (id) {

                        $("#form-journal-report #report_account").html("")

                        $.ajax({

                            type: "GET",

                            url: "{{ url('/get-client-gifi-accounts') }}?id=" + id

                        }).done(function(response) {

                            if (response.length > 0) {

                                let html = ``

                                response.forEach(r => {

                                    html +=

                                        `<option value="${r.account_no}">${r.account_no}</option>`

                                })

                                $("#form-journal-report #report_account").html(html)

                                $("#form-journal-report #report_account").selectpicker('refresh')

                            }

                        })

                    }

                })

                $(document).on('submit', '.create-journal-report', function(e) {

                    e.preventDefault()

                    e.stopImmediatePropagation()

                    const client = $(".create-journal-report select[name=report_client]").val(),

                        fyear = $(".create-journal-report select[name=report_fiscal_year]").val();



                    if (client == "" || client == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for client.',

                            delay: 5000

                        });



                        return false;

                    }

                    if (fyear == "" || fyear == undefined) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for fyear.',

                            delay: 5000

                        });



                        return false;

                    }

                    $('.create-journal-report')[0].submit()

                })

            })



            /**

             * REPORT TYPE

             */

            $(document).ready(function() {

                $(document).on('click', ".select-report-type", function() {

                    $(".select-report-type").attr('status', 0);

                    $(this).attr('status', 1);

                });

                $(document).on('click', '#proceed-to-report', function() {

                    const type = $(`.select-report-type[status="1"]`).attr('data-id');

                    if (type == '' || type == null || type == undefined || isNaN(type)) {

                        Dashmix.helpers('notify', {

                            from: 'bottom',

                            align: 'left',

                            message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select Report.',

                            delay: 5000

                        });

                        return false;

                    } else {

                        $("#ReportTypeModal").modal('hide');

                        if (type === '1') {

                            $("#JournalReportModal").modal('show');

                        }

                        if (type === '2') {

                            $("#TrialBalanceModal").modal('show');

                        }

                        if (type === '3') {

                            $("#FinancialStatementModal").modal('show');

                        }

                        if (type === '4') {

                            $("#RemittanceReportModal").modal('show');

                        }

                        if (type === '5') {

                            $("#ProgressReportModal").modal('show')

                        }

                    }

                });

            });

        </script>

