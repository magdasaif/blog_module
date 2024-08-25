@extends('blog::layouts.master')

@section('content')
    <h1>Hello World publisheeeee</h1>

    <p>Module: {!! config('blog.name') !!}</p>


    @if(count($errors) > 0)
     <alert title="{{ trans('main.form.error') }}" type="is-danger">
        <ul class="add-padding-16">
            @foreach ($errors->all() as $error)
                <li>{!! $error !!}</li>
            @endforeach
        </ul>
    </alert> 

@endif
    
    <form method="POST" action="{{route('blog.store')}}">
        @csrf
        <label>name</label>
        <input type="text" class="form-control" name="name">
        <br>
        <button class="btn btn-success btn-submit">Submit</button>
    </form>
@endsection
