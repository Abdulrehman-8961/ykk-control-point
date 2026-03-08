  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
<?php
 
if(Auth::user()->role=='read'){
  echo "You dont have access";
    exit;
}
 

$qry=DB::table('ssl_certificate')->where('id',$_GET['id'])->first();

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
             
                           <?php $contract_end_date=date('Y-M-d',strtotime($qry->cert_edate)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
             $ended_qry=DB::Table('users')->Where('id',$qry->ended_by)->first();

              $renewed_qry=DB::Table('users')->Where('id',$qry->renewed_by)->first();  
  ?>
 


                    @if($qry->cert_status=='Active')

                                    @if($abs_diff<=30)
                            <div class="block card-round   bg-new-yellow new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                      <div class="d-flex">
                          <img src="{{asset('public/img/icon-upcoming-removebg-preview.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text text-dark" style="line-height:25px">Upcoming</h4>
                                <p class="mb-0  header-new-subtext text-dark" style="line-height:20px">In {{$abs_diff}} days</p>
                                    </div>
                                </div>
                                    @else
                                         <div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="{{asset('public/img/icon-active-removebg-preview.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Certificate Active</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">Until {{$contract_end_date}} ({{$abs_diff}} days remaining)</p>
                                    </div>
                                </div>

                                @endif
                                @elseif($qry->cert_status=='Inactive')
                                      <div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="{{asset('public/img/icon-renewed-removebg-preview.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Certificate Renewed
</h4>
                                       <p class="mb-0  header-new-subtext" style="line-height:15px">On {{date('Y-M-d H:i:s A',strtotime($qry->renewed_on))}} by   {{@$renewed_qry->firstname.''.@$renewed_qry->lastname}}</p>
                                    </div>
                                </div>

                                @elseif($qry->cert_status=='Expired/Ended')
                                        <div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="{{asset('public/img/icon-ended-removebg-preview.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Certificate Revoked
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On {{date('Y-M-d',strtotime($qry->ended_on))}} at  {{date('H:i:s A',strtotime($qry->ended_on))}} By {{@$ended_qry->firstname.' '.@$ended_qry->lastname}} </p>
                                    </div>
                                </div>
                                @elseif($qry->cert_status=='Ended')
                                    <div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="{{asset('public/img/icon-expired-removebg-preview.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Certificate Revoked
</h4>   
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On {{date('Y-M-d',strtotime($qry->ended_on))}} at  {{date('H:i:s A',strtotime($qry->ended_on))}} By {{@$ended_qry->firstname.' '.@$ended_qry->lastname}}  </p>
                                    </div>
                                </div>
                                @elseif($qry->cert_status=='Expired')
                                    <div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="{{asset('public/img/icon-expired-removebg-preview.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Certificate Expired
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On {{$contract_end_date}}</p>
                                    </div>
                                </div>
                                @else

                                @endif








                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="{{asset('public/img/paper-clip-white.png')}}" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="{{asset('public/img/comment-white.png')}}" width="20px"></a>
                                         </span>
                             <!--               <a href="javascript:;"  class="saveContract text-white"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add and Continue"  class="text-white"><i class="fa fa-plus texti-white"  ></i> </a>
  -->
                                              <a href="javascript:;" class="text-white saveContract" data="0"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save SSL Certificate"><i class="fa fa-check text-white"   ></i> </a>
                                            <a  data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""   data-original-title="Close" href="javascript:;" class="text-white btnClose"><i class="fa fa-times texti-white"   ></i> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full  -boxed" style="    padding-left: 15mm;
    padding-right: 15mm;">
                    <!-- New Post -->
                    <form  id="form-1" action="{{url('insert-contract')}}" class="js-validation   " method="POST" enctype="multipart/form-data"  >
                        @csrf        <input type="hidden" name="id" value="{{$qry->id}}">
                 
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
                                                     <?php $site=DB::table('sites')->where('is_deleted',0)->where('client_id',$qry->client_id)->orderBy('site_name','asc')->get();?>
                                                    @foreach($site as $s)
                                                    <option value="{{$s->id}}" {{$qry->site_id==$s->id?'selected':''}}>{{$s->site_name}}</option>
                                                    @endforeach
                                           
                                                    </select>
                                            </div>
                                          
                                        </div>


                                            <div class="form-group row ">
                                            <label class="col-sm-2 col-form-label mandatory " for="example-hf-client_id">Description</label>
                                          
                                            <div class="col-sm-10">
                                               <input type="text" class="form-control" value="{{$qry->description}}" id="description" name="description" >
                                            </div>
                                          
                                        </div>
                                  
                                   <div class="form-group row">
                                                         <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Type </label>
                                            <div class="col-sm-8">
                                                <div class="form-group  ">
                                         
                                     
  
<div class="contract_type_button ">
  <input type="radio" id="a75" name="cert_type" {{$qry->cert_type=='internal'?'checked':''}}   value="internal"/>
  <label class="btn btn-new " for="a75"  >Internal</label>
</div>
<div class="contract_type_button">
  <input type="radio" id="a50" name="cert_type" value="public"  {{$qry->cert_type=='public'?'checked':''}}/>
  <label class="btn btn-new ml-5" for="a50"  value="public" >Public</label>
</div>
 
                                          
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                      
                                               </div>      

                                               <div class=" row mb-3">
                                            <label class="col-sm-2 col-form-label" for="example-hf-email">Email Notifications</label>
                                            <div class="col-sm-6">
                                                <div class="custom-control custom-switch custom-control-warning custom-control-lg mt-2 ">
                                                <input type="checkbox" class="custom-control-input" id="cert_notification" name="cert_notification" value="1"  {{$qry->cert_notification==1?'checked':''}}>
                                                <label class="custom-control-label" for="cert_notification"> </label>
                                            </div>
                                            </div>
                                          
                                        </div>
                                         
       


                                      <!--      <div class="form-group p row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email"><br><small><a href="javascript:;" class="add-more">(Upto 5)</a></small></label>

                                            <div class="col-sm-3">
      <input type="email" class="form-control" id="renewal_notification_email"   name="notification_renewal_email[]"  placeholder="Email 1"  > 
                                            </div>
                                          


                                        </div> -->
                                                <div id="EmailBlock" class="EmailHide">  
                                                     
                               
                                                   </div>

                                                    <button type="button" data-toggle="modal" data-target="#EmailModal" class="btn EmailHide ml-5 mt-3 btn-new ">Add Email Address</button>
                 
                 </div>
             </div>
         </div>
     </div>


    <div class="block new-block PublicDiv {{$qry->cert_type=='public'?'':'d-none'}}" >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Public SSL Certificate
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Issuer </label>
                                            <div class="col-sm-5">
                                                  <select   type="text" class="form-control select2"    id="cert_issuer" name="cert_issuer" placeholder=""  > 
                                                    
                                                        <?php $use=DB::Table('vendors')->where('is_deleted',0)->orderBy('vendor_name','asc')->get(); ?>
                                                       <option value="">Select Issuer</option>
                                                       @foreach($use as $u)

                                                            <option value="{{$u->id}}" {{$u->id==$qry->cert_issuer?'selected':''}}>{{$u->vendor_name}}</option>
                                                        @endforeach

                                            </select>
                                            </div>
                                        </div>

                                         
                                     
                                             
                                           
 
                                    
                                         <div class="form-group row">
                                            <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Renewal Date
</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="js-flatpickr form-control bg-white" id="cert_rdate"
                     name="cert_rdate" placeholder="Y-M-d" data-alt-input="true"   value="{{$qry->cert_rdate}}"   data-date-format="Y-m-d" data-alt-format="Y-M-d">
                                            </div>
                                        </div>
                                        <div class="row form-group ">
                                                <label class="col-sm-2 mandatory  col-form-label" for="example-hf-email">MSRP</label>
                                            <div class="col-sm-4">
                                              <input type="number" step="any" class="form-control" value="{{$qry->cert_msrp}}"     id="cert_msrp" name="cert_msrp" placeholder=""  > 
                                            </div>
                                        </div>
         
 
</div>
</div>




   <div class="block new-block" >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Subject
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content">
                             
                                         
  
 
                                      <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Name (CN)</label>
                                            <div class="col-sm-4">
                                                      <input type="text" class="form-control"  id="cert_name" value="{{$qry->cert_name}}"  name="cert_name" placeholder=""  > 
                                            </div>
                                                    <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">  Expiration Date  </label>
                                            <div class="col-sm-4">
                                                         <input type="text" class="js-flatpickr form-control bg-white" id="cert_edate" 
                                                          name="cert_edate" placeholder="Y-M-d" data-alt-input="true" data-date-format="Y-m-d" data-alt-format="Y-M-d"  value="{{$qry->cert_edate}}" >
                                            </div>
                                        </div>

                                         
                                     
                                             
                                                <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Company (O) </label>
                                            <div class="col-sm-4">
                                                  <input type="text" class="form-control"  id="cert_company" value="{{$qry->cert_company}}"  name="cert_company" placeholder=""  > 
                                            </div>
                                                    <label class="col-sm-2    col-form-label" for="example-hf-email">  Department(OU)  </label>
                                            <div class="col-sm-4">
                                                       <input type="text" class="form-control"  id="cert_department" value="{{$qry->cert_department}}"   name="cert_department" placeholder=""  > 
                                            </div>
                                        </div>

                                         
                                     
                                                <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Locality (L) </label>
                                            <div class="col-sm-4">
                                                  <input type="text" class="form-control"  id="cert_city"  name="cert_city" placeholder=""   value="{{$qry->cert_city}}" > 
                                            </div>
                                                    <label class="col-sm-2   mandatory col-form-label" for="example-hf-email">  State (S)  </label>
                                            <div class="col-sm-4">
                                                       <input type="text" class="form-control"  id="cert_state"  value="{{$qry->cert_state}}"   name="cert_state" placeholder=""  > 
                                            </div>
                                        </div>

                                                <div class="row form-group ">
                                                         <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Country(CA) </label>
                                            <div class="col-sm-4">
                                                   <input type="text" class="form-control"  id="cert_country" name="cert_country"   value="{{$qry->cert_country}}"   placeholder=""  > 
                                            </div>
                                                    <label class="col-sm-2    col-form-label" for="example-hf-email">  Email (E)  </label>
                                            <div class="col-sm-4">
                                                       <input type="email" class="form-control"  id="cert_email"  value="{{$qry->cert_email}}" name="cert_email" placeholder=""  > 
                                            </div>
                                        </div>

                                         
                                             
                                           
 
                                     
                                <div class="col-sm-8">
         <button type="button" class="btn ml-5 btn-new mt-4 " data-toggle="modal" data-target="#SANModal">Add Subject Alternate Name</button>
 <button type="button" class="btn ml-5 btn-new btnHost float-right mt-4 "  >Assign certificate to hosts</button>
</div>
 
</div>
</div>
                          
    <div class="block new-block  SANDiv d-none" >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Subject Alternate Names
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content" id="SANBlock">
                             
                                       
                                   


      
 </div>
 </div> 

                                 
    <div class="block new-block  HostDiv d-none " >

                            <div class="block-header py-0" style="padding-left:7mm;">
                     
                                 <a class="  section-header"  >Hosts
                                </a>
                        
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content new-block-content row" id="HostBlock">
                             
                                        
                                   


      
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
    

             <div class="modal fade" id="EmailModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header  ">
                            <span class="b e section-header">Add Email Address</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content row">
                            
                  
<div class="col-sm-3   form-group      ">
                <label class="mandatory">Email Address</label>

</div>
<div class="col-sm-9   form-group      ">
         <input     class="form-control  "  required="" name="email_address"   >
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="EmailSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>

               <div class="modal fade" id="EmailModalEdit" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header  ">
                            <span class="b e section-header">Edit Email Address</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content row">
                            
                  <input type="hidden" name="email_id_edit" >
<div class="col-sm-3   form-group      ">
                <label class="mandatory">Email Address</label>

</div>
<div class="col-sm-9   form-group      ">
         <input     class="form-control  "  required="" name="email_address_edit"   >
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="EmailSaveEdit"  >Save</button>
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







<div class="modal fade" id="HostModal1"   role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
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
     <?php  $client_id=$qry->client_id;
                       $asset_qry =DB::select("select a.*,at.asset_icon from assets as a left join asset_type as at on a.asset_type_id=at.asset_type_id where a.is_deleted=0 and ntp=1 and a.client_id='$client_id' and  AssetStatus=1  "); 

                        ?>
                <select type="" class="form-control selectpicker"    data-live-search="true"  id="hostname"  value="" name="cert_hostname"   >
                                                <option value="">Select Hostname</option>  


            @foreach($asset_qry as $a)

 @if($a->asset_type=='physical')
<option value='{{$a->id}}' data="{{$a->role}}"  data1="{{$a->asset_icon}}" data2="{{$a->ip_address}}">{{$a->sn}} [{{$a->hostname}}]</option>
@else
     <option value='{{$a->id}}'  data="{{$a->role}}"  data1="{{$a->asset_icon}}" data2="{{$a->ip_address}}">{{$a->hostname}}</option>
                    @endif
            @endforeach


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
                            <button type="button" class="btn mr-3 btn-new" id="HostSave"  >Save</button>
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
                   <?php      $asset_qry =DB::select("select a.*,at.asset_icon from assets as a left join asset_type as at on a.asset_type_id=at.asset_type_id where a.is_deleted=0 and ntp=1 and a.client_id='$client_id' and  AssetStatus=1  ");     ?>
            @foreach($asset_qry as $a)

 @if($a->asset_type=='physical')
<option value='{{$a->id}}' data="{{$a->role}}"  data1="{{$a->asset_icon}}" data2="{{$a->ip_address}}">{{$a->sn}} [{{$a->hostname}}]</option>
@else
     <option value='{{$a->id}}'  data="{{$a->role}}"  data1="{{$a->asset_icon}}" data2="{{$a->ip_address}}">{{$a->hostname}}</option>
                    @endif
            @endforeach                                
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
                            <button type="button" class="btn mr-3 btn-new" id="HostSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>



</div>






<div class="modal fade" id="SANModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Add Subject Alternative Name</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
                  
 <div class="row">
<div class="col-sm-2   form-group      ">
                <label class="mandatory">SAN Type</label>

</div>
<div class="col-sm-10   form-group      ">
                                            
                                     
  
<div class="contract_type_button ">
  <input type="radio" id="dns" name="san_type" checked   value="DNS"/>
  <label class="btn btn-new " for="dns" >DNS</label>
</div>
<div class="contract_type_button">
  <input type="radio" id="aip" name="san_type" value="IP"/>
  <label class="btn btn-new ml-5" for="aip"   >IP</label>
</div>

                                   <div class="contract_type_button">
  <input type="radio" id="aemail" name="san_type"  value="Email"/>
  <label class="btn btn-new ml-5" for="aemail"   >Email</label>
</div>
          
            </div>
 
            </div>
  

   
   <div class="row">
     <div class="col-sm-2   form-group      ">
                <label class="mandatory">SAN</label>
 
</div>
<div class="col-sm-10   form-group      ">
         <input    rows="4" class="form-control  "     name="san"   ></input>
                 
            </div>
        </div>
                         
                        </div>
                        <div class="block-content block-content-full   pt-1" style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="contractDetailsSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>




<div class="modal fade" id="SANModalEdit" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
                <div class="modal-content">
                    <div class="block  block-transparent mb-0">
                        <div class="block-header pb-0  ">
                            <span class="b e section-header">Edit Line</span>
                            <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-fw fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                                    
                        <div class="block-content new-block-content pt-0 pb-0 ">
                            
                     <input type="hidden" name="contract_id_edit" >
               
 <div class="row">
<div class="col-sm-2   form-group      ">
                <label class="mandatory">SAN Type</label>

</div>
<div class="col-sm-10   form-group      ">
                                            
                                     
  
<div class="contract_type_button ">
  <input type="radio" id="dns1" name="san_type_edit" checked   value="DNS"/>
  <label class="btn btn-new " for="dns1" >DNS</label>
</div>
<div class="contract_type_button">
  <input type="radio" id="aip1" name="san_type_edit" value="IP"/>
  <label class="btn btn-new ml-5" for="aip1"   >IP</label>
</div>

                                   <div class="contract_type_button">
  <input type="radio" id="aemail1" name="san_type_edit"  value="Email"/>
  <label class="btn btn-new ml-5" for="aemail1"   >Email</label>
</div>
          
            </div>
 
            </div>
  

   
   <div class="row">
     <div class="col-sm-2   form-group      ">
                <label class="mandatory">SAN</label>
 
</div>
<div class="col-sm-10   form-group      ">
         <input    rows="4" class="form-control  "     name="san_edit"   > 
                 
            </div>
        </div>
                 
                 </div>        
                        
                        <div class="block-content block-content-full   pt-1" style="padding-left: 9mm;">
                            <button type="submit" class="btn mr-3 btn-new" id="contractDetailsSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
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




$("label").mouseout(function(){
        
         $('.tooltip').tooltip('hide');
        
    })




// EMAIL ARRAY

var emailArray=[];
var email_key_count=0;
$('#EmailSave').click(function(){
var email=$('input[name=email_address]').val();
     if(email==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Email Address', delay: 5000});

    }
    else{

           var l=emailArray.length;
                        if(l<5){
           emailArray.push({key:email_key_count,email:email});
           showEmail()   
           $('#EmailModal').modal('hide')
           $('input[name=email_address]').val('')
           email_key_count++;
        }
    }
})


$('#EmailSaveEdit').click(function(){
var email=$('input[name=email_address_edit]').val();
var id=$('input[name=email_id_edit]').val();
     if(email==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Email Address', delay: 5000});

    }
    else{

           var l=emailArray.length;
                    
           emailArray[id].email=email;
           showEmail()   
           $('#EmailModalEdit').modal('hide')
           $('input[name=email_address_edit]').val('')
       
    }
})

$(document).on('click','.btnEditEmail',function(){
        var id=$(this).attr('data');
        $('#EmailModalEdit').modal('show');
                $('input[name=email_id_edit]').val(id);
        $('input[name=email_address_edit]').val(emailArray[id].email);

})
var temp_email=[];
$(document).on('click','.btnDeleteEmail',function(){
    var id=$(this).attr('data');
            var key=emailArray[id].key;
     temp_email.push(emailArray[id]);

     emailArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Email address Deleted. <a href="javascript:;" class="  btn-notify btnEmailUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showEmail();
 
})

$(document).on('click','.btnEmailUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_email.filter(l=>l.key==id);
         
if (index[0]) { 
  emailArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_email= temp_email.filter(l=>l.key!=id);


      
    showEmail(); 
    }
    })


$('#cert_notification').change(function(){
    if($(this).prop('checked')==1){
   $('.EmailHide').removeClass('d-none')
    }
    else{
    $('.EmailHide').addClass('d-none')
}
})
function showEmail(){
    var html='';  
    for(var i=0;i<emailArray.length;i++){
        html+=`   <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>@</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <label class="mb-0">${emailArray[i].email}</label>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                        <a type="button"  data="${i}" data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="Edit" class="btnEditEmail  btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}">
                                                        </a>
                                                        <a type="button"   data="${i}" class="j e btn btn-sm btn-link btnDeleteEmail text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
    }

     $('#EmailBlock').html(html)
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
    
              showHost()   

           $('#HostModal1').modal('hide')
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













$('.btnHost').click(function() {

        $('#HostModal1').modal('show')

})

















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

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> SAN  Deleted. <a href="javascript:;" class="  btn-notify btnContractUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showcontractDetails();
 
})



function unEntity(str){
   return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}


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
    url:"{{url('get-comments-ssl')}}",
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
    url:"{{url('get-attachment-ssl')}}",
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
    url:"{{url('get-email-ssl')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){

          emailArray.push({key:i,email:res[i].renewal_email});
        email_key_count=i;
        }
        showEmail();

    }
})



$.ajax({
    type:'get',
    'method':'get',
    data:{id:'<?php echo $_GET['id'] ?>'},
    url:"{{url('get-san-ssl')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){

          contractDetailsArray.push({key:i,san:res[i].san,san_type:res[i].san_type});
        contract_key_count=i;
        }
        showcontractDetails();

    }
})


$.ajax({
    type:'get',
    'method':'get',
    data:{id:'<?php echo $_GET['id'] ?>'},
    url:"{{url('get-host-ssl')}}",
    success:function(res){
        for(var i=0;i<res.length;i++){
     var ip=[];
                    var ips=res[i].ip_id.split(',');
var ipnames=res[i].ip_name.split(',');                        
    if(ipnames.includes('Primary')){
                ip.push({name:'Primary',value:res[i].ip_address,id:null})
    }
              
                $.ajax({
                    type:'get',
                    data:{id:ips},
                    url:"{{url('get-ip-ssl')}}",
                    async:false,
                    success:function(resp){
                            for(var j=0;j<resp.length;j++){
                                ip.push({name:resp[j].ip_address_name,value:resp[j].ip_address_value,id:resp[j].id})
                            }
                    },
                })

   hostArray.push({key:i,hostname:res[i].host,asset_icon:res[i].asset_icon,asset_description:res[i].role,asset_name:res[i].asset_name,ip:ip});


         
        host_key_count=i;
        }
        showHost();

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
})
            
$('#client_id').change(function(){
        var address=$('option:selected',$('#client_id')).attr('data-address')
        var email=$('option:selected',$('#client_id')).attr('data-email')
        var logo=$('option:selected',$('#client_id')).attr('data-logo')
        var renewal_notification_email=$('option:selected',$('#client_id')).attr('data-renewal_notification_email')
        $('#renewal_notification_email').val(renewal_notification_email)
            $('#registered_email').val(email)
            var id=$(this).val();
            $.ajax({
                type:'get',
                data:{id:id},
                url:'{{url('get-ssl-notification')}}',
                success:function(res){
                    var html='';
                          var l=0;
                          emailArray=[];
                    for(var i=0;i<res.length;i++){
                                 if(emailArray.length<5){
                                email_key_count++;
                       
                          emailArray.push({key:email_key_count,email:res[i].renewal_email});
                                 showEmail(); 
                             }

                                       
                                      }
                
                 
                }

            })


})






$('select[name=cert_hostname]_modal').change(function(){
    var sn=$('option:selected',$(this)).attr('data');


    $('#sn_modal').val(sn)
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
    window.location.href=unEntity('{{URL::previous()==URL::full()?url('ssl-certificate'):URL::previous()}}');
}
})
$(document).on('click','.btnCloseUndo',function(){
        window.location.href=unEntity('{{URL::previous()==URL::full()?url('ssl-certificate'):URL::previous()}}');
})
 


$('.saveContract').click(function(){
    $('.tooltip').tooltip('hide');
         $('.show').addClass('d-none');
var data1=$(this).attr('data');
        var client_id=$('#client_id').val();
        var cert_name=$('#cert_name').val();
        var cert_edate=$('#cert_edate').val();
        var cert_company=$('#cert_company').val();
        var cert_city=$('#cert_city').val();
        var cert_state=$('#cert_state').val();
        var cert_country=$('#cert_country').val();
  var site_id=$('#site_id').val();
  var description=$('#description').val();
          var cert_issuer=$('#cert_issuer').val();
            var cert_rdate=$('#cert_rdate').val();
              var cert_msrp=$('#cert_msrp').val();
  var cert_type=$('input[name=cert_type]:checked').val();

     var start_date = new Date(cert_rdate);
var end_date = new Date(cert_edate);
    

     
 if(client_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Client.', delay: 5000});

    }
        else if(site_id==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Site.', delay: 5000});

    }
     else if(description==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Description.', delay: 5000});

    }
      else  if(cert_type=='public' && cert_issuer==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Issuer.', delay: 5000});

    }
 else  if(cert_type=='public' && cert_rdate==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Renewal Date.', delay: 5000});

    } else  if(cert_type=='public' && cert_msrp==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for MSRP.', delay: 5000});

    }

    else  if(cert_name==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Name.', delay: 5000});

    }
     else  if(cert_edate==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Expiry Date.', delay: 5000});

    }

      else  if(cert_company==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for Company.', delay: 5000});

    }
      else  if(cert_city==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for City.', delay: 5000});

    }
      else  if(cert_state==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for State.', delay: 5000});

    }
      else  if(cert_country==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Country.', delay: 5000});

    }
    
     
    else{
var formData=new FormData(document.getElementById("form-1"));
for(var i=0;i<emailArray.length;i++){
    formData.append('emailArray[]',JSON.stringify(emailArray[i]));
}
for(var i=0;i<contractDetailsArray.length;i++){
    formData.append('sanArray[]',JSON.stringify(contractDetailsArray[i]));
}
for(var i=0;i<hostArray.length;i++){
    formData.append('hostArray[]',JSON.stringify(hostArray[i]));
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
    'url':'{{url('update-ssl-certificate')}}',
    dataType:'json',
    async:false,
 
        contentType:false,
        processData:false,
        cache:false,
    success:function(res) {
     
        Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Certificate successfully saved', delay: 5000});
        click=0;

 
    }
})

    }

})

 
  


        // Init Validation on Select2 change
        jQuery('.select2').on('change', e => {
            jQuery(e.currentTarget).valid();
        });

        var previous='';
$('#client_id').change(function(){
        var address=$('option:selected',$('#client_id')).attr('data-address')
        var logo=$('option:selected',$('#client_id')).attr('data-logo')
          var email=$('option:selected',$('#client_id')).attr('data-email')
         $('#cert_email').val(email)

            var renewal_notification_email=$('option:selected',$('#client_id')).attr('data-renewal_notification_email')
       var id=$(this).val()
 

        if(address!='' || logo!=''){
            $('#clientInfo').html('<div class="col-sm-8">'+
                                                '<a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">'+
                                        '<div class="block-content block-content-full d-flex align-items-center justify-content-between">'+
                                            '<div>'+
                                   
                                               '<div class="font-size-sm text-muted" style="white-space: pre-line;">'+address+'</div>'+
                                            '</div>'+
                                            '<div class="ml-3">'+
                                                '<img class="img-avatar" src="{{asset("public/client_logos/")}}/'+logo+'" alt="">'+

                                            '</div>'+
                                        '</div>'+
                                        ' </a>'+
                                    '</div>')
        }
        else{
            $('#clientInfo').html('')
        }
        var id=$(this).val();
   var client_id= $('#client_id').val();
            $.ajax({
                type:'ajax',
                method:'get',
                data:{client_id:id},
                url:"{{url('show-asset-ssl')}}",
                success:function(res){
                    var html='';
                   html+='<option value="">Select hostname</option>';
                    $('select[name=cert_hostname]').selectpicker('destroy') ;
                    for(var i=0;i<res.length;i++){
                        html+='<option value='+res[i].id+' data="'+res[i].role+'"  data1="'+res[i].asset_icon+'" data2="'+res[i].ip_address+'">'+res[i].hostname.toUpperCase()+'</option>';
                    }
                    $('select[name=cert_hostname]').html(html);
                    $('select[name=cert_hostname]').selectpicker();

                      $('select[name=cert_hostname_edit]').selectpicker('destroy') ;
                       $('select[name=cert_hostname_edit]').html(html);
                    $('select[name=cert_hostname_edit]').selectpicker();
                }

            })





})
 




$('#cpu_cores,#cpu_sockets').focusout(function(){

    var cpu_cores=$('#cpu_cores').val();
    var cpu_sockets=$('#cpu_sockets').val();
        $('#cpu_total_cores').val(cpu_cores*cpu_sockets)
})

$('select[name=cert_hostname]').focusout(function(){
    var hostname=$('select[name=cert_hostname]').val();
  var domain=$('option:selected',$('#domain')).text();
if(hostname!='' && domain!=''){
        $('#fqdn').val(hostname+'.'+domain);
}
}
)


$('#domain').change(function(){
    var hostname=$('select[name=cert_hostname]').val();
 var domain=$('option:selected',$('#domain')).text();
if(hostname!='' && domain!=''){
        $('#fqdn').val(hostname+'.'+domain);
}
})



  
    })
 </script>