<?php

namespace macgyer\yii2cropit\validators;

use yii\validators\Validator;

class DataUriValidator extends Validator
{
    private $pattern = "/data:(.*?)(?:;charset=(.*?))?(;base64)?,(.+)/i";

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('yii2-cropit', '{attribute} is not a valid data URI.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $result = $this->validateValue($value);
        
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (preg_match($this->pattern, $value)) {
            return null;
        }

        return [$this->message, []];
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $js = "if (!{$this->pattern}.test(value)) {messages.push({$message});}";
        return $js;
    }
}
