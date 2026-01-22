	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<title> {{ $site_name ?? 'RoomUnite' }} | Dashboard </title>

			<!-- Tell the browser to be responsive to screen width -->
			<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			<!-- Bootstrap 3.3.6 -->
			<link rel="stylesheet" href="{{ asset('backend/bootstrap/css/bootstrap.min.css') }}">
			<!-- Font Awesome -->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
			<!-- Ionicons -->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
			<!-- Theme style -->
			<link rel="stylesheet" href="{{ asset('backend/dist/css/AdminLTE.css') }}">
			<!-- Custom css -->
			<link rel="stylesheet" href="{{ asset('backend/dist/css/custom.css') }}">

			<!-- AdminLTE Skins. Choose a skin from the css/skins
				folder instead of downloading all of them to reduce the load. -->
			<link rel="stylesheet" href="{{ asset('backend/dist/css/skins/_all-skins.css') }}">
			<!-- iCheck -->
			<link rel="stylesheet" href="{{ asset('backend/plugins/iCheck/flat/blue.css') }}">
			<!-- Morris chart -->
			<link rel="stylesheet" href="{{ asset('backend/plugins/morris/morris.css') }}">
			<!-- jvectormap -->
			<link rel="stylesheet" href="{{ asset('backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
			<!-- Date Picker -->
			<link rel="stylesheet" href="{{ asset('backend/plugins/datepicker/datepicker3.css') }}">
			<!-- Daterange picker -->
			<link rel="stylesheet" href="{{ asset('backend/plugins/daterangepicker/daterangepicker.css') }}">
			<!-- bootstrap wysihtml5 - text editor -->
			<link rel="stylesheet" href="{{ asset('backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
		
			<!--datatable style-->
			<link rel="stylesheet" href="{{ asset('backend/plugins/datatables/dataTables.bootstrap.css') }}">
			<link rel="stylesheet" href="{{ asset('backend/plugins/datatables/jquery.dataTables.css') }}">
			<link rel="stylesheet" href="{{ asset('backend/plugins/DataTables-1.10.18/css/jquery.dataTables.min.css') }}">
			<link rel="stylesheet" href="{{ asset('backend/plugins/Responsive-2.2.2/css/responsive.dataTables.min.css') }}">
			<!--Select2-->

			<link rel="stylesheet" type="text/css" href="{{ asset('js/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">  
			<link href="{{ asset('backend/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
			<link href="{{ asset('backend/css/style2.css') }}" rel="stylesheet" type="text/css" /> 
			<link href="{{ asset('backend/css/style.css') }}" rel="stylesheet" type="text/css" /> 
			<link href="{{ asset('css/glyphicon.css') }}" rel="stylesheet" type="text/css" />

			<!-- Place the first <script> tag in your HTML's <head> -->
			<script src="https://cdn.tiny.cloud/1/s63i07jv6zzmjbvh1jnemybwthrhjcju5p3n1jsagq3wsp25/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>


			@stack('css')
		</head>
	<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">