  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
<?php
 
if(Auth::user()->role=='read'){
  echo "You dont have access";
    exit;
}
 $qry=DB::table('network')->where('id',$_GET['id'])->first();
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
             
                            <div class="block card-round   bg-new-dark new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="{{asset('public/img/menu-network-grey.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Edit Network</h4>
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
                                           <a href="javascript:;" class="text-white saveContract" data="0"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save Asset"><i class="fa fa-check text-white"   ></i> </a>
                                            <a  data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""   data-original-title="Close" href="javascript:;" class="text-white btnClose"><i class="fa fa-times texti-white"   ></i> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full  -boxed" style="    padding-left: 15mm;
    padding-right: 15mm;">
                    <!-- New Post -->
                    <form  id="form-1" action="{{url('insert-asset')}}" class="js-validation   " method="POST" enctype="multipart/form-data"  >
                        @csrf
                        <input type="hidden" name="id" value="{{$_GET['id']}}">
                         <input type="hidden" name="attachment_array" id="attachment_array" >
                         
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
                                     
                           <div class="row">
 
                                    <div class="col-sm-12">
                                        

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
                                                    <option value="{{$c->id}}" data-logo="{{$c->logo}}"  data-email="{{$c->email_address}}" data-renewal_notification_email="{{$c->renewal_notification_email}}" data-address="{{nl2br($c->client_address)}}" {{$qry->client_id==$c->id?'selected':''}}>{{$c->firstname}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>

                                         </div>
                                         
                                  <div class="form-group row">
                                            <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Site</label>
                                          
                                            <div class="col-sm-5">
                                                 <select type="" class="form-control select2" id="site_id"  value="" name="site_id"   >
                                                    <option value=""></option>
                                                                    <?php $site=DB::table('sites')->where('is_deleted',0)->where('client_id',$qry->client_id)->orderBy('site_name','asc')->get();?>
                                                    @foreach($site as $s)
                                                    <option value="{{$s->id}}" {{$qry->site_id==$s->id?'selected':''}}>{{$s->site_name}}</option>
                                                    @endforeach
                                                    </select>
                                            </div>
                                          
                                        </div>


  <div class="row form-group ">

             <label class="col-sm-2    col-form-label" for="example-hf-email">Description  </label>
  
              <div class="col-sm-5">
                               <input type="text" class="form-control"  value="{{$qry->description}}" id="description" name="description"   > 
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
                     
                                 <a class="  section-header"  >Network Information

                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group storageHide">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Network Zone
 </label>
 <?php
                                            $network_zone=DB::Table('network_zone')->where('is_deleted',0)->orderBy('network_zone_description','asc')->get();
                                             ?>
                                            <div class="col-sm-4">
                         <select type="" class="form-control select2" id="network_zone"  value="" name="network_zone"   >
                                                    <option value=""></option>
                                                    @foreach($network_zone as $c)
                                                    
                                                    <option value="{{$c->network_zone_description}}" {{$qry->zone==$c->network_zone_description?'selected':''}}>{{$c->network_zone_description}}</option>

                                                    @endforeach
                                                    </select>
                                            </div>
                                        </div>

                                         
                            
                                      

                                            <div class="row form-group ">
                                    <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">vlanId
 </label>
  
                                            <div class="col-sm-2">
                                                 <input type="text" class="form-control"  id="vlan_id" name="vlan_id" value="{{$qry->vlan_id}}"   > 
                                            </div>
                                             <div class="col-sm-2">
                                                <div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" {{$qry->internet_facing==1?'checked':''}} value="1">
  <label class="btn btn-new w-75 " for="internet_facing">Internet Facing</label>
</div>
                                            </div>
                                        </div>
                                                    <div class="row form-group ">
                                                              <label class="col-sm-2    col-form-label" for="example-hf-email">Wifi Enabled
 </label>
  
 <div class="col-sm-2">
                                                <div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="wifi_enabled" name="wifi_enabled" value="1" {{$qry->wifi_enabled==1?'checked':''}}>
  <label class="btn btn-new w-75 WifiDiv " for="wifi_enabled">{{$qry->wifi_enabled==1?'Yes':'No'}}</label>
</div>
                                            </div>
                                        </div>
 
 
</div>
</div>




   <div class="block new-block {{$qry->wifi_enabled==1?'':'d-none'}}  WifiHide" >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Wifi Information
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group ">

             <label class="col-sm-2    col-form-label" for="example-hf-email">SSID Name  </label>
  
              <div class="col-sm-4">
                               <input type="text" class="form-control" value="{{$qry->ssid_name}}"  id="ssid_name" name="ssid_name"   > 
                          </div>
                                                </div>
                                                 <div class="row form-group ">

             <label class="col-sm-2    col-form-label" for="example-hf-email">Encryption  </label>
  
              <div class="col-sm-4">
                                <select type="text" class="form-control"  id="encryption" name="encryption"    > 
                                <option value="">Select Encryption</option>
                                <option value="Open" {{$qry->encryption=='Open'?'selected':''}}>Open</option>
                                <option value="PSK with RADIUS" {{$qry->encryption=='PSK with RADIUS'?'selected':''}}>PSK with RADIUS</option>
                                     <option value="PSK (WPA2)" {{$qry->encryption=='PSK (WPA2)'?'selected':''}}>PSK (WPA2)</option>
                               </select>
                          </div>
                                                </div>

<div class="row form-group ">

             <label class="col-sm-2    col-form-label" for="example-hf-email">Sign On Method  </label>
  
              <div class="col-sm-4">
                               <select type="text" class="form-control"  id="sign_in_method" name="sign_in_method"   > 
                                <option value="None">None</option>
                                <option value="Password Protected"  {{$qry->sign_in_method=='Password Protected'?'selected':''}}>Password Protected</option>
                                <option value="MAC Address Filtering"  {{$qry->sign_in_method=='MAC Address Filtering'?'selected':''}}>MAC Address Filtering</option>
                                     <option value="Active Directory"  {{$qry->sign_in_method=='Active Directory'?'selected':''}}>Active Directory</option>
                               </select>
                          </div>
                                                </div>
 <div class="row form-group ">
                                                              <label class="col-sm-2    col-form-label" for="example-hf-email">Certificate
 </label>
  
 <div class="col-sm-2">
                                                <div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="certificate" name="certificate" value="1" {{$qry->certificate=='1'?'checked':''}}>
  <label class="btn btn-new w-75 CertDiv " for="certificate">{{$qry->certificate==1?'Yes':'No'}}</label>
</div>
                                            </div>
                                        </div>

</div>
                                   
                                             
                                                

                                         
                                             
                                           
 
                                
 
</div>
  


    <div class="block new-block  " >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Subnet Information


                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
<div class="row form-group ">
                                    <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Subnet IP / Mask
 </label>
  
                                            <div class="col-sm-3">
                                                 <input type="text" class="form-control" id="subnet_ip" value="{{$qry->subnet_ip}}" name="subnet_ip"> 
                                            </div>
                                             <div class="col-sm-1">
                                                 <select type="text" class="form-control"  id="mask" name="mask"   > 
                                <option value=""></option>
                                <option value="/8" {{$qry->mask=='/8'?'selected':''}}>/8</option>
                                <option value="/9" {{$qry->mask=='/9'?'selected':''}}>/9</option> 
                                <option value="/10" {{$qry->mask=='/10'?'selected':''}}>/10</option>
                                <option value="/11" {{$qry->mask=='/11'?'selected':''}}>/11</option>
                                <option value="/12" {{$qry->mask=='/12'?'selected':''}}>/12</option>
                                <option value="/13" {{$qry->mask=='/13'?'selected':''}}>/13</option>
                                <option value="/15" {{$qry->mask=='/14'?'selected':''}}>/14</option>
                                <option value="/15" {{$qry->mask=='/15'?'selected':''}}>/15</option>
                                <option value="/16" {{$qry->mask=='/16'?'selected':''}}>/16</option>
                                <option value="/17" {{$qry->mask=='/17'?'selected':''}}>/17</option>
                                <option value="/18" {{$qry->mask=='/18'?'selected':''}}>/18</option>
                                <option value="/19" {{$qry->mask=='/19'?'selected':''}}>/19</option>
                                <option value="/21" {{$qry->mask=='/20'?'selected':''}}>/20</option>
                                <option value="/21" {{$qry->mask=='/21'?'selected':''}}>/21</option>
                                <option value="/22" {{$qry->mask=='/22'?'selected':''}}>/22</option>
                                <option value="/23" {{$qry->mask=='/23'?'selected':''}}>/23</option>
                                <option value="/24" {{$qry->mask=='/24'?'selected':''}}>/24</option>
                                <option value="/25" {{$qry->mask=='/25'?'selected':''}}>/25</option>
                                <option value="/26" {{$qry->mask=='/26'?'selected':''}}>/26</option>
                                <option value="/27" {{$qry->mask=='/27'?'selected':''}}>/27</option>
                                <option value="/28" {{$qry->mask=='/28'?'selected':''}}>/28</option>
                                <option value="/29" {{$qry->mask=='/29'?'selected':''}}>/29</option>
                                <option value="/30" {{$qry->mask=='/30'?'selected':''}}>/30</option>
                               </select>
                                            </div>
                                        </div>  
      

                                         
<div class="row form-group ">
                                    <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Gateway IP
 </label>
  
                                            <div class="col-sm-3">
                                                 <input type="text" class="form-control" id="gateway_ip" value="{{$qry->gateway_ip}}" name="gateway_ip"> 
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


$('#wifi_enabled').change(function(){
    if($(this).prop('checked')==1){
        $('.WifiDiv').html('Yes')
        $('.WifiHide').removeClass('d-none')
    }
    else{
$('.WifiDiv').html('No')
            $('.WifiHide').addClass('d-none')
    }
})

$('#certificate').change(function(){
    if($(this).prop('checked')==1){
        $('.CertDiv').html('Yes')
    }
    else{
$('.CertDiv').html('No')
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
$(".contract_type_button label").mouseout(function(){
        
         $('.tooltip').tooltip('hide');
    
    })

$('#HasWarranty').change(function(){
    if($(this).prop('checked')==1){
               $('.supported').attr('data-original-title',"Allows you to assign contracts to asset");
    $('.supported').tooltip()
    $('.supported').html('Supported');
    }
    else{
$('#UnsupportedModal').modal('show');
    }


})


$('#ssid_name').focusout(function(){
    if($(this).val()!=''){
    $('#encryption').val('PSK (WPA2)')
    $('#sign_in_method').val('Password Protected')
}
})




function unEntity(str){
   return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}

$('#HasWarranty1').change(function(){
           
    if($(this).prop('checked')==1){

         $('.supported').attr('data-original-title',"Allows you to assign contracts to asset");
    $('.supported').tooltip()
    $('.supported').html('Supported');
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
                                                        <div class="badge badge-success py-1 mr-3 " style="font-size:18px!important">${zone}</div>
                                                        </div> 
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td   class="w-50 ">
                                                            <div class="d-flex align-items-center">
                                                              <p class="mb-0 mr-3  text-white   px-2 " style="width: fit-content;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>gw</b></p>
                                                             <h5 class="text-primary mb-0">
                                                    ${gateway_ip}
                                                </h5>
                                                </div>
                                                </td>
                                                     <td    class="w-50" >
                                                                <div  class="d-flex align-items-center"> 
                                                              <p class="mb-0 mr-3  text-white   px-2 " style="width: fit-content;border-radius: 7px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>SSID</b></p>
                                                    
                                                    <h5 class="text-primary mb-0">${ssid_name}</h5>
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
        if(not_supported=='N/A' || not_supported==''){
                Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> You must select a reason', delay: 5000});
$('.supported').html('Supported');
        }
        else{
          $('#UnsupportedModal').modal('hide');
          $('.supported').attr('data-original-title','Reason : '+not_supported)
          $('.supported').tooltip();
$('.supported').html('Unsupported');
             $('#HasWarranty').prop('checked',false)

              
              $('#HasWarranty1').prop('checked',false)

        }
})
$('#UnsupportedClose').click(function() {
          var not_supported=$('input[name=NotSupportedReason]:checked').val();
         
        if(not_supported=='N/A' || not_supported==''){
$('#HasWarranty').prop('checked',1)
$('#HasWarranty1').prop('checked',1)
}
})
$('#contract_end_date').flatpickr()
 
           @if(Session::has('success'))
              
             Dashmix.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: '{{Session::get('success')}}'});


             @endif




 
 
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
            url: '{{url('uploadNetworkAttachment')}}',
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

 
            fetch(`{{url('revertNetworkAttachment')}}?key=${uniqueFileId}`, {
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
    url:"{{url('get-comments-network')}}",
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
    url:"{{url('get-attachment-network')}}",
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
    window.location.href=unEntity('{{URL::previous()==URL::current()?url('network'):URL::previous()}}');
}
})
$(document).on('click','.btnCloseUndo',function(){
      window.location.href=unEntity('{{URL::previous()==URL::current()?url('network'):URL::previous()}}');
})
 

 
function unEntity(str){
   return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}


$('.saveContract').click(function(){
    $('.tooltip').tooltip('hide');
         $('.show').addClass('d-none');
var data1=$(this).attr('data');
 
     var client_id=$('#client_id').val();
 
        var site_id=$('#site_id').val();
        var network_zone=$('#network_zone').val();
        var role=$('#role').val();
        var subnet_ip=$('#subnet_ip').val();
        var encryption=$('#encryption').val();

          var gateway_ip=$('#gateway_ip').val();
           
   
         if( client_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Client.', delay: 5000});

    }
 else  if( site_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Site.', delay: 5000});

    }  

    else  if(network_zone==''   ) {
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Network Zone.', delay: 5000});

    }
     
     else  if(role==''  ){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Role.', delay: 5000});

    }
    

      else  if(subnet_ip==''   ){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Subnet Ip.', delay: 5000});

    }
     
      else  if(gateway_ip==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Gateway Ip.', delay: 5000});

    }
  
     else{
var formData=new FormData(document.getElementById("form-1"));
 
for(var i=0;i<attachmentArray.length;i++){
    formData.append('attachmentArray[]',JSON.stringify(attachmentArray[i]));
}
for(var i=0;i<commentArray.length;i++){
    formData.append('commentArray[]',JSON.stringify(commentArray[i]));
}

 
$.ajax({
    type:'post',
    data:formData,
    'url':'{{url('update-network')}}',
    dataType:'json',
    async:false,
 
        contentType:false,
        processData:false,
        cache:false,
    success:function(res) {
           Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Network Succesfully saved', delay: 5000});

    click=0;   

   
 
    }
})

    }

})

 
  

 var storage_check=0;
                        $('.storageHide1').hide()
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
                html+='<option value="'+res[i].id+'"  data-subnet_ip="'+res[i].subnet_ip+'" data-mask="'+res[i].mask+'"   data-description="'+res[i].description+'"   data-gateway_ip="'+res[i].gateway_ip+'"   data-ssid_name="'+res[i].ssid_name+'"   data-zone="'+res[i].zone+'"    >'+res[i].vlan_id+'</option>';
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