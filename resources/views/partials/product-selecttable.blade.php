<div class="row">

	<div class="col-md-6 col-md-offset-3">
		<div class="input-group">
			<span class="input-group-addon">设备号</span>
			<input type="text" name="pid" value="{{old('pid')}}" class="form-control">
			<span class="input-group-btn">
				<button class="btn btn-default" onclick="Component.modules.selecttable_filter(event)" type="button" data-event="selecttable_filter" data-targettable="#products" data-filter="pid" data-field="row" data-action="{{url('product/selecttable?house='.old('house').'&country='.old('country'))}}">查找</button>
			</span>
		</div>
	</div>
	
</div>

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