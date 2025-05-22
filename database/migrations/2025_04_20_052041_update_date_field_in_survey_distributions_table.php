<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDateFieldInSurveyDistributionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('survey_distributions', function (Blueprint $table) {
            // 🛠 Drop old column first (if necessary)
            $table->dropColumn('date_field');
        });

        Schema::table('survey_distributions', function (Blueprint $table) {
            // 🛠 Recreate the column with correct ENUM
            $table->enum('date_field', ['enroll_date', 'graduate_date'])->after('target_role');
        });
    }

    public function down(): void
    {
        Schema::table('survey_distributions', function (Blueprint $table) {
            $table->dropColumn('date_field');

            $table->string('date_field')->nullable(); // revert to old string if rollback
        });
    }
}
