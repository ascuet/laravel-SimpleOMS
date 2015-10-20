<div class="row">

{!!$field->selectFieldHTML('oid','订单号',isset($oid)&&$oid)!!}
{!!$field->selectFieldHTML('gid','淘宝ID',isset($gid)&&$gid)!!}

	
</div>
<div class="row">
{!!$field->selectFieldHTML('gname','客户姓名',isset($gname)&&$gname)!!}
{!!$field->selectFieldHTML('gmobile','客户电话',isset($gmobile)&&$gmobile)!!}


	
</div>
<div class="row">
{!!$field->selectFieldHTML('country','国家',isset($country)&&$country)!!}
</div>
<div class="row">
{!!$field->selectFieldHTML('order_date','订单时间',isset($order_date)&&$order_date);!!}
{!!$field->selectFieldHTML('go_date','出国日期',isset($go_date)&&$go_date)!!}
	
</div>
<div class="row">
{!!$field->selectFieldHTML('back_date','回国日期',isset($back_date)&&$back_date)!!}
{!!$field->selectFieldHTML('send_date','发货日期',isset($send_date)&&$send_date)!!}
</div>
<div class="row">
{!!$field->selectFieldHTML('belongsToSupply_supply','供应商',isset($belongsToSupply_supply)&&$belongsToSupply_supply)!!}
{!!$field->selectFieldHTML('belongsToSupply_name','库存名',isset($belongsToSupply_name)&&$belongsToSupply_name)!!}
	
</div>
<div class="row">
{!!$field->selectFieldHTML('is_deliver','发货方式',isset($is_deliver)&&$is_deliver)!!}
{!!$field->selectFieldHTML('source','来源',isset($source)&&$source)!!}
	
</div>
<div class="row">
{!!$field->selectFieldHTML('status','订单状态',isset($status)&&$status)!!}
{!!$field->selectFieldHTML('modified_at','操作时间',isset($modified_at)&&$modified_at)!!}	
</div>
