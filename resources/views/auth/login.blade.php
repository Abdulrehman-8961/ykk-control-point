 
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
    <link rel="shortcut icon" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
<link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&family=Signika:wght@300..700&display=swap"
        rel="stylesheet">
        <!-- Stylesheets -->
        <!-- Fonts and Dashmix framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
        <link rel="stylesheet" id="css-main" href="{{asset('public')}}/dashboard_assets/css/dashmix.min.css">

        <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
        <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
        <!-- END Stylesheets -->
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
body{
    overflow-y: hidden;
}            
.login-header{
    width: 100%;
    border-radius: 10px;
/*    border: 1px solid;*/
    border-color: #7f7f7f;
    border-bottom: 0px;
    background-color: #588CB7;
    box-shadow: 0 2pt 5pt #7f7f7f88;
}
.login-content{
    width: 95%;
    margin: 0 auto;
    padding: 20px;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    background-color: #F8FAFC;
    box-shadow: 0 2pt 5pt #7f7f7f88;    
}        
.login-content input,
.login-content input:focus
{
    background-color: #f8fafc !important;
    border: 1px solid #000000!important;
    border-radius: 20px;
    font-size: 13px;
    padding: 8px 20px 8px 20px;    
} 
.signin-btn{
    background-color: #588CB7;
    border: 1px solid #588cb7 !important;
    border-radius: 20px;
    font-size: 13px;
    padding: 8px 20px 8px 20px;
    font-weight: 300;  
} 
.signin-btn:hover{
    background-color: #588CB7;
    box-shadow: 0 .375rem .75rem rgba(4,65,134,.4);
} 
.forgot-link{
    color: #C41E3A;
    font-size: 15px;
    font-family: 'Signika';
    font-weight: 300;    
}   
.forgot-link:hover{
    color: #C41E3A;
    font-weight: 500;    
}   

.alert-info{
    width: auto !important;
    background-color: #262626e8 !important;
/*    background-color: #262626 !important;*/
border-radius: 10px;
    color: #FFFFFF !important;
    font-family: Calibri !important;
    font-size: 14pt !important;
    padding-top: 14px;
    padding-bottom: 14px;
    z-index: 11000 !important;
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
}




.alert-notify-desktop button.close,
.alert-info button.close {
    top: 10px !important;
}

.alert-notify-desktop [data-notify="message"],
.alert-info [data-notify="message"] {
    font-size: 12pt !important;
    font-family: Signika !important;
    font-weight: 300;
}

.alert-info .close {
    color: #BDBDBE !important;
    font-size: 30px !important;
    opacity: 1 !important;
    font-weight: 200 !important;
    width: 33px;
    height: 35px;
    padding-bottom: 3px;
    bottom: 10px;
}
.alert-info .close:hover {
    border-radius: 50%;
    border: 1px solid #BDBDBE;
    background-color: #BFBFBF !important;
}

        </style>
    </head>
    <body>
                <div class="bg-image" style="background-color: #f0f3f8!important; backgr ound-image: url('{{asset("public")}}/dashboard_assets/media/photos/photo22@2x.jpg');">
                    <div class="row no-gutters bg-primary-op">
                        <!-- Main Section -->
                        <div class="hero-static col-md-12 d-flex align-items-center" style="background-color: #f0f3f8!important;">
                            <div class="p-3 w-100">
                                <!-- Header -->
                                <!-- END Header -->

                                <!-- Sign In Form -->
                                <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _js/pages/op_auth_signin.js) -->
                                <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                                <div class="row no-gutters justify-content-center">
                                    <div class="col-sm-4 co l-xl-6">
                                <div class="login-header py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('public/img/cf-menu-icons/ykk-logo.png') }}" class="ml-n1" width="130">
                                        <div class="w-100 text-center">
                                            
                                    <span class="text-white" style="font-family: 'Signika'; font-size: 20px; font-weight: 300; line-height: 1;">ControlPoint</span>
                                        </div>
                                    </div>
                                   <!-- <img src="{{asset('public/img/logo.png')}}" width="300">                             -->
                                </div>
                                <div class="login-content">
                                        <form class="js-validation-signin" action="{{url('login')}}" method="POST">
                                            @csrf
                                            <div class="pt-3">
                                                <div class="form-group">
                                                    <input type="text"  autofocus class="form-control form-control-lg form-control-alt  @error('email') is-invalid @enderror" id="login-username" name="email" placeholder="E-mail address">
                                                        @error('email')
                                    <span class="invalid-feedback pl-3" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror 
                                                </div>
                                                <div class="form-group">
                                                    <input type="password" class="form-control form-control-lg form-control-alt  @error('password') is-invalid @enderror" id="login-password" name="password" placeholder="Password">
                                                      @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-block btn-hero-lg signin-btn btn-hero-primary">
                                                    <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Sign In
                                                </button>
                                                <p class="mt-3 mb-0 d-lg-flex justify-content-lg-between">
                                                    <a class="forgot-link ml-2" href="{{route('password.request')}}">
                                                        <!-- <i class="fa fa-exclamation-triangle text-muted mr-1"></i>  -->
                                                        Forgot password
                                                    </a>
                                               
                                                </p>
                                            </div>
                                        </form>
                                </div>
                                    </div>
                                </div>
                                <!-- END Sign In Form -->
                            </div>
                        </div>
                        <!-- END Main Section -->

                        <!-- Meta Info Section -->
                        <!-- <div class="hero-static col-md-6 d-none d-md-flex align-items-md-center justify-content-md-center text-md-center">
                            <div class="p-3">
                                <p class="display-4 font-w700 text-primary mb-3" style="font-family: 'Good Times', 'Agency FB', sans-serif;">
                            Welcome to <span class="text-warning">YKK Control Point</span> 

                                </p>
                                <p class="font-size-lg font-w600 text-white-75 mb-0">
                                  IT Solutions and Consulting

                                </p>
                            </div>
                        </div> -->
                        <!-- END Meta Info Section -->
                    </div>
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
        </div>
         
        <script src="{{asset('public')}}/dashboard_assets/js/dashmix.core.min.js"></script>

        <!--
            Dashmix JS

            Custom functionality including Blocks/Layout API as well as other vital and optional helpers
            webpack is putting everything together at assets/_js/main/app.js
        -->

        <script src="{{asset('public')}}/dashboard_assets/js/dashmix.app.min.js"></script>

        <script src="{{ asset('public/dashboard_assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <!-- Page JS Plugins -->
        <script src="{{asset('public')}}/dashboard_assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>

        <!-- Page JS Code -->
        <script src="{{asset('public')}}/dashboard_assets/js/pages/op_auth_signin.min.js"></script>
    </body>
</html>
@if ($errors->has('general'))
<script type="text/javascript">
    jQuery(function() {
        // Ensure Dashmix is available before using
        if (typeof Dashmix !== 'undefined') {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader', 'rangeslider');

            Dashmix.helpers('notify', {
                from: 'bottom',
                align: 'center',
                message: '{{ $errors->first('general') }}',
                delay: 1000
            });
        } else {
            console.error('Dashmix is not loaded');
        }
    });
</script>
@endif
