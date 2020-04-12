@extends('field@field')

@section('inner')
    @foreach($data['values'] as $value)
        <div class="field-item">
            {!! $value !!}
        </div>
    @endforeach
@overwrite