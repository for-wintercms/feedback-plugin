<?php

namespace DS\Feedback\Models;

use Model;

/**
 * Model
 */
class FeedbackStatus extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \October\Rain\Database\Traits\Sortable;

    protected $dates = ['deleted_at'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'ds_feedback_status';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'slug'              => 'required|alpha_dash|between:1,128|unique:ds_feedback_status',
        'name'              => 'required|between:1,128',
        'color'             => 'required|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/',
        'is_hide_message'   => 'required|boolean',
        'is_default_status' => 'required|boolean',
    ];

    public function afterSave()
    {
        if ($this->is_default_status)
            self::where('id', '!=', $this->id)->update(['is_default_status' => false]);
        elseif (! self::where('is_default_status', true)->value('id'))
        {
            $this->is_default_status = true;
            $this->save();
        }
    }

    public function afterDelete()
    {
        if ($this->is_default_status)
        {
            $status = FeedbackStatus::orderBy('sort_order', 'asc')->first();
            if ($status)
            {
                $status->is_default_status = true;
                $status->save();
            }
        }
    }
}
