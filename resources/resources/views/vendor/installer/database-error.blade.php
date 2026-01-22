@extends('vendor.installer.layout')

@section('content')
    <div class="card lighten-1">
        <div class="card-content black-text">
            <p class="card-title center-align">{{ trans('installer.database-error.another-title') }}</p>
            <hr>
            <p>{{ trans('installer.database-error.msg-here') }}</p>
                <pre class="error"><code class="error">{{ $error }}</code></pre>
            <p>{{ trans('installer.database-error.message') }}</p>
        </div>
        <div class="card-action">
            <a class="btn waves-effect red waves-light" href="{{ url('install/database') }}">
                {{ trans('installer.database-error.button') }}
            </a>
        </div>
    </div>
@endsection


@section('style')
<style>
    code.error {
        white-space: pre-wrap;
        color: red;
    }

    pre.error {
        background: lavenderblush;
        padding: 15px;
    }
</style>
@endsection
