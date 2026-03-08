  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')



<?php 

$limit=10;
        $no_check=DB::Table('settings')->where('user_id',Auth::id())->first();
 
if(isset($_GET['limit']) && $_GET['limit']!=''){
    $limit=$_GET['limit'];
 
        if($no_check!=''){
                  if($page_type==''){
                DB::table('settings')->where('user_id',Auth::id())->update(['physical_asset'=>$limit]);
        }
        elseif($page_type=='servers'){
            DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_server'=>$limit]);
        }
        elseif($page_type=='other'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_others'=>$limit]);
        }
        elseif($page_type=='managed'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_managed'=>$limit]);
        }
        elseif($page_type=='support-contracts'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_support'=>$limit]);
        }
            elseif($page_type=='ssl-certificate'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_ssl'=>$limit]);
        }
            elseif($page_type=='inactive'){
             DB::table('settings')->where('user_id',Auth::id())->update(['asset_physical_inactive'=>$limit]);
        }


       
        }
        else{
              if($page_type==''){

                     DB::table('settings')->insert(['user_id'=>Auth::id(),'physical_asset'=>$limit]);
        }
        elseif($page_type=='servers'){

                      DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_server'=>$limit]);
        }
        elseif($page_type=='other'){
                       DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_others'=>$limit]);
        }
        elseif($page_type=='managed'){
                         DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_managed'=>$limit]);
        }
        elseif($page_type=='support-contracts'){
                          DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_support'=>$limit]);
        }
            elseif($page_type=='ssl-certificate'){
               DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_ssl'=>$limit]);
        }
            elseif($page_type=='inactive'){
                     DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_physical_inactive'=>$limit]);
        }


     
        }
        
}
else{
           
        if($no_check!=''){

            if($no_check->physical_asset!=''){
     
            if($page_type==''){
                 $limit=$no_check->physical_asset;
        }

        elseif($page_type=='servers'){
             if($no_check->asset_physical_server!=''){
                 $limit=$no_check->asset_physical_server;
        }
        elseif($page_type=='other'){
             if($no_check->asset_physical_others!=''){
                   $limit=$no_check->asset_physical_others;
        }
    }
        elseif($page_type=='managed'){
             if($no_check->asset_physical_managed!=''){
                    $limit=$no_check->asset_physical_managed;
        }
    }
        elseif($page_type=='support-contracts'){
             if($no_check->asset_physical_support!=''){
               $limit=$no_check->asset_physical_support;
        }
    }
            elseif($page_type=='ssl-certificate'){
                 if($no_check->asset_physical_ssl!=''){
                   $limit=$no_check->asset_physical_ssl;
        }
    }
            elseif($page_type=='inactive'){
                 if($no_check->asset_physical_inactive!=''){
               $limit=$no_check->asset_physical_inactive;
        }
    }
}





        }
        }
}
$userAccess=explode(',',Auth::user()->access_to_client);

if(sizeof($_GET)>0){


  

$orderby='asc';
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

  if(isset($_GET['domain']) && sizeof($_GET['domain'])>0){
                    $domain=implode(',',$_GET['domain']);
                $cond.=" and a.domain in ($domain)";
 }
    if(isset($_GET['asset_type_id']) && sizeof($_GET['asset_type_id'])>0){

                $asset_type_id=implode(',',$_GET['asset_type_id']);
                $cond.=" and a.asset_type_id in ($asset_type_id)";
 }

 if(isset($_GET['asset_status'])  && $_GET['asset_status']!=''){
                $asset_status=$_GET['asset_status'];      
                $cond.=" and a.AssetStatus='$asset_status'";
 }
 if(isset($_GET['hostname'])  && $_GET['hostname']!=''){
                $hostname=$_GET['hostname'];
                $cond.=" and a.hostname='$hostname'";
 }
 if(isset($_GET['sla'])  && $_GET['sla']!=''){
            $sla=$_GET['sla'];
                $cond.=" and a.sla='$sla'";
 }

  if(isset($_GET['os']) && sizeof($_GET['os'])>0){

                    $os=implode(',',$_GET['os']);
                $cond.=" and a.os in ($os)";
 }

 
  if(isset($_GET['ntp'])  && $_GET['ntp']!=''){
                $ntp=$_GET['ntp'];
                if($_GET['ntp']==2){
                        $cond.=" and a.ssl_certificate_status='Unassigned'";

                }
                else{
                $cond.=" and a.ntp='$ntp'";
            }
 }
   if(isset($_GET['internet_facing'])  && $_GET['internet_facing']!=''){
                $internet_facing=$_GET['internet_facing'];
                $cond.=" and a.internet_facing='$internet_facing'";
 }
   if(isset($_GET['SupportStatus'])  && $_GET['SupportStatus']!=''){
                $SupportStatus=$_GET['SupportStatus'];

                if($_GET['SupportStatus']=='N/A'){
                        $cond.=" and (a.SupportStatus='N/A' || a.SupportStatus is null || a.SupportStatus='') ";
                    }
                    else{

                         if($_GET['SupportStatus']=='End Of Life' || $_GET['SupportStatus']=='Other Partner' || $_GET['SupportStatus']=='Forgone'){
  $cond.=" and a.NotSupportedReason='$SupportStatus' ";   
                            }else{

                        $cond.=" and a.SupportStatus='$SupportStatus' ";   

                            }
                    }
}

  if(isset($_GET['ip_address'])  && $_GET['ip_address']!=''){
                 $ip_address=$_GET['ip_address'];
                $cond.=" and a.ip_address='$ip_address'";
 }

   if(isset($_GET['vlan_id'])  && $_GET['vlan_id']!=''){
            $vlan_id=$_GET['vlan_id'];
                $cond.=" and a.vlan_id='$vlan_id'";
 }
   if(isset($_GET['network_zone'])  && $_GET['network_zone']!=''){
                  $network_zone=$_GET['network_zone'];
                $cond.=" and a.network_zone='$network_zone'";
 }
 
 



   if(isset($_GET['os']) && sizeof($_GET['os'])>0){
                    $os=implode(',',$_GET['os']);
                $cond.=" and a.os in ($os)";
 }
  
  if(isset($_GET['manufacturer']) && sizeof($_GET['manufacturer'])>0){
                    $manufacturer=implode(',',$_GET['manufacturer']);
                $cond.=" and a.manufacturer in ($manufacturer)";
 }
 
   if(isset($_GET['use_'])  && $_GET['use_']!=''){
                  $use_=$_GET['use_'];
                $cond.=" and a.use_='$use_'";
 }

   if(isset($_GET['model'])  && $_GET['model']!=''){
                  $model=$_GET['model'];
                $cond.=" and a.model='$model'";
 }
 
   if(isset($_GET['managed'])  && $_GET['managed']!=''){
                  $managed=$_GET['managed'];
                $cond.=" and a.managed='$managed'";
 }

 
     if(isset($_GET['asset_type_id']) && sizeof($_GET['asset_type_id'])>0){

                $asset_type_id=implode(',',$_GET['asset_type_id']);
                $cond.=" and a.asset_type_id in ($asset_type_id)";
 }
 
 
 
 
 $sear=@$_GET['search'];
 
if(Auth::user()->role=='admin'){
 
$qry=DB::table('assets as a')->where(function($query) use($sear){
        
         $query->Orwhere('a.location','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.hostname','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.fqdn','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.role','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.model','like','%'.@$_GET['search'].'%');
$query->Orwhere('a.type','like','%'.@$_GET['search'].'%');
             $query->Orwhere('a.sn','like','%'.@$_GET['search'].'%');
  $query->Orwhere('a.cpu_model','like','%'.@$_GET['search'].'%');
    $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
              $query->Orwhere('a.ip_address','like','%'.@$_GET['search'].'%');
          $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
     $query->Orwhere('a.app_owner','like','%'.@$_GET['search'].'%');
            $query->Orwhere('a.sla','like','%'.@$_GET['search'].'%');
     $query->OrwhereRaw("exists (select 1
              from asset_comments i 
              where i.asset_id = a.id and
                    (i.comment LIKE '%$sear%'  )
             )");
 $query->OrwhereRaw("exists (select 1
              from asset_ip_addresses i 
              where i.asset_id = a.id and
                    (i.ip_address_value LIKE '%$sear%' or  i.ip_address_name LIKE '%$sear%' )
             )");

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId','c.logo','n.mask')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset') ->whereRaw("a.is_deleted=0 $cond")->where('a.is_deleted',0)->where('a.asset_type','physical') ->orderBy($field,$orderby) ->paginate($limit); 
 
 

}
else{

$qry=DB::table('assets as a')->where(function($query) use ($sear){
     
         $query->Orwhere('a.location','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.hostname','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.fqdn','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.role','like','%'.@$_GET['search'].'%');
         $query->Orwhere('a.model','like','%'.@$_GET['search'].'%');
$query->Orwhere('a.type','like','%'.@$_GET['search'].'%');
             $query->Orwhere('a.sn','like','%'.@$_GET['search'].'%');
  $query->Orwhere('a.cpu_model','like','%'.@$_GET['search'].'%');
    $query->Orwhere('domain_name','like','%'.@$_GET['search'].'%');
              $query->Orwhere('a.ip_address','like','%'.@$_GET['search'].'%');
          $query->Orwhere('n.vlan_id','like','%'.@$_GET['search'].'%');
     $query->Orwhere('a.app_owner','like','%'.@$_GET['search'].'%');
            $query->Orwhere('a.sla','like','%'.@$_GET['search'].'%');
     $query->OrwhereRaw("exists (select 1
              from asset_comments i 
              where i.asset_id = a.id and
                    (i.comment LIKE '%$sear%'  )
             )");
 $query->OrwhereRaw("exists (select 1
              from asset_ip_addresses i 
              where i.asset_id = a.id and
                    (i.ip_address_value LIKE '%$sear%' or  i.ip_address_name LIKE '%$sear%' )
             )");

 
     })->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId','c.logo')->join('clients as c','c.id','=','a.client_id','n.mask')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset') ->whereRaw("a.is_deleted=0 $cond")->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 

}

}
 else{

  if(Auth::user()->role=='admin'){ 

 
$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId','c.logo','n.mask')->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset') ->where('a.is_deleted',0)->where('a.asset_type','physical')->orderBy('a.id','asc') ->paginate($limit); 
 
 }
else{ 

$qry=DB::table('assets as a')->select('a.*','s.site_name','p.hostname as parent_asset_name','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name','at.asset_icon','at.asset_type_description','n.vlan_id  as vlanId','c.logo','n.mask')->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('assets as p','p.id','=','a.parent_asset') ->where('a.is_deleted',0)->where('a.asset_type','physical')->whereIn('a.client_id',$userAccess)->orderBy('a.id','asc') ->paginate($limit); 
 
}}
if(isset($_GET['id'])){
$GETID=$_GET['id'];
}
else{
$GETID=@$qry[0]->id;
}

 ?>        <!-- Main Container -->
   <main id="main-container pt-0">
                <!-- Hero -->
           
<style type="text/css">
             .dropdown-menu {
        z-index: 100000!important;
    }
    .pagination{
        margin-bottom: 0px;
    }
  #page-header{
        display: none;
    }
    .ActionIcon{

    border-radius: 50%;
    padding: 6px;
}
.OsIcons{
    background: none!important;
}
.ActionIcon:hover{

 background: #F2F2F2;
}
   body {
overflow: -moz-scrollbars-vertical;
  overflow-x: hidden;
}
 .blockDivs .block-header-default {
    background-color: #f1f3f8;
    padding: 7px 1.25rem;
}
.blockDivs{
    border: 1px solid lightgrey;
    margin-bottom: 10px!important;
}
 
.cert_type_button label,
.cert_type_button input {
  
 
}
.cert_type_button{
    float: left;
}
.cert_type_button input[type="radio"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.cert_type_button input[type="radio"]:checked + label {
    background: #4194F6;
    font-weight: bold;
      color: white;
}

.cert_type_button label:hover {
  
 
 
background-color:#EEEEEE;
color: #7F7F7F;
 
 
}
@media only print{
    .no-print{
        display: none!important;
    }
    #showData{
        height: 100%!important;
      
    }   
    .content{
    background: #F0F3F8;
 
    }
}

.cert_type_button label {
  
  width: 150px;
 
border-color:#D9D9D9;
color: #7F7F7F;
font-size: 12pt;

 
}
.modal-backdrop{
    background-color: #00000080!important;
}
.alert-info,.alert{
 
        width: auto!important;
        padding-right: 70px;
        background-color:#262626!important;
 top: 75px!important;
 right: 50px!important;
color:#FFFFFF!important;
font-family: Calibri!important;
font-size: 14pt!important;
 padding-top: 14px;
 padding-bottom: 14px;
        z-index: 11000!important;
}

.attachmentDivNew:hover{
color: #FFFFFF!important;
background-color:#4194F6;
}
.alert-info .close{
color: #898989!important;
font-size: 30px!important;
top: 10px!important;
right: 15px!important;
opacity: 1!important;
font-weight: 200!important;
width: 33px;
padding-bottom: 3px;
    }
    .alert-info .close:hover{
background-color: white!important;
border-radius: 50%;
    }
.modal-lg, .modal-xl {
    max-width: 950px;
}
.alert-info .btn-tooltip{
color: #00B0F0!important;
font-family: Calibri!important;
font-size: 14pt!important; 
font-weight: bold!important;
}
.btn-notify{
    color: #00B0F0;
font-family: Calibri;
font-size: 14pt;
font-weight: bold;
    padding: 5px 13px;
    font-weight: bold;
    border-radius: 7px;
}
.btn-link{

    padding: 0px;
    margin: .25rem .5rem;
}
.btn-link:hover{
        box-shadow: -1px 2px 4px 3px #99dff9;
    background: #99dff9;
}
.btn-notify:hover{
 color: #00B0F0;
background: #386875;

}
.btnDeleteAttachment{
    position: absolute;
    right: 2px;
    top: 6px;

}
.attachmentDiv{
        border: 1px solid lightgrey;
    padding: 7px;
    font-size: 10px;
    border-radius: 32px;
    color: grey;
    width: 50px;
}
.dropdown-menu{
        border: 1px solid #D4DCEC!important;
    box-sizing: 1px 1px 1pxo #D4DCEC;
    box-shadow: 6px 6px 8px #8f8f8f5e;
    border-radius: 11px;
}
.bs-select-all,.bs-deselect-all,.bs-actionsbox .btn-light {
      border: 1px solid #D9D9D9!important;
    background: white!important;

    color: #2080F4!important;
    font-weight: normal!important;
font-family: Calibri!important;
font-size: 12pt!important;
border-radius: 15px!important;
padding-top: 0px!important;
padding-bottom: 0px!important;
margin-top: 10px!important;
margin-bottom: 10px!important;
margin-left: 10px;
margin-right: 10px;
height: 35px!important;
padding-left: 10px;
padding-right: 10px;
min-width: 90px!important;
}
 

 .bs-deselect-all:hover{
background-color: #EEEEEE!important;
    color: #7F7F7F!important;
    }
    .bs-select-all:hover{
background-color: #EEEEEE!important;
    color: #7F7F7F!important;
    }

.c1{
    color: #3F3F3F;
font-family: 'Calibri'; 
}
.c2{
    color: #7F7F7F;
font-family: 'Calibri'; 
}
.c3{
    color: #595959;
font-family: 'Calibri'; 
}
.cert_type_button label,
.cert_type_button input {
  
 
}
.cert_type_button{
    float: left;
}
.cert_type_button input[type="radio"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.cert_type_button input[type="radio"]:checked + label {
    background: #4194F6;
    font-weight: bold;
      color: white;
}

.cert_type_button label:hover {
  
 
 
background-color:#EEEEEE;
color: #7F7F7F;
 
 
}

.cert_type_button label {
  
  width: 150px;
 
border-color:#D9D9D9;
color: #7F7F7F;
font-size: 12pt;

 
}
.modal-backdrop{
    background-color: #00000080!important;
}
.alert-info,.alert{
 
        width: auto!important;
        padding-right: 70px;
        background-color:#262626!important;
 top: 75px!important;
 right: 50px!important;
color:#FFFFFF!important;
font-family: Calibri!important;
font-size: 14pt!important;
 padding-top: 14px;
 padding-bottom: 14px;
        z-index: 11000!important;
}

.attachmentDivNew:hover{
color: #FFFFFF!important;
background-color:#4194F6;
}
.alert-info .close{
color: #898989!important;
font-size: 30px!important;
top: 10px!important;
right: 15px!important;
opacity: 1!important;
font-weight: 200!important;
width: 33px;
padding-bottom: 3px;
    }
    .alert-info .close:hover{
background-color: white!important;
border-radius: 50%;
    }

.alert-info .btn-tooltip{
color: #00B0F0!important;
font-family: Calibri!important;
font-size: 14pt!important; 
font-weight: bold!important;
}
.btn-notify{
    color: #00B0F0;
font-family: Calibri;
font-size: 14pt;
font-weight: bold;
    padding: 5px 13px;
    font-weight: bold;
    border-radius: 7px;
}
.btn-link{

    padding: 0px;
    margin: .25rem .5rem;
}
.btn-link:hover{
        box-shadow: -1px 2px 4px 3px #99dff9;
    background: #99dff9;
}
.btn-notify:hover{
 color: #00B0F0;
background: #386875;

}
.btnDeleteAttachment{
    position: absolute;
    right: 2px;
    top: 6px;

}
.btnNewAction:hover,.btnNewAction1:hover,.btnNewAction2:hover{
    background: #59595930;
    border-radius: 50%;
 }
 .btnNewAction{
    height: 29px;
 }   
 .btnNewAction1{
    height: 23px;

 }   
 .btnNewAction2{
    height: 20px;
 }   
 .HostActive{
    font-family: Calibri;
    font-size:9pt;
    font-weight: bold;
    color:#1EFF00 ;
   letter-spacing:0px ;
 }
 .HostInActive{
    font-family: Calibri;
    font-size:9pt;
    font-weight: bold;
    color:#C0C6CC ;
      letter-spacing:0px ;
 
 }
 .text-info{
    color: #4194F6!important;
 }
  .text-danger{
    color: #E54643!important;
 }
   .text-warning{
    color: #FFCC00!important;
 }
 
.contract_type_button label,
.contract_type_button input {
  
 
}
.contract_type_button{
    float: left;
}
.contract_type_button input[type="radio"],.contract_type_button input[type="checkbox"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.contract_type_button input[type="radio"]:checked + label ,.contract_type_button input[type="checkbox"]:checked + label {
    background: #4194F6;
    font-weight: bold;
      color: white;
}

.contract_type_button label:hover {
  
 
 
background-color:#EEEEEE;
color: #7F7F7F;
 
 
}

.contract_type_button label {
  
  width: 150px;
 
border-color:#D9D9D9;
color: #7F7F7F;
font-size: 12pt;

 
}
</style>
              
                <!-- END Hero -->

                <!-- Page Content -->
                 <div class="content  no-print">
          <div class="block block-rounded   mb-0 pb-0">
                                   <div class="block-content pt-0 mt-0">

<div class="TopArea" style="position: sticky; 
    padding-top: 8px;
    z-index: 1000;
    background: white;
    padding-bottom: 5px;">
    <div class="row" >
                 <?php
                   
 
  
 
 $filter=(isset($_GET['advance_search'])?'advance_search='.$_GET['advance_search']:'').(isset($_GET['client_id'])?'&client_id='.$_GET['client_id']:'').(isset($_GET['site_id'])?'&'.http_build_query(array('site_id'=>$_GET['site_id'])):'').(isset($_GET['asset_type_id'])?'&'.http_build_query(array('asset_type_id'=>$_GET['asset_type_id'])):'').(isset($_GET['domain'])?'&'.http_build_query(array('domain'=>$_GET['domain'])):'').(isset($_GET['manufacturer'])?'&'.http_build_query(array('manufacturer'=>$_GET['manufacturer'])):'').(isset($_GET['hostname'])?'&hostname='.$_GET['hostname']:'').(isset($_GET['asset_status'])?'&asset_status='.$_GET['asset_status']:'').(isset($_GET['sla'])?'&sla='.$_GET['sla']:'').(isset($_GET['SupportStatus'])?'&SupportStatus='.$_GET['SupportStatus']:'').(isset($_GET['os'])?'&'.http_build_query(array('os'=>$_GET['os'])):'').(isset($_GET['ntp'])?'&ntp='.$_GET['ntp']:'').(isset($_GET['model'])?'&model='.$_GET['model']:'').(isset($_GET['managed'])?'&managed='.$_GET['managed']:'').(isset($_GET['vlan_id'])?'&vlan_id='.$_GET['vlan_id']:'').(isset($_GET['network_zone'])?'&network_zone='.$_GET['network_zone']:'').(isset($_GET['use_'])?'&use_='.$_GET['use_']:'').(isset($_GET['limit'])?'&limit='.$_GET['limit']:'');
?> 
        <div class="col-sm-3">
                     <form class="push mb-0"   method="get" id="form-search" action="{{url('physical/')}}?{{$filter}}">
                                        
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control searchNew" name="search" placeholder="Search SSL Certificate">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                              <img src="{{asset('public/img/ui-icon-search.png')}}" width="23px">
                                        </span>
                                    </div>
                                </div>
                                 <div class="    float-left " role="tab" id="accordion2_h1">
                                         
                                    
                                   <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->
                                      
                                            </div>  
                            </form>
</div>
<div class="col-sm-3">
        <span data-toggle="modal" data-bs-target="#filterModal" data-target="#filterModal"> 
      <button type="button" class="btn btn-dual d1 {{isset($_GET['advance_search'])?'active':''}} "   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Filters"   >
                           <img src="{{asset('public/img/ui-icon-filters.png')}}" width="20px" height="24px">
                        </button>
                    </span>
                  
                             <span data-toggle="modal" data-bs-target="#ExportModal" data-target="#ExportModal"> 
                           <button class="btn btn-dual d2    "    data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Export"   href="javascript:;" style="margin-left: 5px;margin-right:  0px" >
                           <img src="{{asset('public/img/ui-icon-export.png')}}" width="20px" height="20px">
                        </button>
                    </span>
                              @if(Auth::user()->role!='read') 
                         <a class="btn btn-dual d2    "    href="{{url('add-assets')}}/physical" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Physical Asset">
                           <img src="{{asset('public/img/ui-icon-add.png')}}"  width="19px" height="19px">
                        </a>
                        @endif
</div>

<div class="   col-lg-3   " >
          {{$qry->appends($_GET)->onEachSide(0)->links()}}
                       </div><div class="d-flex text-right col-lg-3 justify-content-end" ><form  id="limit_form" class="ml-2 mb-0" action="{{url('physical')}}/{{$page_type}}?{{$_SERVER['QUERY_STRING']}}">
                                <select name="limit" class="float-right form-control mr-3   px-0" style="width:auto">
                                        <option value="10" {{@$limit==10?'selected':''}}>10</option>
                                        <option value="25" {{@$limit==25?'selected':''}}>25</option>
                                        <option value="50" {{@$limit==50?'selected':''}}>50</option>
                                        <option value="100" {{@$limit==100?'selected':''}}>100</option>
                                </select>
                            </form>
                                 @if(@Auth::user()->role=='admin')
                        <a href="{{url('settings')}}"  data-toggle="tooltip" data-title="Settings"class="mr-3 text-dark d3   " ><img src="{{asset('public/img/ui-icon-settings.png')}}" width="23px"></a>
                            @endif
                        <!-- User Dropdown -->
                        <div class="dropdown d-inline-block">
                            <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  >
                       @if(Auth::user()->user_image=='')
                                <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public')}}/dashboard_assets/media/avatars/avatar2.jpg"  alt="">
                                @else
                                  <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public/client_logos/')}}/{{Auth::user()->user_image}}"  alt="">
                                
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">
                                
                                <div class="p-2">
                                    @auth
                                    <a class="dropdown-item" href="{{url('change-password')}}">
                                        <i class="far fa-fw fa-user mr-1"></i> My Profile
                                    </a>
                                   
                                    
                                    


                                    <!-- END Side Overlay -->
<form id="logout-form" class="mb-0" method="post" action="{{url('logout')}}">
  @csrf
</form>
                                    <div role="separator" class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:;" onclick="document.getElementById('logout-form').submit()">
                                        <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                    </a>
                                    @else
                                         <a class="dropdown-item" href="{{url('/login')}}">
                                        <i class="far fa-fw fa-user mr-1"></i> Login
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </div>



                     </div>
                 </div>


                            </div>
                        </div>
                    </div>

              



                        
           

       
                                    <div class="content  ">
       <!-- Page Content -->
       <div class="row px-0">
         <div class="col-lg-4   no-print  "  style="overflow-y: auto;height: 90vh;">
                @foreach($qry as $q)

<div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="{{$q->id}}" style="cursor:pointer;">
                    
                        <div class="block-content d-flex py-3 pl-1 mt-0 position-relative">
                                        <div class="mr-1   align-items-center  d-flex" style="width:15%">
                                            <img src="{{asset('/public')}}/client_logos/{{$q->logo}}" class="rounded-circle"  width="100%" style="max-width:90px;object-fit: cover;">
                                        </div>
                                        <div class="  " style="width:60%">
                                                    <p class="font-12pt mb-0  text-truncate c1"><b>{{$q->asset_type_description}}</b></p>
                                                                    <p class="font-11pt   mb-0 text-truncate  c4"  style="max-width:100%" data="{{$q->id}}">{{$q->fqdn}}</p>
                                                    <p class="font-12pt mb-0 text-truncate c2">{{$q->role}}</p>
                                                    <p class="font-12pt mb-0 text-truncate c3"><b>{{$q->firstname}}</b></p>
                                        </div>
                                        <div class=" text-right" style="width:25%;;">
                                                                            <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                               

                                                        
 


                    @if($q->AssetStatus=='1')

                         
                                           <div class=" bg-new-green ml-auto  badge-new  text-center font-weight-bold   text-white"  >
                                                                                 <img src="{{asset('public')}}/img/status-white-active.png" class="mr-2" width="15px"><span class=" ">Active</span>
                                                                    </div>  
                                                                 

                             
                               
                                @else 
                                       <div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold text-white"  >
                                                                                 <img src="{{asset('public')}}/img/action-white-end-revoke.png" class="mr-2" width="15px"><span class=" ">Inactive</span>
                                                                    </div>
                            

                                @endif

                                                             


                                                                </div>
                                                                 
                                                                <div  class="" style="position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                
                                                                    @if(strpos($q->operating_system_name,'Windows')!==false)
                                                                        <div class="ActionIcon OsIcons" data-src="{{asset('public')}}/img/icon-os-windows-color.png?cache=1" data-original-src="{{asset('public')}}/img/icon-os-windows-grey.png">
                                                                           <a href="javascript:;" class="toggle" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{$q->operating_system_name}}" data-html="true"   data-original-title="<span class='HostActive text-white' >{{$q->operating_system_name}}</span><br><span class=' HostActive text-yellow' >{{$q->use_}}</span>" >
                                                                        <img  src="{{asset('public')}}/img/icon-os-windows-grey.png" data="windows"   width="20px">
                                                                    </a>
                                                                </div>
                                                                        @elseif(strpos($q->operating_system_name,'ESXi')!==false)
                                                                                     <div class="ActionIcon OsIcons" data-src="{{asset('public')}}/img/icon-os-esxi-color.png?cache=1" data-original-src="{{asset('public')}}/img/icon-os-esxi-grey.png">
                                                                           <a href="javfiascript:;" class="toggle" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{$q->operating_system_name}}" data-html="true"   data-original-title="<span class='HostActive text-white' >{{$q->operating_system_name}}</span><br><span class=' HostActive text-yellow' >{{$q->use_}}</span>" >
                                                                           <img  src="{{asset('public')}}/img/icon-os-esxi-grey.png" data="esxi"  width="24px" >
                                                                       </a>
                                                                   </div>
                                                                        @elseif(strpos($q->operating_system_name,'Linux')!==false)
                                                                                     <div class="ActionIcon OsIcons" data-src="{{asset('public')}}/img/icon-os-linux-color.png?cache=1" data-original-src="{{asset('public')}}/img/icon-os-linux-grey.png">
                                                                           <a href="javascript:;" class="toggle" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{$q->operating_system_name}}" data-html="true"   data-original-title="<span class='HostActive text-white' >{{$q->operating_system_name}}</span><br><span class=' HostActive text-yellow' >{{$q->use_}}</span>" >
                                                                           <img  src="{{asset('public')}}/img/icon-os-linux-grey.png" data="linux"    width="26px" >
                                                                       </a>
                                                                   </div>
                                                                        @endif
                                                                        </a><?php $line_items=DB::Table('contract_assets as ca')->select('a.contract_no','a.contract_status','a.contract_start_date','a.contract_end_date')->where('ca.hostname',$q->id)->join('contracts  as a','a.id','=','ca.contract_id')->groupBy('a.contract_no')->where('a.is_deleted',0)->orderBy('a.contract_no','asc')->get();
                                                                            $cvm='<b class="HostActive text-white">Assigned Contracts</b><br>';
                                                                                if(sizeof($line_items)>0){
                                                                            foreach($line_items as $l){
                                                                                    $contract_end_date=date('Y-M-d',strtotime($l->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
        if($l->contract_status=='Active'){

                  

if($abs_diff<=30){
    $cvm.='<span class="HostActive text-warning">'.$l->contract_no.'</span><br>'; 
 }else{
 $cvm.='<span class="HostActive  ">'.$l->contract_no.'</span><br>'; 
  }

}
 elseif($l->contract_status=='Inactive'){
     $cvm.='<span class="HostActive text-info">'.$l->contract_no.'</span><br>'; 
  }elseif($l->contract_status=='Expired/Ended'){
     $cvm.='<span class="HostActive text-danger">'.$l->contract_no.'</span><br>'; 
   }elseif($l->contract_status=='Ended'){
     $cvm.='<span class="HostActive text-danger">'.$l->contract_no.'</span><br>'; 
   }elseif($l->contract_status=='Expired'){
     $cvm.='<span class="HostActive text-danger">'.$l->contract_no.'</span><br>'; 
  }else{}
}
}
else{
     $cvm.='<span class="HostInactive  ">Unassigned</span><br>'; 
}
           

           $line_items1=DB::Table('ssl_host as ca')->select('a.cert_name','a.cert_status','a.cert_edate')->where('ca.hostname',$q->id)->join('ssl_certificate  as a','a.id','=','ca.ssl_id')->groupBy('a.cert_name')->where('a.is_deleted',0)->orderBy('a.cert_name','asc')->get();
                                                                            $cvm1='<b class="HostActive text-white">Assigned Certificates</b><br>';
                                                                                if(sizeof($line_items1)>0){
                                                                            foreach($line_items1 as $l){
                                                                                    $cert_edate=date('Y-M-d',strtotime($l->cert_edate)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($cert_edate);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
        if($l->cert_status=='Active'){

                  

if($abs_diff<=30){
    $cvm1='<span class="HostActive text-warning">'.$l->cert_name.'</span><br>'; 
 }else{
 $cvm1.='<span class="HostActive  ">'.$l->cert_name.'</span><br>'; 
  }

}
 elseif($l->cert_status=='Inactive'){
     $cvm1.='<span class="HostActive text-info">'.$l->cert_name.'</span><br>'; 
  }elseif($l->cert_status=='Expired/Ended'){
     $cvm1.='<span class="HostActive text-danger">'.$l->cert_name.'</span><br>'; 
   }elseif($l->cert_status=='Ended'){
     $cvm1.='<span class="HostActive text-danger">'.$l->cert_name.'</span><br>'; 
   }elseif($l->cert_status=='Expired'){
     $cvm1.='<span class="HostActive text-danger">'.$l->cert_name.'</span><br>'; 
  } 
}
}
else if($q->ssl_certificate_status=='Unassigned' || $q->ntp=='1'){
     $cvm1.='<span class="HostInactive  ">Unassigned</span><br>';
}
else{
     $cvm1.='<span class="HostInactive  ">N/A</span><br>'; 
}                                                      
         ?>

                                                                  <div class="ActionIcon" data-src="{{asset('public')}}/img/icon-ssl-certificate-grey-darker.png?cache=1" data-original-src="{{asset('public')}}/img/icon-ssl-certificate-grey.png">
    <a  data-toggle="tooltip" data-trigger="hover" class="b  " data-placement="top" data-title="Edit" data-html="true" data-original-title="{{$cvm1}}" href="javascript:;" c>
                                                                       
                                                                        <img src="{{asset('public')}}/img/icon-ssl-certificate-grey.png" width="24px" >
                                                                        </a>
                                                                    </div>
                                                                     <div class="ActionIcon" data-src="{{asset('public')}}/img/icon-contract-grey-darker.png?cache=1" data-original-src="{{asset('public')}}/img/icon-contract-grey.png">
                                                                              <a  data-toggle="tooltip" data-trigger="hover" class="b  " data-placement="top" data-title="Edit" data-html="true" data-original-title="{{$cvm}}" href="javascript:;" c>
                                                                       
                                                                             <img src="{{asset('public')}}/img/icon-contract-grey.png"  width="23px">
                                                                        </a>
                                                                    </div>

                                                                        @if($q->asset_type_description=='Physical Server')
                                                                         <div class="ActionIcon" data-src="{{asset('public')}}/img/icon-p-asset-d-grey.png?cache=1" data-original-src="{{asset('public')}}/img/icon-p-asset-l-grey.png">
                                                                            @else
                                                                              <div class="ActionIcon" data-src="{{asset('public')}}/img/icon-hosts-grey-darker.png?cache=1" data-original-src="{{asset('public')}}/img/icon-hosts-grey.png"  width="24px">
                                                                            @endif
                                                                         <a  data-toggle="tooltip" data-trigger="hover" class="b  " data-placement="top" data-title="Edit" data-html="true" data-original-title="<span class='HostActive text-white'>{{$q->vendor_name}} {{$q->model}} {{$q->type}}</span><br><span  class='HostActive text-warning' >{{$q->sn}}</span><br><span  class='HostActive  ' >{{$q->ip_address}}{{$q->mask}}</span>" href="javascript:;" c>
                                                                       
                                                                         @if($q->asset_type_description=='Physical Server')
                                                                                <img src="{{asset('public')}}/img/icon-p-asset-l-grey.png"  width="24px" >
                                                                                @else
                                                                                   <img src="{{asset('public')}}/img/icon-hosts-grey.png" width="24px" >
                                                                        @endif
                                                                        </a>
                                                                    </div>
                                                                         <div class="ActionIcon" data-src="{{asset('public')}}/img/icon-edit-grey-darker.png?cache=1" data-original-src="{{asset('public')}}/img/icon-edit-grey.png">
                                                                         <a  data-toggle="tooltip" data-trigger="hover" class="b  " data-placement="top" data-title="Edit"   data-html="true"    data-original-title="<span class='font-10pt'>Edit</span>"   href="{{url('edit-assets')}}?id={{$q->id}}" c>
                                                                       
                                                                        <img src="{{asset('public')}}/img/icon-edit-grey.png" width="24px"  >
                                                                        </a>
                                                                    </div>
                                                                     <div class="ActionIcon" data-src="{{asset('public')}}/img/icon-trash-grey-darker.png" data-original-src="{{asset('public')}}/img/icon-trash-grey.png">
                                                           
                                                                           <a  data-toggle="tooltip" data-trigger="hover" class="btnDelete " data="{{$q->id}}" data-placement="top" data-html="true"  data-title="Delete" data-original-title="<span class='font-10pt'>Delete</span>"  href="javascript:;" c>
                                                                        <img src="{{asset('public')}}/img/icon-trash-grey.png"  width="22px" >
                                                                        </a>
                                                                    </div>
                                                                    </div>
                                        </div>
                                </div>
                            </div>
                            @endforeach
                                                          </div>

               <div class="col-lg-8    " id="showData"  style="overflow-y: auto;height:90vh;">
          




    </div>


                    </div>



                </div>
               </div>
       </div>


</div>

 

























































<form action="{{url('docommision-asset')}}" method="post">
    @csrf
<div class="modal fade" id="EndModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header   ">
                            <span class="b e section-header reactivateHeader">Decommission Asset</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content pt-0 row">
                            
                    <input type="hidden" name="id">
 
<div class="col-sm-12    p      ">
         <textarea     class="form-control  "   rows="4" required="" name="reason"   ></textarea>
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="EndSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>
    </form>


 
<form action="" class="mb-0 pb-0">
<div class="modal fade" id="filterModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Filter Physical Assets</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
                   
 
   
   <div class="row">
     

      <div class="col-sm-4  form-group">     
                                                     <label>Status</label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="asset_status"  >
                                                        <option value="">All</option>
                                                        <option value="1" {{@$_GET['asset_status']==1?'selected':''}}>Active</option>
                                                        <option value="0" {{isset($_GET['asset_status']) && $_GET['asset_status']==0?'selected':''}}>Decomissioned</option>
     
                                                    </select>
                             </div>
                                   <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Type</label>
                                          
                                          
                                                 <select type="" class="form-control  selectpicker   " id="asset_type_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="asset_type_id[]" multiple=""   >
                                                     <?php
                                            $asset_type_qry=DB::Table('asset_type')->where('is_deleted',0)->orderBy('asset_type_description','asc') ->get();

                                                          $asset_typeArray=$_GET['asset_type_id'] ?? [];
                                             ?>
                                                         @foreach($asset_type_qry as $s)
                                                    <option value="{{$s->asset_type_id}}" {{in_array($s->asset_type_id,$asset_typeArray)?'selected':''}}  >{{$s->asset_type_description}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                                <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Client</label>
                                            <?php
                                              $userAccess=explode(',',Auth::user()->access_to_client);

                                            if(Auth::user()->role=='admin'){
                                            $client=DB::Table('clients')->where('is_deleted',0)->where('client_status',1)->orderBy('firstname','asc')->get();
                                            }
                                            else{
                                                $client=DB::Table('clients')->whereIn('id',$userAccess)->where('is_deleted',0)->where('client_status',1)->orderBy('firstname','asc')->get();   
                                            }
                                             ?>
                              
                                                 <select type="client_id" class="form-control selectpicker"   data-style="btn-outline-light border text-dark" data-live-search="true" id="client_id"  title="All" value="" name="client_id" placeholder="Client"  >
                                           
                                                    @foreach($client as $c)
                                                    <option value="{{$c->id}}" {{@$_GET['client_id']==$c->id?'selected':''}}>{{$c->firstname}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                 
                                         
                                        <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Site</label>
                                          
                                          
                                                 <select type="" class="form-control    selectpicker " id="site_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="site_id[]" multiple=""   >
                                                     <?php
                                            $site=DB::Table('sites')->where('is_deleted',0)->orderBy('site_name','asc') ->get();

                                                          $siteArray=$_GET['site_id'] ?? [];
                                             ?>
                                                         @foreach($site as $s)
                                                    <option value="{{$s->id}}" {{in_array($s->id,$siteArray)?'selected':''}}  >{{$s->site_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>


                                          <div class="col-sm-4 form-group">
                                            <label class="   " for="example-hf-client_id">OS</label>
                                            <?php
                                            $operating_system=DB::Table('operating_systems')->where('is_deleted',0)->orderBy('operating_system_name','asc') ->get();
                                             ?>
                                           <select type="" class="form-control   selectpicker"   data-style="btn-outline-light border text-dark" data-live-search="true" title="All" multiple="" id="os"    data-actions-box="true"  data-live-search="true"  value="" name="os[]"   >
                                                
                                                        <?php $osArray=$_GET['os'] ?? [] ?>
                                                    @foreach($operating_system as $c)

                                                    <option value="{{$c->id}}"  {{in_array($c->id,$osArray)?'selected':''}} >{{$c->operating_system_name}}</option>
                                                    @endforeach
                                                    </select>
                                           
                                        </div>

                                            <div class="col-sm-4 form-group">
                                                        <label class="   " for="example-hf-client_id">Domain</label>
                                                 <select type="text" class="form-control   selectpicker " id="domain"     data-style="btn-outline-light border text-dark" data-actions-box="true" data-live-search="true" title="All" name="domain[]" placeholder="" multiple=""  >
                                                      
                                                              <?php
                                            $client=DB::Table('domains')->where('is_deleted',0) ->orderBy('domain_name','asc') ->get();
                                              $domainArray=$_GET['domain'] ?? [];
                                             ?>
                                                         @foreach($client as $c)
                                                    <option value="{{$c->id}}" {{in_array($c->id,$domainArray)?'selected':''}}>{{$c->domain_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>



                                   <div class="col-sm-4 form-group">
                                          
                 
                                            <label class="   " for="example-hf-email">Environment</label>
                                       
                                                 <input type="text" class="form-control"  value="{{@$_GET['use_']}}" name="use_" placeholder="All"  >
                                            </div>
                                                   
   <div class="col-sm-4  form-group">     
                                                     <label>SSL Certificate </label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="ntp"  >
                                                        <option value="">All</option>
                                                    <option value="1" {{@$_GET['ntp']==1?'selected':''}}>Assigned</option>
                                               
                                                       <option value="2"  {{isset($_GET['ntp']) && $_GET['ntp']==2?'selected':''}} >Unassigned</option>
      <option value="0"  {{isset($_GET['ntp']) && $_GET['ntp']==0?'selected':''}} >N/A</option>
                                                    </select>
                                            </div>


                                             <div class="col-sm-4  form-group">     
                                                     <label>Support  </label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="SupportStatus"  >
                                                        <option value="">All</option>
                                                    <option value="N/A" {{@$_GET['SupportStatus']==1?'selected':''}}>N/A</option>
                                                    <option value="Supported" {{@$_GET['SupportStatus']=='Supported'?'selected':''}}>Supported</option>
                                                    <option value="Unassigned" {{@$_GET['SupportStatus']=='Unassigned'?'selected':''}}>Unassigned</option>
                                                    <option value="Expired" {{@$_GET['SupportStatus']=='Expired'?'selected':''}}>Expired</option>
          <option value="End Of Life" {{@$_GET['SupportStatus']=='End Of Life'?'selected':''}}>EoL</option>
                                                     <option value="Other Partner" {{@$_GET['SupportStatus']=='Other Partner'?'selected':''}}>OP</option>
                                                            <option value="Forgone" {{@$_GET['SupportStatus']=='Forgone'?'selected':''}}>Forgone</option>
 
 

                                                        </select>
                                            </div>
                                            

 
                                  
       <div class="col-sm-4 form-group">
                                                        <label class="   " for="example-hf-client_id">Manufacturer</label>
                                          
                                                    <?php
                                            $manufacturer=DB::Table('vendors')->where('is_deleted',0)->orderBy('vendor_name','asc')->get();
                                               $manufacturerArray=$_GET['manufacturer'] ?? [];
                                             ?>
                                    
                                               <select type="" class="form-control selectpicker " id="manufacturer"    data-style="btn-outline-light border text-dark" data-actions-box="true" data-live-search="true" value=""  id="manufacturer"  name="manufacturer[]" multiple=""  >
                                             
                                                    @foreach($manufacturer as $c)
                                                    <option value="{{$c->id}}"   {{in_array($c->id,$manufacturerArray)?'selected':''}} >{{$c->vendor_name}}</option>
                                                    @endforeach
                                                    </select>
                                       


                                            </div>


                                   
    <div class="col-sm-4 form-group">
                                                        <label class="   " for="example-hf-client_id">Model</label>
                                          
      <input type="text" class="form-control" list="modelDatalist"     id="model" name="model"   value="{{@$_GET['model']}}"  > 
                                                <datalist id="modelDatalist">
                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(model) as model'))->get(); 

                                                    ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->model}}"></option>
                                                    @endforeach

                                                </datalist>

                                          </div>    


                                             <div class="col-sm-4  form-group">     
                                                     <label>Managed  </label>                 
                                                 <select type="" class="form-control  "   data-style="btn-outline-light border text-dark"      value="" name="managed"  >
                                                        <option value="">All</option>
                                                     
                                                    <option value="1" {{@$_GET['managed']=='Supported'?'selected':''}}>Managed</option>
                                                    <option value="0" {{@$_GET['managed']=='Unmanaged'?'selected':''}}>Unmanaged</option>
                                                      
                                                 </select>
                                            </div>
                                            



                                             
                                       
                                          
        </div>
                         
                        </div>
                        <div class="block-content block-content-full   pt-4" style="padding-left: 9mm;padding-right: 9mm">
                            <button type="submit" class="btn mr-3 btn-new"    name="advance_search"   >Apply</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>
                        @if(isset($_GET['advance_search']))
                                               
 <a href="{{url('physical')}}" class="btn     btn-new-secondary float-right" style="background: black;
    color: goldenrod;">Clear Filters</a>
                                            @else
                                                
     <a href="{{url('physical')}}" class="btn     btn-new-secondary float-right" style="">Clear Filters</a>
                                       @endif
                        </div>
                    </div>
               
                </div>
            </div>
        </div>
            </form>
 
      

        <form class="mb-0 pb-0" action="{{url('end-contract')}}" method="post" >
            @csrf
<div class="modal fade" id="EndModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">End Contract</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
                   
          <input type="hidden" name="id" >
   
   <div class="row">
    <div class="col-sm-12">
          <textarea class="form-control" rows="5" required="" name="reason" id="reason"></textarea>
      </div>
   </div>

                        </div>
                        <div class="block-content block-content-full   pt-4" style="padding-left: 9mm;padding-right: 9mm">
                            <button type="submit" class="btn mr-3 btn-new"     >End Contract</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>
                       
                        </div>
                    </div>
               
                </div>
            </div>
        </div>
     

</form>


     
       <form class="mb-0 pb-0" id="exportform" action="{{url('export-excel-physical')}}?{{$filter}}" method="get" >
            
<div class="modal fade" id="ExportModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal- -centered  modal-md modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Export Physical Asset</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
   <div class="row">
    <div class="col-sm-12">
        <label>Fields to Export</label>
          <select class="form-control selectpicker" data-style="btn-outline-light border columns text-dark"  id="columns" data-actions-box="true"  data-live-search="true"    data- multiple="" required="" name="columns[]" >
              <option value="1">Status</option>
              <option value="2">Asset Type</option>
              <option value="3">Client</option>
              <option value="4">Site</option>
              <option value="5">Location</option>
              <option value="6">Hostname</option>
              <option value="7">Domain</option>
              <option value="8">FQDN</option>
              <option value="9">Parent Asset</option>
              <option value="10">Role/Description</option>
              <option value="11">O/S</option>
              <option value="12">Environment</option> 
              <option value="13"> D/R Plan  </option>
              <option value="14">Clustered  </option>
              <option value="15">Internet Facing </option>
              <option value="16">Load Balanced  </option>
              <option value="17">Manufacturer </option>
              <option value="18">Model  </option>
                  <option value="19">Type  </option>
              <option value="20"> SN#</option>
              <option value="21">VLAN ID</option>
              <option value="22">Primary IP</option>
              <option value="23">Line: Additional IP Label</option> 
              <option value="24">Line: Additional IP Value</option>
              <option value="25">CPU </option>
              <option value="26">Memory </option>
              <option value="27">SSL Certificate </option>
              <option value="28">Support </option>
              <option value="29">Managed </option>
              <option value="30">App Owner</option>
              <option value="31">SLA</option>
              <option value="32">Patched</option>
             <option value="33">Monitored</option>
                <option value="34">Backup</option>
                <option value="35">Anti-Virus</option>
              <option value="36">Replicated</option>
                   <option value="37">Vulnerability Scan</option>
                <option value="38">SIEM</option>
         <option value="39">SMTP</option>
          </select>
      </div>
   </div>

                        </div>
                        <div class="block-content block-content-full   pt-4" style="padding-left: 9mm;padding-right: 9mm">
                            <button type="button" class="btn mr-3 btn-new "  id="btnExport"    >Export</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>
                       
                        </div>
                    </div>
               
                </div>
            </div>
        </div>
     

</form>



            </main>
            <!-- END Main Container -->
            @endsection('content')

 

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
      <script src="{{asset('public/dashboard_assets/js/dashmix.app.min.js')}}"></script>
     
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
$(function(){
   @if(Session::has('success'))
              
             Dashmix.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: '{{Session::get('success')}}'});


             @endif
showData('{{@$GETID}}');
function showData(id){
    $('.c-active').removeClass('c-active');
  if(id){
        $('.viewContent[data='+id+']').addClass('c-active');
                  $('.c4').css({'backgroundColor':'#D9D9D9','color':'#7F7F7F','borderColor':'#7F7F7F'})
        $('.c4[data='+id+']').css({'backgroundColor':'#97C0FF','color':'#595959','borderColor':'#595959'})
         }
                $.ajax({
            type:'get',
            data:{id:id},
            url:'{{url('get-physical-content')}}',
                dataType:'json',
    beforeSend() {
                      Dashmix.layout('header_loader_on');
                    
                    },
     
        success:function(res){
         
             Dashmix.layout('header_loader_off');   
                        $('#showData').html(res);

$('[data-toggle=tooltip]').tooltip();
            }
        })
}
 $('.ActionIcon').mouseover(function() {
var data=$(this).attr('data-src');
$(this).find('img').attr('src',data);
})
$('.ActionIcon').mouseout(function() {
  var data=$(this).attr('data-original-src');
$(this).find('img').attr('src',data);  
})
function updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}

$(document).on('click','.viewContent',function() {
var id=$(this).attr('data');
  var oldURL = window.location.href;
            var type = id;

            if (history.pushState) {
     
var newUrl=updateQueryStringParameter(oldURL,'id',id)
                window.history.pushState({ path: newUrl }, '', newUrl);
            }


showData(id);



})   

          
$(document).on('click','.btnEnd',function(){
    var id=$(this).attr('data-id');
    var data=$(this).attr('data');
    if(data==1){
        $('.reactivateHeader').html('Decomissioned Asset');
    }
    else{
        $('.reactivateHeader').html('Reactivate Asset');
    }
    $('input[name=id]').val(id);
$('#EndModal').modal('show')
})


        var page_type="{{$page_type}}";
 

  
$('select[name=limit]').change(function(){
    var form=$('#limit_form');
   if (form.attr("action") === undefined){
        throw "form does not have action attribute"
    }


    let url = form.attr("action");
    if (url.includes("?") === false) return false;
 
    let index = url.indexOf("?");
    let action = url.slice(0, index)
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}
    form.attr("action", action)

    form.submit();
})

 

   $('#form-search').submit(function(e){
    e.preventDefault();
    })
  $('input[name=search]').keyup(function(e){ 
    
var val=$(this).val();
    if(e.which==13){
     var form=$('#form-search');

   let url = form.attr("action");
        url+='&search='+val;
  window.location.href=url
}
  })
$('.changeSelect').each(function(i,e){
 
 
    var val=$(this).val()
   
    if($(this).prop('checked')){
    
 $('td[data-index='+val+']').removeClass('d-none')
              $('th[data-index='+val+']').removeClass('d-none')

}
else{
   
     $('td[data-index='+val+']').addClass('d-none')
              $('th[data-index='+val+']').addClass('d-none')
}


if(locArray.length==0){
 
 $('td[data-index='+val+']').removeClass('d-none')
              $('th[data-index='+val+']').removeClass('d-none')
}
});

@if(!isset($_GET['search']) && !isset($_GET['advance_search']) && !isset($_GET['field']) )


         @if(Auth::user()->role=='admin')    
 // $("#showdata").sortable({
 //            delay: 150,
 //            update: function() {
 //                var selectedData = new Array();
 //                var position = new Array();
 //                $('#showdata  > tr').each(function() {
 //                    selectedData.push($(this).attr("data"));
 //                    position.push($(this).attr("data-pos"));
 //                });


                    
 //                 $.ajax({
            
 //                type:'get',
 //                data:{id:selectedData,position:position,page:'{{@$_GET['page']}}',limit:'{{@$_GET['limit']}}'},
 //                    url:"{{url('swap-physical-rows')}}",
 //                async:false,
 //                success:function(data){
                    
 //                }
 //            })

 //            }

 //        });


@endif
@endif


$(document).on('click','#btnExport',function(){
        var col=$('#columns').val();
       
        if(col!=''){
    var form=$('#exportform');
   if (form.attr("action") === undefined){
        throw "form does not have action attribute"
    }


    let url = form.attr("action");
var action='';
    if (url.includes("?") === false) {
   let index = url.indexOf("?"); 
        action = url 
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}


        }
 else{

    let index = url.indexOf("?");
      action = url.slice(0, index)
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}
}
    form.attr("action", action)

    form.submit();
        $('#ExportModal').modal('hide')
     Dashmix.helpers('notify', {align: 'center', message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Export Complete.  ', delay: 5000}); 
     }
     else{

     }
})



$('.changeSelect').change(function(){


 var array=[];
        $('.changeSelect:checked').each(function(){
                array.push($(this).val());
        })
        console.log(array);
    $('td[data-index],th[data-index]').addClass('d-none')
    
    for(var i=0;i<array.length;i++)
    {
            $('td[data-index='+array[i]+']').removeClass('d-none')
              $('th[data-index='+array[i]+']').removeClass('d-none')
    }
     

$.ajax({
    type:'get',
    data:{array:array,type:page_type},
    url:"{{url('change-physical-asset-columns')}}",
    success:function(res){
        console.log(res)
    }
    ,error:function(e) {
        console.log(e)
    }
})


})

             
               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                       var cert=$(this).attr('data1');
                    var certedate=$(this).attr('data2');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-assets')}}',
                    success:function(res){

 
                        $('#viewData').modal('show');
                            $('#firstname').html(res.firstname)
                            $('#site_name').html(res.site_name)
                                               var managed=res.managed==1?'<div class="badge   badge-success" style="color:white!important;">Managed</div>':'';     
                               $('#hostnameDisplay').html('<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover" src="{{asset('public/asset_icon/')}}/'+res.asset_icon+'" alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><b>'+res.hostname+'</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">'+res.role+'</span></p></div></div>')

                            $('#clientLogo').html('<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{asset('public/client_logos/')}}/'+res.logo+'" alt="">');


                            $('#domain_nameDisplay').html(res.domain_name)
                                    
                                  $('#fqdnDisplay').html(res.fqdn)
                            
                            $('#roleDisplay').html(res.role)


                             $('#manufactureDisplay').html(res.vendor_name)
                              $('#modelDisplay').html(res.model)
                               $('#typeDisplay').html(res.type)
                                $('#snDisplay').html(res.sn)
                                 $('#cpu_modelDisplay').html(res.cpu_sockets+'x '+res.cpu_model+' '+res.cpu_cores+'C @ '+res.cpu_freq+'GHz')
                               
                                        $('#created_at').html(res.created_at)
                               $('#created_by').html(res.created_by!=null?res.created_firstname+' '+res.created_lastname:'')
                                  $('#updated_by').html(res.updated_by!=null?res.updated_firstname+' '+res.updated_lastname:'')
                               $('#updated_at').html(res.updated_at)
                                      $('#cpu_total_coresDisplay').html(res.cpu_total_cores)
                            

        if(res.HasWarranty==1){
                                                        $('.supportHide').removeClass('d-none')
                                                    }
                                                    else{
                                                        $('.supportHide').addClass('d-none')   
                                                    }
                            $('#use_Display').html(res.use_)
                            $('#operating_system_nameDisplay').html(res.operating_system_name)
                            $('#app_ownerDisplay').html(res.app_owner)
                            $('#ip_addressDisplay').html(res.ip_address)
                            $('#vlan_idDisplay').html(res.vlanId)
                              $('#asset_typeDisplay').html(res.asset_type_name);
                            $('#app_ownerDisplay').html(res.app_owner)
                     var network_zone=res.network_zone;
                             if(res.network_zone=='Internal'){
                                                           network_zone='<div class="badge badge-secondary"  >'+res.network_zone+'</div>';
                                            }
                                            else if(res.network_zone=='Secure'){
                                             network_zone='<div class="badge badge-info"  >'+res.network_zone+'</div>';
                                         }
                                                else if(res.network_zone=='Greenzone'){
                                                network_zone='<div class="badge badge-success"  >'+res.network_zone+'</div>';
                                          }
                                                else if(res.network_zone=='Guest'){
                                                network_zone='<div class="badge badge-warning"  >'+res.network_zone+'</div>';
                                                } else if(res.network_zone=='Semi-Trusted'){
                                                network_zone='<div class="badge  " style="background:#FFFF11;color: black"  >'+res.network_zone+'</div>';;
                                                } else if(res.network_zone=='Public DMZ' || res.network_zone=='Public' || res.network_zone=='Servers Public DMZ' ){
                                                network_zone='<div class="badge badge-danger"  >'+res.network_zone+'</div>';
                                                }


                            $('#network_zoneDisplay').html(network_zone)

                            $('#SupportStatusDisplay').html(res.SupportStatus)
                            $('#InactiveDateDisplay').html(res.InactiveDate)
                            
    
                             $('#managedDisplay').html(res.managed=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>') 
                            if(res.asset_type_name=='Physical Server'){
                                    $('.cpuDiv').removeClass('d-none')
                            }
                            else{
                                 $('.cpuDiv').addClass('d-none')   
                            }

                         $('#HasWarrantyDisplay').html(res.HasWarranty=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>')
                          $('#AssetStatusDisplay').html(res.AssetStatus=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>')

                             if(res.internet_facing==2){
                                    $('#internet_facingDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.internet_facing==1){
                                 $('#internet_facingDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#internet_facingDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                           
                           if(res.disaster_recovery==2  || res.managed!=1){
                                    $('#disaster_recoveryDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.disaster_recovery==1){
                                 $('#disaster_recoveryDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#disaster_recoveryDisplay').html('<div class="badge badge-danger">No</div>');
                            }


                           if(res.load_balancing==2 || res.managed!=1){
                                    $('#load_balancingDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.load_balancing==1){
                                 $('#load_balancingDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#load_balancingDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                            if(res.clustered==2 || res.managed!=1){
                                    $('#clusteredDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.clustered==1){
                                 $('#clusteredDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#clusteredDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                       if(res.monitored==2 || res.managed!=1){
                                    $('#monitoredDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.monitored==1){
                                 $('#monitoredDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#monitoredDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                            

                            if(res.patched==2 || res.managed!=1){
                                    $('#patchedDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.patched==1){
                                 $('#patchedDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#patchedDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                              if(res.antivirus==2 || res.managed!=1){
                                    $('#antivirusDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.antivirus==1){
                                 $('#antivirusDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#antivirusDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                      
                               if(res.backup==2 || res.managed!=1){
                                    $('#backupDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.backup==1){
                                 $('#backupDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#backupDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                      
                              
                         if(res.replicated==2 || res.managed!=1){
                                    $('#replicatedDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.replicated==1){
                                 $('#replicatedDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#replicatedDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                             

                             if(res.smtp==2 || res.managed!=1){
                                    $('#smtpDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.smtp==1){
                                 $('#smtpDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#smtpDisplay').html('<div class="badge badge-danger">No</div>');
                            }

                              if(res.ntp==2 || res.managed!=1){
                                    $('#ntpDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.ntp==1){
                                 $('#ntpDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#ntpDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                             
                              if(res.syslog==2 || res.managed!=1){
                                    $('#syslogDisplay').html('<div class="badge badge-secondary">N/A</div>');
                            }
                            else if(res.syslog==1){
                                 $('#syslogDisplay').html('<div class="badge badge-success">Yes</div>');
                            }
                            else{
                                  $('#syslogDisplay').html('<div class="badge badge-danger">No</div>');
                            }
                    
                     
  $('#cpu_hyperthreadingsDisplay').html(res.cpu_hyperthreadings=='1'?'<div class="badge badge-success">Yes</div>':'<div class="badge badge-danger">No</div>')


                                                    if(res.SupportStatus=='N/A'){
                                                     $('#SupportStatusDisplay').html('<span class="badge text-white bg-secondary">'+res.SupportStatus+'</span>');
                                                        }
                                                        else if(res.SupportStatus=='Supported'){
                                                        $('#SupportStatusDisplay').html('<span class="badge badge-success">'+res.SupportStatus+'</span>');
                                                                 
                                                         }

                                                        else if(res.SupportStatus=='Unassigned'){
                                                        $('#SupportStatusDisplay').html('<span class="badge text-white bg-orange">'+res.SupportStatus+'</span>');
                                                         }else if(res.SupportStatus=='Expired'){
                                                            $('#SupportStatusDisplay').html('<span class="badge badge-danger">'+res.SupportStatus+'</span>');
                                                        }
                                                        else{
                                                          $('#SupportStatusDisplay').html('<span class="badge text-white bg-secondary">N/A</span>');  
                                                        }

                                                                if(res.ssl_certificate_status=='N/A'){
                                                     $('#ssl_cert_statusDisplay').html('<span class="badge text-white bg-secondary">'+res.ssl_certificate_status+'</span>');
                                                        }
                                                        else if(res.ssl_certificate_status=='Active'){
                                                        $('#ssl_cert_statusDisplay').html('<span class="badge badge-success">'+res.ssl_certificate_status+'</span>');
                                                                 
                                                         }

                                                        else if(res.ssl_certificate_status=='Unassigned'){
                                                        $('#ssl_cert_statusDisplay').html('<span class="badge text-white bg-orange">'+res.ssl_certificate_status+'</span>');
                                                         }else if(res.ssl_certificate_status=='Expired'){
                                                            $('#ssl_cert_statusDisplay').html('<span class="badge badge-danger">'+res.ssl_certificate_status+'</span>');
                                                        }
                                                           else{
                                                          $('#ssl_cert_statusDisplay').html('<span class="badge text-white bg-secondary">N/A</span>');  
                                                        }



                                                             if(res.NotSupportedReason=='N/A'){
                                                     $('#NotSupportedReasonDisplay').html('<span class="badge text-white bg-secondary">'+res.NotSupportedReason+'</span>');
                                                        }
                                                        else if(res.NotSupportedReason=='Other Partner'){
                                                        $('#NotSupportedReasonDisplay').html('<span class="badge badge-warning">'+res.NotSupportedReason+'</span>');
                                                                 
                                                         }

                                                        else if(res.NotSupportedReason=='End Of Life'){
                                                        $('#NotSupportedReasonDisplay').html('<span class="badge text-white bg-orange">'+res.NotSupportedReason+'</span>');
                                                         }else if(res.NotSupportedReason=='Forgone'){
                                                            $('#NotSupportedReasonDisplay').html('<span class="badge badge-danger">'+res.NotSupportedReason+'</span>');
                                                        }
                                                           else{
                                                          $('#NotSupportedReasonDisplay').html('<span class="badge text-white bg-secondary">N/A</span>');  
                                                        }




                                $('#ssl_cert_issuerDisplay').html(cert)
                                $('#ssl_cert_edateDisplay').html(certedate)


                                  $('#slaDisplay').html(res.sla)
                                       
                                              $('#memoryDisplay').html(res.memory)
                                                    $('#commentsDisplay').html(res.comments)
             
            
                                                  if(res.ntp==1){
                                                    $('.sslDiv').removeClass('d-none')
                                              }
                                              else{
                                               $('.sslDiv').addClass('d-none') 
                                              }

                                                   if(res.HasWarranty==1){
                                                    $('.contractsDiv').removeClass('d-none')
                                              }
                                              else{
                                               $('.contractsDiv').addClass('d-none') 
                                              }
                                              if(res.managed==1){
                                                    $('.ManagedDiv').removeClass('d-none')
                                              }
                                              else{
                                               $('.ManagedDiv').addClass('d-none') 
                                              }

                                                 
             

                
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-contract-asset')}}',
                    success:function(res){
                        var html='';
                        if(res.length>0){
                             const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];

   var MyDate=new Date('<?php echo date('m/d/Y') ?>');
  



                            for(var i=0;i<res.length;i++){
                                 var end_dateObject=new Date(res[i].contract_end_date);
                                 var contract_end_date=end_dateObject.getFullYear()+'-'+monthNames[end_dateObject.getMonth()]+'-'+end_dateObject.getDate();

                  
                                 var expiry_dateObj=new Date(res[i].contract_end_date);
                                 var status='';

                                    const diffTime = Math.abs(expiry_dateObj - MyDate);
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                                     
                                           if(res[i].contract_status=='Active'){

              if(diffDays<=30 ){
                                                        status='<div class="badge badge-warning">Upcoming</div>';                                                                

                                                        }else{
                                                        status='<div class="badge badge-success">'+res[i].contract_status+' </div>';
                                                        }

 
                                                            }
                                                else if(res[i].contract_status=='Inactive'){
                                                status='<div class="badge  " style="background:lightblue;"  >Renewed</div>';
                                               
                                                }else if(res[i].contract_status=='Expired/Ended'){
                                                status='<div class="badge  " style="background:orange;"  >Ended</div>';
                                               }
                                                else{
                                                    status='<div class="badge badge-danger">'+res[i].contract_status+'</div>'
                                                }


                            html+=`<tr>
                                    <td>${status}</td>
                                    <td>${res[i].contract_type}</td>
                                    <td>${res[i].vendor_name}</td>
                                    <td>${res[i].contract_no}</td>
                                    <td>${contract_end_date}</td>
                                    <td>${res[i].contract_description}</td>

                            </tr>`;
                            $('#showContracts').html(html)
                            }
                        }
                    else{
                             $('#showContracts').html('<tr><td class="text-center" colspan=6><b>Unassigned</b></td></tr>')
                }

 
              
                 

                       
                    }
                })


  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-ssl-asset')}}',
                    success:function(res){
                        var html='';
                        if(res.length>0){
                             const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];

   var MyDate=new Date('<?php echo date('m/d/Y') ?>');
  



                            for(var i=0;i<res.length;i++){
                                 var end_dateObject=new Date(res[i].cert_edate);
                                 var contract_end_date=end_dateObject.getFullYear()+'-'+monthNames[end_dateObject.getMonth()]+'-'+end_dateObject.getDate();

                  
                                 var expiry_dateObj=new Date(res[i].cert_edate);
                                 var status='';

                                    const diffTime = Math.abs(expiry_dateObj - MyDate);
                                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                                     
                                           if(res[i].cert_status=='Active'){

              if(diffDays<=30 ){
                                                        status='<div class="badge badge-warning">Upcoming</div>';                                                                

                                                        }else{
                                                        status='<div class="badge badge-success">'+res[i].cert_status+' </div>';
                                                        }

 
                                                            }
                                                else if(res[i].cert_status=='Inactive'){
                                                status='<div class="badge  " style="background:lightblue;"  >Renewed</div>';
                                               
                                                }else if(res[i].cert_status=='Expired/Ended'){
                                                status='<div class="badge  " style="background:orange;"  >Ended</div>';
                                               }
                                                else{
                                                    status='<div class="badge badge-danger">'+res[i].cert_status+'</div>'
                                                }


                            html+=`<tr>
                                    <td>${status}</td>
                                    <td class="text-capitalize">${res[i].cert_type}</td>
                                    <td>${res[i].vendor_name==null?'':res[i].vendor_name}</td>
                                     
                                    <td>${contract_end_date}</td>
                                    <td>${res[i].cert_name}</td>

                            </tr>`;
                            $('#showSSL').html(html)
                            }
                        }
                    else{
                             $('#showSSL').html('<tr><td class="text-center" colspan=6><b>Unassigned</b></td></tr>')
                }

 
              
                 

                       
                    }
                })
  if(res.AssetStatus==1){
                            $('.inactveShow').addClass('d-none')
                }   
                else{
                            $('.inactveShow').removeClass('d-none')
                }      
                        




      $('.printDiv').attr('href','{{url('print-asset')}}?id='+id)
                                $('.pdfDiv').attr('href','{{url('pdf-asset')}}?id='+id)



                
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-contract-asset')}}',
                    success:function(res){
                    if(res==''){
                           $('.contractDisplay').addClass('d-none');   
                


                }
                else{
                     $('.contractDisplay').removeClass('d-none');
                        $('#ContractStartDateDisplay').html(res.contract_end_date)
                        $('#ContractNoDisplay').html('<a  target="_blank" href="{{url("print-contract")}}?id='+res.id+'">'+res.contract_no+'</a>')
                }
              
                 

                       
                    }
                })


 $('.ip').remove()
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-asset-ip')}}',
                    success:function(res){
                            var html='';
                            for(var i=0;i<res.length;i++){
                                html+='<tr class="ip"><td><b>'+res[i].ip_address_name+'</b></td><td>'+res[i].ip_address_value+'</td></tr>';
                            }
                           $('#networkDiv').after(html);   
                
         
                    }
                })







                    }
                })

               })

@if(isset($_GET['advance_search']) && $_GET['client_id']!='')

run('{{$_GET['client_id']}}','on')
var site_id='<?php echo isset($_GET['site_id'])?implode(',',$_GET['site_id']):''?>';
 
getVendor('{{@$_GET['client_id']}}',site_id.split(','),'on')
@endif


function getVendor(client_id,site_id,on){
      $.ajax({
        type:'get',
        data:{client_id:client_id,site_id:site_id},
        url:'{{url('getVendorOfPhysical')}}',
         async:false,
        success:function(res){
            var html='';
          var check='<?php echo @$_GET['manufacturer']?implode(',',$_GET['manufacturer']):''   ?>';;
                        check=check.split(',');
            for(var i=0;i<res.length;i++){
                if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].vendor_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].vendor_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].vendor_name+'</option>';
                }
            } 

            $('#manufacturer').html(html);
            $('#manufacturer').selectpicker('refresh');
        }
    })
}
 
$('#site_id').change(function(){
    var site_id=$(this).val();
    var client_id=$('#client_id').val()
   
    getVendor(client_id,site_id)
})




function run(id,on){ 
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getSiteByClientId')}}',
        success:function(res){
            var html='';
                    var check='<?php echo @$_GET['site_id']?implode(',',$_GET['site_id']):''   ?>';;
                           check=check.split(',');
            for(var i=0;i<res.length;i++){
                if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].site_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
                }
            } 

            $('#site_id').html(html);
            $('#site_id').selectpicker('refresh');
        }
    })
       $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getDomainByClientId')}}',
        success:function(res){
            var html='';

               var check='{{@$domain}}';
                        check=check.split(',');
          
            for(var i=0;i<res.length;i++){
                    if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].domain_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].domain_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].domain_name+'</option>';
            }
        }
         
            $('#domain').html(html);
              $('#domain').selectpicker('refresh');
        }
    })
   }
$('#site_id').change(function(){
    var id=$(this).val()
    var client_id=$('#client_id').val();
    $.ajax({
        type:'get',
        data:{site_id:id,client_id:client_id},
        url:'{{url('getVlanIdAll')}}',
        success:function(res){
            var html='';
             html+='<option value>Select Vlan Id</option>';
            for(var i=0;i<res.length;i++){
                html+='<option value="'+res[i].id+'"   >'+res[i].vlan_id+'</option>';
            }
            $('#network_zone').val('');
            $('#ip_address').val('');
            $('#vlan_id').select2('destroy');
            $('#vlan_id').html(html);
$('#vlanInfo').addClass('d-none')
            $('#vlan_id').select2();
        }
    })
       
})

$('#client_id').change(function(){
    var id=$(this).val()

    run(id)
       getVendor(id); 
})



               $(document).on('click','.btnDelete',function(){
                    var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this Assets");
                    if(c){
                        window.location.href="{{url('delete-physical-assets')}}?id="+id;
                    }
                            })  
           })
</script>
