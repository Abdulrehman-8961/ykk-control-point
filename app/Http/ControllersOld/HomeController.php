<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
 use App\Imports\UsersImport;
use Excel;
use PDF;
use Cookie;
use Validator;
use Hash;
use Session;
use URL;
class HomeController extends Controller
{
    //
public function __construct(){
     
     }


 public function Transfer(Request $request){
           // $qry=DB::table('ssl_certificate')->where('comments','!=','')->get();
             
           //  foreach($qry as $q){
           //       DB::Table('ssl_comments')->insert(['comment'=>strip_tags(str_replace('&nbsp;',' ',$q->comments)),'name'=>'Stephen Tzintzis','asset_id'=>$q->id,'added_by'=>5,'date'=>date('Y-m-d')]);
           //  }
        }
  
   public function PrivacyPolicy(Request $request){
            return view('PrivacyPolicy');

        }

   public function Error(Request $request){
            return view('error',['message'=>'You dont have access to portal']);

        }
  public function InvoiceAuth(Request $request)
    {   $client_id = '1000.E5CROWNS7V3WWRIU6T06L8SF4PH8PL';
        $client_secret = 'b9c3cc4c40e55e59b047f47c235b0fb493b3390803';
        


        if(isset($request->invoice_number)){
            $invoice_no=$request->invoice_number;
                    $uri = URL::TO("GetZohoInvoices");
        $scope =  'ZohoInvoice.contacts.Create';
 
        $accestype = 'offline';

        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
        [
        'client_id' => $client_id,
        'redirect_uri' => $uri,
         'scope' =>  'ZohoBooks.fullaccess.all',
       
        'response_type' => 'code',
        ]);

        $minutes=120;
    Session::put('invoice_no',$invoice_no);

        }
        if(isset($request->estimate_number)){
            $estimate_number=$request->estimate_number;
                    $uri = URL::TO("GetZohoEstimes");
    
        $accestype = 'offline';

        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
        [
        'client_id' => $client_id,
        'redirect_uri' => $uri,
         'scope' =>  'ZohoBooks.fullaccess.all',
       
        'response_type' => 'code',
        ]);

        $minutes=120;
        Session::put('estimate_number',$estimate_number);

        }
         if(isset($request->sales_number)){
            $sales_number=$request->sales_number;
 
                    $uri = URL::TO("GetZohoSalesOrders");
    
        $accestype = 'offline';

        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
        [
        'client_id' => $client_id,
        'redirect_uri' => $uri,
         'scope' =>  'ZohoBooks.fullaccess.all',
       
        'response_type' => 'code',
        ]);

        $minutes=120;
        Session::put('sales_number',$sales_number);

        }
          
        if(isset($request->po_number)){
            $po_number=$request->po_number;
                           $uri = URL::TO("GetZohoPOs");
    
        $accestype = 'offline';

        $redirectTo = 'https://accounts.zoho.com/oauth/v2/auth' . '?' . http_build_query(
        [
        'client_id' => $client_id,
        'redirect_uri' => $uri,
         'scope' =>  'ZohoBooks.fullaccess.all',
       
        'response_type' => 'code',
        ]);

        $minutes=120;
        Session::put('po_number',$po_number);


        }
          
        

 
        return redirect($redirectTo);
    }
    
    
   public function GetZohoInvoices(Request $request){

  
      $invoice_number=Session::get('invoice_no');
   
   
         
        $client_id = '1000.E5CROWNS7V3WWRIU6T06L8SF4PH8PL';
        $client_secret = 'b9c3cc4c40e55e59b047f47c235b0fb493b3390803';
 
    $code=$request->code;
 
  
        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code='.$code.'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.URL::TO('GetZohoInvoices').'&grant_type=authorization_code';

        $tokenData = [

        ];
      

        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_VERBOSE, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
        curl_setopt($curl, CURLOPT_POST, TRUE);//Regular post  
        curl_setopt($curl, CURLOPT_URL, $tokenUrl);     
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenData));

        $tResult = curl_exec($curl);
        curl_close($curl);
        $tokenResult = json_decode($tResult);
           
            if(!isset($tokenResult->access_token)){
                
                 Session::put('invoice_no');
                    return redirect('GetZohoInvoicesAuth?invoice_number='.$invoice_number);
            }
         
        try{ 
            $curl = curl_init("https://books.zoho.com/api/v3/invoices?organization_id=669692720&invoice_number=$invoice_number");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);
                
            $contactResponse = json_decode($cResponse);
     
            return redirect($contactResponse->invoices[0]->invoice_url);
        }
        catch (ErrorException $e){
                return redirect('GetZohoInvoicesAuth?invoice_number='.$invoice_number);
        }
   }
   


    public function GetZohoEstimes(Request $request){

  
      $estimate_number=Session::get('estimate_number');
   
   
         
        $client_id = '1000.E5CROWNS7V3WWRIU6T06L8SF4PH8PL';
        $client_secret = 'b9c3cc4c40e55e59b047f47c235b0fb493b3390803';
 
    $code=$request->code;
 
  
        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code='.$code.'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.URL::TO('GetZohoEstimes').'&grant_type=authorization_code';

        $tokenData = [

        ];
      

        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_VERBOSE, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
        curl_setopt($curl, CURLOPT_POST, TRUE);//Regular post  
        curl_setopt($curl, CURLOPT_URL, $tokenUrl);     
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenData));

        $tResult = curl_exec($curl);
        curl_close($curl);
        $tokenResult = json_decode($tResult);
           
            if(!isset($tokenResult->access_token)){
                
                 Session::put('estimate_number');
                    return redirect('GetZohoInvoicesAuth?estimate_number='.$estimate_number);
            }
         
        try{ 
            $curl = curl_init("https://books.zoho.com/api/v3/estimates?organization_id=669692720&estimate_number=$estimate_number");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);
                
            $contactResponse = json_decode($cResponse);

      $estimate_id=$contactResponse->estimates[0]->estimate_id;
 
        $curl = curl_init("https://books.zoho.com/api/v3/estimates/$estimate_id?organization_id=669692720&print");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);
                
            $contactResponse = json_decode($cResponse);
         
      
            return redirect($contactResponse->estimate->estimate_url);
        }
        catch (ErrorException $e){
                return redirect('GetZohoInvoicesAuth?estimate_number='.$estimate_number);
        }
   }



    public function GetZohoSalesOrders(Request $request){

  
      $sales_number=Session::get('sales_number');
   
   
         
        $client_id = '1000.E5CROWNS7V3WWRIU6T06L8SF4PH8PL';
        $client_secret = 'b9c3cc4c40e55e59b047f47c235b0fb493b3390803';
 
    $code=$request->code;
 
  
        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code='.$code.'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.URL::TO('GetZohoSalesOrders').'&grant_type=authorization_code';

        $tokenData = [

        ];
      

        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_VERBOSE, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
        curl_setopt($curl, CURLOPT_POST, TRUE);//Regular post  
        curl_setopt($curl, CURLOPT_URL, $tokenUrl);     
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenData));

        $tResult = curl_exec($curl);
        curl_close($curl);
        $tokenResult = json_decode($tResult);
           
            if(!isset($tokenResult->access_token)){
                
                 Session::put('sales_number');
                    return redirect('GetZohoInvoicesAuth?sales_number='.$sales_number);
            }
         
        try{ 
            $curl = curl_init("https://books.zoho.com/api/v3/salesorders?organization_id=669692720&salesorder_number=$sales_number");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);
                
            $contactResponse = json_decode($cResponse);
         $sales_id=$contactResponse->salesorders[0]->salesorder_id;
 
        $curl = curl_init("https://books.zoho.com/api/v3/salesorders/$sales_id?organization_id=669692720&accept=pdf");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);
                
         header('Content-type: application/pdf');
$result = curl_exec($curl);
curl_close($curl);
echo $result;
            // return redirect($contactResponse->salesorder->salesorder_url);
          
        }
        catch (ErrorException $e){
                return redirect('GetZohoInvoicesAuth?sales_number='.$sales_number);
        }
   }



    public function GetZohoPOs(Request $request){

  
      $po_number=Session::get('po_number');
   
   
         
        $client_id = '1000.E5CROWNS7V3WWRIU6T06L8SF4PH8PL';
        $client_secret = 'b9c3cc4c40e55e59b047f47c235b0fb493b3390803';
 
    $code=$request->code;
 
  
        $tokenUrl = 'https://accounts.zoho.com/oauth/v2/token?code='.$code.'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.URL::TO('GetZohoPOs').'&grant_type=authorization_code';

        $tokenData = [

        ];
      

        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_VERBOSE, 0);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);     
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
        curl_setopt($curl, CURLOPT_POST, TRUE);//Regular post  
        curl_setopt($curl, CURLOPT_URL, $tokenUrl);     
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);     
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenData));

        $tResult = curl_exec($curl);
        curl_close($curl);
        $tokenResult = json_decode($tResult);
           
            if(!isset($tokenResult->access_token)){
                
                 Session::put('po_number');
                    return redirect('GetZohoInvoicesAuth?po_number='.$po_number);
            }
         
        try{ 
            $curl = curl_init("https://books.zoho.com/api/v3/purchaseorders?organization_id=669692720&purchaseorder_number=$po_number");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
            //Execute cUrl session
            $cResponse = curl_exec($curl);
            curl_close($curl);
                
            $contactResponse = json_decode($cResponse);
       $purchaseorder_id=$contactResponse->purchaseorders[0]->purchaseorder_id;
 
        $curl = curl_init("https://books.zoho.com/api/v3/purchaseorders/$purchaseorder_id?organization_id=669692720&accept=pdf");
         
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);   
               curl_setopt($curl, CURLOPT_VERBOSE, 0);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);     
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); 
               curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Zoho-oauthtoken ".$tokenResult->access_token,
              
            ) );
           
       
header('Content-type: application/pdf');
$result = curl_exec($curl);
curl_close($curl);
echo $result;
      
             
        }
        catch (ErrorException $e){
                return redirect('GetZohoInvoicesAuth?po_number='.$po_number);
        }
   }
   public function CheckContractStatus(Request $request){
                         
                         
                       
                 $settings=DB::Table('notification_settings')->first();
    

                        $qry=DB::Table('contracts as c')->select('c.*','cl.renewal_notification_email','cl.renewal_notification','v.vendor_name')->leftjoin('clients as cl','cl.id','=','c.client_id')->leftjoin('vendors as v','v.id','=','c.vendor_id') ->where('c.contract_status','Expired')->where('c.is_deleted',0)->get();
                         
                        foreach($qry as $q){
 // Declare two dates
  $start_date = strtotime($q->contract_end_date);
  $end_date = strtotime(date("Y-m-d"));
 
  // Get the difference and divide into
  // total no. seconds 60/60/24 to get
  // number of days
 
      $days= ($end_date - $start_date)/60/60/24;
  
      if($days>=30){
                     
                                DB::Table('contracts')->where('id',$q->id)->update(['contract_status'=>'Expired/Ended']);                            
               }
           }


                 $qry=DB::Table('contracts as c')->select('c.*','cl.renewal_notification_email','cl.renewal_notification','v.vendor_name')->leftjoin('clients as cl','cl.id','=','c.client_id')->leftjoin('vendors as v','v.id','=','c.vendor_id') ->where('c.contract_status','Active')->where('c.is_deleted',0)->get();
       

                        foreach($qry as $q){
 $start_date = strtotime($q->contract_end_date);
  $end_date = strtotime(date("Y-m-d"));
$days= ($end_date - $start_date)/60/60/24;
  
$result=0;



         if($days==-1*$settings->interval_1){
                $subject="60 days left for Expiring Of Contract # ".$q->contract_no;
                $result=1;
        }
        if($days==-1*$settings->interval_2){
                 $subject="45 days left for Expiring Of Contract # ".$q->contract_no;   
            $result=1;
        }        
        if($days==-1*$settings->interval_3){
                $subject="30 days left for Expiring Of Contract # ".$q->contract_no;            
            $result=1;
        }
        if($days==-1*$settings->interval_4){
                $subject="15 days left for Expiring Of Contract # ".$q->contract_no;            
            $result=1;
        }
        if($days==-1*$settings->interval_5){
                $subject="7 days left for Expiring Of Contract # ".$q->contract_no;            
            $result=1;
        }
        if($days==-1*$settings->interval_6){
                $subject="1 days left for Expiring Of Contract # ".$q->contract_no;            
            $result=1;
        }
        if($days==-1*$settings->interval_7){
                $subject="0 days left for Expiring Of Contract # ".$q->contract_no;            
            $result=1;
        }
echo ($days.'<br>');
echo $result;
            if($result==1){
                    if($q->renewal_notification==1 && $q->email_sent!=date('Y-m-d')){
                    $client_emails=[$q->renewal_notification_email] ;
                        $client_email=DB::Table('client_emails')->where('client_id',$q->client_id)->get();
                     
                    foreach($client_email as $c){
              
                        array_push($client_emails,$c->renewal_email);

                    }

                  $data = array( 'emails' => $client_emails, 'contract_no' =>$q->contract_no,'end_date'=>$q->contract_end_date,'vendor_name'=>$q->vendor_name,'contract_id'=>$q->id, 'subject' => $subject,'from_name'=>$settings->from_name);
                    // return view('emails.renewal_email',['data'=>$data]);
    // $pdf = PDF::loadView('exports.ExportPdfContract',['id'=>$q->id]);
  
        Mail::send('emails.renewal_email', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
    $message->from('info@consultationamaltitek.com',$data['from_name']);
        });
        DB::table('contracts')->where('id',$q->id)->update(['email_sent'=>date('Y-m-d')]);
DB::Table('notifications')->insert(['type'=>'Contract','from_email'=>$data['from_name'],'to_email'=>implode(',',$data['emails']),'subject'=>$data['subject']]);
    }

    }




        }



        

              $qry=DB::Table('contracts as c')->select('c.*','cl.renewal_notification_email','cl.renewal_notification','v.vendor_name')->leftjoin('clients as cl','cl.id','=','c.client_id')->leftjoin('vendors as v','v.id','=','c.vendor_id') ->where('c.contract_end_date','<=',date('Y-m-d'))->where('c.contract_status','Active')->where('c.is_deleted',0)->get();
    foreach($qry as $q){
         $asset=DB::table('contract_assets')->where('is_deleted',0)->where('contract_id',$q->id)->get();
                                foreach($asset as $a){
                            
                                        DB::table('assets')->where('id',$a->hostname)->update(['warranty_status'=>'Inactive' ,'SupportStatus'=>'Expired']);

                                }
    }
        DB::Table('contracts')->where('contract_end_date','<=',date('Y-m-d'))->where('contract_status','Active')->where('is_deleted',0)->update(['contract_status'=>'Expired']);


}
   
  
    public function CheckSSLStatus(Request $request){
                         
                                  
                 $settings=DB::Table('notification_settings')->first();
          
                       
                        

                        $qry=DB::Table('ssl_certificate as c')   ->where('c.cert_status','Expired')->where('c.is_deleted',0)->get();
              
                        foreach($qry as $q){
 // Declare two dates
  $start_date = strtotime($q->cert_edate);
  $end_date = strtotime(date("Y-m-d"));
  
      $days= ($end_date - $start_date)/60/60/24;

      if($days>=30){
                     
                                DB::Table('ssl_certificate')->where('id',$q->id) ->update(['cert_status'=>'Expired/Ended']);                            
               }
           }


      $qry1=DB::Table('ssl_certificate as c')  ->select('c.*','a.hostname')->leftjoin('assets as a','a.id','=','c.cert_hostname') ->where('c.cert_status','Active')->where('c.is_deleted',0)->get();
      
    
          foreach($qry1 as $q){
      $result=0;
      $start_date = strtotime($q->cert_edate);
        $end_date = strtotime(date("Y-m-d"));
        $days= ($end_date - $start_date)/60/60/24;  
         if($days==-1*$settings->interval_1){
                $subject="60 days left for Expiring Of SSL Certificate  ".$q->cert_name;
                $result=1;
        }
        if($days==-1*$settings->interval_2){
                 $subject="45 days left for Expiring Of SSL Certificate  ".$q->cert_name;   
            $result=1;
        }        
        if($days==-1*$settings->interval_3){
                $subject="30 days left for Expiring Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
        if($days==-1*$settings->interval_4){
                $subject="15 days left for Expiring Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
        if($days==-1*$settings->interval_5){
                $subject="7 days left for Expiring Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
         if($days==-1*$settings->interval_6){
                $subject="2 days left for Expiring Of SSL Certificate  ".$q->cert_name;            
            $result=1;
     
        if($days==-1*$settings->interval_7){
                $subject="0 days left for Expiring Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }

            if($result==1){
                    if($q->cert_notification==1 && $q->email_sent!=date('Y-m-d')){

                    $client_emails=[] ;
                    echo ($days.'<br>');
                        $client_email=DB::Table('ssl_emails')->where('ssl_id',$q->id)->get();
                     
                    foreach($client_email as $c){
              
                        array_push($client_emails,$c->renewal_email);

                    }

                  $data = array( 'emails' => $client_emails, 'cert_name' =>$q->cert_name,'cert_edate'=>$q->cert_edate, 'ssl_id'=>$q->id, 'subject' => $subject,'hostname'=>$q->hostname,'from_name'=>$settings->from_name);
                    // return view('emails.renewal_email',['data'=>$data]);
    // $pdf = PDF::loadView('exports.ExportPdfContract',['id'=>$q->id]);
  
        Mail::send('emails.renewal_email_ssl', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
  $message->from('info@consultationamaltitek.com',$data['from_name']);
        });

        DB::table('ssl_certificate')->where('id',$q->id)->update(['email_sent'=>date('Y-m-d')]);
DB::Table('notifications')->insert(['type'=>'Certificate','from_email'=>$settings->from_name,'to_email'=>implode(',',$data['emails']),'subject'=>$data['subject']]);
    }

    }


}



        }


 



$qry1=DB::Table('ssl_certificate as c')  ->select('c.*','a.hostname')->leftjoin('assets as a','a.id','=','c.cert_hostname') ->where('c.cert_status','Active')->where('c.cert_type','public')->where('c.is_deleted',0)->get();
      
   
          foreach($qry1 as $q){
      $result=0;
      $start_date = strtotime($q->cert_rdate);
        $end_date = strtotime(date("Y-m-d"));
  
      $days= ($end_date - $start_date)/60/60/24;
 
         if($days==-1*$settings->interval_1){
                $subject="60 days left for Renew Of SSL Certificate  ".$q->cert_name;
                $result=1;
        }
        if($days==-1*$settings->interval_2){
                 $subject="45 days left for Renew Of SSL Certificate  ".$q->cert_name;   
            $result=1;
        }        
        if($days==-1*$settings->interval_3){
                $subject="30 days left for Renew Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
        if($days==-1*$settings->interval_4){
                $subject="15 days left for Renew Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
        if($days==-1*$settings->interval_5){
                $subject="7 days left for Renew Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
         if($days==-1*$settings->interval_6){
                $subject="2 days left for Renew Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }
     
        if($days==-1*$settings->interval_7){
                $subject="0 days left for Renew Of SSL Certificate  ".$q->cert_name;            
            $result=1;
        }

            if($result==1){
                    if($q->cert_notification==1 && $q->email_sent!=date('Y-m-d')){

                    $client_emails=[] ;
                    echo ($days.'<br>');
                        $client_email=DB::Table('ssl_emails')->where('ssl_id',$q->id)->get();
                     
                    foreach($client_email as $c){
              
                        array_push($client_emails,$c->renewal_email);

                    }

                  $data = array( 'emails' => $client_emails, 'cert_name' =>$q->cert_name,'cert_edate'=>$q->cert_edate, 'ssl_id'=>$q->id, 'subject' => $subject,'hostname'=>$q->hostname,'from_name'=>$settings->from_name);
                    // return view('emails.renewal_email',['data'=>$data]);
    // $pdf = PDF::loadView('exports.ExportPdfContract',['id'=>$q->id]);
  
        Mail::send('emails.renewal_email_ssl', ['data' => $data], function ($message) use ($data) {
            $message->to($data['emails']);
            $message->subject($data['subject']);
  $message->from('info@consultationamaltitek.com',$data['from_name']);
        });

        DB::table('ssl_certificate')->where('id',$q->id)->update(['renew_sent'=>date('Y-m-d')]);
DB::Table('notifications')->insert(['type'=>'Certificate','from_email'=>$data['from_name'],'to_email'=>implode(',',$data['emails']),'subject'=>$data['subject']]);
    }

    } 
 
        }
   DB::Table('ssl_certificate')->where('cert_edate','<=',date('Y-m-d'))->where('cert_status','Active')->where('is_deleted',0)->update(['cert_status'=>'Expired']);
DB::table('assets as a')->join('ssl_certificate as s','s.cert_hostname','=','a.id')->where('cert_status','Expired')->orWhere('cert_status','Expired/Ended')->update(['ssl_certificate_status'=>'Expired']);



}
   
}