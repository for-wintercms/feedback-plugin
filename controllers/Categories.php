<?php

namespace DS\Feedback\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Categories extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'ds.feedback.access_categories'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('DS.Feedback', 'feedback_messages', 'feedback_categories');
    }
}
