<?php

namespace Jhoff\EloquentExtraAttributes\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraAttribute extends Model
{
    use HasFactory;

    protected $casts = [
        'attrs' => AsArrayObject::class,
    ];

    protected $attributes = [
        'attrs' => '[]',
    ];

    protected $fillable = [
        'model_id',
        'model_type',
        'attrs',
    ];

    protected $touches = ['model'];

    public function model()
    {
        return $this->morphTo();
    }

    public function getAttribute($key)
    {
        return parent::getAttribute($key) ?? $this->attrs[$key] ?? null;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, array_merge($this->fillable, ['id', 'created_at', 'updated_at']))) {
            return parent::setAttribute($key, $value);
        }

        $this->attrs[$key] = $value;

        return $this;
    }

    public function offsetExists($offset): bool
    {
        return (isset($this->attrs) && $this->attrs->offsetExists($offset)) ||
            parent::offsetExists($offset);
    }

    public function offsetUnset($offset): void
    {
        parent::offsetUnset($offset);

        if (isset($this->attrs) && $this->attrs->offsetExists($offset)) {
            $this->attrs->offsetUnset($offset);
        }
    }
}
