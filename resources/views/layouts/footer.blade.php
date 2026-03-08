@section('footer')

    <!-- Footer -->



    <!-- END Footer -->

    </div>

    <!-- END Page Container -->



    <?php $system_settings = DB::Table('system_settings')->first(); ?>



    <form class="mb-0 pb-0" id="form-settings" action="{{ url('update-system-settings') }}" enctype="multipart/form-data"
        method="post">

        @csrf

        <div class="modal fade" id="settingsModalold" tabindex="-1" role="dialog" data-backdrop="static"
            aria-labelledby="modal-block-large" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered  modal-xl modal-bac " role="document">

                <div class="modal-content">

                    <div class="block  block-transparent mb-0">

                        <div class="block-header pb-0  ">

                            <span class="b e section-header">System Settings</span>

                            <div class="block-options">

                                <button type="button" class="btn-block-option close-modal" target-modal="#settingsModal">

                                    <i class="fa fa-fw fa-times"></i>

                                </button>

                            </div>

                        </div>



                        <div class="block-content new-block-content pt-0 pb-0 ">







                            <div class="row   form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label">Contact</label>

                                </div>

                                <div class="col-sm-2">



                                    <select class="form-control  " name="system_salutation">



                                        <option value="Mr" {{ $system_settings->salutation == 'Mr' ? 'selected' : '' }}>
                                            Mr

                                        </option>

                                        <option value="Mrs"
                                            {{ $system_settings->salutation == 'Mrs' ? 'selected' : '' }}>Mrs

                                        </option>

                                    </select>

                                </div>

                                <div class="col-lg-3">

                                    <input type="" name="system_firstname" class="form-control"
                                        value="{{ $system_settings->firstname }}" placeholder="First Name">

                                </div>



                                <div class="col-lg-3">

                                    <input type="" name="system_lastname" class="form-control"
                                        placeholder="Last Name" value="{{ $system_settings->lastname }}">



                                </div>

                                <div class="col-lg-2">

                                    <select class="form-control  select2" name="system_designation">



                                        <option value="CIPM"
                                            {{ $system_settings->designation == 'CIPM' ? 'selected' : '' }}>

                                            CIPM</option>

                                        <option value="CFA"
                                            {{ $system_settings->designation == 'CFA' ? 'selected' : '' }}>CFA

                                        </option>

                                        <option value="CBV"
                                            {{ $system_settings->designation == 'CBV' ? 'selected' : '' }}>CBV

                                        </option>

                                        <option value="CMT"
                                            {{ $system_settings->designation == 'CMT' ? 'selected' : '' }}>CMT

                                        </option>

                                        <option value="CAIA"
                                            {{ $system_settings->designation == 'CAIA' ? 'selected' : '' }}>

                                            CAIA</option>

                                        <option value="ChMC"
                                            {{ $system_settings->designation == 'ChMC' ? 'selected' : '' }}>

                                            ChMC</option>

                                        <option value="CGMA"
                                            {{ $system_settings->designation == 'CGMA' ? 'selected' : '' }}>

                                            CGMA</option>





                                        <option value="CAPA"
                                            {{ $system_settings->designation == 'CAPA' ? 'selected' : '' }}>

                                            CAPA</option>

                                        <option value="CAPP"
                                            {{ $system_settings->designation == 'CAPP' ? 'selected' : '' }}>

                                            CAPP</option>

                                        <option value="CPA"
                                            {{ $system_settings->designation == 'CPA' ? 'selected' : '' }}>CPA

                                        </option>

                                        <option value="CICS"
                                            {{ $system_settings->designation == 'CICS' ? 'selected' : '' }}>

                                            CICS</option>

                                        <option value="CICP"
                                            {{ $system_settings->designation == 'CICP' ? 'selected' : '' }}>

                                            CICP</option>

                                        <option value="CFP"
                                            {{ $system_settings->designation == 'CFP' ? 'selected' : '' }}>CFP

                                        </option>

                                        <option value="CDFA"
                                            {{ $system_settings->designation == 'CDFA' ? 'selected' : '' }}>

                                            CDFA</option>

                                        <option value="CAMS"
                                            {{ $system_settings->designation == 'CAMS' ? 'selected' : '' }}>

                                            CAMS</option>

                                        <option value="CFC"
                                            {{ $system_settings->designation == 'CFC' ? 'selected' : '' }}>CFC

                                        </option>

                                        <option value="CCUFC"
                                            {{ $system_settings->designation == 'CCUFC' ? 'selected' : '' }}>

                                            CCUFC</option>

                                        <option value="CLU"
                                            {{ $system_settings->designation == 'CLU' ? 'selected' : '' }}>CLU

                                        </option>



                                        <option value="CFE"
                                            {{ $system_settings->designation == 'CFE' ? 'selected' : '' }}>CFE

                                        </option>

                                        <option value="CIA"
                                            {{ $system_settings->designation == 'CIA' ? 'selected' : '' }}>CIA

                                        </option>



                                        <option value="CRMA"
                                            {{ $system_settings->designation == 'CRMA' ? 'selected' : '' }}>

                                            CRMA</option>

                                        <option value="CGAP"
                                            {{ $system_settings->designation == 'CGAP' ? 'selected' : '' }}>

                                            CGAP</option>

                                        <option value="CPP"
                                            {{ $system_settings->designation == 'CPP' ? 'selected' : '' }}>CPP

                                        </option>

                                        <option value="FPC"
                                            {{ $system_settings->designation == 'FPC' ? 'selected' : '' }}>FPC

                                        </option>

                                        <option value="CCMT"
                                            {{ $system_settings->designation == 'CCMT' ? 'selected' : '' }}>

                                            CCMT</option>

                                        <option value="CGFM"
                                            {{ $system_settings->designation == 'CGFM' ? 'selected' : '' }}>

                                            CGFM</option>

                                        <option value="CGFO"
                                            {{ $system_settings->designation == 'CGFO' ? 'selected' : '' }}>

                                            CGFO</option>

                                        <option value="CMA"
                                            {{ $system_settings->designation == 'CMA' ? 'selected' : '' }}>CMA

                                        </option>

                                        <option value="CMFO"
                                            {{ $system_settings->designation == 'CMFO' ? 'selected' : '' }}>

                                            CMFO</option>

                                        <option value="CPFA"
                                            {{ $system_settings->designation == 'CPFA' ? 'selected' : '' }}>

                                            CPFA</option>

                                        <option value="CPFO"
                                            {{ $system_settings->designation == 'CPFO' ? 'selected' : '' }}>

                                            CPFO</option>

                                        <option value="CPCU"
                                            {{ $system_settings->designation == 'CPCU' ? 'selected' : '' }}>

                                            CPCU</option>

                                        <option value="CDFM"
                                            {{ $system_settings->designation == 'CDFM' ? 'selected' : '' }}>

                                            CDFM</option>

                                        <option value="CMP"
                                            {{ $system_settings->designation == 'CMP' ? 'selected' : '' }}>CMP

                                        </option>

                                        <option value="RTRP"
                                            {{ $system_settings->designation == 'RTRP' ? 'selected' : '' }}>

                                            RTRP</option>

                                        <option value="EA"
                                            {{ $system_settings->designation == 'EA' ? 'selected' : '' }}>EA

                                        </option>



                                        <option value="FICF"
                                            {{ $system_settings->designation == 'FICF' ? 'selected' : '' }}>

                                            FICF</option>







                                        <option value="FIC"
                                            {{ $system_settings->designation == 'FIC' ? 'selected' : '' }}>FIC

                                        </option>

                                        <option value="FP&A"
                                            {{ $system_settings->designation == 'FP&A' ? 'selected' : '' }}>

                                            FP&A</option>



                                        <option value="ISSP Sustainability"
                                            {{ $system_settings->designation == 'ISSP Sustainability' ? 'selected' : '' }}>
                                            ISSP

                                            Sustainability</option>

                                        <option value="CTP"
                                            {{ $system_settings->designation == 'CTP' ? 'selected' : '' }}>CTP

                                        </option>

                                        <option value="CTFA"
                                            {{ $system_settings->designation == 'CTFA' ? 'selected' : '' }}>

                                            CTFA</option>

                                        <option value="CTP"
                                            {{ $system_settings->designation == 'CTP' ? 'selected' : '' }}>CTP

                                        </option>



                                        <option value="RFP"
                                            {{ $system_settings->designation == 'RFP' ? 'selected' : '' }}>RFP

                                        </option>

                                        <option value="FRM"
                                            {{ $system_settings->designation == 'FRM' ? 'selected' : '' }}>FRM

                                        </option>



                                        <option value="PRM"
                                            {{ $system_settings->designation == 'PRM' ? 'selected' : '' }}>PRM

                                        </option>

                                        <option value="SCMP"
                                            {{ $system_settings->designation == 'SCMP' ? 'selected' : '' }}>

                                            SCMP</option>

                                        <option value="LPA"
                                            {{ $system_settings->designation == 'LPA' ? 'selected' : '' }}>LPA

                                        </option>

                                        <option value="ABA"
                                            {{ $system_settings->designation == 'ABA' ? 'selected' : '' }}>ABA

                                        </option>

                                        <option value="IACCP"
                                            {{ $system_settings->designation == 'IACCP' ? 'selected' : '' }}>

                                            IACCP</option>

                                    </select>

                                </div>

                            </div>





                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Company</label>

                                </div>



                                <div class="col-lg-10">

                                    <input type="" name="system_company_name" class="form-control"
                                        placeholder="Company Name" value="{{ $system_settings->company }}">

                                </div>



                            </div>





                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Email</label>

                                </div>



                                <div class="col-lg-10">

                                    <input type="" name="system_email" class="form-control"
                                        value="{{ $system_settings->email }}" placeholder="Company e-mail address">

                                </div>



                            </div>





                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Telephone</label>

                                </div>



                                <div class="col-lg-4">

                                    <input type="" name="system_telephone" class="form-control"
                                        placeholder="555-555-5555" value="{{ $system_settings->telephone }}">

                                </div>



                                <div class="col-lg-2">

                                    <label class="col-form-label ">Fax</label>

                                </div>



                                <div class="col-lg-4">

                                    <input type="" name="system_fax" class="form-control"
                                        value="{{ $system_settings->fax }}" placeholder="555-555-5555">

                                </div>



                            </div>



                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label  ">Website</label>

                                </div>



                                <div class="col-lg-10">

                                    <input type="" name="system_website" class="form-control"
                                        placeholder="https://www.web.url" value="{{ $system_settings->website }}">

                                </div>



                            </div>



                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Country</label>

                                </div>



                                <div class="col-lg-5">

                                    <select type="" name="system_country" class="form-control select2"
                                        placeholder="Company e-mail address">

                                        <?php $country = DB::table('countries')->get(); ?>

                                        <option value="">Select Country</option>

                                        @foreach ($country as $c)
                                            <option value="{{ $c->name }}"
                                                {{ $system_settings->country == $c->name ? 'selected' : '' }}>

                                                {{ $c->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>



                            </div>







                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Address</label>

                                </div>



                                <div class="col-lg-10">

                                    <textarea type="" name="system_address" class="form-control" placeholder="Address" rows="5">{{ $system_settings->address }}</textarea>

                                </div>



                            </div>



                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label  mandatory">City</label>

                                </div>



                                <div class="col-lg-10">

                                    <input type="" name="system_city" class="form-control" placeholder="City"
                                        value="{{ $system_settings->city }}">

                                </div>



                            </div>

                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Province</label>

                                </div>



                                <div class="col-lg-4">



                                    <select type="" name="system_province" class="form-control select2"
                                        value="{{ $system_settings->province }}">

                                        @if (@$system_settings->country != '')
                                            <?php
                                            
                                            $city_qry = DB::Table('cities')->where('country_name', $system_settings->country)->groupBy('state_name')->get();
                                            
                                            ?>

                                            @foreach ($city_qry as $c)
                                                <option value="{{ $c->state_name }}"
                                                    {{ $system_settings->province == $c->state_name ? 'selected' : '' }}>

                                                    {{ $c->state_name }}</option>
                                            @endforeach
                                        @endif





                                    </select>

                                </div>



                                <div class="col-lg-2">

                                    <label class="col-form-label mandatory">Postal Code</label>

                                </div>



                                <div class="col-lg-4">

                                    <input type="" name="system_postal_code" class="form-control"
                                        placeholder="A9A 980" value="{{ $system_settings->postal_code }}">

                                </div>



                            </div>









                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label ">Logo</label>

                                </div>



                                <div class="col-lg-4">



                                    <input type="file" name="logo" class="form-control">

                                </div>

                            </div>





                            <div class="row form-group">

                                <div class="col-lg-2">



                                    <label class="col-form-label">Tax Remittance</label>

                                </div>

                                <div class="col-lg-10">

                                    <input type="text" class="form-control" name="system_tax_remittance"
                                        value="{{ $system_settings->tax_remittance }}"
                                        placeholder="Remit Taxes Description (Pay to the order of)">

                                </div>

                            </div>



                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label ">Federal Corp Tax</label>

                                </div>



                                <div class="col-lg-2">

                                    <input type="text" class="form-control" name="system_federal_corp_tax_perc"
                                        value="{{ $system_settings->federal_corp_tax_perc }}%" placeholder="10.5%">

                                </div>



                                <div class="col-lg-8">

                                    <input type="text" class="form-control" name="system_federal_corp_tax"
                                        value="{{ $system_settings->federal_corp_tax }}"
                                        placeholder="Federal Corp Tax Payment Description (Pay to the order of)">

                                </div>



                            </div>





                            <div class="row  form-group">

                                <div class="col-lg-2">

                                    <label class="col-form-label ">Provincial Corp Tax</label>

                                </div>



                                <div class="col-lg-2">

                                    <input type="text" class="form-control" name="system_provincial_corp_tax_perc"
                                        value="{{ $system_settings->provincial_corp_tax_perc }}%" placeholder="10.5%">

                                </div>



                                <div class="col-lg-8">

                                    <input type="text" class="form-control" name="system_provincial_corp_tax"
                                        value="{{ $system_settings->provincial_corp_tax }}"
                                        placeholder="Provincial Corp Tax Payment Description (Pay to the order of)">

                                </div>



                            </div>





                        </div>

                        <div class="block-content block-content-full text-right  pt-4"
                            style="padding-left: 9mm;padding-right: 9mm">

                            <button type="submit" class="btn mr-3 btn-new ">Save</button>





                        </div>

                    </div>



                </div>

            </div>

        </div>





    </form>

    <form class="mb-0 pb-0" action="{{ url('update-system-settings') }}" id="form-settings" method="post"
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" data-backdrop="static"
            aria-labelledby="modal-block-large" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
                <div class="modal-content">
                    <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                        <h1 class="modal-header-insert mb-0">
                            ADMINISTRATION<br>
                            <span class="modal-subheader">CONTROL FLOW</span>
                        </h1>
                        <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="block small-arrow modal-static-ht block-transparent mb-0" style="max-height: 550px !important;">
                        <!--<p class="ml-4 mb-0">ControlFlow to OnPrem</p>-->
                        <!--<div class="block-content pt-0 row mt-2">-->
                        <!--    <label class="col-sm-3 d-flex align-items-center modal-label"-->
                        <!--        for="testNameSelect">Assets</label>-->
                        <!--    <div class="align-items-center col-3 d-flex justify-content-center">-->
                        <!--        <span class="font-signika bubble-status-active js-tooltip-enabled justify-content-center"-->
                        <!--            style="width: 70px;">Success</span>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-center">-->
                        <!--        <label class="align-items-center modal-label"-->
                        <!--            style="font-size: 11pt !important; line-height: 14px;"-->
                        <!--            for="testNameSelect"><span>Today</span> <br> <span>18:32:45 EST</span></label>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-right">-->
                        <!--        <button type="button" id="" class="btn btn-action">-->
                        <!--            Sync Now-->
                        <!--        </button>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="block-content pt-0 row mt-2">-->

                        <!--    <label class="col-sm-3 d-flex align-items-center modal-label" for="testNameSelect">Item-->
                        <!--        Categories </label>-->
                        <!--    <div class="align-items-center col-3 d-flex justify-content-center">-->
                        <!--        <span class="font-signika bubble-status-inactive js-tooltip-enabled justify-content-center"-->
                        <!--            style="width: 70px;">Failed</span>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-center">-->
                        <!--        <label class="align-items-center modal-label"-->
                        <!--            style="font-size: 11pt !important; line-height: 14px;"-->
                        <!--            for="testNameSelect"><span>25-Feb-2025</span> <br> <span>18:32:45 EST</span></label>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-right">-->
                        <!--        <button type="button" id="" class="btn btn-action">-->
                        <!--            Sync Now-->
                        <!--        </button>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="block-content pt-0 row mt-2">-->

                        <!--    <label class="col-sm-3 d-flex align-items-center modal-label" for="testNameSelect">Sample-->
                        <!--        Tests</label>-->
                        <!--    <div class="align-items-center col-3 d-flex justify-content-center">-->
                        <!--        <span class="font-signika bubble-status-notsync js-tooltip-enabled justify-content-center"-->
                        <!--            style="width: 70px;">Not Sync</span>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-center">-->
                        <!--        <label class="align-items-center modal-label"-->
                        <!--            style="font-size: 11pt !important; line-height: 14px;"-->
                        <!--            for="testNameSelect"><span>25-Feb-2025</span> <br> <span>18:32:45 EST</span></label>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-right">-->
                        <!--        <button type="button" id="" class="btn btn-action">-->
                        <!--            Sync Now-->
                        <!--        </button>-->
                        <!--    </div>-->
                        <!--</div>-->

                        <!--<p class="ml-4 mb-0 mt-3">OnPrem to ControlFlow</p>-->
                        <!--<div class="block-content pt-0 row mt-2">-->
                        <!--    <label class="col-sm-3 d-flex align-items-center modal-label"-->
                        <!--        for="testNameSelect">Itemcodes</label>-->
                        <!--    <div class="align-items-center col-3 d-flex justify-content-center">-->
                        <!--        <span class="font-signika bubble-status-active js-tooltip-enabled justify-content-center"-->
                        <!--            style="width: 70px;">Success</span>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-center">-->
                        <!--        <label class="align-items-center modal-label"-->
                        <!--            style="font-size: 11pt !important; line-height: 14px;"-->
                        <!--            for="testNameSelect"><span>Today</span> <br> <span>18:32:45 EST</span></label>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-right">-->
                        <!--        <button type="button" id="" class="btn btn-action">-->
                        <!--            Sync Now-->
                        <!--        </button>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="block-content pt-0 row mt-2">-->

                        <!--    <label class="col-sm-3 d-flex align-items-center modal-label"-->
                        <!--        for="testNameSelect">Workorders</label>-->
                        <!--    <div class="align-items-center col-3 d-flex justify-content-center">-->
                        <!--        <span class="font-signika bubble-status-inactive js-tooltip-enabled justify-content-center"-->
                        <!--            style="width: 70px;">Failed</span>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-center">-->
                        <!--        <label class="align-items-center modal-label"-->
                        <!--            style="font-size: 11pt !important; line-height: 14px;"-->
                        <!--            for="testNameSelect"><span>25-Feb-2025</span> <br> <span>18:32:45 EST</span></label>-->
                        <!--    </div>-->
                        <!--    <div class="col-sm-3 text-right">-->
                        <!--        <button type="button" id="" class="btn btn-action">-->
                        <!--            Sync Now-->
                        <!--        </button>-->
                        <!--    </div>-->
                        <!--</div>-->

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="ml-4 mb-0">Email Notifications</p>
                            <button type="button" id="" class="btn btn-action btn-email-notification-model" style="margin-right: 33px;">
                                Add
                            </button>
                        </div>

                        <div id="email-notification-content">
                            {{-- <div class="col-sm-11 mx-auto mb-2  pl-0 mt-3" data-id="2" data-code="34 CFT15">
                                <div class="email-item px-3 py-2 d-flex align-items-center">
                                    <div style="width: 10%;">
                                        <img src="{{ asset('public') }}/client_logos/login.png" alt=""
                                            style="width: 40px;">
                                    </div>

                                    <div style="width: 80%;">
                                        <span class="">qcsupport@ykk.com</span>
                                    </div>

                                    <div class="d-flex align-items-center" style="width: 10%;">
                                        <a class="edit-pen edit-pen-selected-module mr-2" data-item="34 CFT15"
                                            data-type="Dimension" data-min="43" data-avg="24" data-max="5"
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Edit"
                                            style="display: none;"><img
                                                src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                        </a>

                                        <button type="button" class="close close-cross remove-item" aria-label="Close"
                                            style="display: none;">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-11 mx-auto mb-2  pl-0 mt-3" data-id="2" data-code="34 CFT15">
                                <div class="email-item px-3 py-2 d-flex align-items-center">
                                    <div style="width: 10%;">
                                        <img src="{{ asset('public') }}/client_logos/login.png" alt=""
                                            style="width: 40px;">
                                    </div>

                                    <div style="width: 80%;">
                                        <span class="">ravirawat@ykk.com</span>
                                    </div>

                                    <div class="d-flex align-items-center" style="width: 10%;">
                                        <a class="edit-pen edit-pen-selected-module mr-2" data-item="34 CFT15"
                                            data-type="Dimension" data-min="43" data-avg="24" data-max="5"
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Edit"
                                            style="display: none;"><img
                                                src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                        </a>

                                        <button type="button" class="close close-cross remove-item" aria-label="Close"
                                            style="display: none;">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-11 mx-auto mb-2  pl-0 mt-3" data-id="2" data-code="34 CFT15">
                                <div class="email-item px-3 py-2 d-flex align-items-center">
                                    <div style="width: 10%;">
                                        <img src="{{ asset('public') }}/client_logos/login.png" alt=""
                                            style="width: 40px;">
                                    </div>

                                    <div style="width: 80%;">
                                        <span class="">ravirawat@ykk.com</span>
                                    </div>

                                    <div class="d-flex align-items-center" style="width: 10%;">
                                        <a class="edit-pen edit-pen-selected-module mr-2" data-item="34 CFT15"
                                            data-type="Dimension" data-min="43" data-avg="24" data-max="5"
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Edit"
                                            style="display: none;"><img
                                                src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                        </a>

                                        <button type="button" class="close close-cross remove-item" aria-label="Close"
                                            style="display: none;">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div> --}}
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="ml-4 mb-0">Sample Failure Notifications</p>
                            <button type="button" class="btn btn-action btn-open-email-model"
                                style="margin-right: 33px;">
                                Add
                            </button>
                        </div>

                        <div id="email-content">

                        </div>

                        <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                            <button type="button" id="saveEmailsBtn" class="btn btn-action">
                                <span class="btn-action-gear d-none mr-2"><img
                                        src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                                Save
                                <span class="btn-action-gear d-none ml-2"><img
                                        src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="addEmailModel" tabindex="-1" role="dialog" data-backdrop="static"
        aria-labelledby="modal-block-large" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered model-sm" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                    <h1 class="modal-header-insert mb-0">
                        Add Email
                    </h1>
                    <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="block small-arrow modal-static-ht block-transparent mb-0">
                    <div class="block-content pt-0 row mt-2">
                        <label class="col-sm-3 d-flex align-items-center modal-label" for="testNameSelect">Email</label>
                        <div class="align-items-center col-9 d-flex justify-content-center">
                            <input type="text" class="form-control" name="sf_email" id="sf_email">
                        </div>
                    </div>

                    <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                        <button type="submit" id="addEmailBtn" class="btn btn-action">
                            <span class="btn-action-gear d-none mr-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                            Add
                            <span class="btn-action-gear d-none ml-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addEmailNotificationModel" tabindex="-1" role="dialog" data-backdrop="static"
        aria-labelledby="modal-block-large" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered model-sm" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                    <h1 class="modal-header-insert mb-0">
                        Add Email
                    </h1>
                    <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="block small-arrow modal-static-ht block-transparent mb-0">
                    <div class="block-content pt-0 row mt-2">
                        <label class="col-sm-3 d-flex align-items-center modal-label" for="testNameSelect">Email</label>
                        <div class="align-items-center col-9 d-flex justify-content-center">
                            <input type="text" class="form-control" name="notification_email" id="notification_email">
                        </div>
                    </div>

                    <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                        <button type="submit" id="addEmailNotificationBtn" class="btn btn-action">
                            <span class="btn-action-gear d-none mr-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                            Add
                            <span class="btn-action-gear d-none ml-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editEmailModel" tabindex="-1" role="dialog" data-backdrop="static"
        aria-labelledby="modal-block-large" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered model-sm" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                    <h1 class="modal-header-insert mb-0">
                        Edit Email
                    </h1>
                    <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="array_index" id="array_index">
                <div class="block small-arrow modal-static-ht block-transparent mb-0">
                    <div class="block-content pt-0 row mt-2">
                        <label class="col-sm-3 d-flex align-items-center modal-label" for="testNameSelect">Email</label>
                        <div class="align-items-center col-9 d-flex justify-content-center">
                            <input type="text" class="form-control" name="edit_sf_email" id="edit_sf_email">
                        </div>
                    </div>

                    <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                        <button type="submit" id="updateEmailBtn" class="btn btn-action">
                            <span class="btn-action-gear d-none mr-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                            Update
                            <span class="btn-action-gear d-none ml-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editEmailNotificationModel" tabindex="-1" role="dialog" data-backdrop="static"
        aria-labelledby="modal-block-large" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered model-sm" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-new align-items-center mb-2 py-2 px-4">
                    <h1 class="modal-header-insert mb-0">
                        Edit Email
                    </h1>
                    <button type="button" class="close close-cross" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="array_notification_index" id="array_notification_index">
                <div class="block small-arrow modal-static-ht block-transparent mb-0">
                    <div class="block-content pt-0 row mt-2">
                        <label class="col-sm-3 d-flex align-items-center modal-label" for="testNameSelect">Email</label>
                        <div class="align-items-center col-9 d-flex justify-content-center">
                            <input type="text" class="form-control" name="edit_notification_email" id="edit_notification_email">
                        </div>
                    </div>

                    <div class="block-content block-content-full  text-right " style="padding-left: 9mm;">
                        <button type="submit" id="updateEmailNotificationBtn" class="btn btn-action">
                            <span class="btn-action-gear d-none mr-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                            Update
                            <span class="btn-action-gear d-none ml-2"><img
                                    src="{{ asset('public/img//cf-menu-icons/gear.png') }}"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('public/dashboard_assets/js/dashmix.core.min.js') }}"></script>

    <script src="{{ asset('public/dashboard_assets/js/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/pages/be_pages_dashboard.min.js') }}"></script>

    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.colVis.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <script
        src="{{ asset('public/dashboard_assets/js/plugins/jquery-validation/jquery.validate.min.js') }}?c={{ rand(1, 999) }}">
    </script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/jquery-validation/additional-methods.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/pages/be_forms_validation.min.js') }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/pages/be_tables_datatables.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/summernote/summernote-bs4.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script defer src="{{ asset('public/dashboard_assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script defer
        src="{{ asset('public/dashboard_assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}">
    </script>

    <script src="{{ asset('public/js/dropify.js') }}"></script>
    <script src="{{ asset('public/js/jquery.floatThead.js') }}"></script>
    <script src="{{ asset('public/js/filepond.min.js') }}"></script>
    <script src="{{ asset('public/js/filepond.jquery.js') }}"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js">
    </script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <script src="{{ asset('public/dashboard_assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        jQuery(function() {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader',
                'rangeslider');
        });
    </script>





    <script>
        $(document).ready(function() {
            const $searchInput = $('#search-input');
            const $searchContainer = $('#search-container');

            // Add focused class when input is focused
            $searchInput.on('focus', function() {
                $searchContainer.addClass('focused');
            });

            // Remove focused class when clicking outside
            $(document).on('click', function(e) {
                if (!$searchContainer.is(e.target) && $searchContainer.has(e.target).length === 0) {
                    $searchContainer.removeClass('focused');
                }
            });
        });

        $(document).on('click', '.btn-email-notification-model', function() {
            $('#addEmailNotificationModel').modal('show');
        })
        $(document).on('click', '.btn-open-email-model', function() {
            $('#addEmailModel').modal('show');
        })

        // Email Notifications

        var emailNotificationArray = [];
        var emailNotificationArrayindex = 0;
        $(document).ready(function() {
            // Load emails on page load
            $.ajax({
                url: '{{url("/get-notification-emails")  }}',
                type: 'GET',
                success: function(response) {
                    response.emails.forEach(function(item) {
                        emailNotificationArray.push({
                            key: emailNotificationArrayindex,
                            email: item.email
                        });
                        emailNotificationArrayindex++;
                    });                    
                    showNotificationEmailCard();
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Error loading emails.');
                }
            });
        });

        $('#addEmailNotificationBtn').on('click', function() {
            var email = $('#notification_email').val().trim();

            if (email === '') {
                showNotification('warning', 'Email cannot be empty.');
                return;
            }
            if (!isValidEmail(email)) {
                showNotification('warning', 'Please enter a valid email address.');
                return;
            }
            if (isDuplicateNotificationEmail(email)) {
                showNotification('warning', 'This email already exists.');
                return;
            }

            emailNotificationArray.push({
                key: emailNotificationArrayindex,
                email: email
            });
            emailNotificationArrayindex++;
            showNotificationEmailCard();
            saveNotificationEmail();
            $('#notification_email').val('');
            $('#addEmailNotificationModel').modal('hide');
        });

        function showNotificationEmailCard() {
            var html = '';
            
            for (var i = 0; i < emailNotificationArray.length; i++) {
                html += `<div class="col-sm-11 mx-auto mb-2  pl-0 mt-3" data-id="2" data-code="34 CFT15">
                                <div class="email-item email-item-2 px-3 py-2 d-flex align-items-center">
                                    <div style="width: 10%;">
                                        <img src="{{ asset('public') }}/client_logos/login.png" alt=""
                                            style="width: 40px;">
                                    </div>

                                    <div style="width: 80%;">
                                        <span class="">${emailNotificationArray[i].email}</span>
                                        <input type="hidden" name='email[]' value="${emailNotificationArray[i].email}">
                                    </div>

                                    <div class="d-flex align-items-center" style="width: 10%;">
                                        <a data="${i}" class="edit-pen edit-pen-selected-module mr-2 email-notification-edit" data-item="34 CFT15"
                                            data-type="Dimension" data-min="43" data-avg="24" data-max="5"
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Edit"
                                            style="display: none;"><img
                                                src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                        </a>

                                        <button data="${i}" type="button" class="close close-cross remove-item email-notification-delete" aria-label="Close"
                                            style="display: none;">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
            }
            $('#email-notification-content').html(html);            
        }

        $(document).on('click', '.email-notification-edit', function() {
            var index = $(this).attr('data');
            $('#array_notification_index').val(index);
            $('#edit_notification_email').val(emailNotificationArray[index].email);
            $('#editEmailNotificationModel').modal('show');
        })
        $(document).on('click', '#updateEmailNotificationBtn', function() {
            var index = parseInt($('#array_notification_index').val(), 10);
            var email = $('#edit_notification_email').val().trim();

            if (email === '') {
                showNotification('warning', 'Email cannot be empty.');
                return;
            }
            if (!isValidEmail(email)) {
                showNotification('warning', 'Please enter a valid email address.');
                return;
            }
            if (isDuplicateNotificationEmail(email, index)) {
                showNotification('warning', 'This email already exists.');
                return;
            }

            emailNotificationArray[index] = {
                key: emailNotificationArray[index].key,
                email: email
            };
            showNotificationEmailCard();
            saveNotificationEmail();
            $('#editEmailNotificationModel').modal('hide');
        });

        $(document).on('click', '.email-notification-delete', function() {
            var id = $(this).attr('data');
            var key = emailNotificationArray[id].key;
            // temp_ip_dns.push(emailNotificationArray[id]);
            emailNotificationArray.splice(id, 1);
            showNotification('warning', 'Email Deleted.');
            showNotificationEmailCard();
            saveNotificationEmail();
        })

        // end Email Notifications

        // Sample Failure Notifications

        var emailArray = [];
        var emailArrayindex = 0;
        $(document).ready(function() {
            // Load emails on page load
            $.ajax({
                url: '{{url("/get-emails")  }}',
                type: 'GET',
                success: function(response) {
                    response.emails.forEach(function(item) {
                        emailArray.push({
                            key: emailArrayindex, // use DB id so update/delete works
                            email: item.email
                        });
                        emailArrayindex++;
                    });

                    showEmailCard(); // refresh UI with fetched emails
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Error loading emails.');
                }
            });
        });

        $('#addEmailBtn').on('click', function() {
            var email = $('#sf_email').val().trim();

            if (email === '') {
                showNotification('warning', 'Email cannot be empty.');
                return;
            }
            if (!isValidEmail(email)) {
                showNotification('warning', 'Please enter a valid email address.');
                return;
            }
            if (isDuplicateEmail(email)) {
                showNotification('warning', 'This email already exists.');
                return;
            }

            emailArray.push({
                key: emailArrayindex,
                email: email
            });
            emailArrayindex++;
            showEmailCard();
            saveEmail()
            $('#sf_email').val('');
            $('#addEmailModel').modal('hide');
        });

        function showEmailCard() {
            var html = '';

            for (var i = 0; i < emailArray.length; i++) {
                html += `<div class="col-sm-11 mx-auto mb-2  pl-0 mt-3" data-id="2" data-code="34 CFT15">
                                <div class="email-item email-item-2 px-3 py-2 d-flex align-items-center">
                                    <div style="width: 10%;">
                                        <img src="{{ asset('public') }}/client_logos/login.png" alt=""
                                            style="width: 40px;">
                                    </div>

                                    <div style="width: 80%;">
                                        <span class="">${emailArray[i].email}</span>
                                        <input type="hidden" name='email[]' value="${emailArray[i].email}">
                                    </div>

                                    <div class="d-flex align-items-center" style="width: 10%;">
                                        <a data="${i}" class="edit-pen edit-pen-selected-module mr-2 email-edit" data-item="34 CFT15"
                                            data-type="Dimension" data-min="43" data-avg="24" data-max="5"
                                            data-custom-class="header-tooltip" data-toggle="tooltip" data-trigger="hover"
                                            data-placement="top" title="" data-original-title="Edit"
                                            style="display: none;"><img
                                                src="public/img/cf-menu-icons/detail-line-edit.png" width="15">
                                        </a>

                                        <button data="${i}" type="button" class="close close-cross remove-item email-delete" aria-label="Close"
                                            style="display: none;">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
            }
            $('#email-content').html(html);
            
        }

        $(document).on('click', '.email-edit', function() {
            var index = $(this).attr('data');
            $('#array_index').val(index);
            $('#edit_sf_email').val(emailArray[index].email);
            $('#editEmailModel').modal('show');
        })
        $(document).on('click', '#updateEmailBtn', function() {
            var index = parseInt($('#array_index').val(), 10);
            var email = $('#edit_sf_email').val().trim();

            if (email === '') {
                showNotification('warning', 'Email cannot be empty.');
                return;
            }
            if (!isValidEmail(email)) {
                showNotification('warning', 'Please enter a valid email address.');
                return;
            }
            if (isDuplicateEmail(email, index)) {
                showNotification('warning', 'This email already exists.');
                return;
            }

            emailArray[index] = {
                key: emailArray[index].key,
                email: email
            };
            showEmailCard();
            saveEmail()
            $('#editEmailModel').modal('hide');
        });

        $(document).on('click', '.email-delete', function() {
            var id = $(this).attr('data');
            var key = emailArray[id].key;
            // temp_ip_dns.push(emailArray[id]);
            emailArray.splice(id, 1);
            showNotification('warning', 'Email Deleted.');
            showEmailCard();
            saveEmail()
        })

        // end Sample Failure Notifications

        $('#saveEmailsBtn').on('click', function() {
            // if (emailArray.length === 0) {
            //     alert('Please add at least one email before saving.');
            //     return;
            // }

            // $.ajax({
            //     url: '{{ url('/save-emails') }}', // route in Laravel
            //     type: 'POST',
            //     data: {
            //         emails: emailArray,
            //         _token: $('meta[name="csrf-token"]').attr('content') // important for Laravel
            //     },
            //     beforeSend: function() {
            //         // Show loading gear animation
            //         $('#saveEmailsBtn .btn-action-gear').removeClass('d-none');
            //         $('#saveEmailsBtn').prop('disabled', true);
            //     },
            //     success: function(response) {
            //         showNotification('warning', response.message);
            //         $('#settingsModal').modal('hide');
            //     },
            //     error: function(xhr) {
            //         console.log(xhr.responseText);
            //         showNotification('error', 'Error saving emails. Please try again.');
            //     },
            //     complete: function() {
            //         // Hide loading gear animation
            //         $('#saveEmailsBtn .btn-action-gear').addClass('d-none');
            //         $('#saveEmailsBtn').prop('disabled', false);
            //     }
            // });
            $('#settingsModal').modal('hide');
        });

        function saveEmail() {
            // if (emailArray.length === 0) {
            //     // alert('Please add at least one email before saving.');
            //     return;
            // }

            $.ajax({
                url: '{{ url('/save-emails') }}', // route in Laravel
                type: 'POST',
                data: {
                    emails: emailArray,
                    _token: $('meta[name="csrf-token"]').attr('content') // important for Laravel
                },
                beforeSend: function() {
                    // Show loading gear animation
                    // $('#saveEmailsBtn .btn-action-gear').removeClass('d-none');
                    // $('#saveEmailsBtn').prop('disabled', true);
                },
                success: function(response) {
                    // showNotification('warning', response.message);
                    // $('#settingsModal').modal('hide');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    // showNotification('error', 'Error saving emails. Please try again.');
                },
                complete: function() {
                    // Hide loading gear animation
                    // $('#saveEmailsBtn .btn-action-gear').addClass('d-none');
                    // $('#saveEmailsBtn').prop('disabled', false);
                }
            });
        }
        function saveNotificationEmail() {
            // if (emailArray.length === 0) {
            //     // alert('Please add at least one email before saving.');
            //     return;
            // }

            $.ajax({
                url: '{{ url('/save-notification-emails') }}', // route in Laravel
                type: 'POST',
                data: {
                    emails: emailNotificationArray,
                    _token: $('meta[name="csrf-token"]').attr('content') // important for Laravel
                },
                beforeSend: function() {
                    // Show loading gear animation
                    // $('#saveEmailsBtn .btn-action-gear').removeClass('d-none');
                    // $('#saveEmailsBtn').prop('disabled', true);
                },
                success: function(response) {
                    // showNotification('warning', response.message);
                    // $('#settingsModal').modal('hide');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    // showNotification('error', 'Error saving emails. Please try again.');
                },
                complete: function() {
                    // Hide loading gear animation
                    // $('#saveEmailsBtn .btn-action-gear').addClass('d-none');
                    // $('#saveEmailsBtn').prop('disabled', false);
                }
            });
        }


        function isValidEmail(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        function isDuplicateEmail(email, excludeIndex = null) {
            return emailArray.some((item, i) => item.email.toLowerCase() === email.toLowerCase() && i !== excludeIndex);
        }
        function isDuplicateNotificationEmail(email, excludeIndex = null) {
            return emailNotificationArray.some((item, i) => item.email.toLowerCase() === email.toLowerCase() && i !== excludeIndex);
        }

        function showNotification(type, message) {
            let icon = '';
            if (type === 'warning') {
                icon = '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> ';
            } else if (type === 'error') {
                icon = '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> ';
            }

            var ErrorMessage = icon + message;
            showCustomWarningNotification(ErrorMessage, "500px");
        }

        function showCustomWarningNotification(message, leftPosition = '20px') {
            $.notify({
                message: message
            }, {
                type: 'info',
                placement: {
                    from: 'bottom',
                    align: 'left'
                },
                offset: {
                    left: leftPosition
                },
                delay: 1000,
                animate: {
                    enter: 'animated fadeIn',
                    exit: 'animated fadeOut'
                },
                template: `
            <div data-notify="container" class="alert alert-{0} col-11 col-sm-4" role="alert" style="position: fixed; bottom: 20px; left: ${leftPosition} !important; z-index: 1033;">
                <button type="button" aria-hidden="true" class="close" data-notify="dismiss" style="position: absolute; right: 10px; top: 5px; z-index: 1035;">×</button>
                <span data-notify="message">{2}</span>
            </div>
        `
            });
        }

        $(document).on('mouseenter', '.email-item', function() {
            var item = $(this);
            item.find('.edit-pen').fadeIn();
            item.find('.remove-item').fadeIn();
        })
        $(document).on('mouseleave', '.email-item', function() {
            var item = $(this);
            item.find('.edit-pen').fadeOut();
            item.find('.remove-item').fadeOut();
        })

        function removeBoxShadow(element) {
            if (element.style.boxShadow) {
                element.style.boxShadow = 'none';
            }
        }

        function watchBoxShadow(selector) {
            $(selector).each(function() {
                removeBoxShadow(this);

                const observer = new MutationObserver(mutations => {
                    mutations.forEach(mutation => {
                        if (mutation.attributeName === 'style') {
                            removeBoxShadow(mutation.target);
                        }
                    });
                });

                observer.observe(this, {
                    attributes: true
                });
            });
        }

        $(document).ready(function() {
            // Watch Select2 box-shadow changes on open
            $(document).on('select2:open', () => {
                watchBoxShadow('.select2-container--default .select2-selection--single');
            });
        });

        $('.dropify').dropify()

        @if (Session::has('settings_success'))



            Dashmix.helpers('notify', {

                align: 'center',

                message: '<img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> {{ Session::get('settings_success') }}',

                delay: 5000

            });
        @endif

        $(function() {

            $('[rel="tooltip"]').on('click', function() {

                $(this).tooltip('hide')

            })

            $('.selectpicker').selectpicker();

            var $table = $('.floathead');

            $table.floatThead({

                top: 67,



                responsiveContainer: function($table) {

                    return $table.closest('.table-responsive');

                }

            });

            var $table1 = $('.floathead1');

            $table1.floatThead({

                top: 67,



                responsiveContainer: function($table) {

                    return $table.closest('.table-responsive');

                }

            });

            $('#accordion2_h1 a').click(function() {



                setTimeout(function() {

                    var reinit = $table.floatThead('destroy');

                    reinit();

                }, 320)

                // ... later you want to re-float the headers with same options



            })

            $('.headerSetting').click(function() {

                $('#settingsModal').modal('show');

            });



            $('select[name=system_country]').change(function() {

                var country = $(this).val();

                $.ajax({

                    type: 'get',

                    data: {

                        country: country

                    },

                    url: "{{ url('get-province') }}",

                    asnyc: false,

                    success: function(res) {



                        var html = '';

                        for (var i = 0; i < res.length; i++) {

                            html += '<option value="' + res[i].state_name + '">' + res[i]

                                .state_name + '</option>';

                        }

                        $('select[name=system_province]').select2('destroy');

                        $('select[name=system_province]').html(html);

                        $('select[name=system_province]').select2()

                    }

                })



            })



            $('#form-settings').submit(function(e) {

                e.preventDefault();

                var company_name = $('input[name=system_company_name]').val()

                var email = $('input[name=system_email]').val()

                var telephone = $('input[name=system_telephone]').val()

                var fax = $('input[name=system_fax]').val()

                var country = $('select[name=system_country]').val()

                var address = $('textarea[name=system_address]').val()

                var city = $('input[name=system_city]').val()

                var province = $('select[name=system_province]').val()

                var postal_code = $('input[name=system_postal_code]').val()

                var email_regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                var postal_regex = /^[A-Za-z0-9]{3}\s?[A-Za-z0-9]{3}$/;

                var tele_regex = /^\d{10}$/;

                if (company_name == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px"  > Please enter value for Company.',

                        delay: 5000

                    });

                } else if (email == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter value for Email Address.',

                        delay: 5000

                    });

                } else if (!email_regex.test(email)) {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Email must be valid email format.',

                        delay: 5000

                    });

                } else if (telephone == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter value for Telephone.',

                        delay: 5000

                    });

                } else if (!tele_regex.test(telephone.split("-").join(""))) {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Telephone, numeric only 10 digits.',

                        delay: 5000

                    });



                } else if (fax != '' && !tele_regex.test(fax.split("-").join(""))) {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Fax, numeric only 10 digits.',

                        delay: 5000

                    });



                } else if (country == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please select value for Country.',

                        delay: 5000

                    });

                } else if (address == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter value for Address.',

                        delay: 5000

                    });

                } else if (city == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter value for city.',

                        delay: 5000

                    });

                } else if (province == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please select value for Province.',

                        delay: 5000

                    });

                } else if (postal_code == '') {

                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Please enter value for Postal Code.',

                        delay: 5000

                    });

                } else if (!postal_regex.test(postal_code)) {



                    Dashmix.helpers('notify', {

                        from: 'bottom',

                        align: 'left',

                        message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="24px" class="mt-n1"> Postal Code, alpha numeric 7 character (3 char,space,3 char)',

                        delay: 5000

                    });

                } else {

                    $('#form-settings')[0].submit()

                }

            })



            $('.select2').select2()

            $("#example1").DataTable({

                'paging': false,

                "responsive": false,

                "lengthChange": false,

                "autoWidth": false,

                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');





            $("#example3").DataTable({

                'paging': true,

                "responsive": false,

                "lengthChange": false,

                "autoWidth": false,

                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]

            }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');





            $("#example4").DataTable({

                'paging': true,

                "responsive": false,

                "lengthChange": false,

                "autoWidth": false,

                "buttons": ["excel", "colvis"]

            }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');





            $("#example5").DataTable({

                'paging': false,

                ordering: false,

                "responsive": false,

                "lengthChange": false,

                "autoWidth": false,

                'searching': false,

                "buttons": ["copy", "csv", "excel", "pdf", "print"]

            }).buttons().container().appendTo('#example5_wrapper .col-md-6:eq(0)');





            $('#example2').DataTable({

                "paging": true,

                "lengthChange": false,

                "searching": true,

                "ordering": true,

                "info": true,

                "autoWidth": false,



            });

            var push = 0;

            $('#pushbtn').click(function() {

                if (push == 0) {

                    push = 1;

                    $('.brand-link').addClass('d-none');



                } else {

                    $('.brand-link').removeClass('d-none');

                    push = 0;

                }

            })

        });
    </script>



    <script>
        $(document).ready(function() {

            // $(document).on("focus", ".form-control", function() {



            //   if ($(this).val() == "") {



            //   } else {

            //     $(this).css({

            //       "-webkit-box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, .6)",

            //       "-moz-box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, 1)",

            //       "box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, 1)"

            //     });

            //   }

            // });



            // $(document).on("focusout", ".form-control", function() {

            //   $(this).css({

            //     "-webkit-box-shadow": "",

            //     "-moz-box-shadow": "",

            //     "box-shadow": ""

            //   });

            // });









            $(document).on("focus", ".form-debit", function() {



                if ($(this).val() == "") {



                } else {

                    $(this).css({

                        "-webkit-box-shadow": "0px 0px 6px 3px rgba(65, 148, 246, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        "-moz-box-shadow": "0px 0px 6px 3px rgba(65, 148, 246, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        "box-shadow": "0px 0px 6px 3px rgba(65, 148, 246, 1)" //"0px 0px 6px 3px rgba(36, 133, 232, 1)"

                    });

                }

            });



            $(document).on("focusout", ".form-debit", function() {

                $(this).css({

                    "-webkit-box-shadow": "",

                    "-moz-box-shadow": "",

                    "box-shadow": ""

                });

            });





            $(document).on("focus", ".form-credit", function() {



                if ($(this).val() == "") {



                } else {

                    $(this).css({

                        "-webkit-box-shadow": "0px 0px 6px 3px rgba(229, 70, 67, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        "-moz-box-shadow": "0px 0px 6px 3px rgba(229, 70, 67, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        "box-shadow": "0px 0px 6px 3px rgba(229, 70, 67, 1)" //"0px 0px 6px 3px rgba(36, 133, 232, 1)"

                    });

                }

            });



            $(document).on("focusout", ".form-credit", function() {

                $(this).css({

                    "-webkit-box-shadow": "",

                    "-moz-box-shadow": "",

                    "box-shadow": ""

                });

            });



            $(document).on("focus", ".form-taxes", function() {



                if ($(this).val() == "") {



                } else {

                    $(this).css({

                        "-webkit-box-shadow": "0px 0px 6px 3px rgba(78, 168, 51, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        "-moz-box-shadow": "0px 0px 6px 3px rgba(78, 168, 51, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        "box-shadow": "0px 0px 6px 3px rgba(78, 168, 51, 1)" //"0px 0px 6px 3px rgba(36, 133, 232, 1)"

                    });

                }

            });



            $(document).on("focusout", ".form-taxes", function() {

                $(this).css({

                    "-webkit-box-shadow": "",

                    "-moz-box-shadow": "",

                    "box-shadow": ""

                });

            });



            $(document).on({

                mouseenter: function() {

                    $(this).css({

                        "max-width": "100%"

                    })

                },

                mouseleave: function() {

                    $(this).css({

                        "max-width": "74.375%"

                    })

                }

            }, '.main-search-input-group');



            $(document).on("focus", ".label-control", function() {



                if ($(this).val() == "") {



                } else {

                    $(this).css({

                        //  "-webkit-box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, .6)",//"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        //  "-moz-box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, 1)",//"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                        //  "box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, 1)"//"0px 0px 6px 3px rgba(36, 133, 232, 1)"





                        "-webkit-box-shadow": "0px 6px 3px -3px rgba(36,134,232,1)",

                        "-moz-box-shadow": "0px 6px 3px -3px rgba(36,134,232,1)",

                        "box-shadow": "0px 6px 3px -3px rgba(36,134,232,1)",

                        "border-bottom": "0",

                    });

                }

            });



            $(document).on("focusout", ".label-control", function() {

                $(this).css({

                    "-webkit-box-shadow": "",

                    "-moz-box-shadow": "",

                    "box-shadow": "",

                    "border-bottom": "1px solid"

                });

            });





            $(document).on("select2:open", ".form-control, .select2-container--open .select2-selection",

                function() {



                    if ($(this).val() == "") {



                    } else {

                        const css = {

                            "-webkit-box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, .6)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                            "-moz-box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, 1)", //"0px 0px 6px 3px rgba(36, 133, 232, 1)",

                            "box-shadow": "0px 0px 6px 3px rgba(36, 133, 232, 1)" //"0px 0px 6px 3px rgba(36, 133, 232, 1)"

                        };



                        var $container = $(this);



                        var classes = [

                            ".select2-container--default.select2-container--focus .select2-selection--multiple",

                            ".select2-container--default.select2-container--focus .select2-selection--single",

                            ".select2-container--default.select2-container--open .select2-selection--multiple",

                            ".select2-container--default.select2-container--open .select2-selection--single",

                            ".select2-container .select2-dropdown .select2-search__field:focus"

                        ];



                        classes.forEach(function(className) {

                            $(className).each(function() {

                                this.style.cssText = $.map(css, function(value, prop) {

                                    return prop + ":" + value + " !important;";

                                }).join("");

                            });

                        });

                    }

                });



            $(document).on("select2:close", ".form-control, .select2-container--open .select2-selection",

                function() {

                    const css = {

                        "-webkit-box-shadow": "",

                        "-moz-box-shadow": "",

                        "box-shadow": ""

                    };

                    var $container = $(this);



                    var classes = [

                        ".select2-container--default.select2-container--focus .select2-selection--multiple",

                        ".select2-container--default.select2-container--focus .select2-selection--single",

                        ".select2-container--default.select2-container--open .select2-selection--multiple",

                        ".select2-container--default.select2-container--open .select2-selection--single",

                        ".select2-container .select2-dropdown .select2-search__field:focus"

                    ];



                    classes.forEach(function(className) {

                        $(className).each(function() {

                            this.style.cssText = $.map(css, function(value, prop) {

                                return prop + ":" + value + " !important;";

                            }).join("");

                        });

                    });

                });



        });
    </script>

    <script>
        $(document).ready(function() {

            $(document).on('click', '.close-modal', function() {

                const target = $(this).attr('target-modal');

                Dashmix.helpers('notify', {

                    from: 'bottom',

                    align: 'left',

                    message: 'Close window? <a href="javascript:;" data="' + target +

                        '" data-notify="dismiss" class="  btn-notify proceed-close-modal ml-4" >Proceed</a>',

                    delay: 5000,

                    type: 'info alert-notify-desktop'

                });

            });

            $(document).on('click', '.proceed-close-modal', function() {

                var target = $(this).attr('data');

                $(target).modal('hide');



            });

        });



        $(document).on({

            mouseenter: function() {

                $(this).find('.irs-single').css('display', 'block');

            },

            mouseleave: function() {

                $(this).find('.irs-single').css('display', 'none');

            },

        }, '.irs--round');







        $(document).on({

            mouseenter: function() {

                $(this).find('img').attr('src', '{{ asset('public/icons2') }}/icon-delete-white.png');

            },

            mouseleave: function() {

                $(this).find('img').attr('src', '{{ asset('public/icons2') }}/icon-delete-grey.png')

            }

        }, '.btnDeleteComment');



        $(document).on({

            mouseenter: function() {

                $(this).find('img').attr('src', '{{ asset('public/icons2') }}/icon-edit-white.png');

            },

            mouseleave: function() {

                $(this).find("img").attr('src', '{{ asset('public/icons2') }}/icon-edit-grey.png');

            }

        }, '.btnEditComment');



        $(document).on({

            mouseenter: function() {

                $(this).find('img').attr('src', '{{ asset('public/icons2') }}/icon-delete-white.png');

            },

            mouseleave: function() {

                $(this).find('img').attr('src', '{{ asset('public/icons2') }}/icon-delete-grey.png');

            }

        }, '.btnDeleteAttachment');
    </script>

    </body>



    </html>

@endsection('footer')
