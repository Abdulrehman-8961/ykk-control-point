<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;
use Auth;
class ExportSSL implements FromView
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
$field='s.id';

if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


 $cond='';
 if(isset($_GET['client_id'])  && $_GET['client_id']!='' ){
                    $client_id=$_GET['client_id'];
                $cond.=" and s.client_id ='$client_id'";
 }
  if(isset($_GET['cert_issuer']) && sizeof($_GET['cert_issuer'])>0){
                    $vendor_id=implode(',',$_GET['cert_issuer']);
                $cond.=" and s.cert_issuer in ($vendor_id)";
 }



 if(isset($_GET['cert_status'])  && $_GET['cert_status']!=''){
                $cert_status=$_GET['cert_status'];      
                if($cert_status=='Upcoming'){
                    $month_date=date('Y-m-d',strtotime('+1 month'));
                        $cond.=" and s.cert_status='Active' and s.cert_edate<='$month_date'";
                }
                else{
                $cond.=" and s.cert_status='$cert_status'";
 }
}


 if(isset($_GET['daterange'])  && $_GET['daterange']!=''){
 
                    $po_date=explode(' to ',$_GET['daterange']);
                $cond.=' and s.cert_edate>="'.$po_date[0].'" and s.cert_edate<="'.$po_date[1].'" ';
 }


   if(isset($_GET['renewal_within'])  && $_GET['renewal_within']!=''){
                    $date=date('Y-m-d');  
                    $po_date=date('Y-m-d',strtotime('+ '.$_GET['renewal_within'].' day'));
                         
                $cond.=' and s.cert_edate>="'.$date.'" and s.cert_edate<="'.$po_date.'" ';
 }

 if(isset($_GET['cert_type'])  && $_GET['cert_type']!=''){
                  $cert_type=$_GET['cert_type'];
                $cond.=" and s.cert_type like '%$cert_type%'";
 }



$sear=@$_GET['search'];
if(Auth::user()->role=='admin'){
 
     $qry=DB::table('ssl_certificate as s')->select('s.*','a.hostname','c.firstname','i.vendor_name as issuer','i.vendor_image','c.logo','st.site_name','i.vendor_name')->leftjoin('assets as a','a.id','=','s.cert_hostname')->leftjoin('sites as st','s.site_id','=','st.id')->leftjoin('vendors as i','s.cert_issuer','=','i.id')->join('clients as c','c.id','=','s.client_id')->where(function($query) use ($sear){
                $query->Orwhere('description','like','%'.@$_GET['search'].'%');
$query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
     $query->Orwhere('cert_name','like','%'.@$_GET['search'].'%');
$query->Orwhere('cert_company','like','%'.@$_GET['search'].'%');
       $query->Orwhere('cert_city','like','%'.@$_GET['search'].'%');
      $query->Orwhere('cert_country','like','%'.@$_GET['search'].'%');
   $query->Orwhere('cert_state','like','%'.@$_GET['search'].'%');
    $query->Orwhere('cert_email','like','%'.@$_GET['search'].'%'); 

       $query->OrwhereRaw("exists (select 1
              from ssl_host i  left join assets as ass on i.hostname=ass.id left join asset_ip_addresses as ip on  find_in_set(ip.id,i.ip_id)
              where i.ssl_id = s.id and
                    ( ass.hostname like '%$sear%' or ass.role like '%$sear%' or ip.ip_address_value like '%$sear%' )
             )");

              $query->OrwhereRaw("exists (select 1
              from ssl_san i 
              where i.ssl_id = s.id and
                    (i.san LIKE '%$sear%'  )
             )");
              $query->OrwhereRaw("exists (select 1
              from ssl_comments i 
              where i.ssl_id = s.id and
                    (i.comment LIKE '%$sear%'  )
             )");
 
     })->whereRaw("s.is_deleted=0 $cond")->orderBy($field,$orderby)->get(); 
 }
 else{
   
     $qry=DB::table('ssl_certificate as s')->select('s.*','a.hostname','c.firstname','i.vendor_name as issuer','i.vendor_image','c.logo','st.site_name','i.vendor_name')->leftjoin('assets as a','a.id','=','s.cert_hostname')->leftjoin('sites as st','s.site_id','=','st.id')->leftjoin('vendors as i','s.cert_issuer','=','i.id')->join('clients as c','c.id','=','s.client_id')->where(function($query) use ($sear){
             $query->Orwhere('description','like','%'.@$_GET['search'].'%');
$query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
     $query->Orwhere('cert_name','like','%'.@$_GET['search'].'%');
$query->Orwhere('cert_company','like','%'.@$_GET['search'].'%');
       $query->Orwhere('cert_city','like','%'.@$_GET['search'].'%');
      $query->Orwhere('cert_country','like','%'.@$_GET['search'].'%');
   $query->Orwhere('cert_state','like','%'.@$_GET['search'].'%');
    $query->Orwhere('cert_email','like','%'.@$_GET['search'].'%'); 

       $query->OrwhereRaw("exists (select 1
              from ssl_host i  left join assets as ass on i.hostname=ass.id left join asset_ip_addresses as ip on  find_in_set(ip.id,i.ip_id)
              where i.ssl_id = s.id and
                    ( ass.hostname like '%$sear%' or ass.role like '%$sear%' or ip.ip_address_value like '%$sear%' )
             )");

              $query->OrwhereRaw("exists (select 1
              from ssl_san i 
              where i.ssl_id = s.id and
                    (i.san LIKE '%$sear%'  )
             )");
              $query->OrwhereRaw("exists (select 1
              from ssl_comments i 
              where i.ssl_id = s.id and
                    (i.comment LIKE '%$sear%'  )
             )");
 
 
     })->whereRaw("s.is_deleted=0 $cond")->whereIn('s.client_id',$userAccess)->orderBy($field,$orderby)->get();

 }
}
 else{


    if(Auth::user()->role=='admin'){
$qry=DB::table('ssl_certificate as s')->select('s.*','a.hostname','c.firstname','i.vendor_name as issuer','i.vendor_image','c.logo')->leftjoin('assets as a','a.id','=','s.cert_hostname')->leftjoin('vendors as i','s.cert_issuer','=','i.id')->join('clients as c','c.id','=','s.client_id')->where('s.is_deleted',0)->orderBy('s.id','desc')->get(); 
}
else{
    $qry=DB::table('ssl_certificate as s')->select('s.*','a.hostname','c.firstname','i.vendor_name as issuer','i.vendor_image','c.logo')->leftjoin('assets as a','a.id','=','s.cert_hostname')->leftjoin('vendors as i','s.cert_issuer','=','i.id')->join('clients as c','c.id','=','s.client_id')->whereIn('s.client_id',$userAccess)->where('s.is_deleted',0)->orderBy('s.id','desc')->get(); 
}
 
 }
 
    return view('exports.ExportSSL', [
        'qry' => $qry,
         
    ]);
 }
}