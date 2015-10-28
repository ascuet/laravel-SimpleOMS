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
			<button type="submit" form="destroy" class="btn navbar-btn btn-danger">批量删除</button>
			@endif
			@if (in_array('import',$actions))
				@if($class==='order')
				<a href="{{url('order/import')}}" class="btn navbar-btn btn-warning">导入</a>
				@endif
			@endif

			@if(in_array('submit',$actions))
				<button type="submit" form="form" class="btn navbar-btn btn-primary">提交</button>
			@endif
			@if(in_array('entryProduct',$actions)&&isset($product->pid))
				<button type="submit" form="form" formmethod="POST" formaction="{{url('product/entry/'.$currentProduct->id)}}" class="btn navbar-btn btn-primary">入库</button>
			@endif
			@if(in_array('backpage',$actions))
				<a href="{{URL::previous()}}" class="btn navbar-btn btn-default">返回</a>
			@endif
			@if(in_array('cancel',$actions)&&$order->status==1)
				<button type="button" class="btn btn-danger"  data-backdrop="static" data-toggle="modal" data-event="confirm" data-action="{{url('order/cancel/'.$order->id)}}" data-description="取消订单" data-target="#confirmModal">取消</button>
			@endif
			@if(in_array('backward',$actions)&&$order->status==2)
				<button type="button" class="btn btn-danger"  data-backdrop="static" data-toggle="modal" data-event="confirm" data-action="{{url('order/backward/'.$order->id)}}" data-description="回退订单并入库所有设备" data-target="#confirmModal">回退</button>
			@endif
			@if(in_array('orderReady',$actions)&&$order->status==0)
				<button type="button" class="btn btn-success"  data-backdrop="static" data-toggle="modal" data-event="confirm" data-action="{{url('order/ready/'.$order->id)}}" data-description="将订单转入待发货" data-target="#confirmModal">准备发货</button>
			@endif
			@if(in_array('sendOrder',$actions)&&$order->status==1)
				<button type="button" class="btn btn-success"  data-backdrop="static" data-toggle="modal" data-event="confirm" data-action="{{url('order/send?id='.$order->id)}}" data-description="订单发货" data-target="#confirmModal">发货</button>
			@endif
			@if(in_array('finishOrder',$actions)&&$order->status==2)
				<button type="button" class="btn btn-success"  data-backdrop="static" data-toggle="modal" data-event="confirm" data-action="{{url('order/finish/'.$order->id)}}" data-description="完成订单并将所有设备入库" data-target="#confirmModal">完成</button>
			@endif
    	</div>
  	</div>
  	<div class="modal fade" id="confirmModal"   tabindex="-1" role="dialog" aria-labelledby="Confirm">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	    	<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="confirmModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="form-group">
						<label class=" col-sm-2 col-sm-offset-1" for="reasons">操作意见</label>
						<div class="col-sm-9">
							<textarea class="form-control" name="reasons" form="form" rows="3"></textarea>
							<p class="help-block">操作意见可选填,会显示在操作记录中</p>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button type="submit" form="form" formmethod="POST" class="btn btn-primary">提交</button>
			</div>
	    </div>
	  </div>
	</div>
</nav>