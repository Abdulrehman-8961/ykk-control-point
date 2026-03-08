<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportSites implements FromView
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

 
     $qry=DB::table('sites as s')->where('s.is_deleted',0)->select('s.*','c.firstname','c.logo')->join('clients as c','c.id','=','s.client_id')->where(function($query){
        $query->Orwhere('c.firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.site_name','like','%'.@$_GET['search'].'%');
        
        $query->Orwhere('s.country','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.city','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.province','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.phone','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.fax','like','%'.@$_GET['search'].'%');
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('sites as s')->where('s.is_deleted',0)->select('s.*','c.firstname','c.logo')->join('clients as c','c.id','=','s.client_id') ->orderBy('s.id','desc')->get(); 
 
 }

 
 
    return view('exports.ExportSites', [
        'qry' => $qry
    ]);
 }
}