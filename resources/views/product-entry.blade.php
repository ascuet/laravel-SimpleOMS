@extends('base')

@section('content')

<div class="container">
    @include('partials.info')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form action="{{url('product/entry')}}" method="GET" class="form-horizontal">
                <div class="form-group">
                    <label for="" class="col-sm-2 col-sm-offset-1">设备号</label>
                    <div class="col-sm-6">
                        <input class="form-control " type="text" name="pid" required>
                    </div>  
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary">查询</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    


     
    
    
</div>
    
@endsection
