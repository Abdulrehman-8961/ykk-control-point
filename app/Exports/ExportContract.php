<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Auth;
class ExportContract implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      
 $userAccess=explode(',',Auth::user()->access_to_client);

 

 




if(sizeof($_GET)>0){


 $orderby='desc';
$field='a.id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}
 

 $cond='';


if(isset($_GET['advance_search'])){


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


 if(isset($_GET['has_attachment'])  && $_GET['has_attachment']!=''){
                $attachment=$_GET['has_attachment'];
                if($attachment==1){      
                $cond.=" and a.attachment!='' ";
                }
                else if($attachment==0){
                    $cond.=" and a.attachment is null ";

                }
 }
if(isset($_GET['contract_status'])  && $_GET['contract_status']!=''){
                $contract_status=$_GET['contract_status'];      
                if($contract_status=='Upcoming'){
                    $month_date=date('Y-m-d',strtotime('+1 month'));
                        $cond.=" and a.contract_status='Active' and a.contract_end_date<='$month_date'";
                }
                else{
                $cond.=" and a.contract_status='$contract_status'";
 }
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

 
  if(isset($_GET['invoice_date'])  && $_GET['invoice_date']!=''){
       
                 $invoice_date=explode(' to ',$_GET['invoice_date']);
                $cond.=' and a.invoice_date>="'.$invoice_date[0].'" and a.invoice_date<="'.$invoice_date[1].'" ';
 }
  if(isset($_GET['po_no'])  && $_GET['po_no']!=''){
                 $po_no=$_GET['po_no'];
                $cond.=" and a.po_no='$po_no'";
 }

   if(isset($_GET['daterange'])  && $_GET['daterange']!=''){
 
                    $po_date=explode(' to ',$_GET['daterange']);
                $cond.=' and a.contract_end_date>="'.$po_date[0].'" and a.contract_end_date<="'.$po_date[1].'" ';
 }


   if(isset($_GET['renewal_within'])  && $_GET['renewal_within']!=''){
                    $date=date('Y-m-d');  
                    $po_date=date('Y-m-d',strtotime('+ '.$_GET['renewal_within'].' day'));
                         
                $cond.=' and a.contract_end_date>="'.$date.'" and a.contract_end_date<="'.$po_date.'" ';
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

 if(isset($_GET['contract_description'])  && $_GET['contract_description']!=''){
                  $contract_description=$_GET['contract_description'];
                $cond.=" and a.contract_description like '%$contract_description%'";
 }
 if(isset($_GET['comments'])  && $_GET['comments']!=''){
                  $comments=$_GET['comments'];
                $cond.=" and a.comments like '%$comments%'";
 }
 if(isset($_GET['contract_type'])  && $_GET['contract_type']!=''){
                  $contract_type=$_GET['contract_type'];
                $cond.=" and a.contract_type like '%$contract_type%'";
 }





 if(isset($_GET['contract_start_date'])  && $_GET['contract_start_date']!=''){
                  $contract_start_date=explode(' to ',$_GET['contract_start_date']);

                $cond.=' and a.contract_start_date>="'.$contract_start_date[0].'" and a.contract_start_date<="'.$contract_start_date[1].'" ';
 }
 if(isset($_GET['contract_end_date'])  && $_GET['contract_end_date']!=''){
           
   
                    $contract_end_date=explode(' to ',$_GET['contract_end_date']);
                $cond.=' and a.contract_end_date>="'.$contract_end_date[0].'" and a.contract_end_date<="'.$contract_end_date[1].'" ';
 }
  }

 $sear=@$_GET['search'];
if(Auth::user()->role=='admin'){
  
// - Comments
// - Line Asset
// - Line Description
// - Line PN#


$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond") ->where(function($query) use($sear){

       $query->Orwhere('contract_no','like','%'.@$_GET['search'].'%');
       $query->Orwhere('contract_description','like','%'.@$_GET['search'].'%');
       $query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
       $query->Orwhere('registered_email','like','%'.@$_GET['search'].'%');
$query->Orwhere('reference_no','like','%'.@$_GET['search'].'%');
    $query->Orwhere('estimate_no','like','%'.@$_GET['search'].'%');
   $query->Orwhere('sales_order_no','like','%'.@$_GET['search'].'%');
        $query->Orwhere('invoice_no','like','%'.@$_GET['search'].'%');
          $query->Orwhere('po_no','like','%'.@$_GET['search'].'%');
$query->Orwhere('po_no','like','%'.@$_GET['search'].'%');
             $query->OrwhereRaw("exists (select 1
              from contract_details i left join contract_assets as ca on ca.contract_detail_id=i.contract_detail_id left join assets as ass on ca.hostname=ass.id
              where i.contract_id = a.id and
                    (i.pn_no LIKE '%$sear%' or i.detail_comments LIKE '%$sear%' or ass.hostname like '%$sear%')
             )");
              $query->OrwhereRaw("exists (select 1
              from contract_comments i 
              where i.contract_id = a.id and
                    (i.comment LIKE '%$sear%'  )
             )");
 
 
     }) ->where('a.is_deleted',0) ->orderBy($field,$orderby) ->get(); 

}
else{
    $qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond") ->where(function($query) use($sear){
      
       $query->Orwhere('contract_no','like','%'.@$_GET['search'].'%');
       $query->Orwhere('contract_description','like','%'.@$_GET['search'].'%');
       $query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
       $query->Orwhere('registered_email','like','%'.@$_GET['search'].'%');
$query->Orwhere('reference_no','like','%'.@$_GET['search'].'%');
    $query->Orwhere('estimate_no','like','%'.@$_GET['search'].'%');
   $query->Orwhere('sales_order_no','like','%'.@$_GET['search'].'%');
        $query->Orwhere('invoice_no','like','%'.@$_GET['search'].'%');
          $query->Orwhere('po_no','like','%'.@$_GET['search'].'%');
$query->Orwhere('po_no','like','%'.@$_GET['search'].'%');
             $query->OrwhereRaw("exists (select 1
              from contract_details i left join contract_assets as ca on ca.contract_detail_id=i.contract_detail_id left join assets as ass on ca.hostname=ass.id
              where i.contract_id = a.id and
                    (i.pn_no LIKE '%$sear%' or i.detail_comments LIKE '%$sear%' or ass.hostname like '%$sear%')
             )");
              $query->OrwhereRaw("exists (select 1
              from contract_comments i 
              where i.contract_id = a.id and
                    (i.comment LIKE '%$sear%'  )
             )");
 
 
     })   ->where('a.is_deleted',0) ->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->get(); 
    
}
 
}
else{

   
  
    if(Auth::user()->role=='admin'){
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')  ->where('a.is_deleted',0) ->orderBy('a.id','desc') ->get(); 
 }
 else{
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')  ->where('a.is_deleted',0) ->whereIn('a.client_id',$userAccess)->orderBy('a.id','desc') ->get(); 
    
 }
 
 }

 
    return view('exports.ExportContract', [
        'qry' => $qry,
         
    ]);
 }
}