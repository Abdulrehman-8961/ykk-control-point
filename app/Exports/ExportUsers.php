<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;

class ExportUsers implements FromView
{
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

public function view(): View
{
    $field = request('field', 'id');
    $orderby = request('orderBy', 'desc');

    $status = request('filter_status');
    $search = request('search');

    $qry = DB::table('users as i')
        ->leftJoin('user_modules as um', function ($join) {
            $join->on('um.user_id', '=', 'i.id')
                 ->where('um.is_deleted', 0); // ✅ only include non-deleted modules
        })
        ->where('i.is_deleted', 0);

    if (request()->has('filter_status') && in_array($status, ['0', '1'])) {
        $qry->where('i.portal_access', (int) $status);
    }

    if (!empty($search)) {
        $qry->where(function($query) use ($search) {
            $query->where('i.firstname', 'like', "%$search%")
                ->orWhere('i.lastname', 'like', "%$search%")
                ->orWhere(DB::raw("CONCAT(i.firstname, ' ', i.lastname)"), 'like', "%$search%")
                ->orWhere('i.email', 'like', "%$search%");
        });
    }

    $data = $qry->select('i.*', 'um.module_name', 'um.access_type')
                ->orderBy("i.$field", $orderby)
                ->get();

    return view('exports.ExportUsers', [
        'qry' => $data
    ]);
}

}


?>