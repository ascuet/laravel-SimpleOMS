@extends('base')
@section('css')
    @parent
    <link href="{{ asset('/css/jquery.fileupload.css') }}" rel="stylesheet">
@stop
@section('content')

<div class="container">
    @include('partials.info')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <span class="btn btn-success fileinput-button">
                <i class="glyphicon glyphicon-plus"></i>
                <span>选择上传文件</span>
                <!-- The file input field used as target for the file upload widget -->
                <input id="fileupload" type="file" name="files[]">
            </span>
            <span><a href="{{asset('order_tpl.xlsx')}}">下载模板文件</a></span>
            <span style="color:red">模板内带星号*的是必填项,请勿更改数据列的顺序</span>
            <br>
            <br>
            <!-- The global progress bar -->
            <div id="progress" class="progress">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <!-- The container for the uploaded files -->
            
        </div>

    </div>
    <hr>
    <h3>已上传文件</h3>
    <table id="files" class="table">
        <thead>
             <tr>
                 <th>#</th>
                 <th>文件名</th>
                 <th>操作</th>
             </tr>
         </thead> 
         <tbody>
             

         </tbody>
    </table>


     
    
    
</div>
    
@endsection

@section('js')
    @parent
    <script src="{{asset('/js/vendor/jquery.ui.widget.js')}}"></script>
    <script src="{{ asset('/js/jquery.fileupload.js') }}"></script>
    <script>
    $('#fileupload').fileupload({
        url: "{{url('upload')}}",
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result, function (index, file) {
                if(typeof(file.error)=='undefined'){
                    $row = $('<tr></tr>');
                    $row.append('<td><span class="glyphicon glyphicon-ok"></span></td>');
                    $row.append('<td>'+file.old_name+'</td');
                    $row.append('<td><button type="button" data-file="'+file.name+'" class="btn btn-sm btn-primary">导入</button></td>');
                    $('#files tbody').append($row);
                        $('#files tbody button').click(function(){
                            $this = $(this);
                            $this.prop('disabled','disabled');
                            $.ajax({
                                type:'POST',
                                url:"{{url('order/import')}}"+'?file_name='+$this.data('file'),
                                success:function(msg){
                                    $this.parent().text(msg);
                                },
                                error:function(msg){
                                    $this.parent().text(msg);
                                }

                            });
                        });

                }else{
                    $row = $('<tr></tr>');
                    $row.append('<td><span class="glyphicon glyphicon-remove"></span></td>');
                    $row.append('<td>'+file.old_name+'</td');
                    $row.append('<td>'+file.error+'</td>');
                    $('#files tbody').append($row);
                }
                    
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
    </script>
@endsection
