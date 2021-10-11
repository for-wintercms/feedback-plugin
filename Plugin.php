<?php

namespace DS\Feedback;

use Backend;
use System\Classes\PluginBase;

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
}
