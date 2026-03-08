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

class WorkordersImport implements ToCollection, WithStartRow
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
            $workorder_no = trim($r[0]);
            $itemcode_id = trim($r[1]);
            $itemcode_color_id = trim($r[2]);
            $length = trim($r[3]);
            $data = [
                'workorder_no' => $workorder_no,
                'itemcode_id' => $itemcode_id,
                'itemcode_color_id' => $itemcode_color_id,
                'length' => $length,
                'status' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
            ];
            DB::Table('workorders')->insert($data);
            $id = DB::getPdo()->lastInsertId();
            DB::table('workorder_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Workorders Added', 'workorder_id' => $id]);
        }

        $this->data = $array;
        return 1;
    }
}
