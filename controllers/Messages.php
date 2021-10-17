<?php

namespace DS\Feedback\Controllers;

use DB;
use Lang;
use BackendMenu;
use Backend\Classes\Controller;

use DS\Feedback\Models\FeedbackStatus;
use DS\Feedback\Models\FeedbackMessage;

use Winter\Storm\Exception\AjaxException;
use Winter\Storm\Exception\ApplicationException;

class Messages extends Controller
{
    public $assetPath = 'plugins/ds/feedback/assets';

    protected $guarded = [
        'update',
        'preview',
    ];

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

    /* ======================================================== */

    /**
     * Controller "qna" action used for query and answer dialogue.
     *
     * @param int|null $recordId
     */
    public function qna($recordId = null)
    {
        if (empty($recordId) || ! is_numeric($recordId))
            abort(404);

        try {
            $this->addCss(['less/messages-page.less'], 'DS.Feedback');

            $this->vars['record'] = $record = FeedbackMessage::find($recordId);
            $this->pageTitle = $record->subject->name ?? $record->another_subject;
        }
        catch (\Exception $ex) {
            $this->handleError($ex);
        }
    }

    /* ======================================================== */

    public function listExtendQuery($query)
    {
        $this->queryFilterStatus($query);
    }

    public function listExtendColumns($list)
    {
        # Buttons
        # ---------
        $list->addColumns([
            'buttons' => [
                'list' => Lang::get('ds.feedback::feedback.message_list.columns.button'),
                'type' => 'partial',
                'width' => '100px',
                'cssClass' => 'nolink',
            ]
        ]);
    }

    public function formBeforeSave($model)
    {
        $this->guardedAction();
    }

    /* ======================================================== */

    /**
     * Query filter status
     * @param $query
     */
    protected function queryFilterStatus($query)
    {
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

    /**
     * Guarded action
     *
     * @throws AjaxException
     * @throws ApplicationException
     */
    protected function guardedAction()
    {
        if (in_array($this->action, $this->guarded))
        {
            $ajaxHandler = $this->getAjaxHandler();
            if (empty($ajaxHandler))
                throw new ApplicationException('Access is denied!');
            else
                throw new AjaxException('Access is denied!');
        }
    }
}
