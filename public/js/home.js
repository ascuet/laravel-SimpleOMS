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
					else{

						if((arrFilter[i].split('=')).length>1){
							arrParam[arrFilter[i].split('=')[0]]=arrFilter[i].split('=')[1];
						}
					}
				};
				console.log($.param(arrParam));
				var str = $.param(arrParam);
				return str;
			},
			callback:function(data){
				var self=this;
				Component.modules.selecttable_callback(data,self.opt);
				
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
				$('input[name="days"]').val((date2.getTime()-date1.getTime())/day+1);
				$('[data-event="days_before"]').trigger('change');
			}
		},
		days_before:{
			type:'change',
			isAjax:false,
			opt:'',
			handler:function($this){
				var day = 24*60*60*1000;
				$('input[name="send_date"]').removeClass('datepicker');
				if($('input[name="send_date"]').val()==""){
					if($('input[name="is_deliver"]:checked').val()==0){
						$('input[name="days_before"]').val(1);
					}
					else{
						$('input[name="days_before"]').val(4);						
					}

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
		change_deliver:{
			type:'change',
			isAjax:false,
			opt:'',
			handler:function($this){
				if($this.val()==0){
					$('input[name="days_before"]').val(1).trigger('change');
				}else if($this.val()==1){
					$('input[name="days_before"]').val(4).trigger('change');
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
		multi_send:{
			type:'click',
			isAjax:false,
			opt:'',
			handler:function($this){
				$('#'+$this.attr('form')).find('input[name="_method"]').remove();
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

Component.modules={
	selecttable_filter:function(e){
		console.log(e);
		var $this = $(e.target);
		var	path=$this.data('action');
		if(typeof($this.data('filter'))=='undefined')return;
		var arrFilter = $this.data('filter').split(',');
		var arrParam = {};
		for (var i = arrFilter.length - 1; i >= 0; i--) {
			var value = $('#selecttableModal').find('[name="'+arrFilter[i]+'"]').val();
			if(typeof(value)!='undefined'){
				arrParam[arrFilter[i]] = value;					
			}
		};
		var str = $.param(arrParam);
		Action.call('GET',path,str,function(data){
			Component.modules.selecttable_callback(data,$this.data());

		},function(data){

		});

	},
	unbindProduct:function(e){
		var $this=$(e.target);
		Action.call('POST',$this.data('action'),'product_id='+$this.data('productid'),function(data){
			$($this.data('targettable')).find('table').empty().html(data);
		},function(data){
			$($this.data('targettable')).before('<div class="alert alert-info" role="alert">'+data.responseText+'...</div>');
			setTimeout(function(){
					$($this.data('targettable')).prev().remove();
				},3000);
		});
	},
	selecttable_callback:function(data,opt){
		$('#selecttableModal .modal-content').html(data);
		if(opt.field=='row'){
			$('#selecttableModal table tbody tr').click(function(){
				var $this = $(this);
				Action.call('POST',$this.data('action')+'/'+$('input[name="obj_id"]').val(),'product_id='+$this.find('[name="id"]').val(),function(data){
					$(opt.targettable).find('table').empty().html(data);
					$('#selecttableModal').modal('hide');
				},function(data){
					$('#selecttableModal').modal('hide');
					$(opt.targettable).before('<div class="alert alert-info" role="alert">'+data.responseText+'...</div>');
					setTimeout(function(){
						$(opt.targettable).prev().remove();
					},3000);
				});
			});
		}
		else{
			$('#selecttableModal table tbody tr').click(function(){
				var $this = $(this);
				$('input:hidden[name="'+opt.name+'"]').val($this.find('[name="id"]').val());
				$('input:hidden[name="'+opt.name+'"]').siblings('input[type="text"]').val($this.find('.td-'+opt.field).text());
				$('#selecttableModal').modal('hide');
			});
		}
	},
	toggleStar:function(e){
		e.stopPropagation();
		var $this=$(e.target);
		Action.call('POST','/order/toggle-star/'+$this.parents('tr').find('[name="id[]"]').val(),'',function(data){
			$this.parent().empty().append(data);
		},function(data){

		});
	},
	redStar:function(e){
		var $this=$(e.target);
		$this.addClass('red-star');
	},
	transStar:function(e){
		var $this=$(e.target);
		if($this.siblings('input[name="is_important"]').val()!=1){
			$this.removeClass('red-star');			
		}
	},
	select_all:function(e){
		var $this=$(e.target);
		$tbody = $this.parents('table').find('tbody');
		$tbody.find('input[type="checkbox"]').prop('checked',$this.prop('checked'));
	}

};
$(document).ready(function(){
	Component.events.init();
	$('[data-event="calculate_days"]').trigger('change');
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