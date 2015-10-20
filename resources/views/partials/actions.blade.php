<nav class="navbar navbar-default navbar-fixed-bottom">
	<div class="navbar-header">
		<p class="navbar-brand">操作栏</p>
	</div>
  	<div class="container">
    	<div class="text-center">
    		@if(in_array('create',$actions))
			<a href="{{url($class.'/create')}}" class="btn navbar-btn btn-primary">新增</a>
			@endif
			@if(in_array('delete',$actions))
			<button type="button" class="btn navbar-btn btn-danger">批量删除</button>
			@endif
			@if (in_array('import',$actions))
				@if($class==='order')
				<a href="{{url('order/import')}}" class="btn navbar-btn btn-warning">导入</a>
				@endif
			@endif

			@if(in_array('submit',$actions))
				<button type="submit" class="btn navbar-btn btn-primary">提交</button>
			@endif

			@if(in_array('backpage',$actions))
				<a href="{{URL::previous()}}" class="btn navbar-btn btn-default">返回</a>
			@endif
    	</div>
  	</div>
</nav>