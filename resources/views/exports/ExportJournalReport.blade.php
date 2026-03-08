<?php

function getFiscalYearEnd($fiscalStart, $filters)
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
    return $filters->fiscal_year . '-' . $fiscalMonth . '-' . $fiscalDay;
}

function findJournalIn($filters, $period, $account, $source)
{
    if ($period != '' && $account != '' && $source != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.period', $period)
                ->where('j.account_no', $account)
                ->where('j.source', $source)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    } elseif ($period != '' && $account != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.period', $period)
                ->where('j.account_no', $account)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    } elseif ($period != '' && $source != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.period', $period)
                ->where('j.source', $source)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    } elseif ($account != '' && $source != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.account_no', $account)
                ->where('j.source', $source)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    } elseif ($period != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.period', $period)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    } elseif ($account != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.account_no', $account)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    } elseif ($source != '') {
        if (
            DB::table('journals as j')
                ->where('j.is_deleted', 0)
                ->where('j.client', $filters->client_id)
                ->where('j.fyear', $filters->fiscal_year)
                ->where('j.source', $source)
                ->where(function ($query) use ($filters) {
                    if (count($filters->period) > 0) {
                        $query->whereIn('j.period', $filters->period);
                    }
                    if (count($filters->source) > 0) {
                        $query->whereIn('j.source', $filters->source);
                    }
                    if (count($filters->account) > 0) {
                        $query->whereIn('j.account_no', $filters->account);
                    }
                })
                ->Join('clients as c', function ($join) {
                    $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                })
                ->leftJoin('source_code as sc', function ($join) {
                    $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                })
                ->exists()
        ) {
            return true;
        }
    }
    return false;
}

$sourceFiltersApplied = $filters->source ?? [];
$accountFiltersApplied = $filters->account ?? [];
?>




<table>
    <thead>
        <tr>
            <td>{{ $client->company }}</td>
            <td>{{ date('d-M-Y', strtotime(getFiscalYearEnd($client->fiscal_start, $filters))) }}</td>
        </tr>
        <tr>
            <td colspan="2">Journal Report</td>
        </tr>
    </thead>
</table>




@if ($rollups == 'None')
    <?php
    $fiscal_year_debit = 0;
    $fiscal_year_credit = 0;
    ?>
    @for ($p = 0; $p < count($periods); $p++)
        <table>

            <thead>

                <tr>
                    <td>Period: {{ $periods[$p] }}</td>
                </tr>
            </thead>
        </table>

        <table class="table border-0 table-period">
            <thead>
                <tr>
                    <td style="font-weight:600;padding:0;border:0 !important;width: 34px !important">
                        EN
                    </td>
                    <td style="font-weight:600;padding:0;border:0 !important;">Src</td>
                    <td style="font-weight:600;padding:0;border:0 !important;">Per</td>
                    <td style="font-weight:600;padding:0;border:0 !important;width: 30px !important;">
                        Acct
                    </td>
                    <td style="font-weight:600;padding:0;border:0 !important;">Date
                    </td>
                    <td style="font-weight:600;padding:0;border:0 !important;">RefNo
                    </td>
                    <td style="font-weight:600;padding:0;border:0 !important;width: 131px !important;">
                        Description</td>
                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">DR
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">CR
                    </td>
                </tr>
            </thead>
            <tbody>
                <?php
                $period_level_debit = 0;
                $period_level_credit = 0;
                ?>
                @foreach (DB::table('journals as j')->where('j.is_deleted', 0)->where('j.client', $filters->client_id)->where('j.fyear', $filters->fiscal_year)->where('j.period', $periods[$p])->where(function ($query) use ($filters) {
            if (count($filters->period) > 0) {
                $query->whereIn('j.period', $filters->period);
            }
            if (count($filters->source) > 0) {
                $query->whereIn('j.source', $filters->source);
            }
            if (count($filters->account) > 0) {
                $query->whereIn('j.account_no', $filters->account);
            }
        })->Join('clients as c', function ($join) {
            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
        })->leftJoin('source_code as sc', function ($join) {
            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
        })->select('c.firstname', 'c.lastname', 'j.*', 'sc.source_code')->orderBy('j.editNo', 'asc')->get() as $j)
                    {{-- @if ($j->period == $periods[$p]) --}}
                    <?php
                    $period_level_debit += $j->debit;
                    $period_level_credit += $j->credit;
                    $fiscal_year_debit += $j->debit;
                    $fiscal_year_credit += $j->credit;
                    ?>
                    <tr>
                        <td style="padding:0;border:0 !important;float: left;">
                            {{ $j->editNo }}
                        </td>
                        <td style="padding:0;border:0 !important;">{{ $j->source_code }}</td>
                        <td style="padding:0;border:0 !important;">{{ @$periods[$p] }}</td>
                        <td style="padding:0;border:0 !important;">{{ $j->account_no }}</td>
                        <td style="padding:0;border:0 !important;">{{ $j->date }}</td>
                        <td style="padding:0;border:0 !important;">{{ $j->ref_no }}</td>
                        <td style="padding:0;border:0 !important;">{{ $j->description }}</td>
                        <td class="text-right" style="padding:0;border:0 !important;">
                            {{ number_format($j->debit, 2, '.', '') }}
                        </td>
                        <td class="text-right" style="padding:0;border:0 !important;">
                            {{ number_format($j->credit, 2, '.', '') }}
                        </td>
                    </tr>
                    {{-- @endif --}}
                @endforeach
            </tbody>
            <tbody>
                <tr>
                    <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">
                        Applied
                        Filters: Sources: @if (count($sourceFiltersApplied) > 0)
                            @foreach ($sources as $s)
                                {{ in_array($s->id, $sourceFiltersApplied) ? $s->source_code . ',' : '' }}
                            @endforeach
                        @endif
                        Accounts: @if (count($accountFiltersApplied) > 0)
                            @foreach ($accountFiltersApplied as $account_no)
                                {{ $account_no . ',' }}
                            @endforeach
                        @endif
                    </td>
                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        Period {{ $periods[$p] }} - Sub-Total</td>
                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        {{ number_format($period_level_debit, 2, '.', '') }}
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                        {{ number_format($period_level_credit, 2, '.', '') }}
                    </td>
                </tr>
                @if ($period_level_debit > $period_level_credit)
                    @php
                        $difference = round($period_level_debit - $period_level_credit, 2);
                    @endphp
                    @if ($difference > 0)
                        <tr>
                            <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                            </td>
                            <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                <span style="padding-top:2px;padding-bottom:2px;">
                                    {{ number_format($difference, 2, '.', '') }}</span>
                            </td>
                        </tr>
                    @endif
                @elseif ($period_level_debit < $period_level_credit)
                    @php
                        $difference = round($period_level_credit - $period_level_debit, 2);
                    @endphp
                    @if ($difference > 0)
                        <tr>
                            <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                            </td>
                            <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            </td>

                            <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                <span style="padding-top:2px;padding-bottom:2px;">
                                    {{ number_format($difference, 2, '.', '') }}</span>
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            </td>
                        </tr>
                    @endif
                @endif
            </tbody>
        </table>
    @endfor
    <table class="table border-0 table-totals">
        <tbody>
            <tr>

                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                    Fiscal Year
                    {{ date('d-M-Y', strtotime(getFiscalYearEnd($client->fiscal_start, $filters))) }}
                    Total
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                    {{ number_format($fiscal_year_debit, 2, '.', '') }}
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                    {{ number_format($fiscal_year_credit, 2, '.', '') }}
                </td>
            </tr>
            @if ($fiscal_year_debit > $fiscal_year_credit)
                @php
                    $difference_ = round($fiscal_year_debit - $fiscal_year_credit, 2);
                @endphp
                @if ($difference_ > 0)
                    <tr>
                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                        </td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span style="padding-top:2px;padding-bottom:2px;">
                                {{ number_format($difference_, 2, '.', '') }}</span>
                        </td>
                    </tr>
                @endif
            @elseif ($fiscal_year_debit < $fiscal_year_credit)
                @php
                    $difference_ = round($fiscal_year_credit - $fiscal_year_debit, 2);
                @endphp
                @if ($difference_ > 0)
                    <tr>
                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                        </td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        </td>

                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span style="padding-top:2px;padding-bottom:2px;">
                                {{ number_format($difference_, 2, '.', '') }}</span>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        </td>
                    </tr>
                @endif
            @endif

        </tbody>
    </table>
@elseif($rollups == 'Source')
    <?php
    $fiscal_year_debit = 0;
    $fiscal_year_credit = 0;
    ?>
    @foreach ($sources as $source)
        <?php
        $source_level_debit = 0;
        $source_level_credit = 0;
        ?>


        <table>
            <thead>
                <tr>
                    <td>Source: {{ $source->source_code }}</td>
                </tr>
            </thead>
        </table>


        @for ($p = 0; $p < count($periods); $p++)
            <?php
            $period_level_debit = 0;
            $period_level_credit = 0;
            ?>
            @if (findJournalIn($filters, $periods[$p], '', $source->id))
                <table>
                    <thead>

                        <tr>
                            <td>Period: {{ $periods[$p] }}</td>
                        </tr>
                    </thead>
                </table>

                <table class="table border-0 table-period">
                    <thead>
                        <tr>
                            <td style="font-weight:600;padding:0;border:0 !important;width: 34px !important">EN
                            </td>
                            <td style="font-weight:600;padding:0;border:0 !important;">Src
                            </td>
                            <td style="font-weight:600;padding:0;border:0 !important;">Per</td>
                            <td style="font-weight:600;padding:0;border:0 !important;width: 30px !important;">
                                Acct
                            </td>
                            <td style="font-weight:600;padding:0;border:0 !important;">Date
                            </td>
                            <td style="font-weight:600;padding:0;border:0 !important;">
                                RefNo
                            </td>
                            <td style="font-weight:600;padding:0;border:0 !important;width: 131px !important;">
                                Description</td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">DR
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">CR
                            </td>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach (DB::table('journals as j')->where('j.is_deleted', 0)->where('j.client', $filters->client_id)->where('j.fyear', $filters->fiscal_year)->where('j.period', $periods[$p])->where('j.source', $source->id)->where(function ($query) use ($filters) {
            if (count($filters->period) > 0) {
                $query->whereIn('j.period', $filters->period);
            }
            if (count($filters->source) > 0) {
                $query->whereIn('j.source', $filters->source);
            }
            if (count($filters->account) > 0) {
                $query->whereIn('j.account_no', $filters->account);
            }
        })->Join('clients as c', function ($join) {
            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
        })->leftJoin('source_code as sc', function ($join) {
            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
        })->select('c.firstname', 'c.lastname', 'j.*', 'sc.source_code')->orderBy('j.editNo', 'asc')->get() as $j)
                            {{-- @if ($periods[$p] == $j->period && $j->source == $source->id) --}}
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
                                    {{ $j->editNo }}
                                </td>
                                <td style="padding:0;border:0 !important;">{{ $j->source_code }}
                                </td>
                                <td style="padding:0;border:0 !important;">{{ @$periods[$p] }}</td>
                                <td style="padding:0;border:0 !important;">{{ $j->account_no }}
                                </td>
                                <td style="padding:0;border:0 !important;">{{ $j->date }}</td>
                                <td style="padding:0;border:0 !important;">{{ $j->ref_no }}</td>
                                <td style="padding:0;border:0 !important;">{{ $j->description }}
                                </td>
                                <td class="text-right" style="padding:0;border:0 !important;">
                                    {{ number_format($j->debit, 2, '.', '') }}</td>
                                <td class="text-right" style="padding:0;border:0 !important;">
                                    {{ number_format($j->credit, 2, '.', '') }}
                                </td>
                            </tr>
                            {{-- @endif --}}
                        @endforeach
                    </tbody>
                    <tbody>
                        <tr>
                            <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">
                                Applied Filters:
                                Sources: @if (count($sourceFiltersApplied) > 0)
                                    @foreach ($sources as $s)
                                        {{ in_array($s->id, $sourceFiltersApplied) ? $s->source_code . ',' : '' }}
                                    @endforeach
                                    @endif Accounts: @if (count($accountFiltersApplied) > 0)
                                        @foreach ($accountFiltersApplied as $account_no)
                                            {{ $account_no . ',' }}
                                        @endforeach
                                    @endif
                            </td>
                            <td style="font-weight:600;padding:0;border:0 !important;">
                                Period {{ $periods[$p] }} - Sub-Total
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                {{ number_format($period_level_debit, 2, '.', '') }}
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                {{ number_format($period_level_credit, 2, '.', '') }}
                            </td>
                        </tr>
                        @if ($period_level_debit > $period_level_credit)
                            @php
                                $difference = round($period_level_debit - $period_level_credit, 2);
                            @endphp
                            @if ($difference > 0)
                                <tr>
                                    <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                                    </td>
                                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                    </td>
                                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                    </td>
                                    <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                        <span style="padding-top:2px;padding-bottom:2px;">
                                            {{ number_format($difference, 2, '.', '') }}</span>
                                    </td>
                                </tr>
                            @endif
                        @elseif ($period_level_debit < $period_level_credit)
                            @php
                                $difference = round($period_level_credit - $period_level_debit, 2);
                            @endphp
                            @if ($difference > 0)
                                <tr>
                                    <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                                    </td>
                                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                    </td>

                                    <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                        <span style="padding-top:2px;padding-bottom:2px;">
                                            {{ number_format($difference, 2, '.', '') }}</span>
                                    </td>
                                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                    </td>
                                </tr>
                            @endif
                        @endif
                    </tbody>
                </table>
            @endif
        @endfor




        <table class="table border-0 table-totals">
            <tbody>
                <tr>

                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight:600;padding:0;border:0 !important;"></td>
                    <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        Source {{ $source->source_code }} - Sub-Total
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        {{ number_format($source_level_debit, 2, '.', '') }}
                    </td>
                    <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                        {{ number_format($source_level_credit, 2, '.', '') }}
                    </td>
                </tr>
                @if ($source_level_debit > $source_level_credit)
                    @php
                        $difference_ = round($source_level_debit - $source_level_credit, 2);
                    @endphp
                    @if ($difference_ > 0)
                        <tr>
                            <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                            </td>
                            <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                <span style="padding-top:2px;padding-bottom:2px;">
                                    {{ number_format($difference_, 2, '.', '') }}</span>
                            </td>
                        </tr>
                    @endif
                @elseif ($source_level_debit < $source_level_credit)
                    @php
                        $difference_ = round($source_level_credit - $source_level_debit, 2);
                    @endphp
                    @if ($difference_ > 0)
                        <tr>
                            <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                            </td>
                            <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            </td>

                            <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                <span style="padding-top:2px;padding-bottom:2px;">
                                    {{ number_format($difference_, 2, '.', '') }}</span>
                            </td>
                            <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            </td>
                        </tr>
                    @endif
                @endif
            </tbody>
        </table>
    @endforeach
    <table class="table border-0 table-totals">
        <tbody>
            <tr>

                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                    Fiscal Year
                    {{ date('d-M-Y', strtotime(getFiscalYearEnd($client->fiscal_start, $filters))) }}
                    Total
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                    {{ number_format($fiscal_year_debit, 2, '.', '') }}
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                    {{ number_format($fiscal_year_credit, 2, '.', '') }}
                </td>
            </tr>
            @if ($fiscal_year_debit > $fiscal_year_credit)
                @php
                    $difference_ = round($fiscal_year_debit - $fiscal_year_credit, 2);
                @endphp
                @if ($difference_ > 0)
                    <tr>
                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                        </td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span style="padding-top:2px;padding-bottom:2px;">
                                {{ number_format($difference_, 2, '.', '') }}</span>
                        </td>
                    </tr>
                @endif
            @elseif ($fiscal_year_debit < $fiscal_year_credit)
                @php
                    $difference_ = round($fiscal_year_credit - $fiscal_year_debit, 2);
                @endphp
                @if ($difference_ > 0)
                    <tr>
                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                        </td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        </td>

                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span style="padding-top:2px;padding-bottom:2px;">
                                {{ number_format($difference_, 2, '.', '') }}</span>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        </td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>
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
        @if (findJournalIn($filters, '', '', $source->id))
            <table>
                <thead>
                    <tr>
                        <td>Source: {{ $source->source_code }}</td>
                    </tr>
                </thead>
            </table>


            @foreach ($accounts as $account)
                <?php
                $account_level_debit = 0;
                $account_level_credit = 0;
                ?>
                @if (findJournalIn($filters, '', $account->account_no, $source->id))
                    <table>
                        <thead>
                            <tr>
                                <td>{{ $account->account_no }}</td>
                            </tr>
                        </thead>
                    </table>



                    @for ($p = 0; $p < count($periods); $p++)
                        <?php
                        $period_level_debit = 0;
                        $period_level_credit = 0;
                        ?>
                        @if (findJournalIn($filters, $periods[$p], $account->account_no, $source->id))
                            <table>
                                <thead>
                                    <tr>

                                        <td>Period: {{ $periods[$p] }}</td>
                                    </tr>
                                </thead>
                            </table>


                            <table class="table border-0 table-period">
                                <thead>
                                    <tr>
                                        <td
                                            style="font-weight:600;padding:0;border:0 !important;width: 34px !important">
                                            EN</td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">
                                            Src</td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">Per</td>
                                        <td
                                            style="font-weight:600;padding:0;border:0 !important;width: 30px !important;">
                                            Acct</td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">
                                            Date</td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">
                                            RefNo</td>
                                        <td
                                            style="font-weight:600;padding:0;border:0 !important;width: 131px !important;">
                                            Description</td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                            DR</td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                            CR</td>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach (DB::table('journals as j')->where('j.is_deleted', 0)->where('j.client', $filters->client_id)->where('j.fyear', $filters->fiscal_year)->where('j.period', $periods[$p])->where('j.source', $source->id)->where('j.account_no', $account->account_no)->where(function ($query) use ($filters) {
            if (count($filters->period) > 0) {
                $query->whereIn('j.period', $filters->period);
            }
            if (count($filters->source) > 0) {
                $query->whereIn('j.source', $filters->source);
            }
            if (count($filters->account) > 0) {
                $query->whereIn('j.account_no', $filters->account);
            }
        })->Join('clients as c', function ($join) {
            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
        })->leftJoin('source_code as sc', function ($join) {
            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
        })->select('c.firstname', 'c.lastname', 'j.*', 'sc.source_code')->orderBy('j.editNo', 'asc')->get() as $j)
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
                                                {{ $j->editNo }}
                                            </td>
                                            <td style="padding:0;border:0 !important;">
                                                {{ $j->source_code }}</td>
                                            <td style="padding:0;border:0 !important;">{{ @$periods[$p] }}</td>
                                            <td style="padding:0;border:0 !important;">
                                                {{ $j->account_no }}</td>
                                            <td style="padding:0;border:0 !important;">{{ $j->date }}
                                            </td>
                                            <td style="padding:0;border:0 !important;">{{ $j->ref_no }}
                                            </td>
                                            <td style="padding:0;border:0 !important;">
                                                {{ $j->description }}</td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{ number_format($j->debit, 2, '.', '') }}</td>
                                            <td class="text-right" style="padding:0;border:0 !important;">
                                                {{ number_format($j->credit, 2, '.', '') }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">
                                            Applied Filters:
                                            Sources: @if (count($sourceFiltersApplied) > 0)
                                                @foreach ($sources as $s)
                                                    {{ in_array($s->id, $sourceFiltersApplied) ? $s->source_code . ',' : '' }}
                                                @endforeach
                                            @endif
                                            <br>
                                            Accounts: @if (count($accountFiltersApplied) > 0)
                                                @foreach ($accountFiltersApplied as $account_no)
                                                    {{ $account_no . ',' }}
                                                @endforeach
                                            @endif
                                        </td>
                                        <td style="font-weight:600;padding:0;border:0 !important;">
                                            Period {{ $periods[$p] }} - Sub-Total
                                        </td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                            {{ number_format($period_level_debit, 2, '.', '') }}
                                        </td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                            {{ number_format($period_level_credit, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    @if ($period_level_debit > $period_level_credit)
                                        @php
                                            $difference = round($period_level_debit - $period_level_credit, 2);
                                        @endphp
                                        @if ($difference > 0)
                                            <tr>
                                                <td colspan="6"
                                                    style="font-weight:600;padding:0;border:0 !important;">

                                                </td>
                                                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">
                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">
                                                    <span style="padding-top:2px;padding-bottom:2px;">
                                                        {{ number_format($difference, 2, '.', '') }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @elseif ($period_level_debit < $period_level_credit)
                                        @php
                                            $difference = round($period_level_credit - $period_level_debit, 2);
                                        @endphp
                                        @if ($difference > 0)
                                            <tr>
                                                <td colspan="6"
                                                    style="font-weight:600;padding:0;border:0 !important;">

                                                </td>
                                                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                                </td>

                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border: 0 !important;">
                                                    <span style="padding-top:2px;padding-bottom:2px;">
                                                        {{ number_format($difference, 2, '.', '') }}</span>
                                                </td>
                                                <td class="text-right"
                                                    style="font-weight:600;padding:0;border:0 !important;">
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        @endif
                    @endfor

                    <table class="table border-0 table-totals">
                        <tbody>
                            <tr>
                                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                    Account {{ $account->account_no }} - Sub-Total
                                </td>
                                <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                    {{ number_format($account_level_debit, 2, '.', '') }}
                                </td>
                                <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                                    {{ number_format($account_level_credit, 2, '.', '') }}
                                </td>
                            </tr>
                            @if ($account_level_debit > $account_level_credit)
                                @php
                                    $difference_ = round($account_level_debit - $account_level_credit, 2);
                                @endphp
                                @if ($difference_ > 0)
                                    <tr>
                                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                                        </td>
                                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                        </td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                        </td>
                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span style="padding-top:2px;padding-bottom:2px;">
                                                {{ number_format($difference_, 2, '.', '') }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @elseif ($account_level_debit < $account_level_credit)
                                @php
                                    $difference_ = round($account_level_credit - $account_level_debit, 2);
                                @endphp
                                @if ($difference_ > 0)
                                    <tr>
                                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                                        </td>
                                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                                        </td>

                                        <td class="text-right"
                                            style="font-weight:600;padding:0;border: 0 !important;">
                                            <span style="padding-top:2px;padding-bottom:2px;">
                                                {{ number_format($difference_, 2, '.', '') }}</span>
                                        </td>
                                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        </tbody>
                    </table>
                @endif
            @endforeach

            {{-- <table class="table border-0 table-totals">
                <tbody>
                    <tr>

                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight:600;padding:0;border:0 !important;"></td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                            Source {{ $source->source_code }} - Sub-Total</td>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                            {{ number_format($source_level_debit, 2, '.', '') }}
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            {{ number_format($source_level_credit, 2, '.', '') }}
                        </td>
                    </tr>
                </tbody>
            </table> --}}
        @endif
    @endforeach
    <table class="table border-0 table-totals">
        <tbody>
            <tr>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>
                <td style="font-weight:600;padding:0;border:0 !important;"></td>

                <td style="font-weight: 600;padding:0;border:0" class="text-right">
                    Fiscal Year
                    {{ date('d-M-Y', strtotime(getFiscalYearEnd($client->fiscal_start, $filters))) }}
                    Total
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                    {{ number_format($fiscal_year_debit, 2, '.', '') }}
                </td>
                <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                    {{ number_format($fiscal_year_credit, 2, '.', '') }}
                </td>
            </tr>
            @if ($fiscal_year_debit > $fiscal_year_credit)
                @php
                    $difference_ = round($fiscal_year_debit - $fiscal_year_credit, 2);
                @endphp
                @if ($difference_ > 0)
                    <tr>
                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                        </td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span style="padding-top:2px;padding-bottom:2px;">
                                {{ number_format($difference_, 2, '.', '') }}</span>
                        </td>
                    </tr>
                @endif
            @elseif ($fiscal_year_debit < $fiscal_year_credit)
                @php
                    $difference_ = round($fiscal_year_credit - $fiscal_year_debit, 2);
                @endphp
                @if ($difference_ > 0)
                    <tr>
                        <td colspan="6" style="font-weight:600;padding:0;border:0 !important;">

                        </td>
                        <td style="font-weight: 600;padding:0;border:0" class="text-right">
                        </td>

                        <td class="text-right" style="font-weight:600;padding:0;border: 0 !important;">
                            <span style="padding-top:2px;padding-bottom:2px;">
                                {{ number_format($difference_, 2, '.', '') }}</span>
                        </td>
                        <td class="text-right" style="font-weight:600;padding:0;border:0 !important;">
                        </td>
                    </tr>
                @endif
            @endif
        </tbody>
    </table>
@endif
