<?php

namespace DS\Feedback;

use Backend;
use System\Classes\PluginBase;
use DS\Feedback\Components\FeedbackForm;

/**
 * Feedback Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Feedback',
            'description' => 'Feedback - user questions and appeals',
            'author'      => 'DS',
            'icon'        => 'icon-commenting'
        ];
    }

    public function registerPermissions()
    {
        $tab = 'Feedback';

        return [
            'ds.feedback.access_categories' => [
                'label' => 'Categories',
                'tab' => $tab,
                'order' => 200,
            ],
            'ds.feedback.access_messages' => [
                'label' => 'Messages',
                'tab' => $tab,
                'order' => 200,
            ],
            'ds.feedback.access_status' => [
                'label' => 'Status',
                'tab' => $tab,
                'order' => 200,
            ],
            'ds.feedback.access_subjects' => [
                'label' => 'Subjects',
                'tab' => $tab,
                'order' => 200,
            ],
        ];
    }

    public function registerNavigation()
    {
        return [
            'feedback_messages' => [
                'label'       => 'Feedback',
                'url'         => Backend::url('ds/feedback/messages'),
                'icon'        => 'icon-support',
                'permissions' => ['ds.feedback.*'],
                'order'       => 500,
                'sideMenu' => [
                    'feedback_messages' => [
                        'label' => 'Messages',
                        'url' => Backend::url('ds/feedback/messages'),
                        'icon' => 'icon-comments-o',
                        'permissions' => ['ds.feedback.access_messages'],
                    ],
                    'feedback_categories' => [
                        'label' => 'Categories',
                        'url' => Backend::url('ds/feedback/categories'),
                        'icon' => 'icon-folder-o',
                        'permissions' => ['ds.feedback.access_categories'],
                    ],
                    'feedback_subjects' => [
                        'label' => 'Subjects',
                        'url' => Backend::url('ds/feedback/subjects'),
                        'icon' => 'icon-filter',
                        'permissions' => ['ds.feedback.access_subjects'],
                    ],
                    'feedback_status' => [
                        'label' => 'Status',
                        'url' => Backend::url('ds/feedback/status'),
                        'icon' => 'icon-flag-o',
                        'permissions' => ['ds.feedback.access_status'],
                    ],
                ]
            ]
        ];
    }

    public function registerMailTemplates()
    {
        return [
            'ds.feedback::mail.feedback-message'
        ];
    }

    public function registerComponents()
    {
        return [
            FeedbackForm::class => 'feedbackForm'
        ];
    }
}
