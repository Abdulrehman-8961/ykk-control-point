<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>YKK Control Point</title>


    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/img/cf-menu-icons/favicon.png') }}">
    <!-- END Icons -->
    <link rel="stylesheet"
        href="{{ asset('public/dashboard_assets/js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/dashboard_assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Source+Sans+Pro:wght@200;300;400;600;700;900&family=Signika:wght@300..700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/dashboard_assets/js/plugins/select2/css/select2.min.css') }}">

    <link rel="stylesheet"
        href="{{ asset('public/dashboard_assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/dashboard_assets/js/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('public/dashboard_assets/js/plugins/ion-rangeslider/css/ion.rangeSlider.css') }}">
    <!-- Stylesheets -->
    <!-- Fonts and Dashmix framework -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" id="css-main" href="{{ asset('public/dashboard_assets/css/dashmix.min.css') }}">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet"
        href="{{ asset('public/dashboard_assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/dashboard_assets/js/plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/filepond.css') }}">
    <link rel="stylesheet"
        href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css">
    <link rel="stylesheet" href="{{ asset('public/css/dropify.css') }}">
    <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/xwork.min.css"> -->
    <!-- END Stylesheets -->
    <link rel="stylesheet" href="{{ asset('public/css/field-groups.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css">
        [list]::-webkit-calendar-picker-indicator {
            display: none !important;
        }

        @font-face {
            font-family: 'Signika';
            /* src: url('public/signika.ttf'); */
            src: url('{{ asset('public') }}/signika.ttf') format('truetype');
        }

        @font-face {
            font-family: 'SignikaLight';
            src: url('public/signikalight.ttf');
        }

        @font-face {
            font-family: 'Jura';
            src: url('public/Jura.ttf');
        }

        .bg-main-primary {
            background: #21263C !important;
        }

        .bg-main-light {
            background: #f0f3f8 !important;
        }

        /* transparent autofill */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            transition: background-color 5000s ease-in-out 0s;
            opacity: 1;
        }

        .nav-main-item {
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
        }

        body {
            font-family: Signika;
            overflow-x: hidden;
            overflow-y: hidden;
        }


        .modal-content {
            -webkit-box-shadow: 5px 5px 6px 0px rgba(0, 0, 0, 0.475);
            -moz-box-shadow: 5px 5px 6px 0px rgba(0, 0, 0, 0.475);
            box-shadow: 5px 5px 6px 0px rgba(0, 0, 0, 0.475);
        }


        label,
        .col-form-label {
            font-family: Jura;
            font-size: 12pt !important;
            color: #595959;
            font-weight: normal;
        }

        .table-block-new {
            border-radius: 10px !important;
            border: 1px solid #ECEFF4 !important;
            box-shadow: 2px 2px 5px #d0d3d8 !important;
        }

        .table-block-new:hover {
            background-color: #F5FAFE !important;
        }

        .table-block-new.c-active:hover {
            background: #E6F2FD !important;
            border-color: #2485E8 !important;
        }

        .bg-new-blue {
            background-color: #4194F6;
            border: 1px solid #D9D9D9;
        }

        .bg-new-green {
            background-color: #4EA833;
            border: 1px solid #D9D9D9;
        }

        .bg-new-dark {
            background-color: #595959;
            border: 1px solid #D9D9D9;
        }

        .pk-purple {
            color: #7F649F;
            border-color: #7F649F;
        }

        .pk-red {
            color: red;
            border-color: red;
        }

        .pk-green {
            color: #4EA833;
            border-color: #4EA833;
        }

        .pk-blue {
            color: #31859C;
            border-color: #31859C;
        }

        .pk-danger {
            color: #7F659F;
            border-color: #7F659F;
        }

        .pk-postive {
            border-color: #E54643;
            color: #E54643;
            font-family: Jura;
        }

        .pk-negative {
            border-color: #31859C;
            color: #32859C;
            font-family: Jura;
        }

        .pk-1 {
            font-family: Signika;
            border: 1px solid;
            padding: 0px 10px;
            border-radius: 3px
        }

        .bg-new-yellow {
            background-color: #FFCC00;
            border: 1px solid #D9D9D9;
        }

        .text-orange {
            color: #FFCC00 !important;
        }

        /*.text-grey {
            color: lightgrey !important;
        }*/

        .HostActive {
            font-family: Signika;
            font-size: 1pt;
            font-weight: bold;
            color: #1EFF00;

        }

        .HostInActive {
            font-family: Signika;
            font-size: 1pt;
            font-weight: bold;
            color: #BFBFBF;
        }

        .tooltip-inner {
            text-align: left;
        }

        .badge-new {
            display: flex;
            align-items: center;
            justify-content: center;

            font-family: Signika !important;
            font-size: 11pt !important;
            border-radius: 6px;
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 15px;
            padding-right: 15px;
            width: fit-content;
        }

        /* label {
            color: #3F3F3F;
            font-family: Calibri !important;
            font-size: 14pt !important;
            font-weight: normal;

        } */

        .form-control,
        .select2,
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .selectpicker,
        .bootstrap-select .btn {
            /* border-color: #D7D5E2 !important; */
            border-color: #D7D5E2;
            color: #3F3F3F;
            font-family: Calibri;
    font-weight: 300 !important;
            font-size: 11pt;
            height: 40px;
            border-radius: 10px;
        }

        /* .form-control:focus,
        .select2:focus,
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--single .select2-selection__rendered:focus,
        .selectpicker:focus,
        .bootstrap-select .btn:focus {
            box-shadow: none!important;            
            border-color: #D7D5E2!important;
        } */


        /* .form-control:hover:not(:focus),
        .select2:hover:not(:focus),
        .select2-container--default .select2-selection--single:hover:not(:focus),
        .select2-container--default .select2-selection--single .select2-selection__rendered:hover:not(:focus),
        .selectpicker:hover:not(:focus),
        .bootstrap-select .btn:hover:not(:focus) {
            box-shadow: none!important; 
            border-color: #D7D5E2!important;
        } */

        /* .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--single {
            box-shadow: none!important; 
            border-color: #D7D5E2!important;
        } */

        .select2-container .select2-dropdown .select2-search__field{
            box-shadow: none;
            border-color: #D7D5E2!important;
            font-family: 'Signika';
            font-size: 11pt;
            color: #7f7f7f;
        }
        .select2-selection .select2-selection--single{
            box-shadow: none!important;
        }
        .select2-selection--single{
            box-shadow: none!important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #acacac;
    font-size: 24px;
    cursor: pointer;
}

.select2-container--default .select2-selection--single {
    position: relative;
    padding-right: 20px; /* Space for the clear button */
}


        .select2-container .select2-dropdown .select2-search__field:focus {
            /* -webkit-box-shadow: 0px 0px 1px 1px rgba(36, 133, 232, 1);
            -moz-box-shadow: 0px 0px 1px 1px rgba(36, 133, 232, 1);
            box-shadow: 0px 0px 1px 1px rgba(36, 133, 232, 1); */
            -webkit-box-shadow: 0px 0px 6px 3px rgba(36, 133, 232, 1);
            -moz-box-shadow: 0px 0px 6px 3px rgba(36, 133, 232, 1);
            box-shadow: none;
            border-color: #d7d5e2!important;
            font-family: 'Signika';
            font-size: 11pt;
            color: #7f7f7f;
        }
        .select2-results__option{

        font-family: 'Signika';
    font-weight: 300;
    font-size: 11pt;
    color: #7f7f7f;
        }


        .form-control::placeholder,
        .select2::placeholder,
        .select2-container--default .select2-selection--single::placeholder,
        .select2-container--default .select2-selection--single .select2-selection__rendered::placeholder,
        .selectpicker::placeholder,
        .bootstrap-select .btn::placeholder {
            color: #B6B3CA !important;
        }

        /* .form-debit:hover:not(:focus),
        .form-credit:hover:not(:focus),
        .form-taxes:hover:not(:focus) {
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
        } */

        .label-control:hover:not(:focus) {
            -webkit-box-shadow: 0px 2px 0px 0px rgba(36, 133, 232, .7) !important;
            -moz-box-shadow: 0px 2px 0px 0px rgba(36, 133, 232, .7) !important;
            box-shadow: 0px 2px 0px 0px rgba(36, 133, 232, .7) !important;
        }

        /* .form-debit:focus {
            -webkit-box-shadow: 0px 0px 1px 1px rgba(65, 148, 246, 1);
            -moz-box-shadow: 0px 0px 1px 1px rgba(65, 148, 246, 1);
            box-shadow: 0px 0px 1px 1px rgba(65, 148, 246, 1);
            background-color: #4194F6 !important;
            color: #fff !important;
        } */

        /* .form-credit:focus {
            -webkit-box-shadow: 0px 0px 1px 1px rgba(229, 70, 67, 1);
            -moz-box-shadow: 0px 0px 1px 1px rgba(229, 70, 67, 1);
            box-shadow: 0px 0px 1px 1px rgba(229, 70, 67, 1);
            background: #E54643 !important;
            color: #fff !important
        } */

        /* .form-taxes:focus {
            -webkit-box-shadow: 0px 0px 1px 1px rgba(78, 168, 51, 1);
            -moz-box-shadow: 0px 0px 1px 1px rgba(78, 168, 51, 1);
            box-shadow: 0px 0px 1px 1px rgba(78, 168, 51, 1);
            background: #4EA833 !important;
            color: #fff !important
        } */


        /* .form-debit:hover:not(:focus),
        .form-credit:hover:not(:focus),
        .form-taxes:hover:not(:focus) {
            color: #fff !important;
        } */

        .bootstrap-select .btn {
            background-color: white;
        }

        .bootstrap-select .dropdown-toggle:focus {
            outline: none !important;
        }

        .comments-subtext {
            color: #7F7F7F !important;
            font-family: Signika !important;
            font-size: 9pt !important;
            font-weight: 300;
        }

        .comments-text {

            color: #3F3F3F !important;
            font-family: Signika !important;
            font-size: 14pt !important;
        }

        .comments-section-text {
            color: #595959 !important;
            font-family: Signika !important;
            line-height: 1.3;
            font-size: 11pt !important;
            font-weight: 300;
        }

        .btn-new,
        .btn-new-secondary {
            /* border: 1px solid #D9D9D9; */
            background: white;
            color: #2485E8;
            font-weight: normal;
            font-family: Signika !important;
            font-size: 12pt !important;
            border-radius: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            min-width: 90px;
        }

        .font-10pt {
            font-size: 10pt;
        }

        .font-105pt {
            font-size: 10.5pt;
        }

        .font-11pt {
            font-size: 11pt !important;
        }

        .font-12pt {
            font-size: 12pt !important;
        }

        .attachmentDivNew {

            color: #7F7F7F;
            box-shadow: 0px 0px 10px #ECEFF4 !important;
            padding: 7px 10px;
            width: 100%;
            font-size: 14pt !important;
            border-radius: 10px !important;
            border: 1px solid #ECEFF4;
        }

        .block-content .push {
            margin-bottom: 0px;
        }

        .form-group {
            margin-bottom: 6.25mm !important;
        }

        .btn-new:hover {
            background: #F2F2F2;
            color: #2485E8;

        }

        .modal.show {
            /* background-color: rgba(0, 0, 0, 0.6); */
        }

        .modal-content .btn-block-option {
            border-radius: 1rem;
            color: #A5A5A5;
            background: #fff;
        }



        .modal-content .btn-block-option:hover {
            background: #BFBFBF;
            color: #A5A5A5;
        }

        [data-notify-position="bottom-left"].alert-notify-desktop,
        [data-notify-position="bottom-left"].alert-info {
            left: 110px !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }
[data-notify-position="bottom-left"].alert-itemcode {
    left: 500px !important;
    padding-top: 12px !important;
    padding-bottom: 12px !important;
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
}

        [data-notify-position="bottom-left"].alert-notify-desktop button.close,
        [data-notify-position="bottom-left"].alert-info button.close {
            top: 7px !important
        }

        .alert-notify-desktop [data-notify="message"],
        .alert-info [data-notify="message"] {
            font-size: 12pt !important;
            font-family: SignikaLight !important;
        }

        .alert-info .close {
            color: #BDBDBE !important;
        }
        .alert-info .close:hover {
            border-radius: 50%;
            border: 1px solid #BDBDBE;
        }

        .alert-info a.btn-notify {
            color: #D8D8D8 !important;
            font-family: Signika !important;
            font-size: 12pt !important;
            font-family: bold;
        }

        .alert-info a.btn-notify:hover {
            color: #1EFF00 !important;
            font-family: Signika !important;
            font-size: 12pt !important;
            font-family: bold;
        }


        .modal-content .btn-block-option:active {
            background-color: #7F7F7F;
            color: #F2F2F2;
        }

        .btn-new:active {
            background: #2485E8;
            color: #fff;
        }

        .btn-new-secondary:hover {

            background-color: #A6A6A6;
            color: #FFFFFF !important;
        }

        .new-block-content {
            padding-left: 9mm;
            padding-top: 5mm !important;
            padding-right: 9mm;
        }

        .mandatory {
            color: #E54643 !important;
        }

        .bg-new-red {
            background-color: #E54643 !important;
            border: 1px solid #D9D9D9 !important;
        }

        .text-yellow {
            color: #FFFF00 !important;
        }

        .top-div {
            position: absolute;
            top: 0;
            left: 50%;
            background: #262626;
            color: white;
            border: 1px solid #D9D9D9;
            width: 270px;
            padding-top: 3px;
            padding-bottom: 3px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            font-family: Signika;
            font-size: 12pt;
            transform: translate(-50%, -50%);

        }

        .inner-body-content {
            border: 1px solid #ECEFF4;
            border-radius: 10px;
            padding-top: 30px;
            padding-bottom: 10px;
            box-shadow: 5px 5px 10px #d0d3d8
        }

        .bubble-new {
            color: #7F7F7F;
            font-family: Signika;
            font-size: 12pt;
            padding-top: 5px;
            padding-bottom: 5px;
            border: 1px solid #ECEFF4;
            background: #F2F2F2;
            border-radius: 10px;
            padding-left: 20px;
            padding-right: 20px;
            box-shadow: 0px 0px 5px #d0d3d8;
            min-height: 36px;
        }

        .bubble-text-first {
            /* color: #595959; */
            color: #7F7F7F
        }

        .bubble-text-sec {
            color: #7F7F7F;
        }

        .bubble-white-new {

            font-family: Signika;
            font-size: 12pt;
            padding-top: 5px;
            padding-bottom: 5px;
            border: 1px solid #ECEFF4;
            background: white;
            width: 100%;
            border-radius: 10px;
            padding-left: 20px;
            padding-right: 20px;
            box-shadow: 3px 3px 5px #d0d3d8;
            min-height: 36px;
        }

        .bubble-white-new1 {
            font-weight: normal;
            font-family: Jura;
            font-size: 12pt;
            padding-top: 5px;
            padding-bottom: 5px;
            /* background: #BFBFBF; */
            background: #F2F2F2;
            border: 1px solid #595959;
            width: 100%;
            border-radius: 10px;
            padding-left: 20px;
            padding-right: 20px;
            min-height: 36px;
        }

        .bubble-white-new2 {

            font-family: Jura;
            font-size: 12pt;
            padding-top: 5px;
            padding-bottom: 5px;
            background: #F2F2F2;
            border-color: #ECEFF4;
            color: #252525;
            box-shadow: 3px 3px 6px #BFBFBF !important;
            width: fit-content;
            border-radius: 10px;
            padding-left: 25px;
            padding-right: 25px;
            min-height: 36px;
        }

        .top-right-div {
            position: absolute;
            top: -15px;
            left: 20px;

            color: #3F3F3F;
            border: 1px solid #D9D9D9;
            width: 200px;
            padding-top: 3px;
            padding-bottom: 3px;
            border-radius: 10px;
            font-weight: bold;
            text-align: center;
            font-family: Signika;
            font-size: 12pt;

        }

        .top-right-div-yellow {
            background: #FFCC00;
        }

        .top-right-div-red {
            background: #E54643;
            color: white;
        }

        .top-right-div-blue {
            background: #4194F6;
            color: white;
        }

        .top-right-div-green {
            background: #4EA833;
            color: white;
        }

        .AssetActive {
            min-height: 7mm;
            width: auto;
            border: 1px solid #ECEFF4;
            background-color: #404040;
            color: #1EFF00;
            font-family: Signika;
            font-size: 12pt;
            font-weight: bold;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 10px;
            padding-left: 20px;
            padding-right: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            word-break: break-all;
        }

        .AssetInactive {
            min-height: 7mm;
            word-break: break-all;
            border: 1px solid #ECEFF4;
            background-color: #404040;
            color: #7F7F7F;
            padding-left: 10px;
            padding-right: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            border-radius: 10px;
            text-align: center;
            font-family: Signika;
            font-size: 12pt;
            font-weight: bold;
        }

        .new-nav {
            position: sticky;
            top: 0px;
            z-index: 100;
        }

        .js-task {
            border: 1px solid #ECEFF4;
            box-shadow: 0px 3px 5px #ECEFF4 !important;
            margin-bottom: 20px !important;
            border-radius: 10px !important;
            margin-left: 9px !important;
        }

        .js-task .bg-dark {
            background-color: #000000 !important;

        }



        /*.tooltip-inner {
            background: #3A3B42;
            border-color: #3A3B42;
            font-family: Signika !important;
            font-size: 11pt !important;
            font-weight: bold !important;
            opacity: 1 !important;
            border-radius: 7px;
            padding: 7px 15px;
        }*/
        .tooltip-inner {
            background: #4e4e4e;
            border-color: #4e4e4e;
            font-family: Signika !important;
            font-size: 10pt !important;
            font-weight: 300!important;
            opacity: 1 !important;
            border-radius: 7px;
            padding: 6px 15px;
        }
        .bs-tooltip-auto[x-placement^=top] .arrow::before, .bs-tooltip-top .arrow::before{
            top: 0;
            border-width: .4rem .9rem 0!important;
            border-top-color: #000;
        }
        .bs-tooltip-auto[x-placement^=bottom] .arrow::before, .bs-tooltip-bottom .arrow::before{
            bottom: 0;
            border-width: 0 .9rem .4rem !important;
            border-bottom-color: #000;
        }


        .header-tooltip .tooltip-inner {
            font-size: 10pt !important;
            font-weight: 300 !important;
            font-family: Signika !important;
        }

        .section-header {
            color: #595959;
            font-size: 16pt;
            font-family: 'Signika';
        }

        .new-block {
            border-radius: 10px;
            border: 1px solid lightgrey;
            padding-top: 5mm !important;
            padding-bottom: 5mm !important;
        }

        .tooltip.show {
            opacity: 1 !important;
        }

        .tooltip .arrow {
            border-color: #4E4E4E !important;
            transform: translate(-50%, 0px) !important;
        }

        .card-round {
            border-radius: 7px;
        }

        .header-new-text {
            color: white;
            font-family: Calibri;
            font-size: 18pt;
            font-weight: bold;
        }

        .header-new-subtext {
            color: #EEEEEE;
            font-family: Calibri;
            font-size: 11pt;
            font-weight: normal;
        }

        .py-new-header {
            /* padding-top: 8px;
            padding-bottom: 8px; */
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .py-new-header2 {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
        }

        .new-header-icon-div a {
            padding-left: 10px;
            margin-left: 3px;
            margin-right: 3px;
            padding-right: 10px;

            padding-top: 7px;
            margin-top: 2px;
            padding-bottom: 7px;
            border: 1px solid transparent;
        }


        .new-header-icon-div a:hover {
            background: #3A3B42 !important;
            border: 1px solid transparent !important;
            border-radius: 10px;

        }

        .tooltip .arrow::before {
            border-bottom-color: #4E4E4E !important;
            border-top-color: #4E4E4E !important;
        }

        .error {
            color: #d73838;
        }

        .select2 {
            width: 100% !important;
        }

        .filepond--root {
            min-height: 150px;

        }

        .btn-dual {

            background: #21263C;
            border: none;
        }

        .d1 {
            padding-right: 12px;
            padding-left: 12px;
        }

        .d2 {
            /* padding: 7px 12px; */
            padding: 5px 10px;
        }

        .d3 {
            color: white !important;
            padding: 7px 12px 5px 12px !important;
            border-radius: 5px
        }

        .d3:hover {
            background: #333850;

        }

        .btn-dual:not(:disabled):not(.disabled).active,
        .btn-dual:not(:disabled):not(.disabled):active,
        .show>.btn-dual.dropdown-toggle {
            color: #16181a;
            background-color: #408DFB;
            border-color: #333850;
        }

        .btn-dual:hover,
        .btn-dual:focus {
            /* border-color: #333850 !important;
            background: #333850 !important */
            border-color: #4194F6 !important;
            background: #4194F6 !important;
        }

        .nav-main-submenu {

            padding-left: 0px;
        }

        .nav-main-submenu .nav-main-item a {
            padding-left: 40px;
        }

        .nav-main-item .active {
            border: 2px solid grey;

        }

/*        ::-webkit-scrollbar {
            width: 5px;
            height: 10px;
            background-color: #F5F5F5;
        }

        .col-lg-8::-webkit-scrollbar {
            width: 10px;
            height: 10px;
            background-color: #F5F5F5;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 10px;
            background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0.44, #9e9e9e), color-stop(0.72, #9e9e9e), color-stop(0.86, #9e9e9e));

        }*/

        .bg-orange {
            background-color: orange;
        }

        .thead-dark {
            background-color: #BFBFBF !important;
        }

        .thead-dark th {
            font-size: 9.5pt;
            background-color: #BFBFBF !important;
            border: none !important;
            font-family: Source Sans Pro;
            color: #262626 !important;
            text-transform: uppercase;

        }

        .font-size-h2 {
            font-size: 1.5rem !important;
        }

        .block-title {
            font-size: 0.9rem;
        }

        .TopArea input,
        .TopArea select {
            font-size: 0.9rem !important;
        }

        .TopArea a,
        .TopArea .btn {
            font-size: 0.9rem;
            /* font-size: 1.1rem; */
            /* font-size: .6rem; */
        }

        .TopArea .page-link i {
            /* font-size: 22px !important; */
            font-size: 18px !important;
        }

        .TopArea .page-link {
            padding: 4px 0.75rem !important;
        }



        tbody .btn-group .btn {
            font-size: 0.7rem;
        }

        .content-side {
            padding-left: 0px;
            padding-right: 0px;
        }

        #accordion2_q1 {
            font-size: 0.8rem;
        }

        .c-active {
            background-color: #F5FAFE!important;
            border-color: #2485E8 !important;
        }

        #accordion2_q1 .btn,
        #accordion2_q1 input,
        #accordion2_q1 select {
            font-size: 0.8rem;
        }

        .breadcrumb-item {
            font-size: 0.8rem;
        }

        #accordion2_q1 .bootstrap-select .dropdown-item {
            font-size: 0.8rem !important;
        }

        .modal-backdrop.show {
            opacity: 1;
        }

        .dropdown-menu {
            z-index: 1040;
        }

        .floatThead-container {
            z-index: 999 !important;
        }

        .thead-dark th a {
            color: #262626 !important;
        }

        td {
            color: #0D0D0D !important;
            font-size: 10pt;
            font-family: Source Sans Pro;
        }

        .bg-primary-dark {
            background-color: #BFBFBF !important;
            color: #262626 !important;
            font-family: 15px;
            font-weight: bold;
        }

        .bg-primary-dark * {
            color: #262626 !important;
            font-size: 12pt;
            font-weight: bold;

        }

        .tablemodal td,
        .tablemodal th {
            color: #0D0D0D !important;
            font-family: Source Sans Pro;
            font-size: 9.5pt !important
        }

        #assetdiv th,
        #assetdiv td {
            font-size: 8pt !important;
            color: #0D0D0D;
            font-family: Open Sans
        }

        #page-container {
            min-height: 75vh !important;
        }

        @media (min-width: 1200px) {
            #page-container.main-content-narrow>#main-container .content {
                width: 100%;
            }

            .badge {
                font-size: 13px !important;
            }

            @media (min-width: 768px) {
                .content {
                    width: 100%;
                    margin: 0 auto;
                    padding: 0.5rem 0.5rem 1px;
                    overflow-x: visible;
                }
            }

            .tooltip1 {
                position: relative;
                display: inline-block;
                border-bottom: 1px dotted black;
            }

            .tooltip1 .tooltiptext {
                visibility: hidden;
                width: 230px;

                background-color: #555;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px 0;
                position: absolute;
                z-index: 400000000000000000000;
                bottom: 0%;
                left: 50%;
                margin-left: -60px;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .c4 {
                background-color: #D9D9D9;
                border: 1px solid #7F7F7F;
                color: black !important;
                padding-left: 5px;
                padding-right: 5px;
                font-family: Signika;
                border-style: dashed;
                border-radius: 2px;
                width: fit-content;
                padding-top: 2px;
                padding-bottom: 2px;
            }

            .dropdown-toggle::after {
                display: none !important;
            }

            .c4-p {
                background-color: #343A40;
                border: 1px solid #7F7F7F;
                color: white !important;
                padding-left: 5px;
                padding-right: 5px;
                font-family: Signika;
                border-style: dashed;
                border-radius: 2px;
                width: fit-content;
                padding-top: 2px;
                padding-bottom: 2px;
            }

            .c4-s {
                background-color: #7F659F;
                border: 1px solid #7F659F;
                color: white !important;
                padding-left: 5px;
                padding-right: 5px;
                font-family: Signika;
                border-style: dashed;
                border-radius: 2px;
                width: fit-content;
                padding-top: 2px;
                padding-bottom: 2px;
            }

            .c4-v {
                background-color: #4194F6;
                border: 1px solid #7F7F7F;
                color: white !important;
                padding-left: 5px;
                padding-right: 5px;
                font-family: Signika;
                border-style: dashed;
                border-radius: 2px;
                width: fit-content;
                padding-top: 2px;
                padding-bottom: 2px;
            }

            .c2 {
                font-size: 11pt !important;
                color: black !important;
            }

            .tooltip1:hover .tooltiptext {
                visibility: visible;
                opacity: 1;
            }

            .sidebar-o .nav-main-link {
                padding-left: 20px;
            }

            .sidebar-o .nav-main-link:hover {
                background: #2b3048 !important;
            }

            .sidebar-dark #sidebar .nav-main-link.active {
                background-color: #578CB7 !important;
                border: #578CB7 !important;
            }

            .breadcrumb-item a {
                color: white
            }

            .headerSetting {
                color: white !important;
                padding-top: 8px;
                padding-bottom: 12px;
                padding-left: 13px;
                padding-right: 13px;
                border-radius: 5px
            }

            .headerSetting:hover {
                background: #333850;

            }

            .imgAvatar {
                border: 2px solid #9e9e9e;
                /* width: 40px !important;
                height: 40px !important; */
                width: 28px !important;
                height: 28px !important;
            }

            .imgAvatar:hover {
                border: 2px solid #408DFB
            }

            .page-header {
                background: #21263C !important;
            }

            .searchNew {
                color: #F0F0F0 !important;
                border-color: #6C7184 !important;
                /* background: #21263C !important; */
                background: #333850 !important;
                border-radius: 8px
            }

            .page-header select {
                background: #333850 !important;
                padding: 3px 0px !important;
                border-color: #6C7184 !important;
                border-radius: 7px !important;
                height: 37px;
                color: #F0F0F0 !important;

            }

            .page-header select:hover {
                border-color: #408DFB !important;
            }

            .searchNew:hover+.input-group-append .input-group-text {
                border-top-color: #408DFB !important;
                border-bottom-color: #408DFB !important;
                border-right-color: #408DFB !important;

            }

            .searchNew:hover {
                border-top-color: #408DFB !important;
                border-bottom-color: #408DFB !important;
                border-left-color: #408DFB !important;

            }

            .input-group-text {
                /* background: transparent; */
                background: #333850 !important;
                padding-left: 7px;
                padding-right: 7px;
                border-color: #6C7184;
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px;
            }

            .pagination {

                border-color: #6C7184 !important;
                font-family: Signika;
                border-radius: 0px !important;

            }

            .pagination a {
                background: #333850 !important;
                color: #F0F0F0 !important;
                border-color: #6C7184 !important;
                border-radius: 0px !important;
                border-right: none;
                border-left: none;
            }

            .pagination a:hover {
                background: #171D29 !important;
            }

            .pagination .active a {

                background: #408DFB !important;

                border-color: #6C7184 !important;

            }



            /*.input-group {
                max-width: 100%;
                transition: max-width 0.3s ease-in-out;
            }*/

            /* .input-group:hover,
            .input-group:focus {
                max-width: 90%;
        }

        */
            .searchNew {
                width: 100%;
            }

            .input-group-append {
                /* width: 50px; */
                width: 39px;
                overflow: hidden;
                transition: width 0.3s ease-in-out;
            }

            .input-group:hover .input-group-append,
            .input-group:focus .input-group-append {
                width: auto;
            }



            main .content {
                padding-left: 15px !important;
            }


            .js-task .rounded {
                border-radius: 0.45rem !important;
            }

            .attachmentDivNew {
                font-size: 9pt !important;
            }

            .attach-first-col .js-task {
                margin-left: 9px !important;
            }

            .attach-other-col .js-task {
                margin-left: 18px !important;
            }

            .attach-first-col .js-task,
            .attach-other-col .js-task {
                margin-bottom: 15px !important;
            }

            main .TopArea .search-col {
/*                padding-left: 24px !important;*/
            }


            .viewContent {
                /* border: 1px solid #7F7F7F !important; */
            }

            .irs--round {
                height: auto !important;
                top: -18px !important;
            }

            .irs.irs--round .irs-single {
                display: none;
            }

            .irs--round .irs-handle {
                background: #2485E8 !important;
                border-color: #C0DBFC !important;
                width: 20px !important;
                height: 20px !important;
            }


            .irs.irs--round .irs-bar,
            .irs.irs--round .irs-from,
            .irs.irs--round .irs-single,
            .irs.irs--round .irs-to {
                background: #2485E8 !important;
            }

            .irs.irs--round .irs-grid-pol,
            .irs.irs--round .irs-line,
            .irs.irs--round .irs-max,
            .irs.irs--round .irs-min {
                background: #C0DBFC !important;
            }

            .irs.irs--round .irs-single {
                background: #3A3B42 !important;
                color: #fff !important;
                padding-left: 17px !important;
                padding-right: 17px !important;
                padding-top: 11px !important;
                padding-bottom: 11px !important;
            }

            .irs-from,
            .irs-to,
            .irs-single {
                top: -19px !important;
            }

            .irs--round .irs-from,
            .irs--round .irs-to,
            .irs--round .irs-single {
                font-size: 10pt !important;
            }

            .irs--round .irs-single:before {
                border-top-color: #3A3B42 !important;
            }

            .irs--round .irs-from,
            .irs--round .irs-to,
            .irs--round .irs-single {
                /* line-height: .1 !important; */
                border-radius: 7px !important;
            }

            .irs .irs-min,
            .irs .irs-max {
                display: none !important;
            }

            .irs--round .irs-from:before,
            .irs--round .irs-to:before,
            .irs--round .irs-single:before {
                margin-left: -7px;
                bottom: -16px;
                border: 8px solid transparent;
            }

            .irs.irs--round .irs-bar,
            .irs.irs--round .irs-line {
                height: 3px !important;
            }

            .irs {
                font-family: Signika !important;
                font-weight: bold !important;
            }

            .box-dark {
                font-family: Signika;
                background: #595959 !important;
                color: #f2f2f2 !important;
                border: 0 !important;
            }


            .label-control {
                border-left: 0px !important;
                border-right: 0px !important;
                border-top: 0px !important;
                border-radius: 0 !important;
            }

            .label-control:focus {

                -webkit-box-shadow: 0px 2px 0px 0px rgba(36, 133, 232, 1) !important;
                -moz-box-shadow: 0px 2px 0px 0px rgba(36, 133, 232, 1) !important;
                box-shadow: 0px 2px 0px 0px rgba(36, 133, 232, 1) !important;
            }

            .custom-control-taxable .custom-control-label::before {
                border: 1px solid #BFBFBF;
                background: #BFBFBF;
                border-radius: 3px;
            }

            .custom-control-taxable .custom-control-input:focus~.custom-control-label::before {
                box-shadow: none;
            }

            .custom-control-taxable .custom-control-label {
                border: 1px solid #BFBFBF;
                background: #BFBFBF;
            }

            .custom-control-taxable .custom-control-input:checked~.custom-control-label::before {
                background-color: #F2F2F2;
                border-color: #F2F2F2;
            }

            .custom-control-taxable .custom-control-input:focus:not(:checked)~.custom-control-label::before {
                border-color: #F2F2F2;
            }



            .custom-control-taxable .custom-control-input:checked~.custom-control-label {
                border-color: #F2F2F2;
                background-color: #F2F2F2;
            }



            .custom-control-label::before {
                border: 1px solid #D7D5E2;
                background: #fff;
                border-radius: 3px;
            }

            .custom-control-input:focus~.custom-control-label::before {
                box-shadow: none;
                border: 1px solid #2485E8;
            }

            .custom-control-input:checked~.custom-control-label::before {
                background-color: #2485E8;
                border-color: #2485E8;
            }

            .custom-control-input:focus:not(:checked)~.custom-control-label::before {
                border-color: #2485E8;
            }


            .ActionIcon {
                padding: 2px;

            }

            .fg-evenly {
                margin-bottom: 4.75mm !important;
            }

            .proceed-close-modal,
            .btn-notify {
                position: initial !important;
            }

            .content-header {
                height: 56px !important;
            }

            .read-mode-active {
                background-color: #595959 !important;
                color: #fff !important;
                border: 1px solid #D9D9D9 !important;
            }

            .read-mode-inactive {
                /*background-color: #595959 !important;**/
                background-color: #BFBFBF !important;
                color: #595959 !important;
                border: 1px solid #D9D9D9 !important;
            }

            .badge-tag {
                font-size: 10pt;
                font-family: Signika;
                font-weight: normal;
                color: #7F7F7F;
                border: 1px solid #7F7F7F;
                padding: 5px 13px;
                line-height: 3;
                border-radius: 0.5rem;
                text-align: center;
            }



            #showData .new-block {
                margin-bottom: 0.875rem !important;
            }


            .pagination .page-link {
                font-weight: normal !important;
            }

            #ajax-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
            }
    </style>
</head>

<body>

    @yield('sidebar')

    <!-- Header -->
    <header id="page-header" class="page-header" style="">
        <!-- Header Content -->
        <div class="content-header w-100 px-3" style="
    padding-top: 28px;
    padding-bottom: 27px;
">
            <!-- Left Section -->
            <div>

                <!--   <button type="button" class="btn btn-dual mt-2" data-toggle="layout" data-action="sidebar_toggle">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                      -->

                <nav class="flex-sm-00-auto ml-sm-3 float-right" aria-label="breadcrumb">

                    <ol class="breadcrumb">


                        @if (Request::is('clients'))
                            <li class="breadcrumb-item active text-dark"> <b>Clients</b> </li>

                        @endif
                        @if (Request::is('add-clients'))
                            <li class="breadcrumb-item  "><a href="{{ url('clients') }}"> Clients</a> </li>
                            <li class="breadcrumb-item active text-dark "><b>Add Clients</b></li>
                        @endif

                        @if (Request::is('edit-clients'))
                            <li class="breadcrumb-item  "><a href="{{ url('clients') }}"> Clients</a> </li>
                            <li class="breadcrumb-item active text-dark "><b>Edit Clients</b></li>
                        @endif

                        @if (Request::is('users'))
                            <li class="breadcrumb-item active text-dark"><b> Users</b> </li>
                        @endif

                        @if (Request::is('add-users'))
                            <li class="breadcrumb-item  "><a href="{{ url('users') }}"> Users</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Add User</b> </li>
                        @endif
                        @if (Request::is('edit-users'))
                            <li class="breadcrumb-item  "><a href="{{ url('users') }}"> Users</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Edit User</b> </li>
                        @endif
                        @if (Request::is('vendors'))

                            <li class="breadcrumb-item active text-dark"><b> Vendors</b> </li>
                        @endif
                        @if (Request::is('add-vendors'))
                            <li class="breadcrumb-item  "><a href="{{ url('vendors') }}"> Vendors</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Add Vendors</b></li>
                        @endif
                        @if (Request::is('edit-vendors'))
                            <li class="breadcrumb-item  "><a href="{{ url('vendors') }}"> Vendors</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Edit Vendors</b></li>
                        @endif
                        @if (Request::is('distributors'))
                            <li class="breadcrumb-item active text-dark"><b> Distributors</b> </li>
                        @endif
                        @if (Request::is('add-distributors'))
                            <li class="breadcrumb-item  "><a href="{{ url('distributors') }}"> Distributors</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Add Distributors</b></li>
                        @endif

                        @if (Request::is('edit-distributors'))
                            <li class="breadcrumb-item  "><a href="{{ url('distributors') }}"> Distributors</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Edit Distributors</b></li>
                        @endif

                        @if (Request::is('sites'))
                            <li class="breadcrumb-item active text-dark"><b> Sites</b> </li>
                        @endif
                        @if (Request::is('add-sites'))
                            <li class="breadcrumb-item  "><a href="{{ url('sites') }}"> Sites</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Add Sites</b></li>
                        @endif

                        @if (Request::is('edit-sites'))
                            <li class="breadcrumb-item  "><a href="{{ url('sites') }}"> Sites</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Edit Sites</b></li>
                        @endif

                        @if (Request::is('operating-systems'))
                            <li class="breadcrumb-item active text-dark"><b> Operating Systems</b> </li>
                        @endif
                        @if (Request::is('add-operating-systems'))
                            <li class="breadcrumb-item  "><a href="{{ url('/operating-systems') }}"> Operating
                                    Systems</a>
                            </li>
                            <li class="breadcrumb-item active text-dark"> <b>Add Operating Systems<b></li>
                        @endif

                        @if (Request::is('edit-operating-systems'))
                            <li class="breadcrumb-item  "><a href="{{ url('/operating-systems') }}"> Operating
                                    Systems</a>
                            </li>
                            <li class="breadcrumb-item active text-dark"><b> Edit Operating Systems</b></li>
                        @endif


                        @if (Request::is('domains'))
                            <li class="breadcrumb-item active text-dark"><b> Domains</b> </li>
                        @endif
                        @if (Request::is('add-domains'))
                            <li class="breadcrumb-item  "><a href="{{ url('/domains') }}">Domains</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Add Domains<b></li>
                        @endif

                        @if (Request::is('edit-domains'))
                            <li class="breadcrumb-item  "><a href="{{ url('/domains') }}"> Domains</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Edit Domains</b></li>
                        @endif


                        @if (Request::is('asset-type'))
                            <li class="breadcrumb-item active text-dark"><b> Asset Type</b> </li>
                        @endif
                        @if (Request::is('add-asset-type'))
                            <li class="breadcrumb-item  "><a href="{{ url('/asset-type') }}">Asset Type</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Add Asset Type<b></li>
                        @endif

                        @if (Request::is('edit-asset-type'))
                            <li class="breadcrumb-item  "><a href="{{ url('/asset-type') }}"> Asset Type</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Edit Asset Type</b></li>
                        @endif



                        @if (Request::is('network'))
                            <li class="breadcrumb-item active text-dark"><b> Network</b> </li>
                        @endif
                        @if (Request::is('add-network'))
                            <li class="breadcrumb-item  "><a href="{{ url('/network') }}">Network</a> </li>
                            <li class="breadcrumb-item active text-dark"> <b>Add Network<b></li>
                        @endif

                        @if (Request::is('edit-network'))
                            <li class="breadcrumb-item  "><a href="{{ url('/network') }}"> Network</a> </li>
                            <li class="breadcrumb-item active text-dark"><b> Edit Network</b></li>
                        @endif


                        @if (Request::is('virtual'))
                            <li class="breadcrumb-item active text-dark"><b> Virtual </b> </li>
                        @endif
                        @if (Request::is('virtual/*'))
                            <li class="breadcrumb-item active text-dark"><b> Virtual / <span
                                        class="text-capitalize">{{ $page_type }}</span></b> </li>
                        @endif

                        @if (Request::is('add-assets/*'))
                            @if ($type == 'virtual')
                                <li class="breadcrumb-item  "><a href="{{ url('/virtual') }}">Virtual</a> </li>
                            @else
                                <li class="breadcrumb-item  "><a href="{{ url('/physical') }}">Physical</a> </li>
                            @endif
                            <li class="breadcrumb-item active text-dark"> <b>Add {{ $type }} Asset<b></li>
                        @endif

                        @if (Request::is('edit-assets'))
                            @if ($type == 'virtual')
                                <li class="breadcrumb-item  "><a href="{{ url('/virtual') }}">Virtual</a> </li>
                            @else
                                <li class="breadcrumb-item  "><a href="{{ url('/physical') }}">Physical</a> </li>
                            @endif
                            <li class="breadcrumb-item active text-dark"><b> Edit {{ $type }} Asset</b></li>
                        @endif



                        @if (Request::is('physical'))
                            <li class="breadcrumb-item active text-dark"><b> Physical </b> </li>
                        @endif

                        @if (Request::is('physical/*'))
                            <li class="breadcrumb-item active text-dark"><b> Physical / <span
                                        class="text-capitalize">{{ $page_type }}</span></b> </li>
                        @endif





                        @if (Request::is('contract/*'))
                            <li class="breadcrumb-item active text-dark"><b> Contract / <span
                                        class="text-capitalize">{{ $type }}</span></b> </li>
                        @endif
                        @if (Request::is('contract'))
                            <li class="breadcrumb-item active text-dark"><b> Contract / <span
                                        class="text-capitalize">All</span></b> </li>
                        @endif

                        @if (Request::is('add-contract/*'))

                            <li class="breadcrumb-item  "><a href="{{ url('/contract/') }}/{{ $type }}"
                                    class="text-capitalize">{{ $type }}</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add {{ $type }} Contract<b></li>
                        @endif

                        @if (Request::is('edit-contract'))

                            <li class="breadcrumb-item active text-dark"><b> Edit Contract</b></li>
                        @endif

                        @if (Request::is('renew-contract'))

                            <li class="breadcrumb-item active text-dark"><b>Renew Contract</b></li>
                        @endif
                        @if (Request::is('ssl-certificate'))

                            <li class="breadcrumb-item active text-dark"><b>SSL Certificate</b></li>
                        @endif

                        @if (Request::is('add-ssl-certificate'))

                            <li class="breadcrumb-item  "><a href="{{ url('/ssl-certificate') }}"
                                    class="text-capitalize">SSL
                                    Certificate</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Add SSL Certificate<b></li>
                        @endif

                        @if (Request::is('edit-ssl-certificate'))

                            <li class="breadcrumb-item  "><a href="{{ url('/ssl-certificate') }}"
                                    class="text-capitalize">SSL
                                    Certificate</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Edit SSL Certificate<b></li>
                        @endif

                        @if (Request::is('renew-ssl-certificate'))

                            <li class="breadcrumb-item  "><a href="{{ url('/ssl-certificate') }}"
                                    class="text-capitalize">SSL
                                    Certificate</a> </li>

                            <li class="breadcrumb-item active text-dark"> <b>Renew SSL Certificate<b></li>
                        @endif

                        @if (Request::is('notifications'))


                        @endif


                    </ol>


                </nav>



            </div>
            <!-- END Left Section -->

            <!-- Right Section -->
            <div>

                @if (@Auth::user()->role == 'admin')

                    <a href="javascript:;" data-toggle="tooltip" data-custom-class="header-tooltip"
                        data-title="Settings" class="mr-2 text-dark headerSetting"><img
                            src="{{ asset('public/img/ui-icon-settings.png') }}" width="16.1px"></a>

                @endif
                <!-- User Dropdown -->
                <div class="dropdown d-inline-block">
                    <a type="button" class="  " id="page-header-user-dropdown" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        @if (@Auth::user()->user_image == '')
                            <img class="img-avatar imgAvatar img-avatar48"
                                src="{{ asset('public') }}/dashboard_assets/media/avatars/avatar2.jpg"
                                alt="">
                        @else
                            <img class="img-avatar imgAvatar img-avatar48"
                                src="{{ asset('public/client_logos/') }}/{{ Auth::user()->user_image }}"
                                alt="">

                        @endif

                    </a>
                    <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown">

                        <div class="p-2">
                            @auth
                                <a class="dropdown-item" href="{{ url('change-password') }}">
                                    <i class="far fa-fw fa-user mr-1"></i> My Profile
                                </a>





                                <!-- END Side Overlay -->
                                <form id="logout-form" method="post" action="{{ url('logout') }}">
                                    @csrf
                                </form>
                                <div role="separator" class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:;"
                                    onclick="document.getElementById('logout-form').submit()">
                                    <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                </a>
                            @else
                                <a class="dropdown-item" href="{{ url('/login') }}">
                                    <i class="far fa-fw fa-user mr-1"></i> Login
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- END User Dropdown -->

                    <!-- Notifications Dropdown -->

                    <!-- Toggle Side Overlay -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->

                    <!-- END Toggle Side Overlay -->
                </div>
                <!-- END Right Section -->
            </div>
            <!-- END Header Content -->


            <!-- Header Loader -->
            <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
            <div id="page-header-loader" class="overlay-header bg-header-dark">
                <div class="bg-white-10">
                    <div class="content-header">
                        <div class="w-100 text-center">
                            <i class="fa fa-fw fa-sun fa-spin text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Header Loader -->
        </header>
        @yield('content')
        @yield('footer')
