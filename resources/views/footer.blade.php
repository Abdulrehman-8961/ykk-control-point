@section('footer')
    <!-- Footer -->

    <!-- END Footer -->
    </div>
    <!-- END Page Container -->

    <?php $system_settings = DB::Table('system_settings')->first(); ?>

    <form class="mb-0 pb-0" id="form-settings-old" action="{{ url('update-system-settings') }}" enctype="multipart/form-data"
        method="post">
        @csrf
        <div class="modal fade" id="settingsModalOld" tabindex="-1" role="dialog" data-backdrop="static"
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
                                            {{ $system_settings->salutation == 'CIPM' ? 'selected' : '' }}>CIPM
                                        </option>
                                        <option value="CFA"
                                            {{ $system_settings->salutation == 'CFA' ? 'selected' : '' }}>CFA
                                        </option>
                                        <option value="CBV"
                                            {{ $system_settings->salutation == 'CBV' ? 'selected' : '' }}>CBV
                                        </option>
                                        <option value="CMT"
                                            {{ $system_settings->salutation == 'CMT' ? 'selected' : '' }}>CMT
                                        </option>
                                        <option value="CAIA"
                                            {{ $system_settings->salutation == 'CAIA' ? 'selected' : '' }}>CAIA
                                        </option>
                                        <option value="ChMC"
                                            {{ $system_settings->salutation == 'ChMC' ? 'selected' : '' }}>
                                            ChMC</option>
                                        <option value="CGMA"
                                            {{ $system_settings->salutation == 'CGMA' ? 'selected' : '' }}>
                                            CGMA</option>


                                        <option value="CAPA"
                                            {{ $system_settings->salutation == 'CAPA' ? 'selected' : '' }}>
                                            CAPA</option>
                                        <option value="CAPP"
                                            {{ $system_settings->salutation == 'CAPP' ? 'selected' : '' }}>
                                            CAPP</option>
                                        <option value="CPA"
                                            {{ $system_settings->salutation == 'CPA' ? 'selected' : '' }}>CPA
                                        </option>
                                        <option value="CICS"
                                            {{ $system_settings->salutation == 'CICS' ? 'selected' : '' }}>
                                            CICS</option>
                                        <option value="CICP"
                                            {{ $system_settings->salutation == 'CICP' ? 'selected' : '' }}>
                                            CICP</option>
                                        <option value="CFP"
                                            {{ $system_settings->salutation == 'CFP' ? 'selected' : '' }}>CFP
                                        </option>
                                        <option value="CDFA"
                                            {{ $system_settings->salutation == 'CDFA' ? 'selected' : '' }}>
                                            CDFA</option>
                                        <option value="CAMS"
                                            {{ $system_settings->salutation == 'CAMS' ? 'selected' : '' }}>
                                            CAMS</option>
                                        <option value="CFC"
                                            {{ $system_settings->salutation == 'CFC' ? 'selected' : '' }}>CFC
                                        </option>
                                        <option value="CCUFC"
                                            {{ $system_settings->salutation == 'CCUFC' ? 'selected' : '' }}>
                                            CCUFC</option>
                                        <option value="CLU"
                                            {{ $system_settings->salutation == 'CLU' ? 'selected' : '' }}>CLU
                                        </option>

                                        <option value="CFE"
                                            {{ $system_settings->salutation == 'CFE' ? 'selected' : '' }}>CFE
                                        </option>
                                        <option value="CIA"
                                            {{ $system_settings->salutation == 'CIA' ? 'selected' : '' }}>CIA
                                        </option>

                                        <option value="CRMA"
                                            {{ $system_settings->salutation == 'CRMA' ? 'selected' : '' }}>
                                            CRMA</option>
                                        <option value="CGAP"
                                            {{ $system_settings->salutation == 'CGAP' ? 'selected' : '' }}>
                                            CGAP</option>
                                        <option value="CPP"
                                            {{ $system_settings->salutation == 'CPP' ? 'selected' : '' }}>CPP
                                        </option>
                                        <option value="FPC"
                                            {{ $system_settings->salutation == 'FPC' ? 'selected' : '' }}>FPC
                                        </option>
                                        <option value="CCMT"
                                            {{ $system_settings->salutation == 'CCMT' ? 'selected' : '' }}>
                                            CCMT</option>
                                        <option value="CGFM"
                                            {{ $system_settings->salutation == 'CGFM' ? 'selected' : '' }}>
                                            CGFM</option>
                                        <option value="CGFO"
                                            {{ $system_settings->salutation == 'CGFO' ? 'selected' : '' }}>
                                            CGFO</option>
                                        <option value="CMA"
                                            {{ $system_settings->salutation == 'CMA' ? 'selected' : '' }}>CMA
                                        </option>
                                        <option value="CMFO"
                                            {{ $system_settings->salutation == 'CMFO' ? 'selected' : '' }}>
                                            CMFO</option>
                                        <option value="CPFA"
                                            {{ $system_settings->salutation == 'CPFA' ? 'selected' : '' }}>
                                            CPFA</option>
                                        <option value="CPFO"
                                            {{ $system_settings->salutation == 'CPFO' ? 'selected' : '' }}>
                                            CPFO</option>
                                        <option value="CPCU"
                                            {{ $system_settings->salutation == 'CPCU' ? 'selected' : '' }}>
                                            CPCU</option>
                                        <option value="CDFM"
                                            {{ $system_settings->salutation == 'CDFM' ? 'selected' : '' }}>
                                            CDFM</option>
                                        <option value="CMP"
                                            {{ $system_settings->salutation == 'CMP' ? 'selected' : '' }}>CMP
                                        </option>
                                        <option value="RTRP"
                                            {{ $system_settings->salutation == 'RTRP' ? 'selected' : '' }}>
                                            RTRP</option>
                                        <option value="EA"
                                            {{ $system_settings->salutation == 'EA' ? 'selected' : '' }}>EA
                                        </option>

                                        <option value="FICF"
                                            {{ $system_settings->salutation == 'FICF' ? 'selected' : '' }}>
                                            FICF</option>



                                        <option value="FIC"
                                            {{ $system_settings->salutation == 'FIC' ? 'selected' : '' }}>FIC
                                        </option>
                                        <option value="FP&A"
                                            {{ $system_settings->salutation == 'FP&A' ? 'selected' : '' }}>
                                            FP&A</option>

                                        <option value="ISSP Sustainability"
                                            {{ $system_settings->salutation == 'ISSP Sustainability' ? 'selected' : '' }}>
                                            ISSP
                                            Sustainability</option>
                                        <option value="CTP"
                                            {{ $system_settings->salutation == 'CTP' ? 'selected' : '' }}>CTP
                                        </option>
                                        <option value="CTFA"
                                            {{ $system_settings->salutation == 'CTFA' ? 'selected' : '' }}>
                                            CTFA</option>
                                        <option value="CTP"
                                            {{ $system_settings->salutation == 'CTP' ? 'selected' : '' }}>CTP
                                        </option>

                                        <option value="RFP"
                                            {{ $system_settings->salutation == 'RFP' ? 'selected' : '' }}>RFP
                                        </option>
                                        <option value="FRM"
                                            {{ $system_settings->salutation == 'FRM' ? 'selected' : '' }}>FRM
                                        </option>

                                        <option value="PRM"
                                            {{ $system_settings->salutation == 'PRM' ? 'selected' : '' }}>PRM
                                        </option>
                                        <option value="SCMP"
                                            {{ $system_settings->salutation == 'SCMP' ? 'selected' : '' }}>
                                            SCMP</option>
                                        <option value="LPA"
                                            {{ $system_settings->salutation == 'LPA' ? 'selected' : '' }}>LPA
                                        </option>
                                        <option value="ABA"
                                            {{ $system_settings->salutation == 'ABA' ? 'selected' : '' }}>ABA
                                        </option>
                                        <option value="IACCP"
                                            {{ $system_settings->salutation == 'IACCP' ? 'selected' : '' }}>
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

    

    <script src="{{ asset('public/dashboard_assets/js/dashmix.core.min.js') }}"></script>

    <!--
                                        Dashmix JS

                                        Custom functionality including Blocks/Layout API as well as other vital and optional helpers
                                        webpack is putting everything together at assets/_js/main/app.js
                                    -->
    <script src="{{ asset('public/dashboard_assets/js/dashmix.app.min.js') }}"></script>

    <!-- Page JS Plugins -->
    <script src="{{ asset('public/dashboard_assets/js/plugins/chart.js/Chart.bundle.min.js') }}"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('public/dashboard_assets/js/pages/be_pages_dashboard.min.js') }}"></script>
    <!-- Page JS Plugins -->
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.print.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons/buttons.colVis.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/jquery-validation/additional-methods.js') }}"></script>

    <!-- Page JS Helpers (Select2 plugin) -->

    <!-- Page JS Code -->
    <script src="{{ asset('public/dashboard_assets/js/pages/be_forms_validation.min.js') }}"></script>


    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Page JS Code -->
    <script src="{{ asset('public/dashboard_assets/js/pages/be_tables_datatables.min.js') }}"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/summernote/summernote-bs4.min.js') }}"></script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('public/dashboard_assets/js/plugins/ckeditor/ckeditor.js') }}"></script>
    <script defer="" src="{{ asset('public/dashboard_assets/js/plugins/flatpickr/flatpickr.min.js') }}"></script>
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

    <script>
        jQuery(function() {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader',
                'rangeslider');
        });
    </script>



    <script>
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
