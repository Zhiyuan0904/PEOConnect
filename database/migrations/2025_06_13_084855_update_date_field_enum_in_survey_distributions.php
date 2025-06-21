<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateDateFieldEnumInSurveyDistributions extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE survey_distributions MODIFY date_field ENUM('enroll_date', 'expected_graduate_date') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE survey_distributions MODIFY date_field ENUM('enroll_date', 'graduate_date') NOT NULL");
    }
}

