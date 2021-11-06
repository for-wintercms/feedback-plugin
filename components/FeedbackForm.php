<?php

namespace DS\Feedback\Components;

use Mail;
use Lang;
use Flash;
use Event;
use Request;
use Cms\Classes\ComponentBase;

use Validator;
use ValidationException;
use Winter\Storm\Exception\ApplicationException;

use DS\Feedback\Models\FeedbackStatus;
use DS\Feedback\Models\FeedbackSubject;
use DS\Feedback\Models\FeedbackMessage;
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
            ],
            'fieldSubjectDefault' => [
                'title'             => 'Default subject',
                'type'              => 'dropdown',
                'depends'           => ['category'],
                'description'       => Lang::get('ds.feedback::component.feedback_form.subject_group'),
                'showExternalParam' => false,
            ],
            'success_message' => [
                'title'             => 'Success message',
                'type'              => 'string',
                'showExternalParam' => false,
            ],
            'fieldName' => [
                'title'             => 'Field',
                'type'              => 'checkbox',
                'group'             => 'Field - Name',
                'showExternalParam' => false,
            ],
            'fieldNameRequired' => [
                'title'             => 'Required',
                'type'              => 'checkbox',
                'group'             => 'Field - Name',
                'showExternalParam' => false,
            ],
            'fieldSendCopy' => [
                'title'             => 'Field',
                'type'              => 'checkbox',
                'group'             => 'Field - Send a copy to myself',
                'showExternalParam' => false,
            ],
        ];
    }

    public function getCategoryOptions()
    {
        return FeedbackCategory::pluck('name', 'slug')
            ->prepend(Lang::get('ds.feedback::lang.feedback.category.uncategorized'), 0)
            ->toArray();
    }

    public function getFieldSubjectDefaultOptions()
    {
        $category = Request::input('category');
        if (! empty($category))
            return [];

        return FeedbackSubject::pluck('name', 'slug')->toArray();
    }

    public function getSubjects()
    {
        $feedbackCategory = $this->getPropertyCategory();
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
        # get form data
        # ---------------
        $postData   = post();
        $customData = Event::fire('ds.feedback.feedbackForm.submitData', [$this->alias, $this->getProperties()], true);

        if (is_array($customData) && count($customData))
            $postData = array_merge($postData, $customData);

        # validation
        # ------------
        $rules = [
            'email'   => 'required|email|between:1,128',
            'message' => 'required|between:1,5000',
        ];

        // name
        if (empty($postData['name']) && ! empty($postData['email']))
            $postData['name'] = $postData['email'];
        else
            $rules['name'] = 'required|between:1,128';

        // subject
        $feedbackCategory = $this->getPropertyCategory();
        $feedbackSubject  = false;

        if ($feedbackCategory)
        {
            if (empty($postData['subject']))
            {
                if ($feedbackCategory->is_allow_user_subject)
                    $rules = ['user_subject' => 'required|between:1,128'] + $rules;
                else
                {
                    $postData['subject'] = '';
                    $rules = ['subject' => 'required'] + $rules;
                }
            }
            else
            {
                Validator::extend('check_feedback_subject', function($attribute, $value, $parameters) use ($feedbackCategory, &$feedbackSubject)
                {
                    $feedbackSubject = $feedbackCategory->subjects()->where('slug', $value)->first();
                    return $feedbackSubject ? true : false;
                });

                $rules = ['subject' => 'required|alpha_dash|between:1,128|check_feedback_subject'] + $rules;
            }
        }
        else
        {
            $fieldSubjectDefault = $this->property('fieldSubjectDefault');
            if (empty($fieldSubjectDefault))
                throw new ApplicationException(Lang::get('ds.feedback::component.feedback_form.error_subject_config'));

            $feedbackSubject = FeedbackSubject::where('slug', $fieldSubjectDefault)->first();
            if (! $feedbackSubject)
                throw new ApplicationException(Lang::get('ds.feedback::component.feedback_form.error_subject_config'));
        }

        // validate
        $validation = Validator::make($postData, $rules, [
            'subject.check_feedback_subject' => Lang::get('ds.feedback::component.feedback_form.validate_error.subject_field'),
        ]);
        if ($validation->fails())
            throw new ValidationException($validation);

        # save message
        # --------------
        $statusId = FeedbackStatus::where('is_default_status', true)->value('id');
        if (! $statusId)
        {
            $statusId = FeedbackStatus::orderBy('sort_order', 'asc')->value('id');
            if (! $statusId)
                throw new ApplicationException(Lang::get('ds.feedback::component.feedback_form.error_status_config'));
        }

        $feedbackMessage = FeedbackMessage::create([
            'email'           => $postData['email'],
            'name'            => $postData['name'],
            'message'         => $postData['message'],
            'status_id'       => $statusId,
            'category_id'     => $feedbackCategory ? $feedbackCategory->id : 0,
            'subject_id'      => $feedbackSubject ? $feedbackSubject->id : 0,
            'another_subject' => $feedbackSubject ? '' : $postData['user_subject'],
        ]);

        # send copy message
        # -------------------
        if (! empty($postData['send_copy']))
        {
            Mail::send('ds.feedback::mail.feedback-message', ['name' => $feedbackMessage->name, 'messageHtml' => $feedbackMessage->message], function($message) use ($feedbackMessage)
            {
                $message->to(e($feedbackMessage->email), e($feedbackMessage->name));
                $message->subject(e($feedbackMessage->subject->name ?? $feedbackMessage->another_subject));
            });
        }

        # flash success
        # ---------------
        $successMessage = $this->property('success_message');
        if ($successMessage)
            Flash::success($successMessage);
    }

    protected function getPropertyCategory(): ?FeedbackCategory
    {
        $category = $this->property('category');
        if (empty($category) || ! preg_match("/^[\w]+$/i", $category))
            return null;

        return FeedbackCategory::where('slug', $category)->first() ?: null;
    }
}
