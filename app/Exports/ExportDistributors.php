<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportDistributors implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      

 
if(sizeof($_GET)>0){

$orderby='desc';
$field='id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


 
     $qry=DB::table('distributors')->where('is_deleted',0)->where(function($query){
         $query->Orwhere('distributor_name','like','%'.@$_GET['search'].'%');
    
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('distributors')->where('is_deleted',0) ->orderBy('id','desc')->get(); 
 
 }
 


 
 
    return view('exports.ExportDistributors', [
        'qry' => $qry
    ]);
 }
}