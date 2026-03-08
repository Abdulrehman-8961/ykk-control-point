<div class="col-lg-12 pl-0">
    <div id="add-journal" style="overflow: hidden;height:93vh;width: 68%; float: left;">
        <!-- Page Content -->
        <div class="content content-full px-0 pt-0 pb-0 -boxed" style="padding-left: 14px !important;">
            <!-- New Post -->
            <form id="form-add-journal" class="js-validation form-1 form-journals row" method="POST">


                <!--startpostingperiod-->
                <div class="col-md-12 pl-0" id="form-add-journal-posting-period"
                    style="position: relative; padding-right: 30px !important;">
                    <div class="modal-content2" style="">
                        <div class="block-options">
                            <button type="button" class="btn-block-option" id="btnCloseAJ">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-12 pr-0">
                        <div class="block new-block"
                            style="padding-top: 16px !important;margin-bottom: 0.875rem !important;">
                            <div class="block-header py-0" style="padding-left:7mm;">

                                <a class="  section-header">Select Posting Period
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">

                                <div class="row justify-content-   push pl-2">
                                    <div class="col-lg-7">
                                        <div class="row ">
                                            <label class="col-sm-3 col-form-label "
                                                for="example-hf-client_id">Client</label>

                                            <div class="col-sm-9  form-group">
                                                <select type="text" class="form-control select2" id="pp_client"
                                                    name="pp_client" placeholder="Salutation">
                                                    <option value="" selected disabled>Select Client
                                                    </option>
                                                    @foreach ($clients as $c)
                                                        <option value="{{ $c->id }}"
                                                            {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}
                                                            client-company="{{ $c->company }}"
                                                            default-province="{{ $c->default_prov }}"
                                                            remittance="{{ $c->tax_remittance }}"
                                                            fiscal-start="{{ $c->fiscal_start }}">{{ $c->display_name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class=" row">
                                            <label class="col-sm-3 col-form-label" for=" ">Month</label>
                                            <div class="col-sm-4  form-group pr-0">
                                                <select type="text" class="form-control" id="pp_month"
                                                    name="pp_month" placeholder="Salutation">
                                                    <option value="1"
                                                        @if (intval(date('m')) == 1) selected @endif>
                                                        January</option>
                                                    <option value="2"
                                                        @if (intval(date('m')) == 2) selected @endif>
                                                        February</option>
                                                    <option value="3"
                                                        @if (intval(date('m')) == 3) selected @endif>
                                                        March</option>
                                                    <option value="4"
                                                        @if (intval(date('m')) == 4) selected @endif>
                                                        April</option>
                                                    <option value="5"
                                                        @if (intval(date('m')) == 5) selected @endif>
                                                        May</option>
                                                    <option value="6"
                                                        @if (intval(date('m')) == 6) selected @endif>
                                                        June</option>
                                                    <option value="7"
                                                        @if (intval(date('m')) == 7) selected @endif>
                                                        July</option>
                                                    <option value="8"
                                                        @if (intval(date('m')) == 8) selected @endif>
                                                        August</option>
                                                    <option value="9"
                                                        @if (intval(date('m')) == 9) selected @endif>
                                                        September</option>
                                                    <option value="10"
                                                        @if (intval(date('m')) == 10) selected @endif>October
                                                    </option>
                                                    <option value="11"
                                                        @if (intval(date('m')) == 11) selected @endif>November
                                                    </option>
                                                    <option value="12"
                                                        @if (intval(date('m')) == 12) selected @endif>December
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-5  form-group">
                                                <div class="col-sm-5 col-form-label text-nowrap transla tion pp_period"
                                                    style="color: #252525">
                                                    &nbsp;
                                                </div>
                                                <input type="hidden" name="pp_period" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class=" row">
                                            <label class="col-sm-3 col-form-label" for=" ">Year</label>
                                            <div class="col-sm-4 pr-0">
                                                <select type="text" class="form-control" id="pp_year"
                                                    name="pp_year" placeholder="Salutation">
                                                    @for ($y = intval(date('Y')) + 3; $y >= 1930; $y--)
                                                        <option value="{{ $y }}"
                                                            @if (Auth::user()->default_fiscal_year
                                                                    ? ($y == Auth::user()->default_fiscal_year
                                                                        ? 'selected'
                                                                        : '')
                                                                    : ($y == intval(date('Y'))
                                                                        ? 'selected'
                                                                        : '')) selected @endif>
                                                            {{ $y }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="col-sm-5 col-form-label text-nowrap transl ation pp_fyear"
                                                    style="color: #252525">
                                                    &nbsp;
                                                </div>
                                                <input type="hidden" name="pp_fyear" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <button type="button"
                                            class="btn ml-5 btn-new selected-posting-period">Continue</button>
                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>

                </div>
                <!--endpostingperiod-->


                <!--startclientstats-->
                <div class="col-md-12 pl-0 d-none" id="form-add-journal-client-stats"
                    style="padding-right: 30px !important;">
                    <div class="block new-block"
                        style="margin-bottom: 0.875rem !important;padding-top: 10px !important;padding-bottom: 10px !important;">
                        <div class="block-content pb-0 new-block-content" style="padding-top: 0 !important;">
                            <div class="modal-content2" style="">
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" id="btnCloseAJ">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="edit-no-bulb">
                                        <p class="mb-0"
                                            style="font-family: Signika;font-size: 16pt;font-weight: bold;color: #262626;line-height: 1;"
                                            id="edit_no">#{{ str_pad($editNo, 5, '0', STR_PAD_LEFT) }}</p>
                                        <p class="mb-0"
                                            style="text-align: left;font-family: Signika;font-weight: 100;font-size: 9pt;color: #595959;line-height: 1;">
                                            EDIT#</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div style="line-height: 1.2;">
                                        <div class="balance-label" id="journal-client-pp-fyear">F/Y</div>
                                        <div class="balance-label ml-auto change-label"
                                            id="journal-client-pp-fyear-year" data-toggle="modal"
                                            data-target="#chaneFiscalYearModal"
                                            style="color: #a5a5a5; font-size: 10pt;text-align: end; cursor: pointer; border: 1px solid #a5a5a5; width: fit-content;border-radius: 7px;padding-left: 5px;padding-right: 5px;">
                                            F/Y</div>
                                    </div>
                                    <div class="acct-balance" id="fiscal_year_balance">0.00</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div style="line-height: 1.2;">
                                        <div class="balance-label" id="journal-client-pp-period">Period</div>
                                        <div class="balance-label ml-auto change-label"
                                            id="journal-client-pp-period_month" data-toggle="modal"
                                            data-target="#chaneMonthModal"
                                            style="color: #a5a5a5; font-size: 10pt;text-align: end; cursor: pointer;border: 1px solid #a5a5a5; width: fit-content;border-radius: 7px;padding-left: 5px;padding-right: 5px;">
                                        </div>
                                    </div>
                                    <div class="acct-balance" id="period_balance">0.00</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div style="line-height: 1.2;">
                                        <div class="balance-label" id="journal-client-pp-current-account">Account #
                                        </div>
                                        <div class="balance-label ml-auto change-label" id="journal-client-account-no"
                                            style="color: #a5a5a5; font-size: 10pt;text-align: end; cursor: pointer;  width: fit-content;border-radius: 7px;padding-left: 5px;padding-right: 5px;">
                                        </div>
                                    </div>
                                    <div class="acct-balance" id="account_balance">0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--endclientstats-->


                <!--startdetails-->
                <div class="col-md-12 pl-0" style="padding-right: 30px !important;">

                    <div class="block new-block d-none" id="form-add-journal-details"
                        style="margin-bottom: 0.875rem !important;position: relative;">

                        {{-- <div class="modal-content2" style="">
                            <div class="block-options">
                                <button type="button" class="btn-block-option" id="btnCloseAJ">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                           </div> --}}

                        <div class="block-header py-0" style="padding-left:7mm;">

                            <a class="  section-header">Details
                            </a>

                            <div class="block-options">

                            </div>
                        </div>
                        <div class="block-content pb-0 new-block-content">

                            <div class="row justify-content-  push pl-2">
                                <div class="col-lg-10">
                                    <div class=" row ">
                                        <label class="col-sm-2 col-form-label text-nowrap"
                                            for="example-hf-email">Account# &nbsp;
                                            <a href="javascript:void(0);" tabindex="-1" data-toggle="modal"
                                                data-target="#AccountChartModal" data-bs-target="#AccountChartModal"
                                                id="view-accounts-chart"><img
                                                    src="{{ asset('public') }}/icons_2024_02_24/icon-change-account-grey-off.png"
                                                    width="20"></a></label>
                                        <div class="col-sm-3  form-group">
                                            <input type="" class="form-control" id="dt_account"
                                                name="dt_account" placeholder="0000" value=""
                                                list="dt_account_description_list" maxlength="4">
                                            <?php
                                            $accounts_list = DB::table('gifi')->where('is_deleted', 0)->distinct('account_no')->limit(100)->pluck('account_no')->toArray();
                                            ?>
                                            {{-- <select name="dt_account" id="dt_account" class="form-control select2">
                                                <option value="" selected disabled>Select Account No
                                                </option>
                                                @foreach ($accounts_list as $l)
                                                    <option value="{{ $l }}">{{ $l }} </option>
                                                @endforeach
                                            </select> --}}
                                            <datalist id="dt_account_description_list">
                                                @foreach ($accounts_list as $l)
                                                    <option value="{{ $l }}">
                                                @endforeach

                                            </datalist>
                                        </div>
                                        <div class="col-sm-7 col-form-label text-nowrap pl-0 dt-account-description"
                                            style="color: #252525;">
                                            &nbsp;

                                        </div>
                                    </div>
                                    <div class=" row ">
                                        <label class="col-sm-2 col-form-label" for="example-hf-email">Source &nbsp;
                                            <a href="javascript:void(0);" tabindex="-1" data-toggle="modal"
                                                data-target="#sourceModal" data-bs-target="#sourceModal"
                                                id="view-accounts-chart"><img
                                                    src="{{ asset('public') }}/icons_2024_02_24/icon-change-account-grey-off.png"
                                                    width="20"></a></label>
                                        <?php
                                        $dt_source_description = '';
                                        ?>
                                        <div class="col-sm-3  form-group">
                                            {{-- <select type="text" class="form-control" id="dt_source_code"
                                                name="dt_source_code" placeholder="Salutation">
                                                @foreach ($sources as $sj)
                                                    @if ($loop->iteration == 1)
                                                        <?php
                                                        // $dt_source_description = $sj->source_description;
                                                        ?>
                                                    @endif
                                                    <option value="{{ $sj->id }}"
                                                        description="{{ $sj->source_description }}">
                                                        {{ $sj->source_code }}
                                                    </option>
                                                @endforeach
                                            </select> --}}
                                            <input type="text" name="dt_source_code_" id="dt_source_code_"
                                                class="form-control" maxlength="4" list="sorce_list">
                                            <input type="hidden" name="dt_source_code" id="dt_source_code"
                                                class="form-control" maxlength="4">
                                            <datalist id="sorce_list">
                                                @foreach ($sources as $sj)
                                                    @if ($loop->iteration == 1)
                                                        <?php
                                                        $dt_source_description = $sj->source_description;
                                                        ?>
                                                    @endif
                                                    <option value="{{ $sj->source_code }}">
                                                @endforeach
                                            </datalist>
                                        </div>
                                        <div class="col-sm-7 col-form-label pl-0 text-nowrap dt-source-description"
                                            style="color: #252525;">
                                            {{-- {{ @$dt_source_description ? $dt_source_description : '&nbsp;' }} --}}
                                            &nbsp;

                                        </div>
                                    </div>
                                    <div class=" row ">
                                        <label class="col-sm-2 col-form-label form-group"
                                            for="example-hf-email">Ref#</label>
                                        <div class="col-sm-3  form-group">
                                            <input type="" class="form-control" id="dt_ref" name="dt_ref"
                                                placeholder="00000000" value="">
                                        </div>
                                        <div class="col-sm-7 form-group pl-0">
                                            <div class=" row ">
                                                <label class="col-sm-2 col-form-label"
                                                    for="example-hf-email">Date</label>
                                                <div class="col-sm-5 ">
                                                    <input type="" class="form-control" id="dt_date"
                                                        name="dt_date" placeholder="DDMMYYYY" value="">
                                                </div>
                                                <div class="col-sm-5 col-form-label text-nowrap translation"
                                                    style="color: #252525">
                                                    {{-- translatedDate() --}}


                                                </div><input type="hidden" name="translation"
                                                    value="{{ translatedDate() }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" row ">
                                        <label class="col-sm-2 col-form-label"
                                            for="example-hf-email">Description</label>
                                        <div class="col-sm-10  form-group">
                                            <input type="" class="form-control" id="dt_description"
                                                list="dt_description_list" name="dt_description"
                                                placeholder="Journal Description" value="">
                                            <datalist id="dt_description_list">
                                                <?php
                                                $journal_descs = DB::table('journals')->distinct('description')->limit(100)->pluck('description')->toArray();
                                                ?>
                                                @foreach ($journal_descs as $d)
                                                    <option value="{{ $d }}">
                                                @endforeach

                                            </datalist>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--enddetails-->


                <!--startamount-->
                <div class="row d-none col-md-12 px-0" id="form-add-journal-amount">
                    <div class="col-md-12">
                        <div class="block new-block" style="margin-bottom: 0.875rem !important;">
                            <div class="block-header py-0" style="padding-left:7mm;">

                                <a class="  section-header">Amount
                                </a>

                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">

                                <div class="row justify-content-   push">
                                    <div class="col-md-12 row pr-0">
                                        <div class="col-md-10">
                                            <div class=" row">
                                                <label class="col-sm-2 col-form-label"
                                                    for="example-hf-email">Debit</label>
                                                <div class="col-sm-3  form-group pr-0">
                                                    <input type="" class="form-control form-debit text-left"
                                                        style="text-align: right !important;" id="amnt_debit"
                                                        name="amnt_debit" placeholder="0.00" value="0">
                                                </div>
                                                <div class="col" style="max-width: 105px;"></div>
                                                {{-- <div class="col-sm-1"></div> --}}
                                                <label class="col-sm-1 col-form-label"
                                                    for="example-hf-email">Credit</label>
                                                <div class="col-sm-3  form-group pr-0">
                                                    <input type="" class="form-control form-credit text-left"
                                                        id="amnt_credit" name="amnt_credit" placeholder="0.00"
                                                        style="text-align: right !important;" value="0">
                                                </div>
                                                <div class="col d-flex justify-content-center"
                                                    style="max-width: 105px;">
                                                    <div class="custom-control custom-  custom-control-  custom-control-lg mt-2 "
                                                        data-toggle="tooltip" data-title="Taxable">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="amnt_taxable" name="amnt_taxable"
                                                            applied-to-tax1="{{ @$tax_rate->applied_to_tax1 == 1 ? 1 : 0 }}">
                                                        <label class="custom-control-label" for="amnt_taxable">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pr-0">
                                            <div class="row justify-content-end">

                                                <div class="col -sm-6" style="max-width: 105px;">
                                                    <input type="" class="form-control sanitize text-left"
                                                        style="border-color: #fff !important;" id="portion"
                                                        name="portion" value="100%" placeholder="100%">
                                                </div>
                                                <div class="col-sm-2 px-0 d-flex justify-content-center mt-2">
                                                    <a href="javascript:;" class="portion-info"
                                                        data-notes="Setting the portioning % , typically used for
                                                        expenses that are under 100% for the business,
                                                        to any other value other than 100%, allows this
                                                        journal entry to be applied as a portion while
                                                        the difference in percentage is automatically
                                                        posted against the clients personal withdrawals"
                                                        data-line1="Setting the portioning % , typically used for
                                                        expenses that are under 100% for the business,"
                                                        data-line2="to any other value other than 100%, allows this
                                                        journal entry to be applied as a portion while"
                                                        data-line3="the difference in percentage is automatically
                                                        posted against the clients personal withdrawals">
                                                        <img src="{{ asset('public') }}/icons2/icon-info.png?cache=1"
                                                            width="20px">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 row pr-0 taxation d-none">
                                        <div class="col-md-10">
                                            <div class=" row">
                                                <label class="col-sm-2 col-form-label amnt_label1"
                                                    for="example-hf-email">
                                                    @if (@$tax_rate->tax_label_1)
                                                        {{ $tax_rate->tax_label_1 }}
                                                    @else
                                                        Tax1
                                                    @endif
                                                </label>
                                                <div class="col-sm-3  form-group pr-0">
                                                    <input type="" class="form-control sanitize form-taxes "
                                                        id="amnt_tax1" name="amnt_tax1"
                                                        style="text-align: right !important;" placeholder="0.00"
                                                        value="0">
                                                </div>
                                                <div class="col"
                                                    style="max-width: 105px;padding-right: 0 !important;">
                                                    <input type="" class="form-control" id="tx_gst"
                                                        style="border-color: #fff !important;padding-left:5px !important;padding-right: 5px !important;"
                                                        name="tx_gst" placeholder=""
                                                        value="{{ @$tax_rate->tax_rate_1 ? @$tax_rate->tax_rate_1 : 0 }}%"
                                                        default="{{ @$tax_rate->tax_rate_1 ? @$tax_rate->tax_rate_1 : 0 }}%">
                                                </div>

                                                <label class="col-sm-1 col-form-label amnt_label2"
                                                    for="example-hf-email">
                                                    @if (@$tax_rate->tax_label_2)
                                                        {{ $tax_rate->tax_label_2 }}
                                                    @else
                                                        Tax2
                                                    @endif
                                                </label>
                                                <div class="col-sm-3  form-group pr-0">
                                                    <input type="" class="form-control sanitize form-taxes "
                                                        style="text-align: right !important;" id="amnt_tax2"
                                                        name="amnt_tax2" placeholder="0.00" value="0">
                                                </div>
                                                <div class="col"
                                                    style="max-width: 105px;padding-right: 0!important;">
                                                    <input type="" class="form-control" id="tx_pst"
                                                        style="border-color: #fff !important;padding-left: 5px !important;padding-right: 5px !important;"
                                                        name="tx_pst" placeholder=""
                                                        value="{{ @$tax_rate->tax_rate_2 ? @$tax_rate->tax_rate_2 : 0 }}%"
                                                        default="{{ @$tax_rate->tax_rate_2 ? @$tax_rate->tax_rate_2 : 0 }}%">
                                                </div>
                                            </div>
                                        </div>

                                    </div>





                                    <div class="col-md-12 row justify-content-end pr-0">
                                        <div class="col-md-10 d-none" id="net-col">
                                            <div class=" row">
                                                <label class="col-sm-2 col-form-label"
                                                    for="example-hf-email">Net</label>
                                                <div class="col-sm-3  form-group pr-0" style="max-width: 235px;">
                                                    <div class="bubble-white-new1 bubble-text-first form-net"
                                                        tabindex="1"
                                                        style="padding-top: 7px;padding-bottom: 7px;text-align:right !important"
                                                        id="net">
                                                        0.00
                                                    </div>
                                                    <input type="hidden" name="net" value="0">
                                                </div>
                                                <div class="col"
                                                    style="max-width: 105px;padding-right: 0 !important;">

                                                </div>
                                                <label class="col-sm-1 col-form-label"
                                                    for="example-hf-email">Prov</label>
                                                <div class="col-sm-3 pr-0">
                                                    <select type="text" class="form-control" id="tx_province"
                                                        name="tx_province" placeholder="Salutation">
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->state_name }}"
                                                                @if ($loop->iteration == 1) selected @endif>
                                                                {{ $city->state_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col"
                                                    style="max-width: 105px;padding-right: 0!important;">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pr-0 text-right">
                                            <button type="submit"
                                                class="btn mr-3 btn-new submit-add-journal d-flex">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <!--endamount-->

            </form>
        </div>
    </div>
    <div style="width: 32%!important;float: left;position:relative;" id="view-client-journals">

    </div>
    <div class="modal fade" id="chaneFiscalYearModal" tabindex="-1" role="dialog" data-backdrop="static"
        aria-labelledby="modal-block-large" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
            <div class="modal-content">
                <div class="block  block-transparent mb-0">
                    <div class="block-header pb-0  " style="padding-top:20px;">
                        <span class="b e section-header">Change Posting Year</span>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="block-content new-block-content pt-0 pb-0 ">
                        <div class="row">
                            <label class="col-sm-3">Year</label>
                            <div class="col-sm-9 form-group">
                                <select type="text" class="form-control" id="change_pp_year"
                                    name="change_pp_year" placeholder="Salutation">
                                    @for ($y = intval(date('Y')) + 3; $y >= 1930; $y--)
                                        <option value="{{ $y }}"
                                            @if ($y == intval(date('Y'))) selected @endif>
                                            {{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">
                        <button type="submit" class="btn mr-3 btn-new change-fy">Select</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="chaneMonthModal" tabindex="-1" role="dialog" data-backdrop="static"
        aria-labelledby="modal-block-large" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
            <div class="modal-content">
                <div class="block  block-transparent mb-0">
                    <div class="block-header pb-0  " style="padding-top:20px;">
                        <span class="b e section-header">Change Posting Month</span>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="block-content new-block-content pt-0 pb-0 ">
                        <div class="row">
                            <label class="col-sm-3">Month</label>
                            <div class="col-sm-9 form-group">
                                <select type="text" class="form-control" id="change_pp_month"
                                    name="change_pp_month" placeholder="Salutation">
                                    <option value="1" @if (intval(date('m')) == 1) selected @endif>
                                        January</option>
                                    <option value="2" @if (intval(date('m')) == 2) selected @endif>
                                        February</option>
                                    <option value="3" @if (intval(date('m')) == 3) selected @endif>
                                        March</option>
                                    <option value="4" @if (intval(date('m')) == 4) selected @endif>
                                        April</option>
                                    <option value="5" @if (intval(date('m')) == 5) selected @endif>
                                        May</option>
                                    <option value="6" @if (intval(date('m')) == 6) selected @endif>
                                        June</option>
                                    <option value="7" @if (intval(date('m')) == 7) selected @endif>
                                        July</option>
                                    <option value="8" @if (intval(date('m')) == 8) selected @endif>
                                        August</option>
                                    <option value="9" @if (intval(date('m')) == 9) selected @endif>
                                        September</option>
                                    <option value="10" @if (intval(date('m')) == 10) selected @endif>October
                                    </option>
                                    <option value="11" @if (intval(date('m')) == 11) selected @endif>November
                                    </option>
                                    <option value="12" @if (intval(date('m')) == 12) selected @endif>December
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="block-content block-content-full text-right  pt-4" style="padding-left: 9mm;">
                        <button type="submit" class="btn mr-3 btn-new change-month">Select</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
