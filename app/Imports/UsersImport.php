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

class UsersImport implements ToCollection, WithStartRow
{

public $insertedUsers = 0;
public $insertedModules = 0;

    public function startRow(): int
    {
        return 2; // skip heading row
    }

    public function collection(Collection $rows)
    {
        $currentFirstname = null;
        $currentLastname = null;
        $currentEmail = null;
        $currentUserId = null;

        foreach ($rows as $r) {
            $firstname = trim($r[0] ?? '');
            $lastname = trim($r[1] ?? '');
            $email = trim($r[2] ?? '');
            $module = trim($r[3] ?? '');
            $access = trim($r[4] ?? '');

            // If a new user is defined in this row, update current user context
            if (!empty($email)) {
                $currentFirstname = $firstname;
                $currentLastname = $lastname;
                $currentEmail = $email;

                // Check if user exists by email
                $currentUserId = DB::table('users')->where('email', $currentEmail)->where('is_deleted', 0)->value('id');

                if (!$currentUserId) {
                    $currentUserId = DB::table('users')->insertGetId([
                        'firstname' => $currentFirstname,
                        'lastname' => $currentLastname,
                        'email' => $currentEmail,
                        'portal_access' => 1,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->insertedUsers++;

                    DB::table('user_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'User Imported | ' .$currentUserId , 'client_id' => $currentUserId]);
                }
            }

            // Use current user ID and insert module data
            if ($currentUserId && !empty($module) && !empty($access)) {
                $exists = DB::table('user_modules')
                    ->where('user_id', $currentUserId)
                    ->where('module_name', $module)
                    ->where('access_type', $access)
                    ->where('is_deleted', 0)
                    ->exists();

                if (!$exists) {
                    DB::table('user_modules')->insert([
                        'user_id' => $currentUserId,
                        'module_name' => $module,
                        'access_type' => $access,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->insertedModules++;
                }
            }
        }
    }
}

?>