@extends('requirepin::layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Pin Required') }}</div>

                    <div class="card-body">
                        @if (session('return_payload'))
                            @php
                                [$status, $status_code, $data] = json_decode(session('return_payload'), true);
                            @endphp
                            <div class="alert alert-{!! $status === 'fail' ? 'danger' : 'success' !!} m-5 text-center">
                                {!! $data['message'] !!}
                            </div>
                        @endif
                        @if (session('pin_validation'))
                            @php
                                [$message, $url, $code] = json_decode(session('pin_validation'), true);
                            @endphp
                            <div class="alert alert-{!! $code === 200 ? 'info' : 'danger' !!} m-5 text-center">
                                {{ $message }}
                            </div>
                            <form method="POST" action="{{ $url }}">
                        @else
                                @if (!session('return_payload'))
                                    <div class="alert alert-warning m-5 text-center">
                                        {{ __('No pin validation url') }}
                                    </div>
                                @endif
                            <form method="POST" action="#">
                        @endif
                            @csrf

                            <div class="row mb-3">
                                <label for="_pin"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Pin') }}</label>

                                <div class="col-md-6">
                                    <input id="_pin" type="password"
                                        class="form-control @error('_pin') is-invalid @enderror" name="_pin" required
                                        autocomplete="_pin">

                                    @error('_pin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
