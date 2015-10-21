<div class="row">
	{!!$field->selectFieldHTML('name','库存名',is_null(old('name'))?'':old('name'))!!}
	{!!$field->selectFieldHTML('is_self','库存所属',is_null(old('is_self'))?'':old('is_self'))!!}

</div>
<div class="row">
	{!!$field->selectFieldHTML('supply','供应商',is_null(old('supply'))?'':old('supply'))!!}

</div>