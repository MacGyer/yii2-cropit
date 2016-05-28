<?php

namespace macgyer\yii2cropit\validators;

use yii\validators\Validator;

class DataUriValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is not a valid data URI.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $result = $this->validateValue($value);
        
        // TODO: implement
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        // TODO: implement validation

        return [$this->message, []];
    }
}
