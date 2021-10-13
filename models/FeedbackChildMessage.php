<?php

namespace DS\Feedback\Models;

use Model;

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

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
