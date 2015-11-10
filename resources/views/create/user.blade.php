@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h2>新增用户</h2>
	
	<form action="{{url('user')}}" id="form" method="POST" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->addFieldHTML('uid','用户名') !!}
			{!! $field->addFieldHTML('auth','权限') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('password','设置密码') !!}	

		</div>
		<div class="row">
			<div class="form-group col-md-6 form-group-sm">
				<label for="" class="col-sm-2 col-sm-offset-1">确认密码</label>
					<div class="col-sm-6">
						<input class="form-control" type="password" name="password_confirmation" required>
					</div>				
				</div>
			</div>
		</div>
		
	</form>
	<div class="modal fade" id="selecttableModal" tabindex="-1" role="dialog" aria-labelledby="Selecttable">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      
	    </div>
	  </div>
	</div>

</div>
@endsection