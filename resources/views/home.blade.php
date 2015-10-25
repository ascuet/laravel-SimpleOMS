@extends('base')
@section('content')
	
<div class="container">
	@include('partials.info')
	<div class="panel panel-default">
		<div class="panel-heading"><h4>{{$title}} <small>{{$stitle}}</small></h4></div>

		<div class="panel-body">
			<form action="{{url($class)}}" method="GET" class="form-horizontal">
				@include('select.'.$class)				
			<div class="row text-center">
				<button class="btn btn-primary" type="submit">查询</button>
				<button class="btn btn-default" type="reset">清空</button>
				@if($class==='order')
				<button class="btn btn-success" type="submit" formaction="{{url('order/export')}}">导出</button>			
				@endif
			</div>
				</form>
		</div>
	</div>
	<div class="table-responsive">
		<form action="{{url($class.'/0')}}" id="destroy" method="POST">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_method" value="DELETE">
			<table class="table table-striped table-bordered table-condensed table-hover">
				<caption>共查询到 {{$data->total()}} 个结果</caption>
				<?php echo $data->render();?>
				@include('table.'.$class)
			</table>
		</form>
	</div>


</div>
@endsection
