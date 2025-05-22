<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatesToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('enroll_date')->nullable()->after('role');
            $table->date('expected_graduate_date')->nullable()->after('enroll_date');
            $table->date('actual_graduate_date')->nullable()->after('expected_graduate_date');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('enroll_date');
            $table->dropColumn('expected_graduate_date');
            $table->dropColumn('actual_graduate_date');
        });
    }
}
