<?php

namespace Jhoff\EloquentExtraAttributes\Traits;

use Jhoff\EloquentExtraAttributes\Models\ExtraAttribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait ExtraAttributes
{
    public static function bootExtraAttributes()
    {
        static::saved(function (Model $model) {
            if ($model->extraAttributes->isDirty('attrs')) {
                if ($model->wasRecentlyCreated) {
                    $model->extraAttributes->model_id = $model->id;
                }
                $model->extraAttributes->save();
            }
        });
    }

    public function extraAttributes()
    {
        return $this->morphOne(ExtraAttribute::class, 'model')
            ->withDefault([
                'model_id' => $this->id,
                'model_type' => static::class,
            ]);
    }

    public function getAttribute($key)
    {
        return $this->hasAllowedExtra($key) ?
            $this->extraAttributes->$key :
            parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        return $this->hasAllowedExtra($key) ?
            $this->extraAttributes->setAttribute($key, $value) :
            parent::setAttribute($key, $value);
    }

    public function offsetExists($offset): bool
    {
        return $this->hasAllowedExtra($offset) ?
            $this->extraAttributes->offsetExists($offset) :
            parent::offsetExists($offset);
    }

    public function offsetUnset($offset): void
    {
        $this->extraAttributes->offsetUnset($offset);
        parent::offsetUnset($offset);
    }

    protected function hasAllowedExtra($key)
    {
        return property_exists($this, 'allowedExtras') &&
            in_array($key, $this->allowedExtras);
    }

    public function scopeWhereExtra($query, $column, $operator = null, $value = null, $boolean = 'and')
    {
        return $query->whereHas('extraAttributes', fn ($query) =>
            $query->where('attrs->' . $column, $operator, $value, $boolean));
    }
}
