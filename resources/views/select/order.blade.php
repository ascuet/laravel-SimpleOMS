<div class="row">

{!!$field->selectFieldHTML('oid','订单号',is_null(old('oid'))?'':old('oid'))!!}
{!!$field->selectFieldHTML('gid','淘宝ID',is_null(old('gid'))?'':old('gid'))!!}

	
</div>
<div class="row">
{!!$field->selectFieldHTML('gname','客户姓名',is_null(old('gname'))?'':old('gname'))!!}
{!!$field->selectFieldHTML('gmobile','客户电话',is_null(old('gmobile'))?'':old('gmobile'))!!}


	
</div>
<div class="row">
{!!$field->selectFieldHTML('country','国家',is_null(old('country'))?'':old('country'))!!}
</div>
<div class="row">
{!!$field->selectFieldHTML('order_date','订单时间',[old('order_date_start'),old('order_date_end')])!!}
{!!$field->selectFieldHTML('go_date','出国日期',[old('go_date_start'),old('go_date_end')])!!}
	
</div>
<div class="row">
{!!$field->selectFieldHTML('back_date','回国日期',[old('back_date_start'),old('back_date_end')])!!}
{!!$field->selectFieldHTML('send_date','发货日期',[old('send_date_start'),old('send_date_end')])!!}
</div>
<div class="row">
{!!$field->selectFieldHTML('belongsToSupply_supply','供应商',is_null(old('belongsToSupply_supply'))?'':old('belongsToSupply_supply'))!!}
{!!$field->selectFieldHTML('belongsToSupply_name','库存名',is_null(old('belongsToSupply_name'))?'':old('belongsToSupply_name'))!!}
	
</div>
<div class="row">
{!!$field->selectFieldHTML('is_deliver','发货方式',is_null(old('is_deliver'))?'':old('is_deliver'))!!}
{!!$field->selectFieldHTML('source','来源',is_null(old('source'))?'':old('source'))!!}
	
</div>
<div class="row">
{!!$field->selectFieldHTML('status','订单状态',is_null(old('status'))?'':old('status'))!!}
{!!$field->selectFieldHTML('modified_at','操作时间',[old('modified_at_start'),old('modified_at_end')])!!}	
</div>
