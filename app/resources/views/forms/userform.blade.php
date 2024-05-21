@extends('layouts.app')

@section('content')
<userform :userform="{{$userform}}" :user_id="{{$user_id}}"></userform>
@endsection
