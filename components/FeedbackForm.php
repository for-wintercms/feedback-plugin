<?php

namespace DS\Feedback\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Facades\Lang;
use DS\Feedback\Models\FeedbackCategory;

class FeedbackForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'FeedbackForm',
            'description' => 'Component for processing Feedback forms'
        ];
    }

    public function defineProperties()
    {
        return [
            'category' => [
                'title'             => 'Category',
                'type'              => 'dropdown',
                'default'           => 'feedback',
                'required'          => true,
                'showExternalParam' => false,
            ]
        ];
    }

    public function getCategoryOptions()
    {
        return FeedbackCategory::pluck('name', 'slug')
            ->prepend(Lang::get('ds.feedback::lang.feedback.category.uncategorized'), 0)
            ->toArray();
    }

    public function getSubjects()
    {
        $category = $this->property('category');
        if (empty($category) || ! preg_match("/^[\w]+$/i", $category))
            return [];

        $feedbackCategory = FeedbackCategory::where('slug', $category)->first();
        if (! $feedbackCategory)
            return [];

        $subjects = $feedbackCategory->subjects->pluck('name', 'slug');
        if ($feedbackCategory->is_allow_user_subject)
            $subjects->prepend(Lang::get('ds.feedback::lang.feedback.subject.custom_subject'), 0);

        return $subjects->toArray();
    }

    public function formFieldId()
    {
        return 'field-'.str_random(7);
    }

    public function onSubmit()
    {
        /*$value1 = post('value1');
        $value2 = post('value2');
        $this->page['result'] = $value1 + $value2;*/
    }
}
