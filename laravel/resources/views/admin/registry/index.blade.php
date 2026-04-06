<x-layouts.admin
    :page-title="__('Gift list') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-registry"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>
    <h1>{{ __('Gift list') }}</h1>
    <p class="sub">{{ __('Manage the gift list shown to guests.') }}</p>
    <p class="registry-admin-help">{{ __('Reservations without an invitation link are tied to the visitor\'s browser. To remove a gift chosen by mistake, open Edit and use Clear reservation.') }}</p>

    @if (session('status'))
        <p class="ok" role="status">{{ session('status') }}</p>
    @endif

    <section class="card" aria-labelledby="registry-add-heading">
        <h2 id="registry-add-heading" class="h2">{{ __('Add item') }}</h2>
        <form method="post" action="{{ route('admin.registry.store') }}" class="stack">
            @csrf
            <div>
                <label for="new-title">{{ __('Item title') }}</label>
                <input id="new-title" type="text" name="title" value="{{ old('title') }}" required maxlength="255" autocomplete="off">
            </div>
            <div>
                <label for="new-desc">{{ __('Description (optional)') }}</label>
                <textarea id="new-desc" name="description" rows="3" maxlength="5000">{{ old('description') }}</textarea>
            </div>
            <div>
                <label for="new-url">{{ __('Link (optional)') }}</label>
                <input id="new-url" type="url" name="product_url" value="{{ old('product_url') }}" placeholder="https://" maxlength="2048" inputmode="url" autocomplete="off">
            </div>
            <div>
                <label for="new-sort">{{ __('Sort order') }}</label>
                <input id="new-sort" type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="99999">
            </div>
            <div class="row-check">
                <input type="hidden" name="is_active" value="0">
                <input id="new-active" type="checkbox" name="is_active" value="1" @checked(old('is_active', '1') === '1')>
                <label for="new-active">{{ __('Visible on public page') }}</label>
            </div>
            <div class="actions">
                <button type="submit">{{ __('Add item') }}</button>
            </div>
        </form>
    </section>

    <h2 class="h2 section-title">{{ __('Items') }}</h2>
    @if ($items->isEmpty())
        <p class="empty">{{ __('No items in the gift list yet.') }}</p>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Sort order') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Reserved by') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>
                                @if ($item->product_url)
                                    <a href="{{ $item->product_url }}" target="_blank" rel="noopener noreferrer">{{ $item->title }}</a>
                                @else
                                    {{ $item->title }}
                                @endif
                            </td>
                            <td class="num">{{ $item->sort_order }}</td>
                            <td>
                                @if ($item->is_active)
                                    <span class="badge badge-yes">{{ __('Visible') }}</span>
                                @else
                                    <span class="badge badge-no">{{ __('Hidden') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($item->isClaimed())
                                    @if (filled($item->claimed_by_name))
                                        {{ $item->claimed_by_name }}
                                    @elseif ($item->claimedBy)
                                        {{ $item->claimedBy->name }}
                                    @else
                                        {{ __('Anonymous (browser)') }}
                                    @endif
                                @else
                                    {{ __('Nobody yet') }}
                                @endif
                            </td>
                            <td class="actions">
                                <a href="{{ route('admin.registry.edit', $item) }}">{{ __('Edit') }}</a>
                                <form
                                    method="post"
                                    action="{{ route('admin.registry.destroy', $item) }}"
                                    style="display:inline;"
                                    onsubmit="return confirm({{ json_encode(__('Delete this item?')) }});"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="link">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.admin>
