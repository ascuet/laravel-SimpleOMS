@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>新增设备</h1>
	
	<form action="{{url('product')}}" id="form" method="POST" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->addFieldHTML('pid','设备号') !!}
			{!! $field->addFieldHTML('house','库存名') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('pstatus','设备状态') !!}
			{!! $field->addFieldHTML('traffic','当前流量') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('memo','备注') !!}			
		</div>

		
	</form>


</div>
@endsection