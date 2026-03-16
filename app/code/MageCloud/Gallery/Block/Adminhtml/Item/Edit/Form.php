<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\Gallery\Block\Adminhtml\Item\Edit;

use Bss\Gallery\Block\Adminhtml\Item\Helper\Image;
use Bss\Gallery\Model\Item;
use Bss\Gallery\Model\Item\Source\Categories;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Class Form
 */
class Form extends Generic
{
    /**
     * @var Categories
     */
    protected $categories;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Categories $categories
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Categories $categories,
        array $data = []
    ) {
        $this->categories = $categories;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('item_form');
        $this->setTitle(__('Item Information'));
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var Item $model */
        $model = $this->_coreRegistry->registry('gallery_item');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => [
                'id'      => 'edit_form',
                'action'  => $this->getData('action'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data',
            ]]
        );

        $form->setHtmlIdPrefix('post_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );
        $fieldset->addType('image', Image::class);
        if ($model->getItemId()) {
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id']);
        }
        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Item Title'), 'title' => __('Item Title'), 'required' => true]
        );

        $fieldset->addField(
            'link',
            'text',
            ['name' => 'link', 'label' => __('Item Link'), 'title' => __('Item Link'), 'required' => false]
        );

        $fieldset->addField(
            'image',
            'image',
            $this->returnConfigImage()
        );
        $fieldset->addField(
            'video',
            'text',
            $this->returnConfigVideo()
        );
        $fieldset->addField(
            'sorting',
            'text',
            [
                'name'     => 'sorting',
                'label'    => __('Sort Order'),
                'title'    => __('Sort Order'),
                'class'    => 'validate-number validate-digits',
                'required' => false,
            ]
        )->getAfterElementHtml();
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'is_active',
                'required' => true,
                'options'  => ['1' => __('Enabled'), '0' => __('Disabled')],
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }
        // Get all the categories that in the database
        $allCategories = $this->categories->toOptionArray();
        $model->setData('category_ids', $this->categories->getCategoryIds());
        $fieldset->addField(
            'category_ids',
            'multiselect',
            [
                'label'    => __('Select Albums'),
                'title'    => __('Select Albums'),
                'required' => false,
                'name'     => 'category_ids[]',
                'values'   => $allCategories,
            ]
        );
        $fieldset->addField(
            'description',
            'editor',
            [
                'name'     => 'description',
                'label'    => __('description'),
                'title'    => __('description'),
                'style'    => 'height:5em',
                'required' => true,
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function returnConfigImage()
    {
        return [
            'name'               => 'image',
            'label'              => __('Image'),
            'title'              => __('Image'),
            'after_element_html' => $this->returnValidateImageJs(),
            'required'           => true,
        ];
    }

    /**
     * @return array
     */
    protected function returnConfigVideo()
    {
        return [
            'name'               => 'video',
            'label'              => __('Video'),
            'title'              => __('Video'),
            'required'           => false,
            'after_element_html' => '<small>Show youtube video when click image</small>',
        ];
    }

    /**
     * @return string
     */
    protected function returnValidateImageJs()
    {
        return '<script type="text/x-magento-init">
        {
            "*": {
            "Bss_Gallery/js/add_validate_image":{}
            }
        }
        </script>';
    }
}