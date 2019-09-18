<?php

namespace denis909\yii;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Menu extends \yii\widgets\Menu
{

    public $linkOptions = [];

    public $iconTemplate = '{label} <i class="{icon}"></i>';

    public $submenuItem = [];

    protected function renderItem($item)
    {
        if (array_key_exists('content', $item) && ($item['content'] !== null))
        {
            return $item['content'];
        }

        if (isset($item['url']))
        {
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

    protected function renderItems($items)
    {
        foreach($items as $key => $item)
        {
            if (!empty($item['items']))
            {
                foreach($item['items'] as $k => $v)
                {
                    $items[$key]['items'][$k] = array_merge($this->submenuItem, $v);
                }

                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);

                if (strpos($submenuTemplate, '{id}') !== false)
                {
                    $id = $this->getId();

                    $submenuTemplate = str_replace('{id}', $id, $submenuTemplate);
                
                    $items[$key]['submenuTemplate'] = $submenuTemplate;
                
                    $template = ArrayHelper::getValue($item, 'template');

                    if (isset($item['url']))
                    {
                        $items[$key]['template'] = str_replace('{id}', $id, $this->linkTemplate);
                    }
                    else
                    {
                        $items[$key]['template'] = str_replace('{id}', $id, $this->labelTemplate);
                    }
                }
            }
        }

        return parent::renderItems($items);
    }    

}