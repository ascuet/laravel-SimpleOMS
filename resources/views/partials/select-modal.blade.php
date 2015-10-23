<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="myModalLabel">{{$title}}</h4>
</div>
<div class="modal-body">
	@include('partials.'.$class.'-selecttable')
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
	@if($multi)
	<button type="button" class="btn btn-primary">选择</button>
	@endif
</div>