@extends('app')
@section('title')
	棉花糖管理系统
@stop

@section('css')
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/bootstrap-datetimepicker.css') }}" rel="stylesheet">	
@stop

@section('nav')
	@include('partials.navs')
@stop

@section('actionNav')
	@if(isset($actions))
	@include('partials.actions')
	@endif
@stop

@section('js')
	<script src="{{ asset('/js/base.js') }}"></script>
	<script src="{{ asset('/js/home.js') }}"></script>
@stop