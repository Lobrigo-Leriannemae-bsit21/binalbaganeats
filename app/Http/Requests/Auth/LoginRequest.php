<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Sign in through a specific portal. Only accounts whose role matches the
     * portal AND whose status is approved may stay signed in.
     * Vendor, rider and admin portals will reuse this in later phases.
     */
    public function authenticate(UserRole $portal): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->failLogin('These credentials do not match our records.');
        }

        $user = Auth::user();

        if ($user->role !== $portal) {
            $this->failLogin('This account cannot sign in on this page. Please use the correct portal.', true);
        }

        if ($user->status !== UserStatus::Approved) {
            $this->failLogin(
                $user->status === UserStatus::Pending
                    ? 'Your account is still awaiting approval.'
                    : 'Your account is not active. Please contact support.',
                true
            );
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function failLogin(string $message, bool $logout = false): never
    {
        if ($logout) {
            Auth::guard('web')->logout();
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages(['email' => $message]);
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower((string) $this->input('email')).'|'.$this->ip());
    }
}