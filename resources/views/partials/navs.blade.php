<ul class="nav navbar-nav">
	@if(in_array(Auth::user()->auth,[0,1,2]))
	<li><a href="{{url('/order/import')}}">导入订单</a></li>
	@endif
	<li class="dropdown btn-group">
		<a class="btn btn-link" href="{{url('order')}}">订单处理</a>
		<a class="btn btn-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ url('/order?status=0') }}">待处理</a></li>
			<li><a href="{{ url('/order?status=1') }}">待发货</a></li>
			<li><a href="{{ url('/order?status=2') }}">已发货</a></li>
			<li><a href="{{ url('/order?status=3') }}">已完成</a></li>
		</ul>
	</li>
	<li class="dropdown btn-group">
		<a class="btn btn-link" href="{{url('product')}}">设备管理</a>
		@if(in_array(Auth::user()->auth,[0,3]))
		<a class="btn btn-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="caret"></span></a>
		@endif
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ url('/product/entry') }}">入库</a></li>
		</ul>
	</li>
	<li><a href="{{ url('/supply') }}">仓库</a></li>
	@if(Auth::user()->auth==0)
	<li><a href="{{ url('/user') }}">用户</a></li>
	@endif
</ul>

<ul class="nav navbar-nav navbar-right">
	@if (Auth::guest())
		<li><a href="{{ url('/auth/login') }}">登陆</a></li>
	@else
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->roleName().' - '.Auth::user()->uid }} <span class="caret"></span></a>
			<ul class="dropdown-menu" role="menu">
				<li><a href="{{ url('/auth/logout') }}">注销</a></li>
			</ul>
		</li>
	@endif
</ul>