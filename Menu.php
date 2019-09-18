<?php

namespace denis909\yii;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Menu extends \yii\widgets\Menu
{

    public $linkOptions = [];

    public $iconTemplate = '{label} <i class="{icon}"></i>';

    protected function renderItem($item)
    {
        if (array_key_exists('content', $item) && ($item['content'] !== null))
        {
            return $item['content'];
        }

        if (!array_key_exists('template', $item) || ($item['template'] === null))
        {
            $options = ArrayHelper::getValue($item, 'linkOptions', []);

            $options = array_merge($this->linkOptions, $options);

            $linkClass = ArrayHelper::remove($item, 'linkClass');

            if ($linkClass)
            {
                Html::addCssClass($options, $linkClass);
            }

            $item['template'] = Html::a('{label}', '{url}', $options);
        }

        $icon = ArrayHelper::remove($item, 'icon');

        if ($icon)
        {
            $label = ArrayHelper::getValue($item, 'label');

            $item['label'] = strtr($this->iconTemplate, [
                '{label}' => $label,
                '{icon}' => $icon
            ]);
        }

        return parent::renderItem($item);
    }

}