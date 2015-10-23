@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>订单{{$order->oid}} <small>{{$field->statusName($order->status)}}</small></h1>
	
	<form action="{{url('order/'.$order->id)}}" id="form" method="POST" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" name="_method" value="PUT">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->editFieldHTML('oid','订单号',$order) !!}
			{!! $field->editFieldHTML('order_date','订单时间',$order) !!}
		</div>
		<div class="row">
			{!! $field->editFieldHTML('sum','金额',$order) !!}
			{!! $field->editFieldHTML('source','来源',$order) !!}
		</div>
		<hr>
		<h3>客户信息</h3>
		<div class="row">
			{!! $field->editFieldHTML('gid','淘宝ID',$order) !!}
			{!! $field->editFieldHTML('gname','客户姓名',$order) !!}
			{!! $field->editFieldHTML('gmobile','客户电话',$order) !!}

		</div>
		<div class="row">
			{!! $field->editFieldHTML('address','地址',$order) !!}
			{!! $field->editFieldHTML('message','买家留言',$order) !!}

		</div>
		<hr>
		<h3>行程</h3>
		<div class="row">
			{!! $field->editFieldHTML('go_date','出国日期',$order) !!}
			{!! $field->editFieldHTML('back_date','回国日期',$order) !!}

		</div>
		<div class="row">	
			{!! $field->editFieldHTML('days','天数',$order) !!}
			{!! $field->editFieldHTML('country','国家',$order) !!}	
			
		</div>
		<hr>
		<h3>设备</h3>
		<div class="row">
			{!! $field->editFieldHTML('amount','数量',$order) !!}
			{!! $field->editFieldHTML('belongsToSupply_supply','供应商',$order) !!}
			{!! $field->editFieldHTML('house','库存名',$order) !!}
		</div>
		<hr>
		<h3>发货信息</h3>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="" class="col-sm-2 col-sm-offset-1">发货策略</label>
				<div class="col-sm-4">
					<div class="input-group input-group-sm">
					  <span class="input-group-addon">提前</span>
						<input class="form-control" type="number" step="1" min="1" name="days_before">
					  <span class="input-group-addon">天发货</span>
					</div>				
				</div>
				</div>
			{!! $field->editFieldHTML('send_date','发货日期',$order) !!}
			{!! $field->editFieldHTML('is_deliver','发货方式',$order) !!}

		</div>
		<hr>
		<div class="row">
			{!! $field->editFieldHTML('memo','客服备注',$order) !!}
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