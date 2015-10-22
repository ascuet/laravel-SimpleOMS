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
                <input id="fileupload" type="file" name="files[]" multiple>
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
    <div id="files" class="files">
        
    </div>


     
    
    
</div>
    
@endsection

@section('js')
    @parent
    <script src="{{ asset('/js/jquery.fileupload.js') }}"></script>
@endsection
