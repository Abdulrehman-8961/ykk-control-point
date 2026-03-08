@if ($filters->type == 'By Period')
    <div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">
        <div class="block-header py-0 d-flex justify-content-between align-items-start"
            style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

            <div>
                <a class="  section-header">Progress Report By Period
                </a>

            </div>


            <p style="font-size: 11pt !important;">Report run by
                {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }} on {{ date('F d, Y') }}</p>
        </div>
        <div class="block-content pb-0   "
            style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

            <div class="col-sm-12">
                <div class="d-flex text-nowrap align-items-center">
                    <div>
                        <p class=" pr-1 mb-1 ">{{ $filters->fiscal_year }}</p>
                    </div>
                    <hr class="w-100" style="border-color: #595959!important">
                </div>
                <div class="row">
                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                        <table class="table border-0 table-period">
                            <thead>
                                <tr>
                                    <td style="width: 250px;"></td>
                                    <td>
                                        <div class="td-month">P1</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P2</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P3</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P4</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P5</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P6</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P7</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P8</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P9</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P10</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P11</div>
                                    </td>
                                    <td>
                                        <div class="td-month">P12</div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $c)
                                    @php
                                        $p1_indicator = 'td-grey';
                                        $p2_indicator = 'td-grey';
                                        $p3_indicator = 'td-grey';
                                        $p4_indicator = 'td-grey';
                                        $p5_indicator = 'td-grey';
                                        $p6_indicator = 'td-grey';
                                        $p7_indicator = 'td-grey';
                                        $p8_indicator = 'td-grey';
                                        $p9_indicator = 'td-grey';
                                        $p10_indicator = 'td-grey';
                                        $p11_indicator = 'td-grey';
                                        $p12_indicator = 'td-grey';
                                        $p1_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 1)
                                            ->count();
                                        $p2_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 2)
                                            ->count();
                                        $p3_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 3)
                                            ->count();
                                        $p4_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 4)
                                            ->count();
                                        $p5_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 5)
                                            ->count();
                                        $p6_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 6)
                                            ->count();
                                        $p7_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 7)
                                            ->count();
                                        $p8_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 8)
                                            ->count();
                                        $p9_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 9)
                                            ->count();
                                        $p10_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 10)
                                            ->count();
                                        $p11_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 11)
                                            ->count();
                                        $p12_journals = DB::table('journals')
                                            ->where('is_deleted', 0)
                                            ->where('client', $c->id)
                                            ->where('fyear', $filters->fiscal_year)
                                            ->where('period', 12)
                                            ->count();

                                        if ($p1_journals > 0) {
                                            $p1_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 1)
                                                ->sum('debit');
                                            $p1_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 1)
                                                ->sum('credit');
                                            if ($p1_debits == $p1_credits) {
                                                $p1_indicator = 'td-green';
                                            } else {
                                                $p1_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p1_indicator = 'td-grey';
                                        }
                                        if ($p2_journals > 0) {
                                            $p2_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 2)
                                                ->sum('debit');
                                            $p2_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 2)
                                                ->sum('credit');
                                            if ($p2_debits == $p2_credits) {
                                                $p2_indicator = 'td-green';
                                            } else {
                                                $p2_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p2_indicator = 'td-grey';
                                        }
                                        if ($p3_journals > 0) {
                                            $p3_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 3)
                                                ->sum('debit');
                                            $p3_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 3)
                                                ->sum('credit');
                                            if ($p3_debits == $p3_credits) {
                                                $p3_indicator = 'td-green';
                                            } else {
                                                $p3_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p3_indicator = 'td-grey';
                                        }
                                        if ($p4_journals > 0) {
                                            $p4_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 4)
                                                ->sum('debit');
                                            $p4_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 4)
                                                ->sum('credit');
                                            if ($p4_debits == $p4_credits) {
                                                $p4_indicator = 'td-green';
                                            } else {
                                                $p4_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p4_indicator = 'td-grey';
                                        }
                                        if ($p5_journals > 0) {
                                            $p5_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 5)
                                                ->sum('debit');
                                            $p5_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 5)
                                                ->sum('credit');
                                            if ($p5_debits == $p5_credits) {
                                                $p5_indicator = 'td-green';
                                            } else {
                                                $p5_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p5_indicator = 'td-grey';
                                        }
                                        if ($p6_journals > 0) {
                                            $p6_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 6)
                                                ->sum('debit');
                                            $p6_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 6)
                                                ->sum('credit');
                                            if ($p6_debits == $p6_credits) {
                                                $p6_indicator = 'td-green';
                                            } else {
                                                $p6_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p6_indicator = 'td-grey';
                                        }
                                        if ($p7_journals > 0) {
                                            $p7_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 7)
                                                ->sum('debit');
                                            $p7_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 7)
                                                ->sum('credit');
                                            if ($p7_debits == $p7_credits) {
                                                $p7_indicator = 'td-green';
                                            } else {
                                                $p7_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p7_indicator = 'td-grey';
                                        }
                                        if ($p8_journals > 0) {
                                            $p8_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 8)
                                                ->sum('debit');
                                            $p8_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 8)
                                                ->sum('credit');
                                            if ($p8_debits == $p8_credits) {
                                                $p8_indicator = 'td-green';
                                            } else {
                                                $p8_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p8_indicator = 'td-grey';
                                        }
                                        if ($p9_journals > 0) {
                                            $p9_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 9)
                                                ->sum('debit');
                                            $p9_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 9)
                                                ->sum('credit');
                                            if ($p9_debits == $p9_credits) {
                                                $p9_indicator = 'td-green';
                                            } else {
                                                $p9_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p9_indicator = 'td-grey';
                                        }
                                        if ($p10_journals > 0) {
                                            $p10_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 10)
                                                ->sum('debit');
                                            $p10_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 10)
                                                ->sum('credit');
                                            if ($p10_debits == $p10_credits) {
                                                $p10_indicator = 'td-green';
                                            } else {
                                                $p10_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p10_indicator = 'td-grey';
                                        }
                                        if ($p11_journals > 0) {
                                            $p11_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 11)
                                                ->sum('debit');
                                            $p11_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 11)
                                                ->sum('credit');
                                            if ($p11_debits == $p11_credits) {
                                                $p11_indicator = 'td-green';
                                            } else {
                                                $p11_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p11_indicator = 'td-grey';
                                        }
                                        if ($p12_journals > 0) {
                                            $p12_debits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 12)
                                                ->sum('debit');
                                            $p12_credits = DB::table('journals')
                                                ->where('is_deleted', 0)
                                                ->where('client', $c->id)
                                                ->where('fyear', $filters->fiscal_year)
                                                ->where('period', 12)
                                                ->sum('credit');
                                            if ($p12_debits == $p12_credits) {
                                                $p12_indicator = 'td-green';
                                            } else {
                                                $p12_indicator = 'td-yellow';
                                            }
                                        } else {
                                            $p12_indicator = 'td-grey';
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $c->company }}</td>
                                        <td>
                                            <div class="{{ $p1_indicator }}">
                                                @if ($p1_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p2_indicator }}">
                                                @if ($p2_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p3_indicator }}">
                                                @if ($p3_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p4_indicator }}">
                                                @if ($p4_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p5_indicator }}">
                                                @if ($p5_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p6_indicator }}">
                                                @if ($p6_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p7_indicator }}">
                                                @if ($p7_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p8_indicator }}">
                                                @if ($p8_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p9_indicator }}">
                                                @if ($p9_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p10_indicator }}">
                                                @if ($p10_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p11_indicator }}">
                                                @if ($p11_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="{{ $p12_indicator }}">
                                                @if ($p12_indicator == 'td-green')
                                                    &#x2713;
                                                @else
                                                    &nbsp;&nbsp;&nbsp;
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-5 pt-5">
                <div style="border: 2px solid #d7d7d7;border-radius: 10px; padding: 15px;width: fit-content;"
                    class="d-flex flex-column">
                    <div class="d-flex mb-3">
                        <div class="td-green mr-3">
                            &#x2713;
                        </div>
                        <span style="font-size: 11pt;">
                            Atleast one journal found and period balances
                        </span>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="td-yellow mr-3">
                            &#x2713;
                        </div>
                        <span style="font-size: 11pt;">
                            Atleast one journal found and period does not balance
                        </span>
                    </div>
                    <div class="d-flex">
                        <div class="td-grey mr-3">
                            &#x2713;
                        </div>
                        <span style="font-size: 11pt;">
                            No journals found for period
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <!-- page footer --->
        <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
            {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>
    </div>
@elseif($filters->type == 'By Fiscal Year')
    <div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">
        <div class="block-header py-0 d-flex justify-content-between align-items-start"
            style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

            <div>
                <a class="  section-header">Progress Report by Fiscal Year
                </a>

            </div>


            <p style="font-size: 11pt !important;">Report run by
                {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }} on {{ date('F d, Y') }}</p>
        </div>
        <div class="block-content pb-0   "
            style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                        <table class="table border-0 table-period">
                            <thead>
                                <tr>
                                    <td></td>
                                    @foreach ($filters->fiscal_years as $fy)
                                        <td class="td-month" style="background: transparent !important;">
                                            {{ $fy }}</td>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $c)
                                    <tr>
                                        <td>{{ $c->company }}</td>
                                        @foreach ($filters->fiscal_years as $fy)
                                            @php
                                                $indicator = 'td-grey';
                                                $journals = DB::table('journals')
                                                    ->where('is_deleted', 0)
                                                    ->where('client', $c->id)
                                                    ->where('fyear', $fy)
                                                    ->count();

                                                if ($journals > 0) {
                                                    $debits = DB::table('journals')
                                                        ->where('is_deleted', 0)
                                                        ->where('client', $c->id)
                                                        ->where('fyear', $fy)
                                                        ->sum('debit');
                                                    $credits = DB::table('journals')
                                                        ->where('is_deleted', 0)
                                                        ->where('client', $c->id)
                                                        ->where('fyear', $fy)
                                                        ->sum('credit');
                                                    if ($debits == $credits) {
                                                        $indicator = 'td-green';
                                                    } else {
                                                        $indicator = 'td-yellow';
                                                    }
                                                } else {
                                                    $indicator = 'td-grey';
                                                }
                                            @endphp
                                            <td>
                                                <div class="{{ $indicator }}">
                                                    @if ($indicator == 'td-green')
                                                        &#x2713;
                                                    @else
                                                        &nbsp;&nbsp;&nbsp;
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-5 pt-5">
                <div style="border: 2px solid #d7d7d7;border-radius: 10px; padding: 15px;width: fit-content;"
                    class="d-flex flex-column">
                    <div class="d-flex mb-3">
                        <div class="td-green mr-3">
                            &#x2713;
                        </div>
                        <span style="font-size: 11pt;">
                            Atleast one journal found and fiscal year balances
                        </span>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="td-yellow mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            Atleast one journal found and fiscal year does not balance
                        </span>
                    </div>
                    <div class="d-flex">
                        <div class="td-grey mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            No journals found for fiscal year
                        </span>
                    </div>
                </div>
            </div>
        </div>



        <!-- page footer --->
        <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |
            {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>


    </div>
@endif
