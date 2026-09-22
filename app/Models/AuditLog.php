<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeByModelId($query, $modelId)
    {
        return $query->where('model_id', $modelId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    public function scopeCreated($query)
    {
        return $query->where('action', 'create');
    }

    public function scopeUpdated($query)
    {
        return $query->where('action', 'update');
    }

    public function scopeDeleted($query)
    {
        return $query->where('action', 'delete');
    }

    // Methods
    public function getChanges(): array
    {
        if ($this->action === 'create') {
            return $this->new_data ?? [];
        }

        if ($this->action === 'delete') {
            return $this->old_data ?? [];
        }

        // For updates, return only changed fields
        $changes = [];
        if ($this->old_data && $this->new_data) {
            foreach ($this->new_data as $key => $newValue) {
                $oldValue = $this->old_data[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }

    public function isCreate(): bool
    {
        return $this->action === 'create';
    }

    public function isUpdate(): bool
    {
        return $this->action === 'update';
    }

    public function isDelete(): bool
    {
        return $this->action === 'delete';
    }
}