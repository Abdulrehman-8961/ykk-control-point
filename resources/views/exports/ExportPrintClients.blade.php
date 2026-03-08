     
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content') 
 
 <head>
           <!-- END Icons -->
     

 
      <!-- Stylesheets -->
        <!-- Fonts and Dashmix framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
   <style type="text/css"> 
 

.badge-secondary{
  background-color: grey!important;
}
/*#F1AC38  #598DB8*/
.text-yellow{
    color: #F1AC38;
}
.text-blue{
    color: #598DB8;
}

.badges{
  border-radius: 0!important;
  border:none;
  font-size: 20px;
  float: left;
  width: 50%;
  
}

 
/*# sourceMappingURL=bootstrap.css.map */</style>
 </head>
 <body  >
    <?php
 
 
      $qry=DB::Table('clients')->where('id',$_GET['id'])->first();
    

    ?>

   
 <body onload="window.print()">
            <main id="main-container" >
                <!-- Hero -->
                <div class="bg-body-light d-print-none">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Clients</h1>
                        
                        </div>
                    </div>
                </div>
                <!-- END Hero -->

                <!-- Page Content -->
                <div class="content content-boxed">
                    <!-- Invoice -->
                    <div class="block block-rounded">
                        <div class="block-options block-header block-header-default">
                       
                         
                                <!-- Print Page functionality is initialized in Helpers.print() -->
                                <button type="button" class="btn-block-option" onclick="Dashmix.helpers('print');">
                                    <i class="si si-printer mr-1"></i> Print  
                                </button>
                                     <a  download="" href="{{url('export-pdf-clients')}}?id={{$_GET['id']}}" type="button" class=" btn btn-alt-primary"  >
                                    <i class="fa fa-download  " ></i> 
                                </a> 
                         
                        </div>
               <div class="block-content">




                            <div class="p-sm-4 p-xl-7">
                                <!-- Invoice Info -->
                                 
                                      <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="firstname">{{$qry->firstname}}</h3>
                            <div class="block-options">
                                
                            </div>
                        </div>
                        <div class="block-content">
                           <table class="table">
               
                                <tbody>
                                    <tr>
                                        <th>Salutation</th>
                                        <td id="salutation">{{$qry->salutation}}</td>
                                    </tr>
                                    <tr>
                                        <th>Firstname</th>
                                        <td id="firstname">{{$qry->firstname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Lastname</th>
                                        <td id="lastname">{{$qry->lastname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Company Name</th>
                                        <td id="company_name">{{$qry->company_name}}</td>
                                    </tr>
                                    
                                    <tr>
                                        <th>Email Address</th>
                                        <td id="email_address">{{$qry->email_address}}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td id="client_address">{{$qry->client_address}}</td>
                                    </tr>
                                    <tr>
                                        <th>Work Phone</th>
                                        <td id="work_phone">{{$qry->work_phone}}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td id="mobile">{{$qry->mobile}}</td>
                                    </tr>
                                    <tr>
                                        <th>Website</th>
                                        <td id="website">{{$qry->website}}</td>
                                    </tr>
                                    <tr>
                                        <th>Renewal Notification</th>
                                        <td id="renewal_notification">
                                                            @if($qry->renewal_notification==1)
                                                               
                                                                            Active
                                                               
                                                            @else
                                                               
                                                                        Inactive
                                                                                                                          
                                                            @endif


                                        </td>
                                    </tr>
                            
                                     <tr>
                                        <th>Active</th>
                                        <td id="client_status">
                                               @if($qry->client_status==1)
                                                               
                                                                            Yes
                                                              
                                                            @else
                                                                
                                                                        No
                                                                                                                  
                                                            @endif
                                        </td>
                                    </tr>
                                      
                                        
                                </tbody>
                           </table>

                        </div>
                                <!-- END Invoice Info -->
 
                                </div>  
                                <!-- END Table -->

                                <!-- Footer -->
                                <p class="text-muted text-center my-5">
                               
                                </p>
                                <!-- END Footer -->
                            </div>
                        </div>
                    </div>
                    <!-- END Invoice -->
           
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
            @endsection('content')