<x-layouts.admin
    :page-title="__('Admin login') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-login"
    :wrap="false"
>
    <div class="card">
        @include('partials.locale-switcher')
        <h1>{{ __('Admin login') }}</h1>
        @if ($errors->has('password'))
            <p class="err">{{ $errors->first('password') }}</p>
        @endif
        <form method="post" action="{{ route('admin.login') }}">
            @csrf
            <label for="password">{{ __('Password') }}</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" autofocus>
            <button type="submit">{{ __('Sign in') }}</button>
        </form>
    </div>
</x-layouts.admin>
