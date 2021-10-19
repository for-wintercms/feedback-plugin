<?php

namespace DS\Feedback\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateDsFeedbackCategorySubject extends Migration
{
    public function up()
    {
        Schema::create('ds_feedback_category_subject', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('category_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->primary(['category_id','subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ds_feedback_category_subject');
    }
}
