<div class="row">
	{!!$field->selectFieldHTML('pid','设备号',isset($pid)&&$pid)!!}
	{!!$field->selectFieldHTML('belongsToSupply_name','库存名',isset($belongsToSupply_name)&&$belongsToSupply_name)!!}

</div>
<div class="row">
	{!!$field->selectFieldHTML('pstatus','设备状态',isset($pstatus)&&$pstatus)!!}	
</div>