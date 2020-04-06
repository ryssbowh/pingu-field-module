@extends('field@field')

@section('inner')
    @foreach($values as $value)
        <div class="field-item">
            {!! $value !!}
        </div>
    @endforeach
@overwrite