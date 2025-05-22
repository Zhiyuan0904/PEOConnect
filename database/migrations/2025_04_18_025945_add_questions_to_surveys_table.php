<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuestionsToSurveysTable extends Migration
{
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->json('questions')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('questions');
        });
    }
}
