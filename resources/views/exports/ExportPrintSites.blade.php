    
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')  <!-- Main Container -->



<?php 
                
          $qry=DB::Table('sites as s')->select('s.*','c.firstname')->join('clients as c','c.id','=','s.client_id')->where('s.id',$_GET['id'])->first();
 
 ?>    

   
 <body onload="window.print()">
            <main id="main-container" >
                <!-- Hero -->
                <div class="bg-body-light d-print-none">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Sites</h1>
                        
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
                                     <a  download="" href="{{url('export-pdf-sites')}}?id={{$_GET['id']}}" type="button" class=" btn btn-alt-primary"  >
                                    <i class="fa fa-download  " ></i> 
                                </a> 
                         
                        </div>
               <div class="block-content">
                            <div class="p-sm-4 p-xl-7">
                                <!-- Invoice Info -->
                                <div class="row mb-5">
                                    <!-- Company Info -->
                                    <div class=" " style="float:left;width: 50%;">
                                       <h3 class="mb-0 text-blue" >{{$qry->site_name}}</span></h3>  
                                       <p>{{$qry->firstname}}<br>
                                        {{$qry->address}}<br>
                                            {{$qry->city}},{{$qry->province}} {{$qry->zip_code}}<br>
                                        {{$qry->country}}<br>
                                                  Tel : {{$qry->phone}}<br>
                                               

                                    </div>
                                    <!-- END Company Info -->

                                    <!-- Client Info -->
                                    <div class="  text-right" style="float: left;width: 50%;">
                                        <img src="{{asset('public/img/amaltitek-logo-fr.png')}}" >           
                                     
                                    </div> 
                                   



 
                                    <!-- END Client Info -->
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