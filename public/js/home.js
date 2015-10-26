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
				if(typeof(self.opt.filter)=='undefined')return;
				var arrFilter = self.opt.filter.split(',');
				var arrParam = [];
				for (var i = arrFilter.length - 1; i >= 0; i--) {
					var value = $('[name="'+arrFilter[i]+'"]').val();
					if(typeof(value)!='undefined'){
						arrParam[arrFilter[i]] = value;					
					}
				};
				return arrParam;
			},
			callback:function(data){
				var self = this;
				console.log(data);
				$('#selecttableModal .modal-content').empty().html(data);
				if(self.opt.field=='row'){

				}
				else{
					$('#selecttableModal table tbody tr').click(function(){
						var $this = $(this);
						$('input:hidden[name="'+self.opt.name+'"]').val($this.find('[name="id"]').val());
						$('input:hidden[name="'+self.opt.name+'"]').siblings('input[type="text"]').val($this.find('.td-'+self.opt.field).text());
						$('#selecttableModal').modal('hide');
					});
				}
				
			},
			errorCallback:function(data){

			}
		},
		calculate_days:{
			type:'change',
			isAjax:false,
			opt:'',
			handler:function($this){
				var date1 = new Date ($('input[name="go_date"]').val());
				var date2 = new Date ($('input[name="back_date"]').val());
				var day = 24*60*60*1000;
				$('input[name="days"]').val((date2.getTime()-date1.getTime())/day);
				$('[data-event="days_before"]').trigger('input');
			}
		},
		days_before:{
			type:'input',
			isAjax:false,
			opt:'',
			handler:function($this){
				var day = 24*60*60*1000;
				$('input[name="send_date"]').removeClass('datepicker');
				if($('input[name="send_date"]').val()==""){
					$('input[name="days_before"]').val(4);

				}else{
					if($('input[name="days_before"]').val()==''){
						var date1 = new Date ($('input[name="send_date"]').val());
						var date2 = new Date ($('input[name="go_date"]').val());
						$('input[name="days_before"]').val((date2.getTime()-date1.getTime())/day);

					}
				}
				var days_before = $('input[name="days_before"]').val();
				if(days_before>=0){
					var  date1 = new Date ($('input[name="go_date"]').val());
					var date2 = date1.getTime()-day*days_before;
					var send_date = new Date();
					send_date.setTime(date2);
					$('input[name="send_date"]').val(send_date.getFullYear()+'-'+String(send_date.getMonth()+1).charLeftAll(0,2)+'-'+String(send_date.getDate()).charLeftAll(0,2));

				}
			}
		},
		confirm:{
			type:'click',
			isAjax:false,
			opt:'',
			handler:function($this){
				$('#confirmModal').find('.modal-title').text($this.data('description'));
				$('#confirmModal').find('button[type="submit"]').attr('formaction',$this.data('action'));
				$('#confirmModal').find('button[type="submit"]').click(function(){
					$('#form').find('input[name="_method"]').remove();
				});
			}

		}

		

	},
	init:function(){
		var self = this;

		for(regEvent in self.register){
			var oCurrent = self.register[regEvent];

			$('[data-event="'+regEvent+'"]').on(oCurrent.type,function(e){
				var reg = self.register[$(this).data('event')];
					reg.opt=$(this).data();
				if(reg.isAjax){
					var sPath = reg.path();
					var sParam = reg.param();
					Action.call(reg.isAjax,sPath,sParam,function(data){
						reg.callback(data);
					},function(data){
						reg.errorCallback(data);
					});
				}else{
					reg.handler($(this));
				}
				
			});
		}
	}

};


$(document).ready(function(){
	Component.events.init();
	$('[data-event="calculate_days"]').trigger('change');
	$('[data-event="days_before"]').trigger('input');
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