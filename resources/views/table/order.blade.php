<thead>
	<tr>
		<th><input type="checkbox" onclick="Component.modules.select_all(event)"></th>
		{!!$field->tableHead('is_important','重要')!!}
		{!!$field->tableHead('gid','淘宝ID')!!}
		{!!$field->tableHead('gname','客户姓名')!!}
		{!!$field->tableHead('order_date','订单时间')!!}
		{!!$field->tableHead('country','国家')!!}
		{!!$field->tableHead('amount','数量')!!}
		{!!$field->tableHead('sum','金额')!!}
		{!!$field->tableHead('go_date','出国日期')!!}
		{!!$field->tableHead('back_date','回国日期')!!}
		{!!$field->tableHead('days','天数')!!}
		{!!$field->tableHead('send_date','发货日期')!!}
		{!!$field->tableHead('belongsToSupply_supply','供应商')!!}
		{!!$field->tableHead('belongsToSupply_name','库存名')!!}
		{!!$field->tableHead('is_deliver','发货方式')!!}
		{!!$field->tableHead('source','来源')!!}
		{!!$field->tableHead('status','订单状态')!!}
		{!!$field->tableHead('modified_at','操作时间')!!}
	</tr>
</thead>
<tbody>
	@foreach($data as $key=>$value)
		<tr onclick="Component.modules.rowAction(event)" data-location = "{{url('order/'.$value->id.'/edit')}}" style="cursor:pointer">
			<th onclick="event.stopPropagation()" ><input type="checkbox" name="id[]" value="{{$value->id}}"></th>			
			{!!$field->tableCell('is_important',$value)!!}
			{!!$field->tableCell('gid',$value)!!}
			{!!$field->tableCell('gname',$value)!!}
			{!!$field->tableCell('order_date',$value)!!}
			{!!$field->tableCell('country',$value)!!}
			{!!$field->tableCell('amount',$value)!!}
			{!!$field->tableCell('sum',$value)!!}
			{!!$field->tableCell('go_date',$value)!!}
			{!!$field->tableCell('back_date',$value)!!}
			{!!$field->tableCell('days',$value)!!}
			{!!$field->tableCell('send_date',$value)!!}
			{!!$field->tableCell('belongsToSupply_supply',$value)!!}
			{!!$field->tableCell('belongsToSupply_name',$value)!!}
			{!!$field->tableCell('is_deliver',$value)!!}
			{!!$field->tableCell('source',$value)!!}
			{!!$field->tableCell('status',$value)!!}
			{!!$field->tableCell('modified_at',$value)!!}
		</tr>	
	@endforeach
</tbody>
