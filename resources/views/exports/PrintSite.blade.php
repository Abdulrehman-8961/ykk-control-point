      
@extends('layouts.header')
@extends('layouts.sidebar')
@extends('layouts.footer')
@section('content')  <!-- Main Container -->


<?php 
 $id=$_GET['id'];
$html='';
   $q=DB::table('sites as a')->select('a.*' ,'c.firstname','c.logo' ,'usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo' )->join('clients as c','c.id','=','a.client_id') ->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   
 
                           $html.='<div class="block card-round   bg-new-dark new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-network-segment-white.png" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Site</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:17px">'.date('Y-M-d').' by '.Auth::user()->firstname.' '.Auth::user()->lastname.'</p>
                                    </div>
                                </div>';
                      

 

                                    $html.='</div></div>
                            </div>
                      

                        <div class="block new-block position-relative  5" >
                                              
                            <div class="block-content pb-0   " style="padding-left: 50px;padding-right: 50px;">
                             
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
                                           <div class="bubble-new">Telephone</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->phone.'</b></div> 
                                     
                                            </div>

                                            <div class="col-sm-4">
                                                
                                           <div class="bubble-white-new bubble-text-sec"><b>'.$q->fax.'</b></div> 
                                     
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

 

                                                     
                 
                 </div>
             </div>


         </div>';
   
 

   $contract=DB::table('site_comments')->where('site_id',$q->id) ->get();  
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




 $contract=DB::table('site_attachments')->where('site_id',$q->id) ->get();  
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


   $contract=DB::table('site_audit_trail as c')->select('c.*','u.firstname','u.lastname')->leftjoin('users as u','u.id','=','c.user_id')->where('c.site_id',$q->id)->get(); 
  
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