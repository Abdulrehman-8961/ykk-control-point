  
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')


<?php 


$limit=20;
        $no_check=DB::Table('settings')->where('user_id',Auth::id())->first();
if(isset($_GET['limit']) && $_GET['limit']!=''){
    $limit=$_GET['limit'];

        if($no_check!=''){
            DB::table('settings')->where('user_id',Auth::id())->update(['notifications'=>$limit]);
        }
        else{
            DB::table('settings')->insert(['user_id'=>Auth::id(),'notifications'=>$limit]);
        }
        
}
else{
           
        if($no_check!=''){
            if($no_check->notifications!=''){
            $limit=$no_check->notifications;

        }
        }
}



if(sizeof($_GET)>0){

$orderby='desc';
$field='id';
if(isset($_GET['orderBy'])){
$orderby=$_GET['orderBy'];
$field=$_GET['field'];
}

 
     $qry=DB::table('notifications') ->where(function($query){
         $query->Orwhere('type','like','%'.@$_GET['search'].'%');
        $query->Orwhere('from_email','like','%'.@$_GET['search'].'%');
        $query->Orwhere('to_email','like','%'.@$_GET['search'].'%');
        $query->Orwhere('subject','like','%'.@$_GET['search'].'%');
 
     }) ->orderBy($field,$orderby)->paginate($limit); 
}
 else{
$qry=DB::table('notifications')  ->orderBy('id','desc')->paginate($limit); 
 
 }

  
 
 ?>       <!-- Main Container -->
        

            <main id="main-container pt-0">
                <!-- Hero -->
           
           
<style type="text/css">
        .dropdown-menu {
        z-index: 100000!important;
    }
    .pagination{
        margin-bottom: 0px;
    }
  #page-header{
        display: none;
    }
.ActionIcon{

    border-radius: 50%;
    padding: 6px;
}
.ActionIcon:hover{

 background:#dadada;
}
@media only print{
    .no-print{
        display: none!important;
    }
    #showData{
        height: 100%!important;
      
    }   
    .content{
    background: #F0F3F8;
 
    }
}
   body {
overflow: -moz-scrollbars-vertical;
  overflow-x: hidden;
}
 .blockDivs .block-header-default {
    background-color: #f1f3f8;
    padding: 7px 1.25rem;
}
.blockDivs{
    border: 1px solid lightgrey;
    margin-bottom: 10px!important;
}
 
.cert_type_button label,
.cert_type_button input {
  
 
}
.cert_type_button{
    float: left;
}
.cert_type_button input[type="radio"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.cert_type_button input[type="radio"]:checked + label {
    background: #4194F6;
    font-weight: bold;
      color: white;
}

.cert_type_button label:hover {
  
 
 
background-color:#EEEEEE;
color: #7F7F7F;
 
 
}

.cert_type_button label {
  
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
.modal-lg, .modal-xl {
    max-width: 950px;
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
.attachmentDiv{
        border: 1px solid lightgrey;
    padding: 7px;
    font-size: 10px;
    border-radius: 32px;
    color: grey;
    width: 50px;
}
.dropdown-menu{
        border: 1px solid #D4DCEC!important;
    box-sizing: 1px 1px 1pxo #D4DCEC;
    box-shadow: 6px 6px 8px #8f8f8f5e;
    border-radius: 11px;
}
.bs-select-all,.bs-deselect-all,.bs-actionsbox .btn-light {
      border: 1px solid #D9D9D9!important;
    background: white!important;

    color: #2080F4!important;
    font-weight: normal!important;
font-family: Calibri!important;
font-size: 12pt!important;
border-radius: 15px!important;
padding-top: 0px!important;
padding-bottom: 0px!important;
margin-top: 10px!important;
margin-bottom: 10px!important;
margin-left: 10px;
margin-right: 10px;
height: 35px!important;
padding-left: 10px;
padding-right: 10px;
min-width: 90px!important;
}
 

 .bs-deselect-all:hover{
background-color: #EEEEEE!important;
    color: #7F7F7F!important;
    }
    .bs-select-all:hover{
background-color: #EEEEEE!important;
    color: #7F7F7F!important;
    }

.c1{
    color: #3F3F3F;
font-family: 'Calibri'; 
}
.c2{
    color: #7F7F7F;
font-family: 'Calibri'; 
}
.c3{
    color: #595959;
font-family: 'Calibri'; 
}
.cert_type_button label,
.cert_type_button input {
  
 
}
.cert_type_button{
    float: left;
}
.cert_type_button input[type="radio"] {
  opacity: 0.011;
  z-index: 100;

position: absolute;
}

.cert_type_button input[type="radio"]:checked + label {
    background: #4194F6;
    font-weight: bold;
      color: white;
}

.cert_type_button label:hover {
  
 
 
background-color:#EEEEEE;
color: #7F7F7F;
 
 
}

.cert_type_button label {
  
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
.btnNewAction:hover,.btnNewAction1:hover,.btnNewAction2:hover{
    background: #59595930;
    border-radius: 50%;
 }
 .btnNewAction{
    height: 29px;
 }   
 .btnNewAction1{
    height: 23px;

 }   
 .btnNewAction2{
    height: 20px;
 }   
 .HostActive{
    font-family: Calibri;
    font-size:9pt;
    font-weight: bold;
    color:#1EFF00 ;
   letter-spacing:0px ;
 }
 .HostInActive{
    font-family: Calibri;
    font-size:9pt;
    font-weight: bold;
    color:#E54643 ;
      letter-spacing:0px ;
 
 }
  .SSLActive{
    font-family: Calibri;
    font-size:9pt;
    font-weight: bold;
    color:#FFCC00 ;
      letter-spacing:0px ;

 
 }
 .text-info{
    color: #4194F6!important;
 }
  .text-danger{
    color: #E54643!important;
 }
   .text-warning{
    color: #FFCC00!important;
 }
</style>
            
             
                <!-- Page Content -->
                <div class="con   no-print page-header py-2" id="">
                    <!-- Full Table -->
                    <div class="b   mb-0  ">
                    
                        <div class="block-content pt-0 mt-0">

<div class="TopArea" style="position: sticky; 
    padding-top: 8px;
    z-index: 1000;
    
    padding-bottom: 5px;">
    <div class="row" >
        <div class="col-sm-3">

         
       <form class="push mb-0"   method="get" id="form-search" action="{{url('notifications/')}}">
                                        
                                <div class="input-group">
                                    <input type="text" value="{{@$_GET['search']}}" class="form-control searchNew" name="search" placeholder="Search Notifications">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                              <img src="{{asset('public/img/ui-icon-search.png')}}" width="23px">
                                        </span>
                                    </div>
                                </div>
                                 <div class="    float-left " role="tab" id="accordion2_h1">
                                         
                                    
                                   <!--     <a href="javascript:;" class="text-info" data-toggle="modal" data-target="#filterModal" >Modify Filters</a> -->
                                      
                                            </div>  
                            </form>
</div>
<div class="col-sm-3  1"  >
    
                             
</div>
<div class="col-sm-3 ">
    {{$qry->appends($_GET)->onEachSide(0)->links()}}
  </div>
 <div class="d-flex text-right col-lg-3 justify-content-end" ><form  id="limit_form" class="ml-2 mb-0"  action="{{url('notifications')}}?{{$_SERVER['QUERY_STRING']}}">
                                <select name="limit" class="float-right form-control mr-3   px-0" style="width:auto">
                                        <option value="10" {{@$limit==10?'selected':''}}>10</option>
                                        <option value="25" {{@$limit==25?'selected':''}}>25</option>
                                        <option value="50" {{@$limit==50?'selected':''}}>50</option>
                                        <option value="100" {{@$limit==100?'selected':''}}>100</option>
                                </select>
                            </form>
                      
                        @if(@Auth::user()->role=='admin')
                   
                        <a href="{{url('settings')}}"  data-toggle="tooltip" data-title="Settings"class="mr-3 text-dark d3   " ><img src="{{asset('public/img/ui-icon-settings.png')}}" width="23px"></a>
                          
                            @endif
                        <!-- User Dropdown -->
                        <div class="dropdown d-inline-block">
                            <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  >
                               
                            @if(Auth::user()->user_image=='')
                                <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public')}}/dashboard_assets/media/avatars/avatar2.jpg"  alt="">
                                @else
                                  <img class="img-avatar imgAvatar img-avatar48" src="{{asset('public/client_logos/')}}/{{Auth::user()->user_image}}"  alt="">
                                
                                @endif

                            </a>
                            <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">
                              
                                <div class="p-2">
                                    @auth
                                    <a class="dropdown-item" href="{{url('change-password')}}">
                                        <i class="far fa-fw fa-user mr-1"></i> My Profile
                                    </a>
                                   
                                    
                                    
                                    


                                    <!-- END Side Overlay -->
<form id="logout-form" class="mb-0" method="post" action="{{url('logout')}}">
  @csrf
</form>
                                    <div role="separator" class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:;" onclick="document.getElementById('logout-form').submit()">
                                        <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                    </a>
                                    @else
                                         <a class="dropdown-item" href="{{url('/login')}}">
                                        <i class="far fa-fw fa-user mr-1"></i> Login
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </div>



                     </div>
                 </div>


                            </div>
                        </div>
                    </div>

                        





                            <div class="table-responsive">
                                <table class="table    table-striped table-vcenter" >
                                    <thead class="thead thead-dark" >
                                        <tr>
                                 <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=id" class=" 
                                                ">#  </a></th>
                              
                                        
                                            <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=type" class=" 
                                                ">Type  </a></th>
                                          
                                            <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=created_at" class=" 
                                                ">Date/Time</<a></th>
                                            <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=from_email" class=" 
                                                ">From</a></th>
                                                   <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=to_email" class=" 
                                                ">To</a></th>
                                                <th><a href="{{url()->current()}}?{{isset($_GET['search'])?'search='.$_GET['search']:''}}&orderBy={{@$_GET['orderBy']=='desc'?'asc':'desc'}}&field=subject" class=" 
                                                ">Subject</a></th> 
                                           
                                           
                                        </tr>
                                    </thead>
                                    <tbody id="showdata">
                                          @php  $sno= $qry->perPage() * ($qry->currentPage() - 1);@endphp
                                        @foreach($qry as $q)
                                        <tr>
                                             <td>{{++$sno}}</td>
                                                <td>{{$q->type}}</td>
                                <td>{{date('Y-M-d',strtotime($q->created_at))}}</td>
                                               <td>{{$q->from_email}}</td>
                                                           <td>{{$q->to_email}}</td>
                                               <td>{{$q->subject}}</td>
                                       
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                       
                            </div>
                                 <div class="row pt-3 mb-2">
                              
                    </div>
                    <!-- END Full Table -->
                        </div>
                    </div>
                    <!-- END Full Table -->
 
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
  $('select[name=limit]').change(function(){
    var form=$('#limit_form');
   if (form.attr("action") === undefined){
        throw "form does not have action attribute"
    }


    let url = form.attr("action");
    if (url.includes("?") === false) return false;
 
    let index = url.indexOf("?");
    let action = url.slice(0, index)
    let params = url.slice(index);
    url = new URLSearchParams(params);
    for (param of url.keys()){
        if(param!='limit'){
        let paramValue = url.get(param);

        let attrObject = {"type":"hidden", "name":param, "value":paramValue};
        let hidden = $("<input>").attr(attrObject);
        form.append(hidden);
    }
}
    form.attr("action", action)

    form.submit();
})



             
               $('#showdata').on('click','.btnEdit',function(){
                    var id=$(this).attr('data');
                $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-users')}}',
                    success:function(res){
                        $('#viewData').modal('show');
                            $('#salutation').html(res.salutation)
                            $('#firstname').html(res.firstname+' '+res.lastname)
                             
                          
                            $('#email_address').html(res.email)

                            $('#work_phone').html(res.work_phone)
                            $('#mobile').html(res.mobile)
                          
                            $('#portal_access').html(res.portal_access=='1'?'<div class="badge badge-success">On</div>':'<div class="badge badge-danger">Off</div>')
                 $('.printDiv').attr('href','{{url('export-print-users')}}?id='+id)
                                $('.pdfDiv').attr('href','{{url('export-pdf-users')}}?id='+id)
                                
                    }
                })

                
  $.ajax({
                    type:'get',
                    data:{id:id},
                    url:'{{url('show-users-clients')}}',
                    success:function(res){
   var html='';
   html+='<div class="row  ">';
                  for(var i=0;i<res.length;i++){
                            html+=  '<div class="col-lg-6">'+ 
                                    '<a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">'+
                                        '<div class="block-content block-content-full d-flex align-items-center justify-content-between">'+
                                            '<div>'+
                                                '<div class="font-w600 mb-1">'+res[i].firstname+'</div>'+
                                                '<div class="font-size-sm text-muted">'+res[i].work_phone+'</div>'+
                                            '</div>'+
                                            '<div class="ml-3">'+
                                               '<img class="img-avatar " style="object-fit:cover" src="{{asset('public/client_logos/')}}/'+res[i].logo+'" alt="">'+

                                            '</div>'+
                                        '</div>'+
                                    '</a>'+
                                
                     
                            '</div>';
                        }
                        html+='</div>'
                        $('#access_to_client').html(html)

                    }
                })

               })


               $('#showdata').on('click','.btnDelete',function(){
                    var id=$(this).attr('data');
                   
                    var c=confirm("Are you sure want to delete this User");
                    if(c){
                        window.location.href="{{url('delete-users')}}?id="+id;
                    }
                            })  
           })
</script>
