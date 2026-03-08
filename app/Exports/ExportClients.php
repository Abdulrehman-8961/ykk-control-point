<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportClients implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      

 

if(isset($_GET)){ 
    $orderby='desc';
$field='id';
if(isset($this->request->orderBy)){
$orderby=$this->request->orderBy;
$field=$this->request->field;
}
 
     $qry=DB::table('clients')->where(function($query){
        $query->Orwhere('firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('lastname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('company_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('email_address','like','%'.@$_GET['search'].'%');
        $query->Orwhere('work_phone','like','%'.@$_GET['search'].'%');
        $query->Orwhere('mobile','like','%'.@$_GET['search'].'%');
        $query->Orwhere('website','like','%'.@$_GET['search'].'%');
 
     })->where('is_deleted',0)->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('clients')->where('is_deleted',0)->orderBy('id','desc')->get(); 
 
 
 
 }
 


 
 
    return view('exports.ExportClients', [
        'qry' => $qry
    ]);
 }
}