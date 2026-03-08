
  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')
<?php 
$qry=DB::Table('notification_settings') ->first();
?>



          <!-- Main Container -->
            <main id="main-container">
                <!-- Hero -->
      <!--           <div class="bg-body-light">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Notification Settings</h1>
                            <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a  href="{{url('/')}}">Home</a></li>
                                
                                    <li class="breadcrumb-item active" aria-current="page">Notification Settings</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div> -->
                <!-- END Hero -->

                <!-- Page Content -->
                <div class="content content-full content-boxed">
                    <!-- New Post -->
                    <form action="{{url('update-settings')}}" class="js-validation" method="POST" enctype="multipart/form-data"  >
                        @csrf<div class="block">
                         	 

                            <div class="block-header block-header-default">
                              Settings
                               
                                <div class="block-options">
                                  
                                </div>
                            </div>
                            <div class="block-content">
                                <div class="row justify-content-  push">
 
							<div class="col-sm-12 m-auto" >
									 
 										<div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 1</label>
                                            <div class="col-sm-6">
                                            	 <input type="number" class="form-control" id="interval_1" name="interval_1" value="{{@$qry->interval_1}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 2</label>
                                            <div class="col-sm-6">
                                                 <input type="number" class="form-control" id="interval_2" name="interval_2" value="{{@$qry->interval_2}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 3</label>
                                            <div class="col-sm-6">
                                                 <input type="number" class="form-control" id="interval_3" name="interval_3" value="{{@$qry->interval_3}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 4</label>
                                            <div class="col-sm-6">
                                                 <input type="number" class="form-control" id="interval_4" name="interval_4" value="{{@$qry->interval_4}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 5</label>
                                            <div class="col-sm-6">
                                                 <input type="number" class="form-control" id="interval_5" name="interval_5" value="{{@$qry->interval_5}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 6</label>
                                            <div class="col-sm-6">
                                                 <input type="number" class="form-control" id="interval_6" name="interval_6" value="{{@$qry->interval_6}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Interval 7</label>
                                            <div class="col-sm-6">
                                                 <input type="number" class="form-control" id="interval_7" name="interval_7" value="{{@$qry->interval_7}}" placeholder="No Of Days"  >
                                            </div>
                                          
                                        </div>
                                         
                                            <div class="form-group row">
                                            <label class="col-sm-3 col-form-label" for="example-hf-email">Emails FROM</label>
                                            <div class="col-sm-6">
                                                 <input type="" class="form-control" id="from_name" name="from_name" value="{{@$qry->from_name}}" placeholder=""  >
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
 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
 <script type="text/javascript">
 	
 	$(function(){
 		  @if(Session::has('success'))
             Swal.fire({
  title: '{{Session::get('success')}}',
 
 
  confirmButtonText: 'Ok'
})
             @endif
 		    // Init Form Validation
        jQuery('.js-validation').validate({
            ignore: [],

        rules: {
 
                'distributor_name': {
                    required: true,
                  
                },
                 
   
           
            },
           
        });

        // Init Validation on Select2 change
        jQuery('.js-select2').on('change', e => {
            jQuery(e.currentTarget).valid();
        });



 
 	})
 </script>