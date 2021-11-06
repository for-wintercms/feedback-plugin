<?php

namespace DS\Feedback\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDsFeedbackStatus extends Migration
{
    public function up()
    {
        Schema::create('ds_feedback_status', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('slug', 128)->unique();
            $table->string('name', 128);
            $table->string('color', 7);
            $table->boolean('is_hide_message')->default(0);
            $table->boolean('is_default_status')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ds_feedback_status');
    }
}
