<?php

namespace denis909\yii;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Menu extends \yii\widgets\Menu
{

    public $linkOptions = [];

    public $iconTemplate = '{label} <i class="{icon}"></i>';

    public $submenuOptions = [];

    public $submenuClass;

    protected function normalizeItems($items, &$active)
    {
        foreach($items as $k => $v)
        {
            if (array_key_exists('label', $v) && is_array($v['label']))
            {
                $items[$k]['label'] = call_user_func_array('Yii::t', $v['label']);
            }
        }

        return parent::normalizeItems($items, $active);
    }

    protected function renderItem($item)
    {
        if (array_key_exists('content', $item) && ($item['content'] !== null))
        {
            return $item['content'];
        }

        $submenuItems = ArrayHelper::remove($item, '_items');

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

                $item['template'] = Html::a($this->labelTemplate, '{url}', $options);
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

        $return = parent::renderItem($item);

        if ($submenuItems)
        {
            $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
          
            $return .= strtr($submenuTemplate, [
                '{items}' => $this->renderSubmenu($submenuItems)
            ]);
        }

        return $return;
    }

    protected function renderItems($items)
    {
        foreach($items as $key => $item)
        {
            if ($this->submenuClass)
            {
                $submenuItems = ArrayHelper::remove($items[$key], 'items');

                if ($submenuItems)
                {
                    $items[$key]['_items'] = $submenuItems;

                    $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);

                    if (strpos($submenuTemplate, '{id}') !== false)
                    {
                        $id = static::$autoIdPrefix . static::$counter++;

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
        }

        return parent::renderItems($items);
    }    

    protected function renderSubmenu($items)
    {
        $class = $this->submenuClass;

        $options = $this->submenuOptions;

        $options['items'] = $items;

        return $class::widget($options);
    }    

}