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

<div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">
    <div class="block-header py-0 d-flex justify-content-between align-items-start"
        style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

        <div>
            {{-- <a class="  section-header">{{$client->company}} --}}
            <a class="  section-header">{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}
            </a>
            <p style="font-size: 11pt !important;">{{date("d-M",
                strtotime(getFiscalYearEnd($client->fiscal_start)))}}-{{ $fyear }}</p>
        </div>


        <a class="  section-header">Trial Balance
        </a>
    </div>
    <div class="block-content pb-0   "
        style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

            <div class="col-sm-12">
                <div class="d-flex text-nowrap align-items-center">
                    <div>
                        <p class=" pr-1 mb-1 ">{{$fyear}}</p>
                    </div>
                    <hr class="w-100" style="border-color: #595959!important">
                </div>
                <div class="row">
                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                        <table class="table border-0 table-period">
                            <thead>
                                <tr>
                                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                    <td style="font-weight:600;padding:0;border:0 !important;"></td>

                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border:0 !important;">Debits</td>
                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border:0 !important;">Credits</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $account_debits =0 ;
                                    $account_credits =0;
                                    ?>
                                @foreach ($reports as $j)
                                <?php
                                 $total_debits = DB::table('journals')
                                    ->where('account_no', $j->account_no)
                                    ->where('fyear', $j->fyear)
                                    ->where('client', $j->client)
                                    ->where('is_deleted', 0)
                                    ->sum('debit');
                                    $total_credits = DB::table('journals')
                                    ->where('account_no', $j->account_no)
                                    ->where('fyear', $j->fyear)
                                    ->where('client', $j->client)
                                    ->where('is_deleted', 0)
                                    ->sum('credit');
                                   $debit = 0.00;
                                   $credit = 0.00;
                                   if($total_debits > $total_credits) {
                                    $debit = round($total_debits - $total_credits, 2);
                                   } else {
                                    $credit = round($total_credits - $total_debits, 2);
                                   }
                                   $account_debits += $debit;
                                   $account_credits += $credit;
                                ?>
                                <tr>
                                    <td style="padding:0;border:0 !important;">{{$j->account_no}}</td>
                                    <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">
                                        {{$debit == 0 ? '-' : number_format($debit,2, '.', '')}}
                                    </td>
                                    <td class="text-right" style="padding:0;border:0 !important;">
                                        {{$credit == 0 ? '-' : number_format($credit, 2, '.', '')}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="border-top: 0 !important;">

                                    </td>

                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border:0 !important;">

                                             <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($account_debits, 2, '.', '')}}</span>
                                            </div>
                                    </td>
                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border: 0 !important;">
                                        <div class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;width:fit-content;float:right;">
                                            <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($account_credits, 2, '.', '' )}}</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

    </div>



    <!-- page footer --->
    <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>


</div>
