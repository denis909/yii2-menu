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

    public $submenuLinkTemplate;

    protected $_itemInProgress = false;

    protected function normalizeItemLabel($item)
    {
        if (array_key_exists('label', $item) && is_array($item['label']))
        {
            $item['label'] = call_user_func_array('Yii::t', $item['label']);
        }

        return $item;
    }

    protected function normalizeItemSubmenu($item)
    {
        if (array_key_exists('items', $item))
        {
            $item['submenuTemplate'] = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);

            if ($this->submenuLinkTemplate)
            {
                $item['template'] = ArrayHelper::getValue($item, 'template', $this->submenuLinkTemplate);
            }
            else
            {
                if (isset($item['url']))
                {
                    $item['template'] = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
                }
                else
                {
                    $item['template'] = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
                }
            }
        
            if ((strpos($item['submenuTemplate'], '{id}') !== false) || (strpos($item['template'], '{id}') !== false))
            {
                $id = static::$autoIdPrefix . static::$counter++;

                $item['submenuTemplate'] = str_replace('{id}', $id, $item['submenuTemplate']);

                $item['template'] = str_replace('{id}', $id, $item['template']);
            }
        }

        return $item;
    }

    protected function normalizeItemIcon($item)
    {
        $icon = ArrayHelper::remove($item, 'icon');

        if ($icon)
        {
            $label = ArrayHelper::getValue($item, 'label');

            $item['label'] = strtr($this->iconTemplate, ['{label}' => $label, '{icon}' => $icon]);
        }

        return $item;
    }

    protected function normalizeItemLink($item)
    {
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

        return $item;
    }

    protected function normalizeItems($items, &$active)
    {
        foreach($items as $k => $v)
        {
            $items[$k] = $this->normalizeItemLabel($items[$k]);

            $items[$k] = $this->normalizeItemIcon($items[$k]);

            $items[$k] = $this->normalizeItemLink($items[$k]);

            $items[$k] = $this->normalizeItemSubmenu($items[$k]);
        }

        return parent::normalizeItems($items, $active);
    }    

    protected function renderItem($item)
    {
        $this->_itemInProgress = true;

        if (array_key_exists('content', $item) && ($item['content'] !== null))
        {
            return $item['content'];
        }

        $return = parent::renderItem($item);
   
        $this->_itemInProgress = false;

        return $return;
    }

    protected function renderItems($items)
    {
        if ($this->submenuClass)
        {
            $this->submenuTemplate = '{items}';

            if ($this->_itemInProgress)
            {
                $class = $this->submenuClass;

                return $class::widget(array_merge(
                    $this->submenuOptions,
                    [
                        'items' => $items
                    ]
                ));
            }
        }

        return parent::renderItems($items);
    }

}