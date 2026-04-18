<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Throwable;

/**
 * Writes audit log entries for admin actions. Failures are swallowed so auditing
 * never breaks the primary action. Keep `meta` small; avoid secrets or PII not
 * already stored in the referenced model.
 */
class AuditLogger
{
    public function __construct(private readonly Request $request) {}

    /** @param array<string, mixed> $meta */
    public function log(string $action, ?Model $subject = null, array $meta = []): void
    {
        try {
            $userAgent = (string) $this->request->userAgent();
            if (strlen($userAgent) > 500) {
                $userAgent = substr($userAgent, 0, 500);
            }

            AuditLog::query()->create([
                'action' => $action,
                'subject_type' => $subject !== null ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'ip' => $this->request->ip(),
                'user_agent' => $userAgent !== '' ? $userAgent : null,
                'meta' => $meta === [] ? null : $meta,
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
