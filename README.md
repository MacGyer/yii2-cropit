[![license](https://img.shields.io/badge/LICENCE-BSD--3--Clause-blue.svg)](https://packagist.org/packages/macgyer/yii2-cropit)
[![Github Release](https://img.shields.io/github/release/macgyer/yii2-cropit.svg)](https://packagist.org/packages/macgyer/yii2-cropit)
[![Packagist](https://img.shields.io/packagist/dt/macgyer/yii2-cropit.svg)](https://packagist.org/packages/macgyer/yii2-cropit)

# yii2-cropit
Implementation of Scott Cheng's jQuery plugin [cropit](https://github.com/scottcheng/cropit).

**Currently implemented cropit.js version: 0.5.1**

## Installation

The preferred way of installation is through Composer.
If you don't have Composer you can get it here: https://getcomposer.org/

To install the package add the following to the ```require``` section of your composer.json:
```
"require": {
    "macgyer/yii2-cropit": "*"
},
```

## Usage

This widget can be used in ActiveForm or as standalone input widget and comes with a [Data URI](https://en.wikipedia.org/wiki/Data_URI_scheme)
validator (as Composer dependency).

The widget can be profoundly configured to meet your needs. Please see all options and below and refer to the original
[cropit documentation](http://scottcheng.github.io/cropit/).

To use the widget in your form, you might do the following:

```
// add the field to your Model class, either ActiveRecord property or class member:

public $cropped_image_data;

// rules
public function rules()
{
  return [
      // more rules
      ['cropped_image_data', \macgyer\yii2dataurivalidator\DataUriValidator::className()],
  ];
}
```

```
// in your View, define the field and widget:

<?= $form->field($model, 'cropped_image_data')->widget(\macgyer\yii2cropit\widgets\CropitWidget::className()) ?>
```

## Road map

* create style assets

## Change log

### 1.0.3 - 2017-07-29
* fixed illegal offset type error when used in PHP 5.6 environment ([#3](https://github.com/MacGyer/yii2-cropit/issues/3))
* cropit.js version set to 0.5.1

### 1.0.2 - 2016-06-02
* changed handling of custom JS handlers ([#2](https://github.com/MacGyer/yii2-cropit/issues/2))
* fixed image export options bug ([#1](https://github.com/MacGyer/yii2-cropit/issues/1))

### 1.0.1 - 2016-05-30
* moved [DataUriValidator into separate repo](https://github.com/MacGyer/yii2-data-uri-validator)

### 1.0.0 - 2016-05-29
* initial release
