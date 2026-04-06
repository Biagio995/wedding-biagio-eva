<x-layouts.site-public
    :page-title="__('Gift list') . ' — ' . config('wedding.event.title', config('app.name'))"
    :brand-title="config('wedding.event.title')"
    page="registry"
>
    <main class="registry-main">
        <p class="badge">{{ __('Gift list') }}</p>
        <h1 class="registry-title">{{ __('Gift list') }}</h1>
        <p class="registry-lead">{{ __('Choose an available gift. Once reserved, it disappears from this list for other guests.') }}</p>

        @if (session('registry_success'))
            <p class="registry-flash registry-flash--ok" role="status">{{ session('registry_success') }}</p>
        @endif
        @if (session('registry_error'))
            <p class="registry-flash registry-flash--err" role="alert">{{ session('registry_error') }}</p>
        @endif
        @if ($errors->has('name'))
            <p class="registry-flash registry-flash--err" role="alert">{{ $errors->first('name') }}</p>
        @endif

        @if ($showEmptyCatalog)
            <p class="registry-empty">{{ __('No items in the gift list yet.') }}</p>
        @else
            @if ($availableItems->isNotEmpty())
                <ul class="registry-list">
                    @foreach ($availableItems as $item)
                        <li class="registry-item">
                            <div class="registry-item__body">
                                <div class="registry-item__text registry-item__text--full">
                                    <h2 class="registry-item__title">
                                        @if ($item->product_url)
                                            <a href="{{ $item->product_url }}" target="_blank" rel="noopener noreferrer">{{ $item->title }}</a>
                                        @else
                                            {{ $item->title }}
                                        @endif
                                    </h2>
                                    @if ($item->description)
                                        <p class="registry-item__desc">{{ $item->description }}</p>
                                    @endif
                                    <form
                                        method="post"
                                        action="{{ route('registry.claim', $item) }}"
                                        class="registry-claim"
                                        data-turbo="false"
                                    >
                                        @csrf
                                        <label class="registry-claim__label" for="registry-name-{{ $item->id }}">{{ __('Your name for this gift') }}</label>
                                        <div class="registry-claim__row">
                                            <input
                                                id="registry-name-{{ $item->id }}"
                                                class="registry-claim__input"
                                                type="text"
                                                name="name"
                                                value="{{ old('name', $guest?->name) }}"
                                                required
                                                maxlength="255"
                                                autocomplete="name"
                                                placeholder="{{ __('Your name') }}"
                                            >
                                            <label class="registry-checkbox-label registry-checkbox-label--claim">
                                                <input
                                                    type="checkbox"
                                                    class="registry-checkbox registry-claim__toggle"
                                                    autocomplete="off"
                                                >
                                                <span class="registry-checkbox-face" aria-hidden="true"></span>
                                                <span class="registry-checkbox-text">{{ __('I will bring this gift') }}</span>
                                            </label>
                                        </div>
                                    </form>
                                    <p class="registry-item__meta">
                                        <span class="tag tag-free">{{ __('Available') }}</span>
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($noGiftsAvailableForYou)
                <p class="registry-empty registry-empty--muted">{{ __('No gifts are available right now. Everything on the list has already been chosen.') }}</p>
            @endif
        @endif
    </main>
</x-layouts.site-public>
