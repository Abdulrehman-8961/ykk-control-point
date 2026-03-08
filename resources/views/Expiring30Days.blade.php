  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')


<?php 
$userAccess=explode(',',Auth::user()->access_to_client);



$limit=10;
 


$expiry_date=date('Y-m-d',strtotime('+30 days'));

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

$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond")->where('s.is_deleted',0)->where('c.is_deleted',0) ->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->where('a.is_deleted',0) ->orderBy($field,$orderby) ->paginate($limit); 

}
else{
    $qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->whereRaw("c.is_deleted=0 $cond")->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0) ->whereIn('a.client_id',$userAccess) ->where('a.contract_status','Active')->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->orderBy($field,$orderby) ->paginate($limit); 
    
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
 
     }) ->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0) ->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active') ->orderBy($field,$orderby) ->paginate($limit); 
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
 
     }) ->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)  ->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->whereIn('a.client_id',$userAccess)->orderBy($field,$orderby) ->paginate($limit); 

}


}

}
 else{

   

if(Auth::user()->role=='admin'){
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0)->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date) ->where('a.contract_status','Active')->orderBy('a.id','desc') ->paginate($limit); 
 }
 else{
$qry=DB::table('contracts as a')->select('a.*','c.firstname','s.site_name','d.distributor_name','v.vendor_name') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->where('s.is_deleted',0)->where('c.is_deleted',0)->where('a.is_deleted',0) ->where('a.contract_end_date','>=',date('Y-m-d'))->where('a.contract_end_date','<=',$expiry_date)->where('a.contract_status','Active')->whereIn('a.client_id',$userAccess)->orderBy('a.id','desc') ->paginate($limit); 
    
 }
 }


 ?>       <!-- Main Container -->
           
             
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

</style>
             
                <!-- Page Content -->
                <div class="content">
                    <!-- Full Table -->
                    <div class="block block-rounded  mb-0 pb-0">
                    
                        <div class="block-content pt-0 mt-0">

<div class="TopArea" style="position: sticky; 
    padding-top: 8px;
    z-index: 1000;
    background: white;
    padding-bottom: 5px;">
    <div class="row" >
        <div class="col-sm-3">
                        <form class="push mb-0"   method="get">
                                        <input type="hidden" name="limit" value="{{$_GET['limit']??10}}">
                          
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control searchNew" name="search" placeholder="Search SSL Certificate">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                              <img src="{{asset('public/img/ui-icon-search.png')}}" width="23px">
                                        </span>
                                    </div>
                                </div>
                                 <div class="    float-left " role="tab" id="accordion2_h1">
                                            @if(!isset($_GET['advance_search']))
                                               
                                       @else
                                       <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a>
                                       <a href="{{url('expiring-30-days')}}" class="text-danger">| Clear Filters</a>
                                       @endif
                                            </div>  
                            </form>
</div>
<div class="col-sm-3">
     <button type="button" class="btn btn-dual d2    "   data-toggle="modal" data-target="#filterModal">
                           <img src="{{asset('public/img/ui-icon-filters.png')}}" width="20px" height="24px">
                        </button>
                           <button type="button" class="btn btn-dual d2    "    data-toggle="modal" data-target="#EditColumnModal">
                           <img src="{{asset('public/img/columns.png')}}" width="20px" height="20px">
                        </button>
                    
</div>

<div class="   col-lg-3   " >
          {{$qry->appends($_GET)->onEachSide(0)->links()}}
                       </div><div class="d-flex text-right col-lg-3 justify-content-end" ><form  id="limit_form" class="ml-2 mb-0" action="{{url('expiring-30-days/')}}?{{$_SERVER['QUERY_STRING']}}">
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
                                <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public')}}/dashboard_assets/media/avatars/avatar2.jpg"   alt="">
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

                               <div class="content">
                    <!-- Full Table -->
                    <div class="block block-rounded   mb-0 pb-0">
                     <div class="block-content px-0 pt-0 mt-0">     
                         
                  <?php
               



 $filter=(isset($_GET['advance_search'])?'advance_search='.$_GET['advance_search']:'').(isset($_GET['client_id'])?'&client_id='.$_GET['client_id']:'').(isset($_GET['site_id'])?'&site_id='.http_build_query(array('domain'=>$_GET['site_id'])):'') .(isset($_GET['contract_status'])?'&contract_status='.$_GET['contract_status']:'').(isset($_GET['estimate_no'])?'&estimate_no='.$_GET['estimate_no']:'').(isset($_GET['sales_order_no'])?'&sales_order_no='.$_GET['sales_order_no']:'').(isset($_GET['distrubutor_id'])?'&distrubutor_id='.http_build_query(array('distrubutor_id'=>$_GET['distrubutor_id'])):'').(isset($_GET['vendor_id'])?'&vendor_id='.http_build_query(array('vendor_id'=>$_GET['vendor_id'])):'').(isset($_GET['invoice_no'])?'&invoice_no='.$_GET['invoice_no']:'').(isset($_GET['invoice_date'])?'&invoice_date='.$_GET['invoice_date']:'').(isset($_GET['po_no'])?'&po_no='.$_GET['po_no']:'').(isset($_GET['po_date'])?'&po_date='.$_GET['po_date']:'').(isset($_GET['reference_no'])?'&reference_no='.$_GET['reference_no']:'').(isset($_GET['distrubutor_sales_order_no'])?'&distrubutor_sales_order_no='.$_GET['distrubutor_sales_order_no']:'').(isset($_GET['contract_no'])?'&contract_no='.$_GET['contract_no']:'').(isset($_GET['contract_start_date'])?'&contract_start_date='.$_GET['contract_start_date']:'').(isset($_GET['has_attachment'])?'&has_attachment='.$_GET['has_attachment']:'').(isset($_GET['contract_end_date'])?'&contract_end_date='.$_GET['contract_end_date']:'').(isset($_GET['limit'])?'&limit='.$_GET['limit']:'');

?>            <div class="table-responsive" style="height:90vh">
                                <table class="table   table-striped floathead table-bordered table-vcenter" >
                                    <thead class="thead thead-dark">
                                        <tr>
                                                <th class="text-center ">Actions</th>
                                 <th data-index=0 style="min-width:70px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.id" class=" 
                                                "># <i class="fa fa-sort"></i>  </a></th>
                                  
                               <th  data-index=21 style="min-width: 100px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.contract_type" class=" 
                                                ">Type <i class="fa fa-sort"></i>  </a></th>
                                        <th  data-index=1 style="min-width: 100px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.contract_status" class=" 
                                                ">Status <i class="fa fa-sort"></i>  </a></th>
                                                     <th  data-index=2 style="min-width: 110px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.contract_no" class=" 
                                                ">Contract # <i class="fa fa-sort"></i>  </a></th>
                                                 <th  data-index=3 style="min-width: 100px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.contract_start_date" class=" 
                                                ">Start Date <i class="fa fa-sort"></i>  </a></th>
                                                 <th  data-index=4 style="min-width: 100px" ><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.contract_end_date" class=" 
                                                ">End Date <i class="fa fa-sort"></i>  </a></th>
                                                             <th  data-index=5 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=v.vendor_name" class=" 
                                                ">Vendor <i class="fa fa-sort"></i> </a></th>
                                            <th data-index=6  style="min-width: 130px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=c.firstname" class=" 
                                                ">Client  <i class="fa fa-sort"></i>  </a></th>
                                            <th data-index=7 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=s.site_name" class=" 
                                                ">Site   <i class="fa fa-sort"></i> </<a></th>
                                            <th data-index=8 style="min-width:90px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.estimate_no" class=" 
                                                ">Est # <i class="fa fa-sort"></i> </a></th>
                                            <th data-index=9 style="min-width: 110px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.sales_order_no" class=" 
                                                ">Sales Ord # <i class="fa fa-sort"></i> </a></th>
                                                <th data-index=10 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.invoice_no" class=" 
                                                ">Inv # <i class="fa fa-sort"></i> </a></th>
                                                    <th  data-index=11 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.invoice_date" class=" 
                                                ">Inv Date  <i class="fa fa-sort"></i> </a></th>
                                                    <th  data-index=12 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.po_no" class=" 
                                                ">PO #  <i class="fa fa-sort"></i> </a></th>
                                                    <th  data-index=13 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=o.po_date" class=" 
                                                ">PO Date  <i class="fa fa-sort"></i> </a></th>
                                                <th data-index=14  style="min-width: 110px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=d.distributor_name" class=" 
                                                ">Distributor <i class="fa fa-sort"></i> </a></th>
                                                <th data-index=15 style="min-width: 100px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.reference_no" class=" 
                                                ">Ref # <i class="fa fa-sort"></i> </a></th>
                                                <th data-index=16  style="min-width: 160px"><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}{{$filter}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=a.distrubutor_sales_order_no" class=" 
                                                ">Distributor Sales # <i class="fa fa-sort"></i> </a></th>
                                        <th data-index=20  style="min-width: 100px"><a href="#" class=" 
                                                ">Description</a></th>
                                                        
                                                  <th data-index=17  style="min-width: 100px"><a href="#" class=" 
                                                ">Attachment </a></th>
                                                 <th data-index=18  style="min-width: 100px"><a href="#" class=" 
                                                ">Comments</a></th>
                                          
                                      
                                           
                                         
                                        </tr>
                                    </thead>
                                    <tbody id="showdata">
                                          @php  $sno= $qry->perPage() * ($qry->currentPage() - 1);@endphp
                                        @foreach($qry as $q)

                                        <tr data="{{$q->id}}" >
                                              <td class="text-center">

                                                <div class="btn-group">
                                                 
                                                <button type="button" class="btn btn-alt-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdown-default-alt-primary" style="">
                                                  
                                                    <a class="dropdown-item" target="_blank" href="{{url('print-contract')}}?id={{$q->id}}">Print</a>
                                                    <a class="dropdown-item" target="_blank" href="{{url('pdf-contract')}}?id={{$q->id}}">Pdf</a>
                                                                @if(Auth::user()->role!='read') 
                                                        

                                                        @if($q->contract_status!='Inactive')
                                                    <a class="dropdown-item" href="{{url('renew-contract')}}?id={{$q->id}}">Renew</a>
                                                    @endif
                                                 
                                                   <a class="dropdown-item"  href="{{url('edit-contract')}}?id={{$q->id}}">Edit</a>
                                                            @if($q->contract_status!='Inactive' && $q->contract_status!='Expired/Ended')
                                                         <a class="dropdown-item" onclick="return confirm('Are you sure want to end this Contract?')"  href="{{url('end-contract')}}?id={{$q->id}}">End Contract</a>
                                                            @endif
                                                    <a class="dropdown-item btnDelete"  data="{{$q->id}}"  href="javascript:void(0)">Delete</a>


                                                    @endif
                                        
                                               </div>
                                                  <button type="button"   data="{{$q->id}}"   class="btn btn-sm btn-alt-success btnEdit" data-toggle="tooltip" data-trigger="hover" title="View">
                                                        <i class="fa fa-eye"></i>
                                                  </button>
                                                </div>

                                            </td>
                                             
                                             <td  data-index=0> {{++$sno}}</td>
                            <td  data-index=21> {{$q->contract_type}}</td>
                        <td  data-index=1 class="font-w600">
                                                @if($q->contract_status=='Active')
                                                        <div class="badge badge-warning"> Upcoming 
                                                     
                                                        </div>
                                                            
                                                @elseif($q->contract_status=='Inactive')
                                                <div class="badge badge-warning">{{$q->contract_status}}/Renewed</div>
                                               
                                                          
                                                          
                                                @else
                                                <div class="badge badge-danger">{{$q->contract_status}}</div>
                                                @endif

                                                
                                            </td>
                                            <td  data-index=2>{{$q->contract_no}}</td>
                                               <td  data-index=3>{{date('Y-M-d',strtotime($q->contract_start_date))}}</td>
                                                <td  data-index=4>{{date('Y-M-d',strtotime($q->contract_end_date))}}</td>
                                                 <td  data-index=5>{{$q->vendor_name}}</td>
                                                  <td  data-index=6>{{$q->firstname}}</td>
                                                   <td  data-index=7>{{$q->site_name}}</td>
                                                    <td  data-index=8>{{$q->estimate_no}}</td>
                                                     <td  data-index=9>{{$q->sales_order_no}}</td>
                                                      <td  data-index=10>{{$q->invoice_no}}</td>
                                                       <td  data-index=11>{{date('Y-M-d',strtotime($q->invoice_date))}}</td>
                                                        <td  data-index=12>{{$q->po_no}}</td>
                                             

                                                         <td  data-index=13>{{date('Y-M-d',strtotime($q->po_date))}}</td>

                                                          <td  data-index=14>{{$q->distributor_name}}</td>
                                                           <td  data-index=15>{{$q->reference_no}}</td>
                                                     <td  data-index=16>{{$q->distrubutor_sales_order_no}}</td>
                                                        <td  data-index=20>{{$q->contract_description}}</td>
    
                                                   <td  data-index=17>
                                                            @if($q->attachment!='')
                                                               <a  href="{{asset('public/contract_attachment')}}/{{$q->attachment}}" target="_blank">View</a>
                                                            @endif
                                                </td>
                                                     <td  data-index=18>{{$q->comments}}</td>
                                              
                                            
                                         
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            
                             
                            </div>
                          
                        </div>
                        </div>

                    </div>
                    <!-- END Full Table -->
 
                </div>









 
<?php 
 
   
    
                $column_array=array(1,2,4,5,6,12,14,16,20);
          if(@$no_check->expiring_contract!='' ){
                        $column_array=explode(',',$no_check->expiring_contract);
            }
        
    
       ?>





       <div class="modal fade" id="viewData" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
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
                                    <tr>
                                        <th>Client</th>
                                        <td id="firstname"></td>
                                        <td></td><td></td> 
                                                                            </tr>
                                    <tr>
                                        <th>Site</th>
                                        <td id="site_name"></td>
                                        <td></td><td></td> 

                                    </tr>
                                
                                    <tr>
                                        <th>Start Date</th>
                                        <td id="contract_start_dateDisplay"></td>
                                              <th>End Date</th>
                                        <td id="contract_end_dateDisplay"></td>
                                    </tr>
                               
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
                                    <tr>
                                        <th>Comment</th>
                                        <td id="commentDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
                                       <tr>
                                        <th>Description</th>
                                        <td id="descriptionDisplay"></td>
                                        <td></td><td></td> 
                                    </tr>
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
     
                             <div class="modal fade" id="EditColumnModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-sm " role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" >Show/Hide Columns</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">

                                <table class="table table-sm table-striped table-bordered">
                                    <thead>
                                        <th>Column</th>
                                        <th></th>
                                    </thead>
                                    <tbody>




                                        <tr>
                                            <td> # </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox1"  {{in_array(0,$column_array)?'checked':''}}  value="0">
                                            <label class="custom-control-label" for="checkbox1"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Status  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox2"  {{in_array(1,$column_array)?'checked':''}}  value="1">
                                            <label class="custom-control-label" for="checkbox2"></label>
                                        </div>
                                             </td>
                                        </tr>
                                           <tr>
                                            <td>Contract Type  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox21"  {{in_array(21,$column_array)?'checked':''}}  value="21">
                                            <label class="custom-control-label" for="checkbox21"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Contract # </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox3"  {{in_array(2,$column_array)?'checked':''}}  value="2">
                                            <label class="custom-control-label" for="checkbox3"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Start Date </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox4"  {{in_array(3,$column_array)?'checked':''}}  value="3">
                                            <label class="custom-control-label" for="checkbox4"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> End Date </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox5"  {{in_array(4,$column_array)?'checked':''}}  value="4">
                                            <label class="custom-control-label" for="checkbox5"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Vendor </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox6"  {{in_array(5,$column_array)?'checked':''}}  value="5">
                                            <label class="custom-control-label" for="checkbox6"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Client  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox7"  {{in_array(6,$column_array)?'checked':''}}  value="6">
                                            <label class="custom-control-label" for="checkbox7"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Site</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox8"  {{in_array(7,$column_array)?'checked':''}}  value="7">
                                            <label class="custom-control-label" for="checkbox8"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Est #</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox9"  {{in_array(8,$column_array)?'checked':''}}  value="8">
                                            <label class="custom-control-label" for="checkbox9"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Sales Ord #</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox10"  {{in_array(9,$column_array)?'checked':''}}  value="9">
                                            <label class="custom-control-label" for="checkbox10"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Inv #   </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox11"  {{in_array(10,$column_array)?'checked':''}}  value="10">
                                            <label class="custom-control-label" for="checkbox11"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Inv Date </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox12"  {{in_array(11,$column_array)?'checked':''}}  value="11">
                                            <label class="custom-control-label" for="checkbox12"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  PO #</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox13"  {{in_array(12,$column_array)?'checked':''}}  value="12">
                                            <label class="custom-control-label" for="checkbox13"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> PO Date </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox14"  {{in_array(13,$column_array)?'checked':''}}  value="13">
                                            <label class="custom-control-label" for="checkbox14"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Distributor </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox15"  {{in_array(14,$column_array)?'checked':''}}  value="14">
                                            <label class="custom-control-label" for="checkbox15"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Ref # </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox16"  {{in_array(15,$column_array)?'checked':''}}  value="15">
                                            <label class="custom-control-label" for="checkbox16"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td> Distributor Sales # </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox17"  {{in_array(16,$column_array)?'checked':''}}  value="16">
                                            <label class="custom-control-label" for="checkbox17"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Description  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox18"  {{in_array(20,$column_array)?'checked':''}}  value="20">
                                            <label class="custom-control-label" for="checkbox18"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>  Attachment</td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox19"  {{in_array(17,$column_array)?'checked':''}}  value="17">
                                            <label class="custom-control-label" for="checkbox19"></label>
                                        </div>
                                             </td>
                                        </tr>
                                         <tr>
                                            <td>Comments  </td>
                                            <td>
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input changeSelect" id="checkbox20"  {{in_array(18,$column_array)?'checked':''}}  value="18">
                                            <label class="custom-control-label" for="checkbox20"></label>
                                        </div>
                                             </td>
                                        </tr>
                                          
                                        
                                        
                                    </tbody>
                                </table>

  </div>
                       <div class="block-content block-content-full   bg-light">
                             

                            <button type="button" class="btn btn-sm float-right btn-light" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>












<form action="" class="mb-0 pb-0">
                             <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-xl " role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="hostnameDisplay">Filters</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="block-content">
 <input type="hidden" name="limit" value="{{$_GET['limit']??10}}">
                                            
                                                <div class="block-content   row ">
                                                 
 

 

                           <div class="col-sm-3  form-group">
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
                                              <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Contract #</label>
                                         
                                                 <input type="text" class="form-control" value="{{@$_GET['contract_no']}}" id="contract_no" name="contract_no" placeholder="All "  > 
                                         
                                          
                                        </div>
                                                          <div class="col-sm-3  form-group">
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
                              

                                         
                                        <div class="col-sm-3  form-group">
                                            <label class="   " for="example-hf-client_id">Site</label>
                                          
                                          
                                                 <select type="" class="form-control  {{!isset($_GET['site_id']) || @$_GET['client_id']==''?'selectpicker':''}}" id="site_id"  data-style="btn-outline-light border text-dark"  data-actions-box="true"  data-live-search="true"  title="All" value="" name="site_id[]" multiple=""   >
                                                     <?php
                                            $site=DB::Table('sites')->where('is_deleted',0)->orderBy('site_name','asc') ->get();

                                                          $siteArray=$_GET['site_id'] ?? [];
                                             ?>
                                                         @foreach($site as $s)
                                                    <option value="{{$s->id}}" {{in_array($s->id,$siteArray)?'selected':''}}  >{{$s->site_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                   


                                                 <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Amaltitek Estimate #</label>
                                         
                                                 <input type="text" class="form-control" value="{{@$_GET['estimate_no']}}" id="estimate_no" name="estimate_no" placeholder="All "  > 
                                         
                                          
                                        </div>
                                         <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Amaltitek  PO #</label>
                                         
                                                 <input type="text" class="form-control" value="{{@$_GET['po_no']}}"  name="po_no" placeholder="All "  > 
                                         
                                          
                                        </div>
                                          
                                            <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Amaltitek  Invoice   #</label>
                                         
                                                 <input type="text" class="form-control" value="{{@$_GET['invoice_no']}}" id="invoice_no" name="invoice_no" placeholder="All "  > 
                                         
                                          
                                        </div>
 

                               
                                                  <div class="col-sm-3  form-group">
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
                                   
                        
                        

                                          <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Distrubutor Sales Order  #</label>
                                         
                                                 <input type="text" class="form-control" value="{{@$_GET['distrubutor_sales_order_no']}}" id="distrubutor_sales_order_no" name="distrubutor_sales_order_no" placeholder="All "  > 
                                         
                                          
                                        </div>

                                                       <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Reference  #</label>
                                         
                                                 <input type="text" class="form-control" value="{{@$_GET['reference_no']}}" id="reference_no" name="reference_no" placeholder="All "  > 
                                         
                                          
                                        </div>
                                           <div class="col-sm-3 form-group">
                                            <label class="   " for="example-hf-email">Has Attachment</label>
                                         
                                                 <select type="text" class="form-control" value="{{@$_GET['has_attachment']}}" id="has_attachment" name="has_attachment" placeholder="All "  > 
                                            <option value=""   >All</option>
                                            <option value="1" {{@$_GET['has_attachment']==1?'selected':''}}>Yes</option>
                                            <option value="0" {{isset($_GET['has_attachment']) && @$_GET['has_attachment']==0?'selected':''}}>No</option>
                                                    </select>                                          
                                        </div>



                                   
                                     
                        </div>
                          <div class="block-content block-content-full  text-right bg-light">
                             
 
                               <button class="btn   btn-primary"   name="advance_search"  >Filter</button>
                                                <button type="button" class="btn   btn-danger" data-dismiss="modal" >Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </form> 




                <!-- END Page Content -->
                      

            </main>
            
            <!-- END Main Container -->
            @endsection('content')
  <?php $column_array=json_encode($column_array);?>

   

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="{{asset('public/dashboard_assets/js/dashmix.app.min.js')}}"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" defer=""></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
$(function(){
   @if(Session::has('success'))
             Dashmix.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: '{{Session::get('success')}}'});

             @endif


 



locArray=JSON.parse('<?php echo $column_array?>');

 
 

$('.changeSelect').each(function(i,e){
 
 
    var val=$(this).val()
    console.log(val)
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
    data:{array:array },
    url:"{{url('change-expiring-columns')}}",
    success:function(res){

    }
})


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



                            $('#firstname').html(res.firstname)
                            $('#site_name').html(res.site_name)

                       $('#contract_noDisplay').html(res.contract_no)
                        $('#contract_start_dateDisplay').html(contract_start_date)
                        $('#contract_end_dateDisplay').html(contract_end_date)
                        $('#estimate_noDisplay').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?invoice_number=')}}'+res.invoice_no+'">'+res.estimate_no+'</a>')
                        $('#sales_no_Display').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?invoice_number=')}}'+res.invoice_no+'">'+res.sales_order_no+'</a>')
                        $('#invoice_noDisplay').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?invoice_number=')}}'+res.invoice_no+'">'+res.invoice_no+'</a>')
                        $('#invoice_dateDisplay').html(invoice_date)
                        $('#po_noDisplay').html('<a target="_blank" href="{{url('GetZohoInvoicesAuth?invoice_number=')}}'+res.invoice_no+'">'+res.po_no+'</a>')
                        $('#po_dateDisplay').html(po_date)
                        $('#distributor_nameDisplay').html(res.distributor_name)
                        $('#reference_noDisplay').html(res.reference_no)
                        $('#registered_emailDisplay').html(res.registered_email)
                        
                              $('#descriptionDisplay').html(res.contract_description)
                        


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
                                   $('#attachmentDisplay').html( '<a  href="{{asset('public/contract_attachment')}}/'+res.attachment+'" target="_blank">View</a>')
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
@endif




function run(id,on){ 
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getSiteByClientId')}}',
        success:function(res){
            var html='';
                   var check='{{@$site_id}}';
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

})



               $('#showdata').on('click','.btnDelete',function(){
                    var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this Contract");
                    if(c){
                        window.location.href="{{url('delete-contract')}}?id="+id;
                    }
                            })  
           })
</script>
