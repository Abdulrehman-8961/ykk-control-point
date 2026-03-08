    
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')  <!-- Main Container -->

<?php 
 
$qry=DB::table('users')->where('id',$_GET['id']) ->orderBy('id','desc')->first(); 
 
 
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
                                     <a  download="" href="{{url('export-pdf-users')}}?id={{$_GET['id']}}" type="button" class=" btn btn-alt-primary"  >
                                    <i class="fa fa-download  " ></i> 
                                </a> 
                         
                        </div>
                        <div class="block-content">
                           <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" id="firstname">{{$qry->firstname}} {{$qry->lastname}}</h3>
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
                                        <th>Email Address</th>
                                        <td id="email_address">{{$qry->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>Work Phone</th>
                                        <td id="work_phone">{{$qry->work_phone}}</td>
                                    </tr>
                                    <tr>
                                        <th>Mobile</th>
                                        <td id="mobile">{{$qry->mobile}}</td>
                                    </tr>
                                  
                                     <th>Portal Access</th>
                                        <td id="portal_access">
                                            {{$qry->portal_access==1?'Active':''}}
                                        </td>
                                    </tr>
                                  

                                     
                                      <tr>
                                        <th>Access to Client</th>
                                        <td id="access_to_client">
                                                      <?php 
                                                         
            $arr=explode(',',$qry->access_to_client);
          $data=DB::Table('clients')->whereIn('id',$arr)->get();

                                                     ?>
                                                     @foreach($data as $d)
                                        <div class="col-6">
                                     <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)"> 
                                         <div class="block-content block-content-full d-flex align-items-center justify-content-between"> 
                                             <div> 
                                                 <div class="font-w600 mb-1">{{$d->firstname}} </div> 
                                                 <div class="font-size-sm text-muted">{{$d->work_phone}} </div> 
                                             </div> 
                                             <div class="ml-3"> 
                                                <img class="img-avatar " style="object-fit:cover" src="{{asset('public/client_logos/')}}/{{$d->logo}}" alt=""> 

                                             </div> 
                                         </div> 
                                     </a> 
                             </div>
                             @endforeach
                                
                     


                        </td>
                                    </tr>
                                     
                                     
                                </tbody>
                           </table>

                        </div>
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
            @endsection('content')