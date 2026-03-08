@php
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

    function getMonth($monthNumber)
    {
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        if (array_key_exists($monthNumber, $monthNames)) {
            return $monthNames[$monthNumber];
        } else {
            return 'Invalid month number';
        }
    }

    $total_debit = 0;
    $total_credit = 0;

    $client = DB::table('clients')->where('is_deleted', 0)->where('id', $client_id)->first();
    $fiscal_start = $client->fiscal_start;
    $parts = explode('-', $fiscal_start);
    $month = intval($parts[1]);

    $qry = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->leftJoin('clients as c', function ($join) {
            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
        })
        ->leftJoin('source_code as sc', function ($join) {
            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
        })
        ->select('j.*', 'c.firstname', 'c.lastname', 'c.display_name', 'c.logo', 'sc.source_code', 'c.company')
        ->where('j.fyear', $fiscal_year)
        ->where('j.client', $client_id)
        ->get();
    foreach ($qry as $q) {
        if ($q->credit > $q->debit) {
            $total_credit += $q->credit;
        } else {
            $total_debit += $q->debit;
        }
    }

@endphp
<style>
    /* Customizing the scrollbar for Webkit browsers (Chrome, Edge, Safari) */
    #showData::-webkit-scrollbar,
    .block.new-block::-webkit-scrollbar {
        width: 8px;
        padding-right: 10px;
        /* Width of the scrollbar */
    }

    #showData::-webkit-scrollbar-track,
    .block.new-block::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    #showData::-webkit-scrollbar-thumb,
    .block.new-block::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    #showData::-webkit-scrollbar-thumb:hover,
    .block.new-block::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* For Firefox */
    #showData,
    .block.new-block {
        scrollbar-color: #888 #f1f1f1;
    }

    a:hover {
        color: #595959;
        text-decoration: none;
    }
</style>
<div class="row px-0 " id="viewDiv">
    <div class="col-lg-12" id="showData">
        <div class="block new-block position-relative data-container"
            style="overflow-y: auto; padding-top: 3mm !important; max-height: 60vh;">
            @for ($p = 1; $p <= 12; $p++)
                @php
                $column_no = 9;
                    $period = $p;
                    $journal_details = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->leftJoin('clients as c', function ($join) {
                            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                        })
                        ->leftJoin('source_code as sc', function ($join) {
                            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                        })
                        ->select('j.*')
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $period)
                        ->first();
                    $cd = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->where('j.source', 3)
                        ->get();
                    $cod = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->where('j.source', 21)
                        ->get();
                    $sj = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->where('j.source', 5)
                        ->get();
                    $ps = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->where('j.source', 4)
                        ->get();
                    $gj = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->where('j.source', 23)
                        ->get();
                    $adj = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->where('j.source', 20)
                        ->get();
                    $credit = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->leftJoin('clients as c', function ($join) {
                            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                        })
                        ->leftJoin('source_code as sc', function ($join) {
                            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                        })
                        ->where('j.credit', '>', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->count();
                    $debit = DB::table('journals as j')
                        ->where('j.is_deleted', 0)
                        ->leftJoin('clients as c', function ($join) {
                            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
                        })
                        ->leftJoin('source_code as sc', function ($join) {
                            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
                        })
                        ->where('j.debit', '>', 0)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.client', $client_id)
                        ->where('j.period', $p)
                        ->count();
                    $client = DB::table('clients')->where('id', $client_id)->first();
                    $total_credit_cd = 0;
                    $total_credit_cod = 0;
                    $total_credit_sj = 0;
                    $total_credit_ps = 0;
                    $total_credit_gj = 0;
                    $total_credit_adj = 0;
                    $total_debit_cd = 0;
                    $total_debit_cod = 0;
                    $total_debit_sj = 0;
                    $total_debit_ps = 0;
                    $total_debit_gj = 0;
                    $total_debit_adj = 0;
                    foreach ($cd as $c) {
                        $total_credit_cd += $c->credit;
                    }
                    foreach ($cd as $c) {
                        $total_debit_cd += $c->debit;
                    }
                    foreach ($cod as $c) {
                        $total_credit_cod += $c->credit;
                    }
                    foreach ($cod as $c) {
                        $total_debit_cod += $c->debit;
                    }
                    foreach ($sj as $c) {
                        $total_credit_sj += $c->credit;
                    }
                    foreach ($sj as $c) {
                        $total_debit_sj += $c->debit;
                    }
                    foreach ($ps as $c) {
                        $total_credit_ps += $c->credit;
                    }
                    foreach ($ps as $c) {
                        $total_debit_ps += $c->debit;
                    }
                    foreach ($gj as $c) {
                        $total_credit_gj += $c->credit;
                    }
                    foreach ($gj as $c) {
                        $total_debit_gj += $c->debit;
                    }
                    foreach ($adj as $c) {
                        $total_credit_adj += $c->credit;
                    }
                    foreach ($adj as $c) {
                        $total_debit_adj += $c->debit;
                    }
                    $excludedIds = [3, 21, 5, 4, 23, 20];
                    $extra = DB::table('journals as j')
                        ->where('j.client', $client_id)
                        ->where('j.fyear', $fiscal_year)
                        ->where('j.is_deleted', 0)
                        ->join('source_code as sc', function ($join) use ($excludedIds) {
                            $join->on('sc.id', 'j.source')->whereNotIn('sc.id', $excludedIds);
                        })
                        ->select('sc.id as source_id')
                        ->distinct()
                        ->orderBy('sc.source_code', 'asc')
                        ->get();
                @endphp
                <div class="mx-3 px-2 py-2 d-flex align-items-center data-row">
                    <div class="text-center mr-2" data-column_1="{{ $p }}" style="width: 80px;">
                        <span
                            style="font-family: Calibri; background: #f2f2f2;border-radius: 7px;width: 80px;border: 1px solid #ECEFF4;padding: 8px 18px;">{{ $p }}</span>
                    </div>
                    <div class="d-flex justify-content-center mr-2" data-column_2="{{ getMonth(@$month) }}" style="width: 115px;">
                        <div
                            style="font-family: Calibri;background: #f2f2f2;border-radius: 7px;width: 97px;border: 1px solid #ECEFF4;padding: 8px 0px;text-align: center;line-height: 1;">
                            {{ getMonth(@$month) }}</div>
                    </div>
                    <div class="text-center d-flex justify-content-center mr-2" data-column_3="{{ count($cd) }}"
                        style="width: 80px;">
                        <div style="font-family: Calibri;"
                            class="@if (count($cd) == 0) td-grey @elseif (round($total_credit_cd, 2) != round($total_debit_cd, 2)) td-yellow @else td-green @endif">
                            {{ count($cd) != 0 ? count($cd) : '.' }}
                        </div>
                    </div>
                    <div class="text-center d-flex justify-content-center mr-2" data-column_4="{{ count($cod) }}"
                        style="width: 80px;">
                        <div style="font-family: Calibri;"
                            class="@if (count($cod) == 0) td-grey @elseif (round($total_credit_cod, 2) != round($total_debit_cod, 2)) td-yellow @else td-green @endif">
                            {{ count($cod) != 0 ? count($cod) : '.' }}
                        </div>
                    </div>
                    <div class="text-center d-flex justify-content-center mr-2 px-2" data-column_5="{{ count($sj) }}"
                        style="width: 80px;">
                        <div style="font-family: Calibri;"
                            class="@if (count($sj) == 0) td-grey @elseif (round($total_credit_sj, 2) != round($total_debit_sj, 2)) td-yellow @else td-green @endif">
                            {{ count($sj) != 0 ? count($sj) : '.' }}
                        </div>
                    </div>
                    <div class="text-center d-flex justify-content-center mr-2 px-2" data-column_6="{{ count($ps) }}"
                        style="width: 80px;">
                        <div style="font-family: Calibri;"
                            class="@if (count($ps) == 0) td-grey @elseif (round($total_credit_ps, 2) != round($total_debit_ps, 2)) td-yellow @else td-green @endif">
                            {{ count($ps) != 0 ? count($ps) : '.' }}
                        </div>
                    </div>
                    <div class="text-center d-flex justify-content-center mr-2 px-2" data-column_7="{{ count($gj) }}"
                        style="width: 80px;">
                        <div style="font-family: Calibri;"
                            class="@if (count($gj) == 0) td-grey @elseif (round($total_credit_gj, 2) != round($total_debit_gj, 2)) td-yellow @else td-green @endif">
                            {{ count($gj) != 0 ? count($gj) : '.' }}
                        </div>
                    </div>
                    <div class="text-center d-flex justify-content-center mr-2 px-2" data-column_8="{{ count($adj) }}"
                        style="width: 80px;">
                        <div style="font-family: Calibri;"
                            class="@if (count($adj) == 0) td-grey @elseif (round($total_credit_adj, 2) != round($total_debit_adj, 2)) td-yellow @else td-green @endif">
                            {{ count($adj) != 0 ? count($adj) : '.' }}
                        </div>
                    </div>
                    @foreach ($extra as $e)
                        @php
                            
                            $total_credit_extra = 0;
                            $total_debit_extra = 0;
                            $extraSource = DB::table('journals as j')
                                ->where('j.is_deleted', 0)
                                ->where('j.fyear', $fiscal_year)
                                ->where('j.client', $client_id)
                                ->where('j.period', $period)
                                ->where('j.source', $e->source_id)
                                ->get();
                            foreach ($extraSource as $c) {
                                $total_credit_extra += $c->credit;
                            }
                            foreach ($extraSource as $c) {
                                $total_debit_extra += $c->debit;
                            }
                            // dd(count($extraSource),$total_credit_extra, $total_debit_extra);
                        @endphp
                        <div class="text-center d-flex justify-content-center mr-2 px-2"
                            data-column_{{ $column_no }}="{{ count($extraSource) }}" style="width: 80px;">
                            <div style="font-family: Calibri;"
                                class="@if (count($extraSource) == 0) td-grey @elseif (round($total_credit_extra, 2) != round($total_debit_extra, 2)) td-yellow @else td-green @endif">
                                {{ count($extraSource) != 0 ? count($extraSource) : '.' }}</div>
                        </div>
                        @php
                            $column_no++;
                        @endphp
                    @endforeach
                </div>

                @php
                    if ($month != 12) {
                        $month++;
                    } else {
                        $month = 1;
                    }
                @endphp
            @endfor
        </div>
        <div class="block new-block position-relative data-container-2" style="padding-top: 3mm; padding-bottom: 3mm;">
            <div class="mx-3 px-2 pt-2 d-flex justify-content-between">
                <p class="section-header text-right mb-1" style="font-size: 14pt;">
                    {{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}
                </p>
                <p class="text-right">Year Ending
                    {{ date('M d,', strtotime(getFiscalYearEnd($client->fiscal_start))) }} {{ date('Y') }}
                </p>
            </div>
            <div class="d-flex w-75 justify-content-center">
                <div style="width: 580px" class="credit-debit-info">
                    <div class="d-flex mb-3 align-items-center">
                        <div class="text-right mr-5"
                            style="width: 160px; font-weight: 700; font-family: Calibri; font-size: 14pt;">
                            Total
                        </div>
                        <div class="mr-2 credit-debit-info-1"
                            style="width: 120px; font-family: Calibri; font-size: 12pt;color: #0B66D5; border: 1px solid #0B66D5; border-radius: 5px; text-align: center;">
                            {{ number_format($total_debit, 2, '.', ',') }}
                        </div>
                        <div class="mr-2 credit-debit-info-2"
                            style="width: 120px; font-family: Calibri; font-size: 12pt;color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; text-align: center;">
                            {{ number_format($total_credit, 2, '.', ',') }}
                        </div>
                        @php
                            if (bccomp($total_debit, $total_credit, 2) > 0) {
                                $color = '#0B66D5';
                                $total = $total_credit - $total_debit;
                                $total = $total * -1;
                            } elseif (bccomp($total_debit, $total_credit, 2) < 0) {
                                $total = $total_credit - $total_debit;
                                $color = '#C41E3A';
                            } else {
                                $total = 0;
                                $color = '#4EA833';
                            }
                        @endphp
                        <div class="mr-2"
                            style="width: 100px; font-family: Calibri; font-size: 12pt;color: {{ $color }}; border: 1px solid {{ $color }}; border-radius: 5px; text-align: center;">
                            {{ number_format($total, 2, '.', ',') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('[data-toggle=tooltip]').tooltip();

        function updateSelectedRows() {
            $('.row-checkbox').each(function() {
                $(this).closest('.journal-data-row').toggleClass('selected-row', this.checked);
            });
        }
        $(document).on('click', '.row-checkbox', function(e) {
            const checkboxes = $('.row-checkbox');
            if (e.shiftKey) {
                const checkedBoxes = checkboxes.filter(':checked').not(this);
                if (checkedBoxes.length > 0) {
                    const currentIndex = checkboxes.index(this);
                    let lastCheckedIndex = -1;
                    checkedBoxes.each(function() {
                        const idx = checkboxes.index(this);
                        if (idx < currentIndex && idx > lastCheckedIndex) {
                            lastCheckedIndex = idx;
                        }
                    });
                    if (lastCheckedIndex === -1) {
                        checkedBoxes.each(function() {
                            const idx = checkboxes.index(this);
                            if (idx > currentIndex && (lastCheckedIndex === -1 || idx <
                                    lastCheckedIndex)) {
                                lastCheckedIndex = idx;
                            }
                        });
                    }
                    if (lastCheckedIndex !== -1) {
                        const start = Math.min(currentIndex, lastCheckedIndex);
                        const end = Math.max(currentIndex, lastCheckedIndex);
                        checkboxes.slice(start, end + 1).prop('checked', true);
                    }
                }
            }
            if (this.checked) {
                checkboxes.data('last-checked', $(this));
            }
            updateSelectedRows();
        });
    });
</script>
