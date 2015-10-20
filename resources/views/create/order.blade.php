@extends('base')
@section('content')
	
<div class="container">
	<h1>创建订单</h1>
	
	<form action="{{url('order')}}" method="POST" class="form-horizontal">
		<h3>基本信息</h3>
		<div class="row">
			{!! $field->addFieldHTML('oid','订单号') !!}
			{!! $field->addFieldHTML('order_date','订单时间') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('sum','金额') !!}
			{!! $field->addFieldHTML('source','来源') !!}
		</div>
		<hr>
		<h3>客户信息</h3>
		<div class="row">
			{!! $field->addFieldHTML('gid','淘宝ID') !!}
			{!! $field->addFieldHTML('gname','客户姓名') !!}
			{!! $field->addFieldHTML('gmobile','客户电话') !!}

		</div>
		<div class="row">
			{!! $field->addFieldHTML('address','地址') !!}
			{!! $field->addFieldHTML('message','买家留言') !!}

		</div>
		<hr>
		<h3>行程</h3>
		<div class="row">
			{!! $field->addFieldHTML('go_date','出国日期') !!}
			{!! $field->addFieldHTML('back_date','回国日期') !!}

		</div>
		<div class="row">	
			{!! $field->addFieldHTML('days','天数') !!}
			{!! $field->addFieldHTML('country','国家') !!}	
			
		</div>
		<hr>
		<h3>设备</h3>
		<div class="row">
			{!! $field->addFieldHTML('amount','数量') !!}
			{!! $field->addFieldHTML('belongsToSupply_supply','供应商') !!}
			{!! $field->addFieldHTML('house','库存名') !!}
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
			{!! $field->addFieldHTML('send_date','发货日期') !!}
			{!! $field->addFieldHTML('is_deliver','发货方式') !!}

		</div>
		<hr>
		<div class="row">
			{!! $field->addFieldHTML('memo','客服备注') !!}
		</div>
	</form>


</div>
@endsection