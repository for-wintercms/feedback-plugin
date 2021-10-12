<?php

namespace DS\Feedback\Models;

use Model;

/**
 * Model
 */
class FeedbackSubject extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ds_feedback_subjects';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'slug'  => 'required|alpha_dash|between:1,128|unique:ds_feedback_subjects',
        'name'  => 'required|between:1,128',
    ];
}
