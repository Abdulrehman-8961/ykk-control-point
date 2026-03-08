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

class ItemCategoriesImport implements ToCollection, WithStartRow
{
    public $insertedCategories = 0;
    public $insertedItemCodes = 0;
    public $createdRelationships = 0;
    
    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $currentCategory = null;
        $itemCategoryId = null;

        foreach ($rows as $r) {
            $itemCategory = trim($r[0] ?? '');
            $itemCode = trim($r[1] ?? '');
            $description = trim($r[2] ?? '');

            // Skip empty rows
            if (empty($itemCategory) && empty($itemCode)) {
                continue;
            }

            // 1. Handle Item Category
            if (!empty($itemCategory)) {
                $currentCategory = $itemCategory;

                $itemCategoryId = DB::table('item_categories')
                    ->where('item_category', $currentCategory)
                    ->where('is_deleted', 0)
                    ->value('id');

                if (!$itemCategoryId) {
                    $itemCategoryId = DB::table('item_categories')->insertGetId([
                        'item_category' => $currentCategory,
                        'status' => 1,
                        'created_at' => now(),
                        'created_by' => Auth::id(),
                        'updated_at' => now(),
                        'updated_by' => Auth::id(),
                    ]);

                    $this->insertedCategories++;

                    // Audit trail
                    DB::table('item_categories_audit_trail')->insert([
                        'user_id' => Auth::id(),
                        'description' => 'Item Category Added via Import',
                        'item_category_id' => $itemCategoryId,
                        'created_at' => now()
                    ]);
                }
            }

            // 2. Handle Item Codes
            if (!empty($itemCode) && !empty($description)) {
                $itemcodeId = DB::table('itemcodes')
                    ->where('item_code', $itemCode)
                    ->where('description', $description)
                    ->where('is_deleted', 0)
                    ->value('id');

                // if (!$itemcodeId) {
                //     $itemcodeId = DB::table('itemcodes')->insertGetId([
                //         'item_code' => $itemCode,
                //         'description' => $description,
                //         'status' => 1,
                //         'created_at' => now(),
                //         'updated_at' => now(),
                //         'updated_by' => Auth::id(),
                //     ]);

                //     $this->insertedItemCodes++;

                //     // Audit trail for itemcode
                //     DB::table('itemcodes_audit_trail')->insert([
                //         'user_id' => Auth::id(),
                //         'description' => 'Itemcode Added via Import',
                //         'itemcode_id' => $itemcodeId,
                //         'created_at' => now()
                //     ]);
                // }

                // 3. Create Relationship
                if ($itemCategoryId && $itemcodeId) {
                    $exists = DB::table('item_categories_itemcodes')
                        ->where('item_category_id', $itemCategoryId)
                        ->where('itemcode_id', $itemcodeId)
                        ->where('is_deleted', 0)
                        ->exists();

                    if (!$exists) {
                        DB::table('item_categories_itemcodes')->insert([
                            'item_category_id' => $itemCategoryId,
                            'itemcode_id' => $itemcodeId,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'updated_by' => Auth::id(),
                        ]);
                        $this->createdRelationships++;
                    }
                }
            }
        }
    }
}