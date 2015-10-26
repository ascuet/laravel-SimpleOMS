<thead>
	<tr>
		<th>#</th>
		{!!$field->tableHead('pid','设备号')!!}
		{!!$field->tableHead('belongsToSupply_name','库存名')!!}
		{!!$field->tableHead('country','国家')!!}
		{!!$field->tableHead('traffic','流量')!!}
		{!!$field->tableHead('pstatus','设备状态')!!}
	</tr>
</thead>
<tbody>
	@foreach($data as $key=>$value)
		<tr onclick="location.href='{{url('product/'.$value->id.'/edit')}}'" style="cursor:pointer">
			<th onclick="event.stopPropagation()"><input type="checkbox" name="id[]" value="{{$value->id}}"></th>
			{!!$field->tableCell('pid',$value)!!}
			{!!$field->tableCell('belongsToSupply_name',$value)!!}
			{!!$field->tableCell('country',$value)!!}
			{!!$field->tableCell('traffic',$value)!!}
			{!!$field->tableCell('pstatus',$value)!!}
		</tr>
	@endforeach
</tbody>
