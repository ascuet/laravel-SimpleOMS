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
				$('#selecttableModal .modal-content').empty();
				return '/'+self.opt.table+'/selecttable';
			},
			param:function(){
				var self = this;
				if(typeof(self.opt.filter)=='undefined')return;
				var arrFilter = self.opt.filter.split(',');
				var arrParam = {};
				for (var i = arrFilter.length - 1; i >= 0; i--) {
					var value = $('[name="'+arrFilter[i]+'"]').val();
					if(typeof(value)!='undefined'){
						arrParam[arrFilter[i]] = value;					
					}
				};
				console.log($.param(arrParam));
				var str = $.param(arrParam);
				return str;
			},
			callback:function(data){
				var self = this;
				console.log(data);
				$('#selecttableModal .modal-content').html(data);
				if(self.opt.field=='row'){
					$('#selecttableModal table tbody tr').click(function(){
						var $this = $(this);
						Action.call('POST',$this.data('action')+'/'+self.opt.orderid,'product_id='+$this.find('[name="id"]').val(),function(data){
							$(self.opt.targettable).find('table').empty().html(data);
							$('#selecttableModal').modal('hide');
						},function(data){
							$('#selecttableModal').modal('hide');
							$(self.opt.targettable).before('<div class="alert alert-info" role="alert">'+data.responseText+'...</div>');
							setTimeout(function(){
								$(self.opt.targettable).prev().remove();
							},3000);
						});
					});
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

		},
		unbindProduct:{
			type:'click',
			isAjax:'POST',
			opt:'',
			path:function(){
				var self = this;
				return self.opt.action;
			},
			param:function(){
				var self = this;
				return 'product_id='+self.opt.productid;
			},
			callback:function(data){
				var self = this;
				$(self.opt.targettable).find('table').empty().html(data);
			},
			errorCallback:function(data){
				$(self.opt.targettable).before('<div class="alert alert-info" role="alert">'+data.responseText+'...</div>');
				setTimeout(function(){
					$(self.opt.targettable).prev().remove();
				},3000);
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