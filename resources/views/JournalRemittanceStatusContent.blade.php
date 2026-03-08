@php
function getFiscalYearEnd($period, $month, $year) {
    $monthCalendar = array($month);
    while ($period <= 12) {
        $month = $month + 1;
        if ($month == 13) {
            $month = 1;
        }
        array_push($monthCalendar, $month);
        if (count($monthCalendar) == 12) {
            break;
        }
        $period++;
    }
    foreach ($monthCalendar as $key => $m) {
        if ($m == 1 && $key != 0) {
            $year++;
        }
    }
    return $year;
}

function findPeriod($fiscalStart, $dateString) {
    $startDate = strtotime($fiscalStart);
    $endDate = strtotime($dateString);

    $diffMonths = (date('Y', $endDate) - date('Y', $startDate)) * 12 + date('n', $endDate) - date('n', $startDate) + 1;
    $period = ($diffMonths > 0) ? $diffMonths : 12 - abs($diffMonths % 12);

    if ($period > 12) {
        return "";
    }

    $periodString = str_pad($period, 2, "0", STR_PAD_LEFT);
    return $periodString;
}
function remittanceCalender($remittance, $month_no, $year)
{
$result = [];
if ($remittance == "Quarterly") {
for ($i = 0; $i < 3; $i++) { $result[]=$month_no . '-' . $year; if ($month_no==1) { $month_no=12; $year--; } else {
    $month_no--; } } } elseif ($remittance=="Yearly" ) { for ($i=0; $i < 12; $i++) { $result[]=$month_no . '-' . $year;
    if ($month_no==1) { $month_no=12; $year--; } else { $month_no--; } } } elseif ($remittance=="Monthly" ) {
    $result[]=$month_no . '-' . $year; } return $result; }



    function calculateRemittanceMonths($fiscalYearEndMonth) {
    $monthNumbers = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $fiscalYearEndIndex = array_search($fiscalYearEndMonth, $monthNumbers);
    $remittanceMonths = [];
    for ($i = 0; $i < 4; $i++) {
        $remittanceIndex = ($fiscalYearEndIndex - $i * 3 + 12) % 12;
        $remittanceMonths[] = $remittanceIndex + 1;
    }
    return $remittanceMonths;
}

function get_total_remittance_and_revenue($id)
{
    $q = DB::table('remittances as r')
        ->where('r.is_deleted', 0)
        ->where('r.id', $id)
        ->join("clients as c", function ($join) {
            $join->on("c.id", "=", "r.client");
            $join->where("c.is_deleted", 0);
        })
        ->leftJoin("cities as p", function ($join) {
            $join->on("c.province", "=", "p.state_name");
            $join->where("p.state_name", "=", "c.province");
            $join->limit(1);
        })
        ->select(
            "r.*",
            "c.firstname",
            "c.lastname",
            "c.company as company_name",
            "c.federal_tax",
            "c.provincial_tax",
            "c.federal_no",
            "c.provincial_no",
            "c.tax_remittance",
            "c.fiscal_start",
            "p.state_code as province_code",
            "c.default_prov"
        )
        ->first();

    $calender = remittanceCalender($q->tax_remittance, $q->month, $q->year);
    $calender_month = [];
    $calender_year = [];

    foreach ($calender as $c) {
        $arr = explode("-", $c);
        array_push($calender_month, intval($arr[0]));
        array_push($calender_year, intval($arr[1]));
    }

    $calender_month = array_values(array_unique($calender_month));
    $calender_year = array_values(array_unique($calender_year));

    $month = $q->month;
    $year = $q->year;

    $fs = $q->fiscal_start;
    $fiscal_start = explode("-", $fs);
    $fiscal_start = $fiscal_start[0];
    $period = findPeriod($fs, $fiscal_start . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-01");
    $fiscal_year_end = getFiscalYearEnd(intval($period), intval($month), intval($year));

    $tax_remittance = $q->tax_remittance;
    $taxes = $q->taxes;
    $federal_tax = $q->federal_tax;
    $provincial_tax = $q->provincial_tax;
    $federal_credit = 0;
    $federal_rev_credit = 0;
    $federal_exp_credit = 0;
    $federal_debit = 0;
    $federal_rev_debit = 0;
    $federal_exp_debit = 0;
    $federal_remit = 0;
    $federal_rev_remit = 0;
    $federal_exp_remit = 0;
    $provincial_credit = 0;
    $provincial_rev_credit = 0;
    $provincial_exp_credit = 0;
    $provincial_debit = 0;
    $provincial_rev_debit = 0;
    $provincial_exp_debit = 0;
    $provincial_remit = 0;
    $provincial_rev_remit = 0;
    $provincial_exp_remit = 0;
    $total_remittance = 0;
    $total_rev_remittance = 0;
    $total_exp_remittance = 0;

    $whereClauses = [
        ['j.client', $q->client],
        ['j.is_deleted', 0]
    ];

    $_federal_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                 $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $federal_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo', 'asc')
        ->orderBy('j.debit', 'asc')
        ->get();

        $_federal_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $federal_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo', 'asc')
        ->orderBy('j.credit', 'asc')
        ->get();
    $_Rev_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $federal_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.client_id', $q->client)
            ->where('cg.is_deleted', 0)->where('cg.sub_type', "Revenue");
        })
        ->select("j.*", "sc.source_code")
        ->get();

        $_Rev_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.client_id', $q->client)
            ->where('cg.is_deleted', 0)->where('cg.sub_type', "Revenue");
        })
        ->select("j.*", "sc.source_code")
        ->get();


        $_Exp_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.is_deleted', 0)
            ->where('cg.client_id', $q->client)
            ->where(function ($qry) {
                $qry->where('cg.sub_type', "Cost of sale")
                ->orWhere('cg.sub_type', "Operating expense");
            });
        })
        ->select("j.*", "sc.source_code")
        ->get();

        $_Exp_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $federal_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
               $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->Join("clients_gifi as cg", function ($join) use ($q) {
            $join->on("j.account_no", "=", "cg.account_no")
            ->where('cg.is_deleted', 0)
            ->where('cg.client_id', $q->client)
            ->where(function ($qry) {
                $qry->where('cg.sub_type', "Cost of sale")
                ->orWhere('cg.sub_type', "Operating expense");
            });
        })
        ->select("j.*", "sc.source_code")
        ->get();
    $_provincial_debit = array();
    $_provincial_credit = array();
    if ($taxes == 'Both') {
        $_provincial_debit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.debit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
               $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $provincial_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo')
        ->orderBy('j.debit')
        ->get();

        $_provincial_credit = DB::table('journals as j')
        ->where($whereClauses)
        ->where('j.credit', '>', 0)
        ->where(function ($query) use ($tax_remittance, $fiscal_year_end, $month, $year, $calender, $calender_month, $calender_year, $provincial_tax) {
            if($tax_remittance == 'Monthly') {
                $query->where('j.month', $month)
                ->where('j.year', $year);
            }
            if($tax_remittance == 'Quarterly') {
                $query->where(function ($subquery) use ($calender) {
                    foreach ($calender as $key => $range) {
                        $_e = explode("-", $range);
                        $m = date("m", strtotime($_e[1] . "-" . $_e[0]));
                        $y = date("Y", strtotime($_e[1] . "-" . $_e[0]));
                        if ($key == 0) {
                            $subquery->where(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        } else {
                            $subquery->orWhere(function ($q) use ($m, $y) {
                                $q->where('j.month', $m)
                                ->where('j.year', $y);
                            });
                        }
                    }  
                });
            }
            if($tax_remittance == 'Yearly') {
                $query->where('j.fyear', $fiscal_year_end);
            }
            $query->where("j.account_no", $provincial_tax);
        })
        ->leftJoin("source_code as sc", function ($join) {
            $join->on("j.source", "=", "sc.id")
            ->where('sc.is_deleted', 0);
        })
        ->select("j.*", "sc.source_code")
        ->orderBy('j.editNo')
        ->orderBy('j.credit')
        ->get();
    }
    foreach ($_federal_debit as $f) {
        $federal_debit += $f->debit;
    }
    foreach ($_federal_credit as $f) {
        $federal_credit += $f->credit;
    }
    $federal_remit = $federal_credit - $federal_debit;

    foreach ($_provincial_debit as $p) {
        $provincial_debit += $p->debit;
    }
    foreach ($_provincial_credit as $p) {
        $provincial_credit += $p->credit;
    }
    $provincial_remit = $provincial_credit - $provincial_debit;


    $total_rev_debit = 0;
    foreach($_Rev_debit as $p) {
        $total_rev_debit += $p->debit;
    }

    $total_rev_credit = 0;
    foreach($_Rev_credit as $p) {
        $total_rev_credit += $p->credit;
    }

    $total_exp_debit = 0;
    foreach($_Exp_debit as $p) {
        $total_exp_debit += $p->debit;
    }

    $total_exp_credit = 0;
    foreach($_Exp_credit as $p) {
        $total_exp_credit += $p->credit;
    }

    $total_remittance = $federal_remit + $provincial_remit;
    $total_debit = $federal_debit + $provincial_debit;
    $total_credit = $federal_credit + $provincial_credit;

    $total_rev = $total_rev_credit - $total_rev_debit;

    $total_exp = $total_exp_debit - $total_exp_credit;

    $net_revenue = $total_rev - $total_exp;

    $remittance = ($total_remittance <= 0 ? '($' . number_format(abs($total_remittance), 2, '.', '') . ')' : '$' . number_format($total_remittance, 2, '.', ''));

    $revenue = ($net_revenue <= 0 ? '($' . number_format(abs($net_revenue), 2, '.', '') . ')' : '$' . number_format($net_revenue, 2, '.', '') );

    return (object) compact("remittance", "revenue");
}
@endphp
@if($filters->type == 'By Year')
<div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">
    <div class="block-header py-0 d-flex justify-content-between align-items-start"
        style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

        <div>
            <a class="  section-header">Remittance Report by Year
            </a>

        </div>


        <p style="font-size: 11pt !important;">Report run by {{Auth::user()->firstname . ' ' . Auth::user()->lastname}} on {{date("F d, Y")}}</p>
    </div>
    <div class="block-content pb-0   "
        style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

            <div class="col-sm-12">
                <div class="d-flex text-nowrap align-items-center">
                    <div>
                        <p class=" pr-1 mb-1 ">{{$filters->year}}</p>
                    </div>
                    <hr class="w-100" style="border-color: #595959!important">
                </div>
                <div class="row">
                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                        <table class="table border-0 table-period">
                            <thead>
                                <tr>
                                    <td style="width: 200px !important;"></td>
                                    <td ></td>
                                    <td ></td>
                                    <td >
                                        <div class="td-month">Jan</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Feb</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Mar</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Apr</div>
                                    </td>
                                    <td >
                                        <div class="td-month">May</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Jun</div>
                                    </td>
                                    <td>
                                        <div class="td-month">Jul</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Aug</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Sep</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Oct</div>
                                    </td>
                                    <td >
                                        <div class="td-month">Nov</div>
                                    </td>
                                    <td>
                                        <div class="td-month">Dec</div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $c)

                                @php
                                    $jan = 'td-grey';
                                    $feb = 'td-grey';
                                    $mar = 'td-grey';
                                    $apr = 'td-grey';
                                    $may = 'td-grey';
                                    $jun = 'td-grey';
                                    $jul = 'td-grey';
                                    $aug = 'td-grey';
                                    $sept = 'td-grey';
                                    $oct = 'td-grey';
                                    $nov = 'td-grey';
                                    $dec = 'td-grey';
                                    if($c->tax_remittance == 'Monthly') {
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 1)->exists()) {
                                            $jan = 'td-green';
                                        } else {
                                            $jan = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 2)->exists()) {
                                            $feb = 'td-green';
                                        } else {
                                            $feb = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 3)->exists()) {
                                            $mar = 'td-green';
                                        } else {
                                            $mar = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 4)->exists()) {
                                            $apr = 'td-green';
                                        } else {
                                            $apr = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 5)->exists()) {
                                            $may = 'td-green';
                                        } else {
                                            $may = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 6)->exists()) {
                                            $jun = 'td-green';
                                        } else {
                                            $jun = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 7)->exists()) {
                                            $jul = 'td-green';
                                        } else {
                                            $jul = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 8)->exists()) {
                                            $aug = 'td-green';
                                        } else {
                                            $aug = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 9)->exists()) {
                                            $sept = 'td-green';
                                        } else {
                                            $sept = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 10)->exists()) {
                                            $oct = 'td-green';
                                        } else {
                                            $oct = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 11)->exists()) {
                                            $nov = 'td-green';
                                        } else {
                                            $nov = 'td-yellow';
                                        }
                                        if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 12)->exists()) {
                                            $dec = 'td-green';
                                        } else {
                                            $dec = 'td-yellow';
                                        }
                                    }
                                    if($c->tax_remittance == 'Quarterly') {
                                        $client_quarters = calculateRemittanceMonths(explode(" ", $c->fiscal_year_end)[0]);

                                        if(!in_array(1, $client_quarters)) {
                                            $jan = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 1)->exists()) {
                                                $jan = 'td-green';
                                            } else {
                                                $jan = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(2, $client_quarters)) {
                                            $feb = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 2)->exists()) {
                                                $feb = 'td-green';
                                            } else {
                                                $feb = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(3, $client_quarters)) {
                                            $mar = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 3)->exists()) {
                                                $mar = 'td-green';
                                            } else {
                                                $mar = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(4, $client_quarters)) {
                                            $apr = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 4)->exists()) {
                                                $apr = 'td-green';
                                            } else {
                                                $apr = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(5, $client_quarters)) {
                                            $may = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 5)->exists()) {
                                                    $may = 'td-green';
                                            } else {
                                                $may = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(6, $client_quarters)) {
                                            $jun = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 6)->exists()) {
                                                $jun = 'td-green';
                                            } else {
                                                $jun = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(7, $client_quarters)) {
                                            $jul = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 7)->exists()) {
                                                $jul = 'td-green';
                                            } else {
                                                $jul = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(8, $client_quarters)) {
                                            $aug = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 8)->exists()) {
                                                $aug = 'td-green';
                                            } else {
                                                $aug = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(9, $client_quarters)) {
                                            $sept = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 9)->exists()) {
                                                $sept = 'td-green';
                                            } else {
                                                $sept = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(10, $client_quarters)) {
                                            $oct = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 10)->exists()) {
                                                $oct = 'td-green';
                                            } else {
                                                $oct = 'td-yellow';
                                            }
                                        }
                                        if(!in_array(11, $client_quarters)) {
                                            $nov ='td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 11)->exists()) {
                                                $nov = 'td-green';
                                            } else {
                                                $nov = 'td-yellow';
                                            }
                                        }
                                        if( !in_array(12, $client_quarters)) {
                                            $dec = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 12)->exists()) {
                                                $dec = 'td-green';
                                            } else {
                                                $dec = 'td-yellow';
                                            }
                                        }
                                    }
                                    if($c->tax_remittance == 'Yearly') {
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 1) {
                                            $jan = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 1)->exists()) {
                                                $jan = 'td-green';
                                            } else {
                                                $jan = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 2) {
                                            $feb = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 2)->exists()) {
                                                $feb = 'td-green';
                                            } else {
                                                $feb = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 3) {
                                            $mar = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 3)->exists()) {
                                                $mar = 'td-green';
                                            } else {
                                                $mar = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 4) {
                                            $apr = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 4)->exists()) {
                                                $apr = 'td-green';
                                            } else {
                                                $apr = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 5) {
                                            $may = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 5)->exists()) {
                                                    $may = 'td-green';
                                            } else {
                                                $may = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 6) {
                                            $jun = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 6)->exists()) {
                                                $jun = 'td-green';
                                            } else {
                                                $jun = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 7) {
                                            $jul = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 7)->exists()) {
                                                $jul = 'td-green';
                                            } else {
                                                $jul = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 8) {
                                            $aug = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 8)->exists()) {
                                                $aug = 'td-green';
                                            } else {
                                                $aug = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 9) {
                                            $sept = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 9)->exists()) {
                                                $sept = 'td-green';
                                            } else {
                                                $sept = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 10) {
                                            $oct = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 10)->exists()) {
                                                $oct = 'td-green';
                                            } else {
                                                $oct = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 11) {
                                            $nov ='td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 11)->exists()) {
                                                $nov = 'td-green';
                                            } else {
                                                $nov = 'td-yellow';
                                            }
                                        }
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != 12) {
                                            $dec = 'td-grey';
                                        } else {
                                            if(DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', 12)->exists()) {
                                                $dec = 'td-green';
                                            } else {
                                                $dec = 'td-yellow';
                                            }
                                        }
                                    }
                                @endphp

                                <tr>
                                    <td>{{$c->company}}</td>
                                    <td>{{date("M d", strtotime($c->fiscal_year_end))}}</td>
                                    <td>
                                        @if($c->tax_remittance == 'Monthly')
                                        <div class="td-remit td-remit-m">M</div>
                                        @elseif($c->tax_remittance == 'Quarterly')
                                        <div class="td-remit td-remit-q">Q</div>
                                        @elseif($c->tax_remittance == 'Yearly')
                                        <div class="td-remit td-remit-y">Y</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="{{$jan}}">
                                          @if($jan == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$feb}}">
                                            @if($feb == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$mar}}">
                                            @if($mar == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$apr}}">
                                            @if($apr == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$may}}">
                                            @if($may == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$jun}}">
                                            @if($jun == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$jul}}">
                                            @if($jul == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$aug}}">
                                            @if($aug == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$sept}}">
                                            @if($sept == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$oct}}">
                                            @if($oct == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$nov}}">
                                            @if($nov == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="{{$dec}}">
                                            @if($dec == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif
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
                <div style="border: 2px solid #d7d7d7;border-radius: 10px; padding: 15px;width: fit-content;" class="d-flex flex-column">
                    <div class="d-flex mb-3">
                        <div class="td-green mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            Remittance form found for client, year and month
                        </span>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="td-yellow mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            No remittance form found for client, year and month
                        </span>
                    </div>
                    <div class="d-flex">
                        <div class="td-grey mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            Does not remit for this month
                        </span>
                    </div>
                </div>
            </div>
    </div>
    <!-- page footer --->
    <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>
</div>
@elseif($filters->type == 'By Month')
@php
    $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
@endphp
<div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">
    <div class="block-header py-0 d-flex justify-content-between align-items-start"
        style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">

        <div>
            <a class="  section-header">Remittance Report by Month and Year
            </a>

        </div>


        <p style="font-size: 11pt !important;">Report run by {{Auth::user()->firstname . ' ' . Auth::user()->lastname}} on {{date("F d, Y")}}</p>
    </div>
    <div class="block-content pb-0   "
        style="padding-left: 32px;padding-right: 32px; padding-bottom: 20px !important;">

            <div class="col-sm-12">
                <div class="d-flex text-nowrap align-items-center">
                    <div>
                        <p class=" pr-1 mb-1 ">{{$monthNames[$filters->month - 1]}}-{{$filters->year}}</p>
                    </div>
                    <hr class="w-100" style="border-color: #595959!important">
                </div>
                <div class="row">
                    <div class="col-md-12 " style="    padding-left: 4rem!important;">
                        <table class="table border-0 table-period">
                            <thead>
                                <tr>
                                    <td style="width: 200px !important;">Client</td>
                                    <td>FYE</td>
                                    <td></td>
                                    <td>{{$monthNames[$filters->month - 1]}}-{{$filters->year}}</td>
                                    <td>Remittance</td>
                                    <td>Net Revenue</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $c)

                                @php
                                    $remit_month = 'td-grey';
                                    $remittance = "";
                                    $revenue = "";
                                    if($c->tax_remittance == 'Monthly') {
                                        $r = DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', $filters->month)->first();
                                        if($r) {
                                            $remit_month = 'td-green';
                                            $get_remittance_and_revenue = get_total_remittance_and_revenue($r->id);
                                            $remittance = $get_remittance_and_revenue->remittance;
                                            $revenue = $get_remittance_and_revenue->revenue;
                                        } else {
                                            $remit_month = 'td-yellow';
                                            $remittance = "Remittance not found";
                                        }
                                    }
                                    if($c->tax_remittance == 'Quarterly') {
                                        $client_quarters = calculateRemittanceMonths(explode(" ", $c->fiscal_year_end)[0]);
                                        if(!in_array($filters->month, $client_quarters)) {
                                            $remit_month = 'td-grey';
                                            $remittance = "Does not remit this month";
                                        } else {
                                            $r = DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', $filters->month)->first();
                                            if($r) {
                                                $remit_month = 'td-green';
                                                $get_remittance_and_revenue = get_total_remittance_and_revenue($r->id);
                                                $remittance = $get_remittance_and_revenue->remittance;
                                                $revenue = $get_remittance_and_revenue->revenue;
                                            } else {
                                                $remit_month = 'td-yellow';
                                                $remittance = "Remittance not found";
                                            }
                                        }
                                    }
                                    if($c->tax_remittance == 'Yearly') {
                                        if(intval(date("m", strtotime($c->fiscal_year_end))) != $filters->month) {
                                            $remit_month = 'td-grey';
                                            $remittance = "Does not remit this month";
                                        } else {
                                            $r = DB::table('remittances')->where('client', $c->id)->where('is_deleted', 0)->where('year', $filters->year)->where('month', $filters->month)->first();
                                            if($r) {
                                                $remit_month = 'td-green';
                                                $get_remittance_and_revenue = get_total_remittance_and_revenue($r->id);
                                                $remittance = $get_remittance_and_revenue->remittance;
                                                $revenue = $get_remittance_and_revenue->revenue;
                                            } else {
                                                $remit_month = 'td-yellow';
                                                $remittance = "Remittance not found";
                                            }
                                        }
                                    }


                                @endphp

                                <tr>
                                    <td>{{$c->company}}</td>
                                    <td>{{date("M d", strtotime($c->fiscal_year_end))}}</td>
                                    <td>
                                        @if($c->tax_remittance == 'Monthly')
                                        <div class="td-remit td-remit-m">M</div>
                                        @elseif($c->tax_remittance == 'Quarterly')
                                        <div class="td-remit td-remit-q">Q</div>
                                        @elseif($c->tax_remittance == 'Yearly')
                                        <div class="td-remit td-remit-y">Y</div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="{{$remit_month}}">
                                            @if($remit_month == 'td-green')  &#x2713; @else &nbsp;&nbsp;&nbsp; @endif</div>
                                    </td>

                                    <td>
                                        {{$remittance}}
                                    </td>
                                    <td>
                                        {{$revenue}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-5 pt-5">
                <div style="border: 2px solid #d7d7d7;border-radius: 10px; padding: 15px;width: fit-content;" class="d-flex flex-column">
                    <div class="d-flex mb-3">
                        <div class="td-green mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            Remittance form found for client, year and month
                        </span>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="td-yellow mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            No remittance form found for client, year and month
                        </span>
                    </div>
                    <div class="d-flex">
                        <div class="td-grey mr-3">
                            &nbsp;&nbsp;&nbsp;
                        </div>
                        <span style="font-size: 11pt;">
                            Does not remit for this month
                        </span>
                    </div>
                </div>
            </div>
    </div>



    <!-- page footer --->
    <p class="text-center mt-auto page-footer text-nowrap">{{$system_settings->company}} | {{$system_settings->address}} | {{$system_settings->telephone}}</p>


</div>
@endif
