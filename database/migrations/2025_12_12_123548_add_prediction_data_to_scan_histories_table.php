<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scan_histories', function (Blueprint $table) {
            $table->json('prediction_data')->nullable()->after('confidence');
        });
    }

    public function down()
    {
        Schema::table('scan_histories', function (Blueprint $table) {
            $table->dropColumn('prediction_data');
        });
    }
};