@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')
	<h1>订单{{$order->oid}} <small>{{$field->statusName($order->status)}}</small></h1>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab">详细信息</a></li>
		@if($order->status>0 && $order->belongsToSupply->is_self==1)
		<li role="presentation"><a href="#products" aria-controls="products" role="tab" data-toggle="tab">关联设备</a></li>
		@endif
		<li role="presentation"><a href="#logs" aria-controls="logs" role="tab" data-toggle="tab">操作记录</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="detail" role="tabpanel">
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

				</div>
				<div class="row">
					{!! $field->editFieldHTML('gmobile','客户电话',$order) !!}
					{!! $field->editFieldHTML('address','地址',$order) !!}

				</div>
				<div class="row">
					{!! $field->editFieldHTML('message','买家留言',$order) !!}
					{!! $field->editFieldHTML('memo','客服备注',$order) !!}
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
								<input class="form-control" type="number" data-event="days_before" step="1" min="1" name="days_before">
							  <span class="input-group-addon">天发货</span>
							</div>				
						</div>
						</div>
					{!! $field->editFieldHTML('send_date','发货日期',$order) !!}
					{!! $field->editFieldHTML('is_deliver','发货方式',$order) !!}

				</div>
				<hr>
				
				{!! $field->editFieldHTML('status','订单状态',$order) !!}
			</form>
		</div>
		@if($order->status>0 && $order->belongsToSupply->is_self==1)
		<div class="tab-pane" id="products" role="tabpanel">
			<?php $products = $order->products()->with('belongsToSupply')->get()?>
			<h3>关联设备<button type="button"  data-toggle="modal" data-table="product" data-field="row" data-filter="house,country" data-event="selecttable" data-target="#selecttableModal" class="btn btn-success" >添加</button></h3>
			<table class="table table-hover table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<th>设备号</th>
						<th>国家</th>
						<th>库存名</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					@foreach($products as $product)
					<tr id="{{$product->id}}">
						<td>{{$product->pid}}</td>
						<td>{{$product->country}}</td>
						<td>{{$product->belongsToSupply->name}}</td>
						<td><button type="button" class="btn btn-alert btn-sm">移除</button></td>						
					</tr>
						@endforeach
				</tbody>
			</table>
		</div>
		@endif
		<div class="tab-pane" id="logs" role="tabpanel">
			@include('partials.logs',['obj'=>$order])

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