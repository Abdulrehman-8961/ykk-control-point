    
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')  <!-- Main Container -->



<?php 
                
  $id=$_GET['id'];
$html='';

 $q=DB::table('ssl_certificate as s')->select('s.*','a.hostname','c.firstname','site.site_name','site.address','site.city','site.province',
'site.zip_code','v.vendor_name','s.cert_msrp','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo','v.vendor_image')->leftjoin('assets as a','a.id','=','s.cert_hostname')->leftjoin('vendors as v','v.id','=','s.cert_issuer')->leftjoin('clients as c','c.id','=','s.client_id')->leftjoin('sites as site','site.id','=','s.site_id')->leftjoin('users as usr','usr.id','=','s.created_by')->leftjoin('users as upd','upd.id','=','s.updated_by')->where('s.is_deleted',0)->where('s.id',$id)->first(); 

$cert_edate=date('Y-M-d',strtotime($q->cert_edate)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($cert_edate);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
             $ended_qry=DB::Table('users')->Where('id',$q->ended_by)->first();
                 $renewed_qry=DB::Table('users')->Where('id',$q->renewed_by)->first(); 

                   
 


                
                    if($q->cert_status=='Active'){

                                    if($abs_diff<=30){
                            $html.='<div class="block card-round   bg-new-yellow new-nav  " style="position:relative!important" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                      <div class="d-flex">
                          <img src="'.('public/img/icon-upcoming-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text text-dark" style="line-height:25px">Upcoming</h4>
                                <p class="mb-0  header-new-subtext text-dark" style="line-height:20px">In '.$abs_diff.' days</p>
                                    </div>
                                </div>';

                                    }else{
                                         $html.='<div class="block card-round   bg-new-green new-nav"  style="position:relative!important">
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-active-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Active</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">Until '.$cert_edate.' ('.$abs_diff.' days remaining)</p>
                                    </div>
                                </div>';
                            }

                                }
                                elseif($q->cert_status=='Inactive'){
                                      $html.='<div class="block card-round   bg-new-blue new-nav"  style="position:relative!important">
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-renewed-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Renewed
</h4>
                                       <p class="mb-0  header-new-subtext" style="line-height:15px">On '.date('Y-M-d H:i:s A',strtotime($q->renewed_on)).' by   '.@$renewed_qry->firstname.' '.@$renewed_qry->lastname.'</p>
                                    </div>
                                </div>';

                                }elseif($q->cert_status=='Expired/Ended'){
                                        $html.='<div class="block card-round   bg-new-red new-nav"  style="position:relative!important" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-ended-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Ended
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On '.date('Y-M-d',strtotime($q->ended_on)).' at  '.date('H:i:s A',strtotime($q->ended_on)).' By '.@$ended_qry->firstname.' '.@$ended_qry->lastname.': '.substr($q->ended_reason,0,50).'...</p>
                                    </div>
                                </div>';
                            }
                                elseif($q->cert_status=='Ended'){
                                    $html.='<div class="block card-round   bg-new-red new-nav"  style="position:relative!important">
                                
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-expired-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Ended
</h4>   
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On '.date('Y-M-d',strtotime($q->ended_on)).' at  '.date('H:i:s A',strtotime($q->ended_on)).' By '.@$ended_qry->firstname.' '.@$ended_qry->lastname.': '.substr($q->ended_reason,0,50).'...</p>
                                    </div>
                                </div>';
                            }
                                elseif($q->cert_status=='Expired'){
                                    $html.='<div class="block card-round   bg-new-red new-nav"  style="position:relative!important" >

                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-expired-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Expired
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On '.$cert_edate.'</p>
                                    </div>
                                </div>';
                                }
 
                                                                


                                    


                          
                                         
                        $html.=' 

                                </div>
                            </div>
                        </div>

                        <div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">'.$q->cert_type.' SSL Certificate</div>
                            
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
 <div class="top-right-div top-right-div-red text-capitalize">Issuer</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Vendor</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>';
                                        if($q->cert_type=='internal'){
                                          $html.=$q->firstname;

                                        }else{
                                             $html.=$q->vendor_name;
                                            }

                                         $html.='</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Description</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.$q->description.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Expiration Date</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           '.date('Y-M-d',strtotime($q->cert_edate)).'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Renewal Date</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                 '.date('Y-M-d',strtotime($q->cert_rdate)).'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                            <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">MSRP</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                 $'.number_format($q->cert_msrp).'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">';
                                               if($q->cert_type=='internal'){
                                                        
                                                   $html.='<img src="public/client_logos/'.$q->logo.'" style="width: 100%;">';
                                                  }
                                                  else{
                                                   $html.='<img src="public/vendor_logos/'.$q->vendor_image.'" style="width: 100%;">'; 
                                                  }
                                                  $html.='
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-blue text-capitalize">Subject</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Name</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->cert_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Email (E)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.$q->cert_email.'
                                                
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Company (O)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->cert_company.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Department (OU)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->cert_department.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">City (L)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->city.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">State (S)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->cert_state.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                            <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Country (CA)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->cert_country.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         
                                    </div>
                                    <div class="col-sm-2">
                                         <img src="public/img/static-ssl-cert.png" style="width: 100%;">     

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>







';
   $ssl=DB::table('ssl_san')->where('ssl_id',$q->id)->get();
          
           if(sizeof($ssl)>0){
                            $html.='<div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-green text-capitalize">Subject Alt Names
</div>
                            
                            <div class="col-sm-12 m-
                            " >';
                       
            
     

 
foreach($ssl as $s){
     $html.='
     <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>';
                                                        if($s->san_type=='DNS'){
                                                         $html.='<img src="public/img/san.png" width="32px">';
                                                        }elseif($s->san_type=='IP'){
 $html.='<img src="public/img/ip.png" width="32px">';
                                                            }else{
                                                             $html.='@';
                                                        }

                                                      $html.='</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <label class="mb-0">'.$s->san.'</label>
                                                    </td>
                                                   
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>   ';

}                                            


                                   

                                                     
                 
                  $html.='</div>
             </div>
         </div>


     </div>';

 }
 
 
   $line_items=DB::table('ssl_host as s')->select('*','s.id as sid','s.hostname as host','a.AssetStatus','a.id as aid','a.hostname as asset_name','a.ip_address')->leftjoin('assets as a','a.id','=','s.hostname')->leftjoin('asset_type as at','a.asset_type_id','=','at.asset_type_id')->where('s.ssl_id',$q->id)->get();
  
 if(sizeof($line_items)>0){
 $html.='  <div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Assigned Hosts</div>
                            
                                                          <div class="b t px-4  mt-4 row " id="commentBlock"> ';
                                                        
                                                                        foreach($line_items as $c){

                                                              $html.='<div class="j col-lg-6 mb-3  " data-task-id="9" data-task-completed="false" data-task-starred="false">
                                                                <div  class="inner-body-content">
                                                                    <div class="form-group px-3 row">
                                                        <div class="col-sm-12 pb-3 ">';
    if($c->AssetStatus!=1){
                                                                                      $html.='<div  title="" class="w-100   AssetInactive"  style="height: 12mm;padding-top: 8px;    box-shadow: 0px 2px 7px black;padding-top: 8px;border: none;">'.$c->hostname.'</div> ';
                                                                                }else{
                                                                             $html.='<div data-toggle="tooltip" data-trigger="hover" data-html="true"   title="" class="w-100     AssetActive" style="height: 12mm;padding-top: 8px;    box-shadow: 
       0px 1px 5px black;padding-top: 8px;border: none;">'.$c->hostname.'  </div>  ';
                                                                                }
                                                         $html.='</div>';

                                        
                                       $ip_array=explode(',',$c->ip_id);
            $ip=DB::Table('asset_ip_addresses')->whereIn('id',$ip_array)->orderby('ip_address_name','asc')->get();
   
                                                if(@$ip_array[0]==''){
                                          $html.='<div class="col-sm-5 mb-3">
                                           <div class="bubble-new">Primary</div> 
                                       </div>
                                            <div class="col-sm-7 mb-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                        '.@$c->ip_address.'  
                                                </div> 
                                     
                                            </div>';
                                                }
                                                foreach($ip as $i){
                                                   $html.='<div class="col-sm-5 mb-3">

                                           <div class="bubble-new">  '.$i->ip_address_name.'  </div> 
                                       </div>
                                            <div class="col-sm-7 mb-3">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                        '.$i->ip_address_value.'  
                                                </div> 
                                     
                                            </div>';
                                            }
                                         $html.='</div>
                                       
</div>
                                    </div>';
                                    }
                                 $html.='</div>

                            </div>';

 $contract=DB::table('ssl_comments')->where('ssl_id',$q->id) ->get(); 
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




  $contract=DB::table('ssl_attachments')->where('ssl_id',$q->id) ->get(); 
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

  $contract=DB::table('ssl_audit_trail as c')->select('c.*','u.firstname','u.lastname')->leftjoin('users as u','u.id','=','c.user_id')->where('c.ssl_id',$q->id)->get(); 
 
  
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
}
 
 ?>    

   
 <body onload="window.print()">
            <main id="main-container" >
                <!-- Hero -->
             
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
                      {!!$html!!}
                    </div>
                    <!-- END Invoice -->
           
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
            @endsection('content')