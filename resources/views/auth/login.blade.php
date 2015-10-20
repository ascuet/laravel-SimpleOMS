@extends('app')

@section('title')
登陆 - 棉花糖管理系统
@stop
@section('css')
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
@stop
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">登陆</div>
				<div class="panel-body">
					@include('partials.info')

					<form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">用户名</label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="uid" value="{{ old('uid') }}">
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">密码</label>
							<div class="col-md-6">
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						
						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn btn-primary">登陆</button>

								<!--<a class="btn btn-link" href="{{ url('/password/email') }}">Forgot Your Password?</a>-->
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
