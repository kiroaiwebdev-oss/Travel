<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'group', 'value', 'type', 'is_public'];

    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }

    /**
     * Transparently encrypt secret setting values (API keys, tokens, secrets) at rest.
     * Reads fall back to the raw value if it isn't encrypted (handles legacy/plaintext).
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->isSecretKey() ? $this->safeDecrypt($value) : $value,
            set: fn ($value) => ($this->isSecretKey() && filled($value)) ? Crypt::encryptString((string) $value) : $value,
        );
    }

    private function isSecretKey(): bool
    {
        $key = (string) ($this->attributes['key'] ?? '');

        return str_contains($key, '_key')
            || str_contains($key, '_token')
            || str_ends_with($key, '_secret')
            || str_ends_with($key, '_password');
    }

    private function safeDecrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    /** Typed value accessor. */
    public function typedValue(): mixed
    {
        return match ($this->type) {
            'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'int' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => json_decode((string) $this->value, true),
            default => $this->value,
        };
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::rememberForever('settings.all', fn () => self::all()->keyBy('key'));

        return isset($all[$key]) ? $all[$key]->typedValue() : $default;
    }

    public static function publicSettings(): array
    {
        return self::where('is_public', true)->get()
            ->mapWithKeys(fn (Setting $s) => [$s->key => $s->typedValue()])
            ->all();
    }
}
