@extends('app')
@section('title')
	棉花糖管理系统
@stop

@section('css')
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
@stop

@section('nav')
	@include('partials.navs')
@stop

@section('actionNav')
	@if(isset($actions))
	@include('partials.actions')
	@endif
@stop