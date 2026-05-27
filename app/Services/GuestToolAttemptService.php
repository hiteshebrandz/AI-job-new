<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestToolAttemptService
{
    public const TOOL_RESUME_TEST = 'resume_test';

    public const TOOL_ATS_CHECK = 'ats_check';

    public const MAX_ATTEMPTS = 3;

    public function guestSessionId(Request $request): string
    {
        $key = 'guest_tool_session_id';

        if (! $request->session()->has($key)) {
            $request->session()->put($key, (string) Str::uuid());
        }

        return (string) $request->session()->get($key);
    }

    public function getCount(Request $request, string $tool): int
    {
        return (int) $request->session()->get($this->sessionKey($tool), 0);
    }

    public function remaining(Request $request, string $tool): int
    {
        return max(0, self::MAX_ATTEMPTS - $this->getCount($request, $tool));
    }

    public function isLocked(Request $request, string $tool): bool
    {
        if ($request->user()) {
            return false;
        }

        return $this->getCount($request, $tool) >= self::MAX_ATTEMPTS;
    }

    public function increment(Request $request, string $tool): void
    {
        if ($request->user()) {
            return;
        }

        $request->session()->put(
            $this->sessionKey($tool),
            $this->getCount($request, $tool) + 1
        );
    }

    /**
     * @return array{resume_test: array{used: int, remaining: int, locked: bool}, ats_check: array{used: int, remaining: int, locked: bool}}
     */
    public function summary(Request $request): array
    {
        return [
            self::TOOL_RESUME_TEST => $this->toolSummary($request, self::TOOL_RESUME_TEST),
            self::TOOL_ATS_CHECK   => $this->toolSummary($request, self::TOOL_ATS_CHECK),
        ];
    }

    /**
     * @return array{used: int, remaining: int, max: int, locked: bool}
     */
    public function toolSummary(Request $request, string $tool): array
    {
        $used = $this->getCount($request, $tool);

        return [
            'used'      => $used,
            'remaining' => max(0, self::MAX_ATTEMPTS - $used),
            'max'       => self::MAX_ATTEMPTS,
            'locked'    => $used >= self::MAX_ATTEMPTS,
        ];
    }

    private function sessionKey(string $tool): string
    {
        return "guest_attempts.{$tool}";
    }
}
