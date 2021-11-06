<?php

namespace DS\Feedback\Models;

use Lang;
use Model;
use Backend\Facades\BackendAuth;

/**
 * FeedbackMessage Model
 */
class FeedbackMessage extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ds_feedback_messages';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'email'     => 'required|email|between:1,128',
        'name'      => 'required|between:1,128',
        'message'   => 'required|between:1,5000',
        'status_id' => 'required|exists:ds_feedback_status,id',
    ];

    protected $fillable = [
        'email',
        'name',
        'message',
        'status_id',
        'category_id',
        'subject_id',
        'another_subject',
    ];

    public $belongsTo = [
        'status'   => [FeedbackStatus::class, 'key' => 'status_id'],
        'subject'  => [FeedbackSubject::class, 'key' => 'subject_id'],
        'category' => [FeedbackCategory::class, 'key' => 'category_id'],
    ];

    public $hasMany = [
        'child_messages' => [FeedbackChildMessage::class, 'key' => 'parent_message_id'],
    ];

    public function getCategoryOptions($keyValue = null)
    {
        return FeedbackCategory::pluck('name', 'id')->prepend(Lang::get('ds.feedback::lang.feedback.category.uncategorized'), 0);
    }

    public function getSubjectOptions($keyValue = null)
    {
        return FeedbackSubject::pluck('name', 'id')->prepend(Lang::get('ds.feedback::lang.feedback.subject.custom_subject'), 0);
    }

    public function beforeValidate()
    {
        # Category
        if (empty($this->category_id))
            unset($this->rules['category_id']);
        else
            $this->rules['category_id'] = 'required|exists:ds_feedback_categories,id';

        # Subject
        if (empty($this->subject_id))
        {
            unset($this->rules['subject_id']);
            $this->rules['another_subject'] = 'required|between:1,128';  // required_if rule
        }
        else
        {
            unset($this->rules['another_subject']);
            $this->rules['subject_id'] = 'required|exists:ds_feedback_subjects,id';
        }

        # User id
        if (empty($this->user_id))
            unset($this->rules['user_id']);
        else
            $this->rules['user_id'] = 'integer';
    }

    public function beforeSave()
    {
        if (empty($this->category_id))
            $this->category_id = 0;
        if (empty($this->subject_id))
            $this->subject_id = 0;
        if (empty($this->user_id))
            $this->user_id = null;
        if (BackendAuth::check())
            $this->manager_id = BackendAuth::getUser()->id;
    }
}
