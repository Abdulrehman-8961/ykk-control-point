
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
<table class="table border-0 table-period">
    <thead>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;" >{{$client->company}}</td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="2">Unaudited Balance Sheet</td>
        </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;" >{{date("d-M-Y",
                strtotime(getFiscalYearEnd($client->fiscal_start)))}}</td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="2">CURRENT ASSETS</td>

        </tr>

        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;"></td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
                                                    <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                                                        {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                                    </td>
                                                    <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                                                        {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <?php
                                                    $total_assets_previous_fyear += $total_current_assets_previous_fyear;
                                                    $total_assets_current_fyear += $total_current_assets_current_fyear;
                                                ?>







<tr>
    <td style="border-top: 0 !important;font-weight:600;">
        Total Current Assets
    </td>

    <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;">

        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_assets_previous_fyear)}}</span>
    </td>
    <td class="text-right"
        style="font-weight:600;padding:0;border: 0 !important;text-align:right;">
        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_assets_current_fyear)}}</span>
    </td>
</tr>

    </tbody>

    <thead>
       <tr>
        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>
    </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="3">CAPITAL ASSETS</td>


        </tr>

        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;"></td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
        </td>
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear )}}
        </td>
    </tr>
    @endforeach
    <?php
        $total_assets_previous_fyear += $total_capital_assets_previous_fyear;
        $total_assets_current_fyear += $total_capital_assets_current_fyear;
    ?>


<tr>
    <td style="border-top: 0 !important;font-weight:600;">
        Total Capital Assets
    </td>

    <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;">

        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_capital_assets_previous_fyear)}}</span>
    </td>
    <td class="text-right"
        style="font-weight:600;padding:0;border: 0 !important;text-align:right;">
        <span
                style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_capital_assets_current_fyear)}}</span>
    </td>
</tr>



    </tbody>



    <thead>
        <tr>
            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

        </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="3">LONG-TERM ASSETS</td>

        </tr>

        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;"></td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
<td class="text-right" style="padding:0;border:0 !important;text-align:right;">
    {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
</td>
<td class="text-right" style="padding:0;border:0 !important;text-align:right;">
    {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
</td>
</tr>
@endforeach
<?php
$total_assets_previous_fyear += $total_long_term_assets_previous_fyear;
$total_assets_current_fyear += $total_long_term_assets_current_fyear;
?>


<tr>
<td style="border-top: 0 !important;font-weight:600;">
Total Long-Term Assets
</td>

<td class="text-right"
style="font-weight:600;padding:0;border:0 !important;text-align:right;">
<span
style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_assets_previous_fyear)}}</span>
</td>
<td class="text-right"
style="font-weight:600;padding:0;border: 0 !important;text-align:right;">
<span
style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_assets_current_fyear)}}</span>
</td>
</tr>



</tbody>


<thead>
    <tr>
        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

    </tr>
    <tr>

        <td style="border-top: 0 !important;font-weight:600;">
        Total Assets
        </td>

        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-right:right;">

        <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_assets_previous_fyear)}}</span>
        </div>
        </td>
        <td class="text-right"
        style="font-weight:600;padding:0;text-align:right;border: 0 !important;">
        <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_assets_current_fyear)}}</span>
        </div>
        </td>
        </tr>
</thead>












<thead>
    <tr>        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td></tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;" >{{$client->company}}</td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="2">Unaudited Balance Sheet</td>
    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;" >{{date("d-M-Y",
            strtotime(getFiscalYearEnd($client->fiscal_start)))}}</td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="2">CURRENT LIABILITIES</td>

    </tr>

    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;"></td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
                                            <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                                                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                            </td>
                                            <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                                                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <?php
                                            $total_liabilities_previous_fyear += $total_current_liabilities_previous_fyear;
                                            $total_liabilities_current_fyear += $total_current_liabilities_current_fyear;
                                        ?>
                                            <tr>
                                                <td style="border-top: 0 !important;font-weight:600;">
                                                    Total Current Liabilities
                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">

                                                    <span
                                                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_liabilities_previous_fyear)}}</span>
                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">
                                                    <span
                                                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_current_liabilities_current_fyear)}}</span>
                                                </td>
                                            </tr>

                                    </tbody>



                                    <thead>
                                        <tr>
                                            <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="3">LONG-TERM LIABILITIES</td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
                                            <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                                                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
                                            </td>
                                            <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                                                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <?php
                                            $total_liabilities_previous_fyear += $total_long_term_liabilities_previous_fyear;
                                            $total_liabilities_current_fyear += $total_long_term_liabilities_current_fyear;
                                        ?>



<tr>
    <td style="border-top: 0 !important;font-weight:600;">
        Total Long-Term Liabilities
    </td>

    <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;">

        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_liabilities_previous_fyear)}}</span>
    </td>
    <td class="text-right"
        style="font-weight:600;padding:0;border: 0 !important;">
        <span
        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_long_term_liabilities_current_fyear)}}</span>
    </td>
</tr>
                                    </tbody>



                                    <thead>
                                        <tr>
                                            <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="3">EQUITY</td>

                                        </tr>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
        </td>
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
        </td>
    </tr>
    @endforeach
    <?php
        $total_liabilities_previous_fyear += $total_equity_previous_fyear;
        $total_liabilities_current_fyear += $total_equity_current_fyear;
    ?>
      <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total Equity
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_equity_previous_fyear)}}</span>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_equity_current_fyear)}}</span>
        </td>
    </tr>
</tbody>




<thead>
    <tr>
        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

    </tr>
    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total Liabilities
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

                 <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                    <span
                        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_previous_fyear)}}</span>
                </div>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                <span
                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_current_fyear)}}</span>
            </div>
        </td>
    </tr>
</thead>




<thead>
    <tr>        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td></tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;" >{{$client->company}}</td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="2">Unaudited Balance Sheet</td>
    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;" >{{date("d-M-Y",
            strtotime(getFiscalYearEnd($client->fiscal_start)))}}</td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="2">RETAINED EARNINGS</td>

    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;"></td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
            <td class="text-right" style="padding:0;text-align:right;border:0 !important;">
                {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
            </td>
            <td class="text-right" style="padding:0;text-align:right;border:0 !important;">
                {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
            </td>
        </tr>
    @endforeach

    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total retained earnings
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_retained_earnings_previous_fyear)}}</span>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <span
                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_retained_earnings_current_fyear)}}</span>
        </td>
    </tr>
</tbody>


<thead>
    <tr>
        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

    </tr>
    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total retained earnings and Liabilities
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

                 <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                    <span
                        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_previous_fyear + $total_retained_earnings_previous_fyear)}}</span>
                </div>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                <span
                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_liabilities_current_fyear + $total_retained_earnings_current_fyear)}}</span>
            </div>
        </td>
    </tr>
</thead>






<thead>
    <tr>        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td></tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;" >{{$client->company}}</td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="2">Unaudited Statement of Income</td>
    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;" >{{date("d-M-Y",
            strtotime(getFiscalYearEnd($client->fiscal_start)))}}</td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="2">REVENUE</td>

    </tr>

    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;"></td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
        </td>
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
        </td>
    </tr>
    @endforeach

    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total Revenue
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_previous_fyear)}}</span>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <span
                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_current_fyear)}}</span>
        </td>
    </tr>
</tbody>

<thead>
    <tr>
        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="3">COST OF SALES</td>

    </tr>

    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;"></td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
        </td>
        <td class="text-right" style="padding:0;text-align:right;border:0 !important;">
            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
        </td>
    </tr>
    @endforeach


    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total Cost of Sales
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_cost_of_sales_previous_fyear)}}</span>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_cost_of_sales_current_fyear)}}</span>
        </td>
    </tr>
</tbody>




<thead>
    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Gross profit (loss)
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

                 <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                    <span
                        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_previous_fyear - $total_cost_of_sales_previous_fyear)}}</span>
                </div>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                <span
                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_current_fyear - $total_cost_of_sales_current_fyear)}}</span>
            </div>
        </td>
    </tr>
</thead>






<thead>
    <tr>
        <td class="text-right"
        style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="3"></td>

    </tr>
    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="3">EXPENSES</td>

    </tr>

    <tr>
        <td style="font-weight:600;padding:0;border:0 !important;"></td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear-1}}</td>
        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">{{$fyear}}</td>
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
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_previous_fyear == 0 ? '-' : format($amnt_previous_fyear)}}
        </td>
        <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
            {{$amnt_current_fyear == 0 ? '-' : format($amnt_current_fyear)}}
        </td>
    </tr>
    @endforeach

    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total operating expenses
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;">

            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_previous_fyear)}}</span>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;text-align:right;">
            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_current_fyear)}}</span>
        </td>
    </tr>

    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Total expenses
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_previous_fyear + $total_cost_of_sales_previous_fyear)}}</span>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <span
            style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_expenses_current_fyear + $total_cost_of_sales_current_fyear)}}</span>
        </td>
    </tr>

</tbody>




<thead>
    <tr>
        <td style="border-top: 0 !important;font-weight:600;">
            Net Income
        </td>

        <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;">

                 <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                    <span
                        style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_previous_fyear - ($total_expenses_previous_fyear + $total_cost_of_sales_previous_fyear))}}</span>
                </div>
        </td>
        <td class="text-right"
            style="font-weight:600;padding:0;border: 0 !important;">
            <div class="" style="border-bottom: 2px solid #595959;text-align:right;padding-bottom: 5px;width:fit-content;float:right;">
                <span
                    style="border-top:1px solid #595959 !important;text-align:right;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{format($total_revenue_current_fyear - ($total_expenses_current_fyear + $total_cost_of_sales_current_fyear))}}</span>
            </div>
        </td>
    </tr>
</thead>


</table>









