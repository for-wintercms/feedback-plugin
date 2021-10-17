<?php

namespace DS\Feedback\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDsFeedbackMessages extends Migration
{
    public function up()
    {
        Schema::create('ds_feedback_messages', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('email', 128);
            $table->string('name', 128);
            $table->string('message', 5000);
            $table->integer('status_id');
            $table->integer('category_id')->default(0);
            $table->integer('subject_id')->default(0);
            $table->string('another_subject', 128)->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('manager_id')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ds_feedback_messages');
    }
}
