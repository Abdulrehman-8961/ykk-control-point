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

class JournalStandardImport implements ToCollection, WithStartRow
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
    private function getFiscalYearEnd($fiscalStart)
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
        return $fiscalYear;
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

            $month = trim($r[0]);
            $year = trim($r[1]);
            $period = trim($r[2]);
            $fyear = trim($r[3]);
            $source_code = trim($r[4]);
            $account_no = trim($r[5]);
            $ref_no = trim($r[6]);
            $date = $this->parseDate(trim($r[7]), "dmY");
            $description = trim($r[8]);
            $debit = $r[9] == null || $r[9] == "" ? 0.00 : floatval(trim($r[9]));
            $credit = $r[10] == null || $r[10] == "" ? 0.00 : floatval(trim($r[10]));


            $source = DB::table('source_code')->where('is_deleted', 0)->where('source_code', $source_code)->first();

            if (@$source) {

                $lastInsertedEditNo = 0;
                $fyearLatestJournal = DB::table('journals')
                ->where('fyear', $fyear)
                ->where('client', $this->client->id)
                ->where('is_deleted', 0)
                ->orderBy('editNo', 'desc')
                ->first();

                if (@$fyearLatestJournal) {
                    $lastInsertedEditNo = $fyearLatestJournal->editNo;
                }
                if ($period != "") {

                    $data = [
                        "editNo" => null,
                        "client" => $this->client->id,
                        "month" => intval($month),
                        "year" => intval($year),
                        "fyear" => intval($fyear),
                        "period" => intval($period),
                        "account_no" => $account_no,
                        "original_account" => $account_no,
                        "source" => @$source->id,
                        "ref_no" => $ref_no,
                        "description" => $description,
                        "gl_date" => date("d-M-y", strtotime($r[7])),
                        "date" => $date,
                        "debit" => @$debit ? round($debit, 2) : 0.00,
                        "credit" => @$credit ? round($credit, 2) : 0.00,
                        "taxable" => 0,
                        "original_debit" => @$debit ? round($debit, 2) : 0.00,
                        "original_credit" => @$credit ? round($credit, 2) : 0.00,
                        "net" => 0.00,
                        "tax1" =>  0.00,
                        "tax2" => 0.00,
                        "province" => $this->client->province,
                        "pr_tax1" => 0.00,
                        "pr_tax2" => 0.00,
                        "portion" => 100.00,
                        "wo_portion_net" => 0.00,
                        "wo_portion_tax1" => 0.00,
                        "wo_portion_tax2" => 0.00,
                        "created_by" => Auth::user()->id,
                        "edit_by" => Auth::user()->id,
                        "updated_at" => date("Y-m-d H:i:s"),
                    ];

                    $lastInsertedEditNo++;
                    $data['editNo'] = $lastInsertedEditNo;
                    $Eno = DB::table("journals")->insertGetId($data);
                    array_push($array, $Eno);

                }
            }
        }
        foreach ($array as $e) {
            DB::table('journal_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Journal Imported (Standard) ', 'journal_id' => $e]);
        }
        $this->data = $array;
        return 1;
    }
}
