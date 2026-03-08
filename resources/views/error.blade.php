
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>Amalkitec | Error Page </title>

       

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="assets/media/favicons/favicon.png">
        <link rel="icon" type="image/png" sizes="192x192" href="assets/media/favicons/favicon-192x192.png">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/media/favicons/apple-touch-icon-180x180.png">
        <!-- END Icons -->

        <!-- Stylesheets -->
        <!-- Fonts and Dashmix framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
        <link rel="stylesheet" id="css-main" href="{{asset('public/dashboard_assets/css/dashmix.min.css')}}">

        <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
        <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
        <!-- END Stylesheets -->
    </head>
    <body>
    
            <!-- Main Container -->
            <main id="main-container">
                <!-- Page Content -->
                <div class="bg-image"    >
                    <div class="hero bg-white-95">
                        <div class="hero-inner">
                            <div class="content content-full">
                                <div class="px-3 py-5 text-center">
                                    <div class="row invisible" data-toggle="appear">
                                        <div class="col-sm-6 text-center text-sm-right">
                                            <div class="display-1 text-danger font-w700">404</div>
                                        </div>
                                        <div class="col-sm-6 text-center d-sm-flex align-items-sm-center">
                                            <div class="display-1 text-muted font-w300">Error</div>
                                        </div>
                                    </div>
                                    <h1 class="h2 font-w700 mt-5 mb-3 invisible" data-toggle="appear" data-class="animated fadeInUp" data-timeout="300">{{@$message}}</h1>
                                    <h2 class="h3 font-w400 text-muted mb-5 invisible" data-toggle="appear" data-class="animated fadeInUp" data-timeout="450">Oops.. You just found an error page..</h2>
                                    <div class="invisible" data-toggle="appear" data-class="animated fadeInUp" data-timeout="600">
                                     
                                            @if(@Auth::user()->portal_access!=1)
                                                          <a class="btn btn-hero-secondary"  href="javascript:;" onclick="document.getElementById('logout-form').submit()">
                                            <i class="fa fa-arrow-left mr-1"></i>Logout
                                        </a>                                    <!-- END Side Overlay -->
<form id="logout-form" method="post" action="{{url('logout')}}">
  @csrf
</form>
    @else
                                          
                                        <a class="btn btn-hero-secondary" href="{{url('/')}}">
                                            <i class="fa fa-arrow-left mr-1"></i> Back to Website
                                        </a>
                                    
                                      @endif
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->
            </main>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->

        
        <script src="{{asset('public/dashboard_assets/js/dashmix.core.min.js')}}"></script>

       
        <script src="{{asset('public/dashboard_assets/js/dashmix.app.min.js')}}"></script>
    </body>
</html>
