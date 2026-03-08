     
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
.text-new-success{

background-color: #4DD827  !important ;
 font-weight: 200!important;
 letter-spacing: 1px;

}
.text-new-grey{
 
color: #495057  !important;
 

}
   .BLOCKCONTET *{

  font-family: Source Sans Pro!important;
 }
.badges{
  border-radius: 0!important;
  border:none;
  font-size: 20px;
  float: left;
  width: 50%;
  
}.fieldInfo p,.fieldInfo div,.fieldInfo b{
    color: #495057!important;
    font-family: Open Sans!important;
    font-size: 10pt!important;
}
.fieldInfo1 p ,.fieldInfo1 b{
    color: #495057 ;
    font-family: Open Sans!important;
    font-size: 10pt!important;
}
figcaption{
  background-color:  #BFBFBF;
  font-family: Open Sans!important;
  font-size: 8pt!important;
  text-align: center;
  padding: 0px 10px;
margin-top: 1px;

}
figure{
  text-align: center;
}

.textDiv p{
  font-size: 10px;
  margin-bottom: 10px;
}
.bg-orange{
  background-color: #FF9953;
}
.text-grey{
  color: #7D8288;
}
.text-lightgrey{
  color: lightgray!important;
}
.font-weight-bold{
  font-weight: bold!important;
}
.ribbon-box {
    position: absolute;
    top: 0.75rem;
    left: 0;
    padding: 0 0.75rem;
    height: 2rem;
    line-height: 2rem;
    z-index: 5;

    font-weight: 600;
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.text-orange{
  color: #FF8028;
}
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
.footer{
  display: none;
}
 @media print {
  body{
    background-color: white;
     padding: 0px!important

  }
 
 .footer{
  display: block;
}
 .content-boxed{
 padding: 0px!important

  }
  
  .block {
 
    padding: 0px
  }

  
 }

address{
  font-size: 20px;
}
figcaption{
  font-size: 13px;
}
/*# sourceMappingURL=bootstrap.css.map */</style>
 </head>
 <body  >
    <?php
   $qry=DB::table('assets as a')->select('a.*','s.site_name','c.client_address','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name','o.operating_system_image','s.address','s.city','s.country','s.phone','s.zip_code','s.province','c.logo','at.asset_icon','n.vlan_id as vlanId')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')->leftjoin('network as n','a.vlan_id','=','n.id')->where('a.id',$_GET['id'])->first();
    


    ?>

   
 <body onload="window.print()">
            <main id="main-container" >
                <!-- Hero -->
                <div class="bg-body-light d-print-none">
                    <div class="content content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Assets</h1>
                        
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
                                     <a  download="" href="{{url('pdf-asset')}}?id={{$_GET['id']}}" type="button" class=" btn btn-alt-primary"  >
                                    <i class="fa fa-download  " ></i> 
                                </a> 
                         
                        </div>
               <div class="block-content" style="position:relative;height: 100%;">
                    @if($qry->SupportStatus=='N/A')
                                                        <?php $color='bg-secondary';?>
                                                                
                                                        @elseif($qry->SupportStatus=='Supported')
                                                          <?php $color='bg-primary';?>
                                                        @elseif($qry->SupportStatus=='Unassigned')
                                                        <?php $color='bg-warning';?>
                                                        @elseif($qry->SupportStatus=='Expired')
                                                            <?php $color='bg-danger';?>

                                                            @else
                                                          <?php $color='bg-secondary';?>
                                                        @endif
                      <div style="height: 70px;width: 100%;position: absolute;left: 0;top: 0;z-index: 0;" class="{{$color}}  ">
        </div>
     <div class="ribbon-box {{$qry->AssetStatus=='1'?'bg-success':'bg-danger'}}   text-white" style="  
    padding: 0 2.5rem;
    height: 2rem;
    width: fit-content;
    line-height: 2rem;
  font-size: 14pt;font-weight: bold;font-family: : Source Sans Pro;

    font-weight: 600;
        top: 20px;
        border-radius: 0;
 ;font-weight: bold;">
                                          {{$qry->AssetStatus==1?'Active':'Inactive'}}
                                        
                                    </div>
               <div class="block-content BLOCKCONTET " style="padding-top:70px ">

                           
                                <div class="row  pb-5 " >
                                    <!-- Company Info -->
                                    <div class=" " style="float:left;width: 50%;">
                                       <h1 class="mb-n2   text-uppercase   " style="color: #262626;font-size: 26pt;font-weight: bold;font-family: : Source Sans Pro;">        <img width="60" height="55" class="img-avatar  img-av mb-0 atar48" style="object-fit: cover" src="{{asset('public/operating_system_logos/')}}/{{$qry->operating_system_image}}" alt=""> 
                                       {{$qry->hostname}} </h1>
                                       <h4 class="   mb-0  ml-5 pl-4 " style="font-weight:bold;color:#495057;font-size: 15pt"> <img width="35" height="35" src="{{asset('public/asset_icon/')}}/{{$qry->asset_icon}}" style="object-fit: cover;" >  &nbsp;{{$qry->role}}</h4> 
                                       <?php
                  
   $contract=DB::table('contract_assets as a')->join('contracts as c','c.id','=','a.contract_id')->where('a.hostname',$qry->id)->where('c.contract_status','!=','Inactive')->where(function($query){
        $query->Orwhere('a.status','!=','Inactive');
    $query->Orwhere('a.status',null);
    }) ->where('a.is_deleted',0)->first();
                                        ?>
                                        <hr class="mb-1 mt-1">
                                          @if($qry->HasWarranty!=1)
                                           <div style="width: 100%; ">
                                          <div  style="float: left;width: 50%">
                                       
                                       <h5 class="mb-2" style="font-weight:bold;color: #495057;font-size: 10.5pt;;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-contract.png')}}" > &nbsp;&nbsp;&nbsp;No Support Contract</h5>
                                          </div>
                                         
                                      </div>
                                          @else
                                                @if($contract=='')
                                                        <div style="width: 100%; ">
                                          <div  style="float: left;width: 50%">
                                       
                                       <h5 class="mb-2" style="font-weight:bold;color: #495057;font-size: 10.5pt;;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-contract.png')}}" > &nbsp;&nbsp;&nbsp;No Contract Assigned</h5>
                                          </div>
                                         
                                      </div>
                                                @endif
                                       
                                             @if($contract!='')
                                        <div style="width: 100%; ">
                                          <div  style="float: left;width: 50%">
                                       
                                       <h5 class="mb-2" style="font-weight:bold;color: #495057;font-size: 10.5pt;;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-contract.png')}}" > &nbsp;&nbsp;&nbsp;{{@$contract->contract_no}}</h5>
                                          </div>
                                          <div style="float:right;width: 50%; ">
                                        <h6 class="mb-2"  style="font-weight:bold;color:#495057;font-size: 10.5pt;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-date.png')}}" > &nbsp;&nbsp;&nbsp;{{@$contract->contract_end_date!=''?date('Y-M-d',strtotime($contract->contract_end_date)):''}}</h6>
                                         
                                        </div>
                                      </div>
                                       @endif
                                          @endif
                                      <?php $ssl=DB::table('ssl_certificate as s')->leftjoin('vendors as v','v.id','=','s.cert_issuer')->where('cert_hostname',$_GET['id'])->where('s.is_deleted',0)->first(); ?>
                                                    @if($qry->ntp!='1')
                                                                 <div style="width: 100%;  ">
                                          <div  style="float: left;width: 50%">
                                       <h5 style="font-weight:bold;color: #495057;font-size: 10.5pt;;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-cert.png')}}" > &nbsp;&nbsp;&nbsp;
                                                 No SSL Certificate
                                        
                                      </h5>
                                          </div>
                                       
                                      </div>


                                                    @else

                                                      @if($ssl=='')
                                                           <div style="width: 100%;  ">
                                          <div  style="float: left;width: 50%">
                                       <h5 style="font-weight:bold;color: #495057;font-size: 10.5pt;;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-cert.png')}}" > &nbsp;&nbsp;&nbsp;
                                                 No SSL Cert Assigned
                                        
                                      </h5>
                                          </div>
                                       
                                      </div>
                                                      @endif
                                                    @endif
                                       @if($ssl!='')
                                       <div style="width: 100%;  ">
                                          <div  style="float: left;width: 50%">
                                       <h5 style="font-weight:bold;color: #495057;font-size: 10.5pt;;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-cert.png')}}" > &nbsp;&nbsp;&nbsp;
                                          @if(@$ssl->cert_type=='internal')
                                                  Internal Cert
                                          @elseif(@$ssl->cert_type=='public')
                                                {{@$ssl->vendor_name}} {{$ssl->vendor_name!=''?'Public Cert':''}}
                                          @endif
                                      </h5>
                                          </div>
                                          <div style="float:right;width: 50%; ">
                                        <h6  style="font-weight:bold;color:#495057;font-size: 10.5pt;font-family: Open Sans!important;"><img width="40" height="40" src="{{asset('public/img/icon-date.png')}}" > &nbsp;&nbsp;&nbsp;{{@$ssl->cert_edate!=''?date('Y-M-d',strtotime($ssl->cert_edate)):''}}</h6>
                                        </div>
                                      </div>
                                        
                                        @endif
                                     
                                      

                                    </div>
                                    <!-- END Company Info -->

                                    <!-- Client Info -->
                                    <div class="  text-right" style="float: left;width: 50%;">
                                        <h2 style="font-weight: bold;color: #262626;font-size: 18pt">        &nbsp; <img width="60" height="55" src="{{asset('public/client_logos/')}}/{{$qry->logo}}" class="img-avatar" >        <br>{{$qry->firstname}}    
                               </h2>
                                      <address class="mt-n3" style=" - : ;color: #495057;font-size:8pt;font-family: Open Sans!important;">
                                          
                                        {{$qry->address}}<br>
                                            {{$qry->city}},{{$qry->province}} {{$qry->zip_code}}<br>
                                        {{$qry->country}}<br>
                                                  Tel : {{$qry->phone}}<br>
                                               
                                        </address>
                                    </div> 
                                         <div class="mt-4 fieldInfo  " style="float:left;width:30%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class="text-  " style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size: 9.5pt;">General</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                            <div style="margin-top: -10px;margin-bottom: 10px;">
                                              @if($qry->load_balancing==1)
                                                  <div class="badgeIE badge    text-white badge-success  "  style="color: white!important; ">Load Balanced</div>
                                              @else
                                        <div class="badgeIE  badge   badge-secondary   text-white"  style="color: lightgray!important "  >Load Balanced</div>
                                                
                                              @endif

                                                @if($qry->clustered==1)
                                                  <div class="badgeIE badge  badge-success  " style="color: white!important; ">Clustered</div>
                                         @else
                                        <div class="badgeIE  badge   badge-secondary"  style="color: lightgray!important; ">Clustered</div>
                                                  
                                              @endif
                                              </div>
                                        <p class="text-grey">

                                         
                                            <span class="  " style="font-weight:bold; ;">Environment</span>&nbsp;&nbsp;&nbsp;&nbsp;  {{$qry->use_}}
                                          </p>
                                           <p class="text-grey">    <span  style="font-weight:bold; ;"> O / S </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$qry->operating_system_name}}
                                            </p>
                                            
                                     <p class="text-grey">
                                                  
                                                @if($qry->asset_type=='physical')
                                                  <span   style="font-weight:bold; ;">   Processor</span>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span s >{{ $qry->vcpu }}
                                                   @if($qry->asset_type=='physical')
                                                   {{$qry->cpu_sockets}} x {{$qry->cpu_model}} {{$qry->cpu_cores}}C {{$qry->cpu_freq}}GHz 
                                                        @endif
                                                 @else
                                             <span   style="font-weight:bold; ;">     vCPU  </span>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span  >{{ $qry->vcpu }} 
                                              @if($qry->asset_type=='physical')
                                              {{$qry->cpu_sockets}} x {{$qry->cpu_model}} {{$qry->cpu_cores}}C {{$qry->cpu_freq}}GHz 
                                                 @endif
                @endif
                                          </p>
                                           <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> Memory</span>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  > {{$qry->memory}} GB </span>
                                          </p>
                                          
                                  </div>
                                        
      
                                </div>
                                <!-- END Invoice Info -->
        <div class=" fieldInfo mt-4 " style="float:left;width:30%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class="text- "  style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size: 9.5pt;">Network</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                             @if($qry->internet_facing==1)
                                                  <div class="badgeIE badge  badge-success  " style="color:white!important;margin-bottom:10px;margin-top:-10px;">Internet Facing</div>
                                         @else
                                        <div class="badgeIE  badge   badge-secondary" style="color:lightgray!important;margin-bottom:10px;margin-top:-10px;">Internet Facing</div>
                                                   
                                              @endif
                                        <p class="text-grey">
                                            <span class="  " style="font-weight:bold; ;">Domain</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$qry->domain_name}}
                                          </p>
                                           <p class="text-grey">    <span  style="font-weight:bold; ;"> Primary IP </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$qry->ip_address}}
                                            </p>
                                            <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> vLAN ID </span>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$qry->vlanId}} 
                                            </p>
                                            <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> Zone</span>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span s > @if($qry->network_zone=='Internal')
                                                           <span class="badge badge-secondary"  >{{$qry->network_zone}}</span>
                                            @elseif($qry->network_zone=='Secure')
                                                <span class="badge badge-info"  >{{$qry->network_zone}}</span>
                                                @elseif($qry->network_zone=='Greenzone')
                                                <span class="badge badge-success"  >{{$qry->network_zone}}</span>
                                                @elseif($qry->network_zone=='Guest')
                                                <span class="badge badge-warning"  >{{$qry->network_zone}}</span>
                                                @elseif($qry->network_zone=='Semi-Trusted')
                                                <span class="badge  " style="background:#FFFF11;color: black"  >{{$qry->network_zone}}</span>
                                                @elseif($qry->network_zone=='Public DMZ' || $qry->network_zone=='Public' || $qry->network_zone=='Servers Public DMZ' )
                                                <span class="badge badge-danger"  >{{$qry->network_zone}}</span>
                                                @else
                                                {{$qry->network_zone}}
                                                @endif
</span>
 
                                    </p>
                                  </div>
                                        
      
                                </div>
                                      <div class="fieldInfo mt-4  " style="float:left;width:30%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class="text-  "   style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size: 9.5pt;">Comments</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                            <p class="text-grey">
                                                {!!$qry->comments!!}  
                                           </p>
                                             </div> 
                                       </div>
                                                    @if($qry->asset_type=='physical')
                                                        <!-- END Invoice Info -->
        <div class="  mt-4 fieldInfo " style="float:left;width:30%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class=" -orange "  style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size:9.5pt;">Device</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                     
                                        <p class="text-grey">
                                            <span class="  " style="font-weight:bold; ;">Manufacturer</span>  &nbsp;&nbsp;&nbsp;  {{$qry->vendor_name}}
                                          </p>
                                           <p class="text-grey">    <span  style="font-weight:bold; ;"> Model </span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$qry->model}}
                                            </p>
                                            <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> Type </span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$qry->type}} 
                                            </p>
                                            <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> SN#</span>    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  >{{$qry->sn}}</span>
                                          </p>
                                      
                                          
                                  </div>
                                        
      
                                </div>
    @endif
                                                                                 <!-- END Invoice Info -->
        <div class="  mt-4 fieldInfo " style="float:left;width:30%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class=" -orange "  style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size:9.5pt;">SSL Certificate</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                          
                                           
                                           <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> Status</span>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  > {{$qry->ssl_certificate_status}}</span>
                                          </p>
                                          <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> CN</span>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  > {{@$ssl->cert_name}}</span>
                                          </p>
                                          <p class="text-grey">
                                                <span   style="font-weight:bold; ;"> Expiration Date</span>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  > {{@$ssl->cert_edate!=''?date('Y-M-d',strtotime($ssl->cert_edate)):''}}</span>
                                          </p>
                                          
                                  </div>
                                        
      
                                </div>
                                      
                                          <?php $ip=DB::Table('asset_ip_addresses')->where('asset_id',$_GET['id'])->get(); ?>  
                                                @if(sizeof($ip)>0)                                                                     <!-- END Invoice Info -->
        <div class="  mt-4 fieldInfo " style="float:left;width:30%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class=" -orange "  style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size:9.5pt;">Additional IPs</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                          
                                            @foreach($ip as $i)
                                           <p class="text-grey  pb-2 " style="width:50%;float: left;">
                                                <span   style="font-weight:bold;width: 32%;float: left;" > {{$i->ip_address_name}}</span><span  style="width: 62%;float: left;">{{$i->ip_address_value}}</span>
                                          </p>

                                         @endforeach
                                           
                                          
                                          </div>
                                        
      
                                      </div>
                                      @endif
                                      

                                <!-- END Table -->

                                <!-- Footer -->
                                @if($qry->managed==1)
                                   <div class="  mt-4 fieldInfo1  mb-4 " style="float:left;width:100%;border: 1px solid grey;border-radius: 30px;padding: 20px;margin-left: 10px;">
                                          <h2 class=" -orange "  style="font-weight:bold;color:#DF810F;font-family: Open Sans!important;font-weight: bold;font-size:9.5pt;">Managed</h2> 
                                          <div class="ml-2 mt-4 textDiv">
                                          
                                           
                                          <p class="text-grey  pb-2 " style="width:30%;float: left;">
                                                <span   style="font-weight:bold;width: 32%;float: left;" > Owner</span><span  style="width: 62%;float: left;">{{$qry->app_owner}}</span>
                                          </p>
                                          <p class="text-grey  pb-2 " style="width:30%;float: left;">
                                                <span   style="font-weight:bold;width: 32%;float: left;" > SLA</span><span  style="width: 62%;float: left;">{{$qry->sla}}</span>
                                          </p>

                                  </div>
                                        
      
                                 
                                   <div class="   mt-2"  style="    display: flex;
    justify-content: center;
    align-content: center;

    justify-content: space-evenly;
    width: 100%;"> 
                                          
                                     <figure  >
                                         <img width="48" height="48"     class=" " style="object-fit: cover" src="{{asset('public/img/DR.png')}}" alt=""><br>
                                         <div class="badge    mt-1  {{$qry->disaster_recovery==1?'badge-success':'badge-secondary text-lightgrey'}}" >DR</div>
                                     </figure>
   
                                       <figure  >        
                                    <img width="48" height="48"     class=" " style="object-fit: cover" src="{{asset('public/img/monitored.png')}}" alt=""><br>
                                     <div class="badge    mt-1   {{$qry->monitored==1?'badge-success':'badge-secondary  text-lightgrey'}}">  Monitored </div>
                                         </figure>
                                    <figure  >            
                                     <img width="48" height="48"    class=" "  style="object-fit: cover" src="{{asset('public/img/patching.png')}}" alt=""><br>
                                       <div class="badge    mt-1  {{$qry->patched==1?'badge-success':'badge-secondary  text-lightgrey'}}"> Patching</div>
                                         </figure>
                                    <figure  > 
                                    <img width="48" height="48"     class=" " style="object-fit: cover" src="{{asset('public/img/AV.png')}}" alt=""><br>
                                      <div class="badge    mt-1  {{$qry->antivirus==1?'badge-success':'badge-secondary  text-lightgrey'}}">AV</div>
                                         </figure>
                                      <figure  >    
                                         <img width="48" height="48"    class=" "  style="object-fit: cover" src="{{asset('public/img/Backup.png')}}" alt=""><br>
                                         <div class="badge    mt-1  {{$qry->backup==1?'badge-success':'badge-secondary  text-lightgrey'}}">Backup</div>
                                         </figure>
                                   <figure  >    
                                     <img width="48" height="48"    class=" "  style="object-fit: cover" src="{{asset('public/img/replicated.png')}}" alt=""><br>
                                   <div class="badge    mt-1 {{$qry->replicated==1?'badge-success':'badge-secondary  text-lightgrey'}}"> Replicated  </div>
                                         </figure>
                                         
                                      <figure  >       
                                     <img width="48" height="48"     class=" -3" style="object-fit: cover" src="{{asset('public/img/SMTP.png')}}" alt=""><br>
                                      <div class="badge    mt-1  {{$qry->smtp==1?'badge-success':'badge-secondary  text-lightgrey'}}">SMTP</div>
                                         </figure>
                                         
                                
                                     <figure  >           
                                     <img width="48" height="48"  class="pr- "  style="object-fit: cover" src="{{asset('public/img/syslog.png')}}" alt=""><br>
                                      <div class="badge    mt-1  {{$qry->syslog==1?'badge-success':'badge-secondary  text-lightgrey'}}">Syslog</div>
                                         </figure>
                                <!-- END Footer -->
                            </div>
                            @endif
                      </div>

            
    <div class="text-center footer" style="position: fixed;bottom: 0;width: 100%;">
    <address class="  text-center text-grey">
                                            CP 69044 CSP Sainte Dorothee &nbsp;&nbsp;
                                            Laval,Quebec &nbsp;&nbsp;
                                            H7X 3M2 Canada &nbsp;&nbsp; 
                                         
                                            
                                            www.amaltitek.com
                                        </address>
                                               <img src="{{asset('public/img/amaltitek-logo-fr.png')}}"  >         
                                             </div>
   </div>

           
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
            @endsection('content')