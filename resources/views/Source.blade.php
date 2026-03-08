@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
    <?php
    $userAccess = explode(',', Auth::user()->access_to_client);

    $limit = 10;
    $no_check = DB::Table('settings')
        ->where('user_id', Auth::id())
        ->first();
    if (isset($_GET['limit']) && $_GET['limit'] != '') {
        $limit = $_GET['limit'];

        if ($no_check != '') {
            DB::table('settings')
                ->where('user_id', Auth::id())
                ->update(['source' => $limit]);
        } else {
            DB::table('settings')->insert(['user_id' => Auth::id(), 'source' => $limit]);
        }
    } else {
        if ($no_check != '') {
            if ($no_check->source != '') {
                $limit = $no_check->source;
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

        $qry = DB::table('source_code')
            ->where('is_deleted', 0)
            ->where(function ($query) {
                $query->Orwhere('source_code', 'like', '%' . @$_GET['search'] . '%');
                $query->Orwhere('source_description', 'like', '%' . @$_GET['search'] . '%');
            })
            ->orderBy($field, $orderby)
            ->paginate($limit);
    } else {
        $qry = DB::table('source_code')
            ->where('is_deleted', 0)
            ->orderBy('id', 'desc')
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

                padding: 0px;
                margin: .25rem .1rem;
            }

            .btn-link:hover {

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

            .pk-1 {
                border: 0.5px solid;
                font-size: 11pt;
                font-weight: normal;
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
        </style>


        <!-- Page Content -->
        <div class="con   no-print page-header " id="JournalHeader">
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

                                    <?php

                                    $filter = (isset($_GET['advance_search']) ? 'advance_search=' . $_GET['advance_search'] : '') . (isset($_GET['source_code']) ? '&source_code=' . $_GET['source_code'] : '') . (isset($_GET['source_description']) ? '&source_description=' . $_GET['source_description'] : '') . (isset($_GET['slider']) ? '&slider=' . $_GET['slider'] : '') . (isset($_GET['limit']) ? '&limit=' . $_GET['limit'] : '');
                                    ?>

                                    <form class="push mb-0" method="get" id="form-search"
                                        action="{{ url('source/') }}?{{ $filter }}">

                                        <div class="input-group main-search-input-group" style="max-width: 74.375%;">
                                            <input type="text" value="{{ @$_GET['search'] }}"
                                                class="form-control searchNew" name="search"
                                                placeholder="Search Source Code">
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
                                <div class="col-sm-auto  1" style="margin-top: 2px;">
                                    <span data-toggle="modal" data-bs-target="#filterModal" data-target="#filterModal">

                                        @if (Auth::user()->role != 'read')

                                            <a class="btn btn-dual  AddTaxModal d2 " data-toggle="tooltip"
                                            data-custom-class="header-tooltip" data-trigger="hover"
                                            data-placement="top" title=""
                                            data-original-title="Add Source Code" href="javascript:;">
                                                <img src="{{ asset('public/img/ui-icon-add.png') }}"  style="width: 15px">
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
                                    action="{{ url('source') }}?{{ $_SERVER['QUERY_STRING'] }}">
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


                    <div class="col-lg-4    no-print" style="overflow-y: auto;height: 90vh;margin-top: 1px;">
                        <div style="overflow-y: auto;height: 90vh;">
                        @foreach ($qry as $q)
                            <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent mr-2"
                                data="{{ $q->id }}" style="cursor:pointer;">

                                <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative" style="">
                                    <div class="mr-1      justify-content-center align-items-center  d-flex"
                                        style="width: 20%;float: left; padding: 7px;">
                                        <img src="{{ asset('/public') }}/icons2/icon-source-grey.png"
                                            class="rounded-   circle" style="width: 70px;height: 70px;object-fit: cover;">
                                    </div>
                                    <div class="w- 100 d-flex justify-content-between" style="width: 70%;">
                                        <div class="d-flex flex-column" style="width: calc(100% - 130px)">
                                            <span class="font-12pt mb-0 text-truncate font-w600 c1"
                                                style="font-family: Calibri;color:#4194F6 !important;">Source</span>
                                            <span
                                                style="overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                                color: #262626;
                                border:1px solid #262626;
                                background-color: #BFBFBF;
                                border-style: dashed !important;
                                min-width: 100%;
                                border-radius: 2px;
                                line-height: 1.6;
                                padding-top: 2px;
                                padding-bottom: 2px;
                                padding-left: 5px;
                                padding-right: 5px;">{{ $q->source_code }}</span>
                                            <div
                                                style="overflow: hidden;
                                text-overflow: ellipsis;
                                width: fit-content;
                                max-width: calc(100% - -107px);
                                line-height: 1.6;
                                white-space: nowrap;
                                font-size: 11pt;
                                font-family: Calibri;">
                                                <span>{{ $q->source_description }}</span>
                                            </div>
                                        </div>
                                        <div style="position: absolute;right: 10px;top: 10px;">
                                            @if ($q->source_code_status == 1)
                                                <span
                                                    style="float:right;
                                font-family: Calibri;
                                line-height: 1.5 !important;
                                color: #FFF;
                                background-color: #4EA833;
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
                                            <?php     if(Auth::check()){
                                    if(@Auth::user()->role!='read'){ ?>
                                            <div class="ActionIcon ml-2" style="border-radius: 1rem">
                                                <a href="javascript:;" data="{{ $q->id }}" class="btnEdit ">
                                                    <img src="{{ asset('public') }}/icons2/icon-edit-grey.png?cache=1"
                                                        width="25px">
                                                </a>
                                            </div>
                                            <div class="ActionIcon ml-2"
                                                style="border-radius: 1rem;margin-left: 0.25rem !important;">
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
                    </div>







                    {{--

                    <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="{{$q->id}}"
                    style="cursor:pointer;">

                    <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative" style="height: 78px;">
                        <div class="mr-1      justify-content-center align-items-center  d-flex">
                            <img src="{{asset('/public')}}/icons2/icon-source-grey.png" class="rounded-   circle"
                               style=" padding-top: 12px;width: 70px;object-fit: cover;padding-bottom: 12px;padding-left: 8px;padding-right: 4px;height: 80px;">
                        </div>
                        <div class="w-100 d-flex justify-content-between" style="padding-left: 10px">
                            <div class="d-flex flex-column" style="width: calc(100% - 130px)">
                                <span style="font-family: Calibri;color:#4194F6;font-size:10pt;">Source</span>
                                <span class="pl-1" style="overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size:9pt;width: 60%;font-family: Calibri;color: #262626;border:1px solid #262626;background-color: #BFBFBF;border-radius: 4px;">{{$q->source_code}}</span>
                                <div style="overflow: hidden;
                                text-overflow: ellipsis;
                                white-space: nowrap;font-size: 10pt;font-family: Calibri;">
                                   <span>{{$q->source_description}}</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-between" style="padding-top: 4px;padding-bottom: 4px;width:30%;position: absolute;right:20px;">
                                <div>
                                    @if ($q->source_code_status == 1)
                                    <span style="float: right;width:70px !important;font-family: Calibri;color: #FFF;background-color: #4EA833;width:100%;padding-left: 8px;padding-right: 8px;padding-top: 7px;padding-bottom: 7px;display: block;line-height: 1;text-align: center;border-radius: 3px;font-size: 9pt;">Active</span>
                                    @else
                                    <span style="float: right;width:70px !important;font-family: Calibri;color: #FFF;background-color: #E54643;width:100%;padding-left: 8px;padding-right: 8px;padding-top: 7px;padding-bottom: 7px;display: block;line-height: 1;text-align: center;border-radius: 3px;font-size: 9pt;">Inactive</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-row justify-content-end" style="margin-top: 10px;">
                                    <?php     if(Auth::check()){
                                        if(@Auth::user()->role!='read'){ ?>
                                    <div class="ActionIcon ml-2" style="border-radius: 1rem">
                                        <a href="javascript:;" data="{{$q->id}}" class="btnEdit ">
                                            <img src="{{asset('public')}}/icons2/icon-edit-grey.png?cache=1"
                                                width="25px">
                                        </a>
                                    </div>
                                    <div class="ActionIcon ml-2"
                                        style="border-radius: 1rem;margin-left: 0.25rem !important;">
                                        <a href="javascript:;" class=" btnDelete" data="{{$q->id}}">
                                            <img src="{{asset('public')}}/icons2/icon-delete-grey.png?cache=1"
                                                width="25px">
                                        </a>
                                    </div>
                                <?php } }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    --}}



                    {{-- <div class="  " style="width:55%">

                    <div class="d-flex " style="padding-top: 10px">

                        <p class="font-11pt mr-1   mb-0 pk-1 pk-blue " style=" " data="{{$q->id}}">
                            {{$q->source_code}}</p>


                    </div>

                    <div class="d-flex pt-1">
                        <p class="font-11pt  text-truncate mb-0     " data="{{$q->id}}">
                            {{$q->source_description}}</p>


                    </div>


                </div>
                <div style="position: absolute;width:  ; top: 10px;right: 30px;">
                    @if ($q->source_code_status == 1)
                    <div class="    ml-auto     text-center font-weight-bold   pk-green pk-1  ">
                        <span class=" ">Active</span>
                        @else

                        <div class="    ml-auto     text-center font-weight-bold   pk-red pk-1  ">
                            <span class=" ">Inactive</span> @endif
                        </div>

                    </div>

                    <div class=" text-right" style="width:10%;;">

                        <div class=""
                            style="position: absolute;width: 100%; bottom:10px;right: 24px;display: flex;align-items: center;justify-content: end;">


                            <?php     if(Auth::check()){
                                    if(@Auth::user()->role!='read'){ ?>
                            <div class="ActionIcon ml-2    " style="border-radius: 1rem">
                                <a href="javascript:;" data="{{$q->id}}" class="btnEdit ">
                                    <img src="{{asset('public')}}/icons2/icon-edit-grey.png?cache=1"
                                        width="25px">
                                </a>
                            </div>

                            <div class="ActionIcon ml-2   mt-n1  "
                                style="border-radius: 1rem;margin-left: 0.25rem !important;">
                                <a href="javascript:;" class=" btnDelete" data="{{$q->id}}">
                                    <img src="{{asset('public')}}/icons2/icon-delete-grey.png?cache=1"
                                        width="25px">
                                </a>

                            </div>
                            <?php } }?>

                        </div>
                    </div> --}}





                    <form class="mb-0 pb-0" action="{{ url('end-source') }}" method="post">
                        @csrf
                        <div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header"><span class="revokeText">Revoke</span> Source
                                                Code</span>
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









                    <form class="mb-0 pb-0" id="form-add-tax" action="{{ url('insert-source') }}" method="post">
                        @csrf
                        <div class="modal fade" id="AddTaxModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg modal-bac "
                                style="max-width: 750px !important;" role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0" style="padding-top: 15px !important;">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Source Code</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option close-modal"
                                                    target-modal="#AddTaxModal" aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="block-content new-block-content pt-0 pb-0 ">




                                            <div class="row  form-group">
                                                <div class="col-lg-2">
                                                    <label class="col-form-label mandatory"> Code</label>
                                                </div>

                                                <div class="col-lg-4">

                                                    <input type="" name="source_code" placeholder="1-4 Char Code"
                                                        class="form-control ">

                                                </div>

                                            </div>

                                            <div class="row  form-group">
                                                <div class="col-lg-2">
                                                    <label class="col-form-label mandatory">Description</label>
                                                </div>

                                                <div class="col-lg-7">

                                                    <input type="" name="source_description"
                                                        placeholder="Source Description" class="form-control ">

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

                    <form class="mb-0 pb-0" id="exportform" action="{{ url('export-excel-ssl') }}?{{ $filter }}"
                        method="get">

                        <div class="modal fade" id="ExportModal" tabindex="-1" role="dialog" data-backdrop="static"
                            aria-labelledby="modal-block-large" aria-hidden="true">
                            <div class="modal-dialog modal- -centered  modal-md modal-bac " role="document">
                                <div class="modal-content">
                                    <div class="block  block-transparent mb-0">
                                        <div class="block-header pb-0  ">
                                            <span class="b e section-header">Export Source Code</span>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <i class="fa fa-fw fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="block-content new-block-content pt-0 pb-0 ">



                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <label>Fields to Export</label>
                                                    <select class="form-control selectpicker"
                                                        data-style="btn-outline-light border columns text-dark" id="columns"
                                                        data-actions-box="true" data-live-search="true" data- multiple=""
                                                        required="" name="columns[]">
                                                        <option value="1">Status</option>
                                                        <option value="2">Client</option>
                                                        <option value="3">Site</option>
                                                        <option value="4">Cert Type</option>
                                                        <option value="5">Issuer</option>
                                                        <option value="6">Start Date</option>
                                                        <option value="7">End Date</option>
                                                        <option value="8">Description</option>
                                                        <option value="9">Name (CN)</option>
                                                        <option value="10">Company (O)</option>

                                                        <option value="11">Locality (L)</option>
                                                        <option value="12">Country (CA)</option>
                                                        <option value="13">Department (OU)</option>
                                                        <option value="14">State (S)</option>
                                                        <option value="15">Email (e)</option>
                                                        <option value="16">LAN - SAN Type</option>
                                                        <option value="17">LAN - SAN Description</option>
                                                        <option value="18">LAN - Hostname</option>
                                                        <option value="19">LAN - IP Address Name</option>
                                                        <option value="20">LAN - IP Address Value</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="block-content block-content-full   pt-4"
                                            style="padding-left: 9mm;padding-right: 9mm">
                                            <button type="button" class="btn mr-3 btn-new " id="btnExport">Export</button>
                                            <button type="button" class="btn     btn-new-secondary"
                                                data-dismiss="modal">Cancel</button>

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






        </main>
        <!-- END Main Container -->
    @endsection('content')



    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>



    <script type="text/javascript">
        $(function() {
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
                    url: '{{ url('get-source-content') }}',
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

            $(document).on('click', '.AddTaxModal', function(){
                $('#AddTaxModal').modal('show');
            })

            $('#form-add-tax').submit(function(e) {
                e.preventDefault();

                var source_code = $('input[name=source_code]').val()
                var source_description = $('input[name=source_description]').val()


                var tele_regex = /^.{0,4}$/;
                var tele_regex1 = /^.{0,100}$/;
                if (source_code == '') {
                    $('input[name=source_code]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Code.',
                        delay: 5000
                    });
                } else if (!tele_regex.test(source_code)) {
                    $('input[name=source_code]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Source should be max 4 chars.',
                        delay: 5000
                    });
                } else if (source_description == '') {
                    $('input[name=source_description]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Description.',
                        delay: 5000
                    });
                } else if (!tele_regex1.test(source_description)) {
                    $('input[name=source_description]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Description should be max 100 chars.',
                        delay: 5000
                    });
                } else {
                    $.ajax({
                        type: "GET",
                        url: "{{ url('/source-validate') }}",
                        data: {
                            "source_code": source_code,
                        }
                    }).done(function(response) {
                        if (response == 1) {
                            Dashmix.helpers('notify', {
                                from: 'bottom',
                                align: 'left',
                                message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > An active source code already exists.',
                                delay: 5000
                            });
                        } else {
                            $('#form-add-tax')[0].submit();
                        }
                    });

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
                    url: '{{ url('get-source-edit-content') }}',

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
                e.stopImmediatePropagation();
                e.preventDefault();
                var id = $(this).attr('data');

                var c = confirm("Are you sure want to delete this Source Code");
                if (c) {
                    window.location.href = "{{ url('delete-source') }}?id=" + id;
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
                    url: "{{ url('get-comments-source') }}",
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
                    url: "{{ url('get-attachment-source') }}",
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

                    html += `   <div class="col-sm-6 px-0 attach-other-col ">
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


                var source_code = $('input[name=source_code_edit]').val()
                var source_description = $('input[name=source_description_edit]').val()

                var tele_regex = /^.{0,4}$/;
                var tele_regex1 = /^.{0,100}$/;
                if (source_code == '') {
                    $('input[name=source_code_edit]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Code.',
                        delay: 5000
                    });
                } else if (!tele_regex.test(source_code)) {
                    $('input[name=source_code_edit]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Source should be max 4 chars.',
                        delay: 5000
                    });
                } else if (source_description == '') {
                    $('input[name=source_description_edit]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Description.',
                        delay: 5000
                    });
                } else if (!tele_regex1.test(source_description)) {
                    $('input[name=source_description_edit]').focus();
                    Dashmix.helpers('notify', {
                        from: 'bottom',
                        align: 'left',
                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Description should be max 100 chars.',
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
                        'url': '{{ url('update-source') }}',
                        dataType: 'json',
                        async: false,

                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(res) {

                            if (res == 1) {
                                Dashmix.helpers('notify', {
                                    from: 'bottom',
                                    align: 'left',
                                    message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > An active source code already exists.',
                                    delay: 5000
                                });
                            } else {
                                window.location.href = "{{ url('/source') }}?id=" + data1;
                                //$("#JournalHeader").removeClass('d-none');
                                //Dashmix.helpers('notify', {from: 'bottom',align: 'left',message: 'Source Code successfully saved', delay: 5000});
                                //showData(data1)
                                click = 0;
                            }

                        }
                    })

                }

            })









        });

        $(document).ready(function() {
            $(document).on('click', '.undo-delete', function() {
                var id = $(this).attr('data');
                window.location.href = "{{ url('undo-delete-source') }}?id=" + id;
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
                $('#AddTaxModal input[name=source_code]').focus();
            });
        });
    </script>
