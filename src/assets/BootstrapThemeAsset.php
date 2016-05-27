<?php

namespace macgyer\yii2cropit\assets;

use yii\web\AssetBundle;

/**
 * BootstrapThemeAsset provides the required cropit source files.
 * @package yii2cropit
 */
class BootstrapThemeAsset extends AssetBundle
{
    /**
     * @var string the directory that contains the source asset files for this asset bundle.
     */
    public $sourcePath = '@bower/cropit/dist/';

    /**
     * @var array list of JS files that this bundle contains.
     */
    public $js = [
        'jquery.cropit.js',
    ];

    /**
     * @var array list of bundle class names that this bundle depends on.
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
