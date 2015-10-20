@extends('base')
@section('content')
	
<div class="container">
	<h1>设备{{$product->pid}} <small>{{$field->statusName($product->pstatus)}}</small></h1>
	
	<form action="{{url('product/'.$product->id)}}" method="POST" class="form-horizontal">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->editFieldHTML('pid','设备号',$product) !!}
			{!! $field->editFieldHTML('house','库存名',$product) !!}
		</div>
		<div class="row">
			{!! $field->editFieldHTML('pstatus','设备状态',$product) !!}
			{!! $field->editFieldHTML('traffic','当前流量',$product) !!}
		</div>
		<div class="row">
			{!! $field->editFieldHTML('memo','备注',$product) !!}			
		</div>

		
	</form>


</div>
@endsection