<?php

namespace DS\Feedback\Models;

use Model;
use Backend\Models\User as BackendUser;

/**
 * Model
 */
class FeedbackChildMessage extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ds_feedback_child_messages';

    protected $fillable = [
        'message',
        'parent_message_id',
        'manager_id',
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'message'           => 'required|between:1,5000',
        'parent_message_id' => 'required|exists:ds_feedback_messages,id',
        'manager_id'        => 'exists:backend_users,id',
    ];

    public $belongsTo = [
        'manager' => [BackendUser::class, 'key' => 'manager_id'],
    ];
}
