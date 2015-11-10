@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h2>创建订单</h2>
	
	<form action="{{url('order')}}" id="form" method="POST" class="form-horizontal">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<br>
		<div class="row">
			{!! $field->addFieldHTML('oid','订单号') !!}
			{!! $field->addFieldHTML('order_date','订单时间') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('sum','金额') !!}
			{!! $field->addFieldHTML('source','来源') !!}
		</div>
		<hr>
		<div class="row">
			{!! $field->addFieldHTML('gid','淘宝ID') !!}
			{!! $field->addFieldHTML('gname','客户姓名') !!}

		</div>
		<div class="row">
			{!! $field->addFieldHTML('gmobile','客户电话') !!}			
			{!! $field->addFieldHTML('address','地址') !!}
		</div>
		<div class="row">
			{!! $field->addFieldHTML('message','买家留言') !!}
			{!! $field->addFieldHTML('memo','客服备注') !!}
		</div>
		<hr>
		<div class="row">
			{!! $field->addFieldHTML('go_date','出国日期') !!}
			{!! $field->addFieldHTML('back_date','回国日期') !!}

		</div>
		<div class="row">	
			{!! $field->addFieldHTML('days','天数') !!}
			{!! $field->addFieldHTML('country','国家') !!}	
			
		</div>
		<hr>
		<div class="row">
			<div class="form-group col-md-6">
				<label for="" class="col-sm-2 col-sm-offset-1">发货策略</label>
				<div class="col-sm-4">
					<div class="input-group input-group-sm">
					  <span class="input-group-addon">提前</span>
						<input class="form-control" data-event="days_before" type="number" step="1" name="days_before">
					  <span class="input-group-addon">天发货</span>
					</div>				
				</div>
				</div>
			{!! $field->addFieldHTML('send_date','发货日期') !!}
			{!! $field->addFieldHTML('is_deliver','发货方式') !!}

		</div>
		<div class="row">
			{!! $field->addFieldHTML('amount','数量') !!}
			{!! $field->addFieldHTML('belongsToSupply_supply','供应商') !!}
			{!! $field->addFieldHTML('house','库存名') !!}
		</div>
		
		<hr>

	</form>

	<div class="modal fade" id="selecttableModal" tabindex="-1" role="dialog" aria-labelledby="Selecttable">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      
	    </div>
	  </div>
	</div>
</div>
@endsection