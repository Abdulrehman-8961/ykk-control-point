
<?php

function getFiscalYearEnd($fiscalStart, $filters)
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
        return $filters->fiscal_year . '-' . $fiscalMonth . '-' . $fiscalDay ;
    }



function findJournalIn($filters, $period, $account, $source)
{
    if($period != '' && $account != '' && $source != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.period', $period)
            ->where('j.account_no', $account)
            ->where('j.source', $source)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }
    } else if($period != '' && $account != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.period', $period)
            ->where('j.account_no', $account)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }
    } else if($period != '' && $source != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.period', $period)
            ->where('j.source', $source)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }

    } else if($account != '' && $source != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.account_no', $account)
            ->where('j.source', $source)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }
    } else if($period != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.period', $period)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }

    } else if($account != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.account_no', $account)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }
    } else if($source != '') {
        if(DB::table('journals as j')
            ->where('j.is_deleted', 0)
            ->where("j.client", $filters->client_id)
            ->where("j.fyear", $filters->fiscal_year)
            ->where('j.source', $source)
            ->where(function ($query) use ($filters) {
                if (count($filters->period) > 0) {
                   $query->whereIn("j.period", $filters->period);
                }
                if (count($filters->source) > 0) {
                      $query->whereIn("j.source", $filters->source);
                }
                if (count($filters->account) > 0) {
                     $query->whereIn("j.account_no", $filters->account);
                }
            })
            ->Join("clients as c", function ($join) {
                $join->on("j.client", "=", "c.id")
                    ->where("c.is_deleted", 0);
            })
            ->leftJoin("source_code as sc", function ($join) {
                $join->on("j.source", "=", "sc.id")
                    ->where("sc.is_deleted", 0);
            })
            ->exists()) {
                return true;
            }
    }
    return false;
}


    $sourceFiltersApplied = $filters->source ?? [];
    $accountFiltersApplied = $filters->account ?? [];
?>



<div class="block new-block position-relative  5">
    <div class="block-header py-0 d-flex justify-content-between align-items-start"
        style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

        <div>
            <a class="  section-header">{{$client->company}}
            </a>
            <p style="font-size: 11pt !important;">{{date("d-M-Y",
                strtotime(getFiscalYearEnd($client->fiscal_start, $filters)))}}</p>
        </div>


        <a class="  section-header">Journal Report
        </a>
    </div>
    <div class="block-content pb-0   "
        style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

        @if($rollups == 'None')
        <?php
            $fiscal_year_debit = 0;
            $fiscal_year_credit = 0;
        ?>
        @for ($p=0;$p<count($periods);$p++) <div class="row">

            <div class="col-sm-12">
                <div class="d-flex text-nowrap">
                    <div>
                        <p class=" pr-1 mb-0 ">Period: {{$periods[$p]}}</p>
                    </div>
                    <hr class="w-100" style="border-color: #595959!important">
                </div>
                <div class="row">
                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                        <table class="table border-0 table-period">
                            <thead>
                                <tr>
                                    <td style="font-weight:600;padding:0;border:0 !important;width: 34px !important">
                                        EN
                                    </td>
                                    <td style="font-weight:600;padding:0;border:0 !important;">Src</td>
                                    <td style="font-weight:600;padding:0;border:0 !important;width: 30px !important;">Acct
                                    </td>
                                    <td style="font-weight:600;padding:0;border:0 !important;">Date
                                    </td>
                                    <td style="font-weight:600;padding:0;border:0 !important;">RefNo
                                    </td>
                                    <td style="font-weight:600;padding:0;border:0 !important;width: 131px !important;">
                                        Description</td>
                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border:0 !important;">DR</td>
                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border:0 !important;">CR</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $period_level_debit =0 ;
                                    $period_level_credit =0;
                                    ?>
                                @foreach (DB::table('journals as j')
                                ->where('j.is_deleted', 0)
                                ->where("j.client", $filters->client_id)
                                ->where("j.fyear", $filters->fiscal_year)
                                ->where('j.period', $periods[$p])
                                ->where(function ($query) use ($filters) {
                                    if (count($filters->period) > 0) {
                                       $query->whereIn("j.period", $filters->period);
                                    }
                                    if (count($filters->source) > 0) {
                                          $query->whereIn("j.source", $filters->source);
                                    }
                                    if (count($filters->account) > 0) {
                                         $query->whereIn("j.account_no", $filters->account);
                                    }
                                })
                                ->Join("clients as c", function ($join) {
                                    $join->on("j.client", "=", "c.id")
                                        ->where("c.is_deleted", 0);
                                })
                                ->leftJoin("source_code as sc", function ($join) {
                                    $join->on("j.source", "=", "sc.id")
                                        ->where("sc.is_deleted", 0);
                                })
                                ->select("c.firstname", "c.lastname", "j.*", "sc.source_code")
                                ->orderBy('j.editNo', 'asc')
                                ->get() as $j)
                                {{-- @if($j->period == $periods[$p]) --}}
                                <?php
                                    $period_level_debit += $j->debit;
                                    $period_level_credit += $j->credit;
                                    $fiscal_year_debit += $j->debit;
                                    $fiscal_year_credit += $j->credit;
                                ?>
                                <tr>
                                    <td style="padding:0;border:0 !important;float: left;">
                                        <div style="text-align: right;margin-right: 15px !important">
                                            {{$j->editNo}}
                                        </div>
                                    </td>
                                    <td style="padding:0;border:0 !important;">{{$j->source_code}}</td>
                                    <td style="padding:0;border:0 !important;">{{$j->account_no}}</td>
                                    <td style="padding:0;border:0 !important;">{{$j->date}}</td>
                                    <td style="padding:0;border:0 !important;">{{$j->ref_no}}</td>
                                    <td style="padding:0;border:0 !important;">{{$j->description}}</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">
                                        {{number_format($j->debit,2)}}</td>
                                    <td class="text-right" style="padding:0;border:0 !important;">
                                        {{number_format($j->credit, 2)}}
                                    </td>
                                </tr>
                                {{-- @endif --}}
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"
                                        style="font-weight:600;padding:0;border:0 !important;">
                                        <p class="mb-0 filter-applied">Applied
                                            Filters:</p>
                                        <span class="tags">Sources: @if (count($sourceFiltersApplied) > 0) @foreach($sources as
                                            $s){{in_array($s->id, $sourceFiltersApplied) ? $s->source_code . ',' : ''}} @endforeach @endif</span><br>
                                        <span class="tags">Accounts: @if (count($accountFiltersApplied) > 0) @foreach ($accountFiltersApplied as $account_no)
                                            {{$account_no . ','}}
                                            @endforeach @endif</span>
                                    </td>
                                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                        Period {{$periods[$p]}} - Sub-Total</td>
                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border:0 !important;">
                                        <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($period_level_debit,
                                            2)}}</span>
                                    </td>
                                    <td class="text-right"
                                        style="font-weight:600;padding:0;border: 0 !important;">
                                        <span
                                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                            {{number_format($period_level_credit, 2)}}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

    </div>

    @endfor
    <div class="row">
        <div class="col-sm-12" style="padding-left: 4rem !important;">
            <table class="table border-0 table-totals">
                <tfoot>
                    <tr>
                        {{-- <td colspan="5" style="font-weight:600;padding:0;border:0 !important;">

                        </td> --}}
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            Fiscal Year {{date("d-M-Y",
                            strtotime(getFiscalYearEnd($client->fiscal_start, $filters)))}} Total</td>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            <span class=""
                                style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                                <span
                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($fiscal_year_debit,
                                    2)}}</span>
                            </span>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span class=""
                                style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                                <span
                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                    {{number_format($fiscal_year_credit, 2)}}</span>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @elseif($rollups == 'Source')
    <?php
        $fiscal_year_debit = 0;
        $fiscal_year_credit =0;
    ?>
    @foreach($sources as $source)
    <?php
        $source_level_debit = 0;
        $source_level_credit = 0;
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex text-nowrap">
                <div>
                    <p class=" pr-1 mb-0">Source: {{$source->source_code}}</p>
                </div>
                <hr class="w-100" style="border-color: #595959!important">
            </div>
            @for ($p=0;$p<count($periods);$p++ )
                <?php
                    $period_level_debit =0;
                    $period_level_credit =0;
                    ?>
                    @if(findJournalIn($filters, $periods[$p], '', $source->id))
                    <div class="row">
                <div class="col-sm-12 pl-5">
                    <div class="d-flex text-nowrap">
                        <div>
                            <p class=" pr-1 mb-0">Period: {{$periods[$p]}}</p>
                        </div>
                        <hr class="w-100" style="border-color: #595959!important">
                    </div>
                    <div class="row">
                        <div class="col-sm-12 " style="    padding-left: 4rem!important;">
                            <table class="table border-0 table-period">
                                <thead>
                                    <tr>
                                        <td style="font-weight:600;padding:0;border:0 !important;width: 34px !important">EN
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">Src
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;width: 30px !important;">Acct
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">Date
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">
                                            RefNo
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;width: 131px !important;">
                                            Description</td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">DR
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">CR
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach (DB::table('journals as j')
                                    ->where('j.is_deleted', 0)
                                    ->where("j.client", $filters->client_id)
                                    ->where("j.fyear", $filters->fiscal_year)
                                    ->where('j.period', $periods[$p])
                                    ->where('j.source', $source->id)
                                    ->where(function ($query) use ($filters) {
                                        if (count($filters->period) > 0) {
                                           $query->whereIn("j.period", $filters->period);
                                        }
                                        if (count($filters->source) > 0) {
                                              $query->whereIn("j.source", $filters->source);
                                        }
                                        if (count($filters->account) > 0) {
                                             $query->whereIn("j.account_no", $filters->account);
                                        }
                                    })
                                    ->Join("clients as c", function ($join) {
                                        $join->on("j.client", "=", "c.id")
                                            ->where("c.is_deleted", 0);
                                    })
                                    ->leftJoin("source_code as sc", function ($join) {
                                        $join->on("j.source", "=", "sc.id")
                                            ->where("sc.is_deleted", 0);
                                    })
                                    ->select("c.firstname", "c.lastname", "j.*", "sc.source_code")
                                    ->orderBy('j.editNo', 'asc')
                                    ->get() as $j)
                                    {{-- @if($periods[$p] == $j->period && $j->source == $source->id) --}}
                                    <?php
                                        $fiscal_year_debit += $j->debit;
                                        $fiscal_year_credit += $j->credit;
                                        $source_level_debit += $j->debit;
                                        $source_level_credit += $j->credit;
                                        $period_level_debit += $j->debit;
                                        $period_level_credit += $j->credit;
                                    ?>
                                    <tr>
                                        <td style="padding:0;border:0 !important;float: left">
                                            <div style="text-align: right;margin-right: 15px !important">
                                                {{$j->editNo}}
                                            </div>
                                        </td>
                                        <td style="padding:0;border:0 !important;">{{$j->source_code}}
                                        </td>
                                        <td style="padding:0;border:0 !important;">{{$j->account_no}}
                                        </td>
                                        <td style="padding:0;border:0 !important;">{{$j->date}}</td>
                                        <td style="padding:0;border:0 !important;">{{$j->ref_no}}</td>
                                        <td style="padding:0;border:0 !important;">{{$j->description}}
                                        </td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{number_format($j->debit, 2)}}</td>
                                        <td class="text-right" style="padding:0;border:0 !important;">
                                            {{number_format($j->credit, 2)}}
                                        </td>
                                    </tr>
                                    {{-- @endif --}}
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5"
                                            style="font-weight:600;padding:0;border:0 !important;">
                                            <p class="mb-0 filter-applied">Applied Filters:</p>
                                            <span class="tags">Sources: @if (count($sourceFiltersApplied) > 0) @foreach($sources as
                                                $s){{in_array($s->id, $sourceFiltersApplied) ? $s->source_code.',' : ''}} @endforeach @endif</span><br>
                                            <span class="tags">Accounts: @if(count($accountFiltersApplied) > 0) @foreach ($accountFiltersApplied as
                                                $account_no)
                                                {{$account_no. ','}}
                                                @endforeach @endif</span>
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">
                                            Period {{$periods[$p]}} - Sub-Total
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">
                                            <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($period_level_debit,
                                                2)}}</span>
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border:0 !important;">
                                            <span
                                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($period_level_credit,
                                                2)}}</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

        </div>
        @endif
        @endfor
    </div>
</div>
<div class="row">
    <div class="col-sm-12" style="padding-left: 6rem !important;">
        <table class="table border-0 table-totals">
            <tfoot>
                <tr>
                    {{-- <td colspan="5" style="font-weight:600;padding:0;border:0 !important;">

                    </td> --}}
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        Source {{$source->source_code}} - Sub-Total</td>
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        <span class="" style="border: 0;">
                            <span
                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($source_level_debit,
                                2)}}</span>
                        </span>
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                        <span class="" style="border: 0;">
                            <span
                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                {{number_format($source_level_credit, 2)}}</span>
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endforeach
<div class="row">
    <div class="col-sm-12" style="padding-left: 6rem !important;">
        <table class="table border-0 table-totals">
            <tfoot>
                <tr>
                    {{-- <td colspan="5" style="font-weight:600;padding:0;border:0 !important;">

                    </td> --}}
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        Fiscal Year {{date("d-M-Y",
                        strtotime(getFiscalYearEnd($client->fiscal_start, $filters)))}} Total</td>
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                            <span
                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($fiscal_year_debit,
                                2)}}</span>
                        </span>
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                        <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                            <span
                                style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                {{number_format($fiscal_year_credit, 2)}}</span>
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@elseif($rollups == 'Account')
<?php
    $fiscal_year_debit = 0;
    $fiscal_year_credit = 0;
?>
@foreach ($sources as $source)
<?php
    $source_level_debit = 0;
    $source_level_credit = 0;
?>
@if(findJournalIn($filters, '', '', $source->id))
<div class="row ">
    <div class="col-sm-12 pl-5">
        <div class="d-flex text-nowrap">
            <div>
                <p class=" pr-1 mb-0">Source: {{$source->source_code}}</p>
            </div>
            <hr class="w-100" style="border-color: #595959!important">
        </div>
        @foreach ($accounts as $account)
        <?php
            $account_level_debit = 0;
            $account_level_credit = 0;
        ?>
        @if(findJournalIn($filters, '', $account->account_no, $source->id))
        <div class="row">
            <div class="col-sm-12 pl-5">
                <div class="d-flex text-nowrap">
                    <div>
                        <p class="pr-1 mb-0">{{$account->account_no}}</p>
                    </div>
                    <hr class="w-100" style="border-color: #595959!important">
                </div>
                @for($p=0;$p<count($periods);$p++)
                    <?php
                        $period_level_debit =0;
                        $period_level_credit =0;
                        ?>
                        @if(findJournalIn($filters, $periods[$p], $account->account_no, $source->id))
                        <div class="row">
                    <div class="col-sm-12 pl-5">
                        <div class="d-flex text-nowrap">
                            <div>
                                <p class=" pr-1 mb-0 ">Period: {{$periods[$p]}}</p>
                            </div>
                            <hr class="w-100" style="border-color: #595959!important">
                        </div>
                        <div class="row">
                            <div class="col-sm-12 " style="    padding-left: 4rem!important;">
                                <table class="table border-0 table-period">
                                    <thead>
                                        <tr>
                                            <td style="font-weight:600;padding:0;border:0 !important;width: 34px !important">
                                                EN</td>
                                            <td style="font-weight:600;padding:0;border:0 !important;">
                                                Src</td>
                                            <td style="font-weight:600;padding:0;border:0 !important;width: 30px !important;">
                                                Acct</td>
                                            <td style="font-weight:600;padding:0;border:0 !important;">
                                                Date</td>
                                            <td style="font-weight:600;padding:0;border:0 !important;">
                                                RefNo</td>
                                            <td style="font-weight:600;padding:0;border:0 !important;width: 131px !important;">
                                                Description</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                DR</td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                CR</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach (DB::table('journals as j')
                                        ->where('j.is_deleted', 0)
                                        ->where("j.client", $filters->client_id)
                                        ->where("j.fyear", $filters->fiscal_year)
                                        ->where('j.period', $periods[$p])
                                        ->where('j.source', $source->id)
                                        ->where('j.account_no', $account->account_no)
                                        ->where(function ($query) use ($filters) {
                                            if (count($filters->period) > 0) {
                                               $query->whereIn("j.period", $filters->period);
                                            }
                                            if (count($filters->source) > 0) {
                                                  $query->whereIn("j.source", $filters->source);
                                            }
                                            if (count($filters->account) > 0) {
                                                 $query->whereIn("j.account_no", $filters->account);
                                            }
                                        })
                                        ->Join("clients as c", function ($join) {
                                            $join->on("j.client", "=", "c.id")
                                                ->where("c.is_deleted", 0);
                                        })
                                        ->leftJoin("source_code as sc", function ($join) {
                                            $join->on("j.source", "=", "sc.id")
                                                ->where("sc.is_deleted", 0);
                                        })
                                        ->select("c.firstname", "c.lastname", "j.*", "sc.source_code")
                                        ->orderBy('j.editNo', 'asc')
                                        ->get() as $j)
                                        {{-- @if($j->period == $periods[$p] && $j->account_no ==
                                        $account->account_no
                                        && $j->source == $source->id) --}}
                                        <?php
                                            $fiscal_year_debit += $j->debit;
                                            $fiscal_year_credit += $j->credit;
                                            $source_level_debit += $j->debit;
                                            $source_level_credit += $j->credit;
                                            $account_level_debit += $j->debit;
                                            $account_level_credit += $j->credit;
                                            $period_level_debit += $j->debit;
                                            $period_level_credit += $j->credit;
                                        ?>
                                        <tr>
                                            <td style="padding:0;border:0 !important;float: left;">
                                                <div style="text-align: right;margin-right: 15px !important">
                                                    {{$j->editNo}}
                                                </div>
                                            </td>
                                            <td style="padding:0;border:0 !important;">
                                                {{$j->source_code}}</td>
                                            <td style="padding:0;border:0 !important;">
                                                {{$j->account_no}}</td>
                                            <td style="padding:0;border:0 !important;">{{$j->date}}</td>
                                            <td style="padding:0;border:0 !important;">{{$j->ref_no}}
                                            </td>
                                            <td style="padding:0;border:0 !important;">
                                                {{$j->description}}</td>
                                            <td class="text-right"
                                                style="padding:0;border:0 !important;">
                                                {{number_format($j->debit, 2)}}</td>
                                            <td class="text-right"
                                                style="padding:0;border:0 !important;">
                                                {{number_format($j->credit, 2)}}</td>
                                        </tr>
                                        {{-- @endif --}}
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                <p class="mb-0 filter-applied">Applied Filters:</p>
                                                <span class="tags">Sources: @if (count($sourceFiltersApplied) > 0) @foreach($sources as
                                                    $s){{in_array($s->id, $sourceFiltersApplied) ? $s->source_code.',' : ''}} @endforeach @endif</span><br>
                                                <span class="tags">Accounts: @if (count($accountFiltersApplied) > 0) @foreach ($accountFiltersApplied
                                                    as
                                                    $account_no)
                                                    {{$account_no. ','}}
                                                    @endforeach @endif</span>
                                            </td>
                                            <td style="font-weight:600;padding:0;border:0 !important;">
                                                Period {{$periods[$p]}} - Sub-Total
                                            </td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($period_level_debit,
                                                    2)}}</span>
                                            </td>
                                            <td class="text-right"
                                                style="font-weight:600;padding:0;border:0 !important;">
                                                <span
                                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($period_level_credit,
                                                    2)}}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

            </div>
            @endif
            @endfor
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 " style="padding-left: 8rem !important;">
            <table class="table border-0 table-totals">
                <tfoot>
                    <tr>
                        {{-- <td colspan="5" style="font-weight:600;padding:0;border:0 !important;">

                        </td> --}}
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            Account {{$account->account_no}} - Sub-Total</td>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            <span class="" style="border: 0;">
                                <span
                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($account_level_debit,
                                    2)}}</span>
                            </span>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span class="" style="border: 0;">
                                <span
                                    style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                                    {{number_format($account_level_credit, 2)}}</span>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
    @endforeach
</div>
</div>
<div class="row">
<div class="col-sm-12 " style="padding-left: 10rem !important;">
    <table class="table border-0 table-totals">
        <tfoot>
            <tr>
                {{-- <td colspan="5" style="font-weight:600;padding:0;border:0 !important;">

                </td> --}}
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                    Source {{$source->source_code}} - Sub-Total</td>
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                    <span class="" style="border: 0;">
                        <span
                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($source_level_debit,
                            2)}}</span>
                    </span>
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                    <span class="" style="border: 0;">
                        <span
                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                            {{number_format($source_level_credit, 2)}}</span>
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
</div>
@endif
@endforeach
<div class="row">
<div class="col-sm-12 " style="padding-left: 10rem !important;">
    <table class="table border-0 table-totals">
        <tfoot>
            <tr>
                {{-- <td colspan="5" style="font-weight:600;padding:0;border:0 !important;">

                </td> --}}
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>

                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                    Fiscal Year {{date("d-M-Y",
                    strtotime(getFiscalYearEnd($client->fiscal_start, $filters)))}} Total</td>
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                    <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                        <span
                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">{{number_format($fiscal_year_debit,
                            2)}}</span>
                    </span>
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                    <span class="" style="border-bottom: 2px solid #595959;padding-bottom: 5px;">
                        <span
                            style="border-top:1px solid #595959 !important;border-bottom:1px solid #595959 !important;padding-top:2px;padding-bottom:2px;">
                            {{number_format($fiscal_year_credit, 2)}}</span>
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
</div>
@endif
</div>
