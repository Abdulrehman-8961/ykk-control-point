<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Auth;
class ExportExpiringContract implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      

$userAccess=explode(',',Auth::user()->access_to_client);
$limit=10;
if(isset($_GET['limit']) && $_GET['limit']!=''){
    $limit=$_GET['limit'];
}

$expiry_date=date('Y-m-d',strtotime('+30 days'));

if(sizeof($_GET)>0){


 


if(isset($_GET['advance_search'])){

$orderby='desc';
$field='a.id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}
 

 $cond='';
 if(isset($_GET['client_id'])  && $_GET['client_id']!='' ){
                    $client_id=$_GET['client_id'];
                $cond.=" and a.client_id ='$client_id'";
 }
  if(isset($_GET['site_id']) && sizeof($_GET['site_id'])>0){

                    $site_id=implode(',',$_GET['site_id']);
                $cond.=" and a.site_id in ($site_id)";
 }

  if(isset($_GET['vendor_id']) && sizeof($_GET['vendor_id'])>0){
                    $vendor_id=implode(',',$_GET['vendor_id']);
                $cond.=" and a.vendor_id in ($vendor_id)";
 }

  if(isset($_GET['distributor_id']) && sizeof($_GET['distributor_id'])>0){
                    $distributor_id=implode(',',$_GET['distributor_id']);
                $cond.=" and a.distributor_id in ($distributor_id)";
 }

 if(isset($_GET['contract_status'])  && $_GET['contract_status']!=''){
                $contract_status=$_GET['contract_status'];      
                $cond.=" and a.contract_status='$contract_status'";
 }

 if(isset($_GET['estimate_no'])  && $_GET['estimate_no']!=''){
                $estimate_no=$_GET['estimate_no'];
                $cond.=" and a.estimate_no='$estimate_no'";
 }

 if(isset($_GET['sales_order_no'])  && $_GET['sales_order_no']!=''){
            $sales_order_no=$_GET['sales_order_no'];
                $cond.=" and a.sales_order_no='$sales_order_no'";
 }

  if(isset($_GET['invoice_no'])   && $_GET['invoice_no']!=''){
 
                 $invoice_no=$_GET['invoice_no'];
                $cond.=" and a.invoice_no='$invoice_no'";
 }

  if(isset($_GET['contract_type'])   && $_GET['contract_type']!=''){
 
                 $contract_type=$_GET['contract_type'];
                $cond.=" and a.contract_type='$contract_type'";
 }

 
  if(isset($_GET['invoice_date'])  && $_GET['invoice_date']!=''){
       
                 $invoice_date=explode(' to ',$_GET['invoice_date']);
                $cond.=' and a.invoice_date>="'.$invoice_date[0].'" and a.invoice_date<="'.$invoice_date[1].'" ';
 }
  if(isset($_GET['po_no'])  && $_GET['po_no']!=''){
                 $po_no=$_GET['po_no'];
                $cond.=" and a.po_no='$po_no'";
 }

   if(isset($_GET['po_date'])  && $_GET['po_date']!=''){
 
                    $po_date=explode(' to ',$_GET['po_date']);
                $cond.=' and a.po_date>="'.$po_date[0].'" and a.po_date<="'.$po_date[1].'" ';
 }
   if(isset($_GET['reference_no'])  && $_GET['reference_no']!=''){
                  $reference_no=$_GET['reference_no'];
                $cond.=" and a.reference_no='$reference_no'";
 }

 if(isset($_GET['distrubutor_sales_order_no'])  && $_GET['distrubutor_sales_order_no']!=''){
                  $distrubutor_sales_order_no=$_GET['distrubutor_sales_order_no'];
                $cond.=" and a.distrubutor_sales_order_no='$distrubutor_sales_order_no'";
 }
 if(isset($_GET['contract_no'])  && $_GET['contract_no']!=''){
                  $contract_no=$_GET['contract_no'];
                $cond.=" and a.contract_no='$contract_no'";
 }
 if(isset($_GET['contract_start_date'])  && $_GET['contract_start_date']!=''){
                  $contract_start_date=explode(' to ',$_GET['contract_start_date']);

                $cond.=' and a.contract_start_date>="'.$contract_start_date[0].'" and a.contract_start_date<="'.$contract_start_date[1].'" ';
 }
 if(isset($_GET['contract_end_date'])  && $_GET['contract_end_date']!=''){
           
   
                    $contract_end_date=explode(' to ',$_GET['contract_end_date']);
                $cond.=' and a.contract_end_date>="'.$contract_end_date[0].'" and a.contract_end_date<="'.$contract_end_date[1].'" ';
 }
  


if(Auth::user()->role=='admin'){

$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond")->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->orderBy($field,$orderby) ->get(); 

}
else{
    $qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond")->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_status','Active')->where('a.contract_end_date','<=',$expiry_date)->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->get(); 
    
}


}
else{


$orderby='desc';
$field='a.id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


if(Auth::user()->role=='admin'){
$qry=DB::table('contracts as a')->where(function($query){
        $query->Orwhere('firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('distributor_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('contract_status','like',@$_GET['search'].'%');
        $query->Orwhere('estimate_no','like','%'.@$_GET['search'].'%');
        $query->Orwhere('sales_order_no','like','%'.@$_GET['search'].'%');
        $query->Orwhere('invoice_no','like','%'.@$_GET['search'].'%');
         $query->Orwhere('invoice_date','like','%'.@$_GET['search'].'%');
          $query->Orwhere('po_no','like','%'.@$_GET['search'].'%');
           $query->Orwhere('po_date','like','%'.@$_GET['search'].'%');
            $query->Orwhere('reference_no','like','%'.@$_GET['search'].'%');
             $query->Orwhere('distrubutor_sales_order_no','like','%'.@$_GET['search'].'%');
              $query->Orwhere('contract_no','like','%'.@$_GET['search'].'%');
              $query->Orwhere('contract_start_date','like','%'.@$_GET['search'].'%');
  $query->Orwhere('contract_end_date','like','%'.@$_GET['search'].'%');
 
     }) ->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->orderBy($field,$orderby) ->get(); 
}
else{
 
$qry=DB::table('contracts as a')->where(function($query){
        $query->Orwhere('firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('site_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('distributor_name','like','%'.@$_GET['search'].'%');
        $query->Orwhere('contract_status','like',@$_GET['search'].'%');
        $query->Orwhere('estimate_no','like','%'.@$_GET['search'].'%');
        $query->Orwhere('sales_order_no','like','%'.@$_GET['search'].'%');
        $query->Orwhere('invoice_no','like','%'.@$_GET['search'].'%');
         $query->Orwhere('invoice_date','like','%'.@$_GET['search'].'%');
          $query->Orwhere('po_no','like','%'.@$_GET['search'].'%');
           $query->Orwhere('po_date','like','%'.@$_GET['search'].'%');
            $query->Orwhere('reference_no','like','%'.@$_GET['search'].'%');
             $query->Orwhere('distrubutor_sales_order_no','like','%'.@$_GET['search'].'%');
              $query->Orwhere('contract_no','like','%'.@$_GET['search'].'%');
              $query->Orwhere('contract_start_date','like','%'.@$_GET['search'].'%');
  $query->Orwhere('contract_end_date','like','%'.@$_GET['search'].'%');
 
     }) ->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->get(); 

}


}

}
 else{

   

if(Auth::user()->role=='admin'){
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->orderBy('a.id','desc') ->get(); 
 }
 else{
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->whereIn('a.client_id',$userAccess)->orderBy('a.id','desc') ->get(); 
    
 }
 }

 
 
    return view('exports.ExportContract', [
        'qry' => $qry
    ]);
 }
}