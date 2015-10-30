@if($data)
	<span onclick="Component.modules.toggleStar(event)" onmouseover="Component.modules.redStar(event)" onmouseout="Component.modules.transStar(event)" class="glyphicon glyphicon-star red-star" aria-hidden="true"></span>
	

@else
	<span onclick="Component.modules.toggleStar(event)" onmouseover="Component.modules.redStar(event)" onmouseout="Component.modules.transStar(event)" class="glyphicon glyphicon-star" aria-hidden="true"></span>

@endif