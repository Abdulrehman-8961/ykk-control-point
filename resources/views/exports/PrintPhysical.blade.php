      
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')  <!-- Main Container -->


<?php 
 $id=$_GET['id'];
$html='';

   $q=DB::table('assets as a')->select('a.*','s.site_name','d.domain_name','c.firstname','o.operating_system_name','o.operating_system_image','m.vendor_name' ,'s.address','s.city','s.country','s.phone','s.zip_code','s.province','at.asset_icon','at.asset_type_description','at.asset_type_description as asset_type_name','n.vlan_id as vlanId','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo','m.vendor_image')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')  ->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   

                   if($q->AssetStatus==1){
                           $html.='<div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-asset-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">  Asset Active</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                                 
                        }else{                         
                            $html.='<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-asset-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
 $renewed_qry=DB::Table('users')->Where('id',$q->InactiveBy)->first(); 
                         

  
                                $html.='<p class="mb-0  header-new-subtext" style="line-height:17px">On '.date('Y-M-d',strtotime($q->InactiveDate)).' by '.@$renewed_qry->firstname.' '.@$renewed_qry->lastname.'</p>
                                    </div>
                                </div>';
                                   
                        }


                                    
                                    $html.='</div></div>
                            </div>
                 

                        <div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">'.$q->asset_type_description .' </div>
                            
                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">Client</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Client</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->firstname.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    <b>'.$q->site_name.'</b><br>
                                                    <span>'.$q->address.'</span><br>
                                                    <span>'.$q->city.','.$q->province.'</span><br>
                                                    <span>'.$q->zip_code.'</span><br>
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                           <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Location</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->location.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                         

                                                      <img src="public/client_logos/'.$q->logo.'" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>




                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-red text-capitalize">Host Information
</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Operating System</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->operating_system_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">FQDN</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.$q->fqdn.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Environment</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->use_.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Role</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->role.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        <div class="row form-group mt-5">
                                                       <div class="col-sm-3 t er">

<div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="disaster_recovery1" name="disaster_recovery" disabled="" value="1" '.($q->disaster_recovery==1?'checked':'').'>
  <label class="btn btn-new w-75 " for="disaster_recovery1">D/R Plan</label>
</div>
</div>
 

 


 <div class="col-sm-3  ">

<div class="contract_type_button  w-100 mr-4  ">
          <input type="checkbox" class="custom-control-input" id="clustered" name="clustered"  disabled="" value="1"  '.($q->clustered==1?'checked':'').'>
  <label class="btn btn-new w-75 " for="clustered"> Clustered</label>
</div>
</div>

 <div class="col-sm-3 text-center">

<div class="contract_type_button  w-100 mr-4  ">
     <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" value="1" disabled="" '.($q->internet_facing==1?'checked':'').'>

       <label class="btn btn-new w-75 " for="internet_facing"> Internet Facing</label>
</div>
</div>




 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.($q->load_balancing==1?'checked':'').'>
  <label class="btn btn-new w-75 " for="load_balancing"> Load Balanced</label>
</div>
</div>


</div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                           
                                                      <img src="public/operating_system_logos/'.$q->operating_system_image.'" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-blue text-capitalize">Hardware</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Manufacturer</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->vendor_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Model</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.$q->model.'
                                                
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Type</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->type.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>

                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Serial Number</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->sn.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">CPU</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->cpu_sockets.' '.$q->cpu_model.' '.$q->cpu_cores.' C @ '.$q->cpu_freq.' GHz
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Memory</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->memory.'  
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         
                                    </div>
                                    <div class="col-sm-2">
                                               ';
                                               if($q->vendor_image!=''){
                                               $html.='<div class="bubble-white-new bubble-text-sec" style="padding:10px">
 

                                                      <img src="public/vendor_logos/'.$q->vendor_image.'" style="width: 100%;">
                                                </div> ';
                                                }

                                    $html.='</div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>








                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-green text-capitalize">Networking</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
      <div class="col-sm-10">
        <div class="inner-body-content position-relative px-3">
                                   <div class="top-div text-capitalize w-25 font-size-sm" >Primary IP
</div>                               

                                        <div class="form-group    row">
                                                             <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Networking
</div> 
                                       </div>                           
                                            <div class="col-sm-3 form-group ">
                                                
                                          ';
                                                if($q->network_zone=='Internal'){
                                                           $html.='<div class=" text-center border-none text-white font-size-lg bg-secondary bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                            }elseif($q->network_zone=='Secure'){
                                                $html.='<div class="text-center border-none font-size-lg text-white bg-info bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                            }
                                                elseif($q->network_zone=='Greenzone'){
                                                $html.='<div class="text-center border-none font-size-lg text-white bg-success bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                                }elseif($q->network_zone=='Guest'){
                                                $html.='<div class="text-center font-size-lg border-none text-white bg-warning"  ><b>'.$q->network_zone.'</b></div>';
                                                }elseif($q->network_zone=='Semi-Trusted'){
                                                $html.='<div class=" text-center font-size-lg text-white border-none bubble-white-new bubble-text-sec  " style="background:#FFFF11;color: black"  ><b>'.$q->network_zone.'</b></div>';
                                                }elseif($q->network_zone=='Public DMZ' || $q->network_zone=='Public' || $q->network_zone=='Servers Public DMZ' ){
                                                $html.='<div class=" text-center font-size-lg text-white bubble-white-new border-none bubble-text-sec bg-danger"  ><b>'.$q->network_zone.'</b></div>
                                               ';
                                                }else{
                                               $html.=$q->network_zone;
                                                }
                                            $html.='</div> 
                                            </div>
                                            <div class="row">
                                          
    


                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">vLAN ID
</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-first">
                                                   '.$q->vlan_id.' 
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                      
                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Ip Address</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                       '.$q->ip_address.' 
                                                </div> 
                                     
                                            </div>
                                        

</div>
                                    </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-networking.png" style="width: 100%;">
                                                </div> 

                                    </div>';
                                           $asset_ip=DB::Table('asset_ip_addresses')->where('asset_id',$q->id)->orderby('ip_address_name','asc')->get();  
                                         if(sizeof($asset_ip)>0){
$html.='     <div class="col-sm-12 mt-4">
        <div class="inner-body-content position-relative px-3">
                <div class="top-div text-capitalize w-25 font-size-sm" >Additional IPs </div>                               

                                        <div class="row form-group">
                                       
                                                ';
                                                foreach($asset_ip as $i){
                                                $html.='<div class="col-sm-6">
                                                    <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 50%">
                                                        <p class="mb-0 mr-3 mx-auto  text-white text-center  px-2 " style="max-width: 150px;border-radius: 10px;border: 1px solid grey;padding:5px 5px;background: #595959;"><b>'.$i->ip_address_name.'</b></p> 
                                                    </td>
                                                    <td class="js-task-content  text-center">
                                                        <label class="mb-0">'.$i->ip_address_value.'</label>
                                                    </td>
                                                   
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>
                                </div>';
                                }
                                        $html.='</div>                         
                                  
                                    </div>
                                    </div>';
                                    }

                          $html.='</div>
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-yellow text-capitalize">Managed Services</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
      <div class="col-sm-10">
     
                                        <div class="form-group    row">

                                               <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">App Owner
</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-first">
                                                   '.$q->app_owner.' 
                                                  
                                                </div> 
                                     
                                            </div>
                                          


                                                             <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">SLA
</div> 
                                       </div>                           
                                            <div class="col-sm-3 form-group ">
                                                
                                           
                                                <div class=" text-center font-size-lg text-white bubble-white-new border-none bubble-text-sec bg-danger"  ><b>'.$q->sla.'</b></div>
                                              
                                            </div> 
                                            </div>
                                            <div class="row">

 <div class="col-sm-3 mb-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System is patched automatically or manually">
          <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1" disabled="" '.($q->patched==1?'checked':'').'>
  <label class="btn btn-new w-100 " for="patched"> Patched</label>
</div>
</div>

 <div class="col-sm-3   mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System is monitored">
       <input type="checkbox" class="custom-control-input" id="monitored" name="monitored" value="1" disabled="" '.($q->monitored==1?'checked':'').'>
       <label class="btn btn-new w-100 " for="monitored">Monitored</label>
</div>
</div>

 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System data is protected">
        <input type="checkbox" class="custom-control-input" id="backup" name="backup" value="1" disabled="" '.($q->backup==1?'checked':'').'>
       <label class="btn btn-new w-100 " for="backup">Backup</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System has Anti-Virus installed">
           <input type="checkbox" class="custom-control-input" id="antivirus" disabled="" name="antivirus" value="1"   '.($q->antivirus==1?'checked':'').'>
       <label class="btn btn-new w-100 " for="antivirus">Anti-Virus
</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System is replicated">
          <input type="checkbox" class="custom-control-input" id="replicated"  disabled="" name="replicated" value="1" '.($q->replicated==1?'checked':'').'>
       <label class="btn btn-new w-100 " for="replicated">Replicated
</label>
</div>
</div>

 <div class="col-sm-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System is scanned by Drawbridge">
          <input type="checkbox" class="custom-control-input" id="disaster_recovery"  disabled="" name="disaster_recovery" value="1" '.($q->disaster_recovery==1?'checked':'').'>
       <label class="btn btn-new w-100 " for="disaster_recovery">Vulnerability Scan</label>
</div>
</div>

 <div class="col-sm-3  ">


<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System sends info to SIEM/Syslog">
                     <input type="checkbox" class="custom-control-input" id="syslog" disabled="" name="syslog" value="1" '.($q->syslog==1?'checked':'').' >
       <label class="btn btn-new w-100 " for="syslog">SIEM</label>
</div>
</div>

 <div class="col-sm-3  ">

<div class="contract_type_button w-100 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="System requires SMTP Relay Access">
      <input type="checkbox" class="custom-control-input" id="smtp" name="smtp" value="1" disabled="" '.($q->smtp==1?'checked':'').'>
       <label class="btn btn-new w-100 " for="smtp">SMTP</label>
</div>
</div>

 
</div>
                                            
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-amaltitek.png" style="width: 100%;">
                                                </div> 

                                    </div>
                                   
     <div class="col-sm-12 mt-4">
       
                                    </div>
                                   

                          </div>
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>


         </div>';
   
 $line_items=DB::Table('contract_assets as ca')->select('a.contract_no','a.contract_status','a.contract_start_date','a.contract_end_date','c.logo','a.contract_description','a.contract_type','a.id')->where('ca.hostname',$q->id)->join('contracts  as a','a.id','=','ca.contract_id')->join('clients as c','c.id','=','a.client_id')->groupBy('a.contract_no')->where('a.is_deleted',0)->orderBy('a.contract_no','asc')->get();
                                                                         
                                                                                if(sizeof($line_items)>0){
                                                                                     
  $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Supported Contracts</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">
 ';

                            foreach($line_items as $l){

                                                                         $contract_end_date=date('Y-M-d',strtotime($l->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
$html.='<div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="110" style="cursor:pointer;">
                    
                        <div class="block-content d-flex py-3 mt-0 position-relative">
                                        <div class="mr-3   align-items-center  d-flex" style="width:10%">
                                            <img src="public/client_logos/'.$l->logo.'" class="rounded-circle" width="100%" style="max-width:70px;object-fit: cover;">
                                        </div>
                                        <div class="  " style="width:80%">
                                                    <p class="font-10pt mb-0 text-truncate c1">'.$l->contract_no.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c2">'.$l->contract_description.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c3"><b>'.$l->contract_type.'</b></p>
                                        </div>
                                        <div class=" text-right" style="width:35%;;">
                                   <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                                        
                                                                        <div class="d-inline-flex justify-content-end">
                                                                            <span class=" mr-2 ">'.date('Y-M-d',strtotime($l->contract_end_date)).'</span>';

                  if($l->contract_status=='Active'){

                                    if($abs_diff<=30){
                             $html.='<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-w600 text-dark"  >
                                                                                 <img src="public/img/status-upcoming-.png" class="mr-2" width="15px"><span class="ml-n1">Upcoming</span>
                                                                    </div> ';
                                    }else{
                                          $html.=' <div class=" bg-new-green ml-auto  badge-new  text-center   text-white"  >
                                                                                 <img src="public/img/status-white-active.png" class="mr-2" width="15px"><span class="ml-n1">Active</span>
                                                                    </div>  
                                                  ';               

                               }
                                }elseif($l->contract_status=='Inactive'){
                                   
                                                                      $html.='<div class=" bg-new-blue ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-renewed.png" class="mr-2" width="15px"><span class="ml-n1">Renewed</span>
                                
                                                                    </div>  ';

                                }elseif($l->contract_status=='Expired/Ended'){
                                 
                                            $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                
                                                                    </div>';
                                }elseif($l->contract_status=='Ended'){
                                       $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                                                    </div>';
                                                                }
                                elseif($l->contract_status=='Expired'){
                                      $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-expired.png" class="mr-2" width="15px"><span class="ml-n1">Expired</span>
                                                                    </div>';
                                }

                                $html.='                            </div>

                                                                    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>'.$abs_diff.' days remaining</i></small></p>

                                                                    </div>
                                                                   
                                                                </div>
                                                                 
 <div style="position: absolute;width: 100%; bottom: 5px;right: 10px;">
                                       <a href="'.url('contracts').'/'.$l->id.'" class="toggle led" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Open" data-html="true" data-original-title="Open">
          

          <img src="public/img/icon-eye-grey.png" class="mr-2 " width="23px" height="23px">
                                                                        </a>
                                                                           
                                                                    
                                                                    </div>
                                        </div>
                                </div>
                            </div>


';
}

$html.='</div>
</div>';
   }  
 
  $line_items=DB::Table('ssl_host as ca')->select('a.cert_name','a.cert_status','a.cert_edate','a.cert_type','a.id','c.logo','a.description','c.firstname')->where('ca.hostname',$q->id)->join('ssl_certificate  as a','a.id','=','ca.ssl_id')->join('clients as c','c.id','=','a.client_id')->groupBy('a.cert_name')->where('a.is_deleted',0)->orderBy('ca.id','asc')->get();
                                                                         
                                                                                if(sizeof($line_items)>0){
                                                                                     
$html.='  <div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Supported Contracts</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">
';
 
                                                                            foreach($line_items as $l){

                                                                         $contract_end_date=date('Y-M-d',strtotime($l->cert_edate)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->cert_edate);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
$html.='<div class="block block-rounded   table-block-new mb-2 pb-0  -  viewContent" data="110" style="cursor:pointer;">
                    
                        <div class="block-content d-flex py-3 mt-0 position-relative">
                                        <div class="mr-3   align-items-center  d-flex" style="width:10%">
                                            <img src="public/client_logos/'.$l->logo.'" class="rounded-circle" width="100%" style="max-width:70px;object-fit: cover;">
                                        </div>
                                        <div class="  " style="width:80%">
                                                    <p class="font-10pt mb-0 text-truncate c1">'.$l->cert_name.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c2">'.$l->description.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c3"><b>
                                                        ';
                                                         if($l->cert_type=='internal'){
                                                        $html.=$l->cert_type;  
                                                        }else{
                                                        $l->firstname;
                                                        }
                                                        $html.='SSl Certificate

                                                    </b></p>
                                        </div>
                                        <div class=" text-right" style="width:35%;;">
                                   <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                                        
                                                                        <div class="d-inline-flex justify-content-end">
                                                                            <span class=" mr-2 ">'.date('Y-M-d',strtotime($l->cert_edate)).'</span>';

                  if($l->cert_status=='Active'){

                                    if($abs_diff<=30){
                             $html.='<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-w600 text-dark"  >
                                                                                 <img src="public/img/status-upcoming-.png" class="mr-2" width="15px"><span class="ml-n1">Upcoming</span>
                                                                    </div> ';
                                    }else{
                                          $html.=' <div class=" bg-new-green ml-auto  badge-new  text-center   text-white"  >
                                                                                 <img src="public/img/status-white-active.png" class="mr-2" width="15px"><span class="ml-n1">Active</span>
                                                                    </div>  ';
                                                                 

                                }
                                }elseif($l->cert_status=='Inactive'){
                                   
                                                                      $html.='<div class=" bg-new-blue ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-renewed.png" class="mr-2" width="15px"><span class="ml-n1">Renewed</span>
 
                                                                    </div>  ';

                                }elseif($l->cert_status=='Expired/Ended'){
                                 
                                                                              $html.='   <div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                 
                                                                    </div>';
                                }elseif($l->cert_status=='Ended'){
                                       $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/action-white-end-revoke.png" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                                                    </div>';
                                }elseif($l->cert_status=='Expired'){
                                      $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-expired.png" class="mr-2" width="15px"><span class="ml-n1">Expired</span>
                                                                    </div>';
                                

                                }                            

                                $html.='</div>

                                                                    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>'.$abs_diff.' days remaining</i></small></p>

                                                                    </div>
                                                                   
                                                                </div>
                                                                 
 <div style="position: absolute;width: 100%; bottom: 5px;right: 10px;">
                                       <a href="'.url('ssl-certificate').'/'.$l->id.'" class="toggle led" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Open" data-html="true" data-original-title="Open">
          

          <img src="public/img/icon-eye-grey.png" class="mr-2 " width="23px" height="23px">
                                                                        </a>
                                                                           
                                                                    
                                                                    </div>
                                        </div>
                                </div>
                            </div>';
                        }
$html.='</div>
</div>';
}

   $contract=DB::table('asset_comments')->where('asset_id',$q->id) ->get();  
if(sizeof($contract)>0){
     $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Comments</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock"> ';
                                                                         foreach($contract as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  '.nl2br($c->comment).'
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
                                    }
                                $html.='</div>

                            </div>';

}




 $contract=DB::table('asset_attachments')->where('asset_id',$q->id) ->get();  
 if(sizeof($contract)>0){
    $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock"> ';
                                                            foreach($contract as $c){

                                                                                $f=explode('.',$c->attachment);
                                                                            $fileExtension = end($f);
                                         $icon='attachment.png';
                                          if($fileExtension=='pdf'){
                                                $icon='attch-Icon-pdf.png';
                                            }
                                            else if($fileExtension=='doc' || $fileExtension=='docx'){
                                                $icon='attch-word.png';
                                            }
                                            else if($fileExtension=='txt'){
                                                $icon='attch-word.png';

                                            }
                                            else if($fileExtension=='csv' || $fileExtension=='xlsx' || $fileExtension=='xlsm' || $fileExtension=='xlsb' || $fileExtension=='xltx'){
                                                    $icon='attch-excel.png';
                                            }
                                            else if($fileExtension=='png'  || $fileExtension=='gif' || $fileExtension=='webp' || $fileExtension=='svg' ){
                                                $icon='attch-png icon.png';
                                            }
                                              else if(  $fileExtension=='jpeg' || $fileExtension=='jpg'  ){
                                                $icon='attch-jpg-icon.png';
                                            }
                                               else if(  $fileExtension=='potx' || $fileExtension=='pptx' || $fileExtension=='ppsx' || $fileExtension=='thmx'  ){
                                                $icon='attch-powerpoint.png';
                                            }
  
 

                                                        $html.='<div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png" width="24px">
                                                        </a>  -->
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="pt-2">
                                                        <p class=" pb-0 mb-0">
 <a href="temp_uploads/'.$c->attachment.'" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/'.$icon.'"  width="25px"> &nbsp;<span class="text-truncate  " >'.substr($c->attachment,0,25).'</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
                                }
                                $html.='</div>

                            </div>';
                        }


   $contract=DB::table('asset_audit_trail as c')->select('c.*','u.firstname','u.lastname')->leftjoin('users as u','u.id','=','c.user_id')->where('c.asset_id',$q->id)->get(); 
  
if(sizeof($contract)>0){
     $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Audit Trial</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">'; 
                                                                        foreach($contract as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->firstname.' '.$c->lastname.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->created_at)).' at '.date('h:i:s A',strtotime($c->created_at)).' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  '.$c->description.'
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
}
                                $html.='</div>

                            </div>';
                        }




$html.='
    </div>


                    </div>



                </div>
               </div>
       </div>';




?>










 <body onload="window.print()">
            <main id="main-container" >
          

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
                {!!$html!!}
           </div>
                        </div>
                    </div>
                    <!-- END Invoice -->
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
            @endsection('content')