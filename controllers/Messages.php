<?php

namespace DS\Feedback\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\DB;
use DS\Feedback\Models\FeedbackStatus;

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
        # filter - status
        # ------------------
        $messagesTable = $query->getQuery()->from;
        $statusTable   = (new FeedbackStatus())->getTable();

        if (empty($query->getQuery()->columns))
            $query->addSelect(DB::raw($messagesTable.'.*'));

        $query->leftJoin($statusTable, $messagesTable.'.status_id', '=', 'ds_feedback_status.id')
            ->addSelect(DB::raw($statusTable.'.is_hide_message as is_hide_message'));

        foreach ($query->getQuery()->wheres as &$where)
        {
            if (isset($where['column']) && strripos($where['column'], '.') === false)
                $where['column'] = $messagesTable.'.'.$where['column'];
        }
    }
}
