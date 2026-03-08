<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
 use App\Imports\AssetImport;
  use Excel;
  
use App\Exports\ExportExcelPhysical;
use App\Exports\ExportVirtualAssets;
use App\Exports\ExportAssets;
 
use DateTime;
 use Validator;
class AssetsController extends Controller
{
    //
public function __construct(){
     
      
     }
   
   

   
    public function getSiteByClientId(Request $request){
 
         $qry=DB::Table('sites')->where('is_deleted',0)->where('client_id',$request->id)->orderby('site_name','asc')->get();
         return response()->json($qry);
     
    }

     public function getDomainByClientId(Request $request){
 
   
         $qry=DB::Table('domains')->where('is_deleted',0)->where('client_id',$request->id)->get();
         return response()->json($qry);
     
    }

     public function showAssetIp(Request $request){
 
   
         $qry=DB::Table('asset_ip_addresses')->where('asset_id',$request->id)->get();
         return response()->json($qry);
     
    }

    
    public function Virtual($page_type=''){
 
     return view('virtual',['page_type'=>$page_type]);
 
    }
    public function Assets(){
 
     return view('Assets' );
 
    }



     public function Physical($page_type=''){
 
     return view('Physical',['page_type'=>$page_type]);
 
    }
    

    public function AddAssets($type){
        
     return view('AddAssets',['type'=>$type]);
 
    }
 
    public function EditAssets(){
 

     return view('EditAssets');
 
    }



    

  public function getAttachmentAssets(Request $request){
         $qry=DB::table('asset_attachments')->where('asset_id',$request->id)->get();
          return response()->json($qry);
     }

     public function getCommentsAssets(Request $request){
         $qry=DB::table('asset_comments')->where('asset_id',$request->id)->get();
          return response()->json($qry);
     }
   public function getIpAssets(Request $request){
         $qry=DB::table('asset_ip_addresses')->where('asset_id',$request->id)->get();
          return response()->json($qry);
     }
     public function ExportPrintAsset(){
 

     return view('exports/ExportPrintAsset');
 
    }

 
    public function EndContract(Request $request){
            if($request->end==1){



                       DB::Table('contracts')->where('id',$request->id)->update(['contract_status'=>'Active']);       
                DB::table('contract_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'contract_id'=>$request->id,'comment'=>'Contract successfully Reninstated.<br>'.$request->reason]);

               DB::table('contract_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Contract successfully Reinstated.','contract_id'=>$request->id]);
    return redirect()->back()->with('success','Contract Reinstated Successfully');
            }
            else{
             DB::Table('contracts')->where('id',$request->id)->update(['contract_status'=>'Expired/Ended','ended_reason'=>$request->reason,'ended_by'=>Auth::id(),'ended_on'=>date('Y-m-d H:i:s')]);       
                DB::table('contract_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'contract_id'=>$request->id,'comment'=>'Contract successfully Ended.<br>'.$request->reason]);

               DB::table('contract_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Contract successfully Ended.','contract_id'=>$request->id]);
                   return redirect()->back()->with('success','Contract Ended Successfully');
}

           
    }

    public function DecommisionAsset(Request $request){

        $qry= DB::Table('assets')->where('id',$request->id)->first();
         $status=0;      
        if($qry->AssetStatus==1){
            $detail='Asset successfully decommissioned.';
        $status=0;
        $InactiveDate=date('Y-m-d');
        }
        else{
            $detail='Asset successfully Re-activated.';
        $status=1;
        $InactiveDate='';
        }
             DB::Table('assets')->where('id',$request->id)->update(['AssetStatus'=>$status,'InactiveDate'=>$InactiveDate,'InactiveBy'=>Auth::id() ]);       
                DB::table('asset_comments')->insert(['added_by'=>Auth::id(),'date'=>date('Y-m-d H:i:s'),'name'=>Auth::user()->firstname.' '.Auth::user()->lastname,'asset_id'=>$request->id,'comment'=>$detail.'<br>'.$request->reason]);

               DB::table('asset_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>$detail,'asset_id'=>$request->id]);


               return redirect()->back()->with('success',$detail);
    }
    
    public function UploadAssetCsv(Request $request){
              $import= new AssetImport;
        Excel::import($import, $request->file('file')->store('temp') );
            
            return redirect()->back()->with('response',$import->data);     
                    
    }

    public function ExportPdfAsset(){
 

    $pdf = PDF::loadView('exports/ExportPdfAsset');
   
    return $pdf->stream('Assets.pdf');

 
    }
     
    
   public function showContractAsset(Request $request){

     $contract=DB::table('contract_assets as a')->select('c.contract_status','c.contract_type','m.vendor_name','c.contract_no','c.contract_end_date','c.contract_description')->join('contracts as c','c.id','=','a.contract_id')->leftjoin('vendors as m','m.id','=','c.vendor_id') ->where('a.hostname',$request->id)   ->where('a.is_deleted',0)->get();
      
        return response()->json($contract);
      
}

   public function showSSLAsset(Request $request){

     $contract=DB::table('ssl_certificate as c')->select('c.cert_status','c.cert_type','m.vendor_name' ,'c.cert_edate','c.cert_name') ->leftjoin('vendors as m','m.id','=','c.cert_issuer')   ->whereRaw('FIND_IN_SET(?, c.cert_hostname)', [$request->id]) ->where('c.is_deleted',0)->get();
      
        return response()->json($contract);
      
}










    
    public function DeleteVirtualAssets(Request $request){

            $qry=DB::table('assets')->where('id',$request->id)->first();

             $userAccess=explode(',',Auth::user()->access_to_client);
            if(Auth::user()->role!='admin'  ){
              
            if(!in_array($qry->client_id,$userAccess)){
                echo "You dont have access";
                exit;
            }
            }
            if(Auth::user()->role=='read'){
              echo "You dont have access";
                exit;
            }


                      DB::Table('assets')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s') ,'position'=>null]);
          DB::table('assets')->where('position','>',$qry->position)->where('asset_type','virtual')->decrement('position');

          return redirect()->back()->with('success','Virtual Asset Deleted Successfully');
           

    }


    public function DeletePhysicalAssets(Request $request){

            $qry=DB::table('assets')->where('id',$request->id)->first();
             $userAccess=explode(',',Auth::user()->access_to_client);
            if(Auth::user()->role!='admin'  ){
              
            if(!in_array($qry->client_id,$userAccess)){
                echo "You dont have access";
                exit;
            }
            }
            if(Auth::user()->role=='read'){
              echo "You dont have access";
                exit;
            }

          DB::Table('assets')->where('id',$request->id)->update(['is_deleted'=>1,'deleted_at'=>date('Y-m-d H:i:s') ,'position'=>null]);
          DB::table('assets')->where('position','>',$qry->position)->where('asset_type','physical')->decrement('position');

          return redirect()->back()->with('success','Physical Asset Deleted Successfully');
           

    }

    
    public function ShowAssets(Request $request){

                        
       $qry=DB::table('assets as a')->select('a.*','s.site_name','d.domain_name','c.firstname','o.operating_system_name','m.vendor_name' ,'at.asset_icon','at.asset_type_description','at.asset_type_description as asset_type_name','n.vlan_id as vlanId','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')  ->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$request->id)->first();
                  return response()->json($qry);
                   

    }
    
           public function getParentAsset(Request $request){

 
                                            $asset=DB::Table('assets as a')->leftjoin('asset_type as t','a.asset_type_id','=','t.asset_type_id')->where('t.asset_type_description','Storage Controller')->where('a.is_deleted',0)->where('a.client_id',$request->id) ->orderBy('a.hostname','asc')->get();
                                          

 return response()->json($asset);
                   

    }
   public function ExportExcelPhysical(Request $request) 
        {
             
 
            return Excel::download(new ExportExcelPhysical($request), 'PhysicalAsset.xlsx');
        } 
           public function ExportExcelAssets(Request $request) 
        {
             
 
            return Excel::download(new ExportAssets($request), 'Asset.xlsx');
        } 


   public function exportExcelVirtual(Request $request) 
        {
             
 
            return Excel::download(new ExportVirtualAssets($request), 'VirtualAsset.xlsx');
        
}
   public function PrintVirtual(){
 

     return view('exports/PrintVirtual');
 
    }
     public function PrintPhysical(){
 

     return view('exports/PrintPhysical');
 
    }

 public function getVendorOfPhysical(Request $request){
                $client_id=$request->client_id;
                $site_id=$request->site_id;
                              
                    if($site_id!='' && @$site_id[0]!=''){
 
            $qry=DB::Table('vendors as v')->select('v.*')->join('assets as c','c.manufacturer','=','v.id')->where('c.client_id',$client_id)->whereIn('c.site_id',$site_id)->groupBy('c.manufacturer')->orderby('v.vendor_name','asc')->get();
                    }
                    else{
$qry=DB::Table('vendors as v')->select('v.*')->join('assets as c','c.manufacturer','=','v.id')->where('client_id',$client_id)->groupBy('c.manufacturer')->orderby('v.vendor_name','asc')->get();
                    }
                    return response()->json($qry);
 }
   
      
        public function SwapVirtualRows(Request $request){
             $qry='';   
             $sno=0;
             $page=1;
             $limit=$request->limit!=''?$request->limit:10;

                if($request->page==''){
                    $offset=0;
                }
                else{
                    $offset=($request->page*$limit)-$limit;
                 
                }

                // $qry=DB::table('assets')->where('asset_type','Virtual')->where('is_deleted',0)->orderBy('position','asc')->offset($offset)->limit(20)->get();
       
                 foreach($request->id as $key=>$q){
                    $sno++;
                      
 
            DB::table('assets')->where('id',$q)->where('is_deleted',0)->where('asset_type','virtual')->update(['position'=>($key+1)+$offset]);
           }
            return response()->json('success'); 
        }

      
        public function SwapPhysicalRows(Request $request){
             $qry='';   
             $sno=0;
             $page=1;
             $limit=$request->limit!=''?$request->limit:10;

                if($request->page==''){
                    $offset=0;
                }
                else{
                    $offset=($request->page*$limit)-$limit;
                 
                }

                // $qry=DB::table('assets')->where('asset_type','Virtual')->where('is_deleted',0)->orderBy('position','asc')->offset($offset)->limit(20)->get();
       
                 foreach($request->id as $key=>$q){
                    $sno++;
                      
 
            DB::table('assets')->where('id',$q)->where('is_deleted',0)->where('asset_type','physical')->update(['position'=>($key+1)+$offset]);
           }
            return response()->json('success'); 
        }








     public function InsertAssets(Request $request){
           
                                $position=DB::Table('assets')->where('asset_type',$request->asset_type)->max('position');
                                    if($request->HasWarranty==1){
                                        $warranty_status='Unassigned';
                                    }
                                    else{
                                        $warranty_status='N/A';   
                                    }



                                       if($request->ntp==1){
                                        $ssl_status='Unassigned';
                                    }
                                    else{
                                        $ssl_status='N/A';   
                                    }









                                    $app_owner=$request->app_owner;
                            $sla=$request->app_owner;
                            $internet_facing= $request->internet_facing ?? 0;
                            $clustered= $request->clustered ?? 0;
                            $disaster_recovery= $request->disaster_recovery ?? 0;
                            $monitored= $request->monitored ?? 0;
                            $load_balancing= $request->load_balancing ?? 0;
                            $patched= $request->patched ?? 0;
                            $antivirus= $request->antivirus ?? 0;
                            $smtp= $request->smtp ?? 0;
                            $replicated= $request->replicated ?? 0;
                            $ntp= $request->ntp ?? 0;
                            $backup= $request->backup ?? 0;
                            $syslog= $request->syslog ?? 0;
                         



                      if($request->managed!=1){
                            $app_owner='N/A';
                            $sla='N/A';
                            $internet_facing=2;
                            $clustered=2;
                            $disaster_recovery=2;
                            $monitored=2;
                            $load_balancing=2;
                            $patched=2;
                            $antivirus=2;
                            $smtp=2;
                            $replicated=2;
                            $ntp=2;
                            $backup=2;
                            $syslog=2;
                         

                      }              






$domain=DB::table('domains')->where('id',$request->domain)->first();


                     $data=array(
                                'asset_type'=>$request->asset_type,
                                'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id,
                                'warranty_status'=>'Inactive',
                                'warranty_end_date'=>'No Contract Found',
                                'hostname'=>$request->hostname,
                                'domain'=>$request->domain,
                                'fqdn'=>$request->hostname.'.'.@$domain->domain_name,
                                'role'=>$request->role,
                                'SupportStatus'=>$warranty_status,
                                'use_'=>$request->use_,
                                'os'=>$request->os,
                                'HasWarranty'=>$request->HasWarranty??0,
                                'AssetStatus'=>1,
                              
                                'app_owner'=>$app_owner,
                                'ip_address'=>$request->ip_address,
                                'vlan_id'=>$request->vlan_id,
                                'network_zone'=>$request->network_zone,
                                'internet_facing'=> $internet_facing  ,
                                'disaster_recovery'=>$disaster_recovery ,
                                'load_balancing'=>$load_balancing ,
                                'clustered'=>$clustered ,
                                  'managed'=>$request->managed==1?1:0,      
                                'monitored'=>$monitored ,
                                'patched'=>$patched ,
                                'antivirus'=>$antivirus ,
                                'backup'=>$backup ,
                                'replicated'=>$replicated ,
                                'smtp'=>$smtp,
                                'ntp'=>$ntp,
                                'syslog'=>$syslog,
                                'sla'=>$sla,
                                'vcpu'=>$request->vcpu,
                                'memory'=>$request->memory,
                                'comments'=>$request->comments,
                                'location'=>$request->location,
                                'manufacturer'=>$request->manufacturer,
                                'model'=>$request->model,
                                'type'=>$request->type,
                                'sn'=>$request->sn,
                                'parent_asset'=>$request->parent_asset,
                                'ssl_certificate_status'=>$ssl_status,
                                'cpu_model'=>$request->cpu_model,
                                'cpu_sockets'=>$request->cpu_sockets,
                                'cpu_cores'=>$request->cpu_cores,
                                'cpu_freq'=>$request->cpu_freq,
                                'cpu_hyperthreadings'=>$request->cpu_hyperthreadings,
                                'cpu_total_cores'=>$request->cpu_total_cores,
                                'NotSupportedReason'=>$request->NotSupportedReason,
                                'asset_type_id'=>$request->asset_type_id,
                                'position'=>$position+1,
                                'created_by'=>Auth::id(),
                                    

                        );
                     DB::Table('assets')->insert($data);
             
                              $id=DB::getPdo()->lastInsertId();
                     $ipArray= $request->ipArray;
                        if(isset($request->ipArray)){
                        foreach($ipArray as $a){
                            $a=json_decode($a);
                                    DB::table('asset_ip_addresses')->insert([
                                            'asset_id'=>$id,
                                            'ip_address_value'=>$a->ip_address_value,
                                            'ip_address_name'=>$a->ip_address_name,
                                    ]);
                                }

                            }
                             
 
                            $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('asset_attachment/'.$a->attachment) );
                                             DB::table('asset_attachments')->insert([
                                                 'asset_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
  $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('asset_comments')->insert([
                                                 'asset_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }

DB::table('asset_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Asset added','asset_id'=>$id]);
   return response()->json('success');
    }





    public function UpdateAssets(Request $request){
                    $qry=DB::table('contract_assets as ca')->join('contracts as c','c.id','=','ca.contract_id')->join('assets as a','ca.hostname','=','a.id')->where('a.id',$request->id)->where('c.is_deleted',0)->orderBy('c.id','desc')->first();
                     if(!$request->HasWarranty==1 || !$request->AssetStatus==1  ){
                                        $warranty_end_date='No Contract Found';
                                                $warranty_status='Inactive';
                                                $support_status='N/A';
                                    }
                                    else{   
                                        if($qry==''){
                                                  $warranty_end_date='No Contract Found';
                                                $warranty_status='Inactive';
                                                $support_status='Unassigned';
                                        }
                                        else{

                                        if($qry->contract_status=='Active'){
                                         
                                                $warranty_end_date=$qry->warranty_end_date;
                                                $warranty_status='Active';
                                                $support_status='Supported';

                                            }else{
                                                       
                                                            $warranty_end_date=$qry->warranty_end_date;
                                                $warranty_status='Inactive';
                                                $support_status='Expired';
                                    }

                                         }
                                            }

                            
                              

                                    $app_owner=$request->app_owner;
                            $sla=$request->sla;
                            $internet_facing= $request->internet_facing ?? 0;
                            $clustered= $request->clustered ?? 0;
                            $disaster_recovery= $request->disaster_recovery ?? 0;
                            $monitored= $request->monitored ?? 0;
                            $load_balancing= $request->load_balancing ?? 0;
                            $patched= $request->patched ?? 0;
                            $antivirus= $request->antivirus ?? 0;
                            $smtp= $request->smtp ?? 0;
                            $replicated= $request->replicated ?? 0;
                            $ntp= $request->ntp ?? 0;
                            $backup= $request->backup ?? 0;
                            $syslog= $request->syslog ?? 0;
                         



                      if($request->managed!=1){
                            $app_owner='N/A';
                            $sla='N/A';
                             
                            $disaster_recovery=2;
                            $monitored=2;
                           
                            $patched=2;
                            $antivirus=2;
                            $smtp=2;
                            $replicated=2;
                           
                            $backup=2;
                            $syslog=2;
                         

                      }              

$domain=DB::table('domains')->where('id',$request->domain)->first();
    
      
                     $data=array(
                              'client_id'=>$request->client_id,
                                'site_id'=>$request->site_id,
                                'hostname'=>$request->hostname,
                                'domain'=>$request->domain,
                                'fqdn'=>$request->hostname.'.'.@$domain->domain_name,
                                'role'=>$request->role,
                                'warranty_status'=>$warranty_status,
                                'SupportStatus'=>$support_status,
                                'warranty_end_date'=>$warranty_end_date,
                                'use_'=>$request->use_,
                                'os'=>$request->os,
                                'asset_type_id'=>$request->asset_type_id,
                                  'managed'=>$request->managed==1?1:0,     
                                'app_owner'=>$app_owner,
                                'ip_address'=>$request->ip_address,
                                'vlan_id'=>$request->vlan_id,
                                'network_zone'=>$request->network_zone,
                                'internet_facing'=>$internet_facing,
                                'disaster_recovery'=>$disaster_recovery,
                                'load_balancing'=>$load_balancing,
                                'clustered'=>$clustered,
                                 'HasWarranty'=>$request->HasWarranty??0,
                           
                           
                                'monitored'=>$monitored,
                                'patched'=>$patched,
                                'antivirus'=>$antivirus,
                                'backup'=>$backup,
                                'replicated'=>$replicated,
                                'location'=>$request->location,
                                'smtp'=>$smtp,
                                'ntp'=>$ntp,
                                'syslog'=>$syslog,
                                'sla'=>$sla,
                                'vcpu'=>$request->vcpu,
                                'memory'=>$request->memory,
                                'comments'=>$request->comments,
                                'manufacturer'=>$request->manufacturer,
                                'model'=>$request->model,
                                'type'=>$request->type,
                                'sn'=>$request->sn,
                                'parent_asset'=>$request->parent_asset,
                                'cpu_model'=>$request->cpu_model,
                                'cpu_sockets'=>$request->cpu_sockets,
                                'cpu_cores'=>$request->cpu_cores,
                                    'NotSupportedReason'=>$request->NotSupportedReason,
                                'cpu_freq'=>$request->cpu_freq,
                                'cpu_hyperthreadings'=>$request->cpu_hyperthreadings,
                                'cpu_total_cores'=>$request->cpu_total_cores,
                                'updated_at'=>date('Y-m-d H:i:s'),
                                'updated_by'=>Auth::id(),
                        );
                     DB::Table('assets')->where('id',$request->id)->update($data);
                                DB::table('asset_ip_addresses')->where('asset_id',$request->id)->delete();    

                                  DB::table('asset_attachments')->where('asset_id',$request->id)->delete();    
                                    DB::table('asset_comments')->where('asset_id',$request->id)->delete();    
                                        $id=$request->id;

                      $ipArray= $request->ipArray;
                        if(isset($request->ipArray)){
                        foreach($ipArray as $a){
                            $a=json_decode($a);
                                    DB::table('asset_ip_addresses')->insert([
                                            'asset_id'=>$id,
                                            'ip_address_value'=>$a->ip_address_value,
                                            'ip_address_name'=>$a->ip_address_name,
                                    ]);
                                }

                            }
                             
 
                            $attachment_array= $request->attachmentArray;
                        if(isset($request->attachmentArray)){
                        foreach($attachment_array as $a){
                            $a=json_decode($a);
                             
                                   copy( public_path('temp_uploads/'.$a->attachment), public_path('asset_attachment/'.$a->attachment) );
                                             DB::table('asset_attachments')->insert([
                                                 'asset_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'attachment'=>$a->attachment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                    }
                    
  $commentArray= $request->commentArray;
                         if(isset($request->commentArray)){
                        foreach($commentArray as $a){
                            $a=json_decode($a);
                             
                              
                                             DB::table('asset_comments')->insert([
                                                 'asset_id'=>$id,
                                                 'date'=>date('Y-m-d H:i:s',strtotime($a->date.' '.$a->time)),
                                                 'comment'=>$a->comment,
                                                 'name'=>$a->name,
                                                 'added_by'=>Auth::id(),
                                             ]);
                        }   
                        }
DB::table('asset_audit_trail')->insert(['user_id'=>Auth::id(),'description'=>'Asset updated','asset_id'=>$id]);
    return response()->json('success');
 
    }



    
    


public function getPhysicalContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('assets as a')->select('a.*','s.site_name','d.domain_name','c.firstname','o.operating_system_name','o.operating_system_image','m.vendor_name' ,'s.address','s.city','s.country','s.phone','s.zip_code','s.province','at.asset_icon','at.asset_type_description','at.asset_type_description as asset_type_name','n.vlan_id as vlanId','n.subnet_ip','n.mask','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo','m.vendor_image','nz.network_zone_description','nz.tag_back_color','nz.tag_text_color')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')  ->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('network_zone as nz','nz.network_zone_description','=','n.zone')->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   
  $contract_ssl_line_items=DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.firstname,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname',$q->id)->join('contracts  as a','a.id','=','ca.contract_id')->join('clients as c','c.id','=','a.client_id')->join('vendors as v','v.id','=','a.vendor_id')->groupBy('a.id')->where('a.is_deleted',0)->orderBy('a.contract_no','asc')->get();
                                                                         
 
  $ssl_line_items_2=DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate,a.cert_rdate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.firstname ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname',$q->id)->join('ssl_certificate  as a','a.id','=','ca.ssl_id')->join('clients as c','c.id','=','a.client_id')->leftjoin('vendors as v','v.id','=','a.cert_issuer') ->where('a.is_deleted',0)->orderBy('a.cert_name','asc')->get();


                   if($q->AssetStatus==1){
                           $html.='<div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-asset-white.png?cache=1" width="40px">
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
                                <img src="public/img/header-asset-white.png?cache=1" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
 $renewed_qry=DB::Table('users')->Where('id',$q->InactiveBy)->first(); 
                         

  
                                $html.='<p class="mb-0  header-new-subtext" style="line-height:17px">On '.date('Y-M-d',strtotime($q->InactiveDate)).' by '.@$renewed_qry->firstname.' '.@$renewed_qry->lastname.'</p>
                                    </div>
                                </div>';
                                   
                        }


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                    if(Auth::user()->role!='read') {
                                                      
                                                       
                                                      
                                    if($q->AssetStatus==1){
                                                      $html.='<span  > 
                                             <a href="javascript:;" class="btnEnd"  data="'.$q->AssetStatus.'" data-id="'.$q->id.'" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="public/img/icon-header-white-end-decom.png?cache=1" width="22px"></a>
                                         </span>';
                                                            }else{
                                                      $html.='    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="'.$q->AssetStatus.'" data-id="'.$q->id.'" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="public/img/icon-header-white-reactivate.png?cache=1" width="22px"></a>
                                         </span>';
                                                    }
                                          
                                         }
                                        
$html.=' <a  target="_blank" href="pdf-asset?id='.$q->id.'"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf"  style="padding:5px 7px">
                                                <img src="public/img/action-white-pdf.png?cache=1" width="24px"  >
                                            </a>
     <a href="javascript:;" onclick="window.print()"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-assets?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
                                            }

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative mt-3" >
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
                                            ';                                             if($q->asset_type=='physical'){
                                            $html.='
                                           <div class="form-group row">
                                                        <div class="col-sm-4">
                                           <div class="bubble-new">Location</div> 
                                       </div>
                                                                                  
                                            <div class="col-sm-8">
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>'.$q->location.'</b></div> 
                                     
                                            </div>



                                         </div>
                                         ';
                                     }
                                       
                                         $html.='
                                     
                                         
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
                                                       <div class="col-sm-4 t er">

<div class="contract_type_button  w-100 mr-4 mb-3">
  <input type="checkbox" class="custom-control-input" id="disaster_recovery1" name="disaster_recovery" disabled="" value="1" '.($q->disaster_recovery==1?'checked':'').'>
  <label class="btn btn-new w-100  py-1 font-11pt " for="disaster_recovery1">D/R Plan</label>
</div>
</div>
 

 


 <div class="col-sm-4  ">

<div class="contract_type_button  w-100 mr-4  ">
          <input type="checkbox" class="custom-control-input" id="clustered" name="clustered"  disabled="" value="1"  '.($q->clustered==1?'checked':'').'>
  <label class="btn btn-new w-100  py-1 font-11pt " for="clustered"> Clustered</label>
</div>
</div>

 <div class="col-sm-4 text-center">

<div class="contract_type_button  w-100 mr-4  ">
     <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" value="1" disabled="" '.($q->internet_facing==1?'checked':'').'>

       <label class="btn btn-new w-100  py-1 font-11pt " for="internet_facing"> Internet Facing</label>
</div>
</div>




 <div class="col-sm-4  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.($q->load_balancing==1?'checked':'').'>
  <label class="btn btn-new w-100  py-1 font-11pt " for="load_balancing"> Load Balanced</label>
</div>
</div>




 <div class="col-sm-4 text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.(sizeof($ssl_line_items_2)>0?'checked':'').'>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> SSL Certificate</label>
</div>
</div>


 <div class="col-sm-4   text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.(sizeof($contract_ssl_line_items)>0?'checked':'').'>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> '.(sizeof($contract_ssl_line_items)>0?'Supported':'Unsupported').'</label>
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

         ';
if($q->asset_type=='physical'){

         $html.='

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
                                        ';
                                        if($q->asset_type_description=='Physical Server'){
                                        $html.='<div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">CPU</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->cpu_sockets.' '.$q->cpu_model.' '.$q->cpu_cores.' C @ '.$q->cpu_freq.' GHz
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                        ';
                                    }

                                        $html.='<div class="form-group row">
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
         </div>';

     }
     else{
$html.='

                            <div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-blue text-capitalize">Resources</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                    
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">vCPUs</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->vcpu.'  
                                                  
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
                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
 

                                                      <img src="public/img/static-vm.png?cache=1" style="width: 100%;">
                                                </div> </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>






';


     }







$html.='
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

                                        <div class="   row">
                                                             <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Network Zone
</div> 
                                       </div>                           
                                            <div class="col-sm-3 form-group ">
                                                
                                          ';


                                           $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px;width:fit-content!important;border-radius:5px;background:'.$q->tag_back_color.';color: '.$q->tag_text_color.'" class=" text-center px-2 border-none  font-size-md  bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone_description.'</b></div>';

                                            //     if($q->network_zone=='Internal'){
                                            //                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px" class=" text-center border-none text-white font-size-md bg-secondary bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone_description.'</b></div>';
                                            // }elseif($q->network_zone=='Secure'){
                                            //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-info bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                            // }
                                            //     elseif($q->network_zone=='Greenzone'){
                                            //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-success bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                            //     }elseif($q->network_zone=='Guest'){
                                            //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center font-size-md border-none text-white bg-warning"  ><b>'.$q->network_zone.'</b></div>';
                                            //     }elseif($q->network_zone=='Semi-Trusted'){
                                            //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white border-none bubble-white-new bubble-text-sec  " style="background:#FFFF11;color: black"  ><b>'.$q->network_zone.'</b></div>';
                                            //     }elseif($q->network_zone=='Public DMZ' || $q->network_zone=='Public' || $q->network_zone=='Servers Public DMZ' ){
                                            //     $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec bg-danger"  ><b>'.$q->network_zone.'</b></div>
                                            //    ';
                                            //     }else{
                                            //       $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec  "  ><b>'.$q->network_zone.'</b></div>';
                                            //     }
                                            $html.='</div> 
                                            </div>
                                            <div class="row">
                                          
    


                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">vLAN ID
</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-first">
                                                   '.$q->vlanId.' 
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                      
                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">IP Address</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                       '.$q->ip_address.''.$q->mask.' 
                                                </div> 
                                     
                                            </div>
                                          <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Gateway Ip</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                       '.$q->subnet_ip.'   
                                                </div> 
                                     
                                            </div>
                                        

</div>
                                    </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-networking.png?cache=1" style="width: 100%;">
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
                                                        <label class="mb-0 bubble-text-sec font-12pt">'.$i->ip_address_value.'</label>
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


';
if($q->managed==1){

$html.='<div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
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
                                                
                                           
                                              
                                                        ';
                                                
                                                        $sla=DB::Table('sla')->Where('sla_description',$q->sla)->first();
                                                $html.='
                                                <div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;width:fit-content!important;border-radius:5px;min-height:29px;background:'.@$sla->tag_back_color.';color:'.@$sla->tag_text_color.'" class=" text-center font-size-md bubble-white-new border-none bubble-text-sec px-2"  ><b>'.$q->sla.'</b></div>';
                                              $html.='
                                            </div> 
                                            </div>
                                            <div class="row">

 <div class="col-sm-3 mb-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1" disabled="" '.($q->patched==1?'checked':'').'>
  <label class="btn btn-new w-100  py-1 font-11pt" for="patched"> Patched</label>
</div>
</div>

 <div class="col-sm-3   mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
       <input type="checkbox" class="custom-control-input" id="monitored" name="monitored" value="1" disabled="" '.($q->monitored==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="monitored">Monitored</label>
</div>
</div>

 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
        <input type="checkbox" class="custom-control-input" id="backup" name="backup" value="1" disabled="" '.($q->backup==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="backup">Backup</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
           <input type="checkbox" class="custom-control-input" id="antivirus" disabled="" name="antivirus" value="1"   '.($q->antivirus==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt" for="antivirus">Anti-Virus
</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="replicated"  disabled="" name="replicated" value="1" '.($q->replicated==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt" for="replicated">Replicated
</label>
</div>
</div>

 <div class="col-sm-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="disaster_recovery"  disabled="" name="disaster_recovery" value="1" '.($q->disaster_recovery==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="disaster_recovery">Vulnerability Scan</label>
</div>
</div>

 <div class="col-sm-3  ">


<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
                     <input type="checkbox" class="custom-control-input" id="syslog" disabled="" name="syslog" value="1" '.($q->syslog==1?'checked':'').' >
       <label class="btn btn-new w-100  py-1 font-11pt" for="syslog">SIEM</label>
</div>
</div>

 <div class="col-sm-3  ">

<div class="contract_type_button w-100 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
      <input type="checkbox" class="custom-control-input" id="smtp" name="smtp" value="1" disabled="" '.($q->smtp==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt" for="smtp">SMTP</label>
</div>
</div>

 
</div>
                                            
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-amaltitek.png?cache=1" style="width: 100%;">
                                                </div> 

                                    </div>
                                   
     <div class="col-sm-12 mt-4">
       
                                    </div>
                                   

                          </div>
                                      
                                               </div>      

                                             

';
}

$html.='
                                   

                                                     
                 
                 </div>
             </div>


         </div>';
   

                                                                                if(sizeof($contract_ssl_line_items)>0){
                                                                                     
  $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Supported Contracts</div>
                             
       <div class="block-content row new-block-content " id="commentBlock"><div class="col-lg-10">
 ';

                            foreach($contract_ssl_line_items as $l){
$rownumber=ceil($l->rownumber/10);
                                                                         $contract_end_date=date('Y-M-d',strtotime($l->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
$html.='
<div class="block block-rounded align-items-center  table-block-new mb-2 pb-0 " data="'.$l->id.'" style="cursor:pointer;">
                    
                 
                        <div class="block-content pt-1 pb-1 d-flex align-items-center pl-1 position-relative">
                                    

                                         <div class="mr-1   p-2  justify-content-center align-items-center  d-flex" style="width:15%">
                                            <img src="public/vendor_logos/'.$l->vendor_image.'"  class="rounded-circle  "  width="100%" style=" object-fit: cover;">
                                        </div>


                                     <div class="  " style="width:50%">
                                             <p class="font-12pt mb-0 text-truncate font-w600 c1">'.$l->firstname.'</p>

                                               <div class="d-flex">';
if($l->contract_type=='Hardware Support'){
                                                                    $html.='<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Hardware Support" data="'.$l->id.'">H</p>';
                                                                    
                        } elseif($l->contract_type=='Software Support'){
                                                                   $html.=' <p class="font-11pt mr-1   mb-0  c4-s  "  style="max-width:12%; " data-toggle="tooltip" data-title="Software Support" data="'.$l->id.'">S</p>';
                                                                }else{

                                                                    $html.='<p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Subscription" data="'.$l->id.'">C</p>';
                                                                }
                                                                         $html.='<p class="font-12pt mb-0 text-truncate   c4"  style="max-width:90%" data="'.$q->id.'">'.$l->contract_no.'</p></div>

                              
                                                    <p class="font-12pt mb-0 text-truncate c2">'.$l->contract_description.'</p> 
                                        </div>
                                        <div class=" text-right" style="width:25%;;">
                                                                            <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                               

';
                                                            
 $contract_end_date=date('Y-M-d',strtotime($l->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
                


                    if($l->contract_status=='Active'){

                                    if($abs_diff<=30){
                             $html.='<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold   text-dark"  >
                                                                                <span class=" ">Upcoming</span>
                                                                    </div> ';
                                    }else{
                                          $html.=' <div class=" bg-new-green ml-auto  badge-new  text-center font-weight-bold   text-white"  >
                                                                                 <span class=" ">Active</span>
                                                                    </div>  
                                                                    ';
                                                                 

                                }
                                }elseif($l->contract_status=='Inactive'){
                                   
                                                                      $html.='<div class=" bg-new-blue ml-auto  badge-new  font-weight-bold    text-center  font-w600 text-white"  >
                                                                                  <span class=" ">Renewed</span>
                                
                                                                    </div>  ';

                                }elseif($l->contract_status=='Expired/Ended'){
                                 
                                                                              $html.='   <div class=" bg-new-red ml-auto  font-weight-bold    badge-new  text-center  font-w600 text-white"  >
                                                                                  <span class=" ">Ended</span>
                                
                                                                    </div>';
                                }elseif($l->contract_status=='Ended'){
                                       $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-weight-bold   0 text-white"  >
                                                                                  <span class=" ">Ended</span>
                                                                    </div>';

                                }elseif($l->contract_status=='Expired'){ 
                                      $html.='<div class=" bg-new-red ml-auto  badge-new  text-center   text-white"  >
                                                                                  <span class=" ">Expired</span>
                                                                    </div>';
                                }

                                 

                                                           

                                                            $html.='    </div>';
 $ssl_line_items=DB::Table('contract_assets as ca')->select('a.hostname','a.AssetStatus')->where('ca.contract_id',$l->id)->join('assets as a','a.id','=','ca.hostname') ->where('ca.is_deleted',0)->orderBy('a.hostname','asc')->get();
                                                                            $cvm='<b class="HostActive text-white">Assigned Assets</b><br>';
                                                                            foreach($ssl_line_items as $v){
                                                                                if($v->AssetStatus!='1'){
                                                                                            $cvm.='<span class="HostInactive text-uppercase">'.$v->hostname.'</span><br>'; 
                                                                                            }
                                                                                            else{
                                                                                                        $cvm.='<span class="HostActive text-uppercase">'.$v->hostname.'</span><br>';    
                                                                                            }                                                                                    }
                                                                  
                                                                $contract_end_date=date('Y-M-d',strtotime($l->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
  
$cvm='<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >'.date('d-M-Y',strtotime($l->contract_start_date)).'-'.date('d-M-Y',strtotime($l->contract_end_date)).'</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>'.$abs_diff.' days remaining</i></small></p>';
     $html.="<div    style='position: absolute;width: 100%; bottom: 2px;right: 10px;display: flex;align-items: center;justify-content: end;'>
                                                                        

    <div class='ActionIcon'  data-src='public/img/calendar-grey-removebg-preview.png?cache=1' data-original-src='public/img/calendar-grey-removebg-preview.png'>
 <a href='javascript:;' class='toggle '' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
                             <img  src='public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
                        </a>
                                                                    </div>

 
 ";
     if(Auth::check()){    
                                            if(@Auth::user()->role!='read'){
                                                $html.='<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                         <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                                       
                                                                        <img src="public/img/dots.png?cache=1"   >
                                                                        </a>
                                         <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">
                                         ';
  
                  $html.='<a class="dropdown-item d-flex align-items-center px-0" href="contract?id='.$l->id.'&page='.ceil($l->rownumber/10).'" target="_blank">   <div style="  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View Support Contract</div></a>  
                   
                </div>
                                                                   </div>';
                                                                      } } 
                                                         $html.='         </div>
                                                         
                                        </div>    
                                </div>
                            </div>
                            ';

  
}

$html.='</div>
                                <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                  <img src="public/img/signing-contract-icon-business-concept-flat-vector-6269121-removebg-preview.png?cache=1" style="width: 100%;">
                                                </div> 

                                    </div>
</div> </div>';
   }  

                          
                                                                         
                if(sizeof($ssl_line_items_2)>0){
                      $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">SSL Certificates</div>
                             
       <div class="block-content row new-block-content " id="commentBlock"><div class="col-lg-10">
 ';
 
                foreach ($ssl_line_items_2 as  $ssl_row) {
                                                          
$html.=' <div class="block block-rounded   table-block-new mb-2 pb-0  -   " data="'.$ssl_row->id.'" style="cursor:pointer;">
                    
                         <div class="block-content align-items-center pt-1 pb-1 d-flex  pl-1 position-relative">
                                    
                                                                                 <div class="mr-1   p-2  justify-content-center align-items-center  d-flex" style="width:15%">';
                                            
                                             if($ssl_row->cert_type=='internal'){
                                                    $html.='<img src="public/client_logos/'.$ssl_row->logo.'" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
                                                     }else{
         $html.='<img src="public/vendor_logos/'.$ssl_row->vendor_image.'" class="rounded-circle"  width="100%" style=" object-fit: cover;">';
                                            }
                                   
                                        $html.='</div>
                                        <div class="  " style="width:45%">
                            <p class="font-12pt mb-0 text-truncate c1"><b>'.$ssl_row->firstname.'
                                                         </b></p>
                                             
                                                      <div class="d-flex">';
                                                                  if($ssl_row->cert_type=='internal'){
                                                                    $html.='<p class="font-11pt mr-1   mb-0  c4-p  "  style="max-width:12%; " data-toggle="tooltip" data-title="Internal Certificate" data="'.$ssl_row->id.'">I</p>';
                                                                  }else{

                                                                  $html.='   <p class="font-11pt mr-1   mb-0   c4-v  "  style="max-width:12%; " data-toggle="tooltip" data-title="Public Certificate" data="'.$ssl_row->id.'">P</p>';
                                                                    }
                                                                         $html.='<p class="font-12pt mb-0 text-truncate   c4"  style="max-width:88%" data="'.$ssl_row->id.'">'.$ssl_row->cert_name.'</p>
                                                                       </div>

                              

                                          <p class="font-12pt mb-0 text-truncate  c2">'.$ssl_row->description.'</p>
                                                     
                                        </div>
                                        <div class=" text-right" style="width:25%;;">
                                                                            <div style="position: absolute;width: 100%; top: 10px;right: 10px;">';
                                                                $cert_edate=date('Y-M-d',strtotime($ssl_row->cert_edate)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($ssl_row->cert_edate);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); 
 
                  
 


                    if($ssl_row->cert_status=='Active'){

                                    if($abs_diff<=30){
                             $html.='<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-weight-bold text-dark"  >
                                                                               Upcoming</span>
                                                                    </div> ';
                                    }else{
                                     $html.='<div class=" bg-new-green ml-auto  badge-new  text-center  font-weight-bold   text-white"  >
                                                                                 Active</span>
                                                                    </div>  ';
                                                                 

                                }
                                }elseif($ssl_row->cert_status=='Inactive'){
                                   
                                                                      $html.='<div class=" bg-new-blue ml-auto  badge-new   font-weight-bold  text-center  font-w600 text-white"  >
 <span class=" ">Renewed</span>
                                                                    </div>  ';

                                }elseif($ssl_row->cert_status=='Expired/Ended'){
                                 
                                                                                 $html.='<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                               Ended</span>
                                
                                                                    </div>';
                                } elseif($ssl_row->cert_status=='Ended'){
                                       $html.='<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                               Ended</span>
                                                                    </div>';

                                }elseif($ssl_row->cert_status=='Expired'){
                                      $html.='<div class=" bg-new-red ml-auto  badge-new  text-center   font-weight-bold  text-white"  >
                                                                               <span class=" ">Expired</span>
                                                                    </div>';
                                }

                                 

                                                            
                                                            $html.='    </div>';
   $san_items=DB::Table('ssl_san')->select( 'san')->where('ssl_id',$ssl_row->id)  ->groupBy('san') ->orderBy('san','asc')->get();
                                                                            $cvm1='<b class="HostActive text-white ">SANs</b><br>';
                                                                            foreach($san_items as $li){
                                                                                 
  $cvm1.='<span class="SSLActive">'.$li->san.'</span><br>';    
                                                                                }
                                                                 
                                                                 $html.='     <div  class="" style="position: absolute;width: 100%; bottom:2px;right: 10px;display: flex;align-items: center;justify-content: end;">
                                                                            
                                                                      <div  class="ActionIcon" style="margin-left:2px;" data-src="public/img/icon-san-grey-darker.png?cache=1" data-original-src="public/img/icon-san-grey-darker.png?cache=1">
                                                                ';
                                                                       $html.="<a href='javascript:;' class='toggle' data-toggle='tooltip' data-trigger='hover' data-placement='top'  data-html='true'   data-original-title='$cvm1'>";
                                                                      
                                                                      $html.='<img  src="public/img/icon-san-grey-darker.png?cache=1"  height="24px">
                                                                        </a>
                                                                    </div>
                                                                        <div class="ActionIcon"  data-src="public/img/calendar-grey-removebg-preview.png?cache=1" data-original-src="public/img/calendar-grey-removebg-preview.png?cache=1">';

        $cvm='<p class="HostActive text-white  my-0">Validity Range</p><p class="HostActive my-n1 text-orange"  >'.date('d-M-Y',strtotime($ssl_row->cert_rdate)).'-'.date('d-M-Y',strtotime($ssl_row->cert_edate)).'</p><p class="font-10pt mb-0 text-grey text-truncate mt-0"> <small><i>'.$abs_diff.' days remaining</i></small></p>'; 

 $html.="
 <a href='javascript:;' class='toggle ' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title='' >
                             <img  src='public/img/calendar-grey-removebg-preview.png' width='24px'  class='' >
                        </a>
                                                                    </div>

 
 ";
                                                                
     if(Auth::check()){    
                                            if(@Auth::user()->role!='read'){
                                                                     $html.='<div class="ActionIcon px-0 ml-2    " style="border-radius: 5px"  >
                                                                         <a  class="dropdown-toggle"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                                                       
                                                                        <img src="public/img/dots.png?cache=1"   >
                                                                        </a>
                                         <div class="dropdown-menu" aria-labelledby="dropdown-dropright-primary">';
          
                    

$html.='<a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="ssl-certificate?id='.$ssl_row->id.'&page='.$ssl_row->rownumber.'">   <div style="  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > View SSL Certificate</div></a>  
                </div>
                                                                   </div>';
                       } } 
                                                         $html.='         </div>
                                   </div>    
                                </div>
                            </div>
                            ';

  
}

$html.='</div>
                                <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                  <img src="public/img/internal-ssl.png?cache=1" style="width: 100%;">
                                                </div> 

                                    </div>
</div> </div>';
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

 
public function getVirtualContent(Request $request){
    $id=$request->id;
$html='';

     $q=DB::table('assets as a')->select('a.*','s.site_name','d.domain_name','c.firstname','o.operating_system_name','o.operating_system_image','m.vendor_name' ,'s.address','s.city','s.country','s.phone','s.zip_code','s.province','at.asset_icon','at.asset_type_description','at.asset_type_description as asset_type_name','n.vlan_id as vlanId','n.subnet_ip','n.mask','usr.firstname as created_firstname','usr.lastname as created_lastname','upd.firstname as updated_firstname','upd.lastname as updated_lastname','c.logo','m.vendor_image')->join('clients as c','c.id','=','a.client_id')->join('sites as s','s.id','=','a.site_id')->leftjoin('asset_type as at','at.asset_type_id','=','a.asset_type_id')->leftjoin('operating_systems as o','o.id','=','a.os')->leftjoin('domains as d','d.id','=','a.domain')->leftjoin('vendors as m','m.id','=','a.manufacturer')  ->leftjoin('network as n','a.vlan_id','=','n.id')->leftjoin('users as usr','usr.id','=','a.created_by')->leftjoin('users as upd','upd.id','=','a.updated_by')->where('a.id',$id)->first();
   
                   

                   if($q->AssetStatus==1){
                           $html.='<div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                                <div class="d-flex">
                                <img src="public/img/header-asset-white.png?cache=1" width="40px">
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
                                <img src="public/img/header-asset-white.png?cache=1" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text " style="line-height:27px">Asset Decomissioned</h4>';
 $renewed_qry=DB::Table('users')->Where('id',$q->InactiveBy)->first(); 
                         

  
                                $html.='<p class="mb-0  header-new-subtext" style="line-height:17px">On '.date('Y-M-d',strtotime($q->InactiveDate)).' by '.@$renewed_qry->firstname.' '.@$renewed_qry->lastname.'</p>
                                    </div>
                                </div>';
                                   
                        }


                                    $html.='<div class="new-header-icon-div d-flex align-items-center  no-print">';
                                                
                                    if(Auth::user()->role!='read') {
                                                      
                                                       
                                                      
                                    if($q->AssetStatus==1){
                                                      $html.='<span  > 
                                             <a href="javascript:;" class="btnEnd"  data="'.$q->AssetStatus.'" data-id="'.$q->id.'" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Decomission" class=" "><img src="public/img/icon-header-white-end-decom.png?cache=1" width="22px"></a>
                                         </span>';
                                                            }else{
                                                      $html.='    <span  > 
                                             <a href="javascript:;" class="btnEnd"  data="'.$q->AssetStatus.'" data-id="'.$q->id.'" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reactivate" class=" "><img src="public/img/icon-header-white-renew.png?cache=1" width="20px"></a>
                                         </span>';
                                                    }
                                          
                                         }
                                        
$html.=' <a  target="_blank" href="pdf-asset?id='.$q->id.'"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="PDF" class=" ">
                                                <img src="public/img/action-white-pdf.png?cache=1" width="24px"  style="padding:5px 7px">
                                            </a>
     <a href="javascript:;" onclick="window.print()" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';
                                             

                                                  if(Auth::user()->role!='read') {
                                                        
                                              $html.='<a   href="edit-assets?id='.$q->id.'" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                                   
                                            <a data-toggle="tooltip" data-trigger="hover" data="'.$q->id.'" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
                                            }

                                    $html.='</div></div>
                            </div>
                        </div>

                        <div class="block new-block position-relative mt-3" >
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
  <input type="checkbox" class="custom-control-input " id="disaster_recovery1" name="disaster_recovery" disabled="" value="1" '.($q->disaster_recovery==1?'checked':'').'>
  <label class="btn btn-new w-100 py-1 font-11pt" for="disaster_recovery1">D/R Plan</label>
</div>
</div>
 

 


 <div class="col-sm-3  ">

<div class="contract_type_button  w-100 mr-4  ">
          <input type="checkbox" class="custom-control-input" id="clustered" name="clustered"  disabled="" value="1"  '.($q->clustered==1?'checked':'').'>
  <label class="btn btn-new w-100  py-1 font-11pt" for="clustered"> Clustered</label>
</div>
</div>

 <div class="col-sm-3 text-center">

<div class="contract_type_button  w-100 mr-4  ">
     <input type="checkbox" class="custom-control-input" id="internet_facing" name="internet_facing" value="1" disabled="" '.($q->internet_facing==1?'checked':'').'>

       <label class="btn btn-new w-100  py-1 font-11pt" for="internet_facing"> Internet Facing</label>
</div>
</div>




 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.($q->load_balancing==1?'checked':'').'>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> Load Balanced</label>
</div>
</div>


 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.($q->load_balancing==1?'checked':'').'>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> SSL Certificate</label>
</div>
</div>


 <div class="col-sm-3  text-right">

<div class="contract_type_button  w-100 mr-4 ">
              <input type="checkbox" class="custom-control-input" id="load_balancing" name="load_balancing" value="1" disabled="" '.($q->load_balancing==1?'checked':'').'>
  <label class="btn btn-new w-100 py-1 font-11pt" for="load_balancing"> Supprted/Unsupported</label>
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
 <div class="top-right-div top-right-div-blue text-capitalize">Virtual Hardware</div>
                            
                            <div class="col-sm-12 m-
                            " >
                                     
                        <input type="hidden" name="attachment_array" id="attachment_array" >
                                <div class="row">
 
                                    <div class="col-sm-10">
                                    
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">vCPUs</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                          '.$q->vcpu.'  
                                                  
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
                                                <div class="bubble-white-new bubble-text-sec" style="padding:10px">
 

                                                      <img src="public/img/static-vm.png?cache=1" style="width: 100%;">
                                                </div> </div>

                                      
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
                                           <div class="bubble-new">Network Zone
</div> 
                                       </div>                           
                                            <div class="col-sm-3 form-group ">
                                                
                                          ';
                                                if($q->network_zone=='Internal'){
                                                           $html.='<div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px" class=" text-center border-none text-white font-size-md bg-secondary bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                            }elseif($q->network_zone=='Secure'){
                                                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-info bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                            }
                                                elseif($q->network_zone=='Greenzone'){
                                                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center border-none font-size-md text-white bg-success bubble-white-new bubble-text-sec"  ><b>'.$q->network_zone.'</b></div>';
                                                }elseif($q->network_zone=='Guest'){
                                                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class="text-center font-size-md border-none text-white bg-warning"  ><b>'.$q->network_zone.'</b></div>';
                                                }elseif($q->network_zone=='Semi-Trusted'){
                                                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white border-none bubble-white-new bubble-text-sec  " style="background:#FFFF11;color: black"  ><b>'.$q->network_zone.'</b></div>';
                                                }elseif($q->network_zone=='Public DMZ' || $q->network_zone=='Public' || $q->network_zone=='Servers Public DMZ' ){
                                                $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec bg-danger"  ><b>'.$q->network_zone.'</b></div>
                                               ';
                                                }else{
                                               $html.='<div  style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px"  class=" text-center font-size-md text-white bubble-white-new border-none bubble-text-sec  "  ><b>'.$q->network_zone.'</b></div>';
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
                                                   '.$q->vlanId.' 
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                      
                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">IP Address</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                       '.$q->ip_address.''.$q->mask.'  
                                                </div> 
                                     
                                            </div>

                                         <div class="col-sm-4 form-group ">
                                           <div class="bubble-new">Gateway Ip</div> 
                                       </div>
                                            <div class="col-sm-8 form-group ">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           
                                                          '.$q->subnet_ip.'
                                                </div> 
                                     
                                            </div>
                                       

</div>
                                    </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-networking.png?cache=1" style="width: 100%;">
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
                                                        <label class="mb-0 bubble-text-sec font-12pt">'.$i->ip_address_value.'</label>
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
                                                
                                                        ';
                                                
                                                        $sla=DB::Table('sla')->Where('sla_description',$q->sla)->first();
                                                $html.='
                                                <div style="box-shadow:none;padding-bottom: 0px;padding-top:0px;min-height:29px;background:{{@$sla->tag_back_color}};color:{{@$sla->tag_text_color}}" class=" text-center font-size-md bubble-white-new border-none bubble-text-sec"  ><b>'.$q->sla.'</b></div>';
                                              $html.='
                                            </div> 
                                            </div>
                                            <div class="row">

 <div class="col-sm-3 mb-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="patched" name="patched" value="1" disabled="" '.($q->patched==1?'checked':'').'>
  <label class="btn btn-new w-100  py-1 font-11pt " for="patched"> Patched</label>
</div>
</div>

 <div class="col-sm-3   mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
       <input type="checkbox" class="custom-control-input" id="monitored" name="monitored" value="1" disabled="" '.($q->monitored==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt " for="monitored">Monitored</label>
</div>
</div>

 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
        <input type="checkbox" class="custom-control-input" id="backup" name="backup" value="1" disabled="" '.($q->backup==1?'checked':'').'>
       <label class="btn btn-new w-100   py-1 font-11pt" for="backup">Backup</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
           <input type="checkbox" class="custom-control-input" id="antivirus" disabled="" name="antivirus" value="1"   '.($q->antivirus==1?'checked':'').'>
       <label class="btn btn-new w-100   py-1 font-11pt" for="antivirus">Anti-Virus
</label>
</div>
</div>


 <div class="col-sm-3  mb-3">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""  >
          <input type="checkbox" class="custom-control-input" id="replicated"  disabled="" name="replicated" value="1" '.($q->replicated==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt" for="replicated">Replicated
</label>
</div>
</div>

 <div class="col-sm-3 ">

<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
          <input type="checkbox" class="custom-control-input" id="disaster_recovery"  disabled="" name="disaster_recovery" value="1" '.($q->disaster_recovery==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt" for="disaster_recovery">Vulnerability Scan</label>
</div>
</div>

 <div class="col-sm-3  ">


<div class="contract_type_button w-100 mr-4 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
                     <input type="checkbox" class="custom-control-input" id="syslog" disabled="" name="syslog" value="1" '.($q->syslog==1?'checked':'').' >
       <label class="btn btn-new w-100  py-1 font-11pt" for="syslog">SIEM</label>
</div>
</div>

 <div class="col-sm-3  ">

<div class="contract_type_button w-100 px-1 js-tooltip-enabled" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" >
      <input type="checkbox" class="custom-control-input" id="smtp" name="smtp" value="1" disabled="" '.($q->smtp==1?'checked':'').'>
       <label class="btn btn-new w-100  py-1 font-11pt" for="smtp">SMTP</label>
</div>
</div>

 
</div>
                                            
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                                <!--  $q->vendor_logos  -->
                                                      <img src="public/img/static-amaltitek.png?cache=1" style="width: 100%;">
                                                </div> 

                                    </div>
                                   
     <div class="col-sm-12 mt-4">
       
                                    </div>
                                   

                          </div>
                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>


         </div>';
   
 $ssl_line_items=DB::Table('contract_assets as ca')->selectRaw('a.contract_no,c.firstname,a.contract_status,a.contract_start_date,a.contract_end_date,v.vendor_image,a.contract_description,a.contract_type,a.id,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM contracts  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.contract_id order by id desc limit 1 ) as rownumber ')

 ->where('ca.hostname',$q->id)->join('contracts  as a','a.id','=','ca.contract_id')->join('clients as c','c.id','=','a.client_id')->join('vendors as v','v.id','=','a.vendor_id')->groupBy('a.id')->where('a.is_deleted',0)->orderBy('a.contract_no','asc')->get();
                                                                         
                                                                                if(sizeof($ssl_line_items)>0){
                                                                                     
  $html.='<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Supported Contracts</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">
 ';

                            foreach($ssl_line_items as $l){

                                                                         $contract_end_date=date('Y-M-d',strtotime($l->contract_end_date)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->contract_end_date);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
$html.='<div class="block block-rounded   table-block-new mb-2 pb-0  -  ">
                    
                        <div class="block-content d-flex py-3 mt-0 position-relative">
                                        <div class="mr-3   align-items-center  d-flex" style="width:100px">
                                            <img src="public/vendor_logos/'.$l->vendor_image.'" class="rounded-circle" width="75px" style="max-width:100px;object-fit: cover;">
                                        </div>
                                        <div class="  " style="width:80%">
                                                  <p class="font-10pt mb-0 text-truncate c1">'.$l->contract_type.'</p>
                                                                <p class="font-10pt mb-0 text-truncate  c4" >'.$l->contract_no.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c2">'.$l->contract_description.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c3"><b>'.$l->firstname.'</b></p>
                                        </div>
                                        <div class=" text-right" style="width:35%;;">
                                   <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                                        
                                                                        <div class="d-inline-flex justify-content-end align-items-center">
                                                                            <span class=" mr-2 font-10pt"><b>'.date('Y-M-d',strtotime($l->contract_end_date)).'</b></span>';

                  if($l->contract_status=='Active'){

                                    if($abs_diff<=30){
                             $html.='<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-w600 text-dark"  >
                                                                                 <img src="public/img/status-upcoming-.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Upcoming</span>
                                                                    </div> ';
                                    }else{
                                          $html.=' <div class=" bg-new-green ml-auto  badge-new  text-center font-w600   text-white"  >
                                                                                 <img src="public/img/status-white-active.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Active</span>
                                                                    </div>  
                                                  ';               

                               }
                                }elseif($l->contract_status=='Inactive'){
                                   
                                                                      $html.='<div class=" bg-new-blue ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-renewed.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Renewed</span>
                                
                                                                    </div>  ';

                                }elseif($l->contract_status=='Expired/Ended'){
                                 
                                            $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/icon-header-white-end-decom.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                
                                                                    </div>';
                                }elseif($l->contract_status=='Ended'){
                                       $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/icon-header-white-end-decom.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                                                    </div>';
                                                                }
                                elseif($l->contract_status=='Expired'){
                                      $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-expired.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Expired</span>
                                                                    </div>';
                                }

                                $html.='                            </div>

                                                                    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>'.$abs_diff.' days remaining</i></small></p>

                                                                    </div>
                                                                   
                                                                </div>
                                                                 
 <div style="position: absolute;width: 100%; bottom: 5px;right: 10px;">
                                       <a href="'.url('print-contract').'?id='.$l->id.'&page='.ceil($l->rownumber/10).'" target="_blank" class="toggle led" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Open" data-html="true" data-original-title="Open">
          

          <img src="public/img/icon-eye-grey.png?cache=1" class="mr-2 " width="23px" height="23px">
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
 
  $ssl_line_items=DB::Table('ssl_host as ca')->selectRaw(' a.cert_name , a.cert_status , a.cert_edate , a.cert_type , a.id , c.logo , v.vendor_image , a.description , c.firstname ,( SELECT row_number FROM (
    SELECT   id,@curRow := @curRow + 1 AS row_number 
    FROM (
        SELECT * FROM ssl_certificate  where is_deleted=0  
        ORDER BY id desc
    ) l 
    JOIN (
        SELECT @curRow := 0 
    ) r order by id desc
) t where t.id=ca.ssl_id order by id desc limit 1 ) as rownumber ')->where('ca.hostname',$q->id)->join('ssl_certificate  as a','a.id','=','ca.ssl_id')->leftjoin('clients as c','c.id','=','a.client_id')->leftjoin('vendors as v','v.id','=','a.cert_issuer') ->where('a.is_deleted',0)->orderBy('a.cert_name','asc')->get();
                                                                         
                                                                                if(sizeof($ssl_line_items)>0){
                                                                                     
$html.='  <div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">SSL Certificates</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">
';
 
                                                                            foreach($ssl_line_items as $l){

                                                                         $contract_end_date=date('Y-M-d',strtotime($l->cert_edate)); 
             $today=date('Y-m-d');
             $earlier = new DateTime($l->cert_edate);
$later = new DateTime($today);

$abs_diff = $later->diff($earlier)->format("%a"); //3
 
$html.='<div class="block block-rounded   table-block-new mb-2 pb-0  -   ">
                    
                        <div class="block-content d-flex py-3 mt-0 position-relative">
                                        <div class="mr-3   align-items-center  d-flex" style="width:100px">';
                
  if($l->cert_type=='public'){
                                            $html.='<img src="public/vendor_logos/'.$l->vendor_image.'" class="rounded-circle" width="75px" style="max-width:100px;object-fit: cover;">';
                                        }
                                        else{
                                         $html.='<img src="public/client_logos/'.$l->logo.'" class="rounded-circle" width="75px" style="max-width:100px;object-fit: cover;">';   
                                        }
                                        $html.='
                                        </div>
                                        <div class="  " style="width:80%">
                                                     <p class="font-10pt mb-0 text-truncate c1">';
                                                    if($l->cert_type=='internal'){
                                                          $html.='Internal SSL Certificate';  
                                                        }else{
                                                        $html.='Public SSL Certificate';
                                                        }

                                                    $html.=' </p>
                                                               <p class="font-10pt mb-0 text-truncate  c4" >'.$l->cert_name.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c2">'.$l->description.'</p>
                                                    <p class="font-10pt mb-0 text-truncate c3">'.$l->firstname.'<b>
                                                        ';
                                                         
                                                        $html.='SSl Certificate

                                                    </b></p>
                                        </div>
                                        <div class=" text-right" style="width:35%;;">
                                   <div style="position: absolute;width: 100%; top: 10px;right: 10px;">
                                                                        
                                                                        <div class="d-inline-flex justify-content-end align-items-center">
                                                                            <span class=" mr-2 font-10pt"><b>'.date('Y-M-d',strtotime($l->cert_edate)).'</b></span>';

                  if($l->cert_status=='Active'){

                                    if($abs_diff<=30){
                             $html.='<div class=" bg-new-yellow ml-auto  badge-new  text-center  font-w600 text-dark"  >
                                                                                 <img src="public/img/status-upcoming-.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Upcoming</span>
                                                                    </div> ';
                                    }else{
                                          $html.=' <div class=" bg-new-green ml-auto  badge-new  text-center font-w600   text-white"  >
                                                                                 <img src="public/img/status-white-active.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Active</span>
                                                                    </div>  ';
                                                                 

                                }
                                }elseif($l->cert_status=='Inactive'){
                                   
                                                                      $html.='<div class=" bg-new-blue ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-renewed.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Renewed</span>
 
                                                                    </div>  ';

                                }elseif($l->cert_status=='Expired/Ended'){
                                 
                                                                              $html.='   <div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/icon-header-white-end-decom.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                 
                                                                    </div>';
                                }elseif($l->cert_status=='Ended'){
                                       $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/icon-header-white-end-decom.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Ended</span>
                                                                    </div>';
                                }elseif($l->cert_status=='Expired'){
                                      $html.='<div class=" bg-new-red ml-auto  badge-new  text-center  font-w600 text-white"  >
                                                                                 <img src="public/img/status-white-expired.png?cache=1" class="mr-2" width="15px"><span class="ml-n1">Expired</span>
                                                                    </div>';
                                

                                }                            

                                $html.='</div>

                                                                    <div >
                                                                                   <p class="font-10pt mb-0 text-truncate c2"> <small><i>'.$abs_diff.' days remaining</i></small></p>

                                                                    </div>
                                                                   
                                                                </div>
                                                                 
 <div style="position: absolute;width: 100%; bottom: 5px;right: 10px;">
                                       <a href="'.url('print-ssl-certificate').'?id='.$l->id.'&page='.ceil($l->rownumber/10).'" target="_blank" class="toggle led" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Open" data-html="true" data-original-title="Open">
          

          <img src="public/img/icon-eye-grey.png?cache=1" class="mr-2 " width="23px" height="23px">
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