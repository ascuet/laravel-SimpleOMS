@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>设备{{$product->pid}} <small>{{$field->statusName($product->pstatus)}}</small></h1>
	
	<form action="{{url('product/'.$product->id)}}" id="form" method="POST" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_method" value="PUT">
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
	<div class="modal fade" id="selecttableModal" tabindex="-1" role="dialog" aria-labelledby="Selecttable">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      
	    </div>
	  </div>
	</div>

</div>
@endsection