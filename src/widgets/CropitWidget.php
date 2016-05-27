<?php

namespace macgyer\yii2cropit\widgets;

use macgyer\yii2cropit\assets\CropitAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use yii\base\Exception;

/**
 * Class CropitWidget
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
     * - tag: string, defaults to "span", the name of the container tag.
     * - label: string, defaults to "+", the label on the button.
     * - encodeLabel: boolean, defaults to "true", whether to encode the label.
     *
     * To disable the rendering of the wrapper tag, set this variable to boolean "false".
     *
     * @see [yii\helpers\BaseHtml::renderTagAttributes()](http://www.yiiframework.com/doc-2.0/yii-helpers-basehtml.html#renderTagAttributes()-detail)
     * for details on how attributes are being rendered.
     */
    public $zoomControlsWrapperOptions = [];

    /**
     * @var boolean
     */
    public $showRotateButtons = false;

    /**
     * @var array
     */
    public $rotateLeftButtonOptions = [];

    /**
     * @var array
     */
    public $rotateRightButtonOptions = [];

    /**
     * @var array|boolean
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
        if ($this->hasModel()) {
            $html[] = Html::activeFileInput($this->model, "{$this->attribute}[original]", ['class' => "cropit-image-input {$this->containerOptions['id']}_image-input"]);
            $html[] = Html::activeHiddenInput($this->model, "{$this->attribute}[crop]", ['class' => "{$this->containerOptions['id']}_crop-image-data"]);
        } else {
            $html[] = Html::fileInput("{$this->name}[original]", null, ['class' => "cropit-image-input {$this->containerOptions['id']}_image-input"]);
            $html[] = Html::hiddenInput("{$this->name}[crop]", null, ['class' => "{$this->containerOptions['id']}_crop-image-data"]);
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

        if ($this->pluginOptions !== false) {
            $options = empty($this->pluginOptions) ? '' : Json::htmlEncode($this->pluginOptions);
            $js = "jQuery('#$id').cropit($options);";

            $js .= "jQuery('.{$id}_select-image-btn').on('click', function() {
                        jQuery('.{$id}_image-input').click();
                    });";

            $js .= "jQuery('.{$id}_crop-image-btn').on('click', function() {
                        var me = $(this);
                        var imageData = jQuery('#$id').cropit('export', {
                            originalSize: true
                        });                       
                        jQuery('.{$id}_crop-image-data').val(imageData);
                    });";

            $view->registerJs($js);
        }
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

        // zoom slider
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
    }
}
