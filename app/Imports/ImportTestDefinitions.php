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

class ImportTestDefinitions implements ToCollection, WithStartRow
{
    public $data = [];

    // Start from row 2
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $insertedTestDefinitionNos = []; // To track duplicates in the same file

        foreach ($rows as $r) {
            $test_name = trim($r[0]);

            // Skip empty test_name
            if (empty($test_name)) {
                continue;
            }

            // Check if already inserted in this import
            if (in_array($test_name, $insertedTestDefinitionNos)) {
                continue;
            }

            // Check if already exists in DB
            $exists = DB::table('test_definitions')->where('test_name', $test_name)->where('is_deleted',0)->exists();
            if ($exists) {
                continue;
            }

            $test_type = trim($r[1]);
            $criteria = trim($r[2]);
            $uom = trim($r[3]);
            $standard = trim($r[4]);
            $description = trim($r[5]);

            $data = [
                'test_name' => $test_name,
                'test_type' => $test_type,
                'criteria'  => $criteria,
                'uom'       => $uom,
                'standard'  => $standard,
                'description' => $description,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $id = DB::table('test_definitions')->insertGetId($data);

            DB::table('test_definition_audit_trail')->insert([
                'user_id' => Auth::id(),
                'description' => 'Test Definitions Imported',
                'test_definition_id' => $id,
            ]);

            $this->data[] = $data;
            $insertedTestDefinitionNos[] = $test_name; // mark as inserted
        }

        return 1;
    }
}
