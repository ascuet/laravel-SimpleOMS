<ul class="nav navbar-nav">
	<li><a href="{{url('/order/import')}}">导入订单</a></li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">订单处理 <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ url('/order?status=0') }}">待处理</a></li>
			<li><a href="{{ url('/order?status=1') }}">待发货</a></li>
			<li><a href="{{ url('/order?status=2') }}">已发货</a></li>
			<li><a href="{{ url('/order?status=3') }}">已完成</a></li>
			<li><a href="{{ url('/order') }}">综合查询</a></li>
		</ul>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">设备管理 <span class="caret"></span></a>
		<ul class="dropdown-menu" role="menu">
			<li><a href="{{ url('/product/entry') }}">入库</a></li>
			<li><a href="{{ url('/product') }}">综合查询</a></li>
		</ul>
	</li>
	<li><a href="{{ url('/supply') }}">仓库</a></li>
	<li><a href="{{ url('/user') }}">用户</a></li>
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