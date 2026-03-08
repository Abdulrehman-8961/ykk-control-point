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
use DB;
use Exception;
use Auth;

class GIFIImport implements ToCollection, WithStartRow
{
    public $data;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function startRow(): int
    {
        return 2;
    }
    public function collection(Collection $rows)
    {
        $array = array();
        foreach ($rows as $r) {
            $type = trim($r[0]);
            $sub_type = trim($r[1]);
            $number = trim($r[2]);
            $desc = trim($r[3]);
            $notes = trim($r[4]);
            $data = [
                'account_type' => $type,
                'sub_type' => $sub_type,
                'account_no' => $number,
                'description' => $desc,
                'note' => $notes,
                'gifi_status' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
            ];
            DB::Table('gifi')->insert($data);
            $id = DB::getPdo()->lastInsertId();
            DB::table('gifi_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Gifi Account Added', 'gifi_id' => $id]);
        }

        $this->data = $array;
        return 1;
    }
}
