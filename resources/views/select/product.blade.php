<div class="row">
	{!!$field->selectFieldHTML('pid','设备号',is_null(old('pid'))?'':old('pid'))!!}
	{!!$field->selectFieldHTML('country','国家',is_null(old('country'))?'':old('country'))!!}	

</div>
<div class="row">
	{!!$field->selectFieldHTML('house','库存名',is_null(old('house'))?'':old('house'))!!}

</div>
<div class="row">
	{!!$field->selectFieldHTML('pstatus','设备状态',is_null(old('pstatus'))?'':old('pstatus'))!!}	
</div>