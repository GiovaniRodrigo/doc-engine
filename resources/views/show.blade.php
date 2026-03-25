@extends(view()->exists(config('documentation-engine.layout')) ? config('documentation-engine.layout') : 'documentation-engine::layout')
@section('content')
    {!! $html !!}
@endsection
