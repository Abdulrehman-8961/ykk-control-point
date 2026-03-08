<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Auth;
use DB;

class ImportTestThresholds implements ToCollection, WithStartRow
{
    public $data = [];

    protected $thresholds = [];

    public function collection(Collection $rows)
    {
        $currentTestName = null;
        $currentTestThresholdId = null;

        foreach ($rows as $row) {
            // Skip header row or empty row
            if ($row[0] == 'Test Name' || (empty($row[0]) && empty($row[1]))) {
                continue;
            }

            // New Test Name row
            // if (!empty($row[0])) {
            //     $currentTestName = trim($row[0]);

            //     $testDefinition = DB::table('test_definitions')
            //         ->where('test_name', $currentTestName)
            //         ->first();

            //     if (!$testDefinition) {
            //         continue;
            //     }

            //     $currentTestThresholdId = DB::table('test_thresholds')->insertGetId([
            //         'test_name_id' => $testDefinition->id,
            //         'status' => 1,
            //         'created_at' => now(),
            //         'created_by' => Auth::id(),
            //         'updated_at' => now(),
            //         'updated_by' => Auth::id(),
            //     ]);

            //     // Audit trail
            //     DB::table('test_threshold_audit_trail')->insert([
            //         'user_id' => Auth::id(),
            //         'description' => 'Test Threshold Added via Import',
            //         'test_threshold_id' => $currentTestThresholdId,
            //         'created_at' => now()
            //     ]);
            // }

            // New Test Name row
            if (!empty($row[0])) {
                $currentTestName = trim($row[0]);

                $testDefinition = DB::table('test_definitions')
                    ->where('test_name', $currentTestName)->where('is_deleted',0)
                    ->first();

                if (!$testDefinition) {
                    continue;
                }

                // ✅ Only create once per Test Name
                if (!isset($this->thresholds[$currentTestName])) {
                    $currentTestThresholdId = DB::table('test_thresholds')->insertGetId([
                        'test_name_id' => $testDefinition->id,
                        'status' => 1,
                        'created_at' => now(),
                        'created_by' => Auth::id(),
                        'updated_at' => now(),
                        'updated_by' => Auth::id(),
                    ]);

                    // Save it for later use
                    $this->thresholds[$currentTestName] = $currentTestThresholdId;

                    // Audit trail
                    DB::table('test_threshold_audit_trail')->insert([
                        'user_id' => Auth::id(),
                        'description' => 'Test Threshold Added via Import',
                        'test_threshold_id' => $currentTestThresholdId,
                        'created_at' => now()
                    ]);
                } else {
                    // Reuse existing ID if already created
                    $currentTestThresholdId = $this->thresholds[$currentTestName];
                }
            }


            // Item Category row
            if (!empty($row[1]) && $currentTestThresholdId) {
                $itemCategoryName = trim($row[1]);
                
                // $itemCategory = DB::table('item_categories')
                // ->where('item_category', $itemCategoryName)
                // ->first();
                $itemCategory = DB::table('itemcodes')
                ->where('item_category', $itemCategoryName)->where('is_deleted',0)
                ->first();
                // dd($currentTestThresholdId);

                if (!$itemCategory) {
                    continue;
                }

                DB::table('test_threshold_item_categories')->insert([
                    'test_threshold_id' => $currentTestThresholdId,
                    'item_category_id' => $itemCategory->id,
                    'min' => $row[2] ?? null,
                    'max' => $row[3] ?? null,
                    'avg' => $row[4] ?? null,
                    'YFS' => $row[5] ?? null,
                    'YFGS' => $row[6] ?? null,
                    'safety_threshold' => $row[7] ?? null,
                    'absorption' => $row[8] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

                $this->data[] = $row; // Only track successfully inserted item_category rows
            }
        }
    }

    public function startRow(): int
    {
        return 2; // Skip header row
    }
}
