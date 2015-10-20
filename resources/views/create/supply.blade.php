@extends('base')
@section('content')
	
<div class="container">
	<h1>新增仓库</h1>
	
	<form action="{{url('supply')}}" method="POST" class="form-horizontal">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->addFieldHTML('name','仓库名') !!}
			{!! $field->addFieldHTML('is_self','库存所属') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('supply','供应商') !!}
			{!! $field->addFieldHTML('slocation','所在地') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('saddress','地址') !!}			
		</div>

		
	</form>


</div>
@endsection