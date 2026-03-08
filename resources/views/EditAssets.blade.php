  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
<?php



$qry=DB::table('assets')->where('id',$_GET['id'])->first();
$userAccess=explode(',',Auth::user()->access_to_client);
if(Auth::user()->role!='admin'  ){
  
if(!in_array($qry->client_id,$userAccess)){
    echo "You dont have access";
    exit;
}
}
if(Auth::user()->role=='read'){
  echo "You dont have access";
    exit;
}

$parent_type=DB::table('asset_type')->where('asset_type_id',$qry->asset_type_id)->first();
 
$type=$qry->asset_type;
 
?>

<style type="text/css">
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
  @media only screen and (min-width: 1000px) {
        .blockfooter {
            position: fixed!important;
    bottom: 3px!important;
    left: 249px!important;
    z-index: 1000!important;

        }
    }

    .dropdown-menu {
        max-height: 400px!important;
    }
    #page-header{
        display: none;
    }
</style>
          <!-- Main Container -->
            <main id="main-container  " style="padding:3mm">
                <!-- Hero -->
                        @if($qry->AssetStatus==1)
                            <div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="{{asset('public/img/header-white-asset-type.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Edit  <span class="text-capitalize">{{$type}}</span> Asset</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px"><?php echo date('Y-M-d') ?> by {{Auth::user()->firstname.' '.Auth::user()->lastname}}</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="{{asset('public/img/paper-clip-white.png')}}" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="{{asset('public/img/comment-white.png')}}" width="20px"></a>
                                         </span>
                                        <!--    <a href="javascript:;"  class="saveContract text-white"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add and Continue"  class="text-white"><i class="fa fa-plus texti-white"  ></i> </a> -->
 
                                              <a href="javascript:;" class="text-white saveContract" data="0"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save Asset"><i class="fa fa-check text-white"   ></i> </a>
                                            <a  data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""   data-original-title="Close" href="javascript:;" class="text-white btnClose"><i class="fa fa-times texti-white"   ></i> </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @else
                         <div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="{{asset('public/img/header-asset-white.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>
                                <?php       $renewed_qry=DB::Table('users')->Where('id',$qry->InactiveBy)->first(); 

 
 ?>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">On <?php echo date('Y-M-d',strtotime($qry->InactiveDate)) ?> by {{@$renewed_qry->firstname.' '.@$renewed_qry->lastname}}</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="{{asset('public/img/paper-clip-white.png')}}" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="{{asset('public/img/comment-white.png')}}" width="20px"></a>
                                         </span>
                                        <!--    <a href="javascript:;"  class="saveContract text-white"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add and Continue"  class="text-white"><i class="fa fa-plus texti-white"  ></i> </a> -->
 
                                              <a href="javascript:;" class="text-white saveContract" data="0"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save Asset"><i class="fa fa-check text-white"   ></i> </a>
                                            <a  data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""   data-original-title="Close" href="javascript:;" class="text-white btnClose"><i class="fa fa-times texti-white"   ></i> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                <!-- Page Content -->
                <?php $asset_type_q=DB::table('asset_type')->where('asset_type_id',$qry->asset_type_id)->first();

                $asset_type=$asset_type_q->asset_type_description;
                ?>      
                <div class="content content-full  -boxed" style="    padding-left: 15mm;
    padding-right: 15mm;">
                    <!-- New Post -->
                    <form  id="form-1" action="{{url('insert-contract')}}" class="js-validation   " method="POST" enctype="multipart/form-data"  >
                        @csrf
                     <input type="hidden" name="asset_type" value="{{$type}}">
                    <input type="hidden" name="id" value="{{$qry->id}}">
                        <div class="block new-block" >
  <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >General Information
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">
                             
                                <div class="row justify-content-  push">
 
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-12">
                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Asset Type</label>
                                            <?php
                                            $asset_type_qry=DB::Table('asset_type')->where('is_deleted',0)->orderBy('asset_type_description','asc')->get();
                                             ?>
                                            <div class="col-sm-5">
                                                <select type="" class="form-control select2" id="asset_type_id"  value="" name="asset_type_id"   >
                                                    <option value=""></option>
                                                    @foreach($asset_type_qry as $c)
                                                    <option value="{{$c->asset_type_id}}" data="{{$c->asset_type_description}}"  {{$qry->asset_type_id==$c->asset_type_id?'selected':''}}  >{{$c->asset_type_description}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>

                                         </div>
                                         

                                          <div class="form-group row">
                                            <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Client</label>
                                            <?php
                                            
                                              $userAccess=explode(',',Auth::user()->access_to_client);

                                            if(Auth::user()->role=='admin'){
                                            $client=DB::Table('clients')->where('is_deleted',0)->where('client_status',1)->orderBy('firstname','asc')->get();
                                            }
                                            else{
                                                $client=DB::Table('clients')->whereIn('id',$userAccess)->where('is_deleted',0)->where('client_status',1)->orderBy('firstname','asc')->get();   
                                            }                                             ?>
                                            <div class="col-sm-5">
                                                 <select type="client_id" class="form-control select2" id="client_id"  value="" name="client_id" placeholder="Client"  >
                                                    <option value=""  data-logo="" data-email="" data-address=""></option>
                                                    @foreach($client as $c)
                                                    <option value="{{$c->id}}" data-logo="{{$c->logo}}"  data-email="{{$c->email_address}}" data-renewal_notification_email="{{$c->renewal_notification_email}}" data-address="{{nl2br($c->client_address)}}"  {{$qry->client_id==$c->id?'selected':''}}>{{$c->firstname}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>

                                         </div>
                                         
                                  <div class="form-group row">
                                            <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Site</label>
                                          
                                            <div class="col-sm-5">
                                                <select type="" class="form-control select2" id="site_id"  value="" name="site_id"   >
                                                    <option value="">Select Site</option>
                                                    <?php $site=DB::table('sites')->where('is_deleted',0)->where('client_id',$qry->client_id)->orderBy('site_name','asc')->get();?>
                                                    @foreach($site as $s)
                                                    <option value="{{$s->id}}" {{$qry->site_id==$s->id?'selected':''}}>{{$s->site_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                          
                                        </div>


                                            <div class="form-group row {{$type=='virtual'?'d-none':''}}">
                                            <label class="col-sm-2 col-form-label  " for="example-hf-client_id">Location</label>
                                          
                                            <div class="col-sm-10">
                                               <input type="text" class="form-control" value="{{$qry->location}}" id="location" name="location" >
                                            </div>
                                          
                                        </div>
                                    </div>

                                      
                                               </div>      
 
                 
                 </div>
             </div>
         </div>
     </div>


    <div class="block new-block  " >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Host Information

                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group storageHide  {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Operating System
 </label>
 <?php
                                            $operating_system=DB::Table('operating_systems')->where('is_deleted',0)->orderBy('operating_system_name','asc')->get();
                                             ?>
                                            <div class="col-sm-10">
                                                <select type="" class="form-control select2" id="os"  value="" name="os"   >
                                                    <option value=""></option>
                                                      @foreach($operating_system as $c)
                                                    <option value="{{$c->id}}" {{$qry->os==$c->id?'selected':''}}>{{$c->operating_system_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                        </div>

                                         
                                         <div class="row form-group storageHide1">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Storage Controller
 </label>
 
                                            <div class="col-sm-10">
                                           <?php
                                             
                                            $asset=DB::Table('assets as a')->leftjoin('asset_type as t','a.asset_type_id','=','t.asset_type_id')->where('t.asset_type_description','Storage Controller')->where('a.is_deleted',0)->where('a.client_id',$qry->client_id) ->orderBy('a.hostname','asc')->get();
                                           
                                             ?>
 <select type="" class="form-control select2" id="parent_asset"  value="" name="parent_asset"   >
                                                    <option value="">Select Type</option>
                                                    @foreach($asset as $c)
                                                    <option value="{{$c->id}}"  {{$qry->parent_asset==$c->id?'selected':''}}  >{{$c->fqdn}}</option>
                                                    @endforeach
                                                    </select>
 
                                            </div>
                                        </div>

                                             
                                           
 
                                    
                                         <div class="form-group row storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}">
                                            <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Hostname
</label>
                                            <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="hostname"  value="{{$qry->hostname}}"  name="hostname"    >
                                            </div>
                                       
                                                <label class="col-sm-2 mandatory   text-center col-form-label" for="example-hf-email">Domain</label>
                                            <div class="col-sm-4">
                                             <select type="text" class="form-control select2" id="domain" name="domain" placeholder=""  >
                                                        <option value=""></option>
                                                           <?php $domain=DB::table('domains')->where('is_deleted',0)->where('client_id',$qry->client_id)->orderBy('domain_name','asc')->get();?>
                                                    @foreach($domain as $s)
                                                    <option value="{{$s->id}}" {{$qry->domain==$s->id?'selected':''}}>{{$s->domain_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                           <div class="row form-group storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Environment
 </label>
  
                                            <div class="col-sm-10">
                                                  <input type="text" class="form-control" list="useDatalist"   value="{{$qry->use_}}"  id="use_" name="use_" placeholder="Enter Use"  > 
                                                 <?php $use=DB::Table('assets')->select(DB::raw('distinct(use_) as use_'))->get();
                                  
                                                     ?> <datalist id="useDatalist">

                                                    @foreach($use as $u)
                                                        <option value="{{$u->use_}}">
                                                    @endforeach

                                                </datalist>
                                            </div>
                                        </div>

                                            <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Role/Description
 </label>
  
                                            <div class="col-sm-10">
                                           <input type="text" class="form-control"  id="role"  value="{{$qry->role}}"  name="role" placeholder="Enter Role"  > 
                                            </div>
                                        </div>

         
<div class="row storageHide1 ">
    
        <label class="col-sm-2">Support Status</label>
 <div class="col-sm-4  ">

<div class="contract_type_button  w-100 mr-4"  >
      <input type="checkbox" class="custom-control-input" id="HasWarranty1" name="HasWarranty" value="1"   {{$qry->HasWarranty==1?'checked':''}}  >
  <label class="btn btn-new w-75 supported " for="HasWarranty1" data-toggle="tooltip" data-trigger="hover" data-html="true"   title="{{$qry->HasWarranty==1?'Allows you to assign
contracts to asset':$qry->NotSupportedReason}}"  > Supported</label>
</div>
</div>
</div>
 
</div>
</div>




   <div class="block new-block storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}" >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Critical Information
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group ">
                                                       <div class="col-sm-4 t er">

<div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="disaster_recovery1" name="disaster_recovery1" value="1"   >
  <label class="btn btn-new w-75 " for="disaster_recovery1" >D/R Plan</label>
</div>
</div>

 <div class="col-sm-4 text-center">

<div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="ntp" name="ntp" value="1"  {{$qry->ntp==1?'checked':''}}  >
  <label class="btn btn-new w-75 " for="ntp"  data-toggle="tooltip" data-trigger="hover" data-html="true"  title="Allows you to assign SSL Certs to asset" > SSL Certificate</label>
</div>
</div>


 <div class="col-sm-4 text-right ">

<div class="contract_type_button  w-100 mr-4 mb-3"  >
      <input type="checkbox" class="custom-control-input" id="HasWarranty" name="HasWarranty" value="1"   {{$qry->HasWarranty==1?'checked':''}}  >
  <label class="btn btn-new w-75 supported " for="HasWarranty" data-toggle="tooltip" data-trigger="hover" data-html="true"   title="{{$qry->HasWarranty==1?'Allows you to assign contracts to asset':$qry->NotSupportedReason}}"   > {{$qry->HasWarranty==1?'Supported':'Unsupported'}}</label>
</div>
</div>


 <div class="col-sm-4  ">

<div class="contract_type_button  w-100 mr-4  "     >
          <input type="checkbox" class="custom-control-input" id="clustered" name="clustered" value="1"   {{$qry->clustered==1?'checked':''}} >
  <label class="btn btn-new w-75 " for="clustered" > Clustered</label>
</div>
</div>

 <div class="col-sm-4 text-center">

<div class="contract_type_button  w-100 mr-4  "     >
     <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" value="1"   {{$qry->internet_facing==1?'checked':''}}  >

       <label class="btn btn-new w-75 " for="internet_facing" > Internet Facing</label>
</div>
</div>




 <div class="col-sm-4  text-right">

<div class="contract_type_button  w-100 mr-4 "     >
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1"  {{$qry->load_balancing==1?'checked':''}} >
  <label class="btn btn-new w-75 " for="load_balancing" > Load Balanced</label>
</div>
</div>


</div>
                                        </div>

                                         
                                     
                                             
                                                

                                         
                                             
                                           
 
                                
 
</div>
 
                          
<div class="modal fade" id="UnsupportedModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header   ">
                            <span class="b e section-header">Unsupported Asset: Reason</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content pt-0 row">
                            
                  
 
<div class="col-sm-12    p d-flex justify-content-between     ">
     
<div class="contract_type_button ">
  <input type="radio" id="NotSupportedReason1" name="NotSupportedReason"  {{$qry->NotSupportedReason=='N/A'?'checked':''}}   value="N/A"/>
  <label class="btn btn-new " for="NotSupportedReason1" >N/A</label>
</div>
<div class="contract_type_button">
  <input type="radio" id="NotSupportedReason2" name="NotSupportedReason" value="End Of Life"   {{$qry->NotSupportedReason=='End Of Life'?'checked':''}}   />
  <label class="btn btn-new ml-2" for="NotSupportedReason2"   >End Of Life</label>
</div>

           <div class="contract_type_button">
  <input type="radio" id="NotSupportedReason3" name="NotSupportedReason"  value="Other Partner"   {{$qry->NotSupportedReason=='Other Partner'?'checked':''}}   />
  <label class="btn btn-new ml-2" for="NotSupportedReason3"   >Other Partner</label>
</div>

    <div class="contract_type_button">
  <input type="radio" id="NotSupportedReason4" name="NotSupportedReason"  value="Forgone"   {{$qry->NotSupportedReason=='Forgone'?'checked':''}}   />
  <label class="btn btn-new ml-2" for="NotSupportedReason4"   >Forgone</label>
</div>

          

            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="button" class="btn mr-3 btn-new" id="UnsupportedSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" id="UnsupportedClose"  data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>


    <div class="block new-block  " >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Hardware

                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
        @if($type=='physical')
                                      <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Manufacturer
 </label>
    <?php
                                            $manufacturer=DB::Table('vendors')->where('is_deleted',0)->orderBy('vendor_name','asc')->get();
                                             ?>
                                            <div class="col-sm-4">
                                               <select type="" class="form-control select2" id="manufacturer"  value="" name="manufacturer"  >
                                                    <option value=""></option>
                                                    @foreach($manufacturer as $c)
                                                    <option value="{{$c->id}}"   {{$qry->manufacturer==$c->id?'selected':''}} >{{$c->vendor_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                        </div>

                                         
                                     
                                             
                                           
 
                                    
                                         <div class="form-group row">
                                            <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Model
</label>
                                            <div class="col-sm-4">
                                                         <input type="text" class="form-control" list="modelDatalist"     id="model" name="model"   value="{{$qry->model}}"  > 
                                                <datalist id="modelDatalist">
                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(model) as model'))->get(); 

                                                    ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->model}}"></option>
                                                    @endforeach

                                                </datalist>
                                            </div>
                                        </div>
                                            <div class="form-group row">
                                       
                                                <label class="col-sm-2 mandatory  col-form-label" for="example-hf-email">Type</label>
                                            <div class="col-sm-4">
                                                      <input type="text" class="form-control" list="typeDatalist"   id="type" name="type"   value="{{$qry->type}}"   > 
                                                <datalist id="typeDatalist">
                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(type) as type'))->get(); ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->type}}"></option>
                                                    @endforeach

                                                </datalist>
                                            </div>
                                        </div>

                                           <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Serial Number

 </label>
  
                      <div class="col-sm-4">
                <input type="text" class="form-control valid"  id="sn" name="sn"    value="{{$qry->sn}}" aria-invalid="false">
                                            </div>
                                        </div>

                                            <div class="row form-group cpuDiv  {{$asset_type=='Physical Server'?'':'d-none'}}">
                                                         <label class="col-sm-2    col-form-label" for="example-hf-email">CPU Model

 </label>
  
                                            <div class="col-sm-4">
                                              <input type="text" class="form-control" list="cpuDatalist"  id="cpu_model" name="cpu_model"     value="{{$qry->cpu_model}}" > 
                                                <datalist id="cpuDatalist">

                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(cpu_model) as cpu_model'))->get(); ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->cpu_model}}"></option>
                                                    @endforeach

                                                </datalist>
                                            </div>
                                        </div>

                                             <div class="row form-group cpuDiv  {{$asset_type=='Physical Server'?'':'d-none'}} ">
                                           <label class="col-sm-2    col-form-label" for="example-hf-email">No. of Sockets </label>
                                              <div class="col-sm-2">
                                                <input type="number" class="form-control"  value="{{$qry->cpu_sockets}}"   id="cpu_sockets" name="cpu_sockets"   > 
                                            </div>
                                              <label class="col-sm-2  text-center  col-form-label" for="example-hf-email">No. of Cores </label>
                                              <div class="col-sm-2">
                                            <input type="number" class="form-control"   id="cpu_cores" name="cpu_cores"    value="{{$qry->cpu_cores}}"   > 
                                            </div>
                                              <label class="col-sm-2 text-center    col-form-label" for="example-hf-email">Frequency (GHz)
 </label>
                                              <div class="col-sm-2">
                                              <input type="number" class="form-control"   id="cpu_freq" name="cpu_freq"  value="{{$qry->cpu_freq}}" > 
                                            </div>
                                        </div>
                                           <div class="row form-group storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}">
                                           <label class="col-sm-2  mandatory  col-form-label" for="example-hf-email">Memory (Gb)</label>
                                              <div class="col-sm-2">
                                               <input type="number" class="form-control" id="memory" name="memory"   value="{{$qry->memory}}" >
                                            </div>
                                            
                                           
                                        </div>
                                            @else
<div class="row form-group  ">
                                           <label class="col-sm-2  mandatory  col-form-label" for="example-hf-email">vCPUs</label>
                                              <div class="col-sm-2">
                                           <input type="number" class="form-control" id="vcpu" name="vcpu"  value="{{$qry->vcpu}}"  >     
                                            </div>
                                            
                                           
                                        </div>

 <div class="row form-group storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}} ">
                                           <label class="col-sm-2  mandatory  col-form-label" for="example-hf-email">Memory (Gb)</label>
                                              <div class="col-sm-2">
                                               <input type="number" class="form-control" value="{{$qry->memory}}" id="memory" name="memory"    >
                                            </div>
                                            
                                           
                                        </div>

                                        @endif

         
 
</div>
</div>


<div class="block new-block storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}" >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Networking
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content pb-0 new-block-content">
                             
                                <div class="row justify-content-  push">
 
                            <div class="col-sm-12 m-
                            " >
                                     
                     <div class="row">
 
                                    <div class="col-sm-8">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label mandatory" for="example-hf-client_id">vLAN ID </label>
                                                                   
                                            <div class="col-sm-5">
                                                <select type="number" class="form-control select2" value="{{old('vlan_id')}}" id="vlan_id" name="vlan_id" placeholder=""  >
                                    <?php
                                      
                                                     $vlan=DB::Table('network')->select('network.*','nz.network_zone_description','nz.tag_back_color','nz.tag_text_color')->where('network.is_deleted',0) ->where('network.client_id',$qry->client_id)->where('network.site_id',$qry->site_id)
->leftjoin('network_zone as nz','nz.network_zone_description','=','network.zone')->orderBy('vlan_id','asc')->get();
                                

                                                     ?>
                                                    @foreach($vlan as $s)
                                                        <option value="{{$s->id}}"  data-subnet_ip="{{$s->subnet_ip}}" data-mask="{{$s->mask}}"  data-description="{{$s->description}}" data-gateway_ip="{{$s->gateway_ip}}" data-ssid_name="{{$s->ssid_name}}"  data-bg="{{$s->tag_back_color}}" data-color="{{$s->tag_text_color}}" data-zone="{{$s->zone}}"  {{$qry->vlan_id==$s->id?'selected':''}}>{{$s->vlan_id
                                                        }}</option>
                                                    @endforeach
                              </select>
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label mandatory" for="example-hf-client_id">Primary IP
</label>
                                          
                                            <div class="col-sm-5">
                                                <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="CIDR FORMAT" value="{{$qry->ip_address}}" >
                                            </div>
                                          
                                        </div>
                                    </div>

                                   
                                     <div class="col-lg-4" id="vLANDiv"> 
                                  
                                      @if($qry->vlan_id!='')
                                      <?php   $vlan_first=DB::Table('network')->select('network.*','nz.network_zone_description','nz.tag_back_color','nz.tag_text_color')->where('network.is_deleted',0) ->where('network.client_id',$qry->client_id)->where('network.site_id',$qry->site_id)
->leftjoin('network_zone as nz','nz.network_zone_description','=','network.zone')->where('network.id',$qry->vlan_id)->orderBy('network.vlan_id','asc')->first();
                                    
                                      ?>
                                   <div class="js-task   block block-rounded mb-2   animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                  
                                                    <td class="js-task-content w-50  ">
                                                        <h2 class="mb-0 comments-text">{{$vlan_first->subnet_ip}} {{$vlan_first->mask}}<br><span class="comments-subtext">{{$vlan_first->description}}


</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 100px;">
                                                       <!-- -->
                                                       <div  >
                                                       <div class="  text-center font-size-md bubble-white-new border-none bubble-text-sec px-2 ml-auto pb-0    " style="background:{{$vlan_first->tag_back_color}};color:{{$vlan_first->tag_text_color}};width:fit-content!important;border-radius:5px;min-height:32px!important;border:none;">{{$vlan_first->zone}}</div>
                                                        </div> 
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td   class="d-flex w-50 align-items-center">

                                                              <p class="mb-0 mr-3  text-white   px-2 " style="width: fit-content;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>gw</b></p>
                                                             <h5 class="text-primary mb-0">
                                                    {{$vlan_first->gateway_ip}}
                                                </h5>
                                                </td>
                                                     <td    class="w-50" >
                                                                <div  class="d-flex align-items-center"> 
                                                              <p class="mb-0 mr-3  text-white   px-2 " style="width: fit-content;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>Mask</b></p>
                                                    
                                                    <h5 class="text-primary mb-0">{{$vlan_first->mask}}</h5>
                                                </div>
                                                </td>
                                                    </tr>
                                        
                                        </tbody> 

                                    </table>

                                    </div>  

@endif
                                </div>
                                           


       

                                        <div class="col-sm-12">
                                      
                                                <div id="IpBlock" class="IpHide">  
                                                     
                               
                                                   </div>

                                                    <button type="button" data-toggle="modal" data-target="#IpModal" class="btn IpHide ml-5 mt-3 btn-new ">Add IP Address</button>
                 
                 </div>
             </div>
         </div>
     </div>
</div>
</div> 



              


   <div class="block new-block  storageHide {{@$parent_type->asset_type_description=='Storage Expansion'?'d-none':''}}  "  >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Managed Services
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group ">
                                   
                                                <label class="col-sm-2 col-form-label " for="example-hf-client_id">Status</label>
                                       
                                                       <div class="col-sm-3  r">

<div class="contract_type_button  w-100 mr-4  ">
       <input type="checkbox" class="custom-control-input" id="managed" name="managed" value="1"  {{$qry->managed==1?'checked':''}}   >
  <label class="btn btn-new w-75 managed" for="managed">{{$qry->managed==1?'Managed':'Unmanaged'}} </label>
</div>
</div>
 

</div>



<div class="ManagedBlock {{$qry->managed==1?'':'d-none'}} ">
<div class="form-group row">
                                            <label class="col-sm-2   col-form-label" for="example-hf-email">App Owner
</label>
                                            <div class="col-sm-4">
                                                      <input type="text" class="form-control"  id="app_owner" list="app_ownerDatalist" name="app_owner" placeholder="Enter App Owner"  value="{{$qry->app_owner}}"> 
                                          
                                              <datalist id="app_ownerDatalist">
                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(app_owner) as app_owner'))->get(); ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->app_owner}}"></option>
                                                    @endforeach

                                                </datalist>

                                                  </div>
                                       
                                                <label class="col-sm-2 text-center  col-form-label" for="example-hf-email">SLA</label>
                                            <div class="col-sm-4">
                                               <input type="text" class="form-control" list="slaDatalist"  id="sla" name="sla" placeholder=""  value="{{$qry->sla}}"  > 
                                                <datalist id="slaDatalist">
                                                    <?php $use=DB::Table('assets')->select(DB::raw('distinct(sla) as sla'))->get(); ?>
                                                    @foreach($use as $u)
                                                        <option value="{{$u->sla}}"></option>
                                                    @endforeach

                                                </datalist>
                                            </div>
                                        </div>

                                        <div class="row">

 <div class="col-sm-3 mb-3 ">

<div class="contract_type_button  w-100 mr-4  px-4"     data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System is patched automatically or manually" >
          <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1"  {{$qry->patched==1?'checked':''}}  >
  <label class="btn btn-new w-100 " for="patched" > Patched</label>
</div>
</div>

 <div class="col-sm-3   mb-3">

<div class="contract_type_button  w-100 mr-4  px-4"       data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System is monitored">
       <input type="checkbox" class="custom-control-input" id="monitored" name="monitored" value="1"  {{$qry->monitored==1?'checked':''}}  >
       <label class="btn btn-new w-100 " for="monitored" >Monitored</label>
</div>
</div>

 <div class="col-sm-3  mb-3">

<div class="contract_type_button  w-100 mr-4 px-4 "      data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System data is protected">
        <input type="checkbox" class="custom-control-input" id="backup" name="backup" value="1"     {{$qry->backup==1?'checked':''}}>
       <label class="btn btn-new w-100 " for="backup" >Backup</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button  w-100 mr-4 px-4 "      data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System has Anti-Virus installed" >
           <input type="checkbox" class="custom-control-input" id="antivirus" name="antivirus" value="1"   {{$qry->antivirus==1?'checked':''}} >
       <label class="btn btn-new w-100 " for="antivirus" >Anti-Virus
</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button  w-100 mr-4  px-4"     data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System is replicated"  >
          <input type="checkbox" class="custom-control-input" id="replicated" name="replicated" value="1"   {{$qry->replicated==1?'checked':''}}  >
       <label class="btn btn-new w-100 " for="replicated" >Replicated
</label>
</div>
</div>

 <div class="col-sm-3 ">

<div class="contract_type_button  w-100 mr-4 px-4 "    data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System is scanned by Drawbridge"   >
          <input type="checkbox" class="custom-control-input" id="disaster_recovery" name="disaster_recovery" value="1"  {{$qry->disaster_recovery==1?'checked':''}}  >
       <label class="btn btn-new w-100 " for="disaster_recovery" >Vulnerability Scan</label>
</div>
</div>

 <div class="col-sm-3  ">


<div class="contract_type_button  w-100 mr-4  px-4"    data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System sends info to SIEM/Syslog"    >
                     <input type="checkbox" class="custom-control-input" id="syslog" name="syslog" value="1"   {{$qry->syslog==1?'checked':''}} >
       <label class="btn btn-new w-100 " for="syslog" >SIEM</label>
</div>
</div>

 <div class="col-sm-3  ">

<div class="contract_type_button  w-100    px-4 "      data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="System requires SMTP Relay Access"  >
      <input type="checkbox" class="custom-control-input" id="smtp" name="smtp" value="1"  {{$qry->smtp==1?'checked':''}}  >
       <label class="btn btn-new w-100 " for="smtp" >SMTP</label>
</div>
</div>

 
</div>
</div>
                                        </div>

                 
</div>
   



                                 
  



       


     <div class="block new-block  commentDiv d-none " >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Comments
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content" id="commentBlock">
                             
                                            
                               
 </div>
 </div> 


     <div class="block new-block attachmentDiv d-none   " >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Attachments
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content row" id="attachmentBlock">
                                           
                                   
                             
 </div> 
 
                        </div>
                    </form>
                    <!-- END New Post -->
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
    

             <div class="modal fade" id="IpModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header  ">
                            <span class="b e section-header">Add IP Address</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content row">
                            
                  
<div class="col-sm-3   form-group      ">
                <input type=" " class="form-control" value="" name="ip_address_name" style="    border: navajowhite;outline: none!important;border-bottom: 1px solid #d4dcec;border-radius:0px"  ><label class=" mandatory">Description</label>

</div>
<div class="col-sm-9   form-group      ">
         <input     class="form-control  "  required="" name="ip_address_value"   >
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="IpSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>

               <div class="modal fade" id="IpModalEdit" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                     <input  type="hidden"   class="form-control  "    name="ip_id_edit"   >
         
                    <div class="block  block-transparent mb-0">
                        <div class="block-header  ">
                            <span class="b e section-header">Edit IP Address</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content row">
                  
<div class="col-sm-3   form-group      ">
                <input type=" " class="form-control" value="" name="ip_address_name_edit" style="    border: navajowhite;outline: none!important;border-bottom: 1px solid #d4dcec;border-radius:0px"  ><label class=" mandatory">Description</label>

</div>
<div class="col-sm-9   form-group      ">
         <input     class="form-control  "  required="" name="ip_address_value_edit"   >
                 
            </div>
   
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="IpSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>

<div class="modal fade" id="CommentModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header   ">
                            <span class="b e section-header">Add Comments</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content pt-0 row">
                            
                  
 
<div class="col-sm-12    p      ">
         <textarea     class="form-control  "   rows="4" required="" name="comment"   ></textarea>
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="CommentSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>

               <div class="modal fade" id="CommentModalEdit" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header  ">
                            <span class="b e section-header">Edit Comments</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content pt-0 row">
                            
                  <input type="hidden" name="comment_id_edit" >

<div class="col-sm-12      ">
         <textarea     class="form-control  "   rows="4" required="" name="comment_edit"   ></textarea>
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="CommentSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>




 

















<div class="modal fade" id="HostModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Add Host </span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            

   
   <div class="row">
     <div class="col-sm-2   form-group      ">
                <label class="mandatory">Hostname</label>
 
</div>
<div class="col-sm-10   form-group      ">
                <select type="" class="form-control selectpicker"    data-live-search="true"  id="hostname"  value="" name="cert_hostname"   >
                                                <option value="">Select Hostname</option>   
                                                    </select>  
            </div>
        </div>


 <div class="row">
<div class="col-sm-2   form-group      ">
                <label class="mandatory">Select IP(s)</label>

</div>
<div class="col-sm-10   form-group      ">
       
<div class="contract_type_button mr-5 mb-3">
  <input type="checkbox" class="ip_type primary_ip" id="ip_primary" name="ip_type[]"     value="Primary"/>
  <label class="btn btn-new " for="ip_primary" >Primary IP</label>
</div>

<div class="IpDiv">

    </div>
     </div>
 
            </div>
  

                         
                        </div>
                        <div class="block-content block-content-full   pt-1" style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="HostSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>




<div class="modal fade" id="HostModalEdit" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Edit Host</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
                     <input type="hidden" name="host_id_edit" >
 
   <div class="row">
     <div class="col-sm-2   form-group      ">
                <label class="mandatory">Hostname</label>
 
</div>
<div class="col-sm-10   form-group      ">
                <select type="" class="form-control selectpicker"   data-live-search="true"  id="hostname_edit"  value="" name="cert_hostname_edit"   >
                <option value="">Select Hostname</option>                                    
                                                    </select>  
            </div>
        </div>


 <div class="row">
<div class="col-sm-2   form-group      ">
                <label class="mandatory">Select IP(s)</label>

</div>
<div class="col-sm-10   form-group      ">
       
<div class="contract_type_button mr-4">
  <input type="checkbox" class="primary_ip_edit ip_type_edit" id="ip_primary_edit1" name="ip_type_edit[]"     value="Primary"/>
  <label class="btn btn-new " for="ip_primary_edit1" >Primary IP</label>
</div>
<div class="IpDivEdit">

    </div>
     </div>
 
            </div>
       
                        
                        <div class="block-content block-content-full   pt-1" style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="HostSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>



</div>











<div class="modal fade" id="AttachmentModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header   ">
                            <span class="b e section-header">Add Attachment</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content pt-0 row">
                            
                  
 
<div class="col-sm-12    p      ">
          <input     type="file" class="  attachment"  multiple="" style="" id="attachment" name="attachment" placeholder=""  >          
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="AttachmentSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" id="AttachmentClose"  data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>

               


 @endsection('content')

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
       <script src="{{asset('public/dashboard_assets/js/dashmix.app.min.js')}}"></script>

         
        <!-- Page JS Helpers (BS Datepicker + BS Colorpicker plugins) --> 
  
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript" defer="" src="{{asset('public/js/jquery.repeater.js')}}"></script>
 

 <script type="text/javascript">
    
    $(function(){

            FilePond.registerPlugin(
 
          FilePondPluginImagePreview,
        FilePondPluginImageExifOrientation,
        FilePondPluginFileValidateSize,
        FilePondPluginImageEdit,
        FilePondPluginFileValidateType
      );
   $('input[name=cert_type]').change(function(){
        if($(this).val()=='public'){
               $('.PublicDiv').removeClass('d-none')
        }
        else{
                $('.PublicDiv').addClass('d-none')   
        }

   })



$('#managed').change(function(){
    if($(this).prop('checked')==1){
        $('.ManagedBlock').removeClass('d-none');
    
        $('.managed').text('Managed');
    }
    else{
    $('.ManagedBlock').addClass('d-none');
    $('.managed').text('Unmanaged');
    }


})

$('#HasWarranty').change(function(){
    if($(this).prop('checked')==1){
               $('.supported').attr('data-original-title',"Allows you to assign contracts to asset");
               $('.supported').html('Supported');
    $('.supported').tooltip()
    }
    else{
$('#UnsupportedModal').modal('show');
    }


})
$('#HasWarranty1').change(function(){
           
    if($(this).prop('checked')==1){

         $('.supported').attr('data-original-title',"Allows you to assign contracts to asset");
         $('.supported').html('Supported');
    $('.supported').tooltip()
    }
    else{
$('#UnsupportedModal').modal('show');
 
    }


})

 

$('#vlan_id').change(function(){
 
var val=$('#vlan_id').val();
$('#vlan_id').select2('destroy');
var subnet_ip=$('option:selected',$(this)).attr('data-subnet_ip');
var mask=$('option:selected',$(this)).attr('data-mask');
var description=$('option:selected',$(this)).attr('data-description');
var gateway_ip=$('option:selected',$(this)).attr('data-gateway_ip');
var ssid_name=$('option:selected',$(this)).attr('data-ssid_name');
var zone=$('option:selected',$(this)).attr('data-zone');
var bg=$('option:selected',$(this)).attr('data-bg');
var color=$('option:selected',$(this)).attr('data-color');

subnet_ip=subnet_ip=='null'?'':subnet_ip;
mask=mask=='null'?'':mask;
description=description=='null'?'':description;
gateway_ip=gateway_ip=='null'?'':gateway_ip;
ssid_name=ssid_name=='null'?'':ssid_name;
zone=zone=='null'?'':zone;

$('#vlan_id').select2();
if(val!=''){
    var html=`     <div class="js-task   block block-rounded mb-2   animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                  
                                                    <td class="js-task-content w-50  ">
                                                        <h2 class="mb-0 comments-text">${subnet_ip} ${mask}<br><span class="comments-subtext">${description}


</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 100px;">
                                                       <!-- -->
                                                       <div  >
                                                             <div class="text-center font-size-md bubble-white-new border-none bubble-text-sec px-2 ml-auto pb-0    " style="background:${bg};color:${color};width:fit-content!important;border-radius:5px;min-height:32px!important;border:none;">
                                                                               <span class=" ">${zone}</span>
                                                                    </div>

                                                        </div> 
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td   class="d-flex w-50 align-items-center">

                                                              <p class="mb-0 mr-3  text-white   px-2 " style="width: fit-content;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>gw</b></p>
                                                             <h5 class="text-primary mb-0">
                                                    ${gateway_ip}
                                                </h5>
                                                </td>
                                                     <td    class="w-50" >
                                                                <div  class="d-flex align-items-center"> 
                                                              <p class="mb-0 mr-3  text-white   px-2 " style="width: fit-content;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>Mask</b></p>
                                                    
                                                    <h5 class="text-primary mb-0">${mask}</h5>
                                                </div>
                                                </td>
                                                    </tr>
                                        
                                        </tbody> 

                                    </table>

                                    </div>  `;
                                    $('#vLANDiv').html(html)
}
})
$('#UnsupportedSave').click(function() {
        var not_supported=$('input[name=NotSupportedReason]:checked').val();
        // if(not_supported=='N/A' || not_supported==''){
        //         Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> You must select a reason', delay: 5000});
        //             $('.supported').html('Supported');
        // }
        // else{
          $('#UnsupportedModal').modal('hide');
          $('.supported').attr('data-original-title',"Reason : "+not_supported)
          $('.supported').tooltip();
          $('.supported').html('Unsupported');

             $('#HasWarranty').prop('checked',false)
              
              $('#HasWarranty1').prop('checked',false)

        //}
})
$('#UnsupportedClose').click(function() {
          var not_supported=$('input[name=NotSupportedReason]:checked').val();
         
        if(not_supported=='N/A' || not_supported==''){
$('#HasWarranty').prop('checked',1)
$('#HasWarranty1').prop('checked',1)
}
})
$('#contract_end_date').flatpickr()

var content3_image=[];

 
var attachments_file=[];
 
  let filePond =  FilePond.create(
        document.querySelector('.attachment'),
        {
          name: 'attachment',
            allowMultiple: true,
            allowImagePreview:true,
 
 imagePreviewFilterItem:false,
 imagePreviewMarkupFilter:false,  

        dataMaxFileSize:"2MB",
      
 
 
          // server
          server: { 
               process: {
            url: '{{url('uploadContractAttachment')}}',
            method: 'POST',
             headers: {
              'x-customheader': 'Processing File'
            },
            onload: (response) => {
        
              response =  response.replaceAll('"','') ;
            content3_image.push(response);
                        
                var attachemnts=$('#attachment_array').val()
                var attachment_array=attachemnts.split(',');
                    attachment_array.push(response);
                    $('#attachment_array').val(attachment_array.join(','));

              return response;

            },
            onerror: (response) => {
       
               
            
              return response
            },
            ondata: (formData) => {
              window.h = formData;
                
              return formData;
            }
          },
               revert: (uniqueFileId, load, error) => {
           
            const formData = new FormData();
            formData.append("key", uniqueFileId);
 
        content3_image=content3_image.filter(function(ele){ 
            return ele != uniqueFileId; 
        });
                    
                var attachemnts=$('#attachment_array').val()
                var attachment_array=attachemnts.split(','); 
                       attachment_array=  attachment_array.filter(function(ele){ 
            return ele != uniqueFileId; 
        });

                    $('#attachment_array').val(attachment_array.join(','));

 
            fetch(`{{url('revertContractAttachment')}}?key=${uniqueFileId}`, {
              method: "DELETE",
              body: formData,
            })
              .then(res => res.json())
              .then(json => {
                console.log(json);
               

                  // Should call the load method when done, no parameters required

                  load();
            
              })
              .catch(err => {
                console.log(err)
                // Can call the error method if something is wrong, should exit after
                error(err.message);
              })
          },
             
     
         
              remove: (uniqueFileId, load, error) => {
            // Should somehow send `source` to server so server can remove the file with this source
   content3_image=content3_image.filter(function(ele){ 
            return ele != uniqueFileId; 
        });
     

            // Should call the load method when done, no parameters required
            load();
        },
      
    }
        }
      );
 


 


           @if(Session::has('success'))
              
             Dashmix.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: '{{Session::get('success')}}'});


             @endif




$(".contract_type_button label").mouseout(function(){
        
         $('.tooltip').tooltip('hide');
    
    })

// EMAIL ARRAY

var ipArray=[];
var ip_key_count=0;
$('#IpSave').click(function(){
var ip_address_name=$('input[name=ip_address_name]').val();
var ip_address_value=$('input[name=ip_address_value]').val();
     if(ip_address_name==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter label.', delay: 5000});

    }
      else if(ip_address_value==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter an IP Address.', delay: 5000});

    }
    else{

           var l=ipArray.length;
                
           ipArray.push({key:ip_key_count,ip_address_name:ip_address_name,ip_address_value:ip_address_value});
           showIp()   
           $('#IpModal').modal('hide')
           $('input[name=ip_address_name]').val('')
                 $('input[name=ip_address_value]').val('')
           ip_key_count++;
   
    }
})


$('#IpSaveEdit').click(function(){
var ip_address_name=$('input[name=ip_address_name_edit]').val();
var ip_address_value=$('input[name=ip_address_value_edit]').val();
var id=$('input[name=ip_id_edit]').val();
       if(ip_address_name==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter   label.', delay: 5000});

    }
     else  if(ip_address_value==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter an IP Address.', delay: 5000});

    }
    else{

           var l=ipArray.length;
                    
           ipArray[id].ip_address_name=ip_address_name;
                  ipArray[id].ip_address_value=ip_address_value;

           showIp()   
           $('#IpModalEdit').modal('hide')
       $('input[name=ip_address_name]').val('')
                 $('input[name=ip_address_value]').val('')
       
    }
})

$(document).on('click','.btnIpEmail',function(){
        var id=$(this).attr('data');
        $('#IpModalEdit').modal('show');
                $('input[name=ip_id_edit]').val(id);
        $('input[name=ip_address_name_edit]').val(ipArray[id].ip_address_name);
           $('input[name=ip_address_value_edit]').val(ipArray[id].ip_address_value);

})
var temp_ip=[];
$(document).on('click','.btnDeleteIp',function(){
    var id=$(this).attr('data');
            var key=ipArray[id].key;
     temp_ip.push(ipArray[id]);

     ipArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Ip address Deleted. <a href="javascript:;" class="  btn-notify btnIpUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showIp();
 
})

$(document).on('click','.btnIpUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_ip.filter(l=>l.key==id);
         
if (index[0]) { 
  ipArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_ip= temp_ip.filter(l=>l.key!=id);


      
    showIp(); 
    }
    })

 
function showIp(){
    var html='';  
    for(var i=0;i<ipArray.length;i++){
        html+=`   <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                        <p class="mb-0 mr-3  text-white   px-2 " style="min-width: 100px;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>${ipArray[i].ip_address_name}</b></p> 
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <label class="mb-0">${ipArray[i].ip_address_value}</label>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                        <a type="button"  data="${i}" data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="Edit" class="btnIpEmail  btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}">
                                                        </a>
                                                        <a type="button"   data="${i}" class="j e btn btn-sm btn-link btnDeleteIp text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
    }

     $('#IpBlock').html(html)
}

 // END EMAIL




// Comment ARRAY

var commentArray=[];
var comment_key_count=0;
$('#CommentSave').click(function(){
var comment=$('textarea[name=comment]').val();
     if(comment==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Comment', delay: 5000});

    }
    else{

           var l=commentArray.length;
                        if(l<5){
           commentArray.push({key:comment_key_count,comment:comment,date:'{{date('Y-M-d')}}',time:'{{date('h:i:s A')}}',name:'{{Auth::user()->firstname.''.Auth:: user()->lastname}}'});
           showComment()   
           $('#CommentModal').modal('hide')
           $('textarea[name=comment]').val('')
           comment_key_count++;
        }
    }
})


$('#CommentSaveEdit').click(function(){
var comment=$('textarea[name=comment_edit]').val();
var id=$('input[name=comment_id_edit]').val();
     if(comment==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Comment', delay: 5000});

    }
    else{

           var l=commentArray.length;
                    
           commentArray[id].comment=comment;
           showComment()   
           $('#CommentModalEdit').modal('hide')
           $('textarea[name=comment_edit]').val('')
       
    }
})

$(document).on('click','.btnEditComment',function(){
        var id=$(this).attr('data');
        $('#CommentModalEdit').modal('show');
                $('input[name=comment_id_edit]').val(id);
        $('textarea[name=comment_edit]').val(commentArray[id].comment);

})
var temp_comment=[];
$(document).on('click','.btnDeleteComment',function(){
    var id=$(this).attr('data');
            var key=commentArray[id].key;
     temp_comment.push(commentArray[id]);

     commentArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Comment Deleted. <a href="javascript:;" class="  btn-notify btnCommentUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showComment();
 
})

$(document).on('click','.btnCommentUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_comment.filter(l=>l.key==id);
         
if (index[0]) { 
  commentArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_comment= temp_comment.filter(l=>l.key!=id);


      
    showComment(); 
    }
    })

function showComment(){
    var html='';  
    if(commentArray.length>0){
        $('.commentDiv').removeClass('d-none');
    }
    else{
     $('.commentDiv').addClass('d-none');   
    }
    for(var i=0;i<commentArray.length;i++){
        html+=`    <div class="js-task block block-rounded mb-2 animated fadeIn"   data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{asset('public/img/profile-white.png')}}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${commentArray[i].name}<br><span class="comments-subtext">On ${commentArray[i].date} at ${commentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                         <a type="button"  data="${i}" class="j btnEditComment btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button"   data="${i}" class="btnDeleteComment btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a> 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ${commentArray[i].comment.replace(/\r?\n/g, '<br />')}
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
    }

     $('#commentBlock').html(html)
}

 // END Comment



 

















// Host ARRAY

var hostArray=[];
var host_key_count=0;
$('#HostSave').click(function(){
var host=$('select[name=cert_hostname]').val();
 
var asset_icon=$('option:selected',$('select[name=cert_hostname]')).attr('data1');
var asset_description=$('option:selected',$('select[name=cert_hostname]')).attr('data');
var asset_name=$('option:selected',$('select[name=cert_hostname]')).text();
asset_icon=asset_icon==null?'':asset_icon;
asset_description=asset_description==null?'':asset_description;
     
var ip=[];
$.each($('.ip_type'),function(){
    if($(this).prop('checked')==1){
        ip.push({name:$(this).val(),value:$(this).attr('data'),id:$(this).attr('data1')});
    }
})


     if(host==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> You must select a hostname.', delay: 5000});

    }
    else if(ip.length==0){
        Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> You must select at least one IP.', delay: 5000});

    }
    else{
 
           hostArray.push({key:host_key_count,hostname:host,asset_icon:asset_icon,asset_description:asset_description,asset_name:asset_name,ip:ip});
        console.log(hostArray)
              showHost()   
           $('#HostModal').modal('hide')
         $('select[name=cert_hostname]').val('');
         $('select[name=cert_hostname]').selectpicker('refresh');
         $.each($('.ip_type'),function(){
            $(this).prop('checked',false)
         })
         $('.ipDiv').html('');
           host_key_count++;
         
    }
})


$('#HostSaveEdit').click(function(){
    var id=$('input[name=host_id_edit]').val();
var host=$('select[name=cert_hostname_edit]').val();
      $('select[name=cert_hostname_edit').selectpicker('destroy');
var asset_icon=$('option:selected',$('select[name=cert_hostname_edit')).attr('data1');
var asset_description=$('option:selected',$('select[name=cert_hostname_edit')).attr('data');
var asset_name=$('option:selected',$('select[name=cert_hostname_edit')).text();
asset_icon=asset_icon==null?'':asset_icon;
asset_description=asset_description==null?'':asset_description;
    $('select[name=cert_hostname_edit').val(host);
  $('select[name=cert_hostname_edit').selectpicker();
var ip=[];
$.each($('.ip_type_edit'),function(){
    if($(this).prop('checked')==1){
        ip.push({name:$(this).val(),value:$(this).attr('data'),id:$(this).attr('data1')});
    }
})


     if(host==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> You must select a hostname.', delay: 5000});

    }
    else if(ip.length==0){
        Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> You must select at least one IP.', delay: 5000});

    }
    else{

            
                    
           hostArray[id].hostname=host;
           hostArray[id].asset_icon=asset_icon;
           hostArray[id].asset_description=asset_description;
           hostArray[id].asset_name=asset_name;
 hostArray[id].ip=ip;



           showHost()   
           $('#HostModalEdit').modal('hide')
          $('select[name=cert_hostname_edit]').val('');
         $('select[name=cert_hostname_edit]').selectpicker('refresh');
         $.each($('.ip_type_edit'),function(){
            $(this).prop('checked',false)
         })
         $('.ipDivEdit').html('');
       
    }
})

$(document).on('click','.btnEditHost',function(){
        var id=$(this).attr('data');
        $('#HostModalEdit').modal('show');
                $('input[name=host_id_edit]').val(id);
     $('select[name=cert_hostname_edit]').val(hostArray[id].hostname);
$('select[name=cert_hostname_edit]').selectpicker('refresh')
$('.primary_ip_edit').attr('data',$('option:selected',$('select[name=cert_hostname_edit]')).attr('data2'));
var ipArray=hostArray[id].ip;
let m=ipArray.filter(l=>l.name=='Primary');
    if(m.length>0){
        $('.primary_ip_edit').prop('checked',1);
    }
    else{
     $('.primary_ip_edit').prop('checked',0);   
    }
    $.ajax({
        type:'get',
        data:{id:hostArray[id].hostname},
        url:'{{url('get-ip-hostname')}}',
        success:function(res){
                var html='';
                for(var i=0;i<res.length;i++){
                    var l=ipArray.filter(l=>l.id==res[i].id);

                    html+=`<div class="contract_type_button mr-5 mb-3">
                      <input type="checkbox" id="ip_primaryEdit${i}" class="ip_type_edit" name="ip_type[]"   ${l.length>0?'checked':''}  value="${res[i].ip_address_name}" data="${res[i].ip_address_value}" data1="${res[i].id}"/>
                      <label class="btn btn-new " for="ip_primaryEdit${i}" >${res[i].ip_address_name}</label>
                </div>`;

              }
              $('.IpDivEdit').html(html);  
        }

    })


})
var temp_host=[];
$(document).on('click','.btnDeleteHost',function(){
    var id=$(this).attr('data');
            var key=hostArray[id].key;
     temp_host.push(hostArray[id]);

     hostArray.splice(id,1);

    Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Host unassigned. <a href="javascript:;" class="  btn-notify btnHostUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showHost();
 
})

$(document).on('click','.btnHostUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_host.filter(l=>l.key==id);
         
if (index[0]) { 
  hostArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_host= temp_host.filter(l=>l.key!=id);


      
    showHost(); 
    }
    })

 
function showHost(){
    var html='';  

      if(hostArray.length>0){
        $('.hostDiv').removeClass('d-none');
    }
    else{
     $('.hostDiv').addClass('d-none');   
    }
    for(var i=0;i<hostArray.length;i++){

        var html2='';
        var ipArray=hostArray[i].ip;
        for(var j=0;j<ipArray.length;j++){
            html2+=` <div class="row">
                                                            <div class="col-sm-5">
                                                       <p class="pl-4 mb-1  text-primary">${ipArray[j].name}</p>
                                                    </div>
                                                    <div class="col-sm-7">
                                                           <p class=" mb-1  comments-section-text"> ${ipArray[j].value} </p>
                                                        </div>
                                                        </div>`;
        }

        html+=`<div class="col-lg-4">   <div class="js-task  block block-rounded mb-2   animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                         <img width="40px" height="40px" src="{{asset('public/asset_icon/${hostArray[i].asset_icon}')}}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${hostArray[i].asset_name}<br><span class="comments-subtext">${hostArray[i].asset_description}
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 100px;">
                                                       <!-- -->
                                                       <div style="position: absolute;right: 25px;top:5px;">
                                                            <a type="button" data="${i}" class="js- mx-0 px-0 btnEditHost  btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button" data="${i}" class=" btnDeleteHost mx-0 px-0  btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a> 
                                                        </div> 
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td  colspan="3"  >

                                                       ${html2}
                                                       
                                                    </td>
                                                  
                                        
                                        </tbody> 

                                    </table>

                                    </div> </div>`;
    }

     $('#HostBlock').html(html)
}

 // END EMAIL































// Contract Details ARRAY

var contractDetailsArray=[];
var contract_key_count=0;
$('#contractDetailsSave').click(function(){
 
var san=$('input[name=san]').val();
var san_type=$('input[name=san_type]:checked').val();



 
     if(san==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1">  Please enter a value for SAN', delay: 5000});

    }
    
    else{

           var l=contractDetailsArray.length;
                    
           var asset_array=[];
           $.each($('select[name=cert_hostname]_modal option:selected'),function(){
                    asset_array.push($(this).attr('data-hostname'));
           })


           contractDetailsArray.push({key:contract_key_count,san:san,san_type:san_type  });
           showcontractDetails()   
           $('#SANModal').modal('hide')
         
           $('input[name=san]').val('')
          
           contract_key_count++;
        }
   
})

$('select[name=cert_hostname]').change(function(){
    var id=$(this).val();

$('.primary_ip').attr('data',$('option:selected',$(this)).attr('data2'));
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('get-ip-hostname')}}',
        success:function(res){
                var html='';
                for(var i=0;i<res.length;i++){
                    html+=`<div class="contract_type_button mr-5 mb-3">
                      <input type="checkbox" id="ip_primary${i}" class="ip_type" name="ip_type[]"     value="${res[i].ip_address_name}" data="${res[i].ip_address_value}" data1="${res[i].id}"/>
                      <label class="btn btn-new " for="ip_primary${i}" >${res[i].ip_address_name}</label>
                </div>`;

              }
              $('.ipDiv').html(html);  
        }

    })
})


$('select[name=cert_hostname_edit]').change(function(){
    var id=$(this).val();

$('.primary_ip_edit').attr('data',$('option:selected',$(this)).attr('data2'));
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('get-ip-hostname')}}',
        success:function(res){
                var html='';
                for(var i=0;i<res.length;i++){
                    html+=`<div class="contract_type_button mr-5 mb-3">
                      <input type="checkbox" id="ip_primaryEdit${i}" class="ip_type_edit" name="ip_type[]"     value="${res[i].ip_address_name}" data="${res[i].ip_address_value}" data1="${res[i].id}"/>
                      <label class="btn btn-new " for="ip_primaryEdit${i}" >${res[i].ip_address_name}</label>
                </div>`;

              }
              $('.IpDivEdit').html(html);  
        }

    })
})
$('#contractDetailsSaveEdit').click(function(){
var san_edit=$('input[name=san_edit]').val();
var san_type_edit=$('input[name=san_type_edit]:checked').val();
 

var id=$('input[name=contract_id_edit]').val();
    if(san_edit==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for SAN', delay: 5000});

    }
    
    else{

           var l=contractDetailsArray.length;
                    
           contractDetailsArray[id].san=san_edit;
           contractDetailsArray[id].san_type=san_type_edit;
          

           showcontractDetails()   
           $('#SANModalEdit').modal('hide')
          
              
           $('input[name=san_edit]').val('')
     
       
    }
})

$(document).on('click','.btnEditContract',function(){
        var id=$(this).attr('data');
        $('#SANModalEdit').modal('show');
                $('input[name=contract_id_edit]').val(id);
 
         
           $('input[name=san_edit]').val(contractDetailsArray[id].san)
           $('input[name=san_type_edit][value='+contractDetailsArray[id].san_type+']').prop('checked',true)
            



})
var temp_contract=[];
$(document).on('click','.btnDeleteContract',function(){
    var id=$(this).attr('data');
            var key=contractDetailsArray[id].key;
     temp_contract.push(contractDetailsArray[id]);

     contractDetailsArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Contract details Deleted. <a href="javascript:;" class="  btn-notify btnContractUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showcontractDetails();
 
})

$(document).on('click','.btnContractUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_contract.filter(l=>l.key==id);
         
if (index[0]) { 
  contractDetailsArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_contract= temp_contract.filter(l=>l.key!=id);
     showcontractDetails(); 
    }
    })

function showcontractDetails(){
    var html='';  
    if(contractDetailsArray.length>0){
        $('.SANDiv').removeClass('d-none');
    }
    else{
     $('.SANDiv').addClass('d-none');   
    }
    for(var i=0;i<contractDetailsArray.length;i++){
var img='@';
        if(contractDetailsArray[i].san_type=='DNS'){
            img='<img src="{{asset('public/img/san.png')}}" width="32px">';
        }
        else if(contractDetailsArray[i].san_type=='IP'){
            img='<img src="{{asset('public/img/ip.png')}}" width="32px">';
        }
        html+=`        <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>${img}</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <label class="mb-0">${contractDetailsArray[i].san}</label>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                        <a type="button"  data="${i}" data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="Edit" class="btnEditContract  btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}">
                                                        </a>
                                                        <a type="button"   data="${i}" class="j e btn btn-sm btn-link btnDeleteContract text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
    }

     $('#SANBlock').html(html)
}

 // END Contract Details















// Attachment ARRAY

var attachmentArray=[];
var attachment_key_count=0;
$('#AttachmentSave').click(function(){
var attachment=content3_image;
     if(content3_image.length==0){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1">  Add an attachment before saving.', delay: 5000});

    }
    else{

           var l=attachmentArray.length;
                         


           for(var i=0;i<attachment.length;i++){
           attachmentArray.push({key:attachment_key_count,attachment:attachment[i],date:'{{date('Y-M-d')}}',time:'{{date('h:i:s A')}}',name:'{{Auth::user()->firstname.''.Auth:: user()->lastname}}'});
     attachment_key_count++;
       }

       filePond.removeFiles();
       content3_image=[];
           showAttachment()   
           $('#AttachmentModal').modal('hide')
           
           
         
    }
})

var temp_attachment=[];
$(document).on('click','.btnDeleteAttachment',function(){
    var id=$(this).attr('data');
            var key=attachmentArray[id].key;
     temp_attachment.push(attachmentArray[id]);

     attachmentArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Attachment  Deleted. <a href="javascript:;" class="  btn-notify btnAttachmentUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showAttachment();
 
})


$('#AttachmentClose').click(function(){
    temp_attachment=[];
    content3_image=[];
       filePond.removeFiles();
})

$(document).on('click','.btnAttachmentUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_attachment.filter(l=>l.key==id);
         
if (index[0]) { 
  attachmentArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_attachment= temp_attachment.filter(l=>l.key!=id);


      
    showAttachment(); 
    }
    })

function showAttachment(){
    var html='';  
    if(attachmentArray.length>0){
        $('.attachmentDiv').removeClass('d-none');
    }
    else{
     $('.attachmentDiv').addClass('d-none');   
    }
    for(var i=0;i<attachmentArray.length;i++){
           var fileExtension = attachmentArray[i].attachment.split('.').pop();
                                         icon='attachment.png';
                                          if(fileExtension=='pdf'){
                                                icon='attch-Icon-pdf.png';
                                            }
                                            else if(fileExtension=='doc' || fileExtension=='docx'){
                                                icon='attch-word.png'
                                            }
                                            else if(fileExtension=='txt'){
                                                icon='attch-word.png';

                                            }
                                            else if(fileExtension=='csv' || fileExtension=='xlsx' || fileExtension=='xlsm' || fileExtension=='xlsb' || fileExtension=='xltx'){
                                                    icon='attch-excel.png'
                                            }
                                            else if(fileExtension=='png'  || fileExtension=='gif' || fileExtension=='webp' || fileExtension=='svg' ){
                                                icon='attch-png icon.png';
                                            }
                                              else if(  fileExtension=='jpeg' || fileExtension=='jpg'  ){
                                                icon='attch-jpg-icon.png';
                                            }
                                               else if(  fileExtension=='potx' || fileExtension=='pptx' || fileExtension=='ppsx' || fileExtension=='thmx'  ){
                                                icon='attch-powerpoint.png';
                                            }


        html+=`   <div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{asset('public/img/profile-white.png')}}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                           <h2 class="mb-0 comments-text">${attachmentArray[i].name}<br><span class="comments-subtext">On ${attachmentArray[i].date} at ${attachmentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                       <!-- -->
                                                      
                                                        <a type="button"  class="  btnDeleteAttachment    btn btn-sm btn-link text-danger"  data="${i}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a> 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-2"><p class="mb-2">
 <a href="{{asset('public/temp_uploads/${attachmentArray[i].attachment}')}}" target="_blank"    class="   attachmentDivNew comments-section-text"><img src="{{asset('public/img/${icon}')}}" width="25px"> &nbsp;${attachmentArray[i].attachment.substring(0,30)}...
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>`;
    }

     $('#attachmentBlock').html(html)
}

 // END Attachment




















$.ajax({
    type:'get',
    'method':'get',
    data:{id:'<?php echo $_GET['id'] ?>'},
    url:"{{url('get-comments-assets')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){
            var date=res[i].date;
            var newDate=new Date(date);
     const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];
    var date1=newDate.getFullYear()+'-'+monthNames[newDate.getMonth()]+'-'+newDate.getDate();
    var time=newDate.toLocaleString('en-US', { hour: date1.getHours, minute:date1.getSeconds, hour12: true }) ;

        commentArray.push({key:i,comment:res[i].comment,date:date1,time:time.split(',')[1],name:res[i].name});
        comment_key_count=i;
        }
        showComment();

    }
})



$.ajax({
    type:'get',
    'method':'get',
    data:{id:'<?php echo $_GET['id'] ?>'},
    url:"{{url('get-attachment-assets')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){
            var date=res[i].date;
            var newDate=new Date(date);
     const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",
  "July", "Aug", "Sep", "Oct", "Nov", "Dec"
];
    var date1=newDate.getFullYear()+'-'+monthNames[newDate.getMonth()]+'-'+newDate.getDate();
    var time=newDate.toLocaleString('en-US', { hour: date1.getHours, minute:date1.getSeconds, hour12: true }) ;

        attachmentArray.push({key:i,attachment:res[i].attachment,date:date1,time:time.split(',')[1],name:res[i].name});
        attachment_key_count=i;
        }
        showAttachment();

    }
})


 
$.ajax({
    type:'get',
    'method':'get',
    data:{id:'<?php echo $_GET['id'] ?>'},
    url:"{{url('get-ip-assets')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){

  ipArray.push({key:ip_key_count,ip_address_name:res[i].ip_address_name,ip_address_value:res[i].ip_address_value});
       
 
        ip_key_count=i;
        }
         showIp()   
    }
})












function unEntity(str){
   return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}






 



 



 
function unEntity(str){
   return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}


let click=0;
$('input,textarea').on('keyup',function(){
click=1;

})

$('select').on('change',function(){
click=1;

})
$('.btnClose').click(function(){
if(click==1){
   Dashmix.helpers('notify', {message: 'Close window?  <a href="javascript:;" class="  btn-notify btnCloseUndo ml-4" >Proceed</a>', delay: 5000});
 
}
else{
   window.location.href=unEntity('{{URL::previous()==URL::current()?url($type):URL::previous()}}');
}
})
$(document).on('click','.btnCloseUndo',function(){
        window.location.href=unEntity('{{URL::previous()==URL::current()?url($type):URL::previous()}}');
})
 


$('.saveContract').click(function(){
    $('.tooltip').tooltip('hide');
         $('.show').addClass('d-none');
var data1=$(this).attr('data');
        var client_id=$('#client_id').val();
        var asset_type_id=$('#asset_type_id').val();
        var site_id=$('#site_id').val();
        var location=$('#location').val();
        var os=$('#os').val();
        var hostname=$('#hostname').val();
        var domain=$('#domain').val();

          var use_=$('#use_').val();
            var role=$('#role').val();
              var manufacturer=$('#manufacturer').val();
   
 var model=$('#model').val();
 var type=$('#type').val();
 $('#asset_type_id').select2('destroy');
  var type_name=$('option:selected',$('#asset_type_id')).text().trim();
  
 var sn=$('#sn').val();
 var memory=$('#memory').val();
 var vlan_id=$('#vlan_id').val();
 var ip_address=$('#ip_address').val();
    var vcpu=$('#vcpu').val();
 var page_type="{{$type}}";
     var parent_asset=$('#parent_asset').val();
$('#asset_type_id').select2();
 if(asset_type_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Asset Type.', delay: 5000});

    }
      else  if( client_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Client.', delay: 5000});

    }
 else  if( site_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Site.', delay: 5000});

    }  

    else  if(os=='' && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Operating System.', delay: 5000});

    }
      else  if(parent_asset=='' && type_name=='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Storage Controller.', delay: 5000});

    }
     else  if(hostname=='' && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Hostname.', delay: 5000});

    }

      else  if(domain==''  && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Domain.', delay: 5000});

    }
      else  if(use_==''  && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Environment.', delay: 5000});

    }
      else  if(role==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Role.', delay: 5000});

    }
      else  if(manufacturer=='' && page_type=='physical'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Manufacturer.', delay: 5000});

    }
       else  if(model=='' && page_type=='physical'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for model.', delay: 5000});

    }
      else  if(type=='' && page_type=='physical'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Type.', delay: 5000});

    }
      else  if(sn=='' && page_type=='physical'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for SN.', delay: 5000});

    }
      else  if(vcpu=='' && page_type=='virtual'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for vCpu.', delay: 5000});

    }
      else  if(memory==''  && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Memory.', delay: 5000});

    }
      else  if(vlan_id==''  && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for vlan Id.', delay: 5000});

    }
     else  if(ip_address==''  && type_name!='Storage Expansion'){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Primary Ip.', delay: 5000});

    }
    else{
var formData=new FormData(document.getElementById("form-1"));
for(var i=0;i<ipArray.length;i++){
    formData.append('ipArray[]',JSON.stringify(ipArray[i]));
}
 
for(var i=0;i<attachmentArray.length;i++){
    formData.append('attachmentArray[]',JSON.stringify(attachmentArray[i]));
}
for(var i=0;i<commentArray.length;i++){
    formData.append('commentArray[]',JSON.stringify(commentArray[i]));
}

 
$.ajax({
    type:'post',
    data:formData,
    'url':'{{url('update-assets')}}',
    dataType:'json',
    async:false,
 
        contentType:false,
        processData:false,
        cache:false,
    success:function(res) {
     
        Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Asset Succesfully saved', delay: 5000});

    click=0;   

 
    }
})

    }

})

 
  

 var storage_check=0;
@if(@$parent_type->asset_type_description!='Storage Expansion')
            $('.storageHide1').hide()
    @endif
$('#asset_type_id').change(function(){
  
    var val=$(this).val();

     $('#asset_type_id').select2('destroy');
                $('#asset_type_id').val(val);
                var data=$('option:selected',$('#asset_type_id')).attr('data');
                 
                $('#asset_type_id').select2();


                    if(data=='Storage Expansion'){
                        $('.storageHide').hide()
                        
                        $('.storageHide1').show()


                    storage_check=1;
                    }
                    else{
                        $('.storageHide').show()
                        $('.storageHide1').hide()

            $('#parent_asset').select2('destroy');
                        $('#parent_asset').val("");
                            $('#parent_asset').select2()
                    storage_check=0;
                    }


                if(data=='Physical Server'){
                    $('.cpuDiv').removeClass('d-none')
                }
                else{
                    $('.cpuDiv').addClass('d-none')   
                }

})
$('#client_id').change(function(){
    var id=$(this).val()
    $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getSiteByClientId')}}',
        success:function(res){
            var html='';
             html+='<option value>Select Site</option>';
            for(var i=0;i<res.length;i++){
                html+='<option value="'+res[i].id+'" >'+res[i].site_name+'</option>';
            }
            $('#site_id').select2('destroy');
            $('#site_id').html(html);
            $('#site_id').select2();
        }
    })
    $('#vlan_id').select2('destroy');
$('#vlan_id').html('<option value="">Select Site First</option>');
$('#vlan_id').select2();

    $('#vlanInfo').addClass('d-none')
       $('#network_zone').val('');
            $('#ip_address').val('');
            $('#internet_facing').prop('checked',0);
       $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getDomainByClientId')}}',
        success:function(res){
            var html='';
            html+='<option value>Select Domain</option>';
            for(var i=0;i<res.length;i++){
                html+='<option value="'+res[i].id+'" >'+res[i].domain_name+'</option>';
            }
            $('#domain').select2('destroy');
            $('#domain').html(html);
            $('#domain').select2();
        }
    })

 $.ajax({
        type:'get',
        data:{id:id},
        url:'{{url('getParentAsset')}}',
        success:function(res){
            var html='';
             html+='<option value>Select Parent Asset</option>';
            for(var i=0;i<res.length;i++){
                html+='<option value="'+res[i].id+'" >'+res[i].fqdn+'</option>';
            }  
            $('#parent_asset').select2('destroy');
            $('#parent_asset').html(html);
            $('#parent_asset').select2();
        }
    })


})
$('#site_id').change(function(){
    var id=$(this).val()
    var client_id=$('#client_id').val();
    $.ajax({
        type:'get',
        data:{site_id:id,client_id:client_id},
        url:'{{url('getVlanId')}}',
        success:function(res){
            var html='';
             html+='<option value>Select Vlan Id</option>';
            for(var i=0;i<res.length;i++){
                html+='<option value="'+res[i].id+'"  data-subnet_ip="'+res[i].subnet_ip+'" data-mask="'+res[i].mask+'"   data-description="'+res[i].description+'"   data-gateway_ip="'+res[i].gateway_ip+'"   data-ssid_name="'+res[i].ssid_name+'"   data-zone="'+res[i].zone+'"      data-bg="'+res[i].tag_back_color+'"   data-color="'+res[i].tag_text_color+'" >'+res[i].vlan_id+'</option>';
            }
            $('#network_zone').val('');
            $('#ip_address').val('');
            $('#vlan_id').select2('destroy');
            $('#vlan_id').html(html);
$('#vlanInfo').addClass('d-none')
            $('#vlan_id').select2();
            $('#internet_facing').prop('checked',0);
        }
    })
       
})
$('#cpu_cores,#cpu_sockets').focusout(function(){

    var cpu_cores=$('#cpu_cores').val();
    var cpu_sockets=$('#cpu_sockets').val();
        $('#cpu_total_cores').val(cpu_cores*cpu_sockets)
})

$('#AssetStatus').change(function(){
$('#InactiveDate').val('')
    if($(this).prop('checked')==1){
              $('.InactiveDiv').addClass('d-none')  

    }
    else{
       
         $('.InactiveDiv').removeClass('d-none')    
    }
})

$('#hostname').focusout(function(){
    var hostname=$('#hostname').val();
  var domain=$('option:selected',$('#domain')).text();
if(hostname!='' && domain!=''){
        $('#fqdn').val(hostname+'.'+domain);
}
}
)


$('#domain').change(function(){
    var hostname=$('#hostname').val();
 var domain=$('option:selected',$('#domain')).text();
if(hostname!='' && domain!=''){
        $('#fqdn').val(hostname+'.'+domain);
}
})


  
    })
 </script>