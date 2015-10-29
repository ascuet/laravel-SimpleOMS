@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>仓库 {{$supply->name}}</h1><input type="hidden" name="obj_id" value="{{$supply->id}}">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab">详细信息</a></li>
		<li role="presentation"><a href="#logs" aria-controls="logs" role="tab" data-toggle="tab">操作记录</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="detail" role="tabpanel">
			<form action="{{url('supply/'.$supply->id)}}" id="form" method="POST" class="form-horizontal">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="_method" value="PUT">
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
		<div class="tab-pane" id="logs" role="tabpanel">
			@include('partials.logs',['obj'=>$supply])

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