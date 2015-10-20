@extends('base')
@section('content')
	
<div class="container">
	<h1>仓库 {{$supply->name}}</h1>
	
	<form action="{{url('supply/'.$supply->id)}}" method="POST" class="form-horizontal">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->editFieldHTML('name','仓库名',$supply) !!}
			{!! $field->editFieldHTML('is_self','库存所属',$supply) !!}
		</div>
		<div class="row">
			{!! $field->editFieldHTML('supply','供应商',$supply) !!}
			{!! $field->editFieldHTML('slocation','所在地',$supply) !!}
		</div>
		<div class="row">
			{!! $field->editFieldHTML('saddress','地址',$supply) !!}			
		</div>

		
	</form>


</div>
@endsection