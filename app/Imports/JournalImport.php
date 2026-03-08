<?php

namespace App\Imports;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class JournalImport implements ToCollection, WithStartRow
{
    public $data, $client;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function __construct($client)
    {
        $this->client = $client;
    }
    public function startRow(): int
    {
        return 2;
    }
    // private function getFiscalYearEnd($fiscalStart)
    // {
    //     $parts = explode('-', $fiscalStart);
    //     $year = intval($parts[0]);
    //     $month = intval($parts[1]);
    //     $day = intval($parts[2]);

    //     $fiscalYearEnd = new DateTime(($year + 1) . '-' . $month . '-' . $day);
    //     $fiscalYearEnd->modify('-1 day');

    //     $fiscalYear = $fiscalYearEnd->format('Y');
    //     $fiscalMonth = $fiscalYearEnd->format('n');
    //     $fiscalDay = $fiscalYearEnd->format('j');

    //     //$fiscalYearEndFormatted = 'Fiscal Year End ' . $this->monthToStringShort($fiscalMonth) . ' ' . $fiscalYear;
    //     return $fiscalYear;
    // }


    private function getFiscalYearEnd($fmonth ,$period, $month, $year)
    {
        if($fmonth == 1) {
                        return $year;
        }
        $monthCalendar = [$month];
        while($period <= 12) {
            $month = $month + 1;
            if($month == 13) {
                $month = 1;
            }
            array_push($monthCalendar, $month);
            if(count($monthCalendar) == 12) {
                break;
            }
            $period++;
        }
        foreach($monthCalendar as $key => $m) {
            if($m == 1 && $key != 0) {
                $year++;
            }
        }
        return $year;
    }

    private function monthToStringShort($month)
    {
        $months = array(
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        );
        return $months[$month];
    }

    private function findPeriod($fiscalStart, $dateString)
    {
        $startDate = new DateTime($fiscalStart);
        $endDate = new DateTime($dateString);

        // if ($endDate < $startDate) {
        //     return "";
        // }

        $diffMonths = ($endDate->format('Y') - $startDate->format('Y')) * 12 + ($endDate->format('m') - $startDate->format('m')) + 1;
        $period = ($diffMonths > 0) ? $diffMonths : 12 - abs($diffMonths % 12);
        if ($period > 12) {
            return "";
        }
        //$periodString = "Period " . sprintf("%02d", $period);

        return $period;
    }
    private function getMonthNumber($dateString)
    {
        try {
            $date = DateTime::createFromFormat('m/d/Y', $dateString);
            return $date->format('n');
        } catch (\Exception $e) {
            abort(500, "Internal Server Error");
        }
    }
    private function parseDate($dateString, $returnFormat)
    {
        try {
            $date = DateTime::createFromFormat('m/d/Y', $dateString);
            return $date->format($returnFormat);
        } catch (\Exception $e) {
            abort(400, "Internal Server Error");
        }
    }

    private function calcTaxes($p_tx1, $p_tx2, $debit, $credit, $applied_to_tax1)
    {

        $p_tx1 = str_replace('%', '', trim($p_tx1));
        $p_tx2 = str_replace('%', '', trim($p_tx2));

        if($p_tx1 != '') {
            $p_tx1 = floatval($p_tx1) / 100;
        }
        if($p_tx1 != '') {
            $p_tx2 = floatval($p_tx2) / 100;
        }
        $gross =0;
        $net = 0;
        if($debit > 0) {
            $gross = $debit;
        } else if($credit > 0) {
            $gross = $credit;
        }
        $tax1 = 0;
        $tax2 = 0;

        if($applied_to_tax1 == 1) {
             /**
            * CASE3
            * Net = gross / ((1+tax1%) + tax2% * (1+tax1%))
            * Tax1Amount = Net * Tax1%
            * Tax2Amount = (Net+tax1Amount) * Tax2%
            * Validation: Gross = net + tax1Amount + tax2Amount
            */

            $net = $gross / ((1+$p_tx1) + $p_tx2 * (1+$p_tx1));
            $tax1 = $net * $p_tx1;
            $tax2 = ($net + $tax1) * $p_tx2;
        } else {
            if($p_tx2 == '') {
                /**
                * CASE1
                * Net = Gross / (1 + Tax1%)
                * Tax1Amount = Net * Tax1%
                * Validation: Gross = net + tax1Amount
                */
                $net = $gross / (1 + $p_tx1);
                $tax1 = $net * $p_tx1;
            } else {
                 /**
                * CASE2
                * Net = Gross / (1 + Tax1% + Tax2%)
                * Tax1Amount = Net * Tax1%
                * Tax2 Amount = Net * Tax2%
                * Validation: Gross = net + tax1Amount + tax2Amount
                */
                $net = $gross / (1 + $p_tx1 + $p_tx2);
                $tax1 = $net * $p_tx1;
                $tax2 = $net * $p_tx2;
            }
        }

        $result = [
            'tax1' => $tax1,
            'tax2' => $tax2,
            'net' => $net,
        ];

        return $result;
    }
    private function calcNet($debit, $credit, $tax1, $tax2, $taxable)
    {
        $net = 0;
        $amount = 0;

        if ($debit > 0) {
            $amount = $debit;
        } else if ($credit > 0) {
            $amount = $credit;
        }

        if ($taxable == 1) {
            $net = $amount - ($tax1 + $tax2);
        } else {
            $net = $amount;
        }

        return $net;
    }



    public function collection(Collection $rows)
    {


        $array = array();


        foreach ($rows as $r) {
            $source_code = trim($r[0]);
            $account_no = trim($r[1]);
            $ref_no = trim($r[2]);

            $taxable = trim($r[3]) == 'Yes' || trim($r[3]) == 'Y' || trim($r[3]) == 'YES' || trim($r[3]) == 'TRUE' || trim($r[3]) == 'true' || trim($r[3]) == 'True' || trim($r[3]) == '1' ? 1 : 0;
            $portion = floatval(str_replace('%', '', trim($r[4]))) > 0 ? floatval(str_replace('%', '', trim($r[4]))) : 100.00;
            $gl_date = trim($r[5]);
            $description = trim($r[6]);
            $debit = $r[7] == null || $r[7] == "" ? 0.00 : floatval(trim($r[7]));
            $credit = $r[8] == null || $r[8] == "" ? 0.00 : floatval(trim($r[8]));
                $source = DB::table('source_code')->where('is_deleted', 0)->where('source_code', $source_code)->first();

            if (@$source) {
                $month = $this->parseDate($gl_date, "m");//client month
                $year = $this->parseDate($gl_date, "Y");//client year
                $fiscal_start = $this->client->fiscal_start;//client fiscal year
                $fyear = explode("-", $fiscal_start);//fiscal year
                $period = $this->findPeriod($fiscal_start, $fyear[0] . "-" . $month . "-01");//find period base on fyear and client month
                $fiscal_end = $this->getFiscalYearEnd(intval($fyear[1]) ,intval($period), intval($month), intval($year));//find fiscal end

                $date = $this->parseDate($gl_date, "dmY");

                $lastInsertedEditNo = 0;
                $fyearLatestJournal = DB::table('journals')
                ->where('fyear', $fiscal_end)
                ->where('client', $this->client->id)
                ->where('is_deleted', 0)
                ->orderBy('editNo', 'desc')
                ->first();

                if (@$fyearLatestJournal) {
                    $lastInsertedEditNo = $fyearLatestJournal->editNo;
                }
                if ($period != "") {
                    $tax_rate = DB::table('tax_rate')->where('is_deleted', 0)->where('province', $this->client->province)->first();


                    $taxes = [
                        'tax1' => null,
                        'tax2' => null
                    ];
                    $net = null;
                    if (@$tax_rate) {
                        $calc = $this->calcTaxes($tax_rate->tax_rate_1, $tax_rate->tax_rate_2, $debit, $credit, $tax_rate->applied_to_tax1);
                        $taxes = ['tax1' => $calc['tax1'], 'tax2' => $calc['tax2']];
                        $net = $calc['net'];
                    }
                    $data = [
                        "editNo" => null,
                        "client" => $this->client->id,
                        "month" => intval($month),
                        "year" => intval($year),
                        "fyear" => $fiscal_end,
                        "period" => intval($period),
                        "account_no" => $account_no,
                        "original_account" => $account_no,
                        "source" => @$source->id,
                        "ref_no" => $ref_no,
                        "description" => $description,
                        "gl_date" => $this->parseDate($gl_date, "d-M-y"),
                        "date" => $date,
                        "debit" => @$debit ? round($debit, 2) : 0.00,
                        "credit" => @$credit ? round($credit, 2) : 0.00,
                        "taxable" => $taxable,
                        "original_debit" => @$debit ? round($debit, 2) : 0.00,
                        "original_credit" => @$credit ? round($credit, 2) : 0.00,
                        "net" => @$net ? round($net, 2) : 0.00,
                        "tax1" => @$taxes["tax1"] ? round($taxes["tax1"], 2) : 0.00,
                        "tax2" => @$taxes["tax2"] ? round($taxes["tax2"], 2) : 0.00,
                        "province" => $this->client->province,
                        "pr_tax1" => @$tax_rate->tax_rate_1 ? round($tax_rate->tax_rate_1, 2) : 0.00,
                        "pr_tax2" => @$tax_rate->tax_rate_2 ? round($tax_rate->tax_rate_2, 2) : 0.00,
                        "portion" => round($portion, 2),
                        "wo_portion_net" => @$net ? round($net, 2) : 0.00,
                        "wo_portion_tax1" => @$taxes["tax1"] ? round($taxes["tax1"], 2) : 0.00,
                        "wo_portion_tax2" => @$taxes["tax2"] ? round($taxes["tax2"], 2) : 0.00,
                        "created_by" => Auth::user()->id,
                        "edit_by" => Auth::user()->id,
                        "updated_at" => date("Y-m-d H:i:s"),
                    ];


/**
 * DR = 1150
 * portion = 80%
 * tax1=50 = 40
 * tax2=100 = 80
 * net=1000 = 800
 * new journal = 1150 - (800+80+40) = 230
 */


   //apply portion && create a journal for portion
   if($data['portion'] < 100) {
        if ($data['taxable'] == 1)  {
            $data['net'] = round(($data['portion'] / 100) * $data['net'], 2);
            $data['tax1'] = round(($data['portion'] / 100) * $data['tax1'], 2);
            $data['tax2'] = round(($data['portion'] / 100) * $data['tax2'], 2);

            $portion_journal = $data;


//             dd(($portion_journal['net'] + $portion_journal['tax1'] + $portion_journal['tax2']));

            $portion_journal['account_no'] = @$this->client->dividends_account;
            $portion_journal['description'] = "Personal Portion " . $portion_journal['description'];
            if($data['original_debit'] > $data['original_credit']) {
         $portion_journal['debit'] = round($portion_journal['original_debit'] - ($portion_journal['net'] + $portion_journal['tax1'] + $portion_journal['tax2']), 2);
                $portion_journal['credit'] = 0.00;
                $lastInsertedEditNo++;
                $portion_journal['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($portion_journal);
                array_push($array, $Eno);
            } else {
         $portion_journal['credit'] = round($portion_journal['original_credit'] - ($portion_journal['net'] + $portion_journal['tax1'] + $portion_journal['tax2']), 2);
                $portion_journal['debit'] = 0.00;
                $lastInsertedEditNo++;
                $portion_journal['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($portion_journal);
                array_push($array, $Eno);
            }
        } else {
            $portion_journal = $data;
            $portion_journal['account_no'] = @$this->client->dividends_account;
            $portion_journal['description'] = "Personal Portion " . $portion_journal['description'];
            if($data['original_debit'] > $data['original_credit']) {
                $data['debit'] = round(($data['portion'] / 100) * $data['original_debit'], 2);
                $portion_journal['debit'] = round($portion_journal['original_debit'] - $data['debit'], 2);
                       $portion_journal['credit'] = 0.00;
                       $lastInsertedEditNo++;
                       $portion_journal['editNo'] = $lastInsertedEditNo;
                       $Eno = DB::table('journals')->insertGetId($portion_journal);
                       array_push($array, $Eno);
                   } else {
                    $data['credit'] = round(($data['portion'] / 100) * $data['original_credit'], 2);
                $portion_journal['credit'] = round($portion_journal['original_credit'] - $data['credit'], 2);
                       $portion_journal['debit'] = 0.00;
                       $lastInsertedEditNo++;
                       $portion_journal['editNo'] = $lastInsertedEditNo;
                       $Eno = DB::table('journals')->insertGetId($portion_journal);
                       array_push($array, $Eno);
                   }
        }
    }









if ($data['taxable'] == 1) {
    $client = DB::table('clients')->where('id', $data['client'])->where('is_deleted', 0)->first();
    if ($data['tax1'] > 0 && $data['tax1'] != '' && $data['tax2'] > 0 && $data['tax2'] != '' && $data['original_debit'] > $data['original_credit']) {
        /**
         * If taxable = on and tax1 and tax2 are not equal to 0 and Debit > Credit)
         *- Create 3 journals
         */
        //journal 1 net
        $data['debit'] = $data['net'];
        $data['credit'] = 0.00;
        $lastInsertedEditNo++;
        $data['editNo'] = $lastInsertedEditNo;
        $Eno = DB::table('journals')->insertGetId($data);
        array_push($array, $Eno);
        //journal 2 (tax1)
        $data['debit'] = $data['tax1'];
        $data['credit'] = 0.00;
        $data['account_no'] = $client->federal_tax;
        $lastInsertedEditNo++;
        $data['editNo'] = $lastInsertedEditNo;
        $Eno = DB::table('journals')->insertGetId($data);
        array_push($array, $Eno);
        //journal 3 (tax2)
        $data['debit'] = $data['tax2'];
        $data['credit'] = 0.00;
        $data['account_no'] = $client->provincial_tax;
        $lastInsertedEditNo++;
        $data['editNo'] = $lastInsertedEditNo;
        $Eno = DB::table('journals')->insertGetId($data);
        array_push($array, $Eno);
    } else {
        if ($data['tax1'] > 0 && $data['tax1'] != '' && $data['tax2'] > 0 && $data['tax2'] != '' && $data['original_credit'] > $data['original_debit']) {
            /**
             * If taxable = on and tax1 and tax2 are not equal to 0 and Credit > Debit)
             *- Create 3 journals
             */
            //journal 1 net
            $data['debit'] = 0.00;
            $data['credit'] = $data['net'];
            $lastInsertedEditNo++;
            $data['editNo'] = $lastInsertedEditNo;
            $Eno = DB::table('journals')->insertGetId($data);
            array_push($array, $Eno);
            //journal 2 (tax1)
            $data['debit'] = 0.00;
            $data['credit'] = $data['tax1'];
            $data['account_no'] = $client->federal_tax;
            $lastInsertedEditNo++;
            $data['editNo'] = $lastInsertedEditNo;
            $Eno = DB::table('journals')->insertGetId($data);
            array_push($array, $Eno);
            //journal 3 (tax2)
            $data['debit'] = 0.00;
            $data['credit'] = $data['tax2'];
            $data['account_no'] = $client->provincial_tax;
            $lastInsertedEditNo++;
            $data['editNo'] = $lastInsertedEditNo;
            $Eno = DB::table('journals')->insertGetId($data);
            array_push($array, $Eno);
        } else {
            if ((($data['tax1'] > 0 && $data['tax1'] != '') || ($data['tax2'] > 0 && $data['tax2'] != '')) && $data['original_debit'] > $data['original_credit']) {
                /**
                 * If taxable = on and tax1 or tax2 are not equal to 0 and Debit > Credit)
                 *- Create 2 journals
                 */
                //journal 1 net
                $data['debit'] = $data['net'];
                $data['credit'] = 0.00;
                $lastInsertedEditNo++;
                $data['editNo'] = $lastInsertedEditNo;
                $Eno = DB::table('journals')->insertGetId($data);
                array_push($array, $Eno);
                if ($data['tax1'] > 0 && $data['tax1'] != '') {
                    //journal 2 (tax1)
                    $data['debit'] = $data['tax1'];
                    $data['credit'] = 0.00;
                    $data['account_no'] = $client->federal_tax;
                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table('journals')->insertGetId($data);
                    array_push($array, $Eno);
                }
                if ($data['tax2'] > 0 && $data['tax2'] != '') {
                    //journal 2 (tax2)
                    $data['debit'] = $data['tax2'];
                    $data['credit'] = 0.00;
                    $data['account_no'] = $client->provincial_tax;
                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table('journals')->insertGetId($data);
                    array_push($array, $Eno);
                }
            } else {
                if ((($data['tax1'] > 0 && $data['tax1'] != '') || ($data['tax2'] > 0 && $data['tax2'] != '')) && $data['original_credit'] > $data['original_debit']) {
                    /**
                     * If taxable = on and tax1 or tax2 are not equal to 0 and Credit > Debit)
                     *- Create 2 journals
                     */
                    //journal 1 net
                    $data['debit'] = 0.00;
                    $data['credit'] = $data['net'];
                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table('journals')->insertGetId($data);
                    array_push($array, $Eno);
                    if ($data['tax1'] > 0 && $data['tax1'] != '') {
                        //journal 2 (tax1)
                        $data['debit'] = 0.00;
                        $data['credit'] = $data['tax1'];
                        $data['account_no'] = $client->federal_tax;
                        $lastInsertedEditNo++;
                        $data['editNo'] = $lastInsertedEditNo;
                        $Eno = DB::table('journals')->insertGetId($data);
                        array_push($array, $Eno);
                    }
                    if ($data['tax2'] > 0 && $data['tax2'] != '') {
                        //journal 2 (tax2)
                        $data['debit'] = 0.00;
                        $data['credit'] = $data['tax2'];
                        $data['account_no'] = $client->provincial_tax;
                        $lastInsertedEditNo++;
                        $data['editNo'] = $lastInsertedEditNo;
                        $Eno = DB::table('journals')->insertGetId($data);
                        array_push($array, $Eno);
                    }
                } else {
                    if (($data['tax1'] <= 0 || $data['tax1'] == '') && ($data['tax2'] <= 0 || $data['tax2'] == '')) {
                        $data['debit'] = $data['original_debit'];
                        $data['credit'] = $data['original_credit'];
                        $data['account_no'] = $data['original_account'];
                        $lastInsertedEditNo++;
                        $data['editNo'] = $lastInsertedEditNo;
                        $Eno = DB::table("journals")->insertGetId($data);
                        array_push($array, $Eno);
                    }
                }
            }
        }
    }
} else {
    $lastInsertedEditNo++;
    $data['editNo'] = $lastInsertedEditNo;
    $Eno = DB::table("journals")->insertGetId($data);
    array_push($array, $Eno);
}



                }
            }
        }
        foreach ($array as $e) {
            DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Imported (translated) ', 'journal_id' => $e]);
        }
        $this->data = $array;
        return 1;
        // return new Availibility([

        //     'class_id'     => $row[0],
        //     'date'    => $date,
        //     'location_id' => $row[2],
        //     'class_limit' => $row[3],
        //     'time' =>$time,

        //]);

    }
}
