@extends('base')
@section('content')
	
<div class="container">
		@include('partials.info')

	<h1>用户 {{$user->uid}}</h1>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" ><a href="{{url('user/'.$user->id.'/edit')}}">详细信息</a></li>
		<li role="presentation" class="active"><a href="#userlogs" aria-controls="userlogs" role="tab" data-toggle="tab">用户行为</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="userlogs" role="tabpanel">
			@include('partials.userlogs',['obj'=>$user])

		</div>

	</div>
</div>
@endsection