  @extends('layouts.header')
  @extends('layouts.sidebar')
  @extends('layouts.footer')
  @section('content')
      <?php

      if (Auth::user()->role == 'read') {
          echo 'You dont have access';
          exit();
      }
      $page_type = $type == 'support' ? '' : $type;
      ?>

      <style type="text/css">
          .contract_type_button label,
          .contract_type_button input {}

          .contract_type_button {
              float: left;
          }

          .contract_type_button input[type="radio"] {
              opacity: 0.011;
              z-index: 100;

              position: absolute;
          }

          .contract_type_button input[type="radio"]:checked+label {
              background: #4194F6;
              font-weight: bold;
              color: white;
          }

          .contract_type_button label:hover {



              background-color: #EEEEEE;
              color: #7F7F7F;


          }

          .contract_type_button label {

              width: 150px;

              border-color: #D9D9D9;
              color: #7F7F7F;
              font-size: 12pt;


          }

          .modal-backdrop {
              background-color: #00000080 !important;
          }

          .alert-info,
          .alert {

              width: auto !important;
              padding-right: 70px;
              background-color: #262626 !important;
              top: 75px !important;
              right: 50px !important;
              color: #FFFFFF !important;
              font-family: Calibri !important;
              font-size: 14pt !important;
              padding-top: 14px;
              padding-bottom: 14px;
              z-index: 11000 !important;
          }

          .attachmentDivNew:hover {
              color: #FFFFFF !important;
              background-color: #4194F6;
          }

          .alert-info .close {
              color: #898989 !important;
              font-size: 30px !important;
              top: 10px !important;
              right: 15px !important;
              opacity: 1 !important;
              font-weight: 200 !important;
              width: 33px;
              padding-bottom: 3px;
          }

          .alert-info .close:hover {
              background-color: white !important;
              border-radius: 50%;
          }

          .alert-info .btn-tooltip {
              color: #00B0F0 !important;
              font-family: Calibri !important;
              font-size: 14pt !important;
              font-weight: bold !important;
          }

          .btn-notify {
              color: #00B0F0;
              font-family: Calibri;
              font-size: 14pt;
              font-weight: bold;
              padding: 5px 13px;
              font-weight: bold;
              border-radius: 7px;
          }

          .btn-link {

              padding: 0px;
              margin: .25rem .5rem;
          }

          .btn-link:hover {
              box-shadow: -1px 2px 4px 3px #99dff9;
              background: #99dff9;
          }

          .btn-notify:hover {
              color: #00B0F0;
              background: #386875;

          }

          .btnDeleteAttachment {
              position: absolute;
              right: 2px;
              top: 6px;

          }

          @media only screen and (min-width: 1000px) {
              .blockfooter {
                  position: fixed !important;
                  bottom: 3px !important;
                  left: 249px !important;
                  z-index: 1000 !important;

              }
          }

          .dropdown-menu {
              max-height: 400px !important;
          }

          #page-header {
              display: none;
          }
      </style>
      <!-- Main Container -->
      <main id="main-container  " style="padding:3mm">
          <!-- Hero -->

          <div class="block card-round   bg-new-blue new-nav">
              <div class="block-header   py-new-header">
                  <div class="d-flex align-items-center w-100  " style="justify-content:space-between;">
                      <div class="d-flex">
                          <img src="{{ asset('public/img/contract.jpg') }}" width="40px">
                          <div class="ml-4">
                              <h4 class="mb-0 header-new-text" style="line-height:25px">New Contract</h4>
                              <p class="mb-0  header-new-subtext" style="line-height:15px"><?php echo date('Y-M-d') . ' at ' . date('H:i:s') . ' GMT'; ?> by
                                  {{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}</p>
                          </div>
                      </div>
                      <div class="new-header-icon-div">
                          <span data-toggle="modal" data-bs-target="#AttachmentModal" data-target="#AttachmentModal">
                              <a href="javascript:;" id="AddAttachment" data-toggle="tooltip" data-trigger="hover"
                                  data-placement="top" title="" data-original-title="Add Attachment"><img
                                      src="{{ asset('public/img/paper-clip-white.png') }}" width="20px"></a>
                          </span>
                          <span data-toggle="modal" data-bs-target="#CommentModal" data-target="#CommentModal">
                              <a href="javascript:;" data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                  title="" data-original-title="Add Comment"><img
                                      src="{{ asset('public/img/comment-white.png') }}" width="20px"></a>
                          </span>
                          <a href="javascript:;" class="saveContract text-white" data-toggle="tooltip" data-trigger="hover"
                              data-placement="top" title="" data-original-title="Add and Continue"
                              class="text-white"><i class="fa fa-plus texti-white"></i> </a>

                          <a href="javascript:;" class="text-white saveContract" data="0" data-toggle="tooltip"
                              data-trigger="hover" data-placement="top" title=""
                              data-original-title="Save Contract"><i class="fa fa-check text-white"></i> </a>
                          <a data-toggle="tooltip" data-trigger="hover" data-placement="top" title=""
                              data-original-title="Close" href="javascript:;" class="text-white btnClose"><i
                                  class="fa fa-times texti-white"></i> </a>
                      </div>
                  </div>
              </div>
          </div>
          <!-- Page Content -->
          <div class="content content-full  -boxed" style="    padding-left: 15mm;
    padding-right: 15mm;">
              <!-- New Post -->
              <form id="form-1" action="{{ url('insert-contract') }}" class="js-validation   " method="POST"
                  enctype="multipart/form-data">
                  @csrf

                  <div class="block new-block">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">General Information
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content pb-0 new-block-content">

                          <div class="row justify-content-  push">

                              <div class="col-sm-12 m-
                            ">

                                  <input type="hidden" name="attachment_array" id="attachment_array">
                                  <div class="row">

                                      <div class="col-sm-12">
                                          <div class="form-group row">
                                              <label class="col-sm-2 col-form-label mandatory"
                                                  for="example-hf-client_id">Client</label>
                                              <?php

                                              $userAccess = explode(',', Auth::user()->access_to_client);

                                              if (Auth::user()->role == 'admin') {
                                                  $client = DB::Table('clients')->where('is_deleted', 0)->where('client_status', 1)->orderBy('firstname', 'asc')->get();
                                              } else {
                                                  $client = DB::Table('clients')->whereIn('id', $userAccess)->where('is_deleted', 0)->where('client_status', 1)->orderBy('firstname', 'asc')->get();
                                              } ?>
                                              <div class="col-sm-5">
                                                  <select type="client_id" class="form-control select2" id="client_id"
                                                      value="" name="client_id" placeholder="Client">
                                                      <option value="" data-logo="" data-email="" data-address="">
                                                      </option>
                                                      @foreach ($client as $c)
                                                          <option value="{{ $c->id }}"
                                                              data-logo="{{ $c->logo }}"
                                                              data-email="{{ $c->email_address }}"
                                                              data-renewal_notification_email="{{ $c->renewal_notification_email }}"
                                                              data-address="{{ nl2br($c->client_address) }}">
                                                              {{ $c->firstname }}</option>
                                                      @endforeach
                                                  </select>
                                              </div>

                                          </div>

                                          <div class="form-group row">
                                              <label class="col-sm-2 col-form-label mandatory"
                                                  for="example-hf-client_id">Site</label>

                                              <div class="col-sm-5">
                                                  <select type="" class="form-control select2" id="site_id"
                                                      value="" name="site_id">
                                                      <option value=""></option>

                                                  </select>
                                              </div>

                                          </div>
                                      </div>


                                  </div>

                                  <div class=" row mb-3">
                                      <label class="col-sm-2 col-form-label" for="example-hf-email">Email
                                          Notifications</label>
                                      <div class="col-sm-6">
                                          <div
                                              class="custom-control custom-switch custom-control-warning custom-control-lg mt-2 ">
                                              <input type="checkbox" class="custom-control-input" id="cert_notification"
                                                  name="contract_notification" value="1" checked="">
                                              <label class="custom-control-label" for="cert_notification"> </label>
                                          </div>
                                      </div>

                                  </div>




                                  <!--      <div class="form-group p row">
                                                <label class="col-sm-3 col-form-label" for="example-hf-email"><br><small><a href="javascript:;" class="add-more">(Upto 5)</a></small></label>

                                                <div class="col-sm-3">
          <input type="email" class="form-control" id="renewal_notification_email"   name="notification_renewal_email[]"  placeholder="Email 1"  >
                                                </div>



                                            </div> -->
                                  <div id="EmailBlock" class="EmailHide">


                                  </div>

                                  <button type="button" data-toggle="modal" data-target="#EmailModal"
                                      class="btn EmailHide ml-5 mt-3 btn-new ">Add Email Address</button>

                              </div>
                          </div>
                      </div>
                  </div>


                  <div class="block new-block">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">Contract Information
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content new-block-content">




                          <div class="row form-group ">
                              <label class="col-sm-2  mandatory col-form-label" for="example-hf-email">Description
                              </label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" req id="contract_description"
                                      name="contract_description" placeholder=" ">
                              </div>
                          </div>

                          <div class="form-group row">
                              <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Type </label>
                              <div class="col-sm-8">
                                  <div class="form-group  ">



                                      <div class="contract_type_button ">
                                          <input type="radio" id="a75" name="contract_type" checked
                                              value="Software Support" />
                                          <label class="btn btn-new " for="a75"
                                              {{ $type == 'support' ? 'checked' : '' }}>Software Support</label>
                                      </div>
                                      <div class="contract_type_button">
                                          <input type="radio" id="a50" name="contract_type"
                                              value="Hardware Support" />
                                          <label class="btn btn-new ml-5" for="a50"
                                              {{ $type == 'hardware' ? 'checked' : '' }}>Hardware Support</label>
                                      </div>

                                      <div class="contract_type_button">
                                          <input type="radio" id="a25" name="contract_type"
                                              value="Subscription" />
                                          <label class="btn btn-new ml-5" for="a25"
                                              {{ $type == 'subscription' ? 'checked' : '' }}>Subscription</label>
                                      </div>
                                      <!--     <select type="text" class="form-control"  id="contract_type" name="contract_type" placeholder=" "  >
                                                        <option value=""> </option>

                                                        <option value="Subscription" {{ $type == 'Subscription' ? 'selected' : '' }}>Subscription</option>
                                                        <option value="Hardware Support" {{ $type == 'Hardware Support' ? 'selected' : '' }}>Hardware Support</option>
                                                        <option value="Software Support" {{ $type == 'Software Support' ? 'selected' : '' }}>Software Support</option>

                                                     </select>  -->
                                  </div>
                              </div>
                          </div>
                          <div class="form-group row">
                              <label class="col-sm-2 col-form-label mandatory" for="example-hf-client_id">Vendor</label>
                              <?php
                              $vendor = DB::Table('vendors')->where('is_deleted', 0)->orderBy('vendor_name', 'asc')->get();
                              ?>
                              <div class="col-sm-5">
                                  <select type="" class="form-control select2" id="vendor_id" value=""
                                      name="vendor_id">
                                      <option value="" data-logo=""> </option>
                                      @foreach ($vendor as $c)
                                          <option value="{{ $c->id }}" data-logo="{{ $c->vendor_image }}">
                                              {{ $c->vendor_name }}</option>
                                      @endforeach
                                  </select>
                              </div>

                          </div>






                          <div class="form-group row">
                              <label class="col-sm-2 mandatory col-form-label" for="example-hf-email">Contract / Ref
                                  #</label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" id="contract_no" name="contract_no"
                                      placeholder="">
                              </div>
                          </div>
                          <div class="row form-group ">
                              <label class="col-sm-2 mandatory  col-form-label" for="example-hf-email">End User
                                  Email</label>
                              <div class="col-sm-10">
                                  <input type="email" class="form-control" id="registered_email"
                                      name="registered_email" placeholder="">
                              </div>
                          </div>
                          <div class="row form-group ">
                              <label class="col-sm-2 col-form-label mandatory" for="example-hf-email">Start Date</label>
                              <div class="col-sm-4  ">

                                  <input type="text" class="js-flatpickr form-control bg-white"
                                      id="contract_start_date" value="{{ date('Y-m-d') }}" name="contract_start_date"
                                      placeholder="" data-alt-input="true" data-date-format="Y-m-d"
                                      data-alt-format="Y-M-d">
                              </div>

                              <label class="col-sm-2 col-form-label text-md-center mandatory  " for="example-hf-email">End
                                  Date</label>
                              <div class="col-sm-4   ">

                                  <input type="text" class="  form-control bg-white" id="contract_end_date"
                                      name="contract_end_date" value="{{ date('Y-m-d', strtotime(' + 1 year')) }}"
                                      placeholder="" data-alt-input="true" data-date-format="Y-m-d"
                                      data-alt-format="Y-M-d">
                              </div>

                          </div>
                          <button type="button" class="btn ml-5 btn-new mt-4 " data-toggle="modal"
                              data-target="#ContractModal">Add detail line data to contract</button>


                          <!--     <div class="form-group row">
                                                <label class="col-sm-3 col-form-label   " for="example-hf-email">Comment</label>
                                                <div class="col-sm-3  ">
                                                     <textarea type="text" class="form-control" rows="8" id="comments" name="comments" placeholder=""></textarea>
                                                </div>
                                                <label class="col-sm-2 col-form-label text-md-right" for="example-hf-email">Attach Contract</label>
                                                <div class="col-sm-3">
                                                     <input     type="file" class="  attachment"  multiple="" style="" id="attachment" name="attachment" placeholder=""  >
                                                </div>

                                            </div>
                         -->
                      </div>
                  </div>

                  <div class="block new-block  contractDetailsDiv d-none">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">Contract Details
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content new-block-content" id="contractDetailsBlock">






                      </div>
                  </div>

                  <div class="block new-block">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">Distribution
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content new-block-content">




                          <div class="form-group row">
                              <label class="col-sm-2 col-form-label" for="example-hf-client_id">Distributor</label>
                              <?php
                              $distributors = DB::Table('distributors')->where('is_deleted', 0)->orderBy('distributor_name', 'asc')->get();
                              ?>
                              <div class="col-sm-5">
                                  <select type="" class="form-control select2" id="distributor_id" value=""
                                      name="distributor_id">
                                      <option value="" data-logo=""> </option>
                                      @foreach ($distributors as $c)
                                          <option value="{{ $c->id }}" data-logo="{{ $c->distributor_image }}">
                                              {{ $c->distributor_name }}</option>
                                      @endforeach
                                  </select>
                              </div>

                          </div>

                          <div class="form-group row">
                              <label class="col-sm-2 col-form-label" for="example-hf-email">Reference #</label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" id="reference_no" name="reference_no"
                                      placeholder=" ">
                              </div>

                          </div>


                          <div class="form-  row">
                              <label class="col-sm-2 col-form-label" for="example-hf-email">Sales Order #</label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" id="distrubutor_sales_order_no"
                                      name="distrubutor_sales_order_no" placeholder=" ">
                              </div>

                          </div>






                      </div>
                  </div>



                  <div class="block new-block">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">Purchasing
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content new-block-content">







                          <div class="form-group row">
                              <label class="col-sm-2 col-form-label" for="example-hf-email">Estimate #</label>
                              <div class="col-sm-4">
                                  <input type="text" class="form-control" id="estimate_no" name="estimate_no"
                                      placeholder=" ">
                              </div>


                              <label class="col-sm-2 col-form-label text-md-center" for="example-hf-email">Sales Order
                                  #</label>
                              <div class="col-sm-4">
                                  <input type="text" class="form-control" id="sales_order_no" name="sales_order_no"
                                      placeholder="">
                              </div>

                          </div>

                          <div class="row">
                              <label class="col-sm-2 col-form-label" for="example-hf-email">Invoice #</label>
                              <div class="col-sm-4 form-group ">
                                  <input type="text" class="form-control" id="invoice_no" name="invoice_no"
                                      placeholder="">
                              </div>
                              <label class="col-sm-2 col-form-label text-md-center  t  " style="margin-left: -5px;"
                                  for="example-hf-email">Invoice Date</label>
                              <div class="col-sm-4 form-group " style="margin-left: 5px;">

                                  <input type="text" class="js-flatpickr form-control bg-white" id="invoice_date"
                                      value="{{ date('Y-m-d') }}" name="invoice_date" placeholder="Y-M-d"
                                      data-alt-input="true" data-date-format="Y-m-d" data-alt-format="Y-M-d">



                              </div>

                          </div>
                          <div class="row">
                              <label class="col-sm-2 col-form-label" for="example-hf-email">PO #</label>
                              <div class="col-sm-4 form-  ">
                                  <input type="text" class="form-control" id="po_no" name="po_no"
                                      placeholder="">
                              </div>
                              <label class="col-sm-2 col-form-label  text-md-center   ht" style="margin-left: -20px;"
                                  for="example-hf-email">PO Date</label>
                              <div class="col-sm-4 form-  " style="margin-left:20px">

                                  <input type="text" class="js-flatpickr form-control bg-white" id="po_date"
                                      value="{{ date('Y-m-d') }}" name="po_date" placeholder="Y-M-d"
                                      data-alt-input="true" data-date-format="Y-m-d" data-alt-format="Y-M-d">
                              </div>

                          </div>

                      </div>
                  </div>


                  <div class="block new-block  commentDiv d-none ">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">Comments
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content new-block-content" id="commentBlock">



                      </div>
                  </div>


                  <div class="block new-block attachmentDiv d-none   ">

                      <div class="block-header py-0" style="padding-left:7mm;">

                          <a class="  section-header">Attachments
                          </a>

                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content new-block-content row" id="attachmentBlock">



                      </div>

                      <!--
                                    <div class="block-content bg-body-light blockfooter">
                                    <div class="row justify-content-center push">
                                        <div class="col-md-10">

                                               <button type="submit" class="btn btn-alt-success">
                                                <i class="fa fa-fw fa-check ml-1"></i> Save
                                            </button>
                                               <button type="submit" name="saveAndClose" value="{{ URL::previous() == URL::current() ? url('contract/' . $page_type . '') : URL::previous() }}" class="btn btn-alt-success">
                                                <i class="fa fa-fw fa-check ml-1"></i> Save And Close
                                            </button>
                                                <a   type="reset" class="btn btn-alt-danger">
                                                <i class="fa fa-fw fa-times  "></i> Cancel

                                            </a>



                                        </div>
                                    </div>
                                </div> -->
                  </div>
              </form>
              <!-- END New Post -->
          </div>
          <!-- END Page Content -->
      </main>
      <!-- END Main Container -->


      <div class="modal fade" id="EmailModal" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header  ">
                          <span class="b e section-header">Add Email Address</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content row">


                          <div class="col-sm-3   form-group      ">
                              <label class="mandatory">Email Address</label>

                          </div>
                          <div class="col-sm-9   form-group      ">
                              <input class="form-control  " required="" name="email_address">

                          </div>


                      </div>
                      <div class="block-content block-content-full   " style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="EmailSave">Save</button>
                          <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>

      <div class="modal fade" id="EmailModalEdit" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header  ">
                          <span class="b e section-header">Edit Email Address</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content row">

                          <input type="hidden" name="email_id_edit">
                          <div class="col-sm-3   form-group      ">
                              <label class="mandatory">Email Address</label>

                          </div>
                          <div class="col-sm-9   form-group      ">
                              <input class="form-control  " required="" name="email_address_edit">

                          </div>


                      </div>
                      <div class="block-content block-content-full   " style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="EmailSaveEdit">Save</button>
                          <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>

      <div class="modal fade" id="CommentModal" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header   ">
                          <span class="b e section-header">Add Comments</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content pt-0 row">



                          <div class="col-sm-12    p      ">
                              <textarea class="form-control  " rows="4" required="" name="comment"></textarea>

                          </div>


                      </div>
                      <div class="block-content block-content-full   " style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="CommentSave">Save</button>
                          <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>

      <div class="modal fade" id="CommentModalEdit" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header  ">
                          <span class="b e section-header">Edit Comments</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content pt-0 row">

                          <input type="hidden" name="comment_id_edit">

                          <div class="col-sm-12      ">
                              <textarea class="form-control  " rows="4" required="" name="comment_edit"></textarea>

                          </div>


                      </div>
                      <div class="block-content block-content-full   " style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="CommentSaveEdit">Save</button>
                          <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>





      <div class="modal fade" id="ContractModal" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header pb-0  ">
                          <span class="b e section-header">Add Line</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content new-block-content pt-0 pb-0 ">


                          <div class="row">
                              <div class="col-sm-2   form-group      ">
                                  <label class="mandatory">PN #</label>

                              </div>
                              <div class="col-sm-3   form-group      ">
                                  <input class="form-control  " name="pn_no">

                              </div>

                              <div class="col-sm-1   form-group      ">
                                  <label class="mandatory">Qty</label>

                              </div>
                              <div class="col-sm-2   form-group      ">
                                  <input class="form-control  " type="number" required="" name="qty">

                              </div>
                              <div class="col-sm-1   form-group   text-center   ">
                                  <label class="mandatory">MSRP</label>

                              </div>
                              <div class="col-sm-3   form-group      ">
                                  <input type="number" step="any" class="form-control  " name="msrp">

                              </div>
                          </div>

                          <div class="row">
                              <div class="col-sm-2   form-group      ">
                                  <label class=" ">Asset(s)
                                  </label>

                              </div>
                              <div class="col-sm-10   form-group      ">
                                  <select class="form-control multiple  selectpicker" multiple="" required=""
                                      data-live-search="true" id="hostname_modal" name="hostname_modal[]">

                                  </select>

                              </div>
                          </div>

                          <div class="row">
                              <div class="col-sm-2   form-group      ">
                                  <label class="mandatory">Description</label>

                              </div>
                              <div class="col-sm-10   form-group      ">
                                  <textarea rows="4" class="form-control  " name="asset_description"></textarea>

                              </div>
                          </div>

                      </div>
                      <div class="block-content block-content-full   pt-1" style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="contractDetailsSave">Save</button>
                          <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>




      <div class="modal fade" id="ContractModalEdit" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header pb-0  ">
                          <span class="b e section-header">Edit Line</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content new-block-content pt-0 pb-0 ">

                          <input type="hidden" name="contract_id_edit">

                          <div class="row">
                              <div class="col-sm-2   form-group      ">
                                  <label class="mandatory">PN #</label>

                              </div>
                              <div class="col-sm-3   form-group      ">
                                  <input class="form-control  " name="pn_no_edit">

                              </div>

                              <div class="col-sm-1   form-group      ">
                                  <label class="mandatory">Qty</label>

                              </div>
                              <div class="col-sm-2   form-group      ">
                                  <input class="form-control  " type="number" required="" name="qty_edit">

                              </div>
                              <div class="col-sm-1   form-group   text-center   ">
                                  <label class="mandatory">MSRP</label>

                              </div>
                              <div class="col-sm-3   form-group      ">
                                  <input type="number" step="any" class="form-control  " name="msrp_edit">

                              </div>
                          </div>

                          <div class="row">
                              <div class="col-sm-2   form-group      ">
                                  <label class=" ">Asset(s)
                                  </label>

                              </div>
                              <div class="col-sm-10   form-group      ">
                                  <select class="form-control multiple  selectpicker" multiple="" required=""
                                      data-live-search="true" id="hostname_modal_edit" name="hostname_modal_edit[]">

                                  </select>

                              </div>
                          </div>

                          <div class="row">
                              <div class="col-sm-2   form-group      ">
                                  <label class="mandatory">Description</label>

                              </div>
                              <div class="col-sm-10   form-group      ">
                                  <textarea rows="4" class="form-control  " name="asset_description_edit"></textarea>

                              </div>
                          </div>

                      </div>
                      <div class="block-content block-content-full   pt-1" style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="contractDetailsSaveEdit">Save</button>
                          <button type="button" class="btn     btn-new-secondary" data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>




      <div class="modal fade" id="AttachmentModal" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header   ">
                          <span class="b e section-header">Add Attachment</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="fa fa-fw fa-times"></i>
                                    </button> -->
                          </div>
                      </div>

                      <div class="block-content pt-0 row">



                          <div class="col-sm-12    p      ">
                              <input type="file" class="  attachment" multiple="" style="" id="attachment"
                                  name="attachment" placeholder="">
                          </div>


                      </div>
                      <div class="block-content block-content-full   " style="padding-left: 9mm;">
                          <button type="submit" class="btn mr-3 btn-new" id="AttachmentSave">Save</button>
                          <button type="button" class="btn     btn-new-secondary" id="AttachmentClose"
                              data-dismiss="modal">Close</button>

                      </div>
                  </div>

              </div>
          </div>
      </div>
  @endsection('content')

  <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
      crossorigin="anonymous"></script>
  <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>


  <!-- Page JS Helpers (BS Datepicker + BS Colorpicker plugins) -->

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script type="text/javascript" defer="" src="{{ asset('public/js/jquery.repeater.js') }}"></script>


  <script type="text/javascript">
      $(function() {

          FilePond.registerPlugin(

              FilePondPluginImagePreview,
              FilePondPluginImageExifOrientation,
              FilePondPluginFileValidateSize,
              FilePondPluginImageEdit,
              FilePondPluginFileValidateType
          );


          $('#contract_end_date').flatpickr()

          var content3_image = [];


          var attachments_file = [];

          let filePond = FilePond.create(
              document.querySelector('.attachment'), {
                  name: 'attachment',
                  allowMultiple: true,
                  allowImagePreview: true,

                  imagePreviewFilterItem: false,
                  imagePreviewMarkupFilter: false,

                  dataMaxFileSize: "2MB",



                  // server
                  server: {
                      process: {
                          url: '{{ url('uploadContractAttachment') }}',
                          method: 'POST',
                          headers: {
                              'x-customheader': 'Processing File'
                          },
                          onload: (response) => {

                              response = response.replaceAll('"', '');
                              content3_image.push(response);

                              var attachemnts = $('#attachment_array').val()
                              var attachment_array = attachemnts.split(',');
                              attachment_array.push(response);
                              $('#attachment_array').val(attachment_array.join(','));

                              return response;

                          },
                          onerror: (response) => {



                              return response
                          },
                          ondata: (formData) => {
                              window.h = formData;

                              return formData;
                          }
                      },
                      revert: (uniqueFileId, load, error) => {

                          const formData = new FormData();
                          formData.append("key", uniqueFileId);

                          content3_image = content3_image.filter(function(ele) {
                              return ele != uniqueFileId;
                          });

                          var attachemnts = $('#attachment_array').val()
                          var attachment_array = attachemnts.split(',');
                          attachment_array = attachment_array.filter(function(ele) {
                              return ele != uniqueFileId;
                          });

                          $('#attachment_array').val(attachment_array.join(','));


                          fetch(`{{ url('revertContractAttachment') }}?key=${uniqueFileId}`, {
                                  method: "DELETE",
                                  body: formData,
                              })
                              .then(res => res.json())
                              .then(json => {
                                  console.log(json);


                                  // Should call the load method when done, no parameters required

                                  load();

                              })
                              .catch(err => {
                                  console.log(err)
                                  // Can call the error method if something is wrong, should exit after
                                  error(err.message);
                              })
                      },



                      remove: (uniqueFileId, load, error) => {
                          // Should somehow send `source` to server so server can remove the file with this source
                          content3_image = content3_image.filter(function(ele) {
                              return ele != uniqueFileId;
                          });


                          // Should call the load method when done, no parameters required
                          load();
                      },

                  }
              }
          );






          @if (Session::has('success'))

              Dashmix.helpers('notify', {
                  type: 'success',
                  icon: 'fa fa-check mr-1',
                  message: '{{ Session::get('success') }}'
              });
          @endif





          function unEntity(str) {
              return str.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
          }




          // EMAIL ARRAY

          var emailArray = [];
          var email_key_count = 0;
          $('#EmailSave').click(function() {
              var email = $('input[name=email_address]').val();
              if (email == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Email Address',
                      delay: 5000
                  });

              } else {

                  var l = emailArray.length;
                  if (l < 5) {
                      emailArray.push({
                          key: email_key_count,
                          email: email
                      });
                      showEmail()
                      $('#EmailModal').modal('hide')
                      $('input[name=email_address]').val('')
                      email_key_count++;
                  }
              }
          })


          $('#EmailSaveEdit').click(function() {
              var email = $('input[name=email_address_edit]').val();
              var id = $('input[name=email_id_edit]').val();
              if (email == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Email Address',
                      delay: 5000
                  });

              } else {

                  var l = emailArray.length;

                  emailArray[id].email = email;
                  showEmail()
                  $('#EmailModalEdit').modal('hide')
                  $('input[name=email_address_edit]').val('')

              }
          })

          $(document).on('click', '.btnEditEmail', function() {
              var id = $(this).attr('data');
              $('#EmailModalEdit').modal('show');
              $('input[name=email_id_edit]').val(id);
              $('input[name=email_address_edit]').val(emailArray[id].email);

          })
          var temp_email = [];
          $(document).on('click', '.btnDeleteEmail', function() {
              var id = $(this).attr('data');
              var key = emailArray[id].key;
              temp_email.push(emailArray[id]);

              emailArray.splice(id, 1);

              Dashmix.helpers('notify', {
                  align: 'center',
                  message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Email address Deleted. <a href="javascript:;" class="  btn-notify btnEmailUndo ml-4" data1=' +
                      id + ' data=' + key + '>Undo</a>',
                  delay: 5000
              });
              showEmail();

          })

          $(document).on('click', '.btnEmailUndo', function() {
              var id = $(this).attr('data');
              var key = $(this).attr('data1');

              let index = temp_email.filter(l => l.key == id);

              if (index[0]) {
                  emailArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only
                  temp_email = temp_email.filter(l => l.key != id);



                  showEmail();
              }
          })


          $('#cert_notification').change(function() {
              if ($(this).prop('checked') == 1) {
                  $('.EmailHide').removeClass('d-none')
              } else {
                  $('.EmailHide').addClass('d-none')
              }
          })

          function showEmail() {
              var html = '';
              for (var i = 0; i < emailArray.length; i++) {
                  html += `   <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-2" style=""><b>@</b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <label class="mb-0">${emailArray[i].email}</label>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                        <a type="button"  data="${i}" data-toggle="tooltip" data-trigger="hover"  data-placement="top" title="" data-original-title="Edit" class="btnEditEmail  btn btn-sm btn-link text-warning">
                                                         <img src="{{ url('public/img/editing.png') }}">
                                                        </a>
                                                        <a type="button"   data="${i}" class="j e btn btn-sm btn-link btnDeleteEmail text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/img/trash--v1.png') }}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
              }

              $('#EmailBlock').html(html)
          }

          // END EMAIL




          // Comment ARRAY

          var commentArray = [];
          var comment_key_count = 0;
          $('#CommentSave').click(function() {
              var comment = $('textarea[name=comment]').val();
              if (comment == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Comment',
                      delay: 5000
                  });

              } else {

                  var l = commentArray.length;
                  if (l < 5) {
                      commentArray.push({
                          key: comment_key_count,
                          comment: comment,
                          date: '{{ date('Y-M-d') }}',
                          time: '{{ date('h:i:s A') }}',
                          name: '{{ Auth::user()->firstname . '' . Auth::user()->lastname }}'
                      });
                      showComment()
                      $('#CommentModal').modal('hide')
                      $('textarea[name=comment]').val('')
                      comment_key_count++;
                  }
              }
          })


          $('#CommentSaveEdit').click(function() {
              var comment = $('textarea[name=comment_edit]').val();
              var id = $('input[name=comment_id_edit]').val();
              if (comment == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Comment',
                      delay: 5000
                  });

              } else {

                  var l = commentArray.length;

                  commentArray[id].comment = comment;
                  showComment()
                  $('#CommentModalEdit').modal('hide')
                  $('textarea[name=comment_edit]').val('')

              }
          })

          $(document).on('click', '.btnEditComment', function() {
              var id = $(this).attr('data');
              $('#CommentModalEdit').modal('show');
              $('input[name=comment_id_edit]').val(id);
              $('textarea[name=comment_edit]').val(commentArray[id].comment);

          })
          var temp_comment = [];
          $(document).on('click', '.btnDeleteComment', function() {
              var id = $(this).attr('data');
              var key = commentArray[id].key;
              temp_comment.push(commentArray[id]);

              commentArray.splice(id, 1);

              Dashmix.helpers('notify', {
                  align: 'center',
                  message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Comment Deleted. <a href="javascript:;" class="  btn-notify btnCommentUndo ml-4" data1=' +
                      id + ' data=' + key + '>Undo</a>',
                  delay: 5000
              });
              showComment();

          })

          $(document).on('click', '.btnCommentUndo', function() {
              var id = $(this).attr('data');
              var key = $(this).attr('data1');

              let index = temp_comment.filter(l => l.key == id);

              if (index[0]) {
                  commentArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only
                  temp_comment = temp_comment.filter(l => l.key != id);



                  showComment();
              }
          })

          function showComment() {
              var html = '';
              if (commentArray.length > 0) {
                  $('.commentDiv').removeClass('d-none');
              } else {
                  $('.commentDiv').addClass('d-none');
              }
              for (var i = 0; i < commentArray.length; i++) {
                  html += `    <div class="js-task block block-rounded mb-2 animated fadeIn"   data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{ asset('public/img/profile-white.png') }}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${commentArray[i].name}<br><span class="comments-subtext">On ${commentArray[i].date} at ${commentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                         <a type="button"  data="${i}" class="j btnEditComment btn btn-sm btn-link text-warning">
                                                         <img src="{{ url('public/img/editing.png') }}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button"   data="${i}" class="btnDeleteComment btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/img/trash--v1.png') }}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-0">
                                                       <p class="px-4 mb-0 comments-section-text">  ${commentArray[i].comment.replace(/\r?\n/g, '<br />')}
</p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
              }

              $('#commentBlock').html(html)
          }

          // END Comment







          // Contract Details ARRAY

          var contractDetailsArray = [];
          var contract_key_count = 0;
          $('#contractDetailsSave').click(function() {
              var asset_description = $('textarea[name=asset_description]').val();
              var pn_no = $('input[name=pn_no]').val();
              var qty = $('input[name=qty]').val();
              var msrp = $('input[name=msrp]').val();
              var hostname_modal = $('#hostname_modal').val();


              if (pn_no == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1">  Please enter a value for Pn #',
                      delay: 5000
                  });

              } else if (qty == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Qty',
                      delay: 5000
                  });

              } else if (msrp == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for MSRP',
                      delay: 5000
                  });

              } else if (asset_description == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please select Description',
                      delay: 5000
                  });

              } else {

                  var l = contractDetailsArray.length;

                  var asset_array = [];
                  $.each($('#hostname_modal option:selected'), function() {
                      asset_array.push($(this).attr('data-hostname'));
                  })


                  contractDetailsArray.push({
                      key: contract_key_count,
                      pn_no: pn_no,
                      qty: qty,
                      msrp: msrp,
                      hostname_modal: hostname_modal,
                      asset_description: asset_description,
                      asset_array: asset_array
                  });
                  showcontractDetails()
                  $('#ContractModal').modal('hide')
                  $('textarea[name=asset_description]').val('')
                  $('input[name=pn_no]').val('')
                  $('input[name=qty]').val('')
                  $('input[name=msrp]').val('')
                  $('#hostname_modal').val('')
                  $('#hostname_modal').selectpicker('refresh')
                  contract_key_count++;
              }

          })


          $('#contractDetailsSaveEdit').click(function() {
              var asset_description = $('textarea[name=asset_description_edit]').val();
              var pn_no = $('input[name=pn_no_edit]').val();
              var qty = $('input[name=qty_edit]').val();
              var msrp = $('input[name=msrp_edit]').val();
              var hostname_modal = $('#hostname_modal_edit').val();



              var id = $('input[name=contract_id_edit]').val();
              if (pn_no == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Pn #',
                      delay: 5000
                  });

              } else if (qty == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Qty',
                      delay: 5000
                  });

              } else if (msrp == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for MSRP',
                      delay: 5000
                  });

              } else if (asset_description == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please select Description',
                      delay: 5000
                  });

              } else {

                  var l = contractDetailsArray.length;

                  contractDetailsArray[id].asset_description = asset_description;
                  contractDetailsArray[id].pn_no = pn_no;
                  contractDetailsArray[id].qty = qty;
                  contractDetailsArray[id].msrp = msrp;
                  contractDetailsArray[id].hostname_modal = hostname_modal;

                  var asset_array = [];
                  $.each($('#hostname_modal_edit option:selected'), function() {
                      asset_array.push($(this).attr('data-hostname'));
                  })

                  contractDetailsArray[id].asset_array = asset_array;

                  showcontractDetails()
                  $('#ContractModalEdit').modal('hide')
                  $('textarea[name=asset_description_edit]').val('')
                  $('input[name=pn_no_edit]').val('')
                  $('input[name=qty_edit]').val('')
                  $('input[name=msrp_edit]').val('')
                  $('#hostname_modal_edit').val('')
                  $('#hostname_modal_edit').selectpicker('refresh')

              }
          })

          $(document).on('click', '.btnEditContract', function() {
              var id = $(this).attr('data');
              $('#ContractModalEdit').modal('show');
              $('input[name=contract_id_edit]').val(id);

              $('textarea[name=asset_description_edit]').val(contractDetailsArray[id].asset_description)
              $('input[name=pn_no_edit]').val(contractDetailsArray[id].pn_no)
              $('input[name=qty_edit]').val(contractDetailsArray[id].qty)
              $('input[name=msrp_edit]').val(contractDetailsArray[id].msrp)
              $('#hostname_modal_edit').val(contractDetailsArray[id].hostname_modal)
              $('#hostname_modal_edit').selectpicker('refresh')



          })
          var temp_contract = [];
          $(document).on('click', '.btnDeleteContract', function() {
              var id = $(this).attr('data');
              var key = contractDetailsArray[id].key;
              temp_contract.push(contractDetailsArray[id]);

              contractDetailsArray.splice(id, 1);

              Dashmix.helpers('notify', {
                  align: 'center',
                  message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Contract details Deleted. <a href="javascript:;" class="  btn-notify btnContractUndo ml-4" data1=' +
                      id + ' data=' + key + '>Undo</a>',
                  delay: 5000
              });
              showcontractDetails();

          })

          $(document).on('click', '.btnContractUndo', function() {
              var id = $(this).attr('data');
              var key = $(this).attr('data1');

              let index = temp_contract.filter(l => l.key == id);

              if (index[0]) {
                  contractDetailsArray.splice(id, 0, index[
                  0]); // 2nd parameter means remove one item only
                  temp_contract = temp_contract.filter(l => l.key != id);
                  showcontractDetails();
              }
          })

          function showcontractDetails() {
              var html = '';
              if (contractDetailsArray.length > 0) {
                  $('.contractDetailsDiv').removeClass('d-none');
              } else {
                  $('.contractDetailsDiv').addClass('d-none');
              }
              for (var i = 0; i < contractDetailsArray.length; i++) {
                  var msrp = contractDetailsArray[i].msrp;
                  var assets = contractDetailsArray[i].asset_array;

                  html += `          <div class="js-task block block-rounded mb-2 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          ${contractDetailsArray[i].qty}  <!-- <img width="40px" src="{{ asset('public/img/profile-white.png') }}"> --></b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                        <h2 class="mb-0 comments-text">${contractDetailsArray[i].pn_no}<br><span class="comments-subtext">${contractDetailsArray[i].asset_description}
</span></h2>
                                                    </td>
                                                    <td class="text-right" style="width: 130px;">
                                                       <!-- -->
                                                            <h3 class="mb-0">$${parseFloat(msrp).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</h3>
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <td colspan="2"  class="pt-0" style="vertical-align: top;" >
                                                       <p class="pl-4 mb-0 comments-section-text"> ${assets.join()}</p>
                                                    </td>
                                                    <td style="width: 20%;" class="text-right pt-0">  <a type="button" data="${i}" class="js- btnEditContract  btn btn-sm btn-link text-warning">
                                                         <img src="{{ url('public/img/editing.png') }}"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">
                                                        </a>
                                                        <a type="button" data="${i}" class=" btnDeleteContract  btn btn-sm btn-link text-danger"  data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/img/trash--v1.png') }}" width="24px">
                                                        </a> </td>
                                                </tr>

                                        </tbody>
                                    </table>

                                    </div>`;
              }

              $('#contractDetailsBlock').html(html)
          }

          // END Contract Details















          // Attachment ARRAY

          var attachmentArray = [];
          var attachment_key_count = 0;
          $('#AttachmentSave').click(function() {
              var attachment = content3_image;
              if (content3_image.length == 0) {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1">  Add an attachment before saving.',
                      delay: 5000
                  });

              } else {

                  var l = attachmentArray.length;



                  for (var i = 0; i < attachment.length; i++) {
                      attachmentArray.push({
                          key: attachment_key_count,
                          attachment: attachment[i],
                          date: '{{ date('Y-M-d') }}',
                          time: '{{ date('h:i:s A') }}',
                          name: '{{ Auth::user()->firstname . '' . Auth::user()->lastname }}'
                      });
                      attachment_key_count++;
                  }

                  filePond.removeFiles();
                  content3_image = [];
                  showAttachment()
                  $('#AttachmentModal').modal('hide')



              }
          })

          var temp_attachment = [];
          $(document).on('click', '.btnDeleteAttachment', function() {
              var id = $(this).attr('data');
              var key = attachmentArray[id].key;
              temp_attachment.push(attachmentArray[id]);

              attachmentArray.splice(id, 1);

              Dashmix.helpers('notify', {
                  align: 'center',
                  message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Attachment  Deleted. <a href="javascript:;" class="  btn-notify btnAttachmentUndo ml-4" data1=' +
                      id + ' data=' + key + '>Undo</a>',
                  delay: 5000
              });
              showAttachment();

          })


          $('#AttachmentClose').click(function() {
              temp_attachment = [];
              content3_image = [];
              filePond.removeFiles();
          })

          $(document).on('click', '.btnAttachmentUndo', function() {
              var id = $(this).attr('data');
              var key = $(this).attr('data1');

              let index = temp_attachment.filter(l => l.key == id);

              if (index[0]) {
                  attachmentArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only
                  temp_attachment = temp_attachment.filter(l => l.key != id);



                  showAttachment();
              }
          })

          function showAttachment() {
              var html = '';
              if (attachmentArray.length > 0) {
                  $('.attachmentDiv').removeClass('d-none');
              } else {
                  $('.attachmentDiv').addClass('d-none');
              }
              for (var i = 0; i < attachmentArray.length; i++) {
                  var fileExtension = attachmentArray[i].attachment.split('.').pop();
                  icon = 'attachment.png';
                  if (fileExtension == 'pdf') {
                      icon = 'attch-Icon-pdf.png';
                  } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                      icon = 'attch-word.png'
                  } else if (fileExtension == 'txt') {
                      icon = 'attch-word.png';

                  } else if (fileExtension == 'csv' || fileExtension == 'xlsx' || fileExtension == 'xlsm' ||
                      fileExtension == 'xlsb' || fileExtension == 'xltx') {
                      icon = 'attch-excel.png'
                  } else if (fileExtension == 'png' || fileExtension == 'gif' || fileExtension == 'webp' ||
                      fileExtension == 'svg') {
                      icon = 'attch-png icon.png';
                  } else if (fileExtension == 'jpeg' || fileExtension == 'jpg') {
                      icon = 'attch-jpg-icon.png';
                  } else if (fileExtension == 'potx' || fileExtension == 'pptx' || fileExtension == 'ppsx' ||
                      fileExtension == 'thmx') {
                      icon = 'attch-powerpoint.png';
                  }


                  html += `   <div class="col-lg-4  ">
                                              <div class="js-task  block block-rounded mb-2  pb-3 animated fadeIn" data-task-id="9" data-task-completed="false" data-task-starred="false">
                                        <table class="table table-borderless table-vcenter mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center pr-0" style="width: 38px;padding-top: 9px;">
                                                         <h1 class="mb-0 mr-3  text-white bg-dark rounded px-1" style=""><b>
                                                          <img width="40px" src="{{ asset('public/img/profile-white.png') }}"> </b></h1>
                                                    </td>
                                                    <td class="js-task-content  pl-0">
                                                           <h2 class="mb-0 comments-text">${attachmentArray[i].name}<br><span class="comments-subtext">On ${attachmentArray[i].date} at ${attachmentArray[i].time} GMT
</span></h2>
                                                    </td>
                                                    <td class="text-right position-relative" style="width: auto;">
                                                       <!-- -->

                                                        <a type="button"  class="  btnDeleteAttachment    btn btn-sm btn-link text-danger"  data="${i}" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">
                                                         <img src="{{ url('public/img/trash--v1.png') }}" width="24px">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"  class="pt-2"><p class="mb-2">
 <a href="{{ asset('public/temp_uploads/${attachmentArray[i].attachment}') }}" target="_blank"    class="   attachmentDivNew comments-section-text"><img src="{{ asset('public/img/${icon}') }}" width="25px"> &nbsp;${attachmentArray[i].attachment.substring(0,30)}...
</a></p>
                                                    </td>

                                                </tr>

                                        </tbody>
                                    </table>
                                        </div>
                                    </div>`;
              }

              $('#attachmentBlock').html(html)
          }

          // END Attachment

































          var count = 0;

          $('.repeater').repeater({
              initEmpty: true,
              show: function(setIndexes) {
                  var repeaterItems = $("div[data-repeater-item]");

                  var INDEX = $(this).parent().children().index($(this));

                  var pr_no = $('.pn_no').eq(INDEX - 1);
                  var qty = $('.qty').eq(INDEX - 1);
                  var msrp = $('.msrp').eq(INDEX - 1);
                  var result = '';
                  if (count != 0 && INDEX > 0) {

                      if (pr_no.val() == '') {
                          pr_no.addClass('is-invalid');
                      } else {
                          pr_no.removeClass('is-invalid');
                          result += '1';
                      }

                      if (qty.val() == '') {
                          qty.addClass('is-invalid');
                      } else {
                          qty.removeClass('is-invalid');
                          result += '1';
                      }


                      if (msrp.val() == '') {
                          msrp.addClass('is-invalid');
                      } else {
                          msrp.removeClass('is-invalid');
                          result += '1';
                      }


                  } else {
                      result = '111';
                  }


                  if (result == '111') {

                      count++;




                      $(this).slideDown();

                      $(this).find('.count').val(count);



                      $(this).find('.btnAsset').attr('data', count);
                      $(this).find('.asset_id').attr('data', count);
                      $(this).find('.asset_type').attr('data', count);
                      $(this).find('.countchild').attr('data', count);
                      $(this).find('.qty').attr('data', count);
                      $(this).find('.asset_div').attr('data', count);
                      $(this).find('.assetParentDiv').attr('data', count);

                      $(this).find('.typeDiv').attr('data', count);
                      $(this).find('.hostDiv').attr('data', count);

                      $(this).find('.SNODiv').attr('data', count);

                  } else {
                      $(this).remove()
                  }


              },


              // (Optional)
              // Removes the delete button from the first list item,
              // defaults to false.
              isFirstItemUndeletable: false
          })




          $('#client_id').change(function() {
              var address = $('option:selected', $('#client_id')).attr('data-address')
              var email = $('option:selected', $('#client_id')).attr('data-email')
              var logo = $('option:selected', $('#client_id')).attr('data-logo')
              var renewal_notification_email = $('option:selected', $('#client_id')).attr(
                  'data-renewal_notification_email')
              $('#renewal_notification_email').val(renewal_notification_email)
              $('#registered_email').val(email)
              var id = $(this).val();
              $.ajax({
                  type: 'get',
                  data: {
                      id: id
                  },
                  url: '{{ url('get-contract-notification') }}',
                  success: function(res) {
                      var html = '';
                      var l = 0;
                      emailArray = [];

                      for (var i = 0; i < res.length; i++) {
                          if (emailArray.length < 5) {
                              email_key_count++;

                              emailArray.push({
                                  key: email_key_count,
                                  email: res[i].renewal_email
                              });
                              showEmail();
                          }


                      }


                  }

              })


          })



          $('#distributor_id').change(function() {
              // var address=$('option:selected',$('#distributor_id')).attr('data-address')
              var logo = $('option:selected', $('#distributor_id')).attr('data-logo')
              if (logo != '') {
                  $('#distributorInfo').html('<div class="col-sm-8">' +

                      '<img class="img-avatar" style="width:90px;height:90px"   src="{{ asset('public/distributor_logos/') }}/' +
                      logo + '" alt="">' +

                      '</div>')
              } else {
                  $('#distributorInfo').html('')
              }
          })



          $('#vendor_id').change(function() {
              // var address=$('option:selected',$('#distributor_id')).attr('data-address')
              var logo = $('option:selected', $('#vendor_id')).attr('data-logo')
              if (logo != '') {
                  $('#vendorInfo').html('<div class="col-sm-8">' +

                      '<img class="img-avatar" style="width:90px;height:90px"  src="{{ asset('public/vendor_logos/') }}/' +
                      logo + '" alt="">' +

                      '</div>')
              } else {
                  $('#vendorInfo').html('')
              }
          })


          $('#contract_start_date').change(function() {
              var val = $(this).val();
              var spl = val.split('-');
              var year = parseInt(spl[0]) + parseInt(1);
              var newdate = year + '-' + spl[1] + '-' + spl[2];
              console.log(newdate)
              $("#contract_end_date").flatpickr({
                  defaultDate: newdate
              });



          })
          $('#type_modal').change(function() {
              var val = $(this).val();
              if (val == 'physical') {
                  $('.snDiv').removeClass('d-none')
              } else {
                  $('.snDiv').addClass('d-none')
              }


              var client_id = $('#client_id').val();
              $.ajax({
                  type: 'get',
                  data: {
                      val: val,
                      client_id: client_id
                  },
                  url: "{{ url('get-assets-by-type') }}",
                  async: false,

                  success: function(res) {
                      var html = '';

                      for (var i = 0; i < res.length; i++) {
                          if (val == 'physical') {
                              html += '<option value=' + res[i].id + '  data-hostname="' +
                                  res[i].hostname + '" data="' + res[i].sn + '">' + (res[i]
                                      .sn == null ? '' : res[i].sn) + ' [' + res[i].hostname +
                                  ']</option>';
                          } else {
                              html += '<option value=' + res[i].id + '  data-hostname="' +
                                  res[i].hostname + '" data="' + res[i].sn + '">' + res[i]
                                  .hostname + '</option>';
                          }

                      }

                      $('#hostname_modal').html(html)

                      $('#hostname_modal').selectpicker('refresh')

                  }
              })
          })

          $('#hostname_modal').change(function() {
              var sn = $('option:selected', $(this)).attr('data');


              $('#sn_modal').val(sn)
          })





          $('#showdata').on('focusout', '.qty', function() {


              var qty = $(this).val();
              var count = $(this).attr('data');
              var len = $('.asset_div[data=' + count + ']').length;


              if (qty < len) {

                  $('.asset_div[data=' + count + ']').each(function(index) {

                      if (qty - 1 < index) {
                          $(this).remove();
                      }

                  })
              }



          })

          let click = 0;
          $('input,textarea').on('keyup', function() {
              click = 1;

          })

          $('select').on('change', function() {
              click = 1;

          })
          $('.btnClose').click(function() {
              if (click == 1) {
                  Dashmix.helpers('notify', {
                      message: 'Close window?  <a href="javascript:;" class="  btn-notify btnCloseUndo ml-4" >Proceed</a>',
                      delay: 5000
                  });

              } else {
                  window.location.href = unEntity(
                      '{{ URL::previous() == URL::full() ? url('contract/' . $type . '') : URL::previous() }}');
              }
          })
          $(document).on('click', '.btnCloseUndo', function() {
              window.location.href = unEntity(
                  '{{ URL::previous() == URL::full() ? url('contract/' . $type . '') : URL::previous() }}');
          })



          $('.saveContract').click(function() {
              $('.tooltip').tooltip('hide');

              var data1 = $(this).attr('data');
              var client_id = $('#client_id').val();
              var site_id = $('#site_id').val();
              var contract_description = $('#contract_description').val();
              var vendor_id = $('#vendor_id').val();
              var contract_no = $('#contract_no').val();
              var registered_email = $('#registered_email').val();
              var contract_start_date = $('#contract_start_date').val();
              var contract_end_date = $('#contract_end_date').val();
              var start_date = new Date(contract_start_date);
              var end_date = new Date(contract_end_date);

              if (client_id == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please select a value for Client.',
                      delay: 5000
                  });

              } else if (site_id == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please select a value for Site.',
                      delay: 5000
                  });

              } else if (contract_description == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Description.',
                      delay: 5000
                  });

              } else if (vendor_id == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please select a value for Vendor.',
                      delay: 5000
                  });

              } else if (contract_no == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Contract No.',
                      delay: 5000
                  });

              } else if (registered_email == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for End User Email.',
                      delay: 5000
                  });

              } else if (contract_start_date == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Start Date.',
                      delay: 5000
                  });

              } else if (contract_end_date == '') {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for End Date.',
                      delay: 5000
                  });

              } else if (start_date.getTime() > end_date.getTime()) {
                  Dashmix.helpers('notify', {
                      message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> End Date cannot be greater than Start Date.',
                      delay: 5000
                  });

              } else {
                  var formData = new FormData(document.getElementById("form-1"));
                  for (var i = 0; i < emailArray.length; i++) {
                      formData.append('emailArray[]', JSON.stringify(emailArray[i]));
                  }
                  for (var i = 0; i < contractDetailsArray.length; i++) {
                      formData.append('contractDetailsArray[]', JSON.stringify(contractDetailsArray[i]));
                  }
                  for (var i = 0; i < attachmentArray.length; i++) {
                      formData.append('attachmentArray[]', JSON.stringify(attachmentArray[i]));
                  }
                  for (var i = 0; i < commentArray.length; i++) {
                      formData.append('commentArray[]', JSON.stringify(commentArray[i]));
                  }


                  $.ajax({
                      type: 'post',
                      data: formData,
                      'url': '{{ url('insert-contract') }}',
                      dataType: 'json',
                      async: false,

                      contentType: false,
                      processData: false,
                      cache: false,
                      success: function(res) {

                          $('#contract_description').focus()
                          click = 0;
                          if (data1 == 0) {
                              Dashmix.helpers('notify', {
                                  align: 'center',
                                  message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Contract Saved Succesfully',
                                  delay: 5000
                              });

                              window.location.href = unEntity(
                                  '{{ URL::previous() == URL::full() ? url('contract/' . $type . '') : URL::previous() }}'
                                  );
                          } else {

                              Dashmix.helpers('notify', {
                                  align: 'center',
                                  message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Contract Succesfully added.<br><span  style="margin-left:32px;color:lightgrey">Continue adding contracts</span>',
                                  delay: 5000
                              });
                          }

                          contractDetailsArray = [];
                          contract_key_count = 0;
                          contractDetailsArray = [];
                          temp_contract = [];
                          attachment_key_count = 0;
                          temp_attachment = [];
                          content3_image = [];
                          attachmentArray = [];


                          showComment();
                          showcontractDetails();
                          showAttachment();


                      }
                  })

              }

          })





          // Init Validation on Select2 change
          jQuery('.select2').on('change', e => {
              jQuery(e.currentTarget).valid();
          });

          var previous = '';
          $("#client_id").on('change', function() {








              var id = $(this).val()
              var result = '';


              if (contractDetailsArray.length > 0) {
                  alert('Already Assigned Assets Of this Client,Plz delete them first');
                  $('#client_id').select2('destroy');
                  $(this).val(previous);
                  $('#client_id').select2();
              } else {
                  previous = $(this).val();

                  $.ajax({
                      type: 'get',
                      data: {
                          id: id
                      },
                      url: '{{ url('getSiteByClientId') }}',
                      success: function(res) {
                          var html = '';
                          html += '<option value>Select Site</option>';
                          for (var i = 0; i < res.length; i++) {
                              html += '<option value="' + res[i].id + '" >' + res[i]
                                  .site_name + '</option>';
                          }
                          $('#site_id').select2('destroy');
                          $('#site_id').html(html);
                          $('#site_id').select2();
                      }
                  })
                  $.ajax({
                      type: 'get',
                      data: {
                          id: id
                      },
                      url: '{{ url('getDomainByClientId') }}',
                      success: function(res) {
                          var html = '';
                          html += '<option value>Select Domain</option>';
                          for (var i = 0; i < res.length; i++) {
                              html += '<option value="' + res[i].id + '" >' + res[i]
                                  .domain_name + '</option>';
                          }
                          $('#domain').select2('destroy');
                          $('#domain').html(html);
                          $('#domain').select2();
                      }
                  })
              }






              $.ajax({
                  type: 'get',
                  data: {
                      client_id: id
                  },
                  url: "{{ url('get-assets-by-type') }}",
                  async: false,

                  success: function(res) {
                      var html = '';

                      for (var i = 0; i < res.length; i++) {

                          if (res.asset_type == 'physical') {
                              html += '<option value=' + res[i].id + '  data-hostname="' +
                                  res[i].hostname + '" data="' + res[i].sn + '">' + (res[i]
                                      .sn == null ? '' : res[i].sn) + ' [' + res[i].hostname +
                                  ']</option>';
                          } else {
                              html += '<option value=' + res[i].id + '  data-hostname="' +
                                  res[i].hostname + '" data="' + res[i].sn + '">' + res[i]
                                  .hostname + '</option>';
                          }
                      }

                      $('#hostname_modal').html(html)

                      $('#hostname_modal').selectpicker('refresh')
                      $('#hostname_modal_edit').html(html)

                      $('#hostname_modal_edit').selectpicker('refresh')

                  }
              })
          })




          $('#cpu_cores,#cpu_sockets').focusout(function() {

              var cpu_cores = $('#cpu_cores').val();
              var cpu_sockets = $('#cpu_sockets').val();
              $('#cpu_total_cores').val(cpu_cores * cpu_sockets)
          })

          $('#hostname').focusout(function() {
              var hostname = $('#hostname').val();
              var domain = $('option:selected', $('#domain')).text();
              if (hostname != '' && domain != '') {
                  $('#fqdn').val(hostname + '.' + domain);
              }
          })


          $('#domain').change(function() {
              var hostname = $('#hostname').val();
              var domain = $('option:selected', $('#domain')).text();
              if (hostname != '' && domain != '') {
                  $('#fqdn').val(hostname + '.' + domain);
              }
          })




      })
  </script>
