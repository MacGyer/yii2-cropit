<?php
/**
 * @link https://github.com/MacGyer/yii2-cropit
 * @copyright Copyright (c) 2016 ... MacGyer for pluspunkt coding
 * @license https://github.com/MacGyer/yii2-cropit/blob/master/LICENSE
 */

namespace macgyer\yii2cropit\widgets;

use macgyer\yii2cropit\assets\CropitAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use yii\base\Exception;

/**
 * CropitWidget provides a wrapper for cropit, a jQuery crop and zoom tool.
 *
 * This widget can be used in ActiveForm or as standalone input widget and comes with a [Data URI](https://en.wikipedia.org/wiki/Data_URI_scheme)
 * validator (as Composer dependency).
 *
 * The widget can be profoundly configured to meet your needs. Please see all options and below and refer to the original
 * [cropit documentation](http://scottcheng.github.io/cropit/).
 *
 * To use the widget in your form, you might do the following:
 *
 * ```php
 * // add the field to your Model class, either ActiveRecord property or class member:
 *
 * public $cropped_image_data;
 *
 * // rules
 * public function rules()
 * {
 *      return [
 *          // more rules
 *          ['cropped_image_data', \macgyer\yii2dataurivalidator\DataUriValidator::className()],
 *      ];
 * }
 * ```
 * 
 * ```php
 * // in your View, define the field and widget:
 * 
 * <?= $form->field($model, 'cropped_image_data')->widget(\macgyer\yii2cropit\widgets\CropitWidget::className()) ?>
 * ```
 *
 * @package yii2cropit
 * @see http://scottcheng.github.io/cropit/
 */
class CropitWidget extends InputWidget
{
    /**
     * @var array the HTML attributes for the widget container tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $containerOptions = [];

    /**
     * @var array the HTML attributes for the preview container tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     *
     * If you change the [cropit $preview](http://scottcheng.github.io/cropit/#docs-options-preview) options in
     * [[pluginOptions]], make sure to add the correct CSS class or ID to [[previewOptions]]
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $previewOptions = [];

    /**
     * @var array the options for the underlying JS plugin.
     *
     * This array will be JSON encoded and passed to the `cropit()` call.
     *
     * Please refer to the
     * [official cropit options documentation](http://scottcheng.github.io/cropit/#docs)
     * for an overview of all options and how to use them.
     */
    public $pluginOptions = [];

    /**
     * @var string the custom JS actions to perform.
     * 
     * These handlers will be appended to the existing scripts.
     * @see [[registerAssets()]]
     */
    public $customJsHandlers;

    /**
     * @var array the options passed to the cropit export function.
     *
     * This array will be JSON encoded and passed to the `cropit('export', options)` call.
     *
     * Please refer to the
     * [official cropit API documentation](http://scottcheng.github.io/cropit/#docs-apis)
     * for an overview of all options and how to use them.
     */
    public $imageExportOptions = [];

    /**
     * @var array the HTML attributes for the select image button tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     * - label: string, defaults to "Select image", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $selectImageButtonOptions = [];

    /**
     * @var array|boolean the HTML attributes for the zoom slider tag.
     *
     * The zoom slider is a HTML input tag with type "range".
     *
     * If this property is to boolean "false", no zoom control elements including the [[zoomOutLabelOptions]],
     * [[zoomInLabelOptions]] and [[zoomControlsWrapperOptions]] will be rendered.
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $zoomSliderOptions = [];

    /**
     * @var array|boolean the HTML attributes for the zoom out label tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "span", the name of the container tag.
     * - label: string, defaults to "-", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * To disable the rendering of the label, set this variable to boolean "false".
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $zoomOutLabelOptions = [];

    /**
     * @var array|boolean the HTML attributes for the zoom in label tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "span", the name of the container tag.
     * - label: string, defaults to "+", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * To disable the rendering of the label, set this variable to boolean "false".
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $zoomInLabelOptions = [];

    /**
     * @var array|boolean the HTML attributes for the zoom controls wrapper tag.
     *
     * If this is being rendered, the wrapper encloses the [[zoomSliderOptions]], [[zoomOutLabelOptions]] and [[zoomInLabelOptions]].
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     *
     * To disable the rendering of the wrapper tag, set this variable to boolean "false".
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $zoomControlsWrapperOptions = [];

    /**
     * @var boolean whether to show rotate controls. If this option is set to "false", no rotation control elements including
     * [[rotateLeftButtonOptions]], [[rotateRightButtonOptions]] and [[rotateControlsWrapperOptions]] are rendered.
     */
    public $showRotateControls = true;

    /**
     * @var array the HTML attributes for the counter clockwise rotation tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     * - label: string, defaults to "CCW", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $rotateLeftButtonOptions = [];

    /**
     * @var array the HTML attributes for the clockwise rotation tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     * - label: string, defaults to "CW", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $rotateRightButtonOptions = [];

    /**
     * @var array|boolean the HTML attributes for the rotation controls wrapper tag.
     *
     * If this is being rendered, the wrapper encloses the [[rotateLeftButtonOptions]] and [[rotateRightButtonOptions]].
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     *
     * To disable the rendering of the wrapper tag, set this variable to boolean "false".
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $rotateControlsWrapperOptions = [];

    /**
     * @var array the HTML attributes for the crop image button tag.
     *
     * The following special options are recognized:
     * - tag: string, defaults to "div", the name of the container tag.
     * - label: string, defaults to "Crop image", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $cropImageButtonOptions = [];

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        if (!isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = $this->getId();
        }

        // container
        Html::addCssClass($this->containerOptions, ['container' => 'imageCropperInner']);

        // preview
        Html::addCssClass($this->previewOptions, ['preview' => 'cropit-preview']);

        // zoom controls wrapper
        if ($this->zoomControlsWrapperOptions !== false) {
            Html::addCssClass($this->zoomControlsWrapperOptions, ['zoom-controls' => 'controls-zoom']);
        }

        // rotate controls wrapper
        if ($this->showRotateControls === true && $this->rotateControlsWrapperOptions !== false) {
            Html::addCssClass($this->rotateControlsWrapperOptions, ['rotate-controls' => 'controls-rotate']);
        }

        $this->initControls();
    }

    /**
     * Executes the widget.
     * @return string the result of widget execution to be outputted.
     */
    public function run()
    {
        $html[] = '';

        $tag = ArrayHelper::remove($this->containerOptions, 'tag', 'div');
        $html[] = Html::beginTag($tag, $this->containerOptions);

        // hidden file input element
        $html[] = Html::fileInput("{$this->name}_original", null, ['class' => "cropit-image-input {$this->containerOptions['id']}_image-input"]);
        
        // cropped image data
        if ($this->hasModel()) {
            $html[] = Html::activeHiddenInput($this->model, $this->attribute, ['class' => "{$this->containerOptions['id']}_crop-image-data"]);
        } else {
            $html[] = Html::hiddenInput($this->name, null, ['class' => "{$this->containerOptions['id']}_crop-image-data"]);
        }

        // Preview
        $html[] = $this->renderPreview();

        // Controls
        $html[] = $this->renderControls();

        $html[] = Html::endTag($tag);

        $this->registerAssets();

        return implode("\n", $html);
    }

    /**
     * Registers the plugin asset.
     */
    protected function registerAssets()
    {
        $view = $this->getView();

        CropitAsset::register($view);
        $id = $this->containerOptions['id'];

        $js = [];

        if ($this->pluginOptions !== false) {
            $options = empty($this->pluginOptions) ? '' : Json::htmlEncode($this->pluginOptions);
            $exportOptions = empty($this->imageExportOptions) ? '{}' : Json::htmlEncode($this->imageExportOptions);

            // init cropit
            $js[] = "jQuery('#$id').cropit($options);";

            // select image
            $js[] = "jQuery('.{$id}_select-image-btn').on('click', function() {
                        jQuery('.{$id}_image-input').click();
                    });";

            // rotate right
            $js[] = "jQuery('.{$id}_rotate-right').on('click', function() {
                        jQuery('#{$id}').cropit('rotateCW');
                    });";

            // rotate left
            $js[] = "jQuery('.{$id}_rotate-left').on('click', function() {
                        jQuery('#{$id}').cropit('rotateCCW');
                    });";

            // export the image
            $js[] = "jQuery('.{$id}_crop-image-btn').on('click', function() {
                        var imageData = jQuery('#$id').cropit('export', $exportOptions);                       
                        jQuery('.{$id}_crop-image-data').val(imageData);
                    });";
        }

        // append user defined JS
        if ($this->customJsHandlers) {
            $js[] = $this->customJsHandlers;
        }
        
        $view->registerJs(implode("\n", $js));
    }

    /**
     * Renders the preview container tag.
     * @return string the markup for the preview container.
     */
    protected function renderPreview()
    {
        return Html::tag('div', '', $this->previewOptions);
    }

    /**
     * Renders the different controls.
     * @return string the markup for the controls.
     */
    protected function renderControls()
    {
        $html = [];
        $html[] = Html::beginTag('div', ['class' => 'controls']);

        // select image button
        $html[] = $this->renderButton('selectImageButtonOptions');

        // rotate controls
        $html[] = $this->renderRotateControls();

        // zoom controls
        $html[] = $this->renderZoomControls();

        // crop image button
        $html[] = $this->renderButton('cropImageButtonOptions');

        $html[] = Html::endTag('div');
        return implode("\n", $html);
    }

    /**
     * Renders a button control element.
     * @param string $optionsProperty the name of the class property holding the button options.
     * @param string $defaultTag the name of the default button HTML tag.
     * @return string the HTML for the button.
     * @throws \yii\base\Exception if the button label is not present in the option.
     * @see selectImageButtonOptions
     * @see cropImageButtonOptions
     */
    protected function renderButton($optionsProperty, $defaultTag = 'button')
    {
        $label = ArrayHelper::remove($this->$optionsProperty, 'label');

        if (!$label) {
            throw new Exception("Button label must be specified.");
        }

        $tag = ArrayHelper::remove($this->$optionsProperty, 'tag', $defaultTag);

        if ($tag === 'button' && !isset($this->$optionsProperty['type'])) {
            $this->$optionsProperty['type'] = 'button';
        }

        $encodeLabel = ArrayHelper::remove($this->$optionsProperty, 'encodeLabel', true);

        return Html::tag($tag, $encodeLabel ? Html::encode($label) : $label, $this->$optionsProperty);
    }

    /**
     * Renders the rotate controls consisting of two buttons, each for one direction.
     *
     * No controls are rendered when [[showRotateControls]] is set to "false".
     *
     * All rotate controls can be wrapped in a parent element when [[rotateControlsWrapperOptions]] is an options array.
     * The wrapper can be disabled by setting [[rotateControlsWrapperOptions]] to "false".
     *
     * @return string the HTML for the zoom controls.
     * @uses [[renderZoomLabel()]]
     * @see zoomControlsWrapperOptions
     */
    protected function renderRotateControls()
    {
        if ($this->showRotateControls === false) {
            return '';
        }

        $html = [];

        // wrapper
        if ($this->rotateControlsWrapperOptions !== false) {
            $rotateControlsWrapperTag = ArrayHelper::remove($this->rotateControlsWrapperOptions, 'tag', 'div');
            $html[] = Html::beginTag($rotateControlsWrapperTag, $this->rotateControlsWrapperOptions);
        }

        // rotate left button
        $html[] = $this->renderButton('rotateLeftButtonOptions');

        // rotate right button
        $html[] = $this->renderButton('rotateRightButtonOptions');

        // end wrapper
        if ($this->rotateControlsWrapperOptions !== false) {
            $html[] = Html::endTag($rotateControlsWrapperTag);
        }

        return implode("\n", $html);
    }

    /**
     * Renders the zoom controls consisting of slider and optional labels.
     *
     * If zoom in/out labels are specified, the corresponding markup is returned.
     *
     * All zoom controls can be wrapped in a parent element when [[zoomControlsWrapperOptions]] is an options array.
     *
     * The wrapper can be disabled by setting [[zoomControlsWrapperOptions]] to "false".
     * @return string the HTML for the zoom controls.
     * @uses [[renderZoomLabel()]]
     * @see zoomControlsWrapperOptions
     */
    protected function renderZoomControls()
    {
        if ($this->zoomSliderOptions === false) {
            return '';
        }

        $html = [];

        // wrapper
        if ($this->zoomControlsWrapperOptions !== false) {
            $zoomControlsWrapperTag = ArrayHelper::remove($this->zoomControlsWrapperOptions, 'tag', 'div');
            $html[] = Html::beginTag($zoomControlsWrapperTag, $this->zoomControlsWrapperOptions);
        }

        // zoom out label
        $zoomOutLabel = $this->renderZoomLabel($this->zoomOutLabelOptions);
        if ($zoomOutLabel !== false) {
            $html[] = $zoomOutLabel;
        }

        // the actual slider
        $html[] = Html::input('range', null, null, $this->zoomSliderOptions);

        // zoom in label
        $zoomInLabel = $this->renderZoomLabel($this->zoomInLabelOptions);
        if ($zoomInLabel !== false) {
            $html[] = $zoomInLabel;
        }

        // end wrapper
        if ($this->zoomControlsWrapperOptions !== false) {
            $html[] = Html::endTag($zoomControlsWrapperTag);
        }

        return implode("\n", $html);
    }

    /**
     * Renders a single zoom label tag.
     * @param array|bool $labelOptions the options for the label. If set to "false", no label will be rendered.
     * @return string the markup for the label tag.
     * @see zoomOutLabelOptions
     * @see zoomInLabelOptions
     */
    protected function renderZoomLabel($labelOptions)
    {
        if ($labelOptions === false) {
            return '';
        }

        $zoomInLabelTag = ArrayHelper::remove($labelOptions, 'tag', 'span');
        $zoomInLabelLabel = ArrayHelper::remove($labelOptions, 'label');
        $encodeZoomInLabelLabel = ArrayHelper::remove($labelOptions, 'encodeLabel', true);
        return Html::tag($zoomInLabelTag, $encodeZoomInLabelLabel ? Html::encode($zoomInLabelLabel) : $zoomInLabelLabel, $labelOptions);
    }

    /**
     * Initializes the control options.
     * @see selectImageButtonOptions
     * @see cropImageButtonOptions
     * @see zoomSliderOptions
     * @see zoomOutLabelOptions
     * @see zoomInLabelOptions
     */
    protected function initControls()
    {
        // select image button
        Html::addCssClass($this->selectImageButtonOptions, ['select-image-btn' => "{$this->containerOptions['id']}_select-image-btn"]);
        if (!isset($this->selectImageButtonOptions['label'])) {
            $this->selectImageButtonOptions['label'] = 'Select image';
        }

        // crop image button
        Html::addCssClass($this->cropImageButtonOptions, ['select-image-btn' => "{$this->containerOptions['id']}_crop-image-btn"]);
        if (!isset($this->cropImageButtonOptions['label'])) {
            $this->cropImageButtonOptions['label'] = 'Crop image';
        }

        // zoom slider
        if ($this->zoomSliderOptions !== false) {
            Html::addCssClass($this->zoomSliderOptions, ['zoom-slider' => "cropit-image-zoom-input {$this->containerOptions['id']}_zoom-slider"]);
        }

        // zoom out label
        if ($this->zoomOutLabelOptions !== false) {
            if (!isset($this->zoomOutLabelOptions['label'])) {
                $this->zoomOutLabelOptions['label'] = '-';
            }
            Html::addCssClass($this->zoomOutLabelOptions, ['zoom-out-label' => "control-zoom-out"]);
        }

        // zoom in label
        if ($this->zoomInLabelOptions !== false) {
            if (!isset($this->zoomInLabelOptions['label'])) {
                $this->zoomInLabelOptions['label'] = '+';
            }
            Html::addCssClass($this->zoomInLabelOptions, ['zoom-in-label' => "control-zoom-in"]);
        }

        // rotate buttons
        if ($this->showRotateControls === true) {
            if (!isset($this->rotateLeftButtonOptions['label'])) {
                $this->rotateLeftButtonOptions['label'] = 'CCW';
            }
            Html::addCssClass($this->rotateLeftButtonOptions, ['rotate-left-button' => "control-rotate-left {$this->containerOptions['id']}_rotate-left"]);

            if (!isset($this->rotateRightButtonOptions['label'])) {
                $this->rotateRightButtonOptions['label'] = 'CW';
            }
            Html::addCssClass($this->rotateRightButtonOptions, ['rotate-right-button' => "control-rotate-right {$this->containerOptions['id']}_rotate-right"]);
        }
    }
}
