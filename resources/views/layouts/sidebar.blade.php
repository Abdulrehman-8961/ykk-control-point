@section('sidebar')
    <div id="page-container"
        class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed main-content-narrow">

        <!-- Sidebar -->
        <!--
                            Sidebar Mini Mode - Display Helper classes
                            Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
                            Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
                                If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element
                            Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
                            Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
                            Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
                        -->
        <nav id="sidebar" aria-label="Main Navigation" style="background: #21263C;box-shadow:0px 0px 10px black">
            <!-- Side Header -->
            <div class=" " style="background:#588CB7!important ;border: 1px solid #588CB7;">
                <div class="content-header  " style="overflow: hidden;">
                    <!-- Logo -->
                    <a class="w-100" href="{{ url('/') }}">
                        <span class="smini-visible">
                            S<span class="opacity-75">x</span>
                        </span>
                        <span class="smini-hidden d-sm-flex align-items-center">
                            @if (@DB::table('system_settings')->first()->logo)
                                <!-- <img src="{{ asset('public/company_logos/' . @DB::table('system_settings')->first()->logo) }}"
                                            class="ml-n1" width="100%"> <span class=""> -->
                                <img src="{{ asset('public/img/cf-menu-icons/ykk-logo.png') }}" class="ml-n1"
                                    width="100">
                                <span class="text-white ml-2"
                                    style="font-family: 'Signika'; font-size: 18px; font-weight: 300; line-height: 1;">ControlPoint</span>
                            @else
                                <img src="{{ asset('public/img/cf-menu-icons/ykk-logo.png') }}" class="ml-n1"
                                    width="90">
                            @endif
                        </span>
                    </a>
                    <!-- END Logo -->
                    <!-- Options -->
                    <div>
                        <!-- Toggle Sidebar Style -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <!-- Class Toggle, functionality initialized in Helpers.coreToggleClass() -->
                        <!-- END Toggle Sidebar Style -->
                        <!-- Close Sidebar, Visible only on mobile screens -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close"
                            href="javascript:void(0)">
                            <i class="fa fa-times-circle"></i>
                        </a>
                        <!-- END Close Sidebar -->
                    </div>
                    <!-- END Options -->
                </div>
            </div>
            <!-- END Side Header -->
            <!-- Sidebar Scrolling -->
            <div class="js-sidebar-scroll">
                <!-- Side Navigation -->
                <div class="content-side">
                    <ul class="nav-main">
                        @auth
                            @if (@Auth::user()->role == 'admin')
                                <?php
                                $category_color = '#c0c6cc';
                                ?>
                                <!-- <p class="nav-main-link-name pl-3 mb-0" style="color: <?= $category_color ?>;">Admin</p>
                                                <li class="nav-main-item">
                                                    <a class="nav-main-link {{ Request::is('users') ? 'active' : '' }}"
                                                       href="{{ url('/users') }}">
                                                        <img class="nav-main-link-icon"
                                                             src="{{ asset('public/img/cf-menu-icons/main-menu-users.png') }}"
                                                             data-default="{{ asset('public/img/cf-menu-icons/main-menu-users.png') }}"
                                                             data-hover="{{ asset('public/img/cf-menu-icons/main-menu-users-white.png') }}"
                                                             width="20px">
                                                        <span class="nav-main-link-name">Users</span>
                                                    </a>
                                                </li>

                                                <li class="nav-main-item">
                                                    <a class="nav-main-link   {{ Request::is('assetsMachine') ? 'active' : '' }}"
                                                        href="{{ url('/assetsMachine') }}">
                                                        <img class="nav-main-link-icon "
                                                            src="{{ asset('public/img/cf-menu-icons/main-menu-assets.png') }}"
                                                            data-default="{{ asset('public/img/cf-menu-icons/main-menu-assets.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-assets-white.png') }}"
                                                            width="20px">
                                                        <span class="nav-main-link-name ">Assets (Machine)</span>
                                                    </a>
                                                </li> -->
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ Request::is('home') || request()->path() === '/' ? 'active' : '' }}"
                                        href="{{ url('/home') }}">
                                        <span class="nav-main-link-name" style="color: <?= $category_color ?>;">DASHBOARDS</span>
                                    </a>
                                </li>
                                <li class="nav-main-item {{ Request::is('users') || Request::is('assets') ? 'open' : '' }}">
                                    <a class="nav-main-link nav-main-link-submenu    " data-toggle="submenu"
                                        aria-haspopup="true" aria-expanded="true" href="#">
                                        <!-- <img class="nav-main-link-icon " src="{{ asset('public/img/icon-menu-techspecs-grey.png') }}" width="20px" data-src="{{ asset('public/img/icon-menu-techspecs-white.png') }}"> -->
                                        <span class="nav-main-link-name" style="color: <?= $category_color ?>;">ADMIN</span>
                                    </a>

                                    <ul class="nav-main-submenu">
                                        <!--  <li class="nav-main-item ">
                                                        <a class="nav-main-link {{ Request::is('usersOld') ? 'active' : '' }}"
                                                           href="{{ url('/usersOld') }}">
                                                            <img class="nav-main-link-icon"
                                                                 src="{{ asset('public/img/cf-menu-icons/main-menu-users.png') }}"
                                                                 data-default="{{ asset('public/img/cf-menu-icons/main-menu-users.png') }}"
                                                                 data-hover="{{ asset('public/img/cf-menu-icons/main-menu-users-white.png') }}"
                                                                 width="20px">
                                                            <span class="nav-main-link-name">Users Old</span>
                                                        </a>
                                                    </li> -->
                                        <li class="nav-main-item ">
                                            <a class="nav-main-link {{ Request::is('users') ? 'active' : '' }}"
                                                href="{{ url('/users') }}">
                                                <img class="nav-main-link-icon"
                                                    src="{{ asset('public/img/cf-menu-icons/main-menu-users.png') }}"
                                                    data-default="{{ asset('public/img/cf-menu-icons/main-menu-users.png') }}"
                                                    data-hover="{{ asset('public/img/cf-menu-icons/main-menu-users-white.png') }}"
                                                    width="20px">
                                                <span class="nav-main-link-name">Users</span>
                                            </a>
                                        </li>
                                        <li class="nav-main-item ">
                                            <a class="nav-main-link   {{ Request::is('assets') ? 'active' : '' }}"
                                                href="{{ url('/assets') }}">
                                                <img class="nav-main-link-icon "
                                                    src="{{ asset('public/img/cf-menu-icons/main-menu-assets.png') }}"
                                                    data-default="{{ asset('public/img/cf-menu-icons/main-menu-assets.png') }}"
                                                    data-hover="{{ asset('public/img/cf-menu-icons/main-menu-assets-white.png') }}"
                                                    width="20px">
                                                <span class="nav-main-link-name ">Assets (Machine)</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            <!-- @if (@Auth::user()->role != 'write') -->
                            <li
                                class="nav-main-item {{ Request::is('itemcodes') || Request::is('work-orders') || Request::is('item-categories') ? 'open' : '' }}">
                                <a class="nav-main-link nav-main-link-submenu    " data-toggle="submenu" aria-haspopup="true"
                                    aria-expanded="true" href="#">
                                    <!-- <img class="nav-main-link-icon " src="{{ asset('public/img/icon-menu-techspecs-grey.png') }}" width="20px" data-src="{{ asset('public/img/icon-menu-techspecs-white.png') }}"> -->
                                    <span class="nav-main-link-name" style="color: <?= $category_color ?>;">WINGS</span>
                                </a>

                                <ul class="nav-main-submenu">
                                    <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('itemcodes') ? 'active' : '' }}"
                                            href="{{ url('/itemcodes') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-itemcodes.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-itemcodes.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-itemcodes-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Itemcodes</span>
                                        </a>
                                    </li>
                                    <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('work-orders') ? 'active' : '' }}"
                                            href="{{ url('/work-orders') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-wo.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-wo.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-wo-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Workorders</span>
                                        </a>
                                    </li>
                                    {{-- <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('item-categories') ? 'active' : '' }}"
                                            href="{{ url('/item-categories') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-itemcat.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-itemcat.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-itemcat-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Item Categories</span>
                                        </a>
                                    </li> --}}
                                </ul>
                            </li>
                            <!-- <p class="nav-main-link-name pl-3 mb-0 mt-2" style="color: <?= $category_color ?>;">WINGS</p>
                                                <li class="nav-main-item">
                                                    <a class="nav-main-link   {{ Request::is('itemcodes') ? 'active' : '' }}"
                                                        href="{{ url('/itemcodes') }}">
                                                        <img class="nav-main-link-icon "
                                                            src="{{ asset('public/img/cf-menu-icons/main-menu-itemcodes.png') }}"
                                                            data-default="{{ asset('public/img/cf-menu-icons/main-menu-itemcodes.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-itemcodes-white.png') }}"
                                                            width="20px">
                                                        <span class="nav-main-link-name ">Itemcodes</span>
                                                    </a>
                                                </li>
                                                <li class="nav-main-item">
                                                    <a class="nav-main-link   {{ Request::is('workOrders') ? 'active' : '' }}"
                                                        href="{{ url('/workOrders') }}">
                                                        <img class="nav-main-link-icon "
                                                            src="{{ asset('public/img/cf-menu-icons/main-menu-wo.png') }}"
                                                            data-default="{{ asset('public/img/cf-menu-icons/main-menu-wo.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-wo.png') }}"
                                                            width="20px">
                                                        <span class="nav-main-link-name ">Workorders</span>
                                                    </a>
                                                </li>
                                                <li class="nav-main-item">
                                                    <a class="nav-main-link   {{ Request::is('item-categories') ? 'active' : '' }}"
                                                        href="{{ url('/item-categories') }}">
                                                        <img class="nav-main-link-icon "
                                                            src="{{ asset('public/img/cf-menu-icons/main-menu-itemcat.png') }}"
                                                            data-default="{{ asset('public/img/cf-menu-icons/main-menu-itemcat.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-itemcat-white.png') }}"
                                                            width="20px">
                                                        <span class="nav-main-link-name ">Item Categories</span>
                                                    </a>
                                                </li> -->
                            <li
                                class="nav-main-item {{ Request::is('test-definitions') || Request::is('test-thresholds') || Request::is('sample-tests') ? 'open' : '' }}">
                                <a class="nav-main-link nav-main-link-submenu    " data-toggle="submenu" aria-haspopup="true"
                                    aria-expanded="true" href="#">
                                    <!-- <img class="nav-main-link-icon " src="{{ asset('public/img/icon-menu-techspecs-grey.png') }}" width="20px" data-src="{{ asset('public/img/icon-menu-techspecs-white.png') }}"> -->
                                    <span class="nav-main-link-name" style="color: <?= $category_color ?>;">QUALITY
                                        CONTROL</span>
                                </a>

                                <ul class="nav-main-submenu">
                                    <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('test-definitions') ? 'active' : '' }}"
                                            href="{{ url('/test-definitions') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-testdef.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-testdef.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-testdef-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Test Definitions</span>
                                        </a>
                                    </li>
                                    <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('test-thresholds') ? 'active' : '' }}"
                                            href="{{ url('/test-thresholds') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Testing Thresholds</span>
                                        </a>
                                    </li>
                                    <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('sample-tests') ? 'active' : '' }}"
                                            href="{{ url('/sample-tests') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-sampletests.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-sampletests.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-sampletests-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Sample Tests</span>
                                        </a>
                                    </li>
                                    {{-- <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('sample-tests-optimzed') ? 'active' : '' }}"
                                            href="{{ url('/sample-tests-optimzed') }}">
                                            <img class="nav-main-link-icon "
                                                src="{{ asset('public/img/cf-menu-icons/main-menu-sampletests.png') }}"
                                                data-default="{{ asset('public/img/cf-menu-icons/main-menu-sampletests.png') }}"
                                                data-hover="{{ asset('public/img/cf-menu-icons/main-menu-sampletests-white.png') }}"
                                                width="20px">
                                            <span class="nav-main-link-name ">Sample Tests Optimized</span>
                                        </a>
                                    </li> --}}

                                </ul>
                            </li>
                            <li class="nav-main-item">
                                        <a class="nav-main-link   {{ Request::is('sample-tests-ipad') ? 'active' : '' }}"
                                            href="{{ url('/sample-tests-ipad') }}">
                                            <span class="nav-main-link-name ">IPAD</span>
                                        </a>
                                    </li>
                            <!-- @endif -->
                            <!-- <p class="nav-main-link-name pl-3 mb-0 mt-2" style="color: <?= $category_color ?>;">Qaulity Control</p>
                                            <li class="nav-main-item">
                                                <a class="nav-main-link   {{ Request::is('testDefinitions') ? 'active' : '' }}"
                                                    href="{{ url('/testDefinitions') }}">
                                                    <img class="nav-main-link-icon "
                                                        src="{{ asset('public/img/cf-menu-icons/main-menu-testdef.png') }}"
                                                        data-default="{{ asset('public/img/cf-menu-icons/main-menu-testdef.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-testdef-white.png') }}"
                                                        width="20px">
                                                    <span class="nav-main-link-name ">Test Definitions</span>
                                                </a>
                                            </li>
                                            <li class="nav-main-item">
                                                <a class="nav-main-link   {{ Request::is('test-thresholds') ? 'active' : '' }}"
                                                    href="{{ url('/test-thresholds') }}">
                                                    <img class="nav-main-link-icon "
                                                        src="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold.png') }}"
                                                        data-default="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-testthreshold-white.png') }}"
                                                        width="20px">
                                                    <span class="nav-main-link-name ">Testing Thresholds</span>
                                                </a>
                                            </li>
                                            <li class="nav-main-item">
                                                <a class="nav-main-link   {{ Request::is('sampleTests') ? 'active' : '' }}"
                                                    href="{{ url('/sampleTests') }}">
                                                    <img class="nav-main-link-icon "
                                                        src="{{ asset('public/img/cf-menu-icons/main-menu-sampletests.png') }}"
                                                        data-default="{{ asset('public/img/cf-menu-icons/main-menu-sampletests.png') }}"
                                                            data-hover="{{ asset('public/img/cf-menu-icons/main-menu-sampletests-white.png') }}"
                                                        width="20px">
                                                    <span class="nav-main-link-name ">Sample Tests</span>
                                                </a>
                                            </li> -->
                            <!-- <li
                                                class="nav-main-item {{ Request::is('journal/*') ? 'open' : '' }}">
                                                <a class="nav-main-link nav-main-link-submenu    " data-toggle="submenu" aria-haspopup="true"
                                                    aria-expanded="true" href="#">
                                                    <img class="nav-main-link-icon "
                                                        src="{{ asset('public/img/gl-menu-icons/menu-enquiry-grey.png') }}"
                                                        width="20px">
                                                    <span class="nav-main-link-name">Sample Tests</span>
                                                </a>
                                                <ul class="nav-main-submenu">
                                                    <li class="nav-main-item ">
                                                        <a class="nav-main-link {{ Request::is('journal/by-period') ? 'active' : '' }}"
                                                            href="{{ url('/journal/by-period') }}">
                                                            <img class="nav-main-link-icon "
                                                                src="{{ asset('public/img/gl-menu-icons/enquiry-by-period-grey.png') }}"
                                                                width="20px">
                                                            <span class="nav-main-link-name">By Period</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-main-item">
                                                        <a class="nav-main-link {{ Request::is('journal/by-source') ? 'active' : '' }}"
                                                            href="{{ url('/journal/by-source') }}">
                                                            <img class="nav-main-link-icon "
                                                                src="{{ asset('public/img/gl-menu-icons/enquiry-by-source-grey.png') }}"
                                                                width="20px">
                                                            <span class="nav-main-link-name">By Source</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-main-item">
                                                        <a class="nav-main-link {{ Request::is('journal/by-Accounts') ? 'active' : '' }}"
                                                            href="{{ url('/journal/by-Accounts') }}">
                                                            <img class="nav-main-link-icon "
                                                                src="{{ asset('public/img/gl-menu-icons/enquiry-by-account-grey.png') }}"
                                                                width="20px">
                                                            <span class="nav-main-link-name">Schedule of Accounts</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li> -->
                            <!-- <li class="nav-main-item">
                                                <a class="nav-main-link   {{ Request::is('journal/progress-report') ? 'active' : '' }}"
                                                    href="{{ url('/journal/progress-report') }}">
                                                    {{-- <img class="nav-main-link-icon "
                                        src="{{ asset('public/img/gl-menu-icons/gl-menu-remittance-removebg-preview.png') }}"
                                        width="20px"> --}}
                                                    <span class="nav-main-link-name ">Progress Report</span>
                                                </a>
                                            </li> -->

                            @endif
                    </div>
                </div>
                <!-- END Sidebar Scrolling -->
            </nav>
            <!-- END Sidebar -->
        @endsection('sidebar')
        <script type="text/javascript">
            $(document).ready(function() {
                $('.nav-main-link').hover(function() {
                    $(this).find('img').attr('src', $(this).find('img').data('hover'));
                }, function() {
                    // Only revert if not active
                    if (!$(this).hasClass('active')) {
                        $(this).find('img').attr('src', $(this).find('img').data('default'));
                    }
                });

                // Force set hover image for active items on page load
                $('.nav-main-link.active img').each(function() {
                    var hoverImg = $(this).data('hover');
                    if (hoverImg) {
                        $(this).attr('src', hoverImg);
                    }
                });
            });
        </script>
