<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_ZfcUser
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Popov\ZfcUser\Block\Grid;

use Popov\ZfcDataGrid\Block\AbstractGrid;
use Popov\ZfcUser\Model\User;
use ZfcDatagrid\Action\Mass;

class UserGrid extends AbstractGrid
{
    //protected $createButtonTitle = '';
    protected $backButtonTitle = '';

    protected $id = User::MNEMO;

    public function init()
    {
        $grid = $this->getDataGrid();
        $grid->setId('user');
        $grid->setTitle('Users');
        //$grid->setRendererName('jqGrid');

        $rendererOptions = $grid->getToolbarTemplateVariables();

        //$rendererOptions['gridFooterRow'] = true;
        $rendererOptions['navGridDel'] = true;
        //$rendererOptions['navGridSearch'] = false;
        //$rendererOptions['inlineNavEdit'] = true;
        //$rendererOptions['inlineNavAdd'] = true;
        $rendererOptions['inlineNavCancel'] = true;
        $rendererOptions['navGridRefresh'] = true;

        $grid->setToolbarTemplateVariables($rendererOptions);

        $colId = $this->add([
            'name' => 'Select',
            'construct' => ['id', 'user'],
            'identity' => true,
        ])->getDataGrid()->getColumnByUniqueId('user_id');

        #$massAction = new Mass();
        #$massAction->setTitle('Remove');
        #$grid->addMassAction($massAction);

        $this->add([
            'name' => 'Select',
            'construct' => ['email', 'user'],
            'label' => 'Email',
            'identity' => false,
            'width' => 3,
        ]);

        #$this->add([
        #    'name' => 'Select',
        #    'construct' => ['isInner', 'user'],
        #    'label' => 'Inner User',
        #    'identity' => false,
        #    'width' => 3,
        #    'filter_select_options' => [
        #        0 => ''
        #    ],
        #]);

        $this->add([
            'name' => 'Select',
            'construct' => ['firstName', 'user'],
            'label' => 'First Name',
            'translation_enabled' => true,
            'width' => 3,
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['lastName', 'user'],
            'label' => 'Last Name',
            'translation_enabled' => true,
            'width' => 3,
        ]);

        $this->add([
            'name' => 'Select',
            'construct' => ['createdAt', 'user'],
            'label' => 'Date Create',
            'translation_enabled' => true,
            'width' => 2,
            'type' => ['name' => 'DateTime'],
        ]);

        /*$this->add([
            'name' => 'Select',
            'construct' => ['isInner', 'user'],
            'label' => 'Is Inner',
            'identity' => false,
            'width' => 1,
            'filter_select_options' => [[
                '0' => 'No',
                '1' => 'Yes',
            ]],
        ]);*/

        $this->add([
            'name' => 'Action',
            'construct' => ['edit'],
            'label' => ' ',
            'width' => 1,
            'styles' => [[
                'name' => 'BackgroundColor',
                'construct' => [[224, 226, 229]],
            ]],
            'formatters' => [[
                'name' => 'Link',
                'attributes' => ['class' => 'pencil-edit-icon', 'target' => '_blank'],
                'link' => ['href' => '/admin/user/edit/%s', 'placeholder_column' => $colId]
            ]],
        ]);

        return $this;
    }
}