@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')


<?php 
$userAccess=explode(',',Auth::user()->access_to_client);



$limit=10;
        $no_check=DB::Table('settings')->where('user_id',Auth::id())->first();
if(isset($_GET['limit']) && $_GET['limit']!=''){
    $limit=$_GET['limit'];

        if($no_check!=''){
            DB::table('settings')->where('user_id',Auth::id())->update(['asset_type'=>$limit]);
        }
        else{
            DB::table('settings')->insert(['user_id'=>Auth::id(),'asset_type'=>$limit]);
        }
        
}
else{
           
        if($no_check!=''){
            if($no_check->operating_system!=''){
            $limit=$no_check->operating_system;

        }
        }
}


if(sizeof($_GET)>0){

$orderby='desc';
$field='asset_type_id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


 
     $qry=DB::table('asset_type')->where('is_deleted',0)->where(function($query){
         $query->Orwhere('asset_type_description','like','%'.@$_GET['search'].'%');
    
 
     }) ->orderBy($field,$orderby)->paginate($limit); 
}
 else{
$qry=DB::table('asset_type') ->where('is_deleted',0)->orderBy('asset_type_id','desc')->paginate($limit); 
 
 }
 if(isset($_GET['id'])){
$GETID=$_GET['id'];
}
else{
$GETID=@$qry[0]->asset_type_id;
 
}

 ?>     




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
.ActionIcon:hover{

 background:#dadada;
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
    color:#E54643 ;
      letter-spacing:0px ;
 
 }
  .SSLActive{
    font-family: Calibri;
    font-size:9pt;
    font-weight: bold;
    color:#FFCC00 ;
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
</style>
            
             
                <!-- Page Content -->
                <div class="con   no-print page-header py-2" id="">
                    <!-- Full Table -->
                    <div class="b   mb-0  ">
                    
                        <div class="block-content pt-0 mt-0">

<div class="TopArea" style="position: sticky; 
    padding-top: 8px;
    z-index: 1000;
    
    padding-bottom: 5px;">
    <div class="row" >
        <div class="col-sm-3">

            <?php 


  

 $filter=(isset($_GET['advance_search'])?'advance_search='.$_GET['advance_search']:'').(isset($_GET['client_id'])?'&client_id='.$_GET['client_id']:'').(isset($_GET['site_id'])?'&'.http_build_query(array('site_id'=>$_GET['site_id'])):'') .(isset($_GET['cert_issuer'])?'&'.http_build_query(array('cert_issuer'=>$_GET['cert_issuer'])):'') .(isset($_GET['cert_status'])?'&cert_status='.$_GET['cert_status']:'').(isset($_GET['cert_type'])?'&cert_type='.$_GET['cert_type']:'').(isset($_GET['renewal_within'])?'&renewal_within='.$_GET['renewal_within']:'').(isset($_GET['operating_system_id'])?'&'.http_build_query(array('operating_system_id'=>$_GET['operating_system_id'])):'').(isset($_GET['daterange'])?'&daterange='.$_GET['daterange']:'').(isset($_GET['invoice_date'])?'&invoice_date='.$_GET['invoice_date']:'').(isset($_GET['po_no'])?'&po_no='.$_GET['po_no']:'').(isset($_GET['po_date'])?'&po_date='.$_GET['po_date']:'').(isset($_GET['reference_no'])?'&reference_no='.$_GET['reference_no']:'').(isset($_GET['distrubutor_sales_order_no'])?'&distrubutor_sales_order_no='.$_GET['distrubutor_sales_order_no']:'').(isset($_GET['contract_no'])?'&contract_no='.$_GET['contract_no']:'').(isset($_GET['contract_start_date'])?'&contract_start_date='.$_GET['contract_start_date']:'').(isset($_GET['has_attachment'])?'&has_attachment='.$_GET['has_attachment']:'').(isset($_GET['daterange'])?'&daterange='.$_GET['daterange']:'').(isset($_GET['renewal_within'])?'&renewal_within='.$_GET['renewal_within']:'').(isset($_GET['cert_edate'])?'&cert_edate='.$_GET['cert_edate']:'').(isset($_GET['contract_description'])?'&contract_description='.$_GET['contract_description']:'').(isset($_GET['comments'])?'&comments='.$_GET['comments']:'').(isset($_GET['limit'])?'&limit='.$_GET['limit']:'');
?>

               <form class="push mb-0"   method="get" id="form-search" action="{{url('asset_type/')}}?{{$filter}}">
                                        
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control searchNew" name="search" placeholder="Search Asset Type">
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
<div class="col-sm-3  1"  style="margin-top: 2px;" >
    
                              @if(Auth::user()->role!='read') 
                         <a class="btn btn-dual  d2 "    href="{{url('add-asset-type')}}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Asset Type">
                           <img src="{{asset('public/img/ui-icon-add.png')}}"  width="19px" height="19px">
                        </a>
                        @endif
</div>
<div class="col-sm-3 ">
    {{$qry->appends($_GET)->onEachSide(0)->links()}}
  </div>
 <div class="d-flex text-right col-lg-3 justify-content-end" ><form  id="limit_form" class="ml-2 mb-0"  action="{{url('asset-type')}}?{{$_SERVER['QUERY_STRING']}}">
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

                            </a>
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
         <div class="col-lg-4    no-print"  style="overflow-y: auto;height: 90vh;">
                @foreach($qry as $q)
<div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="{{$q->asset_type_id}}" style="cursor:pointer;">
                    
                         <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">
                                    
                                                                                 <div class="mr-1      justify-content-center align-items-center  d-flex" style="width:70px;padding: 7px" >
                                            
                                            @if($q->asset_icon!='')
                                                    <img src="{{asset('/public')}}/asset_icon/{{$q->asset_icon}}" class="rounded-circle"  width="100%" height="60px" style=" object-fit: cover;">
                                                     @else
         <img src="{{asset('/public')}}/img/image-default.png" class="rounded-circle"  width="100%" style=" object-fit: cover;">
                                            @endif
                                   
                                        </div>
                                        <div class="  " style="width:55%">
                       
                                                      <div class="d-flex " style="padding-top: 20px">
                                                                 
                                                                    <p class="font-11pt mr-1   mb-0  c4-v  "  style="max-width:12%; " data="{{$q->asset_type_id}}" data-toggle="tooltip"  >A</p>
                                                             
                                                                         <p class="font-12pt mb-0 text-truncate   c4"   style="max-width:100%;min-width: 100%" data="{{$q->asset_type_id}}">{{$q->asset_type_description}} </p>
                                                                       </div>

                              

                                               
                                        </div>
                                        <div class=" text-right" style="width:10%;;">
                                     
                  <div class="" style="position: absolute;width: 100%; bottom:20px;right: 10px;display: flex;align-items: center;justify-content: end;">
 
                                                             
                                       <?php     if(Auth::check()){    
                                            if(@Auth::user()->role!='read'){ ?>
                                                                     <div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                         <a    href="{{url('edit-asset-type')}}?id={{$q->asset_type_id}}" class=" "   >
                                                                       <img src="{{asset('public')}}/img/d3-edit.png?cache=1" width="25px"  >
                                                                        </a>
                                                                    </div>

                                                                      <div class="ActionIcon px-0 ml-2  mt-n1   " style="border-radius: 5px"  >
                                                                           <a    href="javascript:;"  class="px-1 btnDelete" data="{{$q->asset_type_id}}"   >
                                                                       <img src="{{asset('public')}}/img/d3-delete.png?cache=1"  width="23px"   >
                                                                        </a>

                                                                   </div>
                                                                    <?php } }?>
                                         
                                                                    </div>
                                        </div>        </div>
                                       </div>
                            @endforeach
                                                          </div>

               <div class="col-lg-8    " id="showData"  style="overflow-y: auto;height:90vh;">
           



</div>












      

        <form class="mb-0 pb-0"  action="{{url('end-asset_type')}}" method="post" >
            @csrf
<div class="modal fade" id="EndModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  -lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Revoke Asset Type</span>
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
                            <button type="submit" class="btn mr-3 btn-new"     >Revoke</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Cancel</button>
                       
                        </div>
                    </div>
               
                </div>
            </div>
        </div>
     

</form>
 

 






       <form class="mb-0 pb-0" id="exportform" action="{{url('export-excel-ssl')}}?{{$filter}}" method="get" >
            
<div class="modal fade" id="ExportModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal- -centered  modal-md modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Export  Asset Type</span>
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
              <option value="4">Cert Type</option>
              <option value="5">Issuer</option>
              <option value="6">Start Date</option>
              <option value="7">End Date</option>
              <option value="8">Description</option>
              <option value="9">Name (CN)</option>
              <option value="10">Company (O)</option>
         
              <option value="11">Locality (L)</option>
              <option value="12">Country (CA)</option>
              <option value="13">Department (OU)</option>
              <option value="14">State (S)</option>
              <option value="15">Email (e)</option>
              <option value="16">LAN - SAN Type</option>
                  <option value="17">LAN - SAN Description</option>
              <option value="18">LAN - Hostname</option>
              <option value="19">LAN - IP Address Name</option>
              <option value="20">LAN - IP Address Value</option> 
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
    if(id){
        $('.viewContent[data='+id+']').addClass('c-active');
    $('.c4').css({'backgroundColor':'#D9D9D9','color':'#7F7F7F','borderColor':'#7F7F7F'})
        $('.c4[data='+id+']').css({'backgroundColor':'#97C0FF','color':'#595959','borderColor':'#595959'})
        }
        $.ajax({
            type:'get',
            data:{id:id},
            url:'{{url('get-asset-type-content')}}',
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
 Dashmix.helpers('notify', {align: 'center', message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Export Complete.  ', delay: 5000}); 
    form.submit();
    $('#ExportModal').modal('hide')
     }
     else{

     }
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




@if(isset($_GET['advance_search']) && @$_GET['client_id']!='')

run('{{$_GET['client_id']}}','on')
var site_id='<?php echo isset($_GET['site_id'])?implode(',',$_GET['site_id']):''?>';
var operating_system_id='<?php echo isset($_GET['cert_issuer'])?implode(',',$_GET['cert_issuer']):''?>';

getVendor('{{@$_GET['client_id']}}',site_id.split(','),'on')
 
@endif

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
}


   
$('#client_id').change(function(){
    var id=$(this).val()

    run(id)
       getVendor(id); 

})

function getVendor(client_id,site_id,on){
      $.ajax({
        type:'get',
        data:{client_id:client_id,site_id:site_id},
        url:'{{url('getVendorOfSSL')}}',
         async:false,
        success:function(res){
            var html='';
          var check='<?php echo @$_GET['cert_issuer']?implode(',',$_GET['cert_issuer']):''   ?>';;
                        check=check.split(',');
            for(var i=0;i<res.length;i++){
                if(on){
                        if(check.includes(String(res[i].id))){
                           html+='<option value="'+res[i].id+'" selected>'+res[i].operating_system_name+'</option>';                            
                        }
                        else{
                                 html+='<option value="'+res[i].id+'" >'+res[i].operating_system_name+'</option>';
                        }
                }
                else{
                html+='<option value="'+res[i].id+'" >'+res[i].operating_system_name+'</option>';
                }
            } 

            $('#cert_issuer').html(html);
            $('#cert_issuer').selectpicker('refresh');
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
$('#site_id').change(function(){
    var site_id=$(this).val();
    var client_id=$('#client_id').val()
   
    getVendor(client_id,site_id)
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


$(document).on('click','.btnEnd',function(){
    var id=$(this).attr('data');
    $('input[name=id]').val(id);
$('#EndModal').modal('show')
})


               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-asset_type')}}',
                    success:function(res){
                        $('#viewData').modal('show');

  
  
                            $('#cert_hostname').html(res.hostname)
                            $('#cert_status').html(res.cert_status!=null?res.cert_status.toUpperCase():'')
                           $('#cert_notification').html(res.cert_notification=='1'?'<div class="badge badge-success">On</div>':'<div class="badge badge-danger">Off</div>')
                            $('#cert_type').html(res.cert_type!=null?res.cert_type.toUpperCase():'')
                            $('#cert_issuer').html(res.cert_issuer)
                        
                         


     if(res.attachment!=''  && res.attachment!=null)
                              {
                                  ht='';
                                    var attachments=res.attachment.split(',');
                                    for(var i=0;i<attachments.length;i++){
                                    var icon='fa-file';

                                        var fileExtension = attachments[i].split('.').pop();
                                       
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
                                            ht+='<span class="attachmentDiv mr-2"><i class="fa '+icon+' text-danger"></i><a class="text-dark"  href="{{asset('public/ssl_attachment')}}/'+attachments[i]+'" target="_blank"> '+attachments[i]+'</a></span>';
                                   } 
                                   $('#attachmentDisplay').html(ht)
                                     }
                                     else{
                                        $('#attachmentDisplay').html('')
                                     }
 
            
                              $('#created_at').html(res.created_at)
                               $('#created_by').html(res.created_by!=null?res.created_firstname+' '+res.created_lastname:'')
                                  $('#updated_by').html(res.updated_by!=null?res.updated_firstname+' '+res.updated_lastname:'')
                               $('#updated_at').html(res.updated_at)

                            $('#cert_name').html(res.cert_name)
                            $('#cert_email').html(res.cert_email)
                            $('#cert_company').html(res.cert_company)
                                $('#cert_department').html(res.cert_department)

                                $('#cert_city').html(res.cert_city)
                                $('#cert_state').html(res.cert_state)
                                $('#cert_country').html(res.cert_country)
                                $('#cert_san1_5').html(res.cert_san1_5)
                                $('#cert_ip_int').html(res.cert_ip_int)
                                $('#cert_ip_pub').html(res.cert_ip_pub)
                                $('#cert_edate').html(res.cert_edate)
                                $('#cert_csr').html(res.cert_csr)
                                $('#cert_process').html(res.cert_process)

                                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June","July", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        var cert_rdate='';
                        if(res.cert_rdate=='' ||  res.cert_rdate==null){
                                 cert_rdate='';
                        }
                        else{

                    var cert_rdateObject=new Date(res.cert_rdate);
                    var cert_rdate=cert_rdateObject.getFullYear()+'-'+monthNames[cert_rdateObject.getMonth()]+'-'+cert_rdateObject.getDate();
                    }

                     var cert_edate='';
                        if(res.cert_edate=='' ||  res.cert_edate==null){
                                 cert_edate='';
                        }
                        else{
                    var cert_edateObject=new Date(res.cert_edate);
                     cert_edate=cert_edateObject.getFullYear()+'-'+monthNames[cert_edateObject.getMonth()]+'-'+cert_edateObject.getDate();
                }

var status='';
    var MyDate=new Date('<?php echo date('m/d/Y') ?>');
                     
const diffTime = Math.abs(cert_edate - MyDate);
const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
 
       if(res.cert_status=='Active'){

              if(diffDays<=30 ){
                                                        status='upcoming.png';                                                                

                                                        }else{
                                                        status='active.png';
                                                        }

 
                                                            }
                                                else if(res.cert_status=='Inactive'){
                                                status='renewed.png';
                                               
                                                }else if(res.cert_status=='Expired/Ended'){
                                                status='ended.png';
                                               }

                                                else if(res.cert_status=='Expired'){
                                                status='expired.png';
                                               }

                                                else{
                                                    status='active.png';
                                                }

 
                     

                       $('#hostnameDisplay').html('<div style="display:flex;align-items:center"><img class="  mr-2 atar48" style="object-fit: cover"  src="'+operating_system+'"  alt="" width="60px" height="40px"> <div><p class="text-uppercase mb-0 mt-2" style="color:#0D0D0D!important;font-size:15pt;line-height:20px"><img class="  mr-3 atar48" width="30px"    src="{{asset('public/img/')}}/'+status+'" alt=""><b>'+res.cert_name+'</b></p><p class="my-0"><span style="color:#D3D3D3!important;font-size:10pt">'+(res.cert_type!=null?res.cert_type.toUpperCase():'')+'</span></p></div></div>')


                            $('#clientLogo').html('<img class="img-avatar  mr-3 atar48" style="object-fit: cover" src="{{asset('public/client_logos/')}}/'+res.logo+'" alt="">');





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
                    $('#cert_rdate').html(cert_rdate)
                            $('#cert_msrp').html(res.cert_msrp)
                            $('#cert_edate').html(cert_edate)

                      }
                })

               })


          $(document).on('click','.btnDelete',function(){
                    var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this Asset Type");
                    if(c){
                        window.location.href="{{url('delete-asset-type')}}?id="+id;
                    }
                            })  
           })
</script>





















 