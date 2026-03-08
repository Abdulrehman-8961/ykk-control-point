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

?>
<table class="table border-0 table-period">
    <thead>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;" colspan="2">{{$client->company}}</td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;" colspan="2">Trial Balance</td>
        </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;" colspan="2">{{date("d-M-Y",
                strtotime(getFiscalYearEnd($client->fiscal_start)))}}</td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;text-align:left;" colspan="2">{{$fyear}}</td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        </tr>
        <tr>
            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>
            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>
            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>
            <td class="text-right"
            style="font-weight:600;padding:0;border:0 !important;text-align:right;" ></td>

        </tr>
        <tr>
            <td style="font-weight:600;padding:0;border:0 !important;"></td>
            <td style="font-weight:600;padding:0;border:0 !important;"></td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">Debits</td>
            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">Credits</td>
        </tr>
    </thead>
    <tbody>
        <?php
            $account_debits =0 ;
            $account_credits =0;
            ?>
        @foreach ($reports as $j)
        <?php
           $debit = 0.00;
           $credit = 0.00;
           if($j->total_debits > $j->total_credits) {
            $debit = round($j->total_debits - $j->total_credits, 2);
           } else {
            $credit = round($j->total_credits - $j->total_debits, 2);
           }
           $account_debits += $debit;
           $account_credits += $credit;
        ?>
        <tr>
            <td style="padding:0;border:0 !important;text-align:left;">{{$j->account_no}}</td>
            <td style="padding:0;border:0 !important;text-align:left;">{{$j->description}}</td>
            <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                {{$debit == 0 ? '-' : number_format($debit,2)}}
            </td>
            <td class="text-right" style="padding:0;border:0 !important;text-align:right;">
                {{$credit == 0 ? '-' : number_format($credit, 2)}}
            </td>
        </tr>
        @endforeach
            <tr>
            <td colspan="2" style="border-top: 0 !important;">

            </td>

            <td class="text-right"
                style="font-weight:600;padding:0;border:0 !important;text-align:right;">

                     <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                        <span
                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($account_debits,
                            2)}}</span>
                    </div>
            </td>
            <td class="text-right"
                style="font-weight:600;padding:0;border: 0 !important;text-align:right;">
                <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                    <span
                        style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($account_credits,
                        2)}}</span>
                </div>
            </td>
        </tr>
    </tbody>

</table>






