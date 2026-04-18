<x-layouts.admin
    :page-title="__('Audit log') . ' — ' . config('app.name', 'Wedding')"
    css-page="page-audit"
>
    @include('partials.locale-switcher')
    <p class="toolbar">
        <a href="{{ route('admin.rsvp.dashboard') }}">{{ __('RSVP dashboard') }}</a>
        ·
        <a href="{{ route('admin.guests.index') }}">{{ __('Guest list') }}</a>
        ·
        <a href="{{ route('admin.seating.index') }}">{{ __('Seating chart') }}</a>
        ·
        <a href="{{ route('admin.registry.index') }}">{{ __('Gift list') }}</a>
        ·
        <a href="{{ route('admin.photos.index') }}">{{ __('Photo moderation') }}</a>
        ·
        <form method="post" action="{{ route('admin.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="link">{{ __('Sign out') }}</button>
        </form>
    </p>

    <h1>{{ __('Audit log') }}</h1>
    <p class="sub">{{ __('Recent administrative actions on this site.') }}</p>

    <form method="get" action="{{ route('admin.audit.index') }}" class="audit-filter">
        <label for="action">{{ __('Filter by action') }}</label>
        <select id="action" name="action">
            <option value="">{{ __('All actions') }}</option>
            @foreach ($availableActions as $a)
                <option value="{{ $a }}" @selected($action === $a)>{{ $a }}</option>
            @endforeach
        </select>
        <button type="submit">{{ __('Apply') }}</button>
        @if ($action)
            <a href="{{ route('admin.audit.index') }}">{{ __('Clear filter') }}</a>
        @endif
    </form>

    @if ($logs->isEmpty())
        <p class="audit-empty">{{ __('No audit entries recorded yet.') }}</p>
    @else
        <div class="table-wrap audit-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('When') }}</th>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('IP') }}</th>
                        <th>{{ __('Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>
                                <time datetime="{{ $log->created_at?->toIso8601String() }}">
                                    {{ $log->created_at?->format('Y-m-d H:i:s') }}
                                </time>
                            </td>
                            <td><code>{{ $log->action }}</code></td>
                            <td class="audit-subject">
                                @if ($log->subject_type)
                                    {{ class_basename($log->subject_type) }}#{{ $log->subject_id ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="audit-ip">{{ $log->ip ?? '—' }}</td>
                            <td>
                                @if (is_array($log->meta) && $log->meta !== [])
                                    <div class="audit-meta">{{ json_encode($log->meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</div>
                                @else
                                    <span class="audit-subject">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="pagination">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</x-layouts.admin>
