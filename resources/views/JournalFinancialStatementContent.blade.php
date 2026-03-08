<?php

function getFiscalYearEnd($fiscalStart)
{
    $parts = explode('-', $fiscalStart);

    $year = intval($parts[0]);

    $month = intval($parts[1]);

    $day = intval($parts[2]);

    $fiscalYearEnd = new DateTime($year + 1 . '-' . $month . '-' . $day);

    $fiscalYearEnd->modify('-1 day');

    $fiscalYear = $fiscalYearEnd->format('Y');

    $fiscalMonth = $fiscalYearEnd->format('n');

    $fiscalDay = $fiscalYearEnd->format('j');

    //$fiscalYearEndFormatted = 'Fiscal Year End ' . $this->monthToStringShort($fiscalMonth) . ' ' . $fiscalYear;

    return $fiscalYear . '-' . $fiscalMonth . '-' . $fiscalDay;
}

function rounding($number, $rounding)
{
    if ($rounding === '0') {
        return round($number);
    }

    if ($rounding === '000') {
        // Move floating point 3 places left

        $moved = $number / 1000;

        // Round to nearest hundred

        $rounded = round($moved);

        return $rounded;
    }

    if ($rounding === '000000') {
        // Move floating point 3 places left

        $moved = $number / 1000000;

        // Round to nearest hundred

        $rounded = round($moved);

        return $rounded;
    }

    return $number;
}

function format($number)
{
    return number_format($number, 2, '.', ',');

    // $formatted = number_format($number, 2, '.', '');

    // if($formatted == (int)$formatted) {

    //     return number_format($number);

    // }

    // return  number_format($number, 2, '.', '');
}

$total_assets_previous_fyear = 0;

$total_assets_current_fyear = 0;

$total_liabilities_previous_fyear = 0;

$total_liabilities_current_fyear = 0;

?>

<!-- Page Content -->

<div class="row px-0">





    <div class="col-lg-12    sheet-page d-flex flex-column" id="showData1">

        <div class="block new-block position-relative  5 page-block">



            <div class="d-flex text-nowrap flex-column h-100 align-items-center justify-content-center">

                <p class=" text-center mb-0 section-header" style="font-size: 24px !important;">Consultation AmaltiTEK</p>

                <p class=" text-center mb-0 " style="font-size: 24px !important;font-weight: normal !important">Financial
                    Statements</p>

                <p class=" text-center mb-0 section-header" style="font-size: 20px !important;">
                    {{ date(
                        'F d,',
                    
                        strtotime(getFiscalYearEnd($client->fiscal_start)),
                    ) }}{{ $fyear }}
                </p>

            </div>



            <!-- page footer --->

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>

        </div>



    </div><!--cover page-->









    <div class="col-lg-12    sheet-page d-flex flex-column" id="showData1">

        <div class="block new-block position-relative  5 page-block"
            style="padding-left: 7%;padding-right: 7%;padding-top: 7% !important;">



            <div class="d-flex text-nowrap flex-column align-items-center justify-content-center"
                style="padding-bottom: 9% !important;">

                <p class=" text-center mb-0 section-header" style="font-size: 18px !important;">
                    {{ $system_settings->company }}</p>

                <p class=" text-center mb-0 " style="font-size: 17px !important;font-weight: 500 !important">
                    {{ $system_settings->firstname . ' ' . $system_settings->lastname }}</p>

                <p class=" text-center mb-0 section-header"
                    style="font-size: 17px !important;font-weight: 500 !important;white-space: pre-line;">
                    {{ $system_settings->address }}</p>

                <p class=" text-center mb-0 section-header"
                    style="font-size: 17px !important;font-weight: 500 !important;">Tel:
                    {{ $system_settings->telephone }} Fax: {{ $system_settings->fax }}</p>

            </div>

            <div class="">

                <p class=" mb-0 section-header" style="font-size: 18px !important;">Notice to reader:</p>

                <p class="mb-2 section-header" style="font-size: 17px !important;font-weight:500 !important;;">We have
                    compiled the balance sheet of <b class="text-danger">{{ $client->company }}</b> as at <b
                        class="text-danger">{{ date(
                            'F d,',
                        
                            strtotime(getFiscalYearEnd($client->fiscal_start)),
                        ) }}
                        {{ $fyear }}</b> and the statements of income and

                    reattained earnings and changes in the financial position for the year then ended from information
                    provided by the director of

                    the company. We have not audited, reviewed or otherwise attempted to verify the accuracy of
                    completeness of such

                    information. Accordingly, readers are cautioned that these statements may not be appropriate for
                    their purposes.</p>

                <p class=" section-header mb-5" style="font-size: 17px !important;font-weight:500 !important;">
                    {{ $system_settings->firstname . ' ' . $system_settings->lastname . ' ' . $system_settings->designation }}
                </p>

            </div>



            <div>

                <p class=" mb-0 section-header pl-2" style="font-size: 17px !important;">{{ $system_settings->city }},
                    {{ $system_settings->province }}</p>

                <p class=" mb-0 section-header pl-2" style="font-size: 17px !important;font-weight:500 !important;">
                    {{ date('F d, Y') }}</p>

            </div>





        </div>



    </div><!--notice to reader page-->









    <div class="col-lg-12    sheet-page d-flex flex-column" id="showData1">

        <div class="block new-block position-relative  5 page-block">

            <div class="block-header py-0 d-flex justify-content-between align-items-start"
                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">



                <div>

                    {{-- <a class="  section-header">{{$client->company}} --}}

                    <a class="  section-header">{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}

                    </a>

                    <p style="font-size: 11pt !important;">
                        {{ date(
                            'd-M',
                        
                            strtotime(getFiscalYearEnd($client->fiscal_start)),
                        ) }}-{{ $fyear }}
                    </p>

                </div>





                <a class="  section-header">Unaudited Balance Sheet

                </a>

            </div>

            <div class="block-content pb-0   "
                style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                @if (count($current_assets) > 0)
                    <div class="col-sm-12"><!--Current Assets-->

                        <div class="d-flex text-nowrap">

                            <div>

                                <p class=" pr-1 mb-0 ">CURRENT ASSETS</p>

                            </div>

                            <hr class="w-100" style="border-color: #595959!important">

                        </div>

                        <div class="row">

                            <div class="col-md-12" style="padding-left: 4rem!important;">

                                <table class="table border-0 table-period">

                                    <thead>

                                        <tr>

                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>




                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_current_assets_previous_fyear = 0;
                                        
                                        $total_current_assets_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($current_assets as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_current_assets_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_current_assets_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            @if ($amnt_previous_fyear != 0 || $amnt_current_fyear != 0)
                                                <tr>

                                                    <td style="padding:0;border:0 !important;">{{ $j->description }}
                                                    </td>


                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                        <?php
                                        
                                        $total_assets_previous_fyear += $total_current_assets_previous_fyear;
                                        
                                        $total_assets_current_fyear += $total_current_assets_current_fyear;
                                        
                                        ?>

                                    </tbody>
                                    @if ($total_current_assets_current_fyear > 0 && $total_current_assets_previous_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Current Assets

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_current_assets_current_fyear) }}</span>

                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_current_assets_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                    </div><!--Current Assets-->
                @endif

                @if (count($capital_assets) > 0)
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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_capital_assets_previous_fyear = 0;
                                        
                                        $total_capital_assets_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($capital_assets as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')->where('account_no', $j->account_no)->where('client', $j->client)->where('fyear', $fyear)->where('is_deleted', 0)->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_capital_assets_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_capital_assets_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            <tr>

                                                <td style="padding:0;border:0 !important;">{{ $j->description }}</td>


                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                </td>
                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                </td>

                                            </tr>
                                        @endforeach

                                        <?php
                                        
                                        $total_assets_previous_fyear += $total_capital_assets_previous_fyear;
                                        
                                        $total_assets_current_fyear += $total_capital_assets_current_fyear;
                                        
                                        ?>

                                    </tbody>
                                    @if ($total_capital_assets_previous_fyear > 0 && $total_capital_assets_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Capital Assets

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_capital_assets_current_fyear) }}</span>

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_capital_assets_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                    </div><!--Capital Assets-->
                @endif
                @if (count($long_term_assets) > 0)
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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_long_term_assets_previous_fyear = 0;
                                        
                                        $total_long_term_assets_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($long_term_assets as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_current_assets_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_current_assets_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            <tr>

                                                <td style="padding:0;border:0 !important;">{{ $j->description }}</td>


                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                </td>
                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                </td>

                                            </tr>

                                            <?php
                                            
                                            $total_long_term_assets_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_long_term_assets_current_fyear += $amnt_current_fyear;
                                            
                                            ?>
                                        @endforeach

                                        <?php
                                        
                                        $total_assets_previous_fyear += $total_long_term_assets_previous_fyear;
                                        
                                        $total_assets_current_fyear += $total_long_term_assets_current_fyear;
                                        
                                        ?>

                                    </tbody>
                                    @if ($total_long_term_assets_previous_fyear > 0 && $total_long_term_assets_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Long-Term Assets

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_long_term_assets_current_fyear) }}</span>

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_long_term_assets_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
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
                                                style="font-weight:600;padding:0;border: 0 !important;">

                                                <div class=""
                                                    style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_assets_current_fyear) }}</span>

                                                </div>

                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">



                                                <div class=""
                                                    style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_assets_previous_fyear) }}</span>

                                                </div>

                                            </td>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    </div><!--Long Total Assets-->
                @endif

            </div>



            <!-- page footer --->

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>

        </div>



    </div><!--Balance sheet assets-->







    <div class="col-lg-12   sheet-page" id="showData2">

        <div class="block new-block position-relative  5 page-block">

            <div class="block-header py-0 d-flex justify-content-between align-items-start"
                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">



                <div>

                    {{-- <a class="  section-header">{{$client->company}} --}}

                    <a class="  section-header">{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}

                    </a>

                    <p style="font-size: 11pt !important;">
                        {{ date(
                            'd-M',
                        
                            strtotime(getFiscalYearEnd($client->fiscal_start)),
                        ) }}-{{ $fyear }}
                    </p>

                </div>





                <a class="  section-header">Unaudited Balance Sheet

                </a>

            </div>

            <div class="block-content pb-0   "
                style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">


                @if (count($current_liabilities) > 0)
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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_current_liabilities_previous_fyear = 0;
                                        
                                        $total_current_liabilities_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($current_liabilities as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            // if($j->account_no == 2710) {
                                            
                                            //     dd($total_debits_current_fyear, $total_credits_current_fyear, $total_debits_previous_fyear, $total_credits_previous_fyear);
                                            
                                            // }
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_current_liabilities_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_current_liabilities_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            @if ($amnt_previous_fyear != 0 || $amnt_current_fyear != 0)
                                                <tr>

                                                    <td style="padding:0;border:0 !important;">{{ $j->description }}
                                                    </td>


                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                        <?php
                                        
                                        $total_liabilities_previous_fyear += $total_current_liabilities_previous_fyear;
                                        
                                        $total_liabilities_current_fyear += $total_current_liabilities_current_fyear;
                                        
                                        ?>

                                    </tbody>
                                    @if ($total_current_liabilities_previous_fyear > 0 && $total_current_liabilities_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Current Liabilities

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_current_liabilities_current_fyear) }}</span>

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_current_liabilities_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif

                                </table>

                            </div>

                        </div>

                    </div><!--Current Liabilities-->

                @endif

                @if (count($long_term_liabilities) > 0)

                    <div class="col-sm-12"><!--Capital Assets-->

                        <div class="d-flex text-nowrap">

                            <div>

                                <p class=" pr-1 mb-0 ">LONG-TERM LIABILITIES</p>

                            </div>

                            <hr class="w-100" style="border-color: #595959!important">

                        </div>

                        <div class="row">

                            <div class="col-md-12 " style="padding-left: 4rem!important;">

                                <table class="table border-0 table-period">

                                    <thead>

                                        <tr>

                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>




                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_long_term_liabilities_previous_fyear = 0;
                                        
                                        $total_long_term_liabilities_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($long_term_liabilities as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_long_term_liabilities_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_long_term_liabilities_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            <tr>

                                                <td style="padding:0;border:0 !important;">{{ $j->description }}</td>


                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                </td>
                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                </td>

                                            </tr>
                                        @endforeach

                                        <?php
                                        
                                        $total_liabilities_previous_fyear += $total_long_term_liabilities_previous_fyear;
                                        
                                        $total_liabilities_current_fyear += $total_long_term_liabilities_current_fyear;
                                        
                                        ?>

                                    </tbody>
                                    @if ($total_long_term_liabilities_previous_fyear > 0 && $total_long_term_liabilities_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Long-Term Liabilities

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_long_term_liabilities_current_fyear) }}</span>

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_long_term_liabilities_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif

                                </table>

                            </div>

                        </div>

                    </div><!--Capital Assets-->

                @endif

                


                <div class="col-sm-12"><!--Total Liabilities-->



                    <div class="row">

                        <div class="col-md-12 " style="    padding-left: 4rem!important;">

                            <table class="table border-0 table-period">

                                @if ($total_liabilities_current_fyear > 0 && $total_liabilities_previous_fyear > 0)
                                    <tfoot>

                                        <tr>

                                            <td style="border-top: 0 !important;font-weight:600;">

                                                Total Liabilities

                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border: 0 !important;">

                                                <div class=""
                                                    style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_liabilities_current_fyear) }}</span>

                                                </div>

                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">



                                                <div class=""
                                                    style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_liabilities_previous_fyear) }}</span>

                                                </div>

                                            </td>

                                        </tr>

                                    </tfoot>
                                @endif
                            </table>

                        </div>

                    </div>

                </div><!--Total Liabilities-->



            </div>

            <!-- page footer --->

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>



        </div>



    </div><!--Balance sheet liability-->













    <div class="col-lg-12   sheet-page " id="showData3">

        <div class="block new-block position-relative  5 page-block">

            <div class="block-header py-0 d-flex justify-content-between align-items-start"
                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">



                <div>

                    {{-- <a class="  section-header">{{$client->company}} --}}

                    <a class="  section-header">{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}

                    </a>

                    <p style="font-size: 11pt !important;">
                        {{ date(
                            'd-M',
                        
                            strtotime(getFiscalYearEnd($client->fiscal_start)),
                        ) }}-{{ $fyear }}
                    </p>

                </div>





                <a class="  section-header">Unaudited Balance Sheet

                </a>

            </div>

            <div class="block-content pb-0   "
                style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                @if (count($equity) > 0)

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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_equity_previous_fyear = 0;
                                        
                                        $total_equity_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($equity as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_equity_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_equity_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            <tr>

                                                <td style="padding:0;border:0 !important;">{{ $j->description }}</td>


                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                </td>
                                                <td class="text-right" style="padding:0;border:0 !important;">

                                                    {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                </td>

                                            </tr>
                                        @endforeach

                                        <?php
                                        
                                        $total_liabilities_previous_fyear += $total_equity_previous_fyear;
                                        
                                        $total_liabilities_current_fyear += $total_equity_current_fyear;
                                        
                                        ?>

                                    </tbody>
                                    @if ($total_equity_previous_fyear > 0 && $total_equity_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Equity

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_equity_current_fyear) }}</span>

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_equity_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                    </div><!--Equity-->


                @endif

                @if (count($retained_earnings) > 0)

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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_retained_earnings_previous_fyear = 0;
                                        
                                        $total_retained_earnings_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($retained_earnings as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_retained_earnings_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_retained_earnings_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            @if ($amnt_previous_fyear != 0 || $amnt_current_fyear != 0)
                                                <tr>

                                                    <td style="padding:0;border:0 !important;">{{ $j->description }}
                                                    </td>


                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                    @if ($total_retained_earnings_previous_fyear > 0 && $total_retained_earnings_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total retained earnings

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_retained_earnings_current_fyear) }}</span>

                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_retained_earnings_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                    </div><!--Total Retained Earnings-->


                @endif


                <div class="col-sm-12"><!--Total Total Retained Earnings + Liabilities-->



                    <div class="row">

                        <div class="col-md-12 " style="    padding-left: 4rem!important;">

                            <table class="table border-0 table-period">

                                @if (
                                    $total_liabilities_previous_fyear + $total_retained_earnings_previous_fyear > 0 &&
                                        $total_liabilities_current_fyear + $total_retained_earnings_current_fyear > 0)
                                    <tfoot>

                                        <tr>

                                            <td style="border-top: 0 !important;font-weight:600;">

                                                Total retained earnings and Liabilities

                                            </td>





                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border: 0 !important;">

                                                <div class=""
                                                    style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_liabilities_current_fyear + $total_retained_earnings_current_fyear) }}</span>

                                                </div>

                                            </td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">



                                                <div class=""
                                                    style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_liabilities_previous_fyear + $total_retained_earnings_previous_fyear) }}</span>

                                                </div>

                                            </td>

                                        </tr>

                                    </tfoot>
                                @endif
                            </table>

                        </div>

                    </div>

                </div><!--Total Retained Earnings + Liabilities-->



            </div>

            <div class="d-flex flex-column " style="padding-left: 7%;margin-top: 15rem !important;">

                <div style="width:200px;">

                    <p class="mb-5"
                        style="font-weight: 600!important;font-family:Signika;color:#595959 !important;border-top:1px solid #595959 !important;padding-top:1rem;">

                        Approved By

                    </p>

                </div>

                <div style="width: 200px;">

                    <p class="mt-4"
                        style="font-weight: 600!important;font-family:Signika;color:#595959 !important;border-top:1px solid #595959 !important;padding-top:1rem;">

                        Date

                    </p>

                </div>

            </div>

            <!-- page footer --->

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>



        </div>



    </div><!--Balance sheet Retained Earnings-->

















    <div class="col-lg-12   sheet-page " id="showData4">

        <div class="block new-block position-relative  5 page-block">

            <div class="block-header py-0 d-flex justify-content-between align-items-start"
                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">



                <div>

                    {{-- <a class="  section-header">{{$client->company}} --}}

                    <a class="  section-header">{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}

                    </a>

                    <p style="font-size: 11pt !important;">
                        {{ date(
                            'd-M',
                        
                            strtotime(getFiscalYearEnd($client->fiscal_start)),
                        ) }}-{{ $fyear }}
                    </p>

                </div>





                <a class="  section-header">Unaudited Statement of Income

                </a>

            </div>

            <div class="block-content pb-0   "
                style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

                @if (count($revenue) > 0)

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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_revenue_previous_fyear = 0;
                                        
                                        $total_revenue_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($revenue as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_revenue_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_revenue_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            @if ($amnt_previous_fyear != 0 || ($amnt_current_fyear = 0))
                                                <tr>

                                                    <td style="padding:0;border:0 !important;">{{ $j->description }}
                                                    </td>


                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                    @if ($total_revenue_previous_fyear > 0 && $total_revenue_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Revenue

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_revenue_current_fyear) }}</span>

                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_revenue_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                    </div><!--Revenue-->


                @endif

                @if (count($cost_of_sales) > 0)
                    <div class="col-sm-12"><!--Cost of Sales-->

                        <div class="d-flex text-nowrap">

                            <div>

                                <p class=" pr-1 mb-0 ">COST OF SALES</p>

                            </div>

                            <hr class="w-100" style="border-color: #595959!important">

                        </div>

                        <div class="row">

                            <div class="col-md-12 " style="padding-left: 4rem!important;">

                                <table class="table border-0 table-period">

                                    <thead>

                                        <tr>

                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>




                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_cost_of_sales_previous_fyear = 0;
                                        
                                        $total_cost_of_sales_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($cost_of_sales as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_cost_of_sales_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_cost_of_sales_current_fyear += $amnt_current_fyear;
                                            
                                            ?>

                                            @if ($amnt_previous_fyear != 0 || $amnt_current_fyear != 0)
                                                <tr>

                                                    <td style="padding:0;border:0 !important;">{{ $j->description }}
                                                    </td>


                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                    @if ($total_cost_of_sales_previous_fyear > 0 && $total_cost_of_sales_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total Cost of Sales

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_cost_of_sales_current_fyear) }}</span>

                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_cost_of_sales_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-12 " style="    padding-left: 4rem!important;">

                                <table class="table border-0 table-period">
                                    @if (
                                        $total_revenue_previous_fyear - $total_cost_of_sales_previous_fyear > 0 &&
                                            $total_revenue_current_fyear - $total_cost_of_sales_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Gross profit (loss)

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <div class=""
                                                        style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                        <span
                                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_revenue_current_fyear - $total_cost_of_sales_current_fyear) }}</span>

                                                    </div>

                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <div class=""
                                                        style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                        <span
                                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_revenue_previous_fyear - $total_cost_of_sales_previous_fyear) }}</span>

                                                    </div>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div><!-- Gross profile -->

                    </div><!--Cost of sales-->


                @endif

                @if (count($operating_expenses) > 0)
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
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear }}</td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                {{ $fyear - 1 }}</td>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        
                                        $total_expenses_previous_fyear = 0;
                                        
                                        $total_expenses_current_fyear = 0;
                                        
                                        ?>

                                        @foreach ($operating_expenses as $j)
                                            <?php
                                            
                                            $total_debits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_current_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('credit');
                                            
                                            $total_debits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                                ->sum('debit');
                                            
                                            $total_credits_previous_fyear = DB::table('journals')
                                            
                                                ->where('account_no', $j->account_no)
                                            
                                                ->where('client', $j->client)
                                            
                                                ->where('fyear', $fyear - 1)
                                            
                                                ->where('is_deleted', 0)
                                            
                                                ->sum('credit');
                                            
                                            $amnt_previous_fyear = 0.0;
                                            
                                            $amnt_current_fyear = 0.0;
                                            
                                            if ($total_debits_previous_fyear > $total_credits_previous_fyear) {
                                                $amnt_previous_fyear = rounding($total_debits_previous_fyear - $total_credits_previous_fyear, $rounding);
                                            } else {
                                                $amnt_previous_fyear = rounding($total_credits_previous_fyear - $total_debits_previous_fyear, $rounding);
                                            }
                                            
                                            if ($total_debits_current_fyear > $total_credits_current_fyear) {
                                                $amnt_current_fyear = rounding($total_debits_current_fyear - $total_credits_current_fyear, $rounding);
                                            } else {
                                                $amnt_current_fyear = rounding($total_credits_current_fyear - $total_debits_current_fyear, $rounding);
                                            }
                                            
                                            $total_expenses_previous_fyear += $amnt_previous_fyear;
                                            
                                            $total_expenses_current_fyear += $amnt_current_fyear;
                                            
                                            ?>



                                            @if ($amnt_previous_fyear != 0 || $amnt_current_fyear != 0)
                                                <tr>

                                                    <td style="padding:0;border:0 !important;">{{ $j->description }}
                                                    </td>


                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear) }}

                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;">

                                                        {{ $amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear) }}

                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                    @if ($total_expenses_previous_fyear > 0 && $total_expenses_current_fyear > 0)
                                        <tfoot>

                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total expenses

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_expenses_current_fyear) }}</span>

                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_expenses_previous_fyear) }}</span>

                                                </td>

                                            </tr>



                                            <tr>

                                                <td style="border-top: 0 !important;font-weight:600;">

                                                    Total operating expenses

                                                </td>





                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">

                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_expenses_current_fyear + $total_cost_of_sales_current_fyear) }}</span>

                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">



                                                    <span
                                                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_expenses_previous_fyear + $total_cost_of_sales_previous_fyear) }}</span>

                                                </td>

                                            </tr>

                                        </tfoot>
                                    @endif
                                </table>

                            </div>

                        </div>

                    </div><!--Expenses-->

                @endif



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
                                            style="font-weight:600;padding:0;border: 0 !important;">

                                            <div class=""
                                                style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_revenue_current_fyear - ($total_expenses_current_fyear + $total_cost_of_sales_current_fyear)) }}</span>

                                            </div>

                                        </td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">



                                            <div class=""
                                                style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">

                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{ format($total_revenue_previous_fyear - ($total_expenses_previous_fyear + $total_cost_of_sales_previous_fyear)) }}</span>

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

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>



        </div>



    </div><!--Statement of Income-->















</div>
