(function(global, init){
    (function(){
        //
        if(typeof $ !== "undefined"){
            init(global);
        }
    })();
}(typeof window !== "undefined" ? window : this, function(root){
	root.$gary={};
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
	});
    var Action = {
        call:function(method,path,param,callback,errorcallback){

            $.ajax({
                type:method,
                url:path,
                data:param,
                success:function(data){
                    callback(data);
                },
                error:function(data){                     
                    errorcallback(data);

                }

            });
        }
    };
    root.$gary.callAction = Action; 
    //
    // scrolly
    jQuery.fn.n33_scrolly = function(offset) {

        jQuery(this).click(function(e) {
            var h = jQuery(this).attr('href'), target;

            if (h.charAt(0) == '#' && h.length > 1 && (target = jQuery(h)).length > 0)
            {
                var pos = offset? Math.max(target.offset().top, 0)+offset:Math.max(target.offset().top, 0);
                e.preventDefault();
                jQuery('body,html').animate({ scrollTop: pos }, 'slow', 'swing');
            }
        });
    };
    root.$gary.event = Event;

    //input event

    if('onpropertychange' in document){
        var rInput = /^INPUT|TEXTAREA$/,
        isInput=function(ele){
            return rInput.text(ele.nodeName);
        }

        $.event.special.input={
            setup:function(){
                var $ele = this;
                if(!isInput($ele))return false;
                $.data($ele,'@oldValue',$ele.value);
                $.event.add($ele,'propertychange',function(event){
                    if($.data(this,'@oldValue')!==this.value){
                        $.event.trigger('input',null,this);
                    };
                    $.data(this,'@oldValue',this.value);
                });
            },
            teardown:function(){
                var $ele = this;
                if(!isInput($ele))return false;
                $.event.remove($ele,'propertychange');
                $.removeData($ele,'@oldValue');
            }
        }
    }

    $.fn.input=function(callback){
        return this.bind('input',callback);
    }

    String.prototype.charLeftAll = function (bchar,alength){
         var xchar = ''+this;
         for(var i=0;i<alength;i++){
             if(xchar.length==alength)
            break; 
             xchar = bchar+xchar;
              
           }
          return(xchar);
     } 

}));