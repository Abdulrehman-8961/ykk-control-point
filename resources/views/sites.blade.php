  
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
            DB::table('settings')->where('user_id',Auth::id())->update(['sites'=>$limit]);
        }
        else{
            DB::table('settings')->insert(['user_id'=>Auth::id(),'sites'=>$limit]);
        }
        
}
else{
           
        if($no_check!=''){
            if($no_check->sites!=''){
            $limit=$no_check->sites;

        }
        }
}

$userAccess=explode(',',Auth::user()->access_to_client);


if(sizeof($_GET)>0){

$orderby='desc';
$field='id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


if(Auth::user()->role=='admin'){ 
     $qry=DB::table('sites as s')->where('s.is_deleted',0)->select('s.*','c.firstname','c.logo')->join('clients as c','c.id','=','s.client_id')->where(function($query){
        $query->Orwhere('c.firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.site_name','like','%'.@$_GET['search'].'%');
        
        $query->Orwhere('s.country','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.city','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.province','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.phone','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.fax','like','%'.@$_GET['search'].'%');
 
     }) ->orderBy($field,$orderby)->paginate($limit); 
}
else{
     $qry=DB::table('sites as s')->where('s.is_deleted',0)->select('s.*','c.firstname','c.logo')->join('clients as c','c.id','=','s.client_id')->where(function($query){
        $query->Orwhere('c.firstname','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.site_name','like','%'.@$_GET['search'].'%');
        
        $query->Orwhere('s.country','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.city','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.province','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.phone','like','%'.@$_GET['search'].'%');
        $query->Orwhere('s.fax','like','%'.@$_GET['search'].'%');
 
     }) ->whereIn('s.client_id',$userAccess)->orderBy($field,$orderby)->paginate($limit);     
}
}
 else{

if(Auth::user()->role=='admin'){ 
$qry=DB::table('sites as s')->where('s.is_deleted',0)->select('s.*','c.firstname','c.logo')->join('clients as c','c.id','=','s.client_id') ->orderBy('s.id','desc')->paginate($limit); 
 
 }
 else{
    $qry=DB::table('sites as s')->where('s.is_deleted',0)->select('s.*','c.firstname','c.logo')->join('clients as c','c.id','=','s.client_id') ->whereIn('s.client_id',$userAccess)->orderBy('s.id','desc')->paginate($limit); 
 }
 }

 if(isset($_GET['id'])){
$GETID=$_GET['id'];
}
else{
$GETID=@$qry[0]->id;
}
 ?>       <!-- Main Container -->
              <main id="main-container pb-0">
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
.ActionIcon{

    border-radius: 50%;
    padding: 6px;
}
.ActionIcon:hover{

 background: #dadada;
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
    font-size:8pt;
    font-weight: bold;
    color:#1EFF00 ;
 
 }
 .HostInActive{
    font-family: Calibri;
    font-size:8pt;
    font-weight: bold;
    color:#BFBFBF ;
 
 }
         .dropdown-menu {
        z-index: 100000!important;
    }
 
    .bg-orange{
  background-color: #FF9953;
}
    .pagination{
        margin-bottom: 0px!important;
    }
  #page-header{
        display: none;
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
                                     <?php

 $filter=(isset($_GET['advance_search'])?'advance_search='.$_GET['advance_search']:'').(isset($_GET['client_id'])?'&client_id='.$_GET['client_id']:'').(isset($_GET['site_name'])?'&site_name='.$_GET['address']:'').(isset($_GET['zone'])?'&zone='.$_GET['zone']:'').(isset($_GET['internet_facing'])?'&internet_facing='.$_GET['internet_facing']:'').(isset($_GET['limit'])?'&limit='.$_GET['limit']:'');
 ?>

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
                       <form class="push mb-0"   method="get" id="form-search" action="{{url('sites/')}}">
                                        
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control searchNew" name="search" placeholder="Search Sites">
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
<div class="col-sm-3 "  style="margin-top: 2px;">
   
                  
                      
                  
                             <span data-toggle="modal" data-bs-target="#ExportModal" data-target="#ExportModal"> 
                           <button class="btn btn-dual d2    "    data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Export"   href="javascript:;" style="margin-left: 5px;margin-right:  0px" >
                           <img src="{{asset('public/img/ui-icon-export.png')}}" width="20px" height="20px">
                        </button>
                    </span>
                              @if(Auth::user()->role!='read') 
                         <a class="btn btn-dual d2    "  href="{{url('add-sites')}}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Site" >
                           <img src="{{asset('public/img/ui-icon-add.png')}}"  width="19px" height="19px">
                        </a>
                        @endif
</div>

<div class="   col-lg-3   " >
          {{$qry->appends($_GET)->onEachSide(0)->links()}}
                       </div><div class="d-flex text-right col-lg-3 justify-content-end" ><form  id="limit_form" class="ml-2 mb-0"  action="{{url('sites')}}?{{$_SERVER['QUERY_STRING']}}">
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
         <div class="col-lg-4     no-print"  style="overflow-y: auto;height: 90vh;">
                @foreach($qry as $q)


                <div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="{{$q->id}}" style="cursor:pointer;">
                    
                 
                        <div class="block-content pt-1 pb-1 d-flex  pl-1 position-relative">
                                    

                                         <div class="mr-1     justify-content-center align-items-center  d-flex" style="width:20%;padding:7px">
                                            <img src="{{asset('/public')}}/client_logos/{{$q->logo}}"  class="rounded-circle  "  width="100%" style=" object-fit: cover;">
                                        </div>


                                     <div class="  " style="width:46%">
                                             <p class="font-12pt mb-0 text-truncate font-w600 c1">{{$q->site_name}}</p>

                                               <div class="d-flex" >
                                                                   
<?php $country=DB::Table('countries')->wherE('name',$q->country)->first(); ?>
                                                                     <p class="font-11pt mr-1   mb-0    c4-p  "  data-toggle="tooltip" data-title="{{$q->country}}" style="max-width:auto;background: #FFCC00;color: black!important " data="{{$q->id}}">{{@$country->iso2}}</p>
                                                                   
                                                                         <p class="font-12pt mb-0 text-truncate   c4" style="max-width:auto;min-width: 90%"  data="{{$q->id}}">{{$q->address}}</p></div>

                              
                                                    <p class="font-12pt mb-0 text-truncate c2"  style="width: 100%">{{$q->city}}, {{$q->province}} {{$q->zip_code}}</p> 
                                        </div>
                                        <div class=" text-right" style="width:25%;;">
                                                                            <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                               

                                                            
                                                             
                                             
                                           <div class=" d-flex " >
                                                            <span class="badge-new  ml-auto  bg-dark px-2" style="  color: white;margin-right: -5px;z-index: 100 ;padding-top: 5px;padding-bottom: 5px">T</span>        <div class="  badge-new  text-center    font-weight-bold text-dark" style="border: 1px solid lightgrey;box-shadow: none;border-radius:5px;background-color: white; "> {{$q->phone}}</div> 
                                                                    </div>  
                                                                 

                                                                </div>
                                                               
                                                                    <div  class="" style="position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                                                        
    
 <div class="ActionIcon"  >
                                       <a href="https://www.google.com/maps?q={{$q->address}}+{{$q->zip_code}}+{{$q->city}}+{{$q->province}}+{{$q->country}}" class="toggle" target="_blank" >
                                                                       
                                                                        <img src="{{asset('public')}}/img/bubble-icon-pin.png" width="30px">
                                                                        </a>
</div>


                                                                     <?php     if(Auth::check()){    
                                            if(@Auth::user()->role!='read'){ ?>
                                                                     <div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                         <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                                       
                                                                        <img src="{{asset('public')}}/img/dots.png?cache=1"   >
                                                                        </a>
                                         <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">
                                       
                                                        
             
                  <a class="dropdown-item d-flex align-items-center  px-0"    href="{{url('edit-sites')}}?id={{$q->id}}" ><div style="width: 32px;  padding-left: 5px"><img src="{{asset('public')}}/img/d3-edit.png?cache=1" width="17px"  ></div> Edit</a>
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

               <div class="col-lg-8    " id="showData"  style="overflow-y: auto;height:90vh;">
            
 </div>


       <form class="mb-0 pb-0" id="exportform" action="{{url('export-excel-site')}}?{{$filter}}" method="get" >
            
<div class="modal fade" id="ExportModal"  tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal- -centered  modal-md modal-bac  " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Export Sites</span>
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
         
 
    
              <option value="1">Client</option>
              <option value="2">Site</option>
              <option value="3">Country</option>
              <option value="4">Address</option>
              <option value="5">City  </option>
   <option value="6">State  </option>

              <option value="7">Zip/Postal Code</option>
              <option value="8">Phone1 </option>
              <option value="9">Phone2</option> 
            

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
        $.ajax({
            type:'get',
            data:{id:id},
            url:'{{url('get-site-content')}}',
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
   $('#form-search').submit(function(e){
    e.preventDefault();
    })
  $('input[name=search]').keyup(function(e){ 
    
var val=$(this).val();
    if(e.which==13){
     var form=$('#form-search');

   let url = form.attr("action");
        url+='?search='+val;
  window.location.href=url
}
  })

$('.ActionIcon').mouseover(function() {
var data=$(this).attr('data-src');
$(this).find('img').attr('src',data);
})
$('.ActionIcon').mouseout(function() {
  var data=$(this).attr('data-original-src');
$(this).find('img').attr('src',data);  
})

               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-sites')}}',
                    success:function(res){
                        $('#viewData').modal('show');
                 
                            $('#firstname').html(res.firstname)
                            $('#site_name').html(res.site_name)
                            $('#country').html(res.country)
                          
                            $('#address').html(res.address)

                            $('#city').html(res.city)
                            $('#province').html(res.province)
                            $('#zip_code').html(res.zip_code)
                            $('#phone').html(res.phone)
                            $('#fax').html(res.fax)

                            $('.printDiv').attr('href','{{url('export-print-sites')}}?id='+id)
                                $('.pdfDiv').attr('href','{{url('export-pdf-sites')}}?id='+id)
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



 $(document).on('click','.btnDelete',function(){
                   var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this site");
                    if(c){
                        window.location.href="{{url('delete-sites')}}?id="+id;
                    }
                            })  
           })
</script>
