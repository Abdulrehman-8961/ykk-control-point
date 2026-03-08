<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Auth;
use DB;
use Mail;
use Hash;
use PDF;
 use VerumConsilium\Browsershot\Facades\PDF as NEWPDF;
  
 use Validator;
class AdminController extends Controller
{
    //
public function __construct(){
     
      
     }
   
    public function index(){
 
     return view('dashboard');
 
    }
     public function changePassword(){
 
     return view('changePassword');
 
    }
 

 
    
}