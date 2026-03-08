  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')



          <!-- Main Container -->
            <main id="main-container">
                <!-- Hero -->
                <div class="bg-body-light">
                 <!--    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Add New Client</h1>
                            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a  href="{{url('/')}}">Home</a></li>
                                    <li class="breadcrumb-item"><a href="{{url('clients')}}">Clients</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Client</li>
                                </ol>
                            </nav>
                        </div>
                    </div> -->
                </div>
                <!-- END Hero -->

                <!-- Page Content -->
                <div class="content content-full content-boxed">
                    <!-- New Post -->
                    <form action="{{url('insert-clients')}}" class="js-validation" method="POST" enctype="multipart/form-data"  >
                        @csrf<div class="block">

                            <div class="block-header block-header-default">
                                <a class="btn btn-light" href="{{url('clients')}}">
                                    <i class="fa fa-arrow-left mr-1"></i> Manage Clients
                                </a>
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="row justify-content-  push">
<!--                                 	Salutation	Drop down
First Name	Text
Last Name	Text
Client Name	
Client Display Name	Text
Email Adresss	email
Work Phone	10-digit phone number
Mobile	10-digit phone number
Website	URL
Logo	Image
Renewal Notification	Enabled/Disable
Renewal Notification Email	email
On the form, add option to "add new email notification", up to 5 email addresses	 -->
							<div class="col-sm-12 m-auto" >
										<div class="  row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Primary Contact</label>
                                            <div class="col-sm-3 form-group">
                                            	 <select type="text" class="form-control" id="salutation" name="salutation" placeholder="Salutation"  >
                                            	 	<option value="Mr">Mr.</option>
                                            	 	<option value="Mrs">Mrs.</option>
                                            	 	<option value="Ms">Ms.</option>
                                            	 	<option value="Miss">Miss.</option>
                                            	 	<option value="Dr">Dr.</option>
                                            	 </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                            	 <input type="text" class="form-control" id="firstname" name="firstname" placeholder="FirstName"  >
                                            </div>
                                            <div class="col-sm-3 form-group">
                                            	 <input type="text" class="form-control" id="lastname" name="lastname" placeholder="LastName"  >
                                            </div>
                                        </div>

 
 										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Client display Name</label>
                                            <div class="col-sm-6">
                                            	 <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Display Name"  >
                                            </div>
                                          
                                        </div>
 										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Company Name</label>
                                            <div class="col-sm-6">
                                            	 <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company"  >
                                            </div>
                                          
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Email</label>
                                            <div class="col-sm-6">
                                            	 <input type="email" class="form-control" id="email" name="email" placeholder="Email"  >
                                            </div>
                                          
                                        </div>

 										 <div class="f  row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Client Phone</label>
                                            <div class="col-sm-3 form-group">
                                            	 <input type="" class="form-control" id="work_phone" name="work_phone" placeholder="Work Phone"  >
                                            </div>
                                            <div class="col-sm-3 form-group">
                                            	 <input type="" class="form-control" id="mobile" name="mobile" placeholder="Mobile"  >
                                            </div>
                                          
                                        </div>
                                             <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Address</label>
                                            <div class="col-sm-6">
                                                 <textarea  class="form-control" id="client_address" name="client_address" placeholder="Enter Address"  ></textarea>
                                            </div>
                                          
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Website</label>
                                            <div class="col-sm-6">
                                            	 <input type="url" class="form-control" id="website" name="website" placeholder="Website"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Logo</label>
                                            <div class="col-sm-6">
                                            	   <div class="custom-file">
                                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                                    <input type="file" class="custom-file-input" accept="image/*" id="logo" name="logo" data-toggle="custom-file-input">
                                                    <label class="custom-file-label" for="logo">Choose a new image</label>
                                                </div>
                                            </div>
                                          
                                        </div>
 									  <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Renewal Notification</label>
                                            <div class="col-sm-6">
                                            	<div class="custom-control custom-switch custom-control-warning custom-control-lg mb-2">
                                                <input type="checkbox" class="custom-control-input" id="renewal_notification" name="renewal_notification" value="1" checked="">
                                                <label class="custom-control-label" for="renewal_notification"> </label>
                                            </div>
                                            </div>
                                          
                                        </div>
                                         
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Contract Notification Email<br><small><a href="javascript:;" class="add-more">Add New Email Notification(Upto 5)</a></small></label>

                                            <div class="col-sm-6">
                                            	  <input type="email" class="form-control"   name="notification_renewal_email_base"  placeholder="Email 1"  >
                                            </div>
                                          


                                        </div>
                                        		<div id="EmailBlock">     </div>
        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">SSL Certificate Email<br><small><a href="javascript:;" class="add-more1">Add New Email Notification(Upto 5)</a></small></label>

                                            <div class="col-sm-6">
                                                  <input type="email" class="form-control"   name="ssl_certificate_email[]"  placeholder="Email 1"  >
                                            </div>
                                          


                                        </div>
                                                <div id="EmailBlock1">     </div>

                                        	 <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Active</label>
                                            <div class="col-sm-6">
                                            	<div class="custom-control custom-switch custom-control-warning custom-control-lg mb-2">
                                                <input type="checkbox" class="custom-control-input" id="client_status" name="client_status" value="1" checked="">
                                                <label class="custom-control-label" for="client_status"> </label>
                                            </div>
                                            </div>
                                          
                                        </div>

                                </div>
                            </div>
                        </div>
                            <div class="block-content bg-body-light">
                                <div class="row justify-content-center push">
                                    <div class="col-md-10">
                                       
                                        <button type="submit" class="btn btn-alt-success">
                                            <i class="fa fa-fw fa-check ml-1"></i> Save
                                        </button>
                                           <button type="submit" name="saveAndClose" value="1" class="btn btn-alt-success">
                                            <i class="fa fa-fw fa-check ml-1"></i> Save And Close
                                        </button>
                                            <a href="{{url('clients')}}" type="reset" class="btn btn-alt-danger">
                                            <i class="fa fa-fw fa-times  "></i> Cancel

                                        </a>
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END New Post -->
                </div>
                <!-- END Page Content -->
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
 		    // Init Form Validation
        jQuery('.js-validation').validate({
            ignore: [],








            rules: {
                'salutation': {
                    required: true,
                   
                },
                'firstname': {
                    required: true,
                  
                },
                'lastname': {
                    required: true,
                  
                },
                'firstname': {
                    required: true,
 
                },
                'company_name': {
                    required: true,
                     
                },
                'email': {
                    required: true,
                    email:true,
                },
                
                'work_phone': {
                    required: true,
       
                     maxlength: 20
                  
                },
                'mobile': {
                    required: true,
              
                     maxlength: 20
                },
                'logo': {
                    required: true,
                     
                },
                'website': {
                     
                    url: true
                },
                   'notification_renewal_email_base': {
                     
                    email: true
                },
                'notification_renewal_email': {
                     
                    email: true
                },
           
            },
            messages: {
                'val-username': {
                    required: 'Please enter a username',
                    minlength: 'Your username must consist of at least 3 characters'
                },
                'val-email': 'Please enter a valid email address',
                'val-password': {
                    required: 'Please provide a password',
                    minlength: 'Your password must be at least 5 characters long'
                },
                'val-confirm-password': {
                    required: 'Please provide a password',
                    minlength: 'Your password must be at least 5 characters long',
                    equalTo: 'Please enter the same password as above'
                },
                'val-select2': 'Please select a value!',
                'val-select2-multiple': 'Please select at least 2 values!',
                'val-suggestions': 'What can we do to become better?',
                'val-skill': 'Please select a skill!',
                'val-currency': 'Please enter a price!',
                'val-website': 'Please enter your website!',
                'val-phoneus': 'Please enter a US phone!',
                'val-digits': 'Please enter only digits!',
                'val-number': 'Please enter a number!',
                'val-range': 'Please enter a number between 1 and 5!',
                'val-terms': 'You must agree to the service terms!'
            }
        });

        // Init Validation on Select2 change
        jQuery('.js-select2').on('change', e => {
            jQuery(e.currentTarget).valid();
        });



 		$('.add-more').click(function(){
	 				var html='';
	 					var l=$('.emailRows').length;
	 					if(l<4){
	 					 	html+=' <div class="form-group emailRows row" data="'+l+'">'+
                                            '<label class="col-sm-3 col-form-label" for="example-hf-email"></label>'+
                                            '<div class="col-sm-6"><div class="input-group">'+
                                            	  '<input type="email" class="form-control"   name="notification_renewal_email[]"  placeholder="Email '+(l+parseInt(2))+'"  >'+
                                            '<div class="input-group-append">'+
                                                    '<button type="button"  data="'+l+'" class="btn btn-alt-danger btnDel">'+
                                                        '<i class="fa fa-times"></i>'+
                                                    '</button>'+
                                                '</div></div></div>'+
                                          '</div>';
 				$('#EmailBlock').append(html)
 		}
 	})


 		$('#EmailBlock').on('click','.btnDel',function(){
 		 
 			var id=$(this).attr('data');
 			$('.emailRows[data='+id+']').remove()
 			 var sno=0;
 			 $('.emailRows input').each(function(){
 			 	$(this).attr('placeholder','Email '+(++sno))
 			 	  			 })
 		})





        $('.add-more1').click(function(){
                    var html='';
                        var l=$('.emailRows1').length;
                        if(l<4){
                            html+=' <div class="form-group emailRows1 row" data="'+l+'">'+
                                            '<label class="col-sm-3 col-form-label" for="example-hf-email"></label>'+
                                            '<div class="col-sm-6"><div class="input-group">'+
                                                  '<input type="email" class="form-control"   name="ssl_certificate_email[]"  placeholder="Email '+(l+parseInt(2))+'"  >'+
                                            '<div class="input-group-append">'+
                                                    '<button type="button"  data="'+l+'" class="btn btn-alt-danger btnDel">'+
                                                        '<i class="fa fa-times"></i>'+
                                                    '</button>'+
                                                '</div></div></div>'+
                                          '</div>';
                $('#EmailBlock1').append(html)
        }
    })

        
        $('#EmailBlock1').on('click','.btnDel',function(){
         
            var id=$(this).attr('data');
            $('.emailRows1[data='+id+']').remove()
             var sno=0;
             $('.emailRows1 input').each(function(){
                $(this).attr('placeholder','Email '+(++sno))
                             })
        })


 	})
 </script>