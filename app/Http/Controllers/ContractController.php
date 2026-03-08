<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
 
  use Excel;
use Response;
use App\Exports\ExportContract;
use App\Exports\ExportExpiringContract;
 use DateTime;

 use Validator;
class ContractController extends Controller
{
    //
public function __construct(){
     
      
     }
   
   
 




     public function Contract(Request $request){
        if(!Auth::check() ){
   if(!isset($request->key) || $request->key==''){
                         
                        return view('error')->with(['message'=>"Unauthorized Access"]);

             }                                      

        $qry=DB::table('contract_sharable_links')->where('hash',$request->key)->first();

            if($qry==''){

                    return view('error')->with(['message'=>"Invalid Link / Link Expired"]);
            }
            else{
                $expiry_date=$qry->expiry_date;
                if(date('Y-m-d')>$expiry_date){
                      return view('error')->with(['message'=>"Link Expired"]);
                }
                $contract=DB::table('contracts')->where('is_deleted',0)->where('id',$qry->contract_id)->first();
                if($contract==''){

                    return view('error')->with(['message'=>"Contract Not Found"]);
                }
            }
                return view('Contract',['type'=>$request->type,'hash'=>$request->key,'id'=>$qry->contract_id]);
    }else{
     if(isset($request->key) && $request->key!=''){
     $qry=DB::table('contract_sharable_links')->where('hash',$request->key)->first();
            if($qry==''){

                    return view('error')->with(['message'=>"Invalid Link / Link Expired"]);
            }
            else{
                $expiry_date=$qry->expiry_date;
                if(date('Y-m-d')>$expiry_date){
                      return view('error')->with(['message'=>"Link Expired"]);
                }
                $contract=DB::table('contracts')->where('is_deleted',0)->where('id',$qry->contract_id)->first();
                if($contract==''){

                    return view('error')->with(['message'=>"Contract Not Found"]);
                }
            }
                return view('Contract',['type'=>$request->type,'hash'=>$request->key,'id'=>$qry->contract_id]);
   
        }
        else{
                return view('Contract',['type'=>$request->type]); 
        }
    }
 
    }
    

    public function AddContract($type){
        
     return view('AddContract',['type'=>$type]);
 
    }

     public function uploadContractAttachment(Request $request){

             $attachment = $_FILES['attachment']['name'];
  $file_tmp = $_FILES['attachment']['tmp_name'];

       $fileExt = explode('.', $attachment);
    $fileActualExt = strtolower(end($fileExt));
        $key=$fileExt[0].uniqid().'.'.$fileActualExt; 

      $request->file('attachment')->move(public_path('temp_uploads'), $key);  

return response()->json($key);
        }




     public function LoadContractAttachment(Request $request){
        
        $request->header('Access-Control-Allow-Origin: *');

  // Allow the following methods to access this file
   $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');

  // Allow the following headers in preflight
   $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');

  // Allow the following headers in response
   $request->header('Access-Control-Expose-Headers: upload-offset');

  // Load our configuration for this server
 
 
 
   
    $uniqueFileID =$_GET["key"];
 
          $imagePointer = public_path("contract_attachment/" .  $uniqueFileID);
        if(!file_exists('..temp_uploads/'.$uniqueFileID)){
             
                copy( public_path("contract_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID ));
         }

  

 
      $imageName = $uniqueFileID;

  



 
      // if imageName was found in the DB, get file with imageName and return file object or blob
      $imagePointer = public_path("contract_attachment/" . $uniqueFileID);
 
      
      $fileObject = null;
       
      if ($imageName!='' && file_exists($imagePointer)) {
     
        $fileObject = file_get_contents($imagePointer);
     
      }

 

    // trigger load local image
    $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];
 
    if ($fileBlob) {
      $imagePointer = public_path("contract_attachment/" .  $imageName);
      $fileContextType = mime_content_type($imagePointer);
      $fileSize = filesize($imagePointer);

       $handle = fopen($imagePointer, 'r');
   if (!$handle) return false;
 $content = fread($handle, filesize($imagePointer));

    
           $response = Response::make($content);
       $response->header('Access-Control-Expose-Headers','Content-Disposition, Content-Length, X-Content-Transfer-Id');
       $response->header('Content-Type',$fileContextType);
       $response->header('Content-Length', $fileSize);
      $response->header('Content-Disposition', "inline; filename=$imageName");

 
         return $response;
       
    } else {
     http_response_code(500);
    }
 
  
}


             public function revertContractAttachment(Request $request){
                $key=str_replace('"',"", $request->key);

               unlink(public_path('temp_uploads/'.$key));
    
            echo json_encode(1);

             }
           

    
  public function GetAssetsByType(Request $request){
         
            $userAccess=explode(',',@Auth::user()->access_to_client);
            $client_id=$request->client_id;
               

                    if($request->id!=''){
                        $id=$request->id;
                                // if($type=='physical'){
//  and not exists  (select id from contracts as c join contract_assets as ca on c.id=ca.contract_id where c.contract_status!='Inactive' and c.id!='$id' and (ca.status is null || ca.status!='Inactive')  and c.is_deleted=0 and ca.is_deleted=0 and ca.hostname=a.id  )
                                  $qry =DB::select("select * from assets as a where a.is_deleted=0 and HasWarranty=1 and a.client_id='$client_id' and  AssetStatus=1    order by a.sn asc ");      
                              
                                    // (select id from contracts as c join contract_assets as ca on c.id=ca.contract_id where c.contract_status!='Inactive' and c.id!='$id' and (ca.status is null || ca.status!='Inactive')  and c.is_deleted=0 and ca.is_deleted=0 and ca.hostname=a.id  ) 
                                  
                    }
                    else{
                           
                            //and not exists  (select id from contracts as c join contract_assets as ca on c.id=ca.contract_id where c.contract_status!='Inactive' and (ca.status is null || ca.status!='Inactive')  and c.is_deleted=0 and ca.is_deleted=0 and ca.hostname=a.id   )   
                $qry =DB::select("select * from assets as a where a.is_deleted=0 and HasWarranty=1 and a.client_id='$client_id' and  AssetStatus=1   order by a.sn asc");
         
                //  and not exists  (select id from contracts as c join contract_assets as ca on c.id=ca.contract_id where c.contract_status!='Inactive' and (ca.status is null || ca.status!='Inactive')  and c.is_deleted=0 and ca.is_deleted=0 and ca.hostname=a.id   )
                   
                    }
 

            return response()->json($qry);

  }
    public function EditContract(){
 

     return view('EditContract');
 
    }
    public function SharinglinkContract(Request $request){
          if(!isset($request->key) || $request->key==''){
                         
                        return view('error')->with(['message'=>"Unauthorized Access"]);

             }                                      
        $qry=DB::table('contract_sharable_links')->where('hash',$request->key)->first();
            if($qry==''){

                    return view('error')->with(['message'=>"Invalid Link / Link Expired"]);
            }
            else{
                $expiry_date=$qry->expiry_date;
                if(date('Y-m-d')>$expiry_date){
                      return view('error')->with(['message'=>"Link Expired"]);
                }
                $contract=DB::table('contracts')->where('is_deleted',0)->where('id',$qry->contract_id)->first();
                if($contract==''){

                    return view('error')->with(['message'=>"Contract Not Found"]);
                }
            }
             

     return view('exports/ExportPrintContract',['id'=>$qry->contract_id]);
 
    }



     public function ExportPrintContract(){
 

     return view('exports/ExportPrintContract');
 
    }

      public function Expiring30Days(){
 

     return view('Expiring30Days');
 
    }

   


    public function RenewContract(){
 

     return view('RenewContract');
 
    }
    

    public function ExportPdfContract(){
 

    $pdf = PDF::loadView('exports/ExportPdfContract');
   
    return $pdf->stream('Contract.pdf');

 
    }
        

    public function ShowContractDetails(Request $request){

                        $details=DB::Table('contract_details')->where('contract_id',$request->id)->where('is_deleted',0)->get();
                         $count=0;

 
                $html='';
                                     if(count($details)>0){
        
                             $html.='<table class="table table-bordered">
                                        <thead class="thead thead-dark">
                                            <tr>
                                                <th class="text-center" style="width: 60px;">#</th>
                                                <th>PN #</th>
                                                <th class="text-center" >Qty</th>
                                                <th class="text-right" >MSRP</th>
                                                <th class="text-right"   >Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                                 foreach($details as $key=>$d){
            $count++; 
                $asset=DB::Table('contract_assets as ca')->select('*','a.hostname as asset_name')->leftjoin('assets as  a','a.id','=','ca.hostname')->where('ca.contract_detail_id',$d->contract_detail_id)->where('ca.is_deleted',0)->get();
            
                                            $html.='<tr><td class="text-center">'.($key+1).'</td>
                                         
                                                     <td class="px-0 pb-0">
                                                        <p class="mb-3 px-2">'.$d->pn_no.'</p>';
                foreach($asset as $a){
                 
                  $html.='<div  class="font-w600 t  border-top " style="font-weight: bold; ">
                    <p class="px-2 mb-0" style="text-transform:uppercase;">'.$a->asset_name.' '.($a->sn!=''?'['.$a->sn.']':'').'</p></div>';
                 
                 }


              $html.='</td><td class="text-center"><p class="mb-0  ">'.$d->qty.'</p></td>
                                                <td   class="text-right"><p class="mb-0 ">$'. number_format($d->msrp,2).'</p> </td>
                                                <td class="text- ">'.$d->detail_comments.'</td>

                                              
                                            </tr>';
                                        }
                                   $html.='</tbody></table>';
                
                }
                echo $html;
    }
    
     public function ShowContracts(Request $request){
                    $qry=DB::table('contracts as a')->select('a.*','c.firstname','c.client_address','s.site_name','d.distributor_name','v.vendor_name','d.distributor_image','c.logo','v.vendor_image','s.address','s.city','s.country','s.phone','s.zip_code','s.province','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','a.ended_reason','a.ended_on','ued.firstname as ended_firstname','ued.lastname as ended_lastname','ued.email as ended_email') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->leftjoin('users as ued','ued.id','=','a.ended_by')->where('a.id',$request->id)->first();
                    return response()->json($qry);
    }

     public function getEmailContracts(Request $request){
         $qry=DB::table('contract_emails')->where('contract_id',$request->id)->get();
          return response()->json($qry);
     }


     public function getAttachmentContracts(Request $request){
         $qry=DB::table('contract_attachments')->where('contract_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsContracts(Request $request){
         $qry=DB::table('contract_comments')->where('contract_id',$request->id)->get();
          return response()->json($qry);
     }

        public function getContractDetails(Request $request){
            $id=$request->id;
         $qry=DB::select("SELECT *,(select GROUP_CONCAT(hostname) from contract_assets  where contract_detail_id=contract_details.contract_detail_id and is_deleted=0) as asset,(select GROUP_CONCAT(a.hostname) from contract_assets as ca left join assets as a on a.id=ca.hostname     where contract_detail_id=contract_details.contract_detail_id and ca.is_deleted=0) as hostname FROM contract_details where contract_id='$id' and is_deleted=0");
          return response()->json($qry);
     }



 public function getVendorOfContract(Request $request){
                $client_id=$request->client_id;
                $site_id=$request->site_id;
                              
                    if($site_id!='' && @$site_id[0]!=''){
 
            $qry=DB::Table('vendors as v')->select('v.*')->join('contracts as c','c.vendor_id','=','v.id')->where('c.client_id',$client_id)->whereIn('c.site_id',$site_id)->groupBy('c.vendor_id')->orderby('v.vendor_name','asc')->get();
                    }
                    else{
$qry=DB::Table('vendors as v')->select('v.*')->join('contracts as c','c.vendor_id','=','v.id')->where('client_id',$client_id)->groupBy('c.vendor_id')->orderby('v.vendor_name','asc')->get();
                    }
                    return response()->json($qry);
 }

 public function getVendorOfSSL(Request $request){
                $client_id=$request->client_id;
                $site_id=$request->site_id;
                              
                    if($site_id!='' && @$site_id[0]!=''){
 
            $qry=DB::Table('vendors as v')->select('v.*')->join('ssl_certificate as c','c.cert_issuer','=','v.id')->where('c.client_id',$client_id)->whereIn('c.site_id',$site_id)->groupBy('c.cert_issuer')->orderby('v.vendor_name','asc')->get();
                    }
                    else{
$qry=DB::Table('vendors as v')->select('v.*')->join('ssl_certificate as c','c.cert_issuer','=','v.id')->where('client_id',$client_id)->groupBy('c.cert_issuer')->orderby('v.vendor_name','asc')->get();
                    }
                    return response()->json($qry);
 }



 public function getDistributorOfContract(Request $request){
                $client_id=$request->client_id;
                $site_id=$request->site_id;
                $vendor_id=$request->vendor_id;
                     if($vendor_id!=''  && @$vendor_id[0]!=''){
                         
            $qry=DB::Table('distributors as v')->select('v.*')->join('contracts as c','c.vendor_id','=','v.id')->where('c.client_id',$client_id)->whereIn('c.site_id',$site_id)->whereIn('c.vendor_id',$vendor_id)->groupBy('c.distributor_id')->orderby('v.distributor_name','asc')->get();
                    }
                    else if($site_id!='' && @$site_id[0]!=''){
                         
            $qry=DB::Table('distributors as v')->select('v.*')->join('contracts as c','c.distributor_id','=','v.id')->where('c.client_id',$client_id)->whereIn('c.site_id',$site_id)->groupBy('c.distributor_id')->orderby('v.distributor_name','asc')->get();
                    }

                    else{
$qry=DB::Table('distributors as v')->select('v.*')->join('contracts as c','c.distributor_id','=','v.id')->where('client_id',$client_id)->groupBy('c.distributor_id')->orderby('v.distributor_name','asc')->get();
                    }
                    return response()->json($qry);
 }
 
     public function GenerateContractSharableLink(Request $request){

                    $hash=uniqid().Hash::make(time().rand(1,100000000000000));
                    DB::table('contract_sharable_links')->insert([
                            'contract_id'=>$request->id,
                            'hash'=>$hash,
                            'expiry_date'=>$request->expiry_date
                ]);

                            return response()->json($hash);

     }

     public function RemoveActiveContractLinks(Request $request){
            DB::table('contract_sharable_links')->where('contract_id',$request->id)->delete();
                    return redirect()->back()->with('success','Active Links Removed Successfully');
                
         }
     
    public function DeleteContract(Request $request){

                             
                    $id=$request->id;
                  $qry=DB::Table('contracts')->where('id',$id)->first();
                                                        
                 $userAccess=explode(',',@Auth::user()->access_to_client);
                if(@Auth::user()->role!='admin'  ){
                  
                if(!in_array($qry->client_id,$userAccess)){
                    echo "You dont have access";
                    exit;
                }
                }
                if(@Auth::user()->role=='read'){
                  echo "You dont have access";
                    exit;
                }


                                        $asset=DB::table('contract_assets')->where('is_deleted',0)->where('contract_id',$id)->get();
                                foreach($asset as $a){
                            
                                        DB::table('assets')->where('id',$a->hostname)->update(['warranty_status'=>'Inactive', 'warranty_end_date'=>'No contract Found','SupportStatus'=>'Unassigned']);

                                }
                                        DB::table('contract_details')->where('contract_id',$id)->where('is_deleted',0)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
                                         DB::table('contract_assets')->where('contract_id',$id)->where('is_deleted',0)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
DB::table('contracts')->wherE('id',$id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);



          return redirect()->back()->with('success','Contract Deleted Successfully');
           

    }


 
    
    public function ShowContract(Request $request){

                        
 


$qry=DB::table('Contract as a')->select('a.*','s.site_name','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')  ->where('a.id',$request->id)->first();
          return response()->json($qry);
           

    }
    
       


        public function ExportExcelContract(Request $request) 
        {
          
              return Excel::download(new ExportContract($request), 'Contract.xlsx');
              
        } 
         public function ExportExpiringExcelContract(Request $request) 
        {
          
            return Excel::download(new ExportExpiringContract($request), 'ExpiringContract.xlsx');
        } 







     public function InsertContract(Request $request){
   


                    
                   
                

                                // $position=DB::Table('contracts')->where('asset_type',$request->asset_type)->max('position');
   
                       


                      $data=array(
                                'contract_status'=>'Active',
                             
                           'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id,
                                     'contract_notification'=>$request->contract_notification,
                                'estimate_no'=>$request->estimate_no,
                                'sales_order_no'=>$request->sales_order_no,
                                'invoice_no'=>$request->invoice_no,
                                'contract_description'=>$request->contract_description,
                                'invoice_date'=>date('Y-m-d',strtotime($request->invoice_date)),
                                'registered_email'=>$request->registered_email,
                                'po_no'=>$request->po_no,
                                'po_date'=>date('Y-m-d',strtotime($request->po_date)),
                            
                                'distributor_id'=>$request->distributor_id,
                                'reference_no'=>$request->reference_no,
                                'distrubutor_sales_order_no'=>$request->distrubutor_sales_order_no,
                                'vendor_id'=>$request->vendor_id,
                                'contract_type'=>$request->contract_type,

                                'contract_no'=>$request->contract_no,
                                'contract_start_date'=>date('Y-m-d',strtotime($request->contract_start_date)),
                                'contract_end_date'=>date('Y-m-d',strtotime($request->contract_end_date)) ,
                                'created_by'=>Auth::id(),
                           
                                
                        );
 
                     DB::Table('contracts')->insert($data);
                    $id=DB::getPdo()->lastInsertId();

                     $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('contract_attachment/'.$a->attachment) );
                                             DB::table('contract_attachments')->insert([
                                                 'contract_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                         $emailArray= $request->emailArray;
                              if(isset($request->emailArray)){
                        foreach($emailArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_emails')->insert([
                                                 'contract_id'=>$id,
                                                  
                                                 'renewal_email'=>$a->email,
                                              
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

                                $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_comments')->insert([
                                                 'contract_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

            $contractDetailsArray= $request->contractDetailsArray;
                         if(isset($request->contractDetailsArray)){
                        foreach($contractDetailsArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_details')->insert([
                                                 'contract_id'=>$id,
                                                    'pn_no'=>$a->pn_no,
                                                    'qty'=>$a->qty,
                                                    'msrp'=>$a->msrp,
                                                    'detail_comments'=>$a->asset_description,
                                                 'added_by'=>Auth::id(),
                                             ]);
                                             $detail_id=DB::getPdo()->lastInsertId();
                                                $asset_array=$a->hostname_modal;
                                                foreach($asset_array as $b){
   $assetQry=DB::table('assets')->where('id',$b)->first();
                                                iF($assetQry!=''){
                                            DB::table('contract_assets')->insert([
                                                    'contract_id'=>$id,
                                                    'contract_detail_id'=>$detail_id,
                                                    'asset_type'=>@$assetQry->asset_type,
                                                    'hostname'=>$b,

                                            ]);

                                            DB::table('assets')->where('id',$b)->update(['warranty_status'=>'Active','warranty_end_date'=>$request->contract_end_date,'SupportStatus'=>'Supported']);
                                                        }
                                                    }
                        }   
                    }
                    

                  
DB::table('contract_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Contract added','contract_id'=>$id]);
 return response()->json('success');
    
 
    }







     public function RenewContractUpdate(Request $request){
       

     $data=array(
                                'contract_status'=>'Active',
                             
                           'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id,
                                     'contract_notification'=>$request->contract_notification,
                                'estimate_no'=>$request->estimate_no,
                                'sales_order_no'=>$request->sales_order_no,
                                'invoice_no'=>$request->invoice_no,
                                'contract_description'=>$request->contract_description,
                                'invoice_date'=>date('Y-m-d',strtotime($request->invoice_date)),
                                'registered_email'=>$request->registered_email,
                                'po_no'=>$request->po_no,
                                'po_date'=>date('Y-m-d',strtotime($request->po_date)),

                                'distributor_id'=>$request->distributor_id,
                                'reference_no'=>$request->reference_no,
                                'distrubutor_sales_order_no'=>$request->distrubutor_sales_order_no,
                                'vendor_id'=>$request->vendor_id,
                                'contract_type'=>$request->contract_type,

                                'contract_no'=>$request->contract_no,
                                'contract_start_date'=>date('Y-m-d',strtotime($request->contract_start_date)),
                                'contract_end_date'=>date('Y-m-d',strtotime($request->contract_end_date)) ,
                           'updated_by'=>Auth::id(),
                                 'updated_at'=>date('Y-m-d H:i:s'),
                           
                                
                        );
 

                 
                     DB::Table('contracts')->insert($data);
                      $id=DB::getPdo()->lastInsertId();

    $contract_id=$request->id;

                               $qry=DB::Table('contracts')->where('id',$contract_id)->first();
                                        

                                        $asset=DB::table('contract_assets')->where('is_deleted',0)->where('contract_id',$contract_id)->get();
                                foreach($asset as $a){
                            
                                        DB::table('assets')->where('id',$a->hostname)->update(['warranty_status'=>'Inactive', 'warranty_end_date'=>'No contract Found','SupportStatus'=>'Unassigned']);

                                }
                               
 DB::table('contract_assets')->where('contract_id',$contract_id)->where('is_deleted',0)->update(['status'=>'Inactive']);

DB::table('contracts')->where('id',$contract_id)->update(['contract_status'=>'Inactive','renewed_on'=>date('Y-m-d H:i:s'),'renewed_by'=>Auth::id()]);
 



                   $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('contract_attachment/'.$a->attachment) );
                                             DB::table('contract_attachments')->insert([
                                                 'contract_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                         $emailArray= $request->emailArray;
                              if(isset($request->emailArray)){
                        foreach($emailArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_emails')->insert([
                                                 'contract_id'=>$id,
                                                  
                                                 'renewal_email'=>$a->email,
                                              
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

                                $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_comments')->insert([
                                                 'contract_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

            $contractDetailsArray= $request->contractDetailsArray;
                         if(isset($request->contractDetailsArray)){
                        foreach($contractDetailsArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_details')->insert([
                                                 'contract_id'=>$id,
                                                    'pn_no'=>$a->pn_no,
                                                    'qty'=>$a->qty,
                                                    'msrp'=>$a->msrp,
                                                    'detail_comments'=>$a->asset_description,
                                                 'added_by'=>Auth::id(),
                                             ]);
                                             $detail_id=DB::getPdo()->lastInsertId();
                                                $asset_array=$a->hostname_modal;
                                                foreach($asset_array as $b){
   $assetQry=DB::table('assets')->where('id',$b)->first();
                                                iF($assetQry!=''){
                                            DB::table('contract_assets')->insert([
                                                    'contract_id'=>$id,
                                                    'contract_detail_id'=>$detail_id,
                                                    'asset_type'=>@$assetQry->asset_type,
                                                    'hostname'=>$b,

                                            ]);

                                       


                                     

                                            DB::table('assets')->where('id',$b)->update([ 'warranty_status'=>'Active', 'warranty_end_date'=>$request->contract_end_date,'SupportStatus'=>'Supported']);
                                            
                                                        }
                                                    }
                        }   
                    }
            
   
     DB::table('contract_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>@Auth::user()->firstname.' '.@Auth::user()->lastname,'contract_id'=>$request->id,'comment'=>'Contract successfully renewed.']);

               DB::table('contract_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Contract successfully renewed.','contract_id'=>$request->id]);
 return response()->json('success');

 
 
    }



    
    public function GetContractNotifications(Request $request){
            $email_qry=DB::table('client_emails')->where('client_id',$request->id)->get(); 
            return response()->json($email_qry);
    }
    public function GetSSLNotifications(Request $request){
            $email_qry=DB::table('client_ssl_emails')->where('client_id',$request->id)->get(); 
            return response()->json($email_qry);
    }
    

    public function UpdateContract(Request $request){
  
                      $data=array(
                                 
                             
                           'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id,
                                     'contract_notification'=>$request->contract_notification,
                                'estimate_no'=>$request->estimate_no,
                                'sales_order_no'=>$request->sales_order_no,
                                'invoice_no'=>$request->invoice_no,
                                'contract_description'=>$request->contract_description,
                                'invoice_date'=>date('Y-m-d',strtotime($request->invoice_date)),
                                'registered_email'=>$request->registered_email,
                                'po_no'=>$request->po_no,
                                'po_date'=>date('Y-m-d',strtotime($request->po_date)),
                            
                                'distributor_id'=>$request->distributor_id,
                                'reference_no'=>$request->reference_no,
                                'distrubutor_sales_order_no'=>$request->distrubutor_sales_order_no,
                                'vendor_id'=>$request->vendor_id,
                                'contract_type'=>$request->contract_type,

                                'contract_no'=>$request->contract_no,
                                'contract_start_date'=>date('Y-m-d',strtotime($request->contract_start_date)),
                                'contract_end_date'=>date('Y-m-d',strtotime($request->contract_end_date)) ,
                                     'updated_by'=>Auth::id(),
                                 'updated_at'=>date('Y-m-d H:i:s'),
                           
                                
                        );
 

                    $id=$request->id;
                     DB::Table('contracts')->where('id',$id)->update($data);
                               $qry=DB::Table('contracts')->where('id',$id)->first();
                                        

                                        $asset=DB::table('contract_assets')->where('is_deleted',0)->where('contract_id',$id)->get();
                                foreach($asset as $a){
                            
                                        DB::table('assets')->where('id',$a->hostname)->update(['warranty_status'=>'Inactive', 'warranty_end_date'=>'No contract Found','SupportStatus'=>'Unassigned']);

                                }
                                        DB::table('contract_details')->where('contract_id',$id)->where('is_deleted',0)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);
                                         DB::table('contract_assets')->where('contract_id',$id)->where('is_deleted',0)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s')]);



                               DB::table('contract_emails')->where('contract_id',$request->id)->delete();

  DB::table('contract_attachments')->where('contract_id',$request->id)->delete();
  DB::table('contract_comments')->where('contract_id',$request->id)->delete();




                   $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('contract_attachment/'.$a->attachment) );
                                             DB::table('contract_attachments')->insert([
                                                 'contract_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
                         $emailArray= $request->emailArray;
                              if(isset($request->emailArray)){
                        foreach($emailArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_emails')->insert([
                                                 'contract_id'=>$id,
                                                  
                                                 'renewal_email'=>$a->email,
                                              
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

                                $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_comments')->insert([
                                                 'contract_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

            $contractDetailsArray= $request->contractDetailsArray;
                         if(isset($request->contractDetailsArray)){
                        foreach($contractDetailsArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('contract_details')->insert([
                                                 'contract_id'=>$id,
                                                    'pn_no'=>$a->pn_no,
                                                    'qty'=>$a->qty,
                                                    'msrp'=>$a->msrp,
                                                    'detail_comments'=>$a->asset_description,
                                                 'added_by'=>Auth::id(),
                                             ]);
                                             $detail_id=DB::getPdo()->lastInsertId();
                                                $asset_array=$a->hostname_modal;
                                                foreach($asset_array as $b){
   $assetQry=DB::table('assets')->where('id',$b)->first();
                                                iF($assetQry!=''){
                                            DB::table('contract_assets')->insert([
                                                    'contract_id'=>$id,
                                                    'contract_detail_id'=>$detail_id,
                                                    'asset_type'=>@$assetQry->asset_type,
                                                    'hostname'=>$b,

                                            ]);

                                       


                                            if($qry->contract_status=='Active'){
                                            DB::table('assets')->where('id',$b)->update([ 'warranty_status'=>'Active', 'warranty_end_date'=>$request->contract_end_date,'SupportStatus'=>'Supported']);
                                            }else{
                                                          DB::table('assets')->where('id',$b)->update([ 'warranty_status'=>'Inactive', 'warranty_end_date'=>$request->contract_end_date,'SupportStatus'=>'Expired']);
                                            }
                                                        }
                                                    }
                        }   
                    }
            
   
DB::table('contract_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Contract updated','contract_id'=>$id]);
 return response()->json('success');
 
    }


public function getContractContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('contracts as a')->select('a.*','c.firstname','c.client_address','s.site_name','d.distributor_name','v.vendor_name','d.distributor_image','c.logo','v.vendor_image','s.address','s.city','s.country','s.phone','s.zip_code','s.province','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','a.ended_reason','a.ended_on','ued.firstname as ended_firstname','ued.lastname as ended_lastname','ued.email as ended_email') ->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('distributors as d','d.id','=','a.distributor_id')->leftjoin('vendors as v','v.id','=','a.vendor_id')->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->leftjoin('users as ued','ued.id','=','a.ended_by')->where('a.id',$id) ->first();
    $contract_end_date=date('Y-M-d',strtotime($q->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
             $ended_qry=DB::Table('users')->Where('id',$q->ended_by)->first();
                 $renewed_qry=DB::Table('users')->Where('id',$q->renewed_by)->first(); 

                   

                    if($q->contract_status=='Active'){

                                    if($abs_diff<=30){
                            $html.='<div class="block card-round   bg-new-yellow new-nav" >
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
                                         $html.='<div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-active-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Active</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">Until '.$contract_end_date.' ('.$abs_diff.' days remaining)</p>
                                    </div>
                                </div>';
                            }

                                }
                                elseif($q->contract_status=='Inactive'){
                                      $html.='<div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-renewed-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Renewed
</h4>
                                       <p class="mb-0  header-new-subtext" style="line-height:15px">On '.date('Y-M-d H:i:s A',strtotime($q->renewed_on)).' by   '.@$renewed_qry->firstname.' '.@$renewed_qry->lastname.'</p>
                                    </div>
                                </div>';

                                }elseif($q->contract_status=='Expired/Ended'){
                                        $html.='<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-ended-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Ended
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On '.date('Y-M-d',strtotime($q->ended_on)).' at  '.date('H:i:s A',strtotime($q->ended_on)).' By '.@$ended_qry->firstname.' '.@$ended_qry->lastname.' </p>
                                    </div>
                                </div>';
                            }
                                elseif($q->contract_status=='Ended'){
                                    $html.='<div class="block card-round   bg-new-red new-nav" >
                                
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-expired-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Ended
</h4>   
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On '.date('Y-M-d',strtotime($q->ended_on)).' at  '.date('H:i:s A',strtotime($q->ended_on)).' By '.@$ended_qry->firstname.' '.@$ended_qry->lastname.' </p>
                                    </div>
                                </div>';
                            }
                                elseif($q->contract_status=='Expired'){
                                    $html.='<div class="block card-round   bg-new-red new-nav" >

                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="'.('public/img/icon-expired-removebg-preview.png').'" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">Contract Expired
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On '.$contract_end_date.'</p>
                                    </div>
                                </div>';
                                }
 
                                                                


                                    $html.='<div class="new-header-icon-div d-flex align-items-center no-print">';
                                            if(Auth::check()){    
                                            if(@Auth::user()->role!='read'){ 
                                                      
                                                        if($q->contract_status!='Inactive'){
                                                    $html.='<a href="'.url('renew-contract').'?id='.$q->id.'" i  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Renew Contract" class=" ">
                                                <img src="public/img/action-white-renew.png?cache=1" width="20px">
                                            </a>';
                                                  }
                                    if($q->contract_status!='Inactive' && $q->contract_status!='Expired/Ended'){
                                                    
                                                   
                                             $html.='<span  > 
                                             <a href="javascript:;" class="btnEnd"   data="'.$q->id.'" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="End Contract" class=" "><img src="public/img/action-white-end-revoke.png?cache=1" width="20px"></a>
                                         </span>';
                                         }
                                          
                                        }
                                        }
$html.=' <a  target="_blank" href="'.url('pdf-contract').'?id='.$q->id.'"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf" class="  " style="padding:5px 7px">
                                                <img src="public/img/action-white-pdf.png?cache=1" width="24px">
                                            </a>
     <a  href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';


                                                  if(@Auth::user()->role!='read' && Auth::check()) {
                                                        
                                              $html.='<a   href="'.url('edit-contract').'?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>

                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
                                        }
                                    $html.='</div>


                                </div>
                            </div>
                        </div>

                        <div class="block new-block position-relative mt-3" >
                                                <div class="top-div text-capitalize">'.$q->contract_type .' Contract</div>
                            
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
 <div class="top-right-div top-right-div-red text-capitalize">Vendor</div>
                            
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
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->vendor_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Contract #</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.$q->contract_no.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Description</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->contract_description.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">End User Email</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->registered_email.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/vendor_logos'.'/'.$q->vendor_image.'" style="width: 100%;">
                                                </div> 

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>



                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-blue text-capitalize">Distribution</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                        <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Distributor</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->distributor_name.'</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Reference #</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.$q->reference_no.'
                                                
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Sales Order #</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                '.$q->distrubutor_sales_order_no.'
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         
                                    </div>
                                    <div class="col-sm-2">';
                                               if($q->distributor_image!=''){


                                               $html.='<div class="bubble-white-new bubble-text-sec" style="padding:10px">
 
                                                      <img src="public/distributor_logos/'.$q->distributor_image.'" style="width: 100%;">
                                                </div> ';
                                                }
$html.='</div>

                                      
                                               </div>      

                          
                 </div>
             </div>
         </div>








                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-green text-capitalize">Purchasing</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-6">
                                        <div class="form-group row">
                                                        <div class="col-sm-6">
                                           <div class="bubble-new">Estimate #
                                           </div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-6 form-group ">
                                                
                                           <div target="_blank" class="bubble-white-new bubble-text-sec"><a href="'.url('GetZohoInvoicesAuth?estimate_number=').''.$q->estimate_no.'">'.$q->estimate_no.'</a></div> 
                                     
                                            </div>

    


                                         <div class="col-sm-6 form-group ">
                                           <div class="bubble-new">Sales Order #</div> 
                                       </div>
                                            <div class="col-sm-6 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    <a  target="_blank" href="'.url('GetZohoInvoicesAuth?sales_number=').''.$q->sales_order_no.'">'.$q->sales_order_no.'</a> 
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                      
                                         <div class="col-sm-6 form-group ">
                                           <div class="bubble-new">Invoice #</div> 
                                       </div>
                                            <div class="col-sm-6 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                        <a target="_blank" href="'.url('GetZohoInvoicesAuth?invoice_number=').''.$q->invoice_no.'">'.$q->invoice_no.'</a> 
                                                </div> 
                                     
                                            </div>
                                              <div class="col-sm-6 form-group ">
                                           <div class="bubble-new">Invoice Date</div> 
                                       </div>
                                            <div class="col-sm-6 form-group " >
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    '.date('Y-M-d',strtotime($q->invoice_date)).'
                                                  
                                                </div> 
                                     
                                            </div>

                                              <div class="col-sm-6 form-group ">
                                           <div class="bubble-new">PO #</div> 
                                       </div>
                                            <div class="col-sm-6 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                
                                                      <a href="'.url('GetZohoInvoicesAuth?po_number=').''.$q->po_no.'" target="_blank">'.$q->po_no.'</a> 
                                                  
                                                </div> 
                                     
                                            </div>
                                            <div class="col-sm-6 form-group ">
                                           <div class="bubble-new">PO Date</div> 
                                       </div>
                                            <div class="col-sm-6 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                     '.date('Y-M-d',strtotime($q->po_date)).' 
                                                  
                                                </div> 
                                     
                                            </div>
                                        </div>
                                        </div>
                                        <div class="col-lg-4">
                                        </div>
                                        <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-amaltitek.png?cache=1" style="width: 100%;">
                                                </div> 

                                    
                                         
                                    </div>
                                    

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>


     </div>';

 

$line_items=DB::Table('contract_details as ca')->select( 'ca.*')->where('ca.contract_id',$q->id) ->orderBy('ca.contract_detail_id','asc')->where('is_deleted',0)->get();
  
 if(sizeof($line_items)>0){
$html.='  <div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Contract Details</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock"> ';
                                                                        foreach($line_items as $c){
                                                             $html.='<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          '.$c->qty.'  <!-- <img width="40px" src="'.public_path('img/profile-white.png').'"> --></b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">'.$c->pn_no.'<br><span class="comments-subtext">'.$c->detail_comments.'
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: 130px;">
                                                        
                                                            <h3 class="mb-0" style="    position: absolute;
    top: 6px;
    right: 10px;
}">$'.number_format($c->msrp,2).'</h3>
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td colspan="2"  class="pt-0" style="vertical-align: top;" >
                                                       <div class="pl-2  mb-0 row  "> ';
                                                                     $line_item=DB::Table('contract_assets as ca')->selectRaw('a.hostname,a.AssetStatus,o.operating_system_name,a.fqdn,a.id,at.asset_icon,at.asset_type_description,a.sn,a.asset_type,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM assets  where is_deleted=0 and asset_type="virtual"
        ORDER BY id ASC
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r
) t where t.id=ca.hostname limit 1) as rownumber_virtual,(SELECT row_number FROM (
    SELECT   id,@curRow1 := @curRow1 + 1 AS row_number 
    FROM (
        SELECT * FROM assets  where is_deleted=0 and asset_type="physical"
        ORDER BY id ASC
    ) l 
    JOIN (
        SELECT @curRow1 := 0 
    ) r
) t where t.id=ca.hostname limit 1) as rownumber_physical')->where('ca.contract_id',$q->id)->where('ca.contract_detail_id',$c->contract_detail_id)->join('assets as a','a.id','=','ca.hostname')->join('operating_systems as o','a.os','=','o.id')->leftjoin('asset_type as at','a.asset_type_id','=','at.asset_type_id')->groupBy('ca.hostname')->where('ca.is_deleted',0)->orderBy('a.hostname','asc')->get();
 $cvm='';
                                                                    foreach($line_item as $l){
                                                                                if($l->asset_type=='virtual'){
                                                                                    $link='virtual?id='.$l->id.'&page='.(ceil($l->rownumber_virtual/10));
                                                                                 $cvm='<p class="HostActive text-white  my-0">'.$l->asset_type_description.'</p><p class="HostActive my-n1 text-orange"  >'.$l->operating_system_name.'</p>'; 

                                                                                }
                                                                                else{
                                                                                    $link='physical?id='.$l->id.'&page='.(ceil($l->rownumber_physical/10));
                                                                                      $cvm='<p class="HostActive text-white  my-0">'.$l->asset_type_description.'</p><p class="HostActive my-n1 text-orange"  >'.$l->sn.'</p>'; 
                                                                                }
                                                                                    
                                                                                    
           $html.='<div class="block block-rounded ml-2  table-block-new ">


<div class="d-flex block-content align-items-center px-2 py-2"><p class="font-11pt mr-1   mb-0  '.($l->asset_type=='physical'?'c4-p':'c4-v').' " style="max-width:20px; " data="262">'.($l->asset_type=='physical'?'P':'V').'</p><p class="font-12pt mb-0 text-truncate   c4" style="  background-color: rgb(151, 192, 255); color: rgb(89, 89, 89); border-color: rgb(89, 89, 89);" data="262">'.$l->fqdn.'</p>';

if($l->asset_type=='physical'){
$html.="<img src='public/img/icon-p-asset-d-grey.png' class='toggle pl-2' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title=''   width='35px' style='object-fit:contain'  >";
}else{
    $html.="<img src='public/img/icon-vm-grey-darker.png' class='toggle pl-2' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title=''   width='30px' style='object-fit:contain'  >";
}


$html.=' <a  class="dropdown-toggle ml-2"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                <img src="public/img/dots.png?cache=1"   >
                                                                        </a>
                                         <div class="dropdown-menu py-0 pt-1 " aria-labelledby="dropdown-dropright-primary">
      
                  <a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="print-asset?id='.$l->id.'">   <div style="width: 32;  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > &nbsp;&nbsp;View Asset</div></a>  
                 
                </div>


</div>

 
           </div>';
                  
              }
                                                                     

                                                       $html.='</div>
                                                    </td>
                                                    
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
}

                                $html.='</div>

                            </div>';


}
   $contract=DB::table('contract_comments')->where('contract_id',$q->id) ->get();  
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
                                                          <img width="40px" src="public/img/profile-white.png?cache=1"> </b></h1>
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




 $contract=DB::table('contract_attachments')->where('contract_id',$q->id) ->get();  
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
  
 

                                                        $html.='<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png?cache=1"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">'.$c->name.'<br><span class="comments-subtext">On '.date('Y-M-d',strtotime($c->date)).' at '.date('h:i:s A',strtotime($c->date)).' GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                        
                                                      
                                                       <!--  <a type="button" class="  btnDeleteAttachment    btn btn-sm btn-link text-danger" data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="public/img/trash--v1.png?cache=1" width="24px">
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


   $contract=DB::table('contract_audit_trail as c')->select('c.*','u.firstname','u.lastname')->leftjoin('users as u','u.id','=','c.user_id')->where('c.contract_id',$q->id)->get(); 
  
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
                                                          <img width="40px" src="public/img/profile-white.png?cache=1"> </b></h1>
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
return response()->json($html);

}

    
    
    
 


}