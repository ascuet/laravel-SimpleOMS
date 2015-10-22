<thead>
	<tr>
		<th>#</th>
		{!!$field->tableHead('uid','用户名')!!}
		{!!$field->tableHead('auth','权限')!!}
	</tr>
</thead>
<tbody>
	@foreach($data as $key=>$value)
		<tr onclick="location.href='{{url('user/'.$value->id.'/edit')}}'" style="cursor:pointer">
			<th onclick="event.stopPropagation()"><input type="checkbox" name="id[]" value="{{$value->id}}"></th>
			{!!$field->tableCell('uid',$value)!!}
			{!!$field->tableCell('auth',$value)!!}
		</tr>
	@endforeach
</tbody>
