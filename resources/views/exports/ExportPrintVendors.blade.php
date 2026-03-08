    
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')  <!-- Main Container -->

<?php 
 
if(sizeof($_GET)>0){

$orderby='desc';
$field='id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}


 
     $qry=DB::table('vendors')->where('is_deleted',0)->where(function($query){
         $query->Orwhere('vendor_name','like','%'.@$_GET['search'].'%');
    
 
     }) ->orderBy($field,$orderby)->get(); 
}
 else{
$qry=DB::table('vendors') ->where('is_deleted',0)->orderBy('id','desc')->get(); 
 
 }
 

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
                        <div class="block-header block-header-default">
                       
                            <div class="block-options">
                                <!-- Print Page functionality is initialized in Helpers.print() -->
                                <button type="button" class="btn-block-option" onclick="Dashmix.helpers('print');">
                                    <i class="si si-printer mr-1"></i> Print  
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="p-sm-4 p-xl-7">
                                <!-- Invoice Info -->
                                <div class="row  ">
                                    <!-- Company Info -->
                                    <div class="col-6">
                                        <p class="h3"><img src="{{asset('public/img/amaltitek-logo-fr.png')}}"></p>
                                     
                                    </div>
                                    <!-- END Company Info -->

                                    <!-- Client Info -->
                                    <div class="col-6 text-right">
                                        <p class="h3">Vendors</p>
                                      
                                    </div>
                                    <!-- END Client Info -->
                                </div>
                                <!-- END Invoice Info -->

                                <!-- Table -->
                          
                                    <table class="table table-bordered">
                                        <thead class="bg-body">
                                              <tr>
                                 <th>#</th>
                                                        

                                       

                                            <th> Name </th>
                                         
                                           
                                            
                                        </tr>
                                        </thead>
                                      <tbody id="showdata">
                                          @php  $sno=0; @endphp
                                        @foreach($qry as $q)
                                        <tr>
                                             <td>{{++$sno}}</td>
                                            
                                            <td class="font-w600">
                                                 {{$q->vendor_name}} 
                                            </td>
                                       
                                         
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    </table>
                              
                                <!-- END Table -->

                                <!-- Footer -->
                               <!--  <p class="text-muted text-center my-5">
                                    Thank you for doing business with us.
                                </p> -->
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