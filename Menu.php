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

    protected $_renderItems = false;

    protected function normalizeItems($items, &$active)
    {
        foreach($items as $k => $v)
        {
            if (array_key_exists('label', $items[$k]) && is_array($items[$k]['label']))
            {
                $items[$k]['label'] = call_user_func_array('Yii::t', $items[$k]['label']);
            }
        }

        return parent::normalizeItems($items, $active);
    }

    protected function setItemSubmenuLinkTemplate($item)
    {
        if (array_key_exists('items', $item))
        {
            if ($this->submenuLinkTemplate)
            {
                $item['template'] = ArrayHelper::getValue($item, 'template', $this->submenuLinkTemplate);
            }            
        }

        return $item;   
    }    

    protected function setItemId($item)
    {
        $id = static::$autoIdPrefix . static::$counter++;

        if (array_key_exists('items', $item))
        {
            $item['submenuTemplate'] = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);

            $item['submenuTemplate'] = str_replace('{id}', $id, $item['submenuTemplate']);

            if (array_key_exists('url', $item))
            {
                $item['template'] = ArrayHelper::getValue($item, 'template', $this->linkTemplate);
            }
            else
            {
                $item['template'] = ArrayHelper::getValue($item, 'template', $this->labelTemplate);
            }            
        }

        if (array_key_exists('template', $item))
        {
            $item['template'] = str_replace('{id}', $id, $item['template']);
        }

        return $item;
    }

    protected function setItemIcon($item)
    {
        $icon = ArrayHelper::remove($item, 'icon');

        if ($icon)
        {
            $label = ArrayHelper::getValue($item, 'label');

            $item['label'] = strtr($this->iconTemplate, ['{label}' => $label, '{icon}' => $icon]);
        }

        return $item;
    }

    protected function setItemLink($item)
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

    protected function renderItem($item)
    {
        if (array_key_exists('content', $item) && ($item['content'] !== null))
        {
            return $item['content'];
        }

        return parent::renderItem($item);
    }

    protected function beforeRenderItem($item)
    {
        $item = $this->setItemIcon($item);

        $item = $this->setItemLink($item);

        $item = $this->setItemSubmenuLinkTemplate($item);

        $item = $this->setItemId($item);

        return $item;
    }

    protected function renderItems($items)
    {
        if ($this->_renderItems && $this->submenuClass)
        {
            return $this->renderSubmenu($items);
        }

        foreach($items as $key => $value)
        {
            $items[$key] = $this->beforeRenderItem($items[$key]);
        }

        $this->_renderItems = true;

        $return = parent::renderItems($items);

        $this->_renderItems = false; 

        return $return;
    }

    protected function renderSubmenu($items)
    {
        $class = $this->submenuClass;

        return $class::widget(
            array_merge(
                $this->submenuOptions,
                [
                    'items' => $items
                ]
            )
        );
    }

}