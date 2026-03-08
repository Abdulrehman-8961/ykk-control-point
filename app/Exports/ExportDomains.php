<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportDomains implements FromView
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

 
     $qry=DB::table('domains as s')->where('s.is_deleted',0)->select('s.*','c.firstname')->join('clients as c','c.id','=','s.client_id')->where(function($query){
        $query->Orwhere('c.firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.domain_name','like','%'.@$_GET['search'].'%');
        
     
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('domains as s')->select('s.*','c.firstname')->where('s.is_deleted',0)->join('clients as c','c.id','=','s.client_id') ->orderBy('s.id','desc')->get(); 
 
 }
 
 
 
    return view('exports.ExportDomains', [
        'qry' => $qry
    ]);
 }
}