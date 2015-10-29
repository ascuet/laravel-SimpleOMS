@extends('base')

@section('content')

<div class="container">
    @include('partials.info')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form action="{{url('product/entry')}}" id="form" method="GET" class="form-horizontal">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="" class="col-sm-2 col-sm-offset-1">设备号</label>
                    <div class="col-sm-6">
                        <input class="form-control " type="text" name="pid" value="{{old('pid')}}" required>
                    </div>  
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary">查询</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <hr>
    @if(isset($order))    
    <h3>当前订单</h3>
    <table class="table table-hover table-condensed">
        <thead>
            <tr>

                <th>#</th>
                <td>淘宝ID</td>
                <td>客户姓名</td>
                <td>国家</td>
                <td>数量</td>
                <td>回国日期</td>
                <td>库存名</td>
                <td>客服备注</td>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <th><input type="checkbox" name="id" value="{{$order->id}}"></th>
                <td>{{$order->gid}}</td>
                <td>{{$order->gname}}</td>
                <td>{{$order->country}}</td>
                <td>{{$order->amount}}</td>
                <td>{{$order->back_date}}</td>
                <td>{{$order->belongsToSupply->name}}</td>
                <td>{{$order->memo}}</td>
                </tr>
        </tbody>
    </table>
    <hr>
    <h3>包含设备</h3>
    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <td>设备号</td>
                <td>国家</td>
                <td>库存名</td>
                <td>归还日期</td>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                @if($product->pid == old('pid'))
                <tr class="warning">
                @else
                <tr>
                @endif
                    <th><input type="checkbox" name="id" value="{{$product->id}}"></th>
                <td>{{$product->pid}}</td>
                <td>{{$product->country}}</td>
                <td>{{$product->belongsToSupply->name}}</td>
                <td>{{$product->pivot->return_at or '待入库'}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @endif
     
    
    
</div>
    
@endsection
