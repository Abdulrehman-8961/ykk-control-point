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

    $sort_column = $sort_column ?? 'j.account_no';
    $sort_order = $sort_order ?? 'asc';

    $reports = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->Join('clients_gifi as g', function ($join) use ($client_id) {
            $join->on('j.account_no', '=', 'g.account_no')->where('g.is_deleted', 0);
            $join->where('g.client_id', $client_id);
        })
        ->groupBy('j.account_no')
        ->select(
            'j.account_no',
            'g.description',
            'g.account_type',
            'j.client',
            'j.fyear',
            DB::raw('SUM(j.debit) as total_debit'),
            DB::raw('SUM(j.credit) as total_credit'),
        )
        ->where('j.fyear', $fiscal_year)
        ->where('j.client', $client_id)
        ->get();

    $journalSums = $reports->groupBy('account_no');

    function getJournalSum($journalSums, $account_no, $type, $column = 'total_debit')
    {
        return optional($journalSums[$account_no]->firstWhere('account_type', $type))?->$column ?? 0;
    }

    $groupA = ['Asset', 'Liability', 'Retained Earning'];
    $groupB = ['Revenue', 'Expense'];

    if (in_array($sort_column, ['column-a-debit', 'column-a-credit', 'columnba-debit', 'column-b-credit'])) {
        $sorted = $reports->sortBy(function ($item) use ($sort_column, $journalSums, $groupA, $groupB) {
            switch ($sort_column) {
                case 'column-a-debit':
                    return collect($groupA)->sum(
                        fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_debit'),
                    );
                case 'column-a-credit':
                    return collect($groupA)->sum(
                        fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_credit'),
                    );
                case 'columnba-debit':
                    return collect($groupB)->sum(
                        fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_debit'),
                    );
                case 'column-b-credit':
                    return collect($groupB)->sum(
                        fn($type) => getJournalSum($journalSums, $item->account_no, $type, 'total_credit'),
                    );
            }
        });

        if (strtolower($sort_order) === 'desc') {
            $sorted = $sorted->reverse();
        }

        $reports = $sorted->values();
    } else {
        if ($sort_column === 'j.account_no') {
            $reports = $reports->sortBy(function ($item) {
                return (int) $item->account_no;
            });
        } elseif ($sort_column === 'g.description') {
            $reports = $reports->sortBy(function ($item) {
                return strtolower($item->description);
            });
        }

        if (strtolower($sort_order) === 'desc') {
            $reports = $reports->reverse();
        }

        $reports = $reports->values();
    }

    $credit = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->leftJoin('clients as c', function ($join) {
            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
        })
        ->leftJoin('source_code as sc', function ($join) {
            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
        })
        ->where('j.credit', '>', 0)
        ->where('j.fyear', $client_fyear)
        ->where('j.client', $client_id)
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
        ->where('j.fyear', $client_fyear)
        ->where('j.client', $client_id)
        ->count();
    $client = DB::table('clients')->where('id', $client_id)->first();
    $total_credit_a = 0;
    $total_debit_a = 0;
    $total_credit_b = 0;
    $total_debit_b = 0;

@endphp
<style>
    #showData::-webkit-scrollbar,
    .block.new-block::-webkit-scrollbar {
        width: 8px;
        padding-right: 10px;
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
        @if (count($reports) > 0)
            <div class="block new-block position-relative data-container" style="max-height: 60vh; overflow-y: auto;">
                @foreach ($reports->chunk(3) as $key => $chunk)
                    <div
                        class="journal-data-row mx-3 px-2 py-2 d-flex mb-2 flex-wrap {{ $key % 2 == 1 ? 'bg-grey' : '' }}">
                        @foreach ($chunk as $q)
                            @php

                                $column_a_debit = collect($groupA)->sum(
                                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_debit'),
                                );
                                $column_a_credit = collect($groupA)->sum(
                                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_credit'),
                                );
                                $column_b_debit = collect($groupB)->sum(
                                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_debit'),
                                );
                                $column_b_credit = collect($groupB)->sum(
                                    fn($type) => getJournalSum($journalSums, $q->account_no, $type, 'total_credit'),
                                );
                                $total_credit_a += $column_a_credit;
                                $total_debit_a += $column_a_debit;
                                $total_credit_b += $column_b_credit;
                                $total_debit_b += $column_b_debit;
                            @endphp
                            <div class="d-flex w-100 chunk">
                                <div class="text-center mr-3" data-column="j.account_no" style="width: 10%;">
                                    <span style="font-family: Calibri;">{{ $q->account_no }}</span>
                                </div>
                                <div class="mr-3" data-column="g.description" style="width: 34%;">
                                    <span style="font-family: Calibri;">{{ $q->description }}</span>
                                </div>
                                <div class="text-center mr-3" data-column="column-a-debit" style="width: 15%;">
                                    <span
                                        style="font-family: Calibri; color: #0B66D5;">{{ $column_a_debit > 0 ? number_format($column_a_debit, 2, '.', ',') : '' }}</span>
                                </div>
                                <div class="text-center mr-3" data-column="column-a-credit" style="width: 15%;">
                                    <span
                                        style="font-family: Calibri; color: #C41E3A;">{{ $column_a_credit > 0 ? number_format($column_a_credit, 2, '.', ',') : '' }}</span>
                                </div>
                                <div class="text-center mr-3 px-2" data-column="columnba-debit" style="width: 15%;">
                                    <span
                                        style="font-family: Calibri; color: #0B66D5;">{{ $column_b_debit > 0 ? number_format($column_b_debit, 2, '.', ',') : '' }}</span>
                                </div>
                                <div class="text-center mr-3 px-2" data-column="column-b-credit" style="width: 15%;">
                                    <span
                                        style="font-family: Calibri; color: #C41E3A;">{{ $column_b_credit > 0 ? number_format($column_b_credit, 2, '.', ',') : '' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="block new-block position-relative data-container-2"
                style="padding-top: 2mm !important; padding-bottom: 2mm !important;">
                <div class="mx-3 px-2 pt-2 d-flex d-flex position-relative">
                    <div style="width: 30%; font-family: Calibri; font-size: 14pt;">
                        <p class="section-header  mb-1" style="font-size: 14pt;">
                            {{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}
                        </p>
                        <p class="">Year Ending
                            {{ date('M d,', strtotime(getFiscalYearEnd($client->fiscal_start))) }} {{ date('Y') }}
                        </p>
                    </div>
                    <div style="width: 70%;" class="">
                        <div class="d-flex mb-1 align-items-center">
                            <div class="text-right mr-5"
                                style="width: 12%; font-weight: 700; font-family: Calibri; font-size: 14pt;">
                                Sub-Total
                            </div>
                            <div class="mr-3 credit-debit-info-1"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #0B66D5; border: 1px solid #0B66D5; border-radius: 5px; text-align: center;">
                                {{ number_format($total_debit_a, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-2"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; text-align: center;">
                                {{ number_format($total_credit_a, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-1"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #0B66D5; border: 1px solid #0B66D5; border-radius: 5px; text-align: center;">
                                {{ number_format($total_debit_b, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-2"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; text-align: center;">
                                {{ number_format($total_credit_b, 2, '.', ',') }}
                            </div>
                        </div>
                        @php
                            $final_credit_a = 0;
                            $final_debit_a = 0;
                            $final_credit_b = 0;
                            $final_debit_b = 0;
                            if (bccomp($total_debit_a, $total_credit_a, 2) > 0) {
                                $color_a = '#0B66D5';
                                $total_a = $total_credit_a - $total_debit_a;
                                $total_a = $total_a * -1;
                                $final_credit_a = $total_credit_a + $total_a;
                                $final_debit_a = $total_debit_a;
                            } elseif (bccomp($total_debit_a, $total_credit_a, 2) < 0) {
                                $total_a = $total_credit_a - $total_debit_a;
                                $color_a = '#C41E3A';
                                $final_credit_a = $total_credit_a;
                                $final_debit_a = $total_debit_a + $total_a;
                            } else {
                                $total_a = 0;
                                $color_a = '#4EA833';
                                $final_credit_a = $total_credit_a;
                                $final_debit_a = $total_debit_a;
                            }

                            if (bccomp($total_debit_b, $total_credit_b, 2) > 0) {
                                $color_b = '#0B66D5';
                                $total_b = $total_credit_b - $total_debit_b;
                                $total_b = $total_b * -1;
                                $final_credit_b = $total_credit_b + $total_b;
                                $final_debit_b = $total_debit_b;
                            } elseif (bccomp($total_debit_b, $total_credit_b, 2) < 0) {
                                $total_b = $total_credit_b - $total_debit_b;
                                $color_b = '#C41E3A';
                                $final_credit_b = $total_credit_b;
                                $final_debit_b = $total_debit_b + $total_b;
                            } else {
                                $total_b = 0;
                                $color_b = '#4EA833';
                                $final_credit_b = $total_credit_b;
                                $final_debit_b = $total_debit_b;
                            }
                        @endphp
                        <div class="d-flex mb-1 align-items-center">
                            <div class="text-right mr-5"
                                style="width: 12%; font-weight: 700; font-family: Calibri; font-size: 14pt;">
                                Balance
                            </div>
                            <div class="mr-3 credit-debit-info-1"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #4EA833; border: 1px solid #4EA833; border-radius: 5px; text-align: center;">
                                {{ number_format($total_a, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-2" style="width: 19%;">
                                &nbsp;
                            </div>
                            <div class="mr-3 credit-debit-info-1" style="width: 19%;">
                                &nbsp;
                            </div>
                            <div class="mr-3 credit-debit-info-2"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #4EA833; border: 1px solid #4EA833; border-radius: 5px; text-align: center;">
                                {{ number_format($total_b, 2, '.', ',') }}
                            </div>
                        </div>
                        <div class="d-flex mb-1 align-items-center">
                            <div class="text-right mr-5"
                                style="width: 12%; font-weight: 700; font-family: Calibri; font-size: 14pt;">
                                Total
                            </div>
                            <div class="mr-3 credit-debit-info-1"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #0B66D5; border: 1px solid #0B66D5; border-radius: 5px; text-align: center;">
                                {{ number_format($final_debit_a, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-2"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; text-align: center;">
                                {{ number_format($final_credit_a, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-1"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #0B66D5; border: 1px solid #0B66D5; border-radius: 5px; text-align: center;">
                                {{ number_format($final_debit_b, 2, '.', ',') }}
                            </div>
                            <div class="mr-3 credit-debit-info-2"
                                style="width: 19%; font-family: Calibri; font-size: 12pt;color: #C41E3A; border: 1px solid #C41E3A; border-radius: 5px; text-align: center;">
                                {{ number_format($final_credit_b, 2, '.', ',') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="block new-block position-relative">
                <div class="mx-3 px-2 py-2 d-flex mb-2 d-flex justify-content-center align-items-center" style="height: 30vh;">
                    <a class="section-header" style="font-size: 20pt;">No journals found</a>
                </div>
            </div>
        @endif
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
