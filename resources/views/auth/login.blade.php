@extends('layouts.app')

@section('content')
<!-- bck Login.vue, nxt npm run watch  -->
    <!-- <login-component valor="2" valor2 = "3" valor-random="4"></login-component> nxt Login.vue  -->
    
    <!-- @csrf
        {{ @csrf_token() }}
    -->
    <login-component token_csrf={{ @csrf_token() }}></login-component>
@endsection
