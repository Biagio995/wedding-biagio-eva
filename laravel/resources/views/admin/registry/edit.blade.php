<x-layouts.admin
    :page-title="__('Edit gift item') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-registry"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
        ·
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Edit gift item') }}</h1>
    <p class="sub">{{ __('Update details or clear a reservation.') }}</p>

    @if ($errors->any())
        <p class="err" role="alert">{{ $errors->first() }}</p>
    @endif

    <section class="card">
        <form method="post" action="{{ route('admin.registry.update', $item) }}" class="stack">
            @csrf
            @method('PUT')
            <div>
                <label for="title">{{ __('Item title') }}</label>
                <input id="title" type="text" name="title" value="{{ old('title', $item->title) }}" required maxlength="255" autocomplete="off">
            </div>
            <div>
                <label for="description">{{ __('Description (optional)') }}</label>
                <textarea id="description" name="description" rows="4" maxlength="5000">{{ old('description', $item->description) }}</textarea>
            </div>
            <div>
                <label for="product_url">{{ __('Link (optional)') }}</label>
                <input id="product_url" type="url" name="product_url" value="{{ old('product_url', $item->product_url) }}" placeholder="https://" maxlength="2048" inputmode="url" autocomplete="off">
            </div>
            <div>
                <label for="sort_order">{{ __('Sort order') }}</label>
                <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" min="0" max="99999">
            </div>
            <div class="row-check">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ? '1' : '0') === '1')>
                <label for="is_active">{{ __('Visible on public page') }}</label>
            </div>
            @if ($item->isClaimed())
                @if (filled($item->claim_message))
                    <div class="registry-admin-msg registry-admin-msg--box">
                        <span class="registry-admin-msg__label">{{ __('Message from the guest') }}</span>
                        <p class="registry-admin-msg__text">{{ $item->claim_message }}</p>
                    </div>
                @endif
                <div class="row-check">
                    <input type="hidden" name="clear_claim" value="0">
                    <input id="clear_claim" type="checkbox" name="clear_claim" value="1" @checked(old('clear_claim'))>
                    <label for="clear_claim">
                        {{ __('Clear reservation') }}
                        @if ($item->claimedBy)
                            ({{ $item->claimedBy->name }})
                        @else
                            ({{ __('Anonymous (browser)') }})
                        @endif
                    </label>
                </div>
            @endif
            <div class="actions">
                <button type="submit">{{ __('Save') }}</button>
                <a href="{{ route('admin.registry.index') }}">{{ __('Cancel') }}</a>
            </div>
        </form>
    </section>
</x-layouts.admin>
