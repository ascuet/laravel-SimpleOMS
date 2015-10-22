var Component={};
var Action = $gary.callAction;

Component.events={
	register:{
		page_type:{
			selector:'[data-event="page_type"]',
			type:'change',
			isAjax:'get',
			path:function(e){
				return '/admin/page/type';
			},
			param:function(e){
				var value=$(this.selector).filter(':checked').val();
				var pageId=$('input[name="page_id"]').val()!=''?'&id='+$('input[name="page_id"]').val():'';
				return 'page_type='+value+pageId;
			},
			callback:function(data){
				$('#page_content').empty().append(data);
			},
			errorCallback:function(data){

			}


		}

	},
	init:function(){
		var self = this;

		for(regEvent in self.register){
			var oCurrent = self.register[regEvent];

			$(oCurrent.selector).on(oCurrent.type,function(e){
				var reg = self.register[$(this).data('event')];
				if(reg.isAjax){
					var sPath = reg.path(e);
					var sParam = reg.param(e);
					Action.call(reg.isAjax,sPath,sParam,function(data){
						reg.callback(data);
					},function(data){
						reg.errorCallback(data);
					});
				}else{
					reg.handler();
				}
				
			});
		}
	}

};


$(document).ready(function(){
	Component.events.init();
	$('.datetimepicker').datetimepicker({
		language:'zh-CN',
		autoclose:1,
		todayBtn:1,
		todayHighlight:1,
		minView:1,
		});
	$('.datepicker').datetimepicker({
		language:'zh-CN',
		autoclose:1,
		todayBtn:1,
		todayHighlight:1,
		minView:2,
	});
});