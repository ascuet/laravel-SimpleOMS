<thead>
	<tr>
		{!!$field->tableHead('uid','用户名')!!}
		{!!$field->tableHead('auth','权限')!!}
	</tr>
</thead>
<tbody>
	@foreach($data as $key=>$value)
		<tr onclick="location.herf='{{url('user').'/'.$key}}'">
			<th><input type="checkbox" name="id" value="{{$key}}"></th>
			{!!$field->tableCell('uid',$value)!!}
			{!!$field->tableCell('auth',$value)!!}
		</tr>
	@endforeach
</tbody>
