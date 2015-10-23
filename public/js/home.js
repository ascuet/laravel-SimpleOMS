var Component={};
var Action = $gary.callAction;

Component.events={
	register:{
		selecttable:{
			selector:'[data-event="selecttable"]',
			type:'click',
			isAjax:'get',
			opt:'',
			path:function(){
				var self = this;
				return '/'+self.opt.table+'/selecttable';
			},
			param:function(){
				var self = this;
				var filter = $('[name="'+self.opt.filter+'"]').val();
				if(typeof(filter)!='undefined'){
					return self.opt.filter+'='+filter;					
				}
				else{
					return '';
				}
			},
			callback:function(data){
				var self = this;
				console.log(data);
				$('#selecttableModal .modal-content').empty().html(data);
				$('#selecttableModal table tbody tr').click(function(){
					var $this = $(this);
					$('input:hidden[name="'+self.opt.name+'"]').val($this.find('[name="id"]').val());
					$('input:hidden[name="'+self.opt.name+'"]').siblings('input[type="text"]').val($this.find('.td-'+self.opt.field).text());
					$('#selecttableModal').modal('hide');
				});
			},
			errorCallback:function(data){

			}


		}

	},
	init:function(){
		var self = this;

		for(regEvent in self.register){
			var oCurrent = self.register[regEvent];

			$('[data-event="'+regEvent+'"]').on(oCurrent.type,function(e){
				var reg = self.register[$(this).data('event')];
				if(reg.isAjax){
					reg.opt=$(this).data();
					var sPath = reg.path();
					var sParam = reg.param();
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