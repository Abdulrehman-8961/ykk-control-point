
    
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
<?php
 
if(Auth::user()->role=='read'){
  echo "You dont have access";
    exit;
}
 
?>
<?php 
$qry=DB::Table('vendors')->where('id',$_GET['id'])->first();
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




.avatar-upload {
  position: relative;
  max-width: 105px;
 
}
.avatar-upload .avatar-edit {
  position: absolute;
  right: 0px;
  z-index: 1;
  top:  -10px;
}
.avatar-upload .avatar-edit input {
  display: none;
}
.avatar-upload .avatar-edit input + label {
  display: inline-block;
  width: 34px;
  height: 34px;
  margin-bottom: 0;
  border-radius: 100%;
  background: #FFFFFF;
  border: 1px solid transparent;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
  cursor: pointer;
  font-weight: normal;
  transition: all 0.2s ease-in-out;
}
.avatar-upload .avatar-edit input + label:hover {
  background: #f1f1f1;
  border-color: #d6d6d6;
}
.avatar-upload .avatar-edit input + label:after {
  content: "\f303";
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
  color: #4194F6;
  position: absolute;
  top:5px;
  left: 0;
  right: 0;
  text-align: center;
  margin: auto;
}
.avatar-upload .avatar-preview {
  width: 100px;
  height: 100px;
  position: relative;
  border-radius: 10;
  border: 6px solid #F8F8F8;
  box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
}
.avatar-upload .avatar-preview > div {
  width: 100%;
  height: 100%;
 
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
}





</style>
          <!-- Main Container -->
            <main id="main-container  " style="padding:3mm">
                <!-- Hero -->
             
                            <div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="{{asset('public/img/white-vendor-icon.png')}}" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">New Vendor
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px"><?php echo date('Y-M-d').' at '.date('H:i:s').' GMT'  ?> by {{Auth::user()->firstname.' '.Auth::user()->lastname}}</p>
                                    </div>
                                </div>
                                    <div class="new-header-icon-div">
                                               <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal" > 
                                             <a href="javascript:;"  id="AddAttachment" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Attachment"><img src="{{asset('public/img/paper-clip-white.png')}}" width="20px"></a>
                                         </span>
                                             <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal" > 
                                             <a  href="javascript:;"   data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Add Comment"><img src="{{asset('public/img/comment-white.png')}}" width="20px"></a>
                                         </span>
                                 
 
                                              <a href="javascript:;" class="text-white saveContract" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Save Vendor"><i class="fa fa-check text-white"   ></i> </a>
                                            <a  data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""   data-original-title="Close" href="javascript:;" class="text-white btnClose"><i class="fa fa-times texti-white"   ></i> </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <!-- Page Content -->
                <div class="content content-full  -boxed" style="    padding-left: 15mm;
    padding-right: 15mm;">
                    <!-- New Post -->
                    <form  id="form-1" action="{{url('insert-client')}}" class="js-validation   " method="POST" enctype="multipart/form-data"  >
                        @csrf
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
                                     
                                                 <div class="col-lg-8">            
                                  <div class="form-group row">
                                            <label class="col-sm-3  col-form-label mandatory" for="example-hf-client_id">Name</label>
                                          
                                            <div class="col-sm-9  form-group">
                                        <input type="name" class="form-control" id="vendor_name" name="vendor_name"  value="{{$qry->vendor_name}}" >
                                            </div>

                                          
                                        </div>
     <div class="form-group row">
                              <div class="col-lg-3"></div>

                                    <div class="col-lg-4">
                                                    <div class="avatar-upload">
        <div class="avatar-edit">
            <input type='file' id="imageUpload" name="vendor_image" accept=".png, .jpg, .jpeg" />
            <label for="imageUpload"></label>
        </div>
        <div class="avatar-preview">
            <div id="imagePreview" style="background-image: url('{{asset('public/vendor_logos/')}}/{{$qry->vendor_image}}');">
            </div>
            <input type="hidden" name="hidden_img" value="{{$qry->vendor_image}}">
        </div>
    </div>
</div>
 </div></div></div>
                                       
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
                            <button type="button" class="btn mr-3 btn-new" id="EmailSave"  >Save</button>
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
                            <button type="button" class="btn mr-3 btn-new" id="EmailSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>









             <div class="modal fade" id="ContractEmailModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
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
         <input     class="form-control  "  required="" name="contract_email_address"   >
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="button" class="btn mr-3 btn-new" id="ContractEmailSave"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
                        </div>
                    </div>
               
                </div>
            </div>
        </div>

               <div class="modal fade" id="ContractEmailModalEdit" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="modal-block-large" aria-hidden="true">
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
                            
                  <input type="hidden" name="contract_email_id_edit" >
<div class="col-sm-3   form-group      ">
                <label class="mandatory">Email Address</label>

</div>
<div class="col-sm-9   form-group      ">
         <input     class="form-control  "  required="" name="contract_email_address_edit"   >
                 
            </div>
   
                         
                        </div>
                        <div class="block-content block-content-full   " style="padding-left: 9mm;">
                            <button type="button" class="btn mr-3 btn-new" id="ContractEmailSaveEdit"  >Save</button>
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
                            <button type="button" class="btn mr-3 btn-new" id="CommentSave"  >Save</button>
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
                            <button type="button" class="btn mr-3 btn-new" id="CommentSaveEdit"  >Save</button>
                            <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>
                    
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
                            <button type="button" class="btn mr-3 btn-new" id="contractDetailsSave"  >Save</button>
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
                            <button type="button" class="btn mr-3 btn-new" id="contractDetailsSaveEdit"  >Save</button>
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
                            <button type="button" class="btn mr-3 btn-new" id="AttachmentSave"  >Save</button>
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



$('#access_type').change(function(){
    if($(this).val()=='admin'){
        $('.divHide').addClass('d-none');
    }
    else{
        $('.divHide').removeClass('d-none');
    }
})
clientArray=[];
var client_key=0;
$('#assigned_clients').change(function(){
    var val=$(this).val();
    var text=$('option:selected',$(this)).text();
    client_key++;
    clientArray.push({id:val,name:text,key:client_key});
   
showClient();

})
function showClient(){
     var html='';
    for(var i=0;i<clientArray.length;i++){
        html+=`<div class="col-lg-2 px-1 mb-2"><div   class="text-truncate   attachmentDivNew comments-section-text"><a href="javascript:;" class="btnDeleteClient" data="${i}"><img src="{{asset('public/img/revoke-removebg-preview.png')}}" width=20></a> &nbsp;<span class="text-truncate  ">${clientArray[i].name}</span></div></div>`;
    }

    $('.ClientDiv').html(html)
}

  var temp_client=[];
$(document).on('click','.btnDeleteClient',function(){
    var id=$(this).attr('data');
  
       var key=clientArray[id].key;
     temp_client.push(clientArray[id]);

     clientArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Client address Deleted. <a href="javascript:;" class="  btn-notify btnClientUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showClient();

})


$(document).on('click','.btnClientUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_client.filter(l=>l.key==id);
         
if (index[0]) { 
  clientArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_client= temp_client.filter(l=>l.key!=id);


      
    showClient(); 
    }
    })


   function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').css('background-image', 'url('+e.target.result +')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);
    }
}


$("#imageUpload").change(function() {
    readURL(this);
});



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











 





















// EMAIL ARRAY

var ContractemailArray=[];
var email_key_count=0;
$('#ContractEmailSave').click(function(){
var email=$('input[name=contract_email_address]').val();
     if(email==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Email Address', delay: 5000});

    }
    else{

           var l=emailArray.length;
                        if(l<5){
           ContractemailArray.push({key:email_key_count,email:email});
           showContractEmail()   
           $('#ContractEmailModal').modal('hide')
           $('input[name=contract_email_address]').val('')
           email_key_count++;
        }
    }
})


$('#ContractEmailSaveEdit').click(function(){
var email=$('input[name=contract_email_address_edit]').val();
var id=$('input[name=contract_email_id_edit]').val();
     if(email==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please enter a value for Email Address', delay: 5000});

    }
    else{

           var l=ContractemailArray.length;
                    
           ContractemailArray[id].email=email;
           showContractEmail()   
           $('#ContractEmailModalEdit').modal('hide')
           $('input[name=contract_email_address_edit]').val('')
       
    }
})

$(document).on('click','.btnEditContractEmail',function(){
        var id=$(this).attr('data');
        $('#ContractEmailModalEdit').modal('show');
                $('input[name=contract_email_id_edit]').val(id);
        $('input[name=contract_email_address_edit]').val(ContractemailArray[id].email);

})
var temp_email=[];
$(document).on('click','.btnDeleteContractEmail',function(){
    var id=$(this).attr('data');
            var key=ContractemailArray[id].key;
     temp_email.push(ContractemailArray[id]);

     ContractemailArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Email address Deleted. <a href="javascript:;" class="  btn-notify btnEmailUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
showContractEmail();
 
})


$(document).on('click','.btnContractEmailUndo',function(){
        var id=$(this).attr('data');
var key=$(this).attr('data1');

        let index = temp_email.filter(l=>l.key==id);
         
if (index[0]) { 
  ContractemailArray.splice(id, 0,index[0]); // 2nd parameter means remove one item only
   temp_email= temp_email.filter(l=>l.key!=id);


      
    showContractEmail(); 
    }
    })


$('#contract_notification').change(function(){
    if($(this).prop('checked')==1){
   $('.ContractEmailHide').removeClass('d-none')
    }
    else{
    $('.ContractEmailHide').addClass('d-none')
}
})
function showContractEmail(){
    var html='';  
    for(var i=0;i<ContractemailArray.length;i++){
        html+=`   <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>@</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <label class="mb-0">${ContractemailArray[i].email}</label>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                        <a type="button"  data="${i}" data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="Edit" class="btnEditContractEmail    btn btn-sm btn-link text-warning">
                                                         <img src="{{url('public/img/editing.png')}}">
                                                        </a>
                                                        <a type="button"   data="${i}" class="j e btn btn-sm btn-link btnDeleteContractEmail text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{url('public/img/trash--v1.png')}}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
    }

     $('#ContractEmailBlock').html(html)
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



function unEntity(str){
   return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
}

var temp_contract=[];
$(document).on('click','.btnDeleteContract',function(){
    var id=$(this).attr('data');
            var key=contractDetailsArray[id].key;
     temp_contract.push(contractDetailsArray[id]);

     contractDetailsArray.splice(id,1);

    Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> SAN Deleted. <a href="javascript:;" class="  btn-notify btnContractUndo ml-4" data1='+id+' data='+key+'>Undo</a>', delay: 5000}); 
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
    console.log(attachmentArray);
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
        window.location.href=unEntity('{{URL::previous()==URL::full()?url('vendors'):URL::previous()}}');


}
})
$(document).on('click','.btnCloseUndo',function(){
          window.location.href=unEntity('{{URL::previous()==URL::full()?url('vendors'):URL::previous()}}');
})
 


$('.saveContract').click(function(){
    $('.tooltip').tooltip('hide');
         $('.show').addClass('d-none');
var data1=$(this).attr('data');
        var vendor_name=$('#vendor_name').val();
     

 if(vendor_name==''){
Dashmix.helpers('notify', {message: '<img src="{{asset('public/img/warning-yellow.png')}}" width="30px" class="mt-n1"> Please select a value for salutation.', delay: 5000});

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
    'url':'{{url('update-vendors')}}',
    dataType:'json',
    async:false,
 
        contentType:false,
        processData:false,
        cache:false,
    success:function(res) {
$('#cert_name').focus()
click=0;

if(data1==0){

        Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Vendor Succesfully added', delay: 5000});

       
    // window.location.href=unEntity('{{URL::previous()==URL::full()?url('vendors'):URL::previous()}}');
}else{
      Dashmix.helpers('notify', {align:'center',message: '<img src="{{asset('public/img/green-check.png')}}" width="30px" class="mt-n1"> Vendor Succesfully added.<br><span  style="margin-left:32px;color:lightgrey">Continue adding Vendor</span>', delay: 5000});
}
         
comment_key_count=0;
temp_comment=[];
commentArray=[];
contractDetailsArray=[];
contract_key_count=0;
contractDetailsArray=[];
temp_contract=[];
attachment_key_count=0;
temp_attachment=[];
content3_image=[];
hostArray=[];
host_key_count=0;
temp_host=[];
attachmentArray=[];
  
 $('#cert_name').val('')
    showEmail();
    showComment();
        showHost();
    showcontractDetails();
    showAttachment();
    showClient();
 
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

$.ajax({
    type:'get',
    'method':'get',
    data:{id:'<?php echo $_GET['id'] ?>'},
    url:"{{url('get-comments-vendors')}}",
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
    url:"{{url('get-attachment-vendors')}}",
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





  
    })
 </script>























