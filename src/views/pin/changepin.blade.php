@extends('requirepin::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Change Pin') }}</div>

                <div class="card-body">
                    @if(session('return_payload'))
                        @php
                            [$status, $status_code, $data] = json_decode(session('return_payload'), true);
                        @endphp
                        <div class="alert alert-{!! $status === 'fail' ? 'danger' : 'success' !!} m-5 text-center">
                            {!! $data['message'] !!}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('changePinWeb') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="current_pin" class="col-md-4 col-form-label text-md-end">{{ __('Old Pin') }}</label>

                            <div class="col-md-6">
                                <input id="current_pin" type="password" class="form-control @error('current_pin') is-invalid @enderror" name="current_pin" required autocomplete="current_pin">

                                @error('current_pin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="pin" class="col-md-4 col-form-label text-md-end">{{ __('New Pin') }}</label>

                            <div class="col-md-6">
                                <input id="pin" type="password" class="form-control @error('pin') is-invalid @enderror" name="pin" required autocomplete="pin">

                                @error('pin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="pin_confirmation" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Pin') }}</label>

                            <div class="col-md-6">
                                <input id="pin_confirmation" type="password" class="form-control @error('pin_confirmation') is-invalid @enderror" name="pin_confirmation" required autocomplete="pin_confirmation">

                                @error('pin_confirmation')
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
