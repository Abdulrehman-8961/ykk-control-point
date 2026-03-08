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
            ->where('j.client', 6)
            ->where('j.source', 15)
            ->where('j.period', 1)
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
    $source = DB::table('source_code')->where('is_deleted', 0)->get();
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

                /* .source-info {
                                                        width: 300px !important;
                                                    } */
                /* .credit-debit-info {
                                                        padding-left: 100px !important;
                                                    }
                                                    .credit-debit-info-1 {
                                                        width: 170px !important;
                                                    }
                                                    .credit-debit-info-2 {
                                                        width: 170px !important;
                                                    } */
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
            .spinner-blue {
                color: #2485E8 !important;
                display: inline-block;
                animation: spin 1s linear infinite;
            }

            .spinner-red {
                color: #C41E3A !important;
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

            .second-header {
                background: #F8FAFC !important;
                border: 1px solid #7F7F7F;
            }

            .third-header {
                background: #ffffff !important;
                border-bottom: 1px solid #7F7F7F;
                border-right: 1px solid #7F7F7F;
            }

            .source-container {
                width: 100px;
                background: #FBFBFB;
                border: 1px solid #262626;
                color: #262626;
                border-radius: 5px;
                text-align: center;
                padding: 5px;
            }

            #btn-prev-account {
                left: 0;
                top: 4px;
            }

            #btn-next-account {
                top: 4px;
                right: 0;
            }

            #btn-prev-periods {
                left: 0;
                top: 4px;
            }

            #btn-next-periods {
                top: 4px;
                right: 0;
            }

            #btn-prev-periods img,
            #btn-next-periods img {
                transition: filter 0.3s ease-in-out;
            }

            #btn-prev-periods:hover img,
            #btn-next-periods:hover img {
                filter: drop-shadow(0px 0px 3pt rgba(127, 127, 127, 0.6));
            }

            #btn-prev-account img,
            #btn-next-account img {
                transition: filter 0.3s ease-in-out;
            }

            #btn-prev-account:hover img,
            #btn-next-account:hover img {
                filter: drop-shadow(0px 0px 3pt rgba(127, 127, 127, 0.6));
            }

            .second-header .btn {
                border: none;
                width: 25px;
                height: 25px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                transition: background 0.2s;
            }

            .second-header .btn:focus {
                box-shadow: none !important;
            }

            .custom-control-input:checked~.custom-control-label::before {
                background-color: #000000;
                border-color: #000000;
            }

            main .content {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            .journal-data-row {
                border: 1px solid lightgray;
                border-radius: 5px;
                color: #989898 !important;
            }

            .journal-data-row {
                transition: background-color 0.3s ease-in-out;
            }

            .journal-data-row:hover {
                background: #F9F9F9;
            }

            .selected-row {
                background-color: #F2F2F2 !important;
            }

            .action-buttons {
                width: 30px;
                border-radius: 1rem;
                opacity: 0;
            }

            .action-buttons:hover {
                background: #dadada;
            }

            .action-button {
                width: 30px;
                height: 30px;
                border-radius: 1rem;
            }

            .action-button:hover {
                background: #dadada;
            }

            .font-calibri {
                font-family: Calibri !important;
            }

            .source-container,
            .btn-print,
            .btn-export,
            #btn_batch_update,
            #btn_batch_delete {
                transition: box-shadow 0.3s ease-in-out;
            }

            .source-container:hover,
            .btn-print:hover,
            .btn-export:hover,
            #btn_batch_update:hover,
            #btn_batch_delete:hover {
                box-shadow: 0px 0px 4pt rgba(127, 127, 127, 0.6);
            }

            .btn-print,
            .btn-export,
            #btn_batch_update,
            #btn_batch_delete {
                background: #FBFBFB;
            }

            button:disabled {
                background-color: #FBFBFB !important;
                border: 1px solid #A6A6A6 !important;
                color: #D8D8D8 !important;
                cursor: not-allowed;
            }

            button:disabled img {
                opacity: 0.5;
            }

            .sortable {
                cursor: pointer;
            }

            .modal-content {
                box-shadow: none !important;
            }

            .data-container {
                user-select: none;
            }

            .sortable a:hover {
                text-decoration: underline !important;
            }

            .displayClient {
                line-height: 1 !important;
                height: 28px !important;
            }

            #btn_batch_update:disabled:hover,
            #btn_batch_delete:disabled:hover {
                box-shadow: none !important;
            }

            .alert-notify-desktop [data-notify="message"],
            .alert-info [data-notify="message"] {
                font-size: 14pt !important;
                font-family: Calibri !important;
            }
        </style>
        <!-- Page Content -->
        <div class="con   no-print page-header " id="JournalHeader">
            <!-- Full Table -->
            <div class="b mb-0">
                <div class="block-content pt-0 mt-0">
                    <div class="TopArea" style=" padding-top: 14px; z-index: 0; padding-bottom: 11px;">
                        <div class="row">
                            <div class="col-8 search-col row" style="position: relative;">
                                <div class="d-flex align-items-center" style="padding-left: 20px;">
                                    <img src="{{ asset('public/img/gl-menu-icons/enquiry-by-account-white.png') }}"
                                        style="position: absolute;left: 35px;top: -8px;width: 40px;height: 40px;">
                                    <h6 class="text-white mb-0"
                                        style="margin-left: 40px;margin-top: 3px;white-space: nowrap;margin-right: 15px;">
                                        Journal Enquiry
                                        by Account
                                    </h6>
                                </div>
                                <div class="d-flex align-items-center" style="position: absolute; right: 0px;">
                                    <div class="input-group" data-toggle="tooltip" data-custom-class="header-tooltip"
                                        data-trigger="hover" data-placement="bottom" title=""
                                        data-original-title="Current Client and Fiscal Year">
                                        <div type="text" class="form-control searchNew displayClient"
                                            style="width: 330px;border-right: transparent;white-space: nowrap;text-overflow: ellipsis;overflow: hidden;font-size: 0.9rem !important;"
                                            name="displayClient" id="displayClient">{{ $defaultClient }}</div>
                                        @if ($defaultFyear != 0)
                                            <div class="input-group-append"
                                                style="width: 60px; height: 28px !important; font-size: 0.9rem !important; font-weight: 400 !important;">
                                                <span class="input-group-text text-white displayYear">
                                                    {{ $defaultFyear }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <span id="btn_change_client" data-client="{{ $defaultClientId }}"
                                            class="topbar-change-client" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-custom-class="header-tooltip"
                                            data-original-title="Change Client">
                                            <img src="{{ asset('public/icons_2024_02_24/icon-change-client-white.png') }}"
                                                width="22px">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex text-right col-lg-4 justify-content-end pr-0">
                                @if (@Auth::user()->role == 'admin')
                                    <a href="javascript:;" data-toggle="tooltip" data-custom-class="header-tooltip"
                                        data-title="Settings" class="mr-1 text-dark headerSetting d3   "><img
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
            <div class="con no-print page-header second-header">
                <div class="b   mb-0  position-relative">
                    <div style="width: 10px;background: #595959;height: 62px;" class="position-absolute"></div>
                    <div class="block-content pt-0 mt-0">
                        <div class="TopArea" style="padding-top: 14px;z-index: 0;padding-bottom: 11px;">
                            <div class="container-fluid row px-0">
                                <div class="float-left search-col row d-flex flex-nowrap" style="width: 70%;">
                                    <div class="col-sm-3 d-flex align-items-center justify-content-end"
                                        style="position: relative;">
                                        <div class="mr-3">Source</div>
                                        <div class="source-container position-relative">
                                            <span id="account-text"> &nbsp; </span>
                                            <a id="btn-prev-account" class="position-absolute btn">
                                                <img src="{{ asset('public') }}/icons/left_arrow_clean.png" width="25"
                                                    alt="">
                                            </a>
                                            <a id="btn-next-account" class="position-absolute btn">
                                                <img src="{{ asset('public') }}/icons/right_arrow_clean.png" width="25"
                                                    alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 d-flex align-items-center justify-content-end"
                                        style="position: relative;">
                                        <div class="mr-3">Periods</div>
                                        <div class="source-container position-relative">
                                            <span class="period_text">1</span>
                                            <a id="btn-prev-periods" class="position-absolute btn">
                                                <img src="{{ asset('public') }}/icons/left_arrow_clean.png" width="25"
                                                    alt="">
                                            </a>
                                            <a id="btn-next-periods" class="position-absolute btn">
                                                <img src="{{ asset('public') }}/icons/right_arrow_clean.png" width="25"
                                                    alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <input type="hidden" name="client_fiscal_start" id="client_fiscal_start"
                                        value="{{ $clientFstart }}">
                                    <input type="hidden" name="client_id" id="client_id" value="{{ $defaultClientId }}">
                                    <input type="hidden" name="client_fyear" id="client_fyear"
                                        value="{{ $defaultFyear }}">
                                    <input type="hidden" name="period" id="period" value="1">
                                    <input type="hidden" name="journal_account" id="journal_account">
                                    <input type="hidden" name="source_text" id="source_text" value="CC-B">
                                    <button href="javascript:void();" id="btnPrint"
                                        class="text-dark btn-print ml-4 mr-3  d-flex align-items-center"
                                        style="padding: 0px 5px;border: 1px solid #262626;border-radius: 5px;width: 90px;text-align: left;">
                                        <img class="mr-2" src="{{ asset('public') }}\img\printer-black.png" width="20px"
                                            alt="">Print</button>
                                    <button href="javascript:void();" class="text-dark btn-export  d-flex align-items-center"
                                        style="padding: 0px 5px; border: 1px solid #262626; border-radius: 5px; width: 90px;">
                                        <img class="mr-2" src="{{ asset('public') }}\img\export-black.png" width="20px"
                                            alt="">Export</button>
                                </div>
                                <div class="float-left search-col row d-flex flex-nowrap justify-content-end"
                                    style="width: 30%;">
                                    <button href="javascript:void();" id="btn_batch_update"
                                        class="text-dark mr-3 d-flex align-items-center"
                                        style="padding: 0px 5px; border: 1px solid #262626; border-radius: 5px;"
                                        disabled="true"><img class="mr-2" src="{{ asset('public') }}\img\batch-icon.png"
                                            width="24px" alt="">Batch
                                        Update</button>
                                    <button href="javascript:void();" id="btn_batch_delete"
                                        class="text-dark d-flex align-items-center"
                                        style="padding: 0px 5px; border: 1px solid #262626; border-radius: 5px;"
                                        disabled="true"><img class="ml-1 mr-2"
                                            src="{{ asset('public') }}\img\delete-all.png" width="14px"
                                            alt="">Delete
                                        All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="con page-header third-header column-heading">
                <div class="b mb-0 d-flex">
                    <div style="width: 10px; background: #fff;"></div>
                    <div class="block-content pt-0 mt-0">
                        <div class="TopArea" style="padding-top: 11px;z-index: 0;padding-bottom: 9px;">
                            <div class="container-fluid row px-0">
                                <div class="float-left search-col row d-flex flex-nowrap align-items-center"
                                    style="width: 100%;">
                                    <div class="custom-control custom-  custom-control-  custom-control-lg mr-3 no-print"
                                        style="padding-left: 45px;">
                                        <input type="checkbox" class="custom-control-input" id="select_all"
                                            name="select_all" applied-to-tax1="1">
                                        <label class="custom-control-label" for="select_all">
                                        </label>
                                    </div>
                                    <div class="text-center mr-2 px-2 sortable" data-column="editNo" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 80px; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: underline;color: #595959 !important; font-family: Signika !important;">Edit
                                            No</a>
                                    </div>
                                    <div class="text-center mr-2 px-2 sortable" data-column="date" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 100px; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">Date</a>
                                    </div>
                                    <div class="text-center mr-2 px-2 sortable" data-column="account_no" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 70px; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">Source</a>
                                    </div>
                                    <div class="text-center mr-2 px-2 sortable" data-column="ref_no" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 100px; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">RefNo</a>
                                    </div>
                                    <div class="text-right mr-2 px-2 sortable" data-column="debit" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 140px; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">Debit</a>
                                    </div>
                                    <div class="text-right mr-2 px-2 sortable" data-column="credit" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 140px; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">Credit</a>
                                    </div>
                                    <div class="text-left mr-2 px-2 sortable" data-column="description" data-order="asc"
                                        style="background: #f2f2f2; padding-top: 7px; padding-bottom: 7px; border-radius: 10px; width: 40%; border: 1px solid #ECEFF4;">
                                        <a href="javascript:void();"
                                            style="text-decoration: none;color: #595959 !important; font-family: Signika !important;">Description</a>
                                    </div>
                                    <div class="text-left mr-2 px-2 no-print" style="width: 206px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content  " id="contentDiv" style="overflow-y: auto;">
                <!-- Page Content -->
            </div>
            @php
                $all_active_clients = DB::table('clients')
                    ->where('is_deleted', 0)
                    ->where('client_status', 1)
                    ->orderBy('display_name', 'asc')
                    ->get();
            @endphp
            <div class="modal fade" id="JournalFYReIndexModal" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header pb-0  ">
                                <span class="b e section-header">Change Client/Year</span>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
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
                                        <select type="" name="change_client" id="change_client"
                                            class="form-control select2" placeholder="">
                                            <option value="" selected>Select Client</option>
                                            @foreach ($all_active_clients as $c)
                                                <option value="{{ $c->id }}" fiscal-start={{ $c->fiscal_start }}>
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
                                        <select class="form-control select2" id="change_fiscal_year"
                                            name="change_fiscal_year">
                                            <option value="" selected>Select Fiscal</option>
                                        </select>
                                    </div>
                                </div>
                                <p class=" found-journals pl-5 mb-0" style="font-family: Signika;"></p>
                            </div>
                            <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">
                                <button type="button" class="btn btn-new btn-select-client">Select</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deleteJoural" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered  modal-sm modal-bac" style="min-width: 340px;"
                    role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header pb-0  ">
                                <span class="b e section-header font-calibri">Delete Journal</span>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content new-block-content pt-0 pb-0 ">
                                <p class=" found-journals mb-0" style="font-family: Signika;">Are you sure you wish to
                                    delete this
                                    journal entry <span class="deleteEditNo"></span>?</p>
                            </div>
                            <div class="block-content block-content-full pt-4 px-5"
                                style=" display: flex; justify-content: space-around;">
                                <button type="button" class="btn btn-new font-calibri confirm-delete">Yes</button>
                                <button type="button" class="btn btn-new font-calibri" data-dismiss="modal"
                                    style="color: #C41E3A;">No</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form class="mb-0 pb-0 form-journals" id="form-edit-client-journal" method="post">
                @csrf
                <div class="modal fade" id="EditClientJournalModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document"
                        style="width: 600px;">
                        <div class="modal-content">
                            <div class="block  block-transparent mb-0 position-relative"
                                style="padding-top: 24px !important;">
                                <div class="position-absolute"
                                    style="height: 60px;width: 104%;top: 0;left: -2%;display: flex;justify-content: space-between;align-items: center;background: white;padding: 10px;border: 1px solid black;border-radius: 10px; box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.4);">
                                    <div style="line-height: 1;">
                                        <div class="b e section-header" style="font-size: 20pt;">#<span
                                                id="edit_no"></span></div>
                                        <small class="ml-1" style="font-size: 60%;">EDITNO</small>
                                    </div>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option" data-dismiss="modal">
                                            <i class="fa fa-fw fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content new-block-content pt-0 pb-0 " style="margin-top: 30px;">
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
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
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
            <form class="mb-0 pb-0 " id="form-journal-batch-update" action="" method="POST">
                @csrf
                <div class="modal fade" id="JournalBatchUpdateModal" tabindex="-1" role="dialog" data-backdrop="static"
                    aria-labelledby="modal-block-large" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac " role="document"
                        style="width: 600px;">
                        <div class="modal-content">
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
                                    {{-- <h5 class="text-info total-selected-batch" style="font-family: Signika;">0 journals
                                    selected</h5> --}}
                                    {{-- <div class="row justify-content- form-group  push">
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
                                </div> --}}
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
                                            <select class="form-control" id="batch_update_month" name="batch_update_month">
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
                                            <select class="form-control" id="batch_update_period" name="batch_update_period">
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
                                            <select class="form-control" id="batch_update_source" name="batch_update_source">
                                                <option value="" selected>No change</option>
                                                @foreach ($sources as $sc)
                                                    <option value="{{ $sc->id }}">{{ $sc->source_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group  form-group-edit">
                                        <div class="col-sm-3">
                                            <label class="col-form-label">Set Account</label>
                                        </div>
                                        <div class="col-sm-6 ">
                                            <input type="" class="form-control" id="batch_update_account"
                                                name="batch_update_account" placeholder="0000"
                                                list="dt_account_description_list" maxlength="4">
                                            <?php
                                            $accounts_list = DB::table('gifi')->where('is_deleted', 0)->distinct('account_no')->limit(100)->pluck('account_no')->toArray();
                                            ?>
                                            <datalist id="dt_account_description_list">
                                                @foreach ($accounts_list as $l)
                                                    <option value="{{ $l }}">
                                                @endforeach
                                            </datalist>
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
            <div class="modal fade" id="BatchJournalUpdatesConfirmationModal" tabindex="-1" role="dialog"
                data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header pb-0  ">
                                <span class="b e section-header">Batch Journal Updates - Confirmation</span>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
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
            <div class="modal fade" id="BatchJournalDeleteModal" tabindex="-1" role="dialog" data-backdrop="static"
                aria-labelledby="modal-block-large" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-bac" style="max-width: 350px;" role="document">
                    <div class="modal-content">
                        <div class="block  block-transparent mb-0">
                            <div class="block-header pb-0  ">
                                <span class="b e section-header">Delete Journals</span>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content new-block-content pt-0 pb-0 confirmation-container">
                                <h5 class="text-info total-selected-batch-delete text-center mb-2"
                                    style="font-family: Signika;">0
                                    journals
                                    selected</h5>
                                <p class=" found-journals mb-0" style="font-family: Signika;">Enter the number of journals
                                    selected
                                    below and click on Yes to confirm deleting
                                    all selected journals.</p>
                                <div class="pt-2" style="display: flex; justify-content: center">
                                    <input type="number" class="form-control" style="max-width: 120px;" name="delete_count"
                                        id="delete_count">
                                </div>
                            </div>
                            <div class="block-content block-content-full pt-2 px-5"
                                style=" display: flex; justify-content: space-around;">
                                <button type="button" class="btn btn-new font-calibri confirm-batch-delete">Yes</button>
                                <button type="button" class="btn btn-new font-calibri" data-dismiss="modal"
                                    style="color: #C41E3A;">No</button>
                            </div>
                        </div>
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
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content new-block-content pt-0 pb-0 ">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <p>Would you like to export ALL periods for the current Account?</p>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="block-content block-content-full   pt-4" style="padding-left: 9mm;padding-right: 9mm">
                                <a href="javascript:void();" class="btn mr-3 btn-new  btn-export-confirm">Ok</a>
                                <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>
                            </div> --}}
                            <div class="block-content block-content-full pt-4 px-5"
                                style=" display: flex; justify-content: space-around;">
                                <button type="button" class="btn btn-new font-calibri btn-export-confirm">Yes</button>
                                <button type="button" class="btn btn-new font-calibri btn-export-confirm-current"
                                    style="color: #C41E3A;">No</button>
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
        $(document).ready(function() {
            $('[data-toggle=tooltip]').tooltip();
            var storedClientId = localStorage.getItem('selected_client_id');
            var storedClientText = localStorage.getItem('selected_client_text');
            var storedFiscalYear = localStorage.getItem('selected_fiscal_year');
            var storedFiscalStart = localStorage.getItem('selected_fiscal_start');
            if (storedClientId && storedFiscalYear) {
                $('select[name=change_client]').val(storedClientId);
                $('#change_fiscal_year').val(storedFiscalYear);
                $('#displayClient').text(storedClientText);
                $('.displayYear').text(storedFiscalYear);
                $('#client_id').val(storedClientId);
                $('#client_fiscal_start').val(storedFiscalStart);
                $('#client_fyear').val(storedFiscalYear);
                $('#btn_change_client').attr('data-client', storedClientId);
            }
            document.getElementById('btnPrint').addEventListener('click', function() {
                var heading = document.querySelector('.column-heading').cloneNode(true);
                var printContainer = document.querySelector('.data-container').cloneNode(true);
                var printContainer2 = document.querySelector('.data-container-2').cloneNode(true);
                // Remove elements with the class "no-print"
                heading.querySelectorAll('.no-print').forEach(el => el.remove());
                printContainer.querySelectorAll('.no-print').forEach(el => el.remove());
                printContainer2.querySelectorAll('.no-print').forEach(el => el.remove());
                var originalContents = document.body.innerHTML;
                // heading.style.paddingLeft = '20px';
                document.body.innerHTML = `
            <html>
                <head>
                    <title>Print</title>
                    <style>
                        body { font-family: Calibri, sans-serif; }
                        .journal-data-row {
                            border-bottom: 1px solid #ccc;
                            padding: 10px;
                        }
                        .journal-data-row:last-child {
                            border-bottom: none;
                        }
                        .source-info {
                    width: 400px !important;
                }
                    </style>
                </head>
                <body>
                    ${heading.innerHTML}
                    ${printContainer.innerHTML}
                    ${printContainer2.innerHTML}
                </body>
            </html>
        `;
                window.print();
                document.body.innerHTML = originalContents;
                window.location.reload();
            });
            var sort_column = "edit_no";
            var sort_order = "asc";
            $(".sortable").click(function() {
                sort_column = $(this).data("column");
                sort_order = $(this).data("order");
                sort_order = sort_order === "asc" ? "desc" : "asc";
                $(".sortable").data("order", "asc");
                $(this).data("order", sort_order);
                $(".sortable a").css("text-decoration", "none");
                $(this).find("a").css("text-decoration", "underline");
                getContent();
            });
            let batch_journals = [];
            let batch_journals_delete = [];
            $(document).on('click', '#btn_batch_update', function() {
                batch_journals = [];
                $(".journal-data-row").each(function() {
                    if ($(this).hasClass('selected-row')) {
                        batch_journals.push($(this).attr('data'));
                    }
                });
                if (batch_journals.length == 0) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
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
            $(document).on('click', '#btn_batch_delete', function() {
                batch_journals_delete = [];
                $(".journal-data-row").each(function() {
                    if ($(this).hasClass('selected-row')) {
                        batch_journals_delete.push($(this).attr('data'));
                    }
                });
                if (batch_journals_delete.length == 0) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please select journals to Delete.',
                        delay: 5000
                    });
                } else {
                    $(".total-selected-batch-delete").html(batch_journals_delete.length +
                        " journals selected");
                    $("#BatchJournalDeleteModal").modal('show');
                }
            });
            // $(document).on('click', '.confirm-batch-delete', function() {
            //     var delete_count = $('#delete_count').val();
            //     if (batch_journals_delete.length == delete_count) {
            //         $.ajax({
            //             type: "POST",
            //             url: "{{ url('/journals/batch-delete') }}",
            //             data: {
            //                 "_token": "{{ csrf_token() }}",
            //                 "journals": JSON.stringify(batch_journals_delete)
            //             }
            //         }).done(function(response) {
            //             getContent();
            //             batch_journals_delete = [];
            //             $(".journal-data-row").removeClass('selected-row');
            //             $(".row-checkbox").prop('checked', false);
            //             $('.batch-selection').attr('data-selected', 0);
            //             $("#BatchJournalDeleteModal").modal('hide');
            //             Dashmix.helpers('notify', {
            // type: 'info alert-notify-desktop',
            //                 from: 'bottom',
            //                 align: 'left',
            //                 message: response + ' journals deleted',
            //                 delay: 5000
            //             });
            //         });
            //     } else {
            //         Dashmix.helpers('notify', {
            // type: 'info alert-notify-desktop',
            //             from: 'bottom',
            //             align: 'left',
            //             message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Delete count dont match.',
            //             delay: 5000
            //         });
            //     }
            // });
            $(document).on('click', '.confirm-batch-delete', function() {
                var delete_count = $('#delete_count').val();
                if (batch_journals_delete.length == delete_count) {
                    let deletedJournals = [...batch_journals_delete]; // Store deleted journals for undo
                    let undoTimeout;
                    const undoBatchDelete = function() {
                        clearTimeout(undoTimeout);
                        $.ajax({
                            type: "POST",
                            url: "{{ url('/journals/undo-batch-delete') }}",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "journals": JSON.stringify(deletedJournals)
                            }
                        }).done(function(response) {
                            getContent();
                            $(".journal-data-row").removeClass('selected-row');
                            $(".row-checkbox").prop('checked', false);
                            $('.batch-selection').attr('data-selected', 0);
                            $("#BatchJournalDeleteModal").modal('hide');
                            Dashmix.helpers('notify', {
                                type: 'info alert-notify-desktop',
                                from: 'bottom',
                                align: 'left',
                                type: 'success',
                                message: 'Batch delete undone',
                                delay: 1000
                            });
                        });
                    };
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/journals/batch-delete') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "journals": JSON.stringify(batch_journals_delete)
                        }
                    }).done(function(response) {
                        $("#select_all").prop('checked', false);
                        $("#btn_batch_update").prop('disabled', true);
                        $("#btn_batch_delete").prop('disabled', true);
                        $("#delete_count").val('');
                        getContent();
                        $(".journal-data-row").removeClass('selected-row');
                        $(".row-checkbox").prop('checked', false);
                        $('.batch-selection').attr('data-selected', 0);
                        $("#BatchJournalDeleteModal").modal('hide');
                        const notify = Dashmix.helpers('notify', {
                            type: 'info alert-notify-desktop',
                            from: 'bottom',
                            align: 'left',
                            message: `${response} journals deleted <a href="javascript:;" id="undo-batch-delete" class="btn-notify ml-4">UNDO</a>`,
                            delay: 4000
                        });
                        undoTimeout = setTimeout(function() {
                            batch_journals_delete = [];
                        }, 4500);
                        $(document).on('click', '#undo-batch-delete', function() {
                            undoBatchDelete();
                            notify.close();
                        });
                    });
                } else {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Delete count doesn’t match.',
                        delay: 5000
                    });
                }
            });
            $("#form-journal-batch-update").on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const client = $("#form-journal-batch-update select[name=batch_update_client]"),
                    month = $("#form-journal-batch-update select[name=batch_update_month]"),
                    year = $("#form-journal-batch-update select[name=batch_update_year]"),
                    period = $("#form-journal-batch-update select[name=batch_update_period]"),
                    fyear = $("#form-journal-batch-update select[name=batch_update_fiscal_year]"),
                    source = $("#form-journal-batch-update select[name=batch_update_source]"),
                    account = $("#form-journal-batch-update input[name=batch_update_account]");
                if (client.val() == "" && month.val() == "" && year.val() == "" && period.val() == "" &&
                    fyear.val() == "" && source.val() == "" && account.val() == "") {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
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
                    if (account.val() != "") {
                        html += `<div class="row form-group  ">
                            <div class="col-sm-3">
                                <label class="col-form-label ">Account</label>
                            </div>
                            <div class="col-sm-6 ">
                                <label class="col-form-label text-danger">${account.val()}</label>
                            </div>
                        </div>`;
                    }
                    $("#JournalBatchUpdateModal").modal('hide');
                    $("#BatchJournalUpdatesConfirmationModal").find('.confirmation-container').html(html);
                    $("#BatchJournalUpdatesConfirmationModal").modal('show');
                }
            });
            $("#proceed-batch-update").on('click', function() {
                const client = $("#form-journal-batch-update select[name=batch_update_client]").val(),
                    month = $("#form-journal-batch-update select[name=batch_update_month]").val(),
                    year = $("#form-journal-batch-update select[name=batch_update_year]").val(),
                    period = $("#form-journal-batch-update select[name=batch_update_period]").val(),
                    fyear = $("#form-journal-batch-update select[name=batch_update_fiscal_year]").val(),
                    source = $("#form-journal-batch-update select[name=batch_update_source]").val(),
                    account = $("#form-journal-batch-update input[name=batch_update_account]").val();
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
                            "account_no": account,
                            "journals": JSON.stringify(batch_journals),
                        }
                    }).done(function(response) {
                        getContent();
                        batch_journals = [];
                        $(".journal-data-row").removeClass('selected-row');
                        $(".row-checkbox").prop('checked', false);
                        $('.batch-selection').attr('data-selected', 0);
                        $("#BatchJournalUpdatesConfirmationModal").modal('hide');
                        Dashmix.helpers('notify', {
                            type: 'info alert-notify-desktop',
                            from: 'bottom',
                            align: 'left',
                            message: 'Undo Batch Update Successfully',
                            delay: 5000
                        });
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
                        "account_no": account,
                        "journals": JSON.stringify(batch_journals),
                    }
                }).done(function(response) {
                    $("#select_all").prop('checked', false);
                    $("#btn_batch_update").prop('disabled', true);
                    $("#btn_batch_delete").prop('disabled', true);
                    $("#batch_update_account").val('');
                    $("#batch_update_client").val('');
                    $("#batch_update_month").val('');
                    $("#batch_update_year").val('');
                    $("#batch_update_period").val('');
                    $("#batch_update_fiscal_year").val('');
                    $("#batch_update_source").val('');
                    getContent();
                    batch_journals = [];
                    $(".journal-data-row").removeClass('selected-row');
                    $(".row-checkbox").prop('checked', false);
                    $('.batch-selection').attr('data-selected', 0);
                    $("#BatchJournalUpdatesConfirmationModal").modal('hide');
                    const notify = Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: `${response} journals updated <a href="javascript:;" id="undo-batch-update" class="btn-notify ml-4">UNDO</a>`,
                        delay: 5000
                    });
                    $('#undo-batch-update').on('click', function() {
                        // notify.close();
                        undoBatchUpdate();
                    });
                });
            });
            $(document).on('submit', '#form-edit-client-journal', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                const client = $(".form-journals select[name=pp_client_edit]").val();
                const year = $(".form-journals select[name=pp_year_edit]").val();
                const month = $(".form-journals input[name=pp_month_edit]").val();
                const period = $(".form-journals select[name=pp_period_edit]").val();
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
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for client.',
                        delay: 5000
                    });
                    return false;
                } else if (year == '' || year == null || year == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for year.',
                        delay: 5000
                    });
                    return false;
                } else if (month == '' || month == null || month == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a value for month.',
                        delay: 5000
                    });
                    return false;
                } else if (period == '' || period == null || period == undefined || period == "NaN") {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',
                        delay: 5000
                    });
                    return false;
                } else if (fyear == '' || fyear == null || fyear == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',
                        delay: 5000
                    });
                    return false;
                } else if (!validateAccNo(account_no) || account_no == '' || account_no == null ||
                    account_no == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter a valid value for account.',
                        delay: 5000
                    });
                    return false;
                } else if (account_valid == '0') {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Account no ' +
                            account_no + ' not found.',
                        delay: 5000
                    });
                    return false;
                } else if (source == '' || source == null || source == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for source.',
                        delay: 5000
                    });
                    return false;
                } else if (ref == '' || ref == null || ref == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for ref.',
                        delay: 5000
                    });
                    return false;
                } else if (date == '' || date == null || date == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for date.',
                        delay: 5000
                    });
                    return false;
                } else if (translation == '' || translation == null || translation == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Failed Validation.',
                        delay: 5000
                    });
                    return false;
                } else if (description == '' || description == null || description == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for description.',
                        delay: 5000
                    });
                    return false;
                } else if ((debit == '' && credit == '') || debit == null || credit == null || debit ==
                    NaN || credit == NaN || debit == undefined || credit == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for debit or credit.',
                        delay: 5000
                    });
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
                            $("#EditClientJournalModal").modal('hide');
                            getContent();
                            if (source != '' || source != null || source != undefined) {
                                loadAccounts();
                            }
                            Dashmix.helpers('notify', {
                                type: 'info alert-notify-desktop',
                                from: 'bottom',
                                align: 'left',
                                message: 'Journal successfully saved',
                                delay: 5000
                            });
                        }
                    })
                }
            });
            $(document).on('click', '.view-accounts-chart-edit-1', function() {
                var client_id = $(this).attr('client-id');
                getClientGifi(client_id, "dt_account_description_list2");
                $("#AccountChartModal").modal('show');
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
                                    style="cursor:pointer;" account-no="${item['account_no']}">
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
                    $("#EditClientJournalModal input[name=dt_account_edit]").val(account_no);
                }
                $("#select-account-no-block").addClass('d-none');
                $("#AccountChartModal").modal('hide');
            });
            $(document).on('input', '#EditClientJournalModal input[name=dt_account_edit]', function() {
                var account_no = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(account_no);
            });

            function validateAccNo(val) {
                return /^\d{4}$/.test(val)
            }
            $(document).on('change', '#EditClientJournalModal input[name=dt_account_edit]', function() {
                var account_no = $(this).val();
                if (/^\d{3}$/.test(account_no)) {
                    account_no += '0';
                }
                $(this).val(account_no);
                const $this = $(this);
                const $descEl = $("#EditClientJournalModal .dt-account-description-edit");
                if (account_no != '') {
                    if (validateAccNo(account_no)) {
                        const client_id = $('#client_id').val();
                        const fyear = $('#EditClientJournalModal input[name=pp_year_edit]').val();
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

            function monthToStringShort(month) {
                var months = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];
                return months[month - 1];
            }
            $(document).on('change', '#EditClientJournalModal input[name=dt_source_code_edit_]', function() {
                var dt_source_code = $(this).val();
                const $descEl = $("#EditClientJournalModal .dt-source-description-edit");
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
                                type: 'info alert-notify-desktop',
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
                            $('#EditClientJournalModal #dt_source_code_edit').val(res.id);
                        }
                    });
                } else {
                    $('#EditClientJournalModal #dt_source_code_edit').val('');
                    $(this).focus();
                    $descEl.html("");
                }
                // $(".form-journals .dt-source-description").html(
                //     `<div class="dt-account-description-tag">${description}</div>`);
            });
            $(document).on('change', '#EditClientJournalModal input[name=dt_date_edit]', function() {
                var value = $(this).val().trim();
                var month = $('#EditClientJournalModal input[name=pp_month_edit]').val();
                var year = $('#EditClientJournalModal select[name=pp_year_edit]').val();
                // Validate the input value
                var isValid = validateInputValue(value, year + "-" + month + "-01", month, year);
                if (!isValid) {
                    value = '';
                } else {
                    value = isValid;
                }
                $(this).val(value);
                $("#EditClientJournalModal input[name=translation_edit]").val(translatedDate(value, month,
                    year));
                $("#EditClientJournalModal .translation-edit").html(
                    `${translatedDate(value, month, year)}`);
            });

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
            $(document).on('change', 'select[name=pp_year_edit]', function() {
                let fiscal_start = $('#client_fiscal_start').val();
                var month = parseInt($(`select[name=pp_month_edit]`).val());
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
                $(".pp_fyear_edit").html(fiscal_end);
            });
            $(document).on('change', '#EditClientJournalModal select[name=pp_period_edit]', function() {
                let fiscal_start = $('#client_fiscal_start').val();
                var month = parseInt($(this).val());
                var year = $(`#EditClientJournalModal select[name=pp_year_edit]`).val();
                if (month < 10) {
                    month = '0' + month;
                }
                const fs = fiscal_start;
                fiscal_start = fiscal_start.split('-');
                const fyear = fiscal_start[0];
                const fmonth = fiscal_start[1];
                // var period = findPeriod(fs, fyear + "-" + month + "-01");
                var period = parseInt($(this).val());
                let calculatedMonth = parseInt(fmonth) + (period - 1);
                let adjustedYear = fyear;
                if (calculatedMonth > 12) {
                    calculatedMonth -= 12;
                    adjustedYear++;
                }
                let formattedMonth = calculatedMonth < 10 ? '0' + calculatedMonth : calculatedMonth;
                let dateObj = new Date(adjustedYear, calculatedMonth - 1);
                let monthName = dateObj.toLocaleString('en-US', {
                    month: 'long'
                });
                // var fiscal_end = getFiscalYearEnd(parseInt(fmonth), parseInt((period.trim()).slice(-2)),
                //     parseInt(month),
                //     parseInt(year));
                var fiscal_end = getFiscalYearEnd(parseInt(fmonth), period, parseInt(month), parseInt(
                    year));
                $("#EditClientJournalModal .pp_period_edit").html(monthName);
                $("#EditClientJournalModal input[name=pp_month_edit]").val(formattedMonth);
                $("#EditClientJournalModal input[name=pp_fyear_edit]").val(parseInt(fiscal_end));
                $("#EditClientJournalModal .pp_fyear_edit").html(parseInt(fiscal_end));
                const dt_date = $('#EditClientJournalModal input[name=dt_date_edit]').val();
                // $('#EditClientJournalModal input[name=dt_date_edit]').val(dt_date[0] + dt_date[1]);
                // $('#EditClientJournalModal input[name=dt_date_edit]').trigger('change');
            });
            $(document).on('click', '.btn-edit', function() {
                const journal_id = $(this).attr('data-id'),
                    client_id = $(this).attr('data-client-id'),
                    editNo = $(this).attr('data-editno');
                $.ajax({
                    type: "GET",
                    url: "{{ url('/journals/find/get-client-journal-edit-content-new') }}/" +
                        journal_id,
                }).done(function(response) {
                    $("#EditClientJournalModal .new-block-content").html(response);
                    $("#EditClientJournalModal #edit_no").html(response);
                    $('.tooltip').tooltip('hide');
                    $('[data-toggle=tooltip]').tooltip();
                    $("#EditClientJournalModal #edit_no").html(editNo);
                    $("#EditClientJournalModal").modal("show");
                });
            });
            $(document).on('change', '#JournalFYReIndexModal select[name=change_client]', function() {
                const client = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "{{ url('/journals/client/get-fyears') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "client": client,
                    }
                }).done(function(response) {
                    $("#JournalFYReIndexModal select[name=change_client]").select2(
                        'destroy');
                    $("#JournalFYReIndexModal select[name=change_fiscal_year]").html(
                        `<option value="" selected>Select Fiscal</option>`);
                    if (response.length > 0) {
                        response.forEach((ele) => {
                            $("#JournalFYReIndexModal select[name=change_fiscal_year]")
                                .append(`<option value="${ele}">${ele}</option>`);
                        });
                    }
                    $("#JournalFYReIndexModal select[name=change_client]").select2();
                });
            });
            $('#btn_change_client').on('click', function() {
                var client = $(this).attr('data-client');
                if (client && client != "" && client != undefined) {
                    $("#JournalFYReIndexModal select[name=change_client]").select2('destroy');
                    $("#JournalFYReIndexModal select[name=change_client]").val(client);
                    $("#JournalFYReIndexModal select[name=change_client]").select2();
                }
                $('#JournalFYReIndexModal select[name=change_client]').trigger('change');
                $("#JournalFYReIndexModal").modal('show');
            })
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).attr('data-id');
                var editNo = $(this).attr('data-editNo');
                var place = $(this).attr('data-place');
                if (id && editNo) {
                    $('#deleteJoural .deleteEditNo').text(editNo);
                    $('#deleteJoural .confirm-delete').attr('data-id', id);
                    $('#deleteJoural .confirm-delete').attr('data-place', place);
                    if (place == 1) {
                        $('#EditClientJournalModal').modal('hide');
                    }
                    $('#deleteJoural').modal('show');
                }
            })
            $(document).on('click', '.confirm-delete', function() {
                var id = $(this).attr('data-id');
                var place = $(this).attr('data-place');
                $.ajax({
                    type: 'Post',
                    url: '{{ url('delete-journl') }}',
                    data: {
                        journalId: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $(this).prop('disabled', true);
                    },
                    success: function(res) {
                        $(this).prop('disabled', false);
                        Dashmix.helpers('notify', {
                            type: 'info alert-notify-desktop',
                            from: 'bottom',
                            align: 'left',
                            message: "Journal deleted." +
                                ' <a href="javascript:;" data="' + id +
                                '" data-notify="dismiss" class="  btn-notify undo-delete-journal ml-4" >Undo</a>',
                            delay: 5000,
                            type: 'info alert-notify-desktop'
                        });
                        $('#deleteJoural').modal('hide');
                        if (place == 1) {
                            $('#EditClientJournalModal').modal('hide');
                        }
                        getContent();
                    },
                    error: function() {
                        $(this).prop('disabled', false);
                    }
                })
            })
            $(document).on('click', '.undo-delete-journal', function() {
                var id = $(this).attr('data');
                $.ajax({
                    type: 'Post',
                    url: '{{ url('undo-delete-journl') }}',
                    data: {
                        journalId: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(res) {
                        getContent();
                    },
                    error: function() {}
                })
            })
            $('.btn-select-client').on('click', function() {
                var change_client = $('select[name=change_client]').val();
                var change_client_text = $('#change_client option:selected').text();
                var client_fiscal_start = $('#change_client option:selected').attr('fiscal-start');
                var change_fiscal_year = $('#change_fiscal_year').val();
                if (change_client == '' || change_client == null || change_client == undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for client.',
                        delay: 5000
                    });
                    return false;
                } else if (change_fiscal_year == '' || change_fiscal_year == null || change_fiscal_year ==
                    undefined) {
                    Dashmix.helpers('notify', {
                        type: 'info alert-notify-desktop',
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for year.',
                        delay: 5000
                    });
                    return false;
                }
                $('#displayClient').text("");
                $('#displayClient').text(change_client_text);
                $('.displayYear').text(change_fiscal_year);
                $('#client_id').val(change_client);
                $('#client_fiscal_start').val(client_fiscal_start);
                $('#client_fyear').val(change_fiscal_year);
                $('#btn_change_client').attr('data-client', change_client);
                localStorage.setItem('selected_client_id', change_client);
                localStorage.setItem('selected_client_text', change_client_text);
                localStorage.setItem('selected_fiscal_year', change_fiscal_year);
                localStorage.setItem('selected_fiscal_start', client_fiscal_start);
                getContent();
                loadAccounts();
                $("#JournalFYReIndexModal").modal('hide');
            })
            $('#select_all').on('change', function() {
                if ($('#select_all').prop('checked') == true) {
                    $('input[name=select_row]').prop('checked', true);
                    $('#btn_batch_update').prop('disabled', false);
                    $('#btn_batch_delete').prop('disabled', false);
                    $('.journal-data-row').addClass('selected-row');
                } else {
                    $('input[name=select_row]').prop('checked', false);
                    $('#btn_batch_update').prop('disabled', true);
                    $('#btn_batch_delete').prop('disabled', true);
                    $('.journal-data-row').removeClass('selected-row');
                }
            })
            $(document).on('mouseenter', '.journal-data-row', function() {
                $(this).find('.action-buttons').css('opacity', 1);
            }).on('mouseleave', '.journal-data-row', function() {
                $(this).find('.action-buttons').css('opacity', 0);
            });
            $('#btn-next-periods').on('click', function() {
                var period = $('#period').val();
                if (period == 12) {
                    $('#period').val(1);
                    $('.period_text').text(1);
                } else {
                    period++;
                    $('#period').val(period);
                    $('.period_text').text(period);
                }
                $("#select_all").prop('checked', false);
                $("#btn_batch_update").prop('disabled', true);
                $("#btn_batch_delete").prop('disabled', true);
                getContent();
            })
            $('#btn-prev-periods').on('click', function() {
                var period = $('#period').val();
                if (period == 1) {
                    $('#period').val(12);
                    $('.period_text').text(12);
                } else {
                    period--;
                    $('#period').val(period);
                    $('.period_text').text(period);
                }
                $("#select_all").prop('checked', false);
                $("#btn_batch_update").prop('disabled', true);
                $("#btn_batch_delete").prop('disabled', true);
                getContent();
            })
            $(document).on('change', '.row-checkbox', function() {
                $(this).closest('.journal-data-row').toggleClass('selected-row', this.checked);
                let selectedCount = $('.row-checkbox:checked').length;
                if (selectedCount > 1) {
                    $('#btn_batch_update').prop('disabled', false);
                    $('#btn_batch_delete').prop('disabled', false);
                } else {
                    $('#btn_batch_update').prop('disabled', true);
                    $('#btn_batch_delete').prop('disabled', true);
                }
            });
            // getContent();
            $(document).on('click', '.btn-export', function() {
                $('#ExportModalNew').modal('show');
            })
            $(document).on('click', '.btn-export-confirm-current', function() {
                var client_id = $('#client_id').val();
                var client_fyear = $('#client_fyear').val();
                var period = $('#period').val();
                var account = $('#journal_account').val();
                var source_text = $('#source_text').val();
                var url = '{{ url('journal-by-account-export') }}';
                if (client_id && client_fyear && period && account && source_text) {
                    var $btn = $(this);
                    var originalContent = $btn.html();
                    if ($btn.hasClass('disabled')) {
                        return;
                    }
                    $btn.addClass('expanded disabled');
                    $btn.html(
                        '<i class="fa fa-cog spinner-red text-white"></i> Exporting...'
                    );
                    setTimeout(() => {
                        $.ajax({
                            type: 'get',
                            data: {
                                client_id: client_id,
                                client_fyear: client_fyear,
                                period: period,
                                account: account,
                                source_text: source_text,
                                sort_column: sort_column,
                                sort_order: sort_order,
                            },
                            url: url,
                            dataType: 'json',
                            beforeSend() {
                                Dashmix.layout('header_loader_on');
                            },
                            success: function(res) {
                                $btn.html(originalContent);
                                $btn.removeClass('expanded disabled');
                                $('#ExportModalNew').modal('hide');
                                if (res.success) {
                                    window.location.href = res.download_url;
                                }
                            },
                            error: function(xhr) {
                                $('#ExportModalNew').modal('hide');
                                $btn.html(originalContent);
                                $btn.removeClass('expanded disabled');
                                console.log("Error:", xhr.responseText);
                            }
                        })
                    }, 100);
                }
            })
            $(document).on('click', '.btn-export-confirm', function() {
                var client_id = $('#client_id').val();
                var client_fyear = $('#client_fyear').val();
                var account = $('#journal_account').val();
                var source_text = $('#source_text').val();
                var period = $('#period').val();
                var url = '{{ url('journal-by-account-export') }}';
                if (client_id && client_fyear && account && source_text) {
                    var $btn = $(this);
                    var originalContent = $btn.html();
                    if ($btn.hasClass('disabled')) {
                        return;
                    }
                    $btn.addClass('expanded disabled');
                    $btn.html(
                        '<i class="fa fa-cog spinner-blue text-white"></i> Exporting...'
                    );
                    setTimeout(() => {
                        $.ajax({
                            type: 'get',
                            data: {
                                client_id: client_id,
                                client_fyear: client_fyear,
                                period: period,
                                source_text: source_text,
                                sort_column: sort_column,
                                sort_order: sort_order,
                            },
                            url: url,
                            dataType: 'json',
                            beforeSend() {
                                Dashmix.layout('header_loader_on');
                            },
                            success: function(res) {
                                $btn.html(originalContent);
                                $btn.removeClass('expanded disabled');
                                $('#ExportModalNew').modal('hide');
                                if (res.success) {
                                    window.location.href = res.download_url;
                                }
                            },
                            error: function(xhr) {
                                $('#ExportModalNew').modal('hide');
                                $btn.html(originalContent);
                                $btn.removeClass('expanded disabled');
                                console.log("Error:", xhr.responseText);
                            }
                        })
                    }, 100);
                }
            })

            function getContent() {
                var client_id = $('#client_id').val();
                var client_fyear = $('#client_fyear').val();
                var period = $('#period').val();
                var journal_account = $('#journal_account').val();
                var source_text = $('#source_text').val();
                $.ajax({
                    type: "GET",
                    url: "{{ url('/journal/load-content-by-account') }}",
                    data: {
                        "client_id": client_id,
                        "client_fyear": client_fyear,
                        "period": period,
                        "account": journal_account,
                        "source_text": source_text,
                        "sort_column": sort_column,
                        "sort_order": sort_order
                    },
                }).done(function(response) {
                    $("#contentDiv").html(response);
                }).fail(function(response) {
                    $("#contentDiv").html(`
                    <div class="d-flex h-100 flex-column align-items-center justify-content-center">
                        <p>An error occurred while loading report.</p>
                    </div>
                    `);
                });
            }

            function loadAccounts() {
                var client = $('#client_id').val();
                var year = $('#client_fyear').val();
                $.ajax({
                    url: '{{ url('/get-accounts') }}',
                    type: 'POST',
                    data: {
                        client: client,
                        year: year,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        let sourceData = response.accounts;
                        
                        let currentIndex = sourceData.length > 0 ? 0 : -1;

                        function updateAccount(index) {
                            if (index >= 0 && index < sourceData.length) {
                                $("#account-text").text(sourceData[index].account_no);
                                $("#journal_account").val(sourceData[index].account_no);
                                currentIndex = index;
                            }
                            getContent();
                        }
                        // Previous Button Click
                        $("#btn-prev-account").off('click').on('click', function() {
                            if (sourceData.length === 0) return;
                            let prevIndex = (currentIndex - 1 + sourceData.length) % sourceData
                                .length;
                            updateAccount(prevIndex);
                            $("#select_all").prop('checked', false);
                            $("#btn_batch_update").prop('disabled', true);
                            $("#btn_batch_delete").prop('disabled', true);
                        });
                        // Next Button Click
                        $("#btn-next-account").off('click').on('click', function() {
                            if (sourceData.length === 0) return;
                            let nextIndex = (currentIndex + 1) % sourceData.length;
                            updateAccount(nextIndex);
                            $("#select_all").prop('checked', false);
                            $("#btn_batch_update").prop('disabled', true);
                            $("#btn_batch_delete").prop('disabled', true);
                        });
                        // Set Default Source
                        updateAccount(currentIndex);
                    }
                });
            }
            loadAccounts();
        })
    </script>
