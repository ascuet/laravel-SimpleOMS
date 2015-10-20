@extends('base')
@section('content')
	
<div class="container">
	<h1>新增设备</h1>
	
	<form action="{{url('product')}}" method="POST" class="form-horizontal">
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