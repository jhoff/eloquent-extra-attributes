<?php

namespace Jhoff\EloquentExtraAttributes\Nova\Fields;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use R64\NovaFields\JSON;

class ExtraAttributes extends JSON
{
    /**
     * Create a new ExtraAttributes field.
     *
     * @param  string  $fields
     * @return void
     */
    public function __construct($fields)
    {
        parent::__construct('Extra Attributes', $fields, 'extraAttributes');
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $extraAttributes = $model->$attribute;

        $this->fields->each(function (Field $field) use ($request, $extraAttributes, $attribute) {
            $field->fillInto($request, $extraAttributes, $field->attribute, $attribute . '.' . $field->attribute);
        });
    }
}
