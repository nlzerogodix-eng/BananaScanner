<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add the new columns
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // Migrate existing data
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $fullName = trim($user->name);
                
                // Find the LAST space to split first name and last name
                $lastSpacePos = strrpos($fullName, ' ');
                
                if ($lastSpacePos !== false) {
                    // Everything before the last space is first name
                    $firstName = substr($fullName, 0, $lastSpacePos);
                    // Only the last word is last name
                    $lastName = substr($fullName, $lastSpacePos + 1);
                } else {
                    // Single name - put it all in first_name
                    $firstName = $fullName;
                    $lastName = '';
                }
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]);
            }
        });

        // Make first_name required after data migration
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
        });

        // Drop the old name column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        // Add back the name column
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->after('email');
        });

        // Combine first and last name back
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $fullName = trim($user->first_name . ' ' . $user->last_name);
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'name' => $fullName ?: 'Unknown',
                    ]);
            }
        });

        // Make name required
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });

        // Drop the new columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};