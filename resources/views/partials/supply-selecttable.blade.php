<table class="table table-hover table-condensed">
	<thead>
		<tr>

			<th>#</th>
			{!!$field->tableHead('name','仓库名')!!}
			{!!$field->tableHead('slocation','所在地')!!}
			{!!$field->tableHead('is_self','所属')!!}
			{!!$field->tableHead('supply','供应商')!!}
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key=>$value)
			<tr onclick="event.stopPropagation()" style="cursor:pointer">
				<th><input type="checkbox" name="id" value="{{$value->id}}"></th>
				{!!$field->tableCell('name',$value)!!}
				{!!$field->tableCell('slocation',$value)!!}
				{!!$field->tableCell('is_self',$value)!!}
				{!!$field->tableCell('supply',$value)!!}
			</tr>
		@endforeach
	</tbody>
</table>
	