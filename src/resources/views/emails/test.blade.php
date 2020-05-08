@extends('emails.layouts.default')

@section('title', 'Test')

@section('preheader', 'Test')

@section('content')
    <p>Test</p>
    @component('emails.components.button', ['url' => config('app.url')])
        Test button
    @endcomponent
@endsection