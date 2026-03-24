<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogger
{
    protected array $except = [
        'broadcasting/*',
        '_debugbar/*',
        'telescope/*',
    ];

    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        '_token',
        'card_number',
        'cvv',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $response;
            }
        }

        $this->log($request, $response->getStatusCode());

        return $response;
    }

    protected function log(Request $request, int $statusCode): void
    {
        $guardType = null;
        $guardId   = null;

        if (auth('web')->check()) {
            $guardType = 'buyer';
            $guardId   = auth('web')->id();
        } elseif (auth('seller')->check()) {
            $guardType = 'seller';
            $guardId   = auth('seller')->id();
        } elseif (auth('admin')->check()) {
            $guardType = 'admin';
            $guardId   = auth('admin')->id();
        }

        ActivityLog::create([
            'guard_type'  => $guardType,
            'guard_id'    => $guardId,
            'ip_address'  => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
            'method'      => $request->method(),
            'url'         => substr($request->fullUrl(), 0, 2000),
            'status_code' => $statusCode,
            'payload'     => $this->sanitizePayload(
                $request->except($this->sensitiveFields)
            ),
        ]);
    }

    protected function sanitizePayload(array $data): ?string
    {
        if (empty($data)) return null;
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}