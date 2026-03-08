@php

    function getFiscalYearEnd($period, $month, $year)

    {

        $monthCalendar = [$month];

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



    function findPeriod($fiscalStart, $dateString)

    {

        $startDate = strtotime($fiscalStart);

        $endDate = strtotime($dateString);



        $diffMonths =

            (date('Y', $endDate) - date('Y', $startDate)) * 12 + date('n', $endDate) - date('n', $startDate) + 1;

        $period = $diffMonths > 0 ? $diffMonths : 12 - abs($diffMonths % 12);



        if ($period > 12) {

            return '';

        }



        $periodString = str_pad($period, 2, '0', STR_PAD_LEFT);

        return $periodString;

    }

    function remittanceCalender($remittance, $month_no, $year)

    {

        $result = [];

        if ($remittance == 'Quarterly') {

            for ($i = 0; $i < 3; $i++) {

                $result[] = $month_no . '-' . $year;

                if ($month_no == 1) {

                    $month_no = 12;

                    $year--;

                } else {

                    $month_no--;

                }

            }

        } elseif ($remittance == 'Yearly') {

            for ($i = 0; $i < 12; $i++) {

                $result[] = $month_no . '-' . $year;

                if ($month_no == 1) {

                    $month_no = 12;

                    $year--;

                } else {

                    $month_no--;

                }

            }

        } elseif ($remittance == 'Monthly') {

            $result[] = $month_no . '-' . $year;

        }

        return $result;

    }



    function calculateRemittanceMonths($fiscalYearEndMonth)

    {

        $monthNumbers = [

            'January',

            'February',

            'March',

            'April',

            'May',

            'June',

            'July',

            'August',

            'September',

            'October',

            'November',

            'December',

        ];

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

            ->join('clients as c', function ($join) {

                $join->on('c.id', '=', 'r.client');

                $join->where('c.is_deleted', 0);

            })

            ->leftJoin('cities as p', function ($join) {

                $join->on('c.province', '=', 'p.state_name');

                $join->where('p.state_name', '=', 'c.province');

                $join->limit(1);

            })

            ->select(

                'r.*',

                'c.firstname',

                'c.lastname',

                'c.company as company_name',

                'c.display_name',

                'c.federal_tax',

                'c.provincial_tax',

                'c.federal_no',

                'c.provincial_no',

                'c.tax_remittance',

                'c.fiscal_start',

                'p.state_code as province_code',

                'c.default_prov',

            )

            ->first();



        $calender = remittanceCalender($q->tax_remittance, $q->month, $q->year);

        $calender_month = [];

        $calender_year = [];



        foreach ($calender as $c) {

            $arr = explode('-', $c);

            array_push($calender_month, intval($arr[0]));

            array_push($calender_year, intval($arr[1]));

        }



        $calender_month = array_values(array_unique($calender_month));

        $calender_year = array_values(array_unique($calender_year));



        $month = $q->month;

        $year = $q->year;



        $fs = $q->fiscal_start;

        $fiscal_start = explode('-', $fs);

        $fiscal_start = $fiscal_start[0];

        $period = findPeriod($fs, $fiscal_start . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01');

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



        $whereClauses = [['j.client', $q->client], ['j.is_deleted', 0]];



        // $_federal_debit = DB::table('journals as j')

        //     ->where($whereClauses)

        //     ->where('j.debit', '>', 0)

        //     ->where(function ($query) use (

        //         $tax_remittance,

        //         $fiscal_year_end,

        //         $month,

        //         $year,

        //         $calender,

        //         $calender_month,

        //         $calender_year,

        //         $federal_tax,

        //     ) {

        //         if ($tax_remittance == 'Monthly') {

        //             $query->where('j.month', $month)->where('j.year', $year);

        //         }

        //         if ($tax_remittance == 'Quarterly') {

        //             $query->where(function ($subquery) use ($calender) {

        //                 foreach ($calender as $key => $range) {

        //                     $_e = explode('-', $range);

        //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                     if ($key == 0) {

        //                         $subquery->where(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     } else {

        //                         $subquery->orWhere(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     }

        //                 }

        //             });

        //         }

        //         if ($tax_remittance == 'Yearly') {

        //             $query->where('j.fyear', $fiscal_year_end);

        //         }

        //         $query->where('j.account_no', $federal_tax);

        //     })

        //     ->leftJoin('source_code as sc', function ($join) {

        //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //     })

        //     ->select('j.*', 'sc.source_code')

        //     ->orderBy('j.editNo', 'asc')

        //     ->orderBy('j.debit', 'asc')

        //     ->get();



        // $_federal_credit = DB::table('journals as j')

        //     ->where($whereClauses)

        //     ->where('j.credit', '>', 0)

        //     ->where(function ($query) use (

        //         $tax_remittance,

        //         $fiscal_year_end,

        //         $month,

        //         $year,

        //         $calender,

        //         $calender_month,

        //         $calender_year,

        //         $federal_tax,

        //     ) {

        //         if ($tax_remittance == 'Monthly') {

        //             $query->where('j.month', $month)->where('j.year', $year);

        //         }

        //         if ($tax_remittance == 'Quarterly') {

        //             $query->where(function ($subquery) use ($calender) {

        //                 foreach ($calender as $key => $range) {

        //                     $_e = explode('-', $range);

        //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                     if ($key == 0) {

        //                         $subquery->where(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     } else {

        //                         $subquery->orWhere(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     }

        //                 }

        //             });

        //         }

        //         if ($tax_remittance == 'Yearly') {

        //             $query->where('j.fyear', $fiscal_year_end);

        //         }

        //         $query->where('j.account_no', $federal_tax);

        //     })

        //     ->leftJoin('source_code as sc', function ($join) {

        //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //     })

        //     ->select('j.*', 'sc.source_code')

        //     ->orderBy('j.editNo', 'asc')

        //     ->orderBy('j.credit', 'asc')

        //     ->get();

        // $_Rev_debit = DB::table('journals as j')

        //     ->where($whereClauses)

        //     ->where('j.debit', '>', 0)

        //     ->where(function ($query) use (

        //         $tax_remittance,

        //         $fiscal_year_end,

        //         $month,

        //         $year,

        //         $calender,

        //         $calender_month,

        //         $calender_year,

        //         $federal_tax,

        //     ) {

        //         if ($tax_remittance == 'Monthly') {

        //             $query->where('j.month', $month)->where('j.year', $year);

        //         }

        //         if ($tax_remittance == 'Quarterly') {

        //             $query->where(function ($subquery) use ($calender) {

        //                 foreach ($calender as $key => $range) {

        //                     $_e = explode('-', $range);

        //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                     if ($key == 0) {

        //                         $subquery->where(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     } else {

        //                         $subquery->orWhere(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     }

        //                 }

        //             });

        //         }

        //         if ($tax_remittance == 'Yearly') {

        //             $query->where('j.fyear', $fiscal_year_end);

        //         }

        //         $query->where('j.account_no', $federal_tax);

        //     })

        //     ->leftJoin('source_code as sc', function ($join) {

        //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //     })

        //     ->Join('clients_gifi as cg', function ($join) use ($q) {

        //         $join

        //             ->on('j.account_no', '=', 'cg.account_no')

        //             ->where('cg.client_id', $q->client)

        //             ->where('cg.is_deleted', 0)

        //             ->where('cg.sub_type', 'Revenue');

        //     })

        //     ->select('j.*', 'sc.source_code')

        //     ->get();



        // $_Rev_credit = DB::table('journals as j')

        //     ->where($whereClauses)

        //     ->where('j.credit', '>', 0)

        //     ->where(function ($query) use (

        //         $tax_remittance,

        //         $fiscal_year_end,

        //         $month,

        //         $year,

        //         $calender,

        //         $calender_month,

        //         $calender_year,

        //         $federal_tax,

        //     ) {

        //         if ($tax_remittance == 'Monthly') {

        //             $query->where('j.month', $month)->where('j.year', $year);

        //         }

        //         if ($tax_remittance == 'Quarterly') {

        //             $query->where(function ($subquery) use ($calender) {

        //                 foreach ($calender as $key => $range) {

        //                     $_e = explode('-', $range);

        //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                     if ($key == 0) {

        //                         $subquery->where(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     } else {

        //                         $subquery->orWhere(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     }

        //                 }

        //             });

        //         }

        //         if ($tax_remittance == 'Yearly') {

        //             $query->where('j.fyear', $fiscal_year_end);

        //         }

        //     })

        //     ->leftJoin('source_code as sc', function ($join) {

        //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //     })

        //     ->Join('clients_gifi as cg', function ($join) use ($q) {

        //         $join

        //             ->on('j.account_no', '=', 'cg.account_no')

        //             ->where('cg.client_id', $q->client)

        //             ->where('cg.is_deleted', 0)

        //             ->where('cg.sub_type', 'Revenue');

        //     })

        //     ->select('j.*', 'sc.source_code')

        //     ->get();



        // $_Exp_debit = DB::table('journals as j')

        //     ->where($whereClauses)

        //     ->where('j.debit', '>', 0)

        //     ->where(function ($query) use (

        //         $tax_remittance,

        //         $fiscal_year_end,

        //         $month,

        //         $year,

        //         $calender,

        //         $calender_month,

        //         $calender_year,

        //         $federal_tax,

        //     ) {

        //         if ($tax_remittance == 'Monthly') {

        //             $query->where('j.month', $month)->where('j.year', $year);

        //         }

        //         if ($tax_remittance == 'Quarterly') {

        //             $query->where(function ($subquery) use ($calender) {

        //                 foreach ($calender as $key => $range) {

        //                     $_e = explode('-', $range);

        //                     $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                     $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                     if ($key == 0) {

        //                         $subquery->where(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     } else {

        //                         $subquery->orWhere(function ($q) use ($m, $y) {

        //                             $q->where('j.month', $m)->where('j.year', $y);

        //                         });

        //                     }

        //                 }

        //             });

        //         }

        //         if ($tax_remittance == 'Yearly') {

        //             $query->where('j.fyear', $fiscal_year_end);

        //         }

        //     })

        //     ->leftJoin('source_code as sc', function ($join) {

        //         $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //     })

        //     ->Join('clients_gifi as cg', function ($join) use ($q) {

        //         $join

        //             ->on('j.account_no', '=', 'cg.account_no')

        //             ->where('cg.is_deleted', 0)

        //             ->where('cg.client_id', $q->client)

        //             ->where(function ($qry) {

        //                 $qry->where('cg.sub_type', 'Cost of sale')->orWhere('cg.sub_type', 'Operating expense');

        //             });

        //     })

        //     ->select('j.*', 'sc.source_code')

        //     ->get();



        // $_Exp_credit = DB::table('journals as j')

        // ->where($whereClauses)

        // ->where('j.credit', '>', 0)

        // ->where(function ($query) use (

        //     $tax_remittance,

        //     $fiscal_year_end,

        //     $month,

        //     $year,

        //     $calender,

        //     $calender_month,

        //     $calender_year,

        //     $federal_tax,

        // ) {

        //     if ($tax_remittance == 'Monthly') {

        //         $query->where('j.month', $month)->where('j.year', $year);

        //     }

        //     if ($tax_remittance == 'Quarterly') {

        //         $query->where(function ($subquery) use ($calender) {

        //             foreach ($calender as $key => $range) {

        //                 $_e = explode('-', $range);

        //                 $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                 $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                 if ($key == 0) {

        //                     $subquery->where(function ($q) use ($m, $y) {

        //                         $q->where('j.month', $m)->where('j.year', $y);

        //                     });

        //                 } else {

        //                     $subquery->orWhere(function ($q) use ($m, $y) {

        //                         $q->where('j.month', $m)->where('j.year', $y);

        //                     });

        //                 }

        //             }

        //         });

        //     }

        //     if ($tax_remittance == 'Yearly') {

        //         $query->where('j.fyear', $fiscal_year_end);

        //     }

        // })

        // ->leftJoin('source_code as sc', function ($join) {

        //     $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        // })

        // ->Join('clients_gifi as cg', function ($join) use ($q) {

        //     $join

        //         ->on('j.account_no', '=', 'cg.account_no')

        //         ->where('cg.is_deleted', 0)

        //         ->where('cg.client_id', $q->client)

        //         ->where(function ($qry) {

        //             $qry->where('cg.sub_type', 'Cost of sale')->orWhere('cg.sub_type', 'Operating expense');

        //         });

        // })

        // ->select('j.*', 'sc.source_code')

        // ->get();



        $_federal_debit = DB::table('remit_federal_debit')->where('remit_id', $q->id)->get();

        $_federal_credit = DB::table('remit_federal_credit')->where('remit_id', $q->id)->get();

        $_Rev_debit = DB::table('remit_rev_debit')->where('remit_id', $q->id)->get();

        $_Rev_credit = DB::table('remit_rev_credit')->where('remit_id', $q->id)->get();

        $_Exp_credit = DB::table('remit_exp_credit')->where('remit_id', $q->id)->get();

        $_Exp_debit = DB::table('remit_exp_debit')->where('remit_id', $q->id)->get();

        $_provincial_debit = [];

        $_provincial_credit = [];

        // if ($taxes == 'Both') {

        //     $_provincial_debit = DB::table('journals as j')

        //         ->where($whereClauses)

        //         ->where('j.debit', '>', 0)

        //         ->where(function ($query) use (

        //             $tax_remittance,

        //             $fiscal_year_end,

        //             $month,

        //             $year,

        //             $calender,

        //             $calender_month,

        //             $calender_year,

        //             $provincial_tax,

        //         ) {

        //             if ($tax_remittance == 'Monthly') {

        //                 $query->where('j.month', $month)->where('j.year', $year);

        //             }

        //             if ($tax_remittance == 'Quarterly') {

        //                 $query->where(function ($subquery) use ($calender) {

        //                     foreach ($calender as $key => $range) {

        //                         $_e = explode('-', $range);

        //                         $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                         $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                         if ($key == 0) {

        //                             $subquery->where(function ($q) use ($m, $y) {

        //                                 $q->where('j.month', $m)->where('j.year', $y);

        //                             });

        //                         } else {

        //                             $subquery->orWhere(function ($q) use ($m, $y) {

        //                                 $q->where('j.month', $m)->where('j.year', $y);

        //                             });

        //                         }

        //                     }

        //                 });

        //             }

        //             if ($tax_remittance == 'Yearly') {

        //                 $query->where('j.fyear', $fiscal_year_end);

        //             }

        //             $query->where('j.account_no', $provincial_tax);

        //         })

        //         ->leftJoin('source_code as sc', function ($join) {

        //             $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //         })

        //         ->select('j.*', 'sc.source_code')

        //         ->orderBy('j.editNo')

        //         ->orderBy('j.debit')

        //         ->get();



        //     $_provincial_credit = DB::table('journals as j')

        //         ->where($whereClauses)

        //         ->where('j.credit', '>', 0)

        //         ->where(function ($query) use (

        //             $tax_remittance,

        //             $fiscal_year_end,

        //             $month,

        //             $year,

        //             $calender,

        //             $calender_month,

        //             $calender_year,

        //             $provincial_tax,

        //         ) {

        //             if ($tax_remittance == 'Monthly') {

        //                 $query->where('j.month', $month)->where('j.year', $year);

        //             }

        //             if ($tax_remittance == 'Quarterly') {

        //                 $query->where(function ($subquery) use ($calender) {

        //                     foreach ($calender as $key => $range) {

        //                         $_e = explode('-', $range);

        //                         $m = date('m', strtotime($_e[1] . '-' . $_e[0]));

        //                         $y = date('Y', strtotime($_e[1] . '-' . $_e[0]));

        //                         if ($key == 0) {

        //                             $subquery->where(function ($q) use ($m, $y) {

        //                                 $q->where('j.month', $m)->where('j.year', $y);

        //                             });

        //                         } else {

        //                             $subquery->orWhere(function ($q) use ($m, $y) {

        //                                 $q->where('j.month', $m)->where('j.year', $y);

        //                             });

        //                         }

        //                     }

        //                 });

        //             }

        //             if ($tax_remittance == 'Yearly') {

        //                 $query->where('j.fyear', $fiscal_year_end);

        //             }

        //             $query->where('j.account_no', $provincial_tax);

        //         })

        //         ->leftJoin('source_code as sc', function ($join) {

        //             $join->on('j.source', '=', 'sc.id')->where('sc.is_deleted', 0);

        //         })

        //         ->select('j.*', 'sc.source_code')

        //         ->orderBy('j.editNo')

        //         ->orderBy('j.credit')

        //         ->get();

        // }

        if ($taxes == 'Both') {

            $_provincial_debit = DB::table('remit_provincial_debit')->where('remit_id', $q->id)->get();

            $_provincial_credit = DB::table('remit_provincial_credit')->where('remit_id', $q->id)->get();

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

        foreach ($_Rev_debit as $p) {

            $total_rev_debit += $p->debit;

        }



        $total_rev_credit = 0;

        foreach ($_Rev_credit as $p) {

            $total_rev_credit += $p->credit;

        }



        $total_exp_debit = 0;

        foreach ($_Exp_debit as $p) {

            $total_exp_debit += $p->debit;

        }



        $total_exp_credit = 0;

        foreach ($_Exp_credit as $p) {

            $total_exp_credit += $p->credit;

        }



        $total_remittance = $federal_remit + $provincial_remit;

        $total_debit = $federal_debit + $provincial_debit;

        $total_credit = $federal_credit + $provincial_credit;



        $total_rev = $total_rev_credit - $total_rev_debit;



        $total_exp = $total_exp_debit - $total_exp_credit;



        $net_revenue = $total_rev - $total_exp;



        $remittance =

            $total_remittance <= 0

                ? '($ ' . number_format(abs($total_remittance), 2) . ')'

                : '$ ' . number_format($total_remittance, 2);
        $revenue =

            $total_rev <= 0

                ? '($ ' . number_format(abs($total_rev), 2) . ')'

                : '$ ' . number_format($total_rev, 2);



        return (object) compact('remittance', 'revenue');

    }

@endphp

<div id="printableArea">

    @if ($filters->type == 'By Year')

        <div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">

            <div class="block-header py-0 d-flex justify-content-between align-items-start"

                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">



                <div>

                    {{-- <a class="  section-header">Remittance Report by Year --}}

                    <a class="  section-header">Sales Tax Remittance Status by Year

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

                            <p class=" pr-1 mb-1 ">{{ $filters->year }}</p>

                        </div>

                        <hr class="w-100" style="border-color: #595959!important">

                    </div>

                    <div class="row">

                        <div class="col-md-12 table-div" style="padding-left: 1.5rem;">

                            <table class="table border-0 table-period">

                                <thead>

                                    <tr>

                                        <td style="width: 150px;" class="first-th"></td>

                                        <td></td>

                                        <td></td>

                                        <td>

                                            <div class="td-month">Jan</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Feb</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Mar</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Apr</div>

                                        </td>

                                        <td>

                                            <div class="td-month">May</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Jun</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Jul</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Aug</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Sep</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Oct</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Nov</div>

                                        </td>

                                        <td>

                                            <div class="td-month">Dec</div>

                                        </td>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($clients as $c)

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

                                            $remittance = '';

                                            $revenue = '';

                                            $fiscal_start = explode('-', $c->fiscal_start);

                                            $fiscal_start_year = (int) $fiscal_start[0];

                                            $fiscal_start_month = (int) $fiscal_start[1];

                                            $current_year = (int) $filters->year;

                                            // if($c->id == 12) {

                                            //     echo "<pre>";

                                            //     var_dump($fiscal_start_year, $fiscal_start_month, $current_year, $c->id);

                                            // }

                                            if ($c->tax_remittance == 'Monthly') {

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 1)

                                                        ->exists()

                                                ) {

                                                    $jan = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 1)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 1

                                                    ) {

                                                        $jan = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $jan = 'td-green-new';

                                                    } else {

                                                        $jan = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 2)

                                                        ->exists()

                                                ) {

                                                    $feb = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 2)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 2

                                                    ) {

                                                        $feb = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $feb = 'td-green-new';

                                                    } else {

                                                        $feb = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 3)

                                                        ->exists()

                                                ) {

                                                    $mar = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 3)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 3

                                                    ) {

                                                        $mar = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $mar = 'td-green-new';

                                                    } else {

                                                        $mar = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 4)

                                                        ->exists()

                                                ) {

                                                    $apr = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 4)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 4

                                                    ) {

                                                        $apr = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $apr = 'td-green-new';

                                                    } else {

                                                        $apr = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 5)

                                                        ->exists()

                                                ) {

                                                    $may = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 5)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 5

                                                    ) {

                                                        $may = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $may = 'td-green-new';

                                                    } else {

                                                        $may = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 6)

                                                        ->exists()

                                                ) {

                                                    $jun = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 6)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 6

                                                    ) {

                                                        $jun = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $jun = 'td-green-new';

                                                    } else {

                                                        $jun = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 7)

                                                        ->exists()

                                                ) {

                                                    $jul = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 7)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 7

                                                    ) {

                                                        $jul = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $jul = 'td-green-new';

                                                    } else {

                                                        $jul = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 8)

                                                        ->exists()

                                                ) {

                                                    $aug = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 8)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 8

                                                    ) {

                                                        $aug = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $aug = 'td-green-new';

                                                    } else {

                                                        $aug = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 9)

                                                        ->exists()

                                                ) {

                                                    $sept = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 9)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 9

                                                    ) {

                                                        $sept = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $sept = 'td-green-new';

                                                    } else {

                                                        $sept = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 10)

                                                        ->exists()

                                                ) {

                                                    $oct = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 10)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 10

                                                    ) {

                                                        $oct = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $oct = 'td-green-new';

                                                    } else {

                                                        $oct = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 11)

                                                        ->exists()

                                                ) {

                                                    $nov = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 11)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 11

                                                    ) {

                                                        $nov = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $nov = 'td-green-new';

                                                    } else {

                                                        $nov = 'td-grey';

                                                    }

                                                }

                                                if (

                                                    DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 12)

                                                        ->exists()

                                                ) {

                                                    $dec = 'td-green';

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', 12)

                                                        ->first();

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                } else {

                                                    if (

                                                        $fiscal_start_year == $current_year &&

                                                        $fiscal_start_month <= 12

                                                    ) {

                                                        $dec = 'td-green-new';

                                                    } elseif ($fiscal_start_year < $current_year) {

                                                        $dec = 'td-green-new';

                                                    } else {

                                                        $dec = 'td-grey';

                                                    }

                                                }

                                            }

                                            if ($c->tax_remittance == 'Quarterly') {

                                                $client_quarters = calculateRemittanceMonths(

                                                    explode(' ', $c->fiscal_year_end)[0],

                                                );

                                                if (!in_array(1, $client_quarters)) {

                                                    $jan = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 1)

                                                            ->exists()

                                                    ) {

                                                        $jan = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 1)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $jan = 'td-yellow';

                                                        if (

                                                            in_array(1, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 1

                                                        ) {

                                                            $jan = 'td-green-new';

                                                        } elseif (

                                                            in_array(1, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $jan = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(2, $client_quarters)) {

                                                    $feb = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 2)

                                                            ->exists()

                                                    ) {

                                                        $feb = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 2)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        if (

                                                            in_array(2, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 2

                                                        ) {

                                                            $feb = 'td-green-new';

                                                        } elseif (

                                                            in_array(2, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $feb = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(3, $client_quarters)) {

                                                    $mar = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 3)

                                                            ->exists()

                                                    ) {

                                                        $mar = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 3)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $mar = 'td-yellow';

                                                        if (

                                                            in_array(3, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 3

                                                        ) {

                                                            $mar = 'td-green-new';

                                                        } elseif (

                                                            in_array(3, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $mar = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(4, $client_quarters)) {

                                                    $apr = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 4)

                                                            ->exists()

                                                    ) {

                                                        $apr = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 4)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $apr = 'td-yellow';

                                                        if (

                                                            in_array(4, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 4

                                                        ) {

                                                            $apr = 'td-green-new';

                                                        } elseif (

                                                            in_array(4, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $apr = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(5, $client_quarters)) {

                                                    $may = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 5)

                                                            ->exists()

                                                    ) {

                                                        $may = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 5)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $may = 'td-yellow';

                                                        if (

                                                            in_array(5, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 5

                                                        ) {

                                                            $may = 'td-green-new';

                                                        } elseif (

                                                            in_array(5, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $may = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(6, $client_quarters)) {

                                                    $jun = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 6)

                                                            ->exists()

                                                    ) {

                                                        $jun = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 6)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $jun = 'td-yellow';

                                                        if (

                                                            in_array(6, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 6

                                                        ) {

                                                            $jun = 'td-green-new';

                                                        } elseif (

                                                            in_array(6, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $jun = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(7, $client_quarters)) {

                                                    $jul = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 7)

                                                            ->exists()

                                                    ) {

                                                        $jul = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 7)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $jul = 'td-yellow';

                                                        if (

                                                            in_array(7, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 7

                                                        ) {

                                                            $jul = 'td-green-new';

                                                        } elseif (

                                                            in_array(7, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $jul = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(8, $client_quarters)) {

                                                    $aug = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 8)

                                                            ->exists()

                                                    ) {

                                                        $aug = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 8)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $aug = 'td-yellow';

                                                        if (

                                                            in_array(8, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 8

                                                        ) {

                                                            $aug = 'td-green-new';

                                                        } elseif (

                                                            in_array(8, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $aug = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(9, $client_quarters)) {

                                                    $sept = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 9)

                                                            ->exists()

                                                    ) {

                                                        $sept = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 9)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $sept = 'td-yellow';

                                                        if (

                                                            in_array(9, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 9

                                                        ) {

                                                            $sept = 'td-green-new';

                                                        } elseif (

                                                            in_array(9, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $sept = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(10, $client_quarters)) {

                                                    $oct = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 10)

                                                            ->exists()

                                                    ) {

                                                        $oct = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 10)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $oct = 'td-yellow';

                                                        if (

                                                            in_array(10, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 10

                                                        ) {

                                                            $oct = 'td-green-new';

                                                        } elseif (

                                                            in_array(10, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $oct = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(11, $client_quarters)) {

                                                    $nov = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 11)

                                                            ->exists()

                                                    ) {

                                                        $nov = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 11)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $nov = 'td-yellow';

                                                        if (

                                                            in_array(11, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 11

                                                        ) {

                                                            $nov = 'td-green-new';

                                                        } elseif (

                                                            in_array(11, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $nov = 'td-green-new';

                                                        }

                                                    }

                                                }

                                                if (!in_array(12, $client_quarters)) {

                                                    $dec = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 12)

                                                            ->exists()

                                                    ) {

                                                        $dec = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 12)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                    } else {

                                                        // $dec = 'td-yellow';

                                                        if (

                                                            in_array(12, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 12

                                                        ) {

                                                            $dec = 'td-green-new';

                                                        } elseif (

                                                            in_array(12, $client_quarters) &&

                                                            $fiscal_start_year < $current_year

                                                        ) {

                                                            $dec = 'td-green-new';

                                                        }

                                                    }

                                                }

                                            }



                                            if ($c->tax_remittance == 'Yearly') {

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 1) {

                                                    $jan = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 1)

                                                            ->exists()

                                                    ) {

                                                        $jan = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 1)

                                                            ->first();

                                                    } else {

                                                        // $jan = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 2) {

                                                    $feb = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 2)

                                                            ->exists()

                                                    ) {

                                                        $feb = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 2)

                                                            ->first();

                                                    } else {

                                                        // $feb = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 3) {

                                                    $mar = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 3)

                                                            ->exists()

                                                    ) {

                                                        $mar = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 3)

                                                            ->first();

                                                    } else {

                                                        // $mar = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 4) {

                                                    $apr = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 4)

                                                            ->exists()

                                                    ) {

                                                        $apr = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 4)

                                                            ->first();

                                                    } else {

                                                        // $apr = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 5) {

                                                    $may = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 5)

                                                            ->exists()

                                                    ) {

                                                        $may = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 5)

                                                            ->first();

                                                    } else {

                                                        // $may = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 6) {

                                                    $jun = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 6)

                                                            ->exists()

                                                    ) {

                                                        $jun = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 6)

                                                            ->first();

                                                    } else {

                                                        // $jun = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 7) {

                                                    $jul = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 7)

                                                            ->exists()

                                                    ) {

                                                        $jul = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 7)

                                                            ->first();

                                                    } else {

                                                        // $jul = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 8) {

                                                    $aug = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 8)

                                                            ->exists()

                                                    ) {

                                                        $aug = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 8)

                                                            ->first();

                                                    } else {

                                                        // $aug = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 9) {

                                                    $sept = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 9)

                                                            ->exists()

                                                    ) {

                                                        $sept = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 9)

                                                            ->first();

                                                    } else {

                                                        // $sept = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 10) {

                                                    $oct = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 10)

                                                            ->exists()

                                                    ) {

                                                        $oct = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 10)

                                                            ->first();

                                                    } else {

                                                        // $oct = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 11) {

                                                    $nov = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 11)

                                                            ->exists()

                                                    ) {

                                                        $nov = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 11)

                                                            ->first();

                                                    } else {

                                                        // $nov = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                                if (intval(date('m', strtotime($c->fiscal_year_end))) != 12) {

                                                    $dec = 'td-grey';

                                                } else {

                                                    if (

                                                        DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 12)

                                                            ->exists()

                                                    ) {

                                                        $dec = 'td-green';

                                                        $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 12)

                                                            ->first();

                                                    } else {

                                                        // $dec = 'td-yellow';

                                                    }

                                                    if (@$r) {

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    }

                                                }

                                            }



                                        @endphp



                                        <tr>

                                            <td style="vertical-align: middle;" class="first-td-text">

                                                {{ $c->display_name }}

                                            </td>

                                            <td style="vertical-align: middle;">

                                                {{ date('M d', strtotime($c->fiscal_year_end)) }}</td>

                                            <td style="vertical-align: middle;">

                                                @if ($c->tax_remittance == 'Monthly')

                                                    <div class="td-remit td-remit-m">M</div>

                                                @elseif($c->tax_remittance == 'Quarterly')

                                                    <div class="td-remit td-remit-q">Q</div>

                                                @elseif($c->tax_remittance == 'Yearly')

                                                    <div class="td-remit td-remit-y">Y</div>

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $jan }} mb-1">

                                                    @if ($jan == 'td-green')

                                                        {{-- &#x2713;  --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 1)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($jan == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 1)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(1, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 1)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(1, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $feb }} mb-1">

                                                    @if ($feb == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 2)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($feb == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 2)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(2, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 2)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(2, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $mar }} mb-1">

                                                    @if ($mar == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 3)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($mar == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 3)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(3, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 3)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(3, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $apr }} mb-1">

                                                    @if ($apr == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 4)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($apr == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 4)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(4, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 4)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(4, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $may }} mb-1">

                                                    @if ($may == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 5)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($may == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 5)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(5, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 5)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(5, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $jun }} mb-1">

                                                    @if ($jun == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 6)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($jun == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 6)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(6, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 6)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(6, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $jul }} mb-1">

                                                    @if ($jul == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 7)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($jul == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 7)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(7, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 7)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(7, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $aug }} mb-1">

                                                    @if ($aug == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 8)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($aug == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 8)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(8, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 8)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(8, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $sept }} mb-1">

                                                    @if ($sept == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 9)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($sept == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 9)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(9, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 9)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(9, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $oct }} mb-1">

                                                    @if ($oct == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 10)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($oct == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 10)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(10, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 10)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(10, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $nov }} mb-1">

                                                    @if ($nov == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 11)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($nov == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 11)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(11, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 11)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(11, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

                                            </td>

                                            <td>

                                                <div class="{{ $dec }} mb-1">

                                                    @if ($dec == 'td-green')

                                                        {{-- &#x2713; --}}

                                                        @php

                                                            $r = DB::table('remittances')

                                                            ->where('client', $c->id)

                                                            ->where('is_deleted', 0)

                                                            ->where('year', $filters->year)

                                                            ->where('month', 12)

                                                            ->first();

                                                        if (@$r) {

                                                            $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                                $r->id,

                                                            );

                                                            $remittance = $get_remittance_and_revenue->remittance;

                                                            $revenue = $get_remittance_and_revenue->revenue;

                                                            

                                                        }

                                                        @endphp

                                                        {{ $remittance }}

                                                    @else

                                                        &nbsp;

                                                    @endif

                                                </div>

                                                @if ($dec == 'td-green')

                                                    @if ($r->remit_status == 'paid')

                                                        <div class="td-paid">

                                                            {{ $r->ref_no }}

                                                        </div>

                                                    @elseif ($r->remit_status == 'not paid')

                                                        <div class="td-not-paid">

                                                            NOT PAID

                                                        </div>

                                                    @elseif ($r->remit_status == 'refund')

                                                        <div class="td-refund">

                                                            REFUND

                                                        </div>

                                                    @else

                                                        <div class="td-not-found">

                                                            NOT FOUND

                                                        </div>

                                                    @endif

                                                @else

                                                    @if ($c->tax_remittance == 'Monthly' && $fiscal_start_year == $current_year && $fiscal_start_month <= 12)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Monthly' && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif(

                                                        $c->tax_remittance == 'Quarterly' &&

                                                            in_array(12, $client_quarters) &&

                                                            $fiscal_start_year == $current_year &&

                                                            $fiscal_start_month <= 12)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @elseif($c->tax_remittance == 'Quarterly' && in_array(12, $client_quarters) && $fiscal_start_year < $current_year)

                                                        <div class="td-not-found">

                                                            Not Found

                                                        </div>

                                                    @else

                                                        <div class="td-grey">

                                                            &nbsp;

                                                        </div>

                                                    @endif

                                                @endif

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

                        {{-- <div class="d-flex mb-3">

                        <div class="td-green mr-3" style="width: fit-content !important; background: #92d050; padding: 2px 7px;">

                            &nbsp;&nbsp;&nbsp;

                        </div>

                        <span style="font-size: 11pt;">

                            Remittance form found for client, year and month

                        </span>

                    </div>

                    <div class="d-flex mb-3">

                        <div class="td-yellow mr-3" style="width: fit-content !important;">

                            &nbsp;&nbsp;&nbsp;

                        </div>

                        <span style="font-size: 11pt;">

                            No remittance form found for client, year and month

                        </span>

                    </div> --}}

                        <div class="d-flex">

                            <div class="td-grey mr-3"

                                style="width: fit-content !important;font-size: 11pt;line-height: 1;padding: 3px 7px;">

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

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |

                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>

        </div>

    @elseif($filters->type == 'By Month')

        @php

            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

        @endphp

        <div class="block new-block position-relative  5 d-flex flex-column" style="min-height: 92vh;">

            <div class="block-header py-0 d-flex justify-content-between align-items-start"

                style="padding-left:32px;padding-right: 32px;padding-top:20px !important;">



                <div>

                    {{-- <a class="  section-header">Remittance Report by Month and Year --}}

                    <a class="  section-header">Sales Tax Remittance Status for

                        {{ $monthNames[$filters->month - 1] }}-{{ $filters->year }}

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

                            <p class=" pr-1 mb-1 ">{{ $monthNames[$filters->month - 1] }}-{{ $filters->year }}</p>

                        </div>

                        <hr class="w-100" style="border-color: #595959!important">

                    </div>

                    <div class="row">

                        <div class="col-md-12 " style="    padding-left: 3.5rem!important;">

                            <table class="table border-0 table-period">

                                <thead>

                                    <tr>

                                        <td

                                            style="width: 200px !important;font-family: Signika !important; font-weight: 700;">

                                            Client</td>

                                        <td

                                            style="width: 100px !important;font-family: Signika !important; font-weight: 700;">

                                            FYE</td>

                                        <td

                                            style="width: 100px !important;font-family: Signika !important; font-weight: 700;">

                                            Remits</td>

                                        {{-- <td>{{ $monthNames[$filters->month - 1] }}-{{ $filters->year }}</td> --}}

                                        <td class="text-center"

                                            style="font-family: Signika !important; font-weight: 700;">Status</td>

                                        <td style="font-family: Signika !important; font-weight: 700;">Amount</td>

                                        <td style="font-family: Signika !important; font-weight: 700;">Net Revenue</td>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($clients as $c)

                                        @php

                                            $remit_month = 'td-grey-new';

                                            $remittance = '';

                                            $revenue = '';

                                            if ($c->tax_remittance == 'Monthly') {

                                                $r = DB::table('remittances')

                                                    ->where('client', $c->id)

                                                    ->where('is_deleted', 0)

                                                    ->where('year', $filters->year)

                                                    ->where('month', $filters->month)

                                                    ->first();

                                                if ($r) {

                                                    // $remit_month = 'td-green';

                                                    $remit_text = '';

                                                    // $remit_month = 'td-green';

                                                    if ($r->remit_status == 'paid') {

                                                        $remit_month = 'td-paid';

                                                        $remit_text = $r->ref_no;

                                                    } elseif ($r->remit_status == 'not paid') {

                                                        $remit_month = 'td-not-paid';

                                                        $remit_text = 'NOT PAID';

                                                    } else {

                                                        $remit_month = 'td-refund';

                                                        $remit_text = 'REFUND';

                                                    }

                                                    $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                        $r->id,

                                                    );

                                                    $remittance = $get_remittance_and_revenue->remittance;

                                                    $revenue = $get_remittance_and_revenue->revenue;

                                                } else {

                                                    $remit_month = 'td-yellow';

                                                    $remittance = '';

                                                }

                                            }

                                            if ($c->tax_remittance == 'Quarterly') {

                                                $client_quarters = calculateRemittanceMonths(

                                                    explode(' ', $c->fiscal_year_end)[0],

                                                );

                                                if (!in_array($filters->month, $client_quarters)) {

                                                    $remit_month = 'td-grey-new';

                                                    $remittance = '';

                                                } else {

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', $filters->month)

                                                        ->first();

                                                    if ($r) {

                                                        // $remit_month = 'td-green';

                                                        $remit_text = '';

                                                        // $remit_month = 'td-green';

                                                        if ($r->remit_status == 'paid') {

                                                            $remit_month = 'td-paid';

                                                            $remit_text = $r->ref_no;

                                                        } elseif ($r->remit_status == 'not paid') {

                                                            $remit_month = 'td-not-paid';

                                                            $remit_text = 'NOT PAID';

                                                        } else {

                                                            $remit_month = 'td-refund';

                                                            $remit_text = 'REFUND';

                                                        }

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    } else {

                                                        $remit_month = 'td-yellow';

                                                        $remittance = '';

                                                    }

                                                }

                                            }

                                            if ($c->tax_remittance == 'Yearly') {

                                                if (

                                                    intval(date('m', strtotime($c->fiscal_year_end))) != $filters->month

                                                ) {

                                                    $remit_month = 'td-grey-new';

                                                    $remittance = '';

                                                } else {

                                                    $r = DB::table('remittances')

                                                        ->where('client', $c->id)

                                                        ->where('is_deleted', 0)

                                                        ->where('year', $filters->year)

                                                        ->where('month', $filters->month)

                                                        ->first();

                                                    if ($r) {

                                                        $remit_text = '';

                                                        // $remit_month = 'td-green';

                                                        if ($r->remit_status == 'paid') {

                                                            $remit_month = 'td-paid';

                                                            $remit_text = $r->ref_no;

                                                        } elseif ($r->remit_status == 'not paid') {

                                                            $remit_month = 'td-not-paid';

                                                            $remit_text = 'NOT PAID';

                                                        } else {

                                                            $remit_month = 'td-refund';

                                                            $remit_text = 'REFUND';

                                                        }

                                                        $get_remittance_and_revenue = get_total_remittance_and_revenue(

                                                            $r->id,

                                                        );

                                                        $remittance = $get_remittance_and_revenue->remittance;

                                                        $revenue = $get_remittance_and_revenue->revenue;

                                                    } else {

                                                        $remit_month = 'td-yellow';

                                                        $remittance = '';

                                                    }

                                                }

                                            }



                                        @endphp



                                        <tr>

                                            <td>{{ $c->display_name }}</td>

                                            <td>{{ date('M d', strtotime($c->fiscal_year_end)) }}</td>

                                            <td>

                                                @if ($c->tax_remittance == 'Monthly')

                                                    <div class="td-remit td-remit-m">M</div>

                                                @elseif($c->tax_remittance == 'Quarterly')

                                                    <div class="td-remit td-remit-q">Q</div>

                                                @elseif($c->tax_remittance == 'Yearly')

                                                    <div class="td-remit td-remit-y">Y</div>

                                                @endif

                                            </td>



                                            <td class="d-flex justify-content-center">

                                                <div class="{{ $remit_month }}"

                                                    style="padding: 8px; width: 130px;font-size: 11pt; border-radius: 8px;">

                                                    @if ($remit_month == 'td-paid')

                                                        {{-- &#x2713; --}}

                                                        {{-- {{ $remittance }} --}}

                                                        {{ $r->ref_no }}

                                                    @elseif ($remit_month == 'td-refund')

                                                        Refund

                                                    @elseif ($remit_month == 'td-not-paid')

                                                        Not Paid

                                                    @elseif ($remit_month == 'td-yellow')

                                                        NOT FOUND

                                                    @elseif ($remit_month == 'td-grey-new')

                                                        Dose not remit

                                                    @else

                                                        &nbsp;&nbsp;&nbsp;

                                                    @endif

                                                </div>

                                            </td>



                                            <td>

                                                {{ $remittance }}

                                            </td>

                                            <td>

                                                {{ $revenue }}

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>



                {{-- <div class="col-md-12 mt-5 pt-5">

                <div style="border: 2px solid #d7d7d7;border-radius: 10px; padding: 15px;width: fit-content;"

                    class="d-flex flex-column">

                    <div class="d-flex mb-3">

                        <div class="td-green mr-3" style="width: fit-content !important; background: #92d050;">

                            &nbsp;&nbsp;&nbsp;

                        </div>

                        <span style="font-size: 11pt;">

                            Remittance form found for client, year and month

                        </span>

                    </div>

                    <div class="d-flex mb-3">

                        <div class="td-yellow mr-3" style="width: fit-content !important;">

                            &nbsp;&nbsp;&nbsp;

                        </div>

                        <span style="font-size: 11pt;">

                            No remittance form found for client, year and month

                        </span>

                    </div>

                    <div class="d-flex">

                        <div class="td-grey mr-3" style="width: fit-content !important;">

                            &nbsp;&nbsp;&nbsp;

                        </div>

                        <span style="font-size: 11pt;">

                            Does not remit for this month

                        </span>

                    </div>

                </div>

            </div> --}}

            </div>







            <!-- page footer --->

            <p class="text-center mt-auto page-footer text-nowrap">{{ $system_settings->company }} |

                {{ $system_settings->address }} | {{ $system_settings->telephone }}</p>





        </div>

    @endif

</div>

