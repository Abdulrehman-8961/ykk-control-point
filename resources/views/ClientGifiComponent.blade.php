<div class="con   no-print page-header py-1  mb-3"
    style="border-radius:10px;height:51.2px !important;margin-bottom: 14px !important;" id="">
    <!-- Full Table -->
    <div class="b   mb-0  ">

        <div class="block-content pt-0 mt-0">

            <div class="TopArea" style="position: sticky;
padding-top: 6px;
z-index: 1000;;">
                <div class="row">
                    <div class="col-sm-7">

                        <!--<form class="push mb-0"   method="get" id="form-search"  >-->

                        <div class="input-group main-search-input-group" style="max-width: 86% !important;">
                            <input type="text" class="form-control searchNew w-75" style="height:32px !important;"
                                name="gifi-search" value="{{ $searchVal }}" data="{{ $id }}"
                                placeholder="Search Accounts">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <img src="{{ asset('public/img/ui-icon-search.png') }}" width="19px">
                                </span>
                            </div>
                        </div>
                        <div class="    float-left " role="tab" id="accordion2_h1">


                            <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->

                        </div>
                        <!--</form>-->

                    </div>
                    <div class="col-sm-5" style="">

                        <span data-toggle="modal" id="btnFilterClientGifi" data-client-id="{{ $id }}"
                            data-bs-target="#filterClientAccountModal" data-target="#filterClientAccountModal">
                            <button type="button" class="btn btn-dual d1   " style="margin-bottom: -5px !important;"
                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                data-original-title="Filter Accounts">
                                <img src="{{ asset('public/img/ui-icon-filters.png') }}" width="24px">
                            </button>
                        </span>
                        <?php if (@Auth::user()->role != 'read') { ?>
                        <a style="margin-bottom: -5px !important;" class="btn btn-dual d2   show-add-client-gifi-modal "
                            data-client-id="{{ $id }}" href="javascript:;">
                            <img src="{{ asset('public/img/ui-icon-add.png') }}" data-toggle="tooltip"
                                data-trigger="hover" data-placement="top" title=""
                                data-original-title="Add New Client Account" width="20px">
                        </a>
                        <?php } ?>
                        <a style="margin-bottom: -5px !important;"
                            href="javascript:void();" data-url="{{ url('export-excel-clients2') }}?{{ $_SERVER['QUERY_STRING'] }}"
                            class="btn btn-dual d2 btn-export" data-client-id="{{ $id }}" href="javascript:;">
                            <img src="{{ asset('public/new-gl-icons-dec/import-icon-white2.png') }}"
                                data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                                data-original-title="Export csv" width="20px">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>






<div class="block new-block"
    style="padding-top:0px !important; overflow-y: auto;height:90vh;border:0;background-color:transparent;">
    <div class="block-content pb-0 new-block-content" style="padding:0!important;">
        <?php
        foreach ($qry as $q) {
        $debits = DB::table('journals as j')
        ->where('j.account_no', $q->account_no)
        ->where('j.client', $q->client_id)
        ->where('is_deleted', 0)
        ->sum('j.debit');
        $credits = DB::table('journals as j')
        ->where('j.account_no', $q->account_no)
        ->where('j.client', $q->client_id)
        ->where('is_deleted', 0)
        ->sum('j.credit');
        $amount_clr = '';
        $text_clr = '';
        $amount = 0;
        $symbol = '';
        if ($debits > $credits) {
        $amount = $debits - $credits;
        if ($amount == 0) {
            $amount_clr = '#BDBDBE ';
            $text_clr = '#7F7F7F';
        } else {
            $amount_clr = '#4194F6';
            $text_clr = '#FFF';
        }
        $symbol = 'DR';
        } else {
        $amount = $credits - $debits;
        if ($amount == 0) {
            $amount_clr = '#BDBDBE ';
            $text_clr = '#7F7F7F';
        } else {
            $amount_clr = '#E54643';
            $text_clr = '#FFF';
        }
        $symbol = 'CR';
        }?>
        {{-- {{ dd($q) }} --}}
        <div class="block block-rounded   table-block-new mb-2 pb-1 -  view-------Content"
            style="background-color: #fff !important;" data="{{ $q->id }}" style="cursor:pointer;">

            <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative" style="">
                <div class="mr-1 justify-content-center align-items-center  d-flex"
                    style="width: 20%;float: left; padding: 7px;">
                    @if ($q->account_type == 'Liability')
                        <img src="{{ asset('/public') }}/icons/icon-account-liability.png" class="rounded-circle  "
                            style="object-fit: cover;width: 70px;height: 70px;">
                    @elseif ($q->account_type == 'Asset')
                        <img src="{{ asset('/public') }}/icons/icon-accounts-asset.png" class="rounded-circle  "
                            style="object-fit: cover;width: 70px;height: 70px;">
                    @elseif ($q->account_type == 'Retained Earning')
                        <img src="{{ asset('/public') }}/icons/icon-account-retained-earning.png"
                            class="rounded-circle  " style="object-fit: cover;width: 70px;height: 70px;">
                    @elseif ($q->account_type == 'Income' && ($q->sub_type == 'Operating expense' || $q->sub_type == 'Cost of sale'))
                        <img src="{{ asset('/public') }}/icons/icon-account-expense.png" class="rounded-circle  "
                            style="object-fit: cover;width: 70px;height: 70px;">
                    @elseif ($q->account_type == 'Income' && $q->sub_type == 'Revenue')
                        <img src="{{ asset('/public') }}/icons/icon-account-revenue.png" class="rounded-circle  "
                            style="object-fit: cover;width: 70px;height: 70px;">
                    @elseif ($q->logo != '')
                        <img src="{{ asset('/public') }}/client_logos/{{ $q->logo }}" class="rounded-circle  "
                            style="object-fit: cover;width: 70px;height: 70px;">
                    @else
                        <img src="{{ asset('public/img/image-default.png') }}" class="rounded- circle  "
                            style="object-fit: cover;width: 70px;height: 70px;">
                    @endif
                </div>
                <div class="w- 100 d-flex justify-content-between" style="width: 79%;">
                    <div class="d-flex flex-column" style="width: calc(100% - 100px);">
                        <span class="font-12pt mb-0 text-truncate font-w600 c1"
                            style="font-family: Calibri;color:#4194F6 !important;">Client Account</span>
                        <div class="d-flex flex-row">
                            <span
                                style="
                            text-overflow: ellipsis;
                            white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
                            color:#3F3F3F;
                            border:1px solid #3F3F3F;
                            border-style: dashed !important;

border-radius: 2px;
line-height: 1.6;
padding-top: 2px;
padding-bottom: 2px;
padding-left: 5px;
padding-right: 5px;

                            margin-right: 0.375rem;"
                                class="px-2">{{ $q->account_type[0] }}</span>
                            <span class="flex-grow-1"
                                style="overflow: hidden;
text-overflow: ellipsis;
white-space: nowrap;font-size:12pt;width: fit-content;font-family: Calibri;
color: #262626;
border:1px solid #262626;
background-color: #BFBFBF;
border-style: dashed !important;

border-radius: 2px;
line-height: 1.6;
padding-top: 2px;
padding-bottom: 2px;
padding-left: 5px;
padding-right: 5px;">{{ $q->account_no }}
                                - {{ $q->sub_type }}</span>
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
                                <span>{{ $q->description }}</span>
                            </div>
                        </div>
                    </div>
                    <div style="position: absolute;right: 10px;top: 10px;">
                        <span
                            style="float:right;
                        min-width: 100%;
                        font-family: Calibri;
                        line-height: 1.5 !important;
                        font-weight: 600!important;
                        color: {{ $text_clr }};
                        border: 1px solid transparent;
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
                                font-size: 11pt;">{{ '$' . number_format($amount, 2) }}</span>
                    </div>
                    <div class="d-flex flex-row justify-content-end"
                        style="margin-top: 20px;position: absolute;right: 10px;bottom: 5px;">
                        <?php
                        if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {?>
                        <div class="ActionIcon ml-1    " style="border-radius: 1rem;">
                            <a href="javascript:;" data="{{ $q->id }}" data-client-id="{{ $q->client_id }}"
                                account_type="{{ $q->account_type }}" sub_type="{{ $q->sub_type }}"
                                account_no="{{ $q->account_no }}" description="{{ $q->description }}"
                                note="{{ $q->note }}" class="btnEditGifi">
                                <img src="{{ asset('public') }}/icons2/icon-edit-grey.png?cache=1" width="25px">
                            </a>
                        </div>

                        <div class="ActionIcon ml-1  " style="border-radius: 1rem;">
                            <a href="javascript:;" class=" btnDeleteGifi" data="{{ $q->id }}"
                                data-client-id="{{ $q->client_id }}">
                                <img src="{{ asset('public') }}/icons2/icon-delete-grey.png?cache=1" width="25px">
                            </a>


                        </div>
                        <?php        }
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>



{{--

      <div class="block block-rounded   table-block-new mb-2 pb-1 -  view-------Content" data="{{$q->id}}"
            style="cursor:pointer;">

            <div class="block-content pt-1 pb-1 d-flex  pl-2 position-relative">
                <div class="mr-1      justify-content-center align-items-center  d-flex"
                    style="width:60px;padding: 0px;">

                    <img src="{{asset('public')}}/icons2/icon-account-grey.png" class="rounded-circle  " width="100%"
                        style=" object-fit: cover;">
                </div>
                <div class="  ml-2" style="width:55%">

                    <div class="d-flex " style="padding-top: 10px">

                        <p class="font-11pt mr-1   mb-0 pk-1 pk-blue text-nowrap" style=" " data="{{$q->id}}">
                            {{$q->account_no}}
                        </p>
                        <p class="font-11pt mr-1   mb-0 pk-1 pk-purple text-nowrap" style=" " data="{{$q->id}}">
                            {{$q->sub_type}}
                        </p>

                    </div>

                    <div class="d-flex pt-1">
                        <p class="font-11pt   mb-0     " data="{{$q->id}}">{{$q->description}}</p>


                    </div>


                </div>
                <div style="position: absolute;width:  ; top: 10px;right: 10px;">

                    <div class="    ml-auto     text-center font-weight-bold    ">
                        <span class="{{$amount_clr}}" style="font-family: Jura !important;">{{number_format($amount, 2)
                            . ' ' . $symbol }}</span>
                    </div>

                </div>

                <div class=" text-right" style="width:10%;;">

                    <div class=""
                        style="position: absolute;width: 100%; bottom: 0px;right: 10px;display: flex;align-items: center;justify-content: end;">
                        <?php
                        if (Auth::check()) {
                        if (@Auth::user()->role != 'read') {?>
                        <div class="ActionIcon px-0 ml-2    " style="border-radius: 1rem;">
                            <a href="javascript:;" data="{{$q->id}}" data-client-id="{{$q->client_id}}"
                                account_type="{{$q->account_type}}" sub_type="{{$q->sub_type}}"
                                account_no="{{$q->account_no}}" description="{{$q->description}}" note="{{$q->note}}"
                                class="btnEditGifi">
                                <img src="{{asset('public')}}/icons2/icon-edit-grey.png?cache=1" width="25px">
                            </a>
                        </div>

                        <div class="ActionIcon px-0 ml-2   mt-n1  " style="border-radius: 1rem;">
                            <a href="javascript:;" class="px-1 btnDeleteGifi" data="{{$q->id}}"
                                data-client-id="{{$q->client_id}}">
                                <img src="{{asset('public')}}/icons2/icon-delete-grey.png?cache=1" width="25px">
                            </a>


                        </div>
                        <?php        }
    }
?>
                    </div>
                </div>
            </div>
        </div>
    --}}
