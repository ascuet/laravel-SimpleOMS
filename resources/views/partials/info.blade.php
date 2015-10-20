@if (count($errors) > 0)
	<div class="alert alert-danger">
		<strong>啊哦。。。出了点小问题 <br><br></strong>
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

@if (Session::has('success'))
	<div class="alert alert-success">
		<strong>{{ Session::get('success') }}</strong>
	</div>
	

@endif