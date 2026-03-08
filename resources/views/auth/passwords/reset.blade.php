 

 
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>Login - YKK Control Point</title>
 
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <!--     <link rel="shortcut icon" href="{{asset('public')}}/dashboard_assets/media/favicons/favicon.png">
        <link rel="icon" type="image/png" sizes="192x192" href="{{asset('public')}}/dashboard_assets/media/favicons/favicon-192x192.png">
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('public')}}/dashboard_assets/media/favicons/apple-touch-icon-180x180.png"> -->
        <!-- END Icons -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic,300italic,300" rel="stylesheet" type="text/css">
        <!-- Stylesheets -->
        <!-- Fonts and Dashmix framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
        <link rel="stylesheet" id="css-main" href="{{asset('public')}}/dashboard_assets/css/dashmix.min.css">

       <style type="text/css">
            .bg-primary-op{
                background:rgb(56 122 241 / 12%) !important;
            }

            .btn-hero-primary{
                background: #588cb7;
            }
            .text-primary{
                color: #588cb7!important;
            }
            .text-warning{
                color: #f1ac38!important;
            }
        </style>


      <div id="page-container">

            <!-- Main Container -->
            <main id="main-container">
                <!-- Page Content -->
                <div class="bg-image" style="background-image: url('assets/media/photos/photo16@2x.jpg');">
                    <div class="row no-gutters justify-content-center bg-black-75">
                        <div class="hero-static col-sm-8 col-md-6 col-xl-4 d-flex align-items-center p-2 px-sm-0">
                            <!-- Reminder Block -->
                            <div class="block block-transparent block-rounded w-100 mb-0 overflow-hidden">
                                <div class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-white">
                                    <!-- Header -->
                                    <div class="mb-2 text-center">
                                        <a class="link-fx text-warning font-w700 font-size-h1" href="{{url('/')}}">
                                        <img src="{{asset('public/img/logo.png')}}" width="300px">
                                        </a>
                                        <p class="text-uppercase font-w700 font-size-sm text-muted">Password Reset</p>
                                    </div>
                                    <!-- END Header -->

                                    <!-- Reminder Form -->
                                    <!-- jQuery Validation (.js-validation-reminder class is initialized in js/pages/op_auth_reminder.min.js which was auto compiled from _js/pages/op_auth_reminder.js) -->
                                    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                                      <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                           
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"  value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    

                        <div class="form-group row">
                           
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter Password" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                         
                        </div>

                        <div class="form-group row">
                          
                                <input id="password-confirm" type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                       

                        
                          <div class="m-login__form-action text-center mt-4">
                <input type="submit" value="Send " class="btn btn-primary ">
            </div>
                        </div>
                    </form>
                
                                    <!-- END Reminder Form -->
                                </div>
                            </div>
                            <!-- END Reminder Block -->
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->