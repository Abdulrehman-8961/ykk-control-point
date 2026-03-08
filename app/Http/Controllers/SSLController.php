<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
use DateTime;

use Excel;

use App\Exports\ExportSSL;
use App\Exports\ExportExpiringSSLCertificate;

use Validator;

class SSLController extends Controller
{
    //
    public function __construct() {}

    public function getIpHostname(Request $request)
    {


        $qry = DB::Table('asset_ip_addresses')->where('asset_id', $request->id)->orderby('ip_address_name', 'asc')->get();
        return response()->json($qry);
    }


    public function SSLCertificate()
    {

        return view('SSLCertificate');
    }

    public function AddSSLCertificate()
    {

        return view('AddSSLCertificate');
    }
    public function EditSSLCertificate()
    {

        return view('EditSSLCertificate');
    }
    public function RenewSSLCertificate()
    {

        return view('RenewSSLCertificate');
    }














    public function getAttachmentSSL(Request $request)
    {
        $qry = DB::table('ssl_attachments')->where('ssl_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getCommentsSSL(Request $request)
    {
        $qry = DB::table('ssl_comments')->where('ssl_id', $request->id)->get();
        return response()->json($qry);
    }


    public function getSanSSL(Request $request)
    {
        $qry = DB::table('ssl_san')->where('ssl_id', $request->id)->get();
        return response()->json($qry);
    }

    public function getEmailSSL(Request $request)
    {
        $qry = DB::table('ssl_emails')->where('ssl_id', $request->id)->get();
        return response()->json($qry);
    }


    public function getHostSSL(Request $request)
    {
        $qry = DB::table('ssl_host as s')->select('*', 's.id as sid', 's.hostname as host', 'a.hostname as asset_name')->leftjoin('assets as a', 'a.id', '=', 's.hostname')->leftjoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')->where('s.ssl_id', $request->id)->get();
        return response()->json($qry);
    }


    public function getIpSSL(Request $request)
    {
        $qry = DB::Table('asset_ip_addresses')->whereIn('id', $request->id)->orderby('ip_address_name', 'asc')->get();

        return response()->json($qry);
    }



    public function ExportExcelSSL(Request $request)
    {

        return Excel::download(new ExportSSL($request), 'SSLCertificate.xlsx');
    }

    public function PrintSSLCertificate(Request $request)
    {

        return view('exports/ExportPrintSSL');
    }




    public function uploadSSLAttachment(Request $request)
    {

        $attachment = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];

        $fileExt = explode('.', $attachment);
        $fileActualExt = strtolower(end($fileExt));
        $key = uniqid() . '.' . $fileActualExt;

        $request->file('attachment')->move(public_path('temp_uploads'), $key);

        echo json_encode($key);
    }




    public function LoadSSLAttachment(Request $request)
    {

        $request->header('Access-Control-Allow-Origin: *');

        // Allow the following methods to access this file
        $request->header('Access-Control-Allow-Methods: OPTIONS, GET, DELETE, POST, HEAD, PATCH');

        // Allow the following headers in preflight
        $request->header('Access-Control-Allow-Headers: content-type, upload-length, upload-offset, upload-name');

        // Allow the following headers in response
        $request->header('Access-Control-Expose-Headers: upload-offset');

        // Load our configuration for this server




        $uniqueFileID = $_GET["key"];

        $imagePointer = public_path("ssl_attachment/" .  $uniqueFileID);
        if (!file_exists('..temp_uploads/' . $uniqueFileID)) {

            copy(public_path("ssl_attachment/" .  $uniqueFileID), public_path("temp_uploads/" . $uniqueFileID));
        }




        $imageName = $uniqueFileID;






        // if imageName was found in the DB, get file with imageName and return file object or blob
        $imagePointer = public_path("ssl_attachment/" . $uniqueFileID);


        $fileObject = null;

        if ($imageName != '' && file_exists($imagePointer)) {

            $fileObject = file_get_contents($imagePointer);
        }



        // trigger load local image
        $loadImageResultArr = [$fileBlob, $imageName] = [$fileObject, $imageName];

        if ($fileBlob) {
            $imagePointer = public_path("ssl_attachment/" .  $imageName);
            $fileContextType = mime_content_type($imagePointer);
            $fileSize = filesize($imagePointer);

            $handle = fopen($imagePointer, 'r');
            if (!$handle) return false;
            $content = fread($handle, filesize($imagePointer));


            $response = Response::make($content);
            $response->header('Access-Control-Expose-Headers', 'Content-Disposition, Content-Length, X-Content-Transfer-Id');
            $response->header('Content-Type', $fileContextType);
            $response->header('Content-Length', $fileSize);
            $response->header('Content-Disposition', "inline; filename=$imageName");


            return $response;
        } else {
            http_response_code(500);
        }
    }


    public function revertSSLAttachment(Request $request)
    {
        $key = str_replace('"', "", $request->key);

        unlink(public_path('temp_uploads/' . $key));

        echo json_encode(1);
    }




    public function ShowAssetSSL(Request $request)
    {
        $client_id = $request->client_id;

        //  and  id not in (   select cert_hostname from  ssl_certificate where cert_status!='Inactive'  and  id!='$id' and is_deleted=0  and cert_hostname =a.id )
        if ($request->id != '') {
            $id = $request->id;
            $qry = DB::select("select a.*,at.asset_icon from assets as a left join asset_type as at on a.asset_type_id=at.asset_type_id where a.is_deleted=0 and ntp=1 and a.client_id='$client_id' and  AssetStatus=1  ");
        } else {
            //and  id not in (   select cert_hostname from  ssl_certificate where cert_status!='Inactive' and is_deleted=0 and cert_hostname =a.id  )
            $qry = DB::select("select a.*,at.asset_icon from assets as a left join asset_type as at on a.asset_type_id=at.asset_type_id where a.is_deleted=0 and ntp=1 and a.client_id='$client_id' and  AssetStatus=1 ");
        }



        return response()->json($qry);
    }

    public function InsertSSLCertificate(Request $request)
    {





        $data = array(
            'client_id' => $request->client_id,
            'cert_status' => 'Active',
            'cert_notification' => $request->cert_notification,
            'cert_type' => $request->cert_type,
            'cert_issuer' => $request->cert_issuer,
            'cert_rdate' => $request->cert_rdate,
            'cert_msrp' => $request->cert_msrp,
            'cert_hostname' => isset($request->cert_hostname) ? implode(',', $request->cert_hostname) : '',
            'cert_name' => $request->cert_name,
            'cert_email' => $request->cert_email,
            'cert_company' => $request->cert_company,
            'cert_department' => $request->cert_department,
            'cert_city' => $request->cert_city,
            'site_id' => $request->site_id,
            'description' => $request->description,
            'cert_state' => $request->cert_state,
            'cert_country' => $request->cert_country,
            'created_by' => Auth::id(),
            'cert_ip_int' => $request->cert_ip_int,
            'cert_ip_pub' => $request->cert_ip_pub,
            'cert_edate' => $request->cert_edate,
            'cert_csr' => $request->cert_csr,
            'cert_process' => $request->cert_process,
        );

        DB::Table('ssl_certificate')->insert($data);
        $id = DB::getPdo()->lastInsertId();

        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('ssl_attachment/' . $a->attachment));
                DB::table('ssl_attachments')->insert([
                    'ssl_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $emailArray = $request->emailArray;
        if (isset($request->emailArray)) {
            foreach ($emailArray as $a) {
                $a = json_decode($a);


                DB::table('ssl_emails')->insert([
                    'ssl_id' => $id,

                    'renewal_email' => $a->email,

                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('ssl_comments')->insert([
                    'ssl_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }




        $sanArray = $request->sanArray;
        if (isset($request->sanArray)) {
            foreach ($sanArray as $a) {
                $a = json_decode($a);
                DB::table('ssl_san')->insert([
                    'ssl_id' => $id,
                    'san' => $a->san,
                    'added_by' => Auth::id(),
                    'san_type' => $a->san_type,
                ]);
            }
        }

        $hostArray = $request->hostArray;
        if (isset($request->hostArray)) {
            foreach ($hostArray as $a) {
                $a = json_decode($a);
                $ip_id = array();
                $ip_name = array();
                foreach ($a->ip as $b) {


                    array_push($ip_id, @$b->id);
                    array_push($ip_name, @$b->name);
                }

                DB::table('ssl_host')->insert([
                    'ssl_id' => $id,
                    'hostname' => $a->hostname,
                    'ip_id' => implode(',', $ip_id),
                    'ip_name' => implode(',', $ip_name),
                ]);

                DB::table('assets')->where('id', $a->hostname)->update(['ssl_certificate_status' => 'Active']);
            }
        }

        DB::table('ssl_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'SSL Certificate', 'ssl_id' => $id]);

        return response()->json('success');
    }







    public function EndSSLCertificate(Request $request)
    {

        $check = DB::Table('ssl_certificate')->where('id', $request->id)->first();


        if ($request->end == 1) {
            DB::Table('ssl_certificate')->where('id', $request->id)->update(['cert_status' => 'Active']);

            DB::table('ssl_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'ssl_id' => $request->id, 'comment' => 'SSL certificate successfully reinstated.<br>' . $request->reason]);

            DB::table('ssl_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'SSL certificate successfully reinstated.', 'ssl_id' => $request->id]);
            return redirect()->back()->with('success', 'SSL Reinstated Successfully');
        } else {
            DB::Table('ssl_certificate')->where('id', $request->id)->update(['cert_status' => 'Ended', 'ended_by' => Auth::id(), 'ended_reason' => $request->reason, 'ended_on' => date('Y-m-d H:i:s')]);

            DB::table('ssl_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'ssl_id' => $request->id, 'comment' => 'SSL certificate successfully revoked.<br>' . $request->reason]);

            DB::table('ssl_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'SSL certificate successfully revoked.', 'ssl_id' => $request->id]);
            return redirect()->back()->with('success', 'SSL Ended Successfully');
        }
    }


    public function DeleteSSLCertificate(Request $request)
    {


        $id = $request->id;
        $qry = DB::Table('ssl_certificate')->where('id', $id)->first();

        $userAccess = explode(',', Auth::user()->access_to_client);
        if (Auth::user()->role != 'admin') {

            if (!in_array($qry->client_id, $userAccess)) {
                echo "You dont have access";
                exit;
            }
        }
        if (Auth::user()->role == 'read') {
            echo "You dont have access";
            exit;
        }

        DB::table('ssl_certificate')->where('id', $id)->update(['is_deleted' => 1, 'deleted_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'SSL Certificate Deleted Successfully');
    }



    public function ShowSSLCertificate(Request $request)
    {
        $qry = DB::table('ssl_certificate as s')->select('s.*', 'a.hostname', 'c.firstname', 'usr.firstname as created_firstname', 'usr.lastname as created_lastname', 'upd.firstname as updated_firstname', 'upd.lastname as updated_lastname', 'c.logo', 'v.vendor_image')->leftjoin('assets as a', 'a.id', '=', 's.cert_hostname')->leftjoin('vendors as v', 'v.id', '=', 's.cert_issuer')->leftjoin('clients as c', 'c.id', '=', 's.client_id')->leftjoin('users as usr', 'usr.id', '=', 's.created_by')->leftjoin('users as upd', 'upd.id', '=', 's.updated_by')->where('s.is_deleted', 0)->where('s.id', $request->id)->first();

        return response()->json($qry);
    }





    public function UpdateSSLCertificate(Request $request)
    {
        $qry = DB::Table('ssl_certificate')->where('id', $request->id)->first();
        $hostname_qry = DB::Table('ssl_host')->where('ssl_id', $request->id)->get();
        foreach ($hostname_qry as $h) {
            DB::table('assets')->where('id', $h->hostname)->update(['ssl_certificate_status' => 'Unassigned']);
        }


        $data = array(
            'client_id' => $request->client_id,
            'cert_notification' => $request->cert_notification,
            'cert_type' => $request->cert_type,
            'cert_issuer' => $request->cert_issuer,
            'site_id' => $request->site_id,
            'description' => $request->description,
            'cert_rdate' => $request->cert_rdate,
            'cert_msrp' => $request->cert_msrp,
            'cert_hostname' => isset($request->cert_hostname) ? implode(',', $request->cert_hostname) : '',
            'cert_name' => $request->cert_name,
            'cert_email' => $request->cert_email,
            'cert_company' => $request->cert_company,
            'cert_department' => $request->cert_department,
            'cert_city' => $request->cert_city,
            'cert_state' => $request->cert_state,
            'cert_country' => $request->cert_country,

            'cert_ip_int' => $request->cert_ip_int,
            'cert_ip_pub' => $request->cert_ip_pub,
            'cert_edate' => $request->cert_edate,
            'cert_csr' => $request->cert_csr,
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
            'cert_process' => $request->cert_process,
        );
        DB::Table('ssl_certificate')->where('id', $request->id)->update($data);
        $id = $request->id;


        DB::table('ssl_emails')->where('ssl_id', $request->id)->delete();


        DB::table('ssl_san')->where('ssl_id', $request->id)->delete();
        DB::table('ssl_comments')->where('ssl_id', $request->id)->delete();
        DB::table('ssl_attachments')->where('ssl_id', $request->id)->delete();
        DB::table('ssl_host')->where('ssl_id', $request->id)->delete();






        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('ssl_attachment/' . $a->attachment));
                DB::table('ssl_attachments')->insert([
                    'ssl_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $emailArray = $request->emailArray;
        if (isset($request->emailArray)) {
            foreach ($emailArray as $a) {
                $a = json_decode($a);


                DB::table('ssl_emails')->insert([
                    'ssl_id' => $id,

                    'renewal_email' => $a->email,

                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('ssl_comments')->insert([
                    'ssl_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }




        $sanArray = $request->sanArray;
        if (isset($request->sanArray)) {
            foreach ($sanArray as $a) {
                $a = json_decode($a);
                DB::table('ssl_san')->insert([
                    'ssl_id' => $id,
                    'san' => $a->san,
                    'added_by' => Auth::id(),
                    'san_type' => $a->san_type,
                ]);
            }
        }

        $hostArray = $request->hostArray;
        if (isset($request->hostArray)) {
            foreach ($hostArray as $a) {
                $a = json_decode($a);
                $ip_id = array();
                $ip_name = array();
                foreach ($a->ip as $b) {


                    array_push($ip_id, @$b->id);
                    array_push($ip_name, @$b->name);
                }

                DB::table('ssl_host')->insert([
                    'ssl_id' => $id,
                    'hostname' => $a->hostname,
                    'ip_id' => implode(',', $ip_id),
                    'ip_name' => implode(',', $ip_name),
                ]);





                if ($qry->cert_status == 'Active') {
                    DB::table('assets')->where('id', $a->hostname)->update(['ssl_certificate_status' => 'Active']);
                } else {


                    DB::table('assets')->where('id', $a->hostname)->update(['ssl_certificate_status' => 'Expired/Ended']);
                }
            }
        }

        DB::table('ssl_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'SSL Certificate updated', 'ssl_id' => $id]);



        return response()->json('success');
    }




    public function RenewSSLCertificateUpdate(Request $request)
    {


        DB::table('ssl_certificate')->where('id', $request->id)->update(['cert_status' => 'Inactive', 'renewed_on' => date('Y-m-d'), 'renewed_by' => Auth::id()]);


        $qry = DB::Table('ssl_certificate')->where('id', $request->id)->first();
        $hostname_qry = DB::Table('ssl_host')->where('ssl_id', $request->id)->get();
        $hostname_qry = DB::Table('ssl_host')->where('ssl_id', $request->id)->get();
        foreach ($hostname_qry as $h) {
            DB::table('assets')->where('id', $h->hostname)->update(['ssl_certificate_status' => 'Unassigned']);
        }


        $data = array(
            'client_id' => $request->client_id,
            'cert_notification' => $request->cert_notification,
            'cert_type' => $request->cert_type,
            'cert_issuer' => $request->cert_issuer,
            'cert_status' => 'Active',
            'cert_rdate' => $request->cert_rdate,
            'cert_msrp' => $request->cert_msrp,
            'cert_hostname' => isset($request->cert_hostname) ? implode(',', $request->cert_hostname) : '',
            'cert_name' => $request->cert_name,
            'cert_email' => $request->cert_email,
            'cert_company' => $request->cert_company,
            'cert_department' => $request->cert_department,
            'cert_city' => $request->cert_city,
            'cert_state' => $request->cert_state,
            'cert_country' => $request->cert_country,
            'site_id' => $request->site_id,
            'description' => $request->description,
            'cert_ip_int' => $request->cert_ip_int,
            'cert_ip_pub' => $request->cert_ip_pub,
            'cert_edate' => $request->cert_edate,
            'cert_csr' => $request->cert_csr,

            'cert_process' => $request->cert_process,
        );
        DB::Table('ssl_certificate')->insert($data);
        $id = DB::getPdo()->lastInsertId();






        $attachment_array = $request->attachmentArray;
        if (isset($request->attachmentArray)) {
            foreach ($attachment_array as $a) {
                $a = json_decode($a);

                copy(public_path('temp_uploads/' . $a->attachment), public_path('ssl_attachment/' . $a->attachment));
                DB::table('ssl_attachments')->insert([
                    'ssl_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'attachment' => $a->attachment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }

        $emailArray = $request->emailArray;
        if (isset($request->emailArray)) {
            foreach ($emailArray as $a) {
                $a = json_decode($a);


                DB::table('ssl_emails')->insert([
                    'ssl_id' => $id,

                    'renewal_email' => $a->email,

                    'added_by' => Auth::id(),
                ]);
            }
        }

        $commentArray = $request->commentArray;
        if (isset($request->commentArray)) {
            foreach ($commentArray as $a) {
                $a = json_decode($a);


                DB::table('ssl_comments')->insert([
                    'ssl_id' => $id,
                    'date' => date('Y-m-d H:i:s', strtotime($a->date . ' ' . $a->time)),
                    'comment' => $a->comment,
                    'name' => $a->name,
                    'added_by' => Auth::id(),
                ]);
            }
        }




        $sanArray = $request->sanArray;
        if (isset($request->sanArray)) {
            foreach ($sanArray as $a) {
                $a = json_decode($a);
                DB::table('ssl_san')->insert([
                    'ssl_id' => $id,
                    'san' => $a->san,
                    'added_by' => Auth::id(),
                    'san_type' => $a->san_type,
                ]);
            }
        }

        $hostArray = $request->hostArray;
        if (isset($request->hostArray)) {
            foreach ($hostArray as $a) {
                $a = json_decode($a);
                $ip_id = array();
                $ip_name = array();
                foreach ($a->ip as $b) {


                    array_push($ip_id, @$b->id);
                    array_push($ip_name, @$b->name);
                }

                DB::table('ssl_host')->insert([
                    'ssl_id' => $id,
                    'hostname' => $a->hostname,
                    'ip_id' => implode(',', $ip_id),
                    'ip_name' => implode(',', $ip_name),
                ]);






                DB::table('assets')->where('id', $a->hostname)->update(['ssl_certificate_status' => 'Active']);
            }
        }

        DB::table('ssl_comments')->insert(['added_by' => Auth::id(), 'date' => date('Y-m-d H:i:s'), 'name' => Auth::user()->firstname . ' ' . Auth::user()->lastname, 'ssl_id' => $request->id, 'comment' => 'SSL certificate successfully renewed.']);

        DB::table('ssl_audit_trail')->insert(['user_id' => Auth::id(), 'description' => 'SSL certificate successfully renewed.', 'ssl_id' => $request->id]);


        return response()->json('success');
    }






    public function getSslContent(Request $request)
    {
        $id = $request->id;
        $html = '';

        $q = DB::table('ssl_certificate as s')->select(
            's.*',
            'a.hostname',
            'c.firstname',
            'site.site_name',
            'site.address',
            'site.city',
            'site.province',
            'site.zip_code',
            'v.vendor_name',
            's.cert_msrp',
            'usr.firstname as created_firstname',
            'usr.lastname as created_lastname',
            'upd.firstname as updated_firstname',
            'upd.lastname as updated_lastname',
            'c.logo',
            'v.vendor_image'
        )->leftjoin('assets as a', 'a.id', '=', 's.cert_hostname')->leftjoin('vendors as v', 'v.id', '=', 's.cert_issuer')->leftjoin('clients as c', 'c.id', '=', 's.client_id')->leftjoin('sites as site', 'site.id', '=', 's.site_id')->leftjoin('users as usr', 'usr.id', '=', 's.created_by')->leftjoin('users as upd', 'upd.id', '=', 's.updated_by')->where('s.is_deleted', 0)->where('s.id', $id)->first();

        $cert_edate = date('Y-M-d', strtotime($q->cert_edate));
        $today = date('Y-m-d');
        $earlier = new DateTime($cert_edate);
        $later = new DateTime($today);

        $abs_diff = $later->diff($earlier)->format("%a"); //3
        $ended_qry = DB::Table('users')->Where('id', $q->ended_by)->first();
        $renewed_qry = DB::Table('users')->Where('id', $q->renewed_by)->first();






        if ($q->cert_status == 'Active') {

            if ($abs_diff <= 30) {
                $html .= '<div class="block card-round   bg-new-yellow new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                      <div class="d-flex">
                          <img src="' . ('public/img/icon-upcoming-removebg-preview.png') . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text text-dark" style="line-height:25px">Upcoming</h4>
                                <p class="mb-0  header-new-subtext text-dark" style="line-height:20px">In ' . $abs_diff . ' days</p>
                                    </div>
                                </div>';
            } else {
                $html .= '<div class="block card-round   bg-new-green new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="' . ('public/img/icon-header-white-reactivate.png') . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Active</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">Until ' . $cert_edate . ' (' . $abs_diff . ' days remaining)</p>
                                    </div>
                                </div>';
            }
        } elseif ($q->cert_status == 'Inactive') {
            $html .= '<div class="block card-round   bg-new-blue new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="' . ('public/img/icon-renewed-removebg-preview.png') . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Renewed
</h4>
                                       <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . date('Y-M-d H:i:s A', strtotime($q->renewed_on)) . ' by   ' . @$renewed_qry->firstname . ' ' . @$renewed_qry->lastname . '</p>
                                    </div>
                                </div>';
        } elseif ($q->cert_status == 'Expired/Ended') {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="' . ('public/img/icon-ended-removebg-preview.png') . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Ended
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . date('Y-M-d', strtotime($q->ended_on)) . ' at  ' . date('H:i:s A', strtotime($q->ended_on)) . ' By ' . @$ended_qry->firstname . ' ' . @$ended_qry->lastname . '</p>
                                    </div>
                                </div>';
        } elseif ($q->cert_status == 'Ended') {
            $html .= '<div class="block card-round   bg-new-red new-nav" >
                                
                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="' . ('public/img/icon-expired-removebg-preview.png') . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Ended
</h4>   
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . date('Y-M-d', strtotime($q->ended_on)) . ' at  ' . date('H:i:s A', strtotime($q->ended_on)) . ' By ' . @$ended_qry->firstname . ' ' . @$ended_qry->lastname . '</p>
                                    </div>
                                </div>';
        } elseif ($q->cert_status == 'Expired') {
            $html .= '<div class="block card-round   bg-new-red new-nav" >

                                <div class="block-header   py-new-header" >
                               <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">

                                
                                <div class="d-flex">
                                <img src="' . ('public/img/icon-expired-removebg-preview.png') . '" width="40px">
                                <div class="ml-4">
                                <h4  class="mb-0 header-new-text" style="line-height:25px">SSL Certificate Expired
</h4>
                                <p class="mb-0  header-new-subtext" style="line-height:15px">On ' . $cert_edate . '</p>
                                    </div>
                                </div>';
        }







        $html .= '<div class="new-header-icon-div d-flex align-items-center  no-print">'
?>
                                                
                                                                <?php if (Auth::user()->role != 'read') {

                                                                    if ($q->cert_status != 'Inactive' && $q->cert_status != 'Expired/Ended' && $q->cert_status != 'Ended') {

                                                                        $html .= '<a href="' . url('renew-ssl-certificate') . '?id=' . $q->id . '" i  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Renew SSL Certificate" class=" ">
                                                <img src="public/img/icon-header-white-renew.png?cache=1" width="20px">
                                            </a>';
                                                                    }
                                                                    if ($q->cert_status != 'Inactive' && $q->cert_status != 'Expired/Ended' && $q->cert_status != 'Ended') {

                                                                        $html .= '
                                             <span  > 
                                             <a href="javascript:;" class="btnEnd"   data="' . $q->id . '" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Revoke SSL" class=" "><img src="public/img/icon-header-white-end-decom.png?cache=1" width="22px"></a>
                                         </span>';
                                                                    }
                                                                }

                                                                if ($q->cert_status == 'Expired/Ended' || $q->cert_status == 'Ended') {

                                                                    $html .= '<a data="' . $q->id . '" href="javascript:;"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Reinstate SSL certificate" class="btnEnd " data-ended="1">
                                                <img src="public/img/icon-header-white-reactivate.png?cache=1" width="22px">
                                            </a>';
                                                                }

                                                                $html .= ' <a  target="_blank" href="' . url('pdf-ssl-certificate') . '?id=' . $q->id . '"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Pdf" class=" " style="padding:5px 7px">
                                                <img src="public/img/action-white-pdf.png?cache=1" width="25px">
                                            </a>
     <a  href="javascript:;" onclick="window.print()" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Print" class=" ">
                                                <img src="public/img/action-white-print.png?cache=1" width="20px">
                                            </a>';
                                                                if (Auth::user()->role != 'read') {

                                                                    $html .= '<a   href="' . url('edit-ssl-certificate') . '?id=' . $q->id . '" class="text-white    " data="0" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="Edit" data-original-title="Edit">     <img src="public/img/action-white-edit.png?cache=1" width="20px">  </a>
                                            <a data-toggle="tooltip" data-trigger="hover" data="' . $q->id . '" data-placement="top" title="" data-original-title="Delete" href="javascript:;" class="text-white btnDelete">     <img src="public/img/action-white-delete.png?cache=1" width="17px"></a>';
                                                                }
                                                                $html .= '</div>


                                </div>
                            </div>
                        </div>

                        <div class="block new-block position-relative mt-3" >
                                                <div class="top-div text-capitalize">' . $q->cert_type . ' SSL Certificate</div>
                            
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
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>' . $q->firstname . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Site</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    <b>' . $q->site_name . '</b><br>
                                                    <span>' . $q->address . '</span><br>
                                                    <span>' . $q->city . ',' . $q->province . '</span><br>
                                                    <span>' . $q->zip_code . '</span><br>
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">
                                       <img src="public/client_logos/' . $q->logo . '" style="width: 100%;">
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
                                                                if ($q->cert_type == 'internal') {
                                                                    $html .= $q->firstname;
                                                                } else {
                                                                    $html .= $q->vendor_name;
                                                                }

                                                                $html .= '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Description</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->description . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Expiration Date</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                           ' . date('Y-M-d', strtotime($q->cert_edate)) . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Renewal Date</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                 ' . date('Y-M-d', strtotime($q->cert_rdate)) . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                            <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">MSRP</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                 $' . number_format($q->cert_msrp) . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                               <div class="bubble-white-new bubble-text-sec" style="padding:10px">';
                                                                if ($q->cert_type == 'internal') {

                                                                    $html .= '<img src="public/client_logos/' . $q->logo . '" style="width: 100%;">';
                                                                } else {
                                                                    $html .= '<img src="public/vendor_logos/' . $q->vendor_image . '" style="width: 100%;">';
                                                                }
                                                                $html .= '
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
                                                
                                           <div class="bubble-white-new bubble-text-first"><b>' . $q->cert_name . '</b></div> 
                                     
                                            </div>

                                         </div>
                                         
                                        <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Email (E)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                    ' . $q->cert_email . '
                                                
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Company (O)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->cert_company . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Department (OU)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->cert_department . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">City (L)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->city . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                          <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">State (S)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->cert_state . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                            <div class="form-group row">
                                         <div class="col-sm-4">
                                           <div class="bubble-new">Country (CA)</div> 
                                       </div>
                                            <div class="col-sm-8">
                                                  <div class="bubble-white-new bubble-text-sec">
                                                ' . $q->cert_country . '
                                                  
                                                </div> 
                                     
                                            </div>
                                          
                                        </div>
                                         
                                    </div>
                                    <div class="col-sm-2">
                                         <img src="public/img/static-ssl-cert.png?cache=1" style="width: 100%;">     

                                    </div>

                                      
                                               </div>      

                                             


                                   

                                                     
                 
                 </div>
             </div>
         </div>







';
                                                                $ssl = DB::table('ssl_san')->where('ssl_id', $q->id)->get();

                                                                if (sizeof($ssl) > 0) {
                                                                    $html .= '<div class="block-content pb-0 mt-4 " style="padding-left: 50px;padding-right: 50px;">
                             
                                <div class="row justify-content- position-relative inner-body-content push" >
 <div class="top-right-div top-right-div-green text-capitalize">Subject Alt Names
</div>
                            
                            <div class="col-sm-12 m-
                            " >';





                                                                    foreach ($ssl as $s) {
                                                                        $html .= '
     <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 2px;padding-bottom: 7px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>';
                                                                        if ($s->san_type == 'DNS') {
                                                                            $html .= '<img src="public/img/san.png?cache=1" width="32px">';
                                                                        } elseif ($s->san_type == 'IP') {
                                                                            $html .= '<img src="public/img/ip.png?cache=1" width="32px">';
                                                                        } else {
                                                                            $html .= '<span style="font-size:30px">@</span>';
                                                                        }

                                                                        $html .= '</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0" style="">
                                                        <label class="mb-0">' . $s->san . '</label>
                                                    </td>
                                                   
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>   ';
                                                                    }






                                                                    $html .= '</div>
             </div>
         </div>


     </div>';
                                                                }


                                                                $line_items = DB::table('ssl_host as s')->select('*', 's.id as sid', 's.hostname as host', 'a.AssetStatus', 'a.id as aid', 'a.hostname as asset_name', 'a.ip_address')->leftjoin('assets as a', 'a.id', '=', 's.hostname')->leftjoin('asset_type as at', 'a.asset_type_id', '=', 'at.asset_type_id')->where('s.ssl_id', $q->id)->get();

                                                                if (sizeof($line_items) > 0) {
                                                                    $html .= '    </div><div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Assigned Hosts</div>
                            
                                                          <div class="   position-relative i block-content pb-0   " id="commentBlock"> <div  class="js-task block block-rounded r ow  col-lg-12 animated fadeIn pt-3"><div class="row">';

                                                                    foreach ($line_items as $l) {


                                                                        $ip_array = explode(',', $l->ip_id);
                                                                        $ip = DB::Table('asset_ip_addresses')->whereIn('id', $ip_array)->orderby('ip_address_name', 'asc')->get();
                                                                        $cvm = '<p class="HostActive  text-white my-n1  "  >Ip Addresses</p><div class=   r  bg-dark">';
                                                                        if (@$ip_array[0] == '') {
                                                                            $cvm .= ' 
                                         <div class="w-100   align-items-center d-flex justify-content-between"> 
                                        <span class="HostActive mr-3 my-n1 text-orange     "   >  Primary </span>  <span class="ml-auto text-grey HostActive ">' . @$l->ip_address . ' </span>  
                                           </div>   
                                     
                                           ';
                                                                        }
                                                                        foreach ($ip as $i) {
                                                                            $cvm .= ' 
                                          <div class="w-100   d-flex  justify-content-between"> 
                                     
                                           <span class="HostActive my-n1 text-orange  mr-3  "  > ' . $i->ip_address_name . ' </span>   <span class="ml-auto  HostActive text-grey f ">' . @$i->ip_address_value . ' </span>  
                                              </div>
                                     

                                     ';
                                                                        }
                                                                        $cvm .= '</div>';
                                                                        $html .= '<div class="block block-rounded ml-2  table-block-new ">


<div class="d-flex block-content align-items-center px-2 py-2"><p class="font-11pt mr-1   mb-0  ' . ($l->asset_type == 'physical' ? 'c4-p' : 'c4-v') . ' " style="max-width:20px; " data="262">' . ($l->asset_type == 'physical' ? 'P' : 'V') . '</p><p class="font-12pt mb-0 text-truncate   c4" style="  background-color: rgb(151, 192, 255); color: rgb(89, 89, 89); border-color: rgb(89, 89, 89);" data="262">' . $l->fqdn . '</p>';
                                                                        $html .= "<img src='public/img/san.png' class='toggle pl-2' data-toggle='tooltip' data-trigger='hover' data-placement='top' data-title='$cvm' data-html='true' data-original-title=''   width='30px' style='object-fit:contain'  >";

                                                                        $html .= ' <a  class="dropdown-toggle ml-2"   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"   href="javascript:;" c>
                                <img src="public/img/dots.png?cache=1"   >
                                                                        </a>
                                         <div class="dropdown-menu py-0 pt-1 " aria-labelledby="dropdown-dropright-primary">
      
                  <a class="dropdown-item d-flex align-items-center px-0" target="_blank" href="print-asset?id=' . $l->id . '">   <div style="width: 32;  padding-left: 2px"><img src="public/img/open-icon-removebg-preview.png?cache=1" width="22px"  > &nbsp;&nbsp;View Asset</div></a>  
                 
                </div>


</div>

 
           </div>';




                                                                        //                                                               $html.='<div class="j col-sm-6 mb-3  " data-task-id="9" data-task-completed="false" data-task-starred="false">
                                                                        //                                                                 <div  class="inner-body-content">
                                                                        //                                                                     <div class="form-group px-3 row">
                                                                        //                                                         <div class="col-sm-12 pb-3 ">';
                                                                        //     if($c->AssetStatus!=1){
                                                                        //                                                                                       $html.='<div  title="" class="w-100   AssetInactive"  style=" box-shadow: 0px 2px 7px black;padding-top: 4px;padding-bottom: 4px;border: none;">'.$c->hostname.' <a href="'.url('print-asset').'?id='.$c->aid.'" target="_blank"><img src="public/img/icon-eye-grey.png?cache=1" class="float-right" style="    width: 29px;margin-right: 10px;"></a></div> ';
                                                                        //                                                                                 }else{
                                                                        //                                                                              $html.='<div data-toggle="tooltip" data-trigger="hover" data-html="true"   title="" class="w-100     AssetActive" style="padding-top: 5px;    box-shadow: 
                                                                        //        0px 1px 5px black;padding-bottom: 5px;border: none;">'.$c->hostname.' <a href="'.url('print-asset').'?id='.$c->aid.'" target="_blank"><img src="public/img/icon-eye-grey.png?cache=1" class="float-right" style="    width: 29px;margin-right: 10px;"></a></div>  ';
                                                                        //                                                                                 }
                                                                        //                                                          $html.='</div>';


                                                                        //                                        $ip_array=explode(',',$c->ip_id);
                                                                        //             $ip=DB::Table('asset_ip_addresses')->whereIn('id',$ip_array)->orderby('ip_address_name','asc')->get();

                                                                        //                                                 if(@$ip_array[0]==''){
                                                                        //                                           $html.='<div class="col-sm-5 mb-3">
                                                                        //                                            <div class="bubble-new">Primary</div> 
                                                                        //                                        </div>
                                                                        //                                             <div class="col-sm-7 mb-3">
                                                                        //                                                   <div class="bubble-white-new bubble-text-sec">
                                                                        //                                                         '.@$c->ip_address.'  
                                                                        //                                                 </div> 

                                                                        //                                             </div>';
                                                                        //                                                 }
                                                                        //                                                 foreach($ip as $i){
                                                                        //                                                    $html.='<div class="col-sm-5 mb-3">

                                                                        //                                            <div class="bubble-new">  '.$i->ip_address_name.'  </div> 
                                                                        //                                        </div>
                                                                        //                                             <div class="col-sm-7 mb-3">
                                                                        //                                                   <div class="bubble-white-new bubble-text-sec">
                                                                        //                                                         '.$i->ip_address_value.'  
                                                                        //                                                 </div> 

                                                                        //                                             </div>';
                                                                        //                                             }
                                                                        //                                          $html.='</div>

                                                                        // </div>
                                                                        //                                     </div>';



                                                                    }
                                                                    $html .= '</div></div>

                            </div>  </div>';

                                                                    $contract = DB::table('ssl_comments')->where('ssl_id', $q->id)->get();
                                                                    if (sizeof($contract) > 0) {
                                                                        $html .= '<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Comments</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock"> ';
                                                                        foreach ($contract as $c) {
                                                                            $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png?cache=1"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . nl2br($c->comment) . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
                                                                        }
                                                                        $html .= '</div>

                            </div>';
                                                                    }




                                                                    $contract = DB::table('ssl_attachments')->where('ssl_id', $q->id)->get();
                                                                    if (sizeof($contract) > 0) {
                                                                        $html .= '<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Attachments</div>
                            
                                                          <div class="block-content new-block-content  px-4 row" id="attachmentBlock"> ';
                                                                        foreach ($contract as $c) {

                                                                            $f = explode('.', $c->attachment);
                                                                            $fileExtension = end($f);
                                                                            $icon = 'attachment.png';
                                                                            if ($fileExtension == 'pdf') {
                                                                                $icon = 'attch-Icon-pdf.png';
                                                                            } else if ($fileExtension == 'doc' || $fileExtension == 'docx') {
                                                                                $icon = 'attch-word.png';
                                                                            } else if ($fileExtension == 'txt') {
                                                                                $icon = 'attch-word.png';
                                                                            } else if ($fileExtension == 'csv' || $fileExtension == 'xlsx' || $fileExtension == 'xlsm' || $fileExtension == 'xlsb' || $fileExtension == 'xltx') {
                                                                                $icon = 'attch-excel.png';
                                                                            } else if ($fileExtension == 'png'  || $fileExtension == 'gif' || $fileExtension == 'webp' || $fileExtension == 'svg') {
                                                                                $icon = 'attch-png icon.png';
                                                                            } else if ($fileExtension == 'jpeg' || $fileExtension == 'jpg') {
                                                                                $icon = 'attch-jpg-icon.png';
                                                                            } else if ($fileExtension == 'potx' || $fileExtension == 'pptx' || $fileExtension == 'ppsx' || $fileExtension == 'thmx') {
                                                                                $icon = 'attch-powerpoint.png';
                                                                            }



                                                                            $html .= '<div class="col-sm-12  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png?cache=1"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                          <h2 class="mb-0 comments-text">' . $c->name . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->date)) . ' at ' . date('h:i:s A', strtotime($c->date)) . ' GMT
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
 <a href="temp_uploads/' . $c->attachment . '" download target="_blank" class="text-truncate   attachmentDivNew comments-section-text"><img src="public/img/' . $icon . '"  width="25px"> &nbsp;<span class="text-truncate  " >' . substr($c->attachment, 0, 25) . '</span>
</a></p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>';
                                                                        }
                                                                        $html .= '</div>

                            </div>';
                                                                    }

                                                                    $contract = DB::table('ssl_audit_trail as c')->select('c.*', 'u.firstname', 'u.lastname')->leftjoin('users as u', 'u.id', '=', 'c.user_id')->where('c.ssl_id', $q->id)->get();


                                                                    if (sizeof($contract) > 0) {
                                                                        $html .= '<div class="block new-block position-relative mt-5" >
                                                <div class="top-div text-capitalize">Audit Trial</div>
                            
                                                          <div class="block-content new-block-content" id="commentBlock">';
                                                                        foreach ($contract as $c) {
                                                                            $html .= '<div class="js-task block block-rounded mb-2 animated fadeIn" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="public/img/profile-white.png?cache=1"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">' . $c->firstname . ' ' . $c->lastname . '<br><span class="comments-subtext">On ' . date('Y-M-d', strtotime($c->created_at)) . ' at ' . date('h:i:s A', strtotime($c->created_at)) . ' GMT
</span></h2>
                                                    </td>
                                                    
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ' . $c->description . '
</p>
                                                    </td>
                                                  
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>';
                                                                        }
                                                                        $html .= '</div>

                            </div>';
                                                                    }




                                                                    $html .= '
    </div>


                    </div>



                </div>
               </div>
       </div>';
                                                                }
                                                                return response()->json($html);
                                                            }
                                                        }
