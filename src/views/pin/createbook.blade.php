@extends('requirepin::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create Book') }}</div>

                <div class="card-body">
                    @if (session('return_payload'))
                        @php
                            [$status, $status_code, $data] = json_decode(session('return_payload'));
                        @endphp
                        <div class="alert alert-{!! $status === 'fail' ? 'danger' : 'success' !!} m-5 text-center">
                            {!! $status !!}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('createBookTest') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="isbn" class="col-md-4 col-form-label text-md-end">{{ __('ISBN') }}</label>

                            <div class="col-md-6">
                                <input id="isbn" type="text" class="form-control @error('isbn') is-invalid @enderror" name="isbn" value="{{ old('isbn') }}" required autocomplete="isbn">

                                @error('isbn')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="authors" class="col-md-4 col-form-label text-md-end">{{ __('Authors') }}</label>

                            <div class="col-md-6">
                                <input id="authors" type="text" class="form-control @error('authors') is-invalid @enderror" name="authors" value="{{ old('authors') }}" required autocomplete="authors">

                                @error('authors')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="country" class="col-md-4 col-form-label text-md-end">{{ __('Country') }}</label>

                            <div class="col-md-6">
                                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ old('country') }}" required autocomplete="country">

                                @error('country')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="number_of_pages" class="col-md-4 col-form-label text-md-end">{{ __('Pages') }}</label>

                            <div class="col-md-6">
                                <input id="number_of_pages" type="text" class="form-control @error('number_of_pages') is-invalid @enderror" name="number_of_pages" value="{!! old('number_of_pages') ?? rand(1000,9999) !!}" required autocomplete="number_of_pages">

                                @error('number_of_pages')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="publisher" class="col-md-4 col-form-label text-md-end">{{ __('Publisher') }}</label>

                            <div class="col-md-6">
                                <input id="publisher" type="text" class="form-control @error('publisher') is-invalid @enderror" name="publisher" value="{{ old('publisher') }}" required autocomplete="publisher">

                                @error('publisher')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="release_date" class="col-md-4 col-form-label text-md-end">{{ __('Release Date') }}</label>

                            <div class="col-md-6">
                                <input id="release_date" type="date" class="form-control @error('release_date') is-invalid @enderror" name="release_date" value="{{ old('release_date') }}" required autocomplete="release_date">

                                @error('release_date')
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
