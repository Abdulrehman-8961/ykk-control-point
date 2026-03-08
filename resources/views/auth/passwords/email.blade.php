 
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>Forgot Password - YKK Control Point</title>
 
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">

        <!-- END Icons -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic,300italic,300" rel="stylesheet" type="text/css">
        <!-- Stylesheets -->
        <!-- Fonts and Dashmix framework -->
<link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&family=Signika:wght@300..700&display=swap"
        rel="stylesheet">
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
/*    width: auto !important;*/
    width: 500px !important;
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
      <div id="page-container">

            <!-- Main Container -->
            <main id="main-container">
                <!-- Page Content -->
                <div class="bg-image" style="background-color: #f0f3f8!important; backgr ound-image: url('assets/media/photos/photo16@2x.jpg');">
                    <div class="row no-gutters justify-content-center bg-bl ack-75">

                        <div class="hero-static d-flex align-items-center p-2 px-sm-0" style="width:500px;">

                            <!-- Reminder Block -->
                            <div class="block block-transparent block-rounded w-100 mb-0 pb-3 overflow-hidden">
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
                                        <form class="js-validatio n-signin" action="{{ route('custom.password.email') }}" method="POST">
                                            @csrf
                                            <div class="pt-3">
                                                <div class="form-group">
                                                    <input id="email" type="email" placeholder="E-mail address" class="form-control form-control-lg form-control-alt @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                    <!-- <input type="text"  autofocus class="form-control form-control-lg form-control-alt  @error('email') is-invalid @enderror" id="login-username" name="email" placeholder="E-mail address"> -->
                                                       
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-block btn-hero-lg signin-btn btn-hero-primary">RESET PASSWORD
                                                </button>
                                            </div>
                                        </form>
                                </div>

                                <!-- <div class="block-content block-content-full px-lg-5 px-xl-6 py-4 py-md-5 py-lg-6 bg-white">

                                    <div class="mb-2 text-center">
                                        <a class="link-fx text-warning font-w700 font-size-h1" href="{{url('/')}}">
                                               <img src="{{asset('public/img/logo.png')}}" width="300px">
                                        </a>
                                        <p class="text-uppercase font-w700 font-size-sm text-muted">Password Reset</p>
                                    </div>
                                      <form class="text-left mt5 " method="post"  action="{{ route('password.email') }}">
                          @csrf
                            <div class="form">
    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                                <div id="username-field" class="form-group">
                                    
                                    <input id="email" type="email" placeholder="Email Address" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback pl-3" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror 
                                </div>
                            </div>
                            <div class="m-login__form-action text-center mt-4">
                <input type="submit" value="Send " class="btn btn-hero-primary ">
            </div>

                    </form>
                                </div> -->
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

@error('email')
<script type="text/javascript">
    jQuery(function() {
        // Ensure Dashmix is available before using
        if (typeof Dashmix !== 'undefined') {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader', 'rangeslider');

            Dashmix.helpers('notify', {
                from: 'bottom',
                align: 'center',
                message: '{{ $message }}',
                delay: 5000
            });
        } else {
            console.error('Dashmix is not loaded');
        }
    });
</script>
@enderror

@if (session('status'))
<script type="text/javascript">
    jQuery(function() {
        if (typeof Dashmix !== 'undefined') {
            Dashmix.helpers('flatpickr', 'simplemde', 'datepicker', 'select2', 'ckeditor', 'notify', 'loader', 'rangeslider');

            Dashmix.helpers('notify', {
                from: 'bottom',
                align: 'center',
                message: '{{ session('status') }}',
                delay: 5000
            });
        } else {
            console.error('Dashmix is not loaded');
        }
    });
</script>
@endif
