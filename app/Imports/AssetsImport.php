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

class AssetsImport implements ToCollection, WithStartRow
{
    public $data = [];

    // Start from row 2
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $insertedAssetNos = []; // To track duplicates in the same file

        foreach ($rows as $r) {
            $asset_no = trim($r[0]);

            // Skip empty asset_no
            if (empty($asset_no)) {
                continue;
            }

            // Check if already inserted in this import
            if (in_array($asset_no, $insertedAssetNos)) {
                continue;
            }

            // Check if already exists in DB
            $exists = DB::table('assets')->where('asset_no', $asset_no)->where('is_deleted',0)->exists();
            if ($exists) {
                continue;
            }

            $machine_no = strtoupper(trim($r[1]));
            $description = trim($r[2]);

            $data = [
                'asset_no' => $asset_no,
                'machine_no' => $machine_no,
                'description' => $description,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $id = DB::table('assets')->insertGetId($data);

            DB::table('assets_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Asset Imported',
                'asset_id' => $id,
            ]);

            $this->data[] = $data;
            $insertedAssetNos[] = $asset_no; // mark as inserted
        }

        return 1;
    }
}
