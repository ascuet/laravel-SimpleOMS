<thead>
	<tr>
		<th>设备号</th>
		<th>国家</th>
		<th>库存名</th>
		<th>操作</th>
	</tr>
</thead>
<tbody>
	@foreach($products as $product)
	<tr >
		<td>{{$product->pid}}</td>
		<td>{{$product->country}}</td>
		<td>{{$product->belongsToSupply->name}}</td>
		<td><button type="button" class="btn btn-danger btn-sm" data-event="unbindProduct" data-productid="{{$product->id}}" data-targetTable="#products" data-action="{{url('order/unbind-product/'.$order->id)}}">移除</button></td>						
	</tr>
		@endforeach
</tbody>