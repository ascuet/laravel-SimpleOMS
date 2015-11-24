<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset('/favicon.ico') }}">

	<title>@yield('title')</title>
    <!-- Bootstrap core CSS -->
    @yield('css')
    <!--<link href="{{ asset('/css/app.css') }}" rel="stylesheet">-->
    <!-- Custom styles for this template -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

<body>

	<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">棉花糖</a>
				</div>

				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					@yield('nav')
					
				</div>
			</div>
	</nav>

	@yield('content')

	@yield('actionNav')
	<!-- Scripts -->
	<script src="{{ asset('/js/jquery-1.11.2.min.js') }}"></script>
	<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/js/jquery.datetimepicker.full.js') }}"></script>
	@yield('js')
	
</body>
</html>
