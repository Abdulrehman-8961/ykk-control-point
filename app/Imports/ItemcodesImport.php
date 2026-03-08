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

class ItemcodesImport implements ToCollection, WithStartRow
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
            $item_code = trim($r[0]);
            $description = trim($r[1]);
            $size = trim($r[2]);
            $chain_code = trim($r[3]);
            $item_category = $size . ' ' . $chain_code;
            $data = [
                'item_code' => $item_code,
                'description' => $description,
                'size' => $size,
                'chain_code' => $chain_code,
                'item_category' => $item_category,
                'status' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
            ];
            DB::Table('itemcodes')->insert($data);
            $id = DB::getPdo()->lastInsertId();
            DB::table('itemcodes_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'Itemcodes Added', 'itemcode_id' => $id]);
        }

        $this->data = $array;
        return 1;
    }
}
