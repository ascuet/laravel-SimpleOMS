<div class="row">
	{!!$field->selectFieldHTML('uid','用户名',is_null(old('uid'))?'':old('uid'))!!}
	{!!$field->selectFieldHTML('auth','权限',is_null(old('auth'))?'':old('auth'))!!}

</div>