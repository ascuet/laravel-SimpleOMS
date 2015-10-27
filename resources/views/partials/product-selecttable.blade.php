<table class="table table-hover table-condensed">
	<thead>
		<tr>

			<th>#</th>
			{!!$field->tableHead('pid','设备号')!!}
			{!!$field->tableHead('country','国家')!!}
		</tr>
	</thead>
	<tbody>
		@foreach($data as $key=>$value)
			<tr data-action="{{url('order/combine-product/')}}"  style="cursor:pointer">
				<th onclick="event.stopPropagation()"><input type="checkbox" name="id" value="{{$value->id}}"></th>
				{!!$field->tableCell('pid',$value)!!}
				{!!$field->tableCell('country',$value)!!}
			</tr>
		@endforeach
	</tbody>
</table>