<?php

namespace DS\Feedback\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Subjects extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'ds.feedback.access_subjects'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('DS.Feedback', 'feedback_messages', 'feedback_subjects');
    }
}
