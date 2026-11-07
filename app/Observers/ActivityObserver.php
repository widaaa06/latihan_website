<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    private const TRACKED_ATTRIBUTES = [
        'name',
        'email',
        'role',
        'nama',
        'alamat',
        'telepon',
        'latitude',
        'longitude',
    ];

    public function created(Model $model): void
    {
        $this->record($model, 'created', $this->trackedAttributes($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated', $this->trackedAttributes($model->getChanges()));
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', $this->trackedAttributes($model->getAttributes()));
    }

    private function record(Model $model, string $event, array $properties): void
    {
        $modelLabel = match (true) {
            $model instanceof User => 'Pengguna',
            $model instanceof Branch => 'Cabang',
            $model instanceof Category => 'Kategori',
            default => class_basename($model),
        };

        $name = $model instanceof User
            ? $model->name
            : ($model->getAttribute('nama') ?? '');

        $verb = match ($event) {
            'created' => 'ditambahkan',
            'updated' => 'diperbarui',
            'deleted' => 'dihapus',
        };

        ActivityLog::record(
            $event,
            trim("{$modelLabel} {$name} {$verb}"),
            $model,
            $properties,
        );
    }

    private function trackedAttributes(array $attributes): array
    {
        return array_intersect_key($attributes, array_flip(self::TRACKED_ATTRIBUTES));
    }
}
