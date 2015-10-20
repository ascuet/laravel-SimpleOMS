@extends('base')
@section('content')
	
<div class="container">
	<h1>用户 {{$user->uid}}</h1>
	
	<form action="{{url('user/'.$user->id)}}" method="POST" class="form-horizontal">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->editFieldHTML('uid','用户名',$user) !!}
			{!! $field->editFieldHTML('auth','权限',$user) !!}
		</div>
		<div class="row">
			{!! $field->editFieldHTML('password','设置密码') !!}	

		</div>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="" class="col-sm-2 col-sm-offset-1">确认密码</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" name="password_confirmation" required>
					</div>				
				</div>
			</div>
		</div>
		
	</form>


</div>
@endsection