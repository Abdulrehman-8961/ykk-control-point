<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportOperatingSystems implements FromView
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


 
     $qry=DB::table('operating_systems')->where('is_deleted',0)->where(function($query){
         $query->Orwhere('operating_system_name','like','%'.@$_GET['search'].'%');
    
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('operating_systems')->where('is_deleted',0) ->orderBy('id','desc')->get(); 
 
 }
 


 
 
    return view('exports.ExportOperatingSystems', [
        'qry' => $qry
    ]);
 }
}