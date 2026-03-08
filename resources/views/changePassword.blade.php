  @extends('layouts.header')
  @extends('layouts.sidebar')
  @extends('layouts.footer')
  @section('content')
      <style type="text/css">
          .avatar-upload {
              position: relative;
              max-width: 105px;

          }

          .avatar-upload .avatar-edit {
              position: absolute;
              right: 0px;
              z-index: 1;
              top: -10px;
          }

          .avatar-upload .avatar-edit input {
              display: none;
          }

          .avatar-upload .avatar-edit input+label {
              display: inline-block;
              width: 34px;
              height: 34px;
              margin-bottom: 0;
              border-radius: 100%;
              background: #FFFFFF;
              border: 1px solid transparent;
              box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
              cursor: pointer;
              font-weight: normal;
              transition: all 0.2s ease-in-out;
          }

          .avatar-upload .avatar-edit input+label:hover {
              background: #f1f1f1;
              border-color: #d6d6d6;
          }

          .avatar-upload .avatar-edit input+label:after {
              content: "\f303";
              font-family: 'Font Awesome 5 Free';
              font-weight: 900;
              color: #4194F6;
              position: absolute;
              top: 5px;
              left: 0;
              right: 0;
              text-align: center;
              margin: auto;
          }

          .avatar-upload .avatar-preview {
              width: 100px;
              height: 100px;
              position: relative;
              border-radius: 10;
              border: 6px solid #F8F8F8;
              box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
          }

          .avatar-upload .avatar-preview>div {
              width: 100%;
              height: 100%;

              background-size: cover;
              background-repeat: no-repeat;
              background-position: center;
          }
      </style>


      <!-- Main Container -->
      <main id="main-container">
          <!-- Hero -->
          <!--          <div class="bg-body-light">
                                <div class="content content-full">
                                    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                                        <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">My Profile</h1>
                                        <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                                <li class="breadcrumb-item"><a  href="{{ url('/') }}">Home</a></li>

                                                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                                            </ol>
                                        </nav>
                                    </div>
                                </div>
                            </div> -->
          <!-- END Hero -->

          <!-- Page Content -->
          <div class="content content-full content-boxed">
              <form action="{{ url('update-user-default-session') }}" class="js-validation1" method="POST"
                  enctype="multipart/form-data">
                  <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                  @csrf<div class="block">

                      <div class="block-header block-header-default">
                          <a class="btn btn-light">
                              Session Defaults
                          </a>
                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content">

                          <div class="row justify-content-  push">

                              <div class="col-sm-12 m-auto">


                                  <div class="form-group  ">
                                      <label class="col-form-label" for="example-hf-email">Clients</label>
                                      <select type="" name="client_id" id="client_id"
                                          class="form-control select2" placeholder="">
                                          <option value="" selected>Select</option>
                                          @foreach ($clients as $c)
                                              <option value="{{ $c->id }}"
                                                  {{ Auth::user()->default_client == $c->id ? 'selected' : '' }}>
                                                  {{ $c->display_name }}
                                              </option>
                                          @endforeach
                                      </select>
                                  </div>
                                  <?php
                                  $fiscal_years = DB::table('journals')->where('is_deleted', 0)->distinct('fyear')->orderByDesc('fyear')->pluck('fyear')->toArray('fyear');
                                  ?>
                                  <div class="form-group">
                                      <label class=" col-form-label" for="example-hf-email">Fiscal Year</label>
                                      {{-- <input type="password" class="form-control" id="comfirm_password"
                                          value="{{ old('comfirm_password') }}" name="comfirm_password"
                                          placeholder="Confirm Password"> --}}
                                          <select class="form-control select2" id="fiscal_year"
                                          name="fiscal_year">
                                          <option value="" selected>Select</option>
                                          @foreach ($fiscal_years as $fy)
                                              <option value="{{ $fy }}" {{ Auth::user()->default_fiscal_year == $fy ? 'selected' : '' }}>{{ $fy }}</option>
                                          @endforeach
                                      </select>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="  bg-body-light  ">
                          <div class="row justify-content-center push py-2 pl-4">
                              <div class="col-md-12">
                                  <button type="submit" class="btn btn-alt-warning">
                                      Change
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </form>
              <form action="{{ url('update-user-password') }}" class="js-validation1" method="POST"
                  enctype="multipart/form-data">
                  <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                  @csrf<div class="block">

                      <div class="block-header block-header-default">
                          <a class="btn btn-light">
                              My Profile
                          </a>
                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content">

                          <div class="row justify-content-  push">

                              <div class="col-sm-12 m-auto">


                                  <div class="form-group  ">
                                      <label class="col-form-label" for="example-hf-email">Password</label>

                                      <input type="password" class="form-control" id="password"
                                          value="{{ old('password') }}" name="password" placeholder="Password">


                                  </div>
                                  <div class="form-group">
                                      <label class=" col-form-label" for="example-hf-email">Confirm Password</label>

                                      <input type="password" class="form-control" id="comfirm_password"
                                          value="{{ old('comfirm_password') }}" name="comfirm_password"
                                          placeholder="Confirm Password">


                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="  bg-body-light  ">
                          <div class="row justify-content-center push py-2 pl-4">
                              <div class="col-md-12">
                                  <button type="submit" class="btn btn-alt-warning">
                                      Change
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </form>
              <form action="{{ url('update-user-profile') }}" class="js- " method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                  @csrf<div class="block">

                      <div class="block-header block-header-default">
                          <a class="btn btn-light">
                              Profile Image
                          </a>
                          <div class="block-options">

                          </div>
                      </div>
                      <div class="block-content">

                          <div class="row justify-content-  push">

                              <div class="col-lg-4">
                                  <div class="avatar-upload">
                                      <div class="avatar-edit">
                                          <input type='file' id="imageUpload" name="logo"
                                              accept=".png, .jpg, .jpeg" />
                                          <label for="imageUpload"></label>
                                      </div>
                                      <div class="avatar-preview">
                                          <div id="imagePreview"
                                              style="background-image: url('{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}');">
                                              <input type="hidden" value="{{ Auth::user()->user_image }}"
                                                  name="hidden_img">
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="  bg-body-light  ">
                          <div class="row justify-content-center push py-2 pl-4">
                              <div class="col-md-12">
                                  <button type="submit" class="btn btn-alt-warning">
                                      Change
                                  </button>
                              </div>
                          </div>
                      </div>
                  </div>
              </form>
              <!-- END New Post -->
          </div>
      </main>
  @endsection('content')

  <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
      crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script type="text/javascript">
      $(function() {
          @if (Session::has('success'))
              Swal.fire({
                  title: '{{ Session::get('success') }}',


                  confirmButtonText: 'Ok'
              })
          @endif
          // Init Form Validation


          function readURL(input) {
              if (input.files && input.files[0]) {
                  var reader = new FileReader();
                  reader.onload = function(e) {
                      $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                      $('#imagePreview').hide();
                      $('#imagePreview').fadeIn(650);
                  }
                  reader.readAsDataURL(input.files[0]);
              }
          }


          $("#imageUpload").change(function() {
              readURL(this);
          });


          jQuery('.js-validation1').validate({
              ignore: [],

              rules: {
                  'password': {
                      required: true,

                  },
                  'comfirm_password': {

                      required: true,
                      equalTo: '#password'

                  },


              },

          });

          // Init Validation on Select2 change


          // Init Validation on Select2 change
          jQuery('.js-select2').on('change', e => {
              jQuery(e.currentTarget).valid();
          });




      })
  </script>
