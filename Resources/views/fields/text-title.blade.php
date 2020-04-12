@extends('field@field')

@section('inner')
    @foreach($data['values'] as $value)
        <div class="field-item">
            <h1>{!! $value !!}</h1>
        </div>
    @endforeach
@overwrite