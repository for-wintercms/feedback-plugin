<?php

namespace DS\Feedback\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Messages extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'ds.feedback.access_messages'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('DS.Feedback', 'feedback_messages', 'feedback_messages');
    }

    public function listExtendQuery($query)
    {
        $query->leftJoin('ds_feedback_status', 'ds_feedback_messages.status_id', '=', 'ds_feedback_status.id')
            ->addSelect('ds_feedback_status.is_hide_message as is_hide_message');
    }
}
