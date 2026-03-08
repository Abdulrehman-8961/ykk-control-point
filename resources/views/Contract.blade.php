  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')


<?php 

$userAccess=explode(',',@Auth::user()->access_to_client);



$limit=10;
 

        $no_check=DB::Table('settings')->where('user_id',Auth::id())->first();
if(isset($_GET['limit']) && $_GET['limit']!=''){
    $limit=$_GET['limit'];

        if($no_check!=''){
            DB::table('settings')->where('user_id',Auth::id())->update(['contract'=>$limit]);
        }
        else{
            DB::table('settings')->insert(['user_id'=>Auth::id(),'contract'=>$limit]);
        }
        
}
else{
           
        if($no_check!=''){
            if($no_check->contract!=''){
            $limit=$no_check->contract;

        }
        }
}





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
if(@Auth::user()->role=='admin'){
  
// - Comments
// - Line Asset
// - Line Description
// - Line PN#


$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond") ->where(function($query) use($sear){

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
 
 
     }) ->where('a.is_deleted',0) ->orderBy($field,$orderby) ->paginate($limit); 

}
else{
    $qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond") ->where(function($query) use($sear){
      
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
 
 
     })   ->where('a.is_deleted',0) ->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 
    
}
 
}
else{

    

if(Auth::user()->role=='admin'){
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')  ->where('a.is_deleted',0) ->orderBy('a.id','desc') ->paginate($limit); 
 }
 else{
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name','v.vendor_image') ->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')  ->where('a.is_deleted',0) ->whereIn('a.client_id',$userAccess)->orderBy('a.id','desc') ->paginate($limit); 
    
 }

  
 }


if(isset($_GET['id']) || isset($id)){
$GETID=$_GET['id'] ?? $id;
}
else{
$GETID=@$qry[0]->id;
}
 ?>     


<style type="text/css">
    #page-header{
        display: none;
    }
</style>










   <!-- Main Container -->
            <main id="main-container pt-0">
                <!-- Hero -->
           
<style type="text/css">
         .dropdown-menu {
        z-index: 100000!important;
    }
    .pagination{
        margin-bottom: 0px;
    }
.ActionIcon{

    border-radius: 50%;
    padding: 6px;
}
.ActionIcon:hover{

 background: #dadada;
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
 
.contract_type_button label,
.contract_type_button input {
  
 
}
.contract_type_button{
    float: left;
}
.contract_type_button input[type="radio"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.contract_type_button input[type="radio"]:checked + label {
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
.contract_type_button label,
.contract_type_button input {
  
 
}
.contract_type_button{
    float: left;
}
.contract_type_button input[type="radio"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.contract_type_button input[type="radio"]:checked + label {
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
    color:#E54643 ;
      letter-spacing:0px ;

 
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
</style>
                      <div class="con   no-print page-header py-2" id="">
                    <!-- Full Table -->
                    <div class="b   mb-0  ">
                    
                        <div class="block-content pt-0 mt-0">

<div class="TopArea" style="position: sticky; 
    padding-top: 8px;
    z-index: 1000;
    
    padding-bottom: 5px;">
    <div class="row" >

                  <?php
               


 
  
  

 $filter=(isset($_GET['advance_search'])?'advance_search='.$_GET['advance_search']:'').(isset($_GET['client_id'])?'&client_id='.$_GET['client_id']:'').(isset($_GET['site_id'])?'&'.http_build_query(array('site_id'=>$_GET['site_id'])):'') .(isset($_GET['distributor_id'])?'&'.http_build_query(array('distributor_id'=>$_GET['distributor_id'])):'') .(isset($_GET['contract_status'])?'&contract_status='.$_GET['contract_status']:'').(isset($_GET['estimate_no'])?'&estimate_no='.$_GET['estimate_no']:'').(isset($_GET['sales_order_no'])?'&sales_order_no='.$_GET['sales_order_no']:'').(isset($_GET['vendor_id'])?'&'.http_build_query(array('vendor_id'=>$_GET['vendor_id'])):'').(isset($_GET['invoice_no'])?'&invoice_no='.$_GET['invoice_no']:'').(isset($_GET['invoice_date'])?'&invoice_date='.$_GET['invoice_date']:'').(isset($_GET['po_no'])?'&po_no='.$_GET['po_no']:'').(isset($_GET['po_date'])?'&po_date='.$_GET['po_date']:'').(isset($_GET['reference_no'])?'&reference_no='.$_GET['reference_no']:'').(isset($_GET['distrubutor_sales_order_no'])?'&distrubutor_sales_order_no='.$_GET['distrubutor_sales_order_no']:'').(isset($_GET['contract_no'])?'&contract_no='.$_GET['contract_no']:'').(isset($_GET['contract_start_date'])?'&contract_start_date='.$_GET['contract_start_date']:'').(isset($_GET['has_attachment'])?'&has_attachment='.$_GET['has_attachment']:'').(isset($_GET['daterange'])?'&daterange='.$_GET['daterange']:'').(isset($_GET['renewal_within'])?'&renewal_within='.$_GET['renewal_within']:'').(isset($_GET['contract_end_date'])?'&contract_end_date='.$_GET['contract_end_date']:'').(isset($_GET['contract_description'])?'&contract_description='.$_GET['contract_description']:'').(isset($_GET['comments'])?'&comments='.$_GET['comments']:'').(isset($_GET['limit'])?'&limit='.$_GET['limit']:'').(isset($_GET['id'])?'&id='.$_GET['id']:'');

?>     

        <div class="col-sm-3">
@Auth
 <form class="push mb-0"   method="get" id="form-search" action="{{url('contract/')}}/{{$type}}?{{$filter}}">
                                        
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control searchNew" name="search" placeholder="Search Contracts">
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
@endif
</div>
<div class="col-sm-3"  style="margin-top: 2px;">
     @Auth
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
                    
                              @if(@Auth::user()->role!='read') 
                         <a class="btn btn-dual d2    "   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Contract"   href="{{url('add-contract')}}/{{$type==''?'support':$type}}">
                           <img src="{{asset('public/img/ui-icon-add.png')}}"  width="19px" height="19px">
                        </a>
                        @endif
                        @endif
</div>

<div class="   col-lg-3   " >
@Auth
          {{$qry->appends($_GET)->onEachSide(0)->links()}}
                       </div><div class="d-flex text-right col-lg-3 justify-content-end" ><form  id="limit_form" class="ml-2 mb-0" action="{{url('contract/')}}/{{$type}}?{{$_SERVER['QUERY_STRING']}}">
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

                            @endif
                        <!-- User Dropdown -->
                        <div class="dropdown d-inline-block">
                            <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  >
           @if(@Auth::user()->user_image!='')
              <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public/client_logos/')}}/{{Auth::user()->user_image}}"  alt="">
                             
                                @else
                                  <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public')}}/dashboard_assets/media/avatars/avatar2.jpg"  alt="">
                                
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
        @if(!isset($hash))
         <div class="col-lg-4   LeftDi no-print "  style="overflow-y: auto;height: 90vh;">
                @foreach($qry as $q)
<div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="{{$q->id}}" style="cursor:pointer;">
                    
                 
                        <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">
                                    

                                         <div class="mr-1     justify-content-center align-items-center  d-flex" style="width:20%;padding:7px">
                                            <img src="{{asset('/public')}}/vendor_logos/{{$q->vendor_image}}"  class="rounded-circle  "  width="100%" style=" object-fit: cover;">
                                        </div>


                                     <div class="  " style="width:55%">
                                             <p class="font-12pt mb-0 text-truncate font-w600 c1">{{$q->firstname}}</p>

                                               <div class="d-flex" >
                                                                    @if($q->contract_type=='Hardware Support')
                                                                    <p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data="{{$q->id}}" data-toggle="tooltip" data-title="Hardware Support">H</p>
                                                                    
                                                                    @elseif($q->contract_type=='Software Support')
                                                                    <p class="font-11pt mr-1   mb-0  c4-s  "  style="max-width:12%; " data="{{$q->id}}" data-toggle="tooltip" data-title="Software Support">S</p>
                                                                     @else

                                                                     <p class="font-11pt mr-1   mb-0   c4-v  "  data-toggle="tooltip" data-title="Subscription" style="max-width:12%; " data="{{$q->id}}">C</p>
                                                                    @endif
                                                                         <p class="font-12pt mb-0 text-truncate   c4" style="max-width:90%;min-width: 90%"  data="{{$q->id}}">{{$q->contract_no}}</p></div>

                              
                                                    <p class="font-12pt mb-0 text-truncate c2"  style="width: 100%">{{$q->contract_description}}</p> 
                                        </div>
                                        <div class=" text-right" style="width:25%;;">
                                                                            <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                               

                                                            
                                                             
                                                                       <?php $contract_end_date=date('Y-M-d',strtotime($q->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($q->contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
                  ?>
 


                    @if($q->contract_status=='Active')

                                    @if($abs_diff<=30)
                             <div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold   text-dark"  >
                                                                                <span class=" ">Upcoming</span>
                                                                    </div> 
                                    @else
                                           <div class=" bg-new-green ml-auto  badge-new  text-center font-weight-bold   text-white"  >
                                                                                 <span class=" ">Active</span>
                                                                    </div>  
                                                                 

                                @endif
                                @elseif($q->contract_status=='Inactive')
                                   
                                                                      <div class=" bg-new-blue ml-auto  badge-new  font-weight-bold    text-center  font-w600 text-white"  >
                                                                                  <span class=" ">Renewed</span>
                                                                    </div>  

                                @elseif($q->contract_status=='Expired/Ended')
                                 
                                                                                 <div class=" bg-new-red ml-auto  font-weight-bold    badge-new  text-center  font-w600 text-white"  >
                                                                                  <span class=" ">Ended</span>
                                                                    </div>
                                @elseif($q->contract_status=='Ended')
                                       <div class=" bg-new-red ml-auto  badge-new  text-center  font-weight-bold   0 text-white"  >
                                                                                  <span class=" ">Revoked</span>
                                                                    </div>
                                @elseif($q->contract_status=='Expired')
                                      <div class=" bg-new-red ml-auto  badge-new  text-center   text-white"  >
                                                                                  <span class=" ">Expired</span>
                                                                    </div>
                                @else

                                @endif

                                                                 <!--    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>{{$abs_diff}} days remaining</i></small></p>

                                                                    </div>
 -->

                                                                </div>
                                                                <?php $line_items=DB::Table('contract_assets as ca')->select('a.hostname','a.AssetStatus')->where('ca.contract_id',$q->id)->join('assets as a','a.id','=','ca.hostname')->groupBy('ca.hostname')->where('ca.is_deleted',0)->orderBy('a.hostname','asc')->get();
                                                                              $cvm='<b class="HostActive text-white">Assigned Assets</b><br>';
                                                                            if(count($line_items)>0){
                                                                            foreach($line_items as $l){
                                                                                if($l->AssetStatus!='1'){
                                                                                            $cvm.='<span class="HostInactive text-uppercase">'.$l->hostname.'</span><br>'; 
                                                                                            }
                                                                                            else{
                                                                                                        $cvm.='<span class="HostActive text-uppercase">'.$l->hostname.'</span><br>';    
                                                                                            }                                              }
                                                                                          }
                                                                                            else{
                                                                                                $cvm.='<span class="HostActive text-orange ">None</span><br>'; 
                                                                                            }     
                                                                 ?>
                                                                    <div  class="" style="position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                                                        

    <div class="ActionIcon"  data-src="{{asset('public')}}/img/calendar-grey-removebg-preview.png?cache=1" data-original-src="{{asset('public')}}/img/calendar-grey-removebg-preview.png?cache=1">
 <a href="javascript:;" class="toggle " data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="<p class='HostActive text-white  my-0'>Validity Range</p><p class='HostActive my-n1 text-orange  '>{{date('d-M-Y',strtotime($q->contract_start_date))}}-{{date('d-M-Y',strtotime($q->contract_end_date))}}</p><p class='font-10pt mb-0 text-grey text-truncate mt-0 '> <small><i>{{$abs_diff}} days remaining</i></small></p>" data-html="true" data-original-title="" >
                             <img  src="{{asset('public')}}/img/calendar-grey-removebg-preview.png?cache=1" width="24px"  class="   " >
                        </a>
                                                                    </div>


    <div class="ActionIcon"  data-src="{{asset('public')}}/img/icon-hosts-grey-darker.png?cache=1" data-original-src="{{asset('public')}}/img/icon-hosts-grey-darker.png?cache=1">
                                                                        <a href="javascript:;" class="toggle " data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="{{$cvm}}" data-html="true" data-original-title="{{$cvm}}" >
                                                                        <img  src="{{asset('public')}}/img/icon-hosts-grey-darker.png?cache=1" width="24px"  class="   " >
                                                                        </a>
                                                                    </div>
                                                                     <?php     if(Auth::check()){    
                                            if(@Auth::user()->role!='read'){ ?>
                                                                     <div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                         <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                                       
                                                                        <img src="{{asset('public')}}/img/dots.png?cache=1"   >
                                                                        </a>
                                         <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">
          <?php   
                                                      
                                                        if($q->contract_status!='Inactive' && $q->contract_status!='Expired/Ended'){?>
                  <a class="dropdown-item d-flex align-items-center px-0  "  data="" href="{{url('renew-contract')}}?id={{$q->id}}"  data-id="{{$q->id}}">     <div style="width: 32px;  padding-left: 5px"><img src="{{asset('public')}}/img/refresh_icon-removebg-preview.png?cache=1"  width="22px" ></div> Renew Contract</a>
                         <?php }
                                    if($q->contract_status!='Inactive' && $q->contract_status!='Expired/Ended'){
                                                    
                                                   ?>
   <a class="dropdown-item d-flex align-items-center px-0 btnEnd  "     data="{{$q->id}}" href="javascript:;"  data-id="{{$q->id}}">     <div style="width: 32px;  padding-left: 5px"><img src="{{asset('public')}}/img/endicon-removebg-preview.png?cache=1"  width="22px" ></div> End Contract</a>

                                                 <?php }else if($q->contract_status=='Expired/Ended'){  ?>
                                                  <a class="dropdown-item d-flex align-items-center px-0 btnEnd  "     data="{{$q->id}}" href="javascript:;"  data-id="{{$q->id}}" data-ended=1>     <div style="width: 32px;  padding-left: 5px"><img src="{{asset('public')}}/img/d3-reactivate.png?cache=1"  width="22px" ></div> Reinstate Contract</a>

                                                 <?php } ?>
                  <a class="dropdown-item d-flex align-items-center px-0" href="pdf-contract?id={{$q->id}}">   <div style="width: 32;  padding-left: 2px"><img src="{{asset('public')}}/img/dr-pdf.png?cache=1" width="26px"  > PDF</div></a>  
                  <a class="dropdown-item d-flex align-items-center  px-0" href="javascript:;" onclick="window.print()" ><div style="width: 32px ;padding-left: 5px "><img src="{{asset('public')}}/img/d3-print.png?cache=1" width="20px"  ></div> Print</a>
                  <a class="dropdown-item d-flex align-items-center  px-0"    href="{{url('edit-contract')}}?id={{$q->id}}" ><div style="width: 32px;  padding-left: 5px"><img src="{{asset('public')}}/img/d3-edit.png?cache=1" width="17px"  ></div> Edit</a>
                  <a class="dropdown-item d-flex align-items-center  px-0 btnDelete"  data="{{$q->id}}" href="javascript:void(0)"><div style="width: 32px;  padding-left: 5px"><img src="{{asset('public')}}/img/d3-delete.png?cache=1"  width="17px" ></div> Delete</a>
                </div>
                                                                   </div>
                                                                    <?php } }?>
                                                                  </div>
                                                         
                                        </div>    
                                </div>
                            </div>
                            @endforeach
                                                          </div>
                                                          @endif

               @if(!isset($hash))
               <div class="  col-lg-8  " id="showData"  style="overflow-y: auto;height:90vh;">
                @else
                  <div class="  col-lg-12  " id="showData"  style="overflow-y: auto;height:90vh;">
                @endif
        </div>








                             <div class="modal fade" id="viewData" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-lg " role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header  py-1" style="background:#4194F6">
                            <h3 class="block-title" id="hostnameDisplay">All Info</h3>
                            <div id="clientLogo" class="block-options">
                               
                            </div>
                        </div>
                        <div class="block-content" id="accordion2" role="tablist" aria-multiselectable="true">
                               <div class="block block-rounded blockDivs mb-1">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q1" aria-expanded="true" aria
                                                -controls="accordion2_q1"><img src="{{asset('public/img/contract.jpg')}}" width="20" class="mr-1" height="20"> Contract Info</a>
                                            </div>
                                            <div id="accordion2_q1" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content">
                                                       <table class="table tablemodal">
                   
                 
      
                               
           <tbody>
              <tr>
                                        <th>Contract Type</th>
                                        <td id="contract_typeDisplay"></td>
                                      
                                    </tr>
              <tr>
                                        <th>Site</th>
                                        <td id="site_name"></td>
                                      
                                    </tr>
                                
                                  
                                     <tr class="   ">
                                        <th>Contract Ended By</th>
                                        <td  id="ended_by" class="ContractEndDiv"></td>
                                       
                                    </tr>
                                      <tr>
                                        <th>Start Date/End Date</th>
                                        <td ><span id="contract_start_dateDisplay"></span> / <span id="contract_end_dateDisplay"></span></td>
 
                                    </tr>
                                    <tr class="   ">
                                        <th>Contract Ended On</th>
                                        <td id="ended_on" class="ContractEndDiv"></td>
                                    
                                    </tr>
                                       <tr class="   ">
                                        <th>Days Remaining</th>
                                        <td id="days_remaining"></td>
                                          
                                    </tr>

           
                                </tbody>
                            </table>
                                                </div>
                                            </div>
                                        </div>
    <div class="block block-rounded mb-1 blockDivs">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q2" aria-expanded="true" aria
                                                -controls="accordion2_q2"><img src="{{asset('public/img/contract-details.png')}}" width="20" class="mr-1" height="20"> Contract Details</a>
                                            </div>
                                            <div id="accordion2_q2" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content">
                                                     
                            <div id="assetdiv" class="table-responsive">
                            </div>
                        </div>
                    </div>
                </div>

                   <div class="block block-rounded blockDivs NetworkDiv mb-1">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q3" aria-expanded="true" aria
                                                -controls="accordion2_q3"><img src="{{asset('public/img/distribution.png')}}" width="20" class="mr-1" height="20"> Distribution</a>
                                            </div>
                                            <div id="accordion2_q3" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content">
                                                       <table class="table tablemodal">
                    
                               
           <tbody>
   <tr>
                                        <th>Distribution</th>
                                        <td id="distributor_nameDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                    <tr>
                                        <th>Reference #</th>
                                        <td id="reference_noDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                    <tr>
                                        <th>Sales Order #</th>
                                        <td id="sales_order_noDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                               
                              
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                                 <div class="block block-rounded ManagedDiv blockDivs mb-1">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q4" aria-expanded="true" aria
                                                -controls="accordion2_q4"><img src="{{asset('public/img/purchasing.png')}}" width="20" class="mr-1" height="20"> Purchasing</a>
                                            </div>
                                            <div id="accordion2_q4" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content">
                                                       <table class="table tablemodal">
                    
                               
           <tbody>
  <tr>
                                        <th>Estimate #</th>
                                        <td id="estimate_noDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                    <tr>
                                        <th>Sales #</th>
                                        <td id="sales_no_Display"></td>
                                        <td></td><td></td> 
                                    </tr>
                                    <tr>
                                        <th>Invoice #</th>
                                        <td id="invoice_noDisplay"></td>
                                         <th>Invoice Date</th>
                                        <td id="invoice_dateDisplay"></td>
                                    </tr>
                                 
                                    <tr>
                                        <th>PO #</th>
                                        <td id="po_noDisplay"></td>
                                           <th>PO Date</th>
                                        <td id="po_dateDisplay"></td>
                                    </tr>
                              
                                  
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                             
                                 
                             
 
                                  
                              
                      @if(@Auth::user()->role=='admin')
                                    <div class="block block-rounded commentsDiv blockDivs mb-1">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q5" aria-expanded="true" aria
                                                -controls="accordion2_q5"><img src="{{asset('public/img/comments.jpg')}}" width="20" class="mr-1" height="20"> Comments</a>
                                            </div>
                                            <div id="accordion2_q5" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content">
                                              
                                       
                                                 <div id="commentDisplay"></div>
                           
                              
                             
                        </div>
                    </div>
                </div>
@endif
    <div class="block block-rounded attachmentsDiv blockDivs mb-1">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q6" aria-expanded="true" aria
                                                -controls="accordion2_q6"><img src="{{asset('public/img/attachment.png')}}" width="20" class="mr-1" height="20"> Attachments</a>
                                            </div>
                                            <div id="accordion2_q6" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content py-4">
                                              
                                       
                                                 <div id="attachmentDisplay"></div>
                           
                              
                             
                        </div>
                    </div>
                </div>
                                     <div class="block block-rounded blockDivs mb-1">
                                            <div class="block-header block-header-default" role="tab" id="accordion2_h1">
                                                <a class="font-w600 text-secondary" data-toggle="collapse" data-parent="#accordion2" href="#accordion2_q8" aria-expanded="true" aria
                                                -controls="accordion2_q8"><img src="{{asset('public/img/audit.png')}}" width="20" class="mr-1" height="20"> Audit Trail</a>
                                            </div>
                                            <div id="accordion2_q8" class="collapse  " role="tabpanel" aria-labelledby="accordion2_h1">
                                                <div class="block-content">
                                                       <table class="table tablemodal">
                    
                               
           <tbody>

                                
                                         <tr>
                                        <th>Created By</th>
                                        <td id="created_by"></td>
                                        <td></td><td></td> 
                                    </tr>
                                      <tr>
                                        <th>Created On</th>
                                        <td id="created_at"></td>
                                        <td></td><td></td> 
                                    </tr>
                                        <tr>
                                        <th>Last Modified By</th>
                                        <td id="updated_by"></td>
                                        <td></td><td></td> 
                                    </tr>
                                        <tr>
                                        <th>Last Modified On</th>
                                        <td id="updated_at"></td>
                                        <td></td><td></td> 
                                    </tr>

                              
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                                  
                                     
                                      
                                 
                                </tbody>
                           </table>
                            <hr>
                      
                        </div>
                       <div class="block-content block-content-full   bg-light">
                                          <a class=" mr-4" href="javascript:;" data-dismiss="modal"><img src="{{asset('public/img/back icon.png')}}" width="40px" height="40px" style="object-fit:contain;"></a>
                                          <a class="  printDiv"  target="_blank"><img src="{{asset('public/img/print.png')}}" width="40px" height="40px" style="object-fit:contain;"></a>
                                   <a class="  pdfDiv" target="_blank"><img src="{{asset('public/img/pdf.jpg')}}" width="40px" height="40px" style="object-fit:contain;"></a>

 
                        </div>
                    </div>
                </div>
            </div>
        </div>





                <!-- END Page Content -->
<!--                              <div class="modal fade" id="viewData" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-lg " role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title"  id="contract_noDisplay"> </h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                           <table class="table tablemodal">
                   
                                                                                    
                                            
           <tbody>
                                                                        <tr class="ContractEndDiv  ">
                                        <th>Reason</th>
                                        <td id="ended_reason"></td>
                                        <td></td><td></td> 

                                    </tr>
                                
                                    <tr>
                                        <th>Client</th>
                                        <td id="firstname"></td>
                                        <td style="width: 25%;"></td><td style="width: 25%;"></td> 
                                                                            </tr>
                                  
                               
                                  
                                    <tr>
                                        <th>Vendor</th>
                                        <td id="vendor_nameDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                      <tr>
                                        <th>Registered Email</th>
                                        <td id="registered_emailDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>

                                    <tr>
                                        <th>Type</th>
                                        <td id="contract_typeDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                      <tr>
                                        <th>Attachment</th>
                                        <td id="attachmentDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                    @if(@Auth::user()->role=='admin')
                                    <tr>
                                        <th>Comments</th>
                                        <td id="commentDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                    @endif
                                       <tr>
                                        <th>Description</th>
                                        <td id="descriptionDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                   
                                   
                                </tbody>
                           </table>
                        
                            <div id="assetdiv" class="table-responsive">
                            </div>
                        </div>
                         <div class="block-content block-content-full   bg-light">
                                          <a class="btn btn-primary printDiv"  target="_blank">Print</a>
                                   <a class="btn btn-primary pdfDiv" target="_blank">PDF</a>


                            <button type="button" class="btn btn-sm float-right btn-light" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
 -->

  

<form action="" class="mb-0 pb-0">
<div class="modal fade" id="filterModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Filter Contracts</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
                   
 
   
   <div class="row">
     


                                                 <div class="col-sm-4 form-group">
                                            <label class="   " for="example-hf-email">Contract Status</label>
                                         
                                                 <select type="text" class="form-control" value="{{@$_GET['contract_status']}}" id="contract_status" name="contract_status" placeholder="All "  > 
                                                            <option value="">All</option>
                                                            <option value="Active" {{@$_GET['contract_status']=='Active'?'selected':''}}>Active</option>
                                                            <option value="Upcoming" {{@$_GET['contract_status']=='Upcoming'?'selected':''}}>Upcoming</option>
                                                            <option value="Expired" {{@$_GET['contract_status']=='Expired'?'selected':''}}>Expired</option>
                                                            <option value="Expired/Ended" {{@$_GET['contract_status']=='Expired/Ended'?'selected':''}}>Ended</option>
                                                            <option value="Inactive" {{@$_GET['contract_status']=='Inactive'?'selected':''}}>Renewed</option>
                                                            
                                                </select>
                                          
                                        </div>
      <?php
                                            $userAccess=explode(',',@Auth::user()->access_to_client);
                                                        $c_check=0;
                                            if(@Auth::user()->role=='admin'){
                                            $client=DB::Table('clients')->where('is_deleted',0)->where('client_status',1)->orderBy('firstname','asc')->get();
                                            }
                                            else{       
                                                    if(sizeof($userAccess)==1){
                                                $c_check=1;
                                                }
                                                $client=DB::Table('clients')->whereIn('id',$userAccess)->where('is_deleted',0)->where('client_status',1)->orderBy('firstname','asc')->get();   
                                            }

                                             ?>
                                    @if($c_check==0)
    <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Client</label>
                                          
                                                 <select type="client_id" class="form-control selectpicker"   data-style="btn-outline-light border text-dark" data-live-search="true" id="client_id"  title="All" value="" name="client_id" placeholder="Client"  >
                                           
                                                    @foreach($client as $c)
                                                    <option value="{{$c->id}}" {{@$_GET['client_id']==$c->id?'selected':''}}>{{$c->firstname}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                    @endif

                                         
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
                                   

                           <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Vendor</label>
                                          
                                          
                                                 <select type="" class="form-control selectpicker " id="vendor_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="vendor_id[]" multiple=""   >
                                                     <?php
                                            $vendor=DB::Table('vendors')->where('is_deleted',0) ->orderBy('vendor_name','asc')->get();

                                                          $vendorArray=$_GET['vendor_id'] ?? [];
                                             ?>
                                                         @foreach($vendor as $s)
                                                    <option value="{{$s->id}}" {{in_array($s->id,$vendorArray)?'selected':''}}  >{{$s->vendor_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                             
                                                      

                                             
                                          
                                            
                                <div class="col-sm-4 form-group">
                                            <label class="   " for="example-hf-email">Contract Type</label>
                                         
                                                 <select type="text" class="form-control" value="{{@$_GET['contract_type']}}" id="contract_type" name="contract_type" placeholder="All "  > 
                                                            <option value="">All</option>
                                                            <option value="Hardware Support" {{@$_GET['contract_type']=='Hardware Support'?'selected':''}}>Hardware Support</option>
                                                                             <option value="Software Support" {{@$_GET['contract_type']=='Software Support'?'selected':''}}>Software Support</option>
                                                            <option value="Subscription" {{@$_GET['contract_type']=='Subscription'?'selected':''}}>Subscription</option>
                                           
                                                         
                                                </select>
                                          
                                        </div>
                                                  <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Distributor</label>
                                          
                                          
                                                 <select type="" class="form-control selectpicker " id="distributor_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="distributor_id[]" multiple=""   >
                                                     <?php
                                            $distributor=DB::Table('distributors')->where('is_deleted',0)->orderBy('distributor_name','asc') ->get();

                                                          $distributorArray=$_GET['distributor_id'] ?? [];
                                             ?>
                                                         @foreach($distributor as $s)
                                                    <option value="{{$s->id}}" {{in_array($s->id,$distributorArray)?'selected':''}}  >{{$s->distributor_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                   
                        
                            <div class="col-sm-4  form-group">
                                            <label class="   " for="example-hf-client_id">Renewal Within (Days)</label>
                                          
                                          
                                                 <input type="number" class="form-control  " id="renewal_within"   value="{{@$_GET['renewal_within']}}" name="renewal_within"    >
                                                   
                                                  
                                            </div>
                                     
                            <div class="col-sm-8  form-group">
                               <label class="   " for="example-hf-client_id">Date Range</label>
                                          
                                          
                    <input type="text" class="js-flatpickr form-control bg-white" id="example-flatpickr-range" name="daterange" placeholder="Select Date Range" data-mode="range" value="{{@$_GET['daterange']}}" data-alt-input="true" data-date-format="Y-m-d"  data-alt-format="d-M-Y">
                                </div>

        </div>
                         
                        </div>
                        <div class="block-content block-content-full   pt-4" style="padding-left: 9mm;padding-right: 9mm">
                            <button type="submit" class="btn mr-3 btn-new"    name="advance_search"   >Apply</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>
                        @if(isset($_GET['advance_search']))
                                               
 <a href="{{url('contract')}}/{{$type}}" class="btn     btn-new-secondary float-right" style="background: black;
    color: goldenrod;">Clear Filters</a>
                                            @else
                                                
     <a href="{{url('contract')}}/{{$type}}" class="btn     btn-new-secondary float-right" style="">Clear Filters</a>
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
              <input type="hidden" name="end"  value="">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header endTitle">End Contract</span>
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

      

       <form class="mb-0 pb-0" id="exportform" action="{{url('export-excel-contract')}}?{{$filter}}" method="get" >
            
<div class="modal fade" id="ExportModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal- -centered  modal-md modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Export  Contract</span>
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
              <option value="2">Client</option>
              <option value="3">Site</option>
              <option value="4">Contract Type</option>
              <option value="5">Vendor</option>
              <option value="6">Start Date</option>
              <option value="7">End Date</option>
              <option value="8">Distributor</option>
              <option value="9">Contract #</option>
              <option value="10">Description</option>
         
              <option value="11">End User Email</option>
              <option value="12">Distro Reference #</option>
              <option value="13">Distro SO#</option>
              <option value="14">Estimate #</option>
              <option value="15">Sales Order #</option>
              <option value="16">Invoice #</option>
                  <option value="17">Invoice Date</option>
              <option value="18">PO #</option>
              <option value="19">Line PN#</option>
              <option value="20">Line Assets</option>
              <option value="21">Line Quantity</option>
              <option value="22">Line Description</option>
              <option value="23">Line MSRP</option>
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
       
         Dashmix.helpers('notify', {align: 'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> {{Session::get('success')}}', delay: 5000});

  @endif
showData('{{@$GETID}}');
function showData(id){
    $('.c-active').removeClass('c-active');
        $('.viewContent[data='+id+']').addClass('c-active');
           $('.c4').css({'backgroundColor':'#D9D9D9','color':'#7F7F7F','borderColor':'#7F7F7F'})
        $('.c4[data='+id+']').css({'backgroundColor':'#97C0FF','color':'#595959','borderColor':'#595959'})
        $.ajax({
            type:'get',
            data:{id:id},
            url:'{{url('get-contract-content')}}',
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

$('.ActionIcon').mouseover(function() {
var data=$(this).attr('data-src');
$(this).find('img').attr('src',data);
})
$('.ActionIcon').mouseout(function() {
  var data=$(this).attr('data-original-src');
$(this).find('img').attr('src',data);  
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
    data:{array:array,type:type},
    url:"{{url('change-contract-columns')}}",
    success:function(res){

    }
})


})


             
               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-contract')}}',
                    success:function(res){

    
                        $('#viewData').modal('show');
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];
                    var start_dateObject=new Date(res.contract_start_date);
                    var contract_start_date=start_dateObject.getFullYear()+'-'+monthNames[start_dateObject.getMonth()]+'-'+start_dateObject.getDate();

                    var end_dateObject=new Date(res.contract_end_date);
                    var contract_end_date=end_dateObject.getFullYear()+'-'+monthNames[end_dateObject.getMonth()]+'-'+end_dateObject.getDate();

                    var po_dateObject=new Date(res.po_date);
                    var po_date=po_dateObject.getFullYear()+'-'+monthNames[po_dateObject.getMonth()]+'-'+po_dateObject.getDate();


                    var invoice_dateObject=new Date(res.invoice_date);
                    var invoice_date=invoice_dateObject.getFullYear()+'-'+monthNames[invoice_dateObject.getMonth()]+'-'+invoice_dateObject.getDate();

                      var ended_onObject=new Date(res.ended_on);
                    var ended_on=ended_onObject.getFullYear()+'-'+monthNames[ended_onObject.getMonth()]+'-'+ended_onObject.getDate(); 
                    var MyDate=new Date('<?php echo date('m/d/Y') ?>');
                     
               var expiry_dateObj=new Date(res.contract_end_date);
           

var status='';
 
const diffTime = Math.abs(expiry_dateObj - MyDate);
const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
 
       if(res.contract_status=='Active'){

              if(diffDays<=30 ){
                                                        status='upcoming.png';                                                                

                                                        }else{
                                                        status='active.png';
                                                        }

 
                                                            }
                                                  else if(res.contract_status=='Inactive'){
                                                status='renewed.png';
                                               
                                                }else if(res.contract_status=='Expired/Ended'){
                                                status='ended.png';
                                               }
                                               else if(res.contract_status=='ended'){
                                                status='ended.png';
                                               }
                                            else if(res.contract_status=='Expired'){
                                                status='expired.png';
                                               }
                                                else{
                                                    status='active.png';
                                                }

               // $('#hostnameDisplay').html('<div style="display:flex;align-items:center"><img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{asset('public/vendor_logos/')}}/'+res.vendor_image+'" alt=""> <p class="text-uppercase mt-3"><img class="  mr-3 atar48" width="30px"  src="{{asset('public/img/')}}/'+status+'" alt="">'+res.contract_no+' <br><span style="color:grey!important">'+res.contract_description+'</span></p></div>')
                   $('#hostnameDisplay').html('<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover" src="{{asset('public/vendor_logos/')}}/'+res.vendor_image+'"  alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><img class="  mr-3 atar48" width="30px"  src="{{asset('public/img/')}}/'+status+'" alt=""><b>'+res.contract_no+'</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">'+res.contract_description+'</span></p></div></div>')



                            $('#clientLogo').html('<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{asset('public/client_logos/')}}/'+res.logo+'" alt="">');



                            $('#firstname').html(res.firstname)
                            $('#site_name').html(res.site_name)

                       $('#contract_noDisplay').html(res.contract_no +' '+status)
                        $('#contract_start_dateDisplay').html(contract_start_date)
                        $('#contract_end_dateDisplay').html(contract_end_date)

                             $('#ended_by').html(res.ended_email)
                             $('#ended_on').html(ended_on)
                             $('#ended_reason').html(res.ended_reason)
                             if(res.contract_status=='Expired/Ended'){
                                $('.ContractEndDiv').removeClass('invisible');
                             }else{
                                $('.ContractEndDiv').addClass('invisible');
                             }
$('#estimate_noDisplay').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?estimate_number=')}}'+res.estimate_no+'">'+res.estimate_no+'</a>')
                        $('#sales_no_Display').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?sales_number=')}}'+res.sales_order_no+'">'+res.sales_order_no+'</a>')
                        $('#invoice_noDisplay').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?invoice_number=')}}'+res.invoice_no+'">'+res.invoice_no+'</a>')
                        
                        @if(@Auth::user()->role=='admin')
                        
                        $('#po_noDisplay').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?po_number=')}}'+res.po_no+'">'+res.po_no+'</a>')
                        @else
                                
                        
                        $('#po_noDisplay').html(res.po_no)
                        @endif
                       $('#invoice_dateDisplay').html(invoice_date)
                        $('#po_dateDisplay').html(po_date)
                        $('#distributor_nameDisplay').html(res.distributor_name)
                        $('#reference_noDisplay').html(res.reference_no)
                        $('#registered_emailDisplay').html(res.registered_email)
                        
                              $('#descriptionDisplay').html(res.contract_description)
                        

  if(res.comments=='' || res.comments==null){
                                                    $('.commentsDiv').addClass('d-none')
                                              }
                                              else{
                                               $('.commentsDiv').removeClass('d-none') 
                                              }


  if(res.attachment=='' || res.attachment==null){
                                                    $('.attachmentsDiv').addClass('d-none')
                                              }
                                              else{
                                               $('.attachmentsDiv').removeClass('d-none') 
                                              }

                            $('#sales_order_noDisplay').html(res.distrubutor_sales_order_no)
                            $('#vendor_nameDisplay').html(res.vendor_name)
                            $('#contract_typeDisplay').html(res.contract_type)
                            $('#commentDisplay').html(res.comments)
    $('#created_at').html(res.created_at)
                               $('#created_by').html(res.created_by!=null?res.created_firstname+' '+res.created_lastname:'')
                                  $('#updated_by').html(res.updated_by!=null?res.updated_firstname+' '+res.updated_lastname:'')
                               $('#updated_at').html(res.updated_at)
                          
                              if(res.attachment!='' && res.attachment!=null)
                              {
                                ht='';
                                    var attachments=res.attachment.split(',');
                                    for(var i=0;i<attachments.length;i++){
                                    var icon='fa-file';

                                        var fileExtension = attachments[i].split('.').pop();
                                        console.log(fileExtension)
                                            if(fileExtension=='pdf'){
                                                icon='fa-file-pdf';
                                            }
                                            else if(fileExtension=='doc' || fileExtension=='docx'){
                                                icon='fa-file-word'
                                            }
                                            else if(fileExtension=='txt'){
                                                icon='fa-file-alt';

                                            }
                                            else if(fileExtension=='csv' || fileExtension=='xlsx' || fileExtension=='xlsm' || fileExtension=='xlsb' || fileExtension=='xltx'){
                                                    icon='fa-file-excel'
                                            }
                                            else if(fileExtension=='png' || fileExtension=='jpeg' || fileExtension=='jpg' || fileExtension=='gif' || fileExtension=='webp' || fileExtension=='svg' ){
                                                icon='fa-image'
                                            }
                                            ht+='<span class="attachmentDiv mr-2"><i class="fa '+icon+' text-danger"></i><a class="text-dark"  href="{{asset('public/contract_attachment')}}/'+attachments[i]+'" target="_blank"> '+attachments[i]+'</a></span>';
                                   } 
                                   $('#attachmentDisplay').html(ht)
                                     }
                                     else{
                                        $('#attachmentDisplay').html('')
                                     }

            
 $('.printDiv').attr('href','{{url('print-contract')}}?id='+id)
                                $('.pdfDiv').attr('href','{{url('pdf-contract')}}?id='+id)



                
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-contract-details')}}',
                    success:function(res){
   var html='';
 
                  
                        $('#assetdiv').html(res)

                    }
                })





                                   }
                })

               })

@if(isset($_GET['advance_search']) && $_GET['client_id']!='')

run('{{$_GET['client_id']}}','on')
var site_id='<?php echo isset($_GET['site_id'])?implode(',',$_GET['site_id']):''?>';
var vendor_id='<?php echo isset($_GET['vendor_id'])?implode(',',$_GET['vendor_id']):''?>';

getVendor('{{@$_GET['client_id']}}',site_id.split(','),'on')
getDistributor('{{@$_GET['client_id']}}',site_id.split(','),vendor_id.split(','),'on')
 
@endif
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




function getVendor(client_id,site_id,on){
      $.ajax({
        type:'get',
        data:{client_id:client_id,site_id:site_id},
        url:'{{url('getVendorOfContract')}}',
         async:false,
        success:function(res){
            var html='';
          var check='<?php echo @$_GET['vendor_id']?implode(',',$_GET['vendor_id']):''   ?>';;
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

            $('#vendor_id').html(html);
            $('#vendor_id').selectpicker('refresh');
        }
    })
}
 
function getDistributor(client_id,site_id,vendor_id,on){
      $.ajax({
        type:'get',
        data:{client_id:client_id,site_id:site_id,vendor_id:vendor_id},
        url:'{{url('getDistributorOfContract')}}',
         async:false,
        success:function(res){
            var html='';

                   var check='<?php echo @$_GET['distributor_id']?implode(',',$_GET['distributor_id']):''?>';;
                        check=check.split(',');
            for(var i=0;i<res.length;i++){
                if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].distributor_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].distributor_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].distributor_name+'</option>';
                }
            } 

            $('#distributor_id').html(html);
            $('#distributor_id').selectpicker('refresh');
        }
    })
}

$('#site_id').change(function(){
    var site_id=$(this).val();
    var client_id=$('#client_id').val()
   
    getVendor(client_id,site_id)
})
$('#vendor_id').change(function(){
    var vendor_id=$(this).val();
    var client_id=$('#client_id').val()
   var site_id=$('#site_id').val()
    getDistributor(client_id,site_id,vendor_id)
})

function run(id,on){ 
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getSiteByClientId')}}',
        async:false,
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

$('#client_id').change(function(){
    var id=$(this).val()

    run(id)
       getVendor(id); 

})


$(document).on('click','.btnEnd',function(){
    var id=$(this).attr('data');
    var ended=$(this).attr('data-ended');
    if(ended==1){
      $('input[name=end]').val(1);
    $('.endTitle').html('Reinstate Contract')
    }
    else{
      $('.endTitle').html('End Contract')
    $('input[name=end]').val(0);
    }
    $('input[name=id]').val(id);
$('#EndModal').modal('show')
})

               $(document).on('click','.btnDelete',function(){
                    var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this Contract");
                    if(c){
                        window.location.href="{{url('delete-contract')}}?id="+id;
                    }
                            })  
           })
</script>
