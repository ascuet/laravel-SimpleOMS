@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>用户 {{$user->uid}}</h1>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab">详细信息</a></li>
		<li role="presentation"><a href="#logs" aria-controls="logs" role="tab" data-toggle="tab">操作记录</a></li>
		<li role="presentation"><a href="#userlogs" aria-controls="userlogs" role="tab" data-toggle="tab">用户行为</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="detail" role="tabpanel">

			<form action="{{url('user/'.$user->id)}}" id="form" method="POST" class="form-horizontal">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_method" value="PUT">
				<h3>基本信息</h3>
				<div class="row">
					{!! $field->editFieldHTML('uid','用户名',$user) !!}
					{!! $field->editFieldHTML('auth','权限',$user) !!}
				</div>
				<div class="row">
					{!! $field->editFieldHTML('password','设置密码',$user) !!}	

				</div>
				<div class="row">
					<div class="form-group col-md-6 form-group-sm">
						<label for="" class="col-sm-2 col-sm-offset-1">确认密码</label>
							<div class="col-sm-6">
								<input class="form-control " type="password" name="password_confirmation" required>
							</div>				
						</div>
				</div>
				
			</form>
	</div>
		<div class="tab-pane" id="logs" role="tabpanel">
			@include('partials.logs',['obj'=>$user])

		</div>
		<div class="tab-pane" id="userlogs" role="tabpanel">
			@include('partials.userlogs',['obj'=>$user])

		</div>
	</div>
	<div class="modal fade" id="selecttableModal" tabindex="-1" role="dialog" aria-labelledby="Selecttable">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      
	    </div>
	  </div>
	</div>
</div>
@endsection