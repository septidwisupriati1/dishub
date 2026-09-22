<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * WhatsappConfig Model
 * 
 * @property int $id
 * @property string $gateway_provider
 * @property string $api_key
 * @property string $api_secret
 * @property string $phone_number
 * @property string|null $webhook_url
 * @property bool $is_active
 * @property int $daily_limit
 * @property int $current_daily_count
 * @property Carbon $reset_date
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class WhatsappConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_provider',
        'api_key',
        'api_secret',
        'phone_number',
        'webhook_url',
        'is_active',
        'daily_limit',
        'current_daily_count',
        'reset_date',
        'notes',
    ];

    protected $casts = [
        'reset_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    // Relationships
    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Methods
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function getRemainingQuota(): int
    {
        return max(0, $this->daily_limit - $this->current_daily_count);
    }

    public function hasReachedLimit(): bool
    {
        return $this->current_daily_count >= $this->daily_limit;
    }

    public function getUsagePercentage(): float
    {
        if ($this->daily_limit === 0) {
            return 0;
        }
        return ($this->current_daily_count / $this->daily_limit) * 100;
    }
}