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
    $qry = DB::table('journals as j')
        ->where('j.is_deleted', 0)
        ->leftJoin('clients as c', function ($join) {
            $join->on('j.client', '=', 'c.id')->where('c.is_deleted', 0);
        })
        ->leftJoin('source_code as sc', function ($join) {
            $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);
        })
        ->select('j.*', 'c.firstname', 'c.lastname', 'c.display_name', 'c.logo', 'sc.source_code', 'c.company')
        ->where('j.fyear', $client_fyear)
        ->where('j.client', $client_id)
        ->where('j.account_no', $account)
        ->where('j.period', $period)
        ->orderBy('j.' . $sort_column, $sort_order)
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
        ->where('j.fyear', $client_fyear)
        ->where('j.client', $client_id)
        ->where('j.account_no', $account)
        ->where('j.period', $period)
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
        ->where('j.account_no', $account)
        ->where('j.period', $period)
        ->count();
    $client = DB::table('clients')->where('id', $client_id)->first();
    $total_credit = 0;
    $total_debit = 0;
    // dd($qry);
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
        @if (count($qry) > 0)
            <div class="block new-block position-relative data-container" style="max-height: 60vh; overflow-y: auto;">
                @foreach ($qry as $key => $q)
                @php
                $source = DB::table('source_code')->where('id', $q->source)->first();
                        $total_credit += $q->credit;
                        $total_debit += $q->debit;
                    @endphp
                    <div class="journal-data-row mx-3 px-2 py-2 d-flex mb-2" data="{{ $q->edit_no }}">
                        <div class="custom-control custom-  custom-control-  custom-control-lg mr-3 no-print">
                            <input type="checkbox" class="custom-control-input row-checkbox"
                                id="select_row_{{ $key }}" name="select_row">
                            <label class="custom-control-label" for="select_row_{{ $key }}"></label>
                        </div>
                        <div class="text-center mr-2" data-column="editNo" style="width: 80px;">
                            <span style="font-family: Calibri;">{{ $q->editNo }}</span>
                        </div>
                        <div class="text-center mr-2" data-column="date" style="width: 100px;">
                            <span style="font-family: Calibri;">{{ $q->date }}</span>
                        </div>
                        <div class="text-center mr-2" data-column="account_no" style="width: 70px;">
                            <span style="font-family: Calibri;">{{ $source->source_code }}</span>
                        </div>
                        <div class="text-center mr-2" data-column="ref_no" style="width: 100px;">
                            <span style="font-family: Calibri;">{{ $q->ref_no }}</span>
                        </div>
                        <div class="text-right mr-2 px-2" data-column="debit" style="width: 140px;">
                            <span
                                style="font-family: Calibri; color: #0B66D5;">{{ number_format($q->debit, 2, '.', ',') }}</span>
                        </div>
                        <div class="text-right mr-2 px-2" data-column="credit" style="width: 140px;">
                            <span
                                style="font-family: Calibri; color: #C41E3A;">{{ number_format($q->credit, 2, '.', ',') }}</span>
                        </div>
                        <div class="text-left mr-2 px-2" data-column="description" style="width: 40%;">
                            <span style="font-family: Calibri;">{{ $q->description }}</span>
                        </div>
                        <div class="d-flex justify-content-end no-print" style="width: 206px;">
                            <div class="action-buttons text-center mr-2">
                                <a href="javacsript:void();" data-toggle="tooltip" data-custom-class="header-tooltip"
                                    data-trigger="hover" data-placement="top" title="" data-original-title="Edit"
                                    class="btn-edit" data-id="{{ $q->edit_no }}" data-editno="{{ $q->editNo }}"
                                    data-client-id="{{ $q->client }}"><img
                                        src="{{ asset('public') }}\icons2\icon-edit-grey.png" alt=""
                                        width="26px"></a>
                            </div>
                            <div class="action-buttons text-center mr-2">
                                <a href="javacsript:void();" data-toggle="tooltip" data-custom-class="header-tooltip"
                                    data-trigger="hover" data-placement="top" title=""
                                    data-original-title="Delete" class="btn-delete" data-place="0"
                                    data-id="{{ $q->edit_no }}" data-editno="{{ $q->editNo }}"><img
                                        src="{{ asset('public') }}\img\close.png" alt="" style="width: 15px; margin-top: 5px"></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="block new-block position-relative data-container-2" style="padding-top: 3mm; padding-bottom: 3mm;">
                <div class="mx-3 px-2 pt-2 d-flex d-flex">
                    <div style="width: 250px">
                        <div class="mt-2 d-flex">
                            <div class="w-50" style="font-family: Calibri;">
                                Period: {{ $period }}
                            </div>
                            <div class="w-50" style="font-family: Calibri;">
                                
                            </div>
                        </div>
                    </div>
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
                                if ($total_debit > $total_credit) {
                                    $color = '#0B66D5';
                                    $total = $total_credit - $total_debit;
                                    $total = $total * -1;
                                } elseif ($total_debit < $total_credit) {
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
                        <div class="d-flex align-items-center">
                            <div class="text-right mr-5"
                                style="width: 160px; font-weight: 700; font-family: Calibri; font-size: 14pt;">
                                No. Of Journals
                            </div>
                            <div class="mr-2 px-2" style="width: 120px;">
                                <div class="bubble-new text-center">{{ $debit }}</div>
                            </div>
                            <div class="mr-2 px-2" style="width: 120px;">
                                <div class="bubble-new text-center">{{ $credit }}</div>
                            </div>
                        </div>
                    </div>
                    <div style="width: 750px; font-family: Calibri; font-size: 14pt;" class="source-info">
                        <p class="section-header text-right mb-1"
                            style="font-size: 14pt;">{{ $client->use_corporation_no == 1 ? $client->corporation_no : $client->display_name }}</p>
                        <p class="text-right">Year Ending
                            {{ date('M d,', strtotime(getFiscalYearEnd($client->fiscal_start))) }} {{ date('Y') }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="block new-block position-relative">
                <div class="mx-3 px-2 py-2 d-flex mb-2 d-flex justify-content-center align-items-center"
                    style="height: 30vh">
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
