@extends("app")
@section("title", $title ?? "ehr")
@section("content")
    {{ $slot }}
@endsection
