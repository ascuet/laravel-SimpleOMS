@if(current($data))
	<span onclick="Component.modules.toggleStar(event)" onmouseover="Component.modules.redStar(event)" onmouseout="Component.modules.transStar(event)" class="glyphicon glyphicon-star red-star" aria-hidden="true"></span><input type="hidden" value="{{current($data)}}" name="{{key($data)}}">
	

@else
	<span onclick="Component.modules.toggleStar(event)" onmouseover="Component.modules.redStar(event)" onmouseout="Component.modules.transStar(event)" class="glyphicon glyphicon-star" aria-hidden="true"></span><input type="hidden" value="{{current($data)}}" name="{{key($data)}}">

@endif