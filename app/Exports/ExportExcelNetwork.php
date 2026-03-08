<?php

namespace App\Exports;
 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class ExportExcelNetwork implements FromView
{
    protected $request;

 function __construct($request) {
        $this->request = $request;
 }

public function view(): View
{      

  

if(sizeof($_GET)>0){

$orderby='desc';
$field='n.id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}
 


$cond='';

if(isset($_GET['advance_search'])){


 if(isset($_GET['client_id'])  && $_GET['client_id']!='' ){
                    $client_id=$_GET['client_id'];
                $cond.=" and n.client_id ='$client_id'";
 }
  if(isset($_GET['site_id']) && sizeof($_GET['site_id'])>0){

                    $site_id=implode(',',$_GET['site_id']);
                $cond.=" and n.site_id in ($site_id)";
 }
 if(isset($_GET['vlan_id'])  && $_GET['vlan_id']!=''){
                $vlan_id=$_GET['vlan_id'];      
                $cond.=" and n.vlan_id='$vlan_id'";
 }
  if(isset($_GET['zone'])  && $_GET['zone']!=''){
                $zone=$_GET['zone'];      
                $cond.=" and n.zone='$zone'";
 }
  if(isset($_GET['internet_facing'])  && $_GET['internet_facing']!=''){
                $internet_facing=$_GET['internet_facing'];
                $cond.=" and n.internet_facing='$internet_facing'";
 }
   if(isset($_GET['wifi_enabled'])  && $_GET['wifi_enabled']!=''){
                $wifi_enabled=$_GET['wifi_enabled'];
                $cond.=" and n.wifi_enabled='$wifi_enabled'";
 }
   if(isset($_GET['certificate'])  && $_GET['certificate']!=''){
                $certificate=$_GET['certificate'];
                $cond.=" and n.certificate='$certificate'";
 }
    if(isset($_GET['encryption'])  && $_GET['encryption']!=''){
                $encryption=$_GET['encryption'];
                $cond.=" and n.encryption='$encryption'";
 }
  if(isset($_GET['sign_in_method'])  && $_GET['sign_in_method']!=''){
                $sign_in_method=$_GET['sign_in_method'];
                $cond.=" and n.sign_in_method='$sign_in_method'";
 }



                 
}


     $qry=DB::table('network as n')->select('n.*','c.firstname','s.site_name','c.logo')->whereRaw("n.is_deleted=0 $cond")->where(function($query){
       
        $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
        $query->Orwhere('n.description','like','%'.@$_GET['search'].'%');
        $query->Orwhere('n.zone','like','%'.@$_GET['search'].'%');
        $query->Orwhere('n.subnet_ip','like','%'.@$_GET['search'].'%');
        $query->Orwhere('n.mask','like','%'.@$_GET['search'].'%');
        $query->Orwhere('n.gateway_ip','like','%'.@$_GET['search'].'%');
        $query->Orwhere('n.ssid_name','like','%'.@$_GET['search'].'%');
     })->where('s.is_deleted',0)->where('c.is_deleted',0) ->join('clients as c','c.id','=','n.client_id')->join('sites as s','s.id','=','n.site_id')->orderBy($field,$orderby)->get(); 


}
 else{
$qry=DB::table('network as n')->select('n.*','c.firstname','s.site_name','c.logo')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('n.is_deleted',0)->join('clients as c','c.id','=','n.client_id')->join('sites as s','s.id','=','n.site_id') ->orderBy('n.id','desc')->get(); 
 
 }
    return view('exports.ExportNetwork', [
        'qry' => $qry
    ]);
 }
}