<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportAssetType implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      

 if(sizeof($_GET)>0){

$orderby='desc';
$field='asset_type_id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


 
     $qry=DB::table('asset_type')->where('is_deleted',0)->where(function($query){
         $query->Orwhere('asset_type_description','like','%'.@$_GET['search'].'%');
    
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('asset_type') ->where('is_deleted',0)->orderBy('asset_type_id','desc')->get(); 
 
 }


 
 
    return view('exports.ExportAssetType', [
        'qry' => $qry
    ]);
 }
}