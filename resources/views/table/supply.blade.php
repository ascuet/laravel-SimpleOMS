<thead>
	<tr>
		{!!$field->tableHead('name','仓库名')!!}
		{!!$field->tableHead('slocation','所在地')!!}
		{!!$field->tableHead('is_self','所属')!!}
		{!!$field->tableHead('supply','供应商')!!}
	</tr>
</thead>
<tbody>
	@foreach($data as $key=>$value)
		<tr onclick="location.herf='{{url('supply').'/'.$key}}'">
			<th><input type="checkbox" name="id" value="{{$key}}"></th>
			{!!$field->tableCell('name',$value)!!}
			{!!$field->tableCell('slocation',$value)!!}
			{!!$field->tableCell('is_self',$value)!!}
			{!!$field->tableCell('supply',$value)!!}
		</tr>
	@endforeach
</tbody>
