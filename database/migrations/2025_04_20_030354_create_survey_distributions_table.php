<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyDistributionsTable extends Migration
{
    public function up()
    {
        Schema::create('survey_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade'); // Link to Surveys
            $table->string('target_role'); // 'student' or 'alumni'
            $table->enum('date_field', ['enrol_date', 'graduate_date']); // Which date field to filter
            $table->date('start_date'); // Start of range
            $table->date('end_date');   // End of range
            $table->date('scheduled_active_date'); // When system will start distribution
            $table->boolean('is_active')->default(false); // Whether survey distribution is active yet
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_distributions');
    }
}
