@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>新增仓库</h1>
	
	<form action="{{url('supply')}}" id="form" method="POST" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
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
	<div class="modal fade" id="selecttableModal" tabindex="-1" role="dialog" aria-labelledby="Selecttable">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      
	    </div>
	  </div>
	</div>

</div>
@endsection