<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('course_user')
            ->select('user_id', 'course_id', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id', 'course_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->each(function ($membership) {
                DB::table('course_user')
                    ->where('user_id', $membership->user_id)
                    ->where('course_id', $membership->course_id)
                    ->where('id', '<>', $membership->keep_id)
                    ->delete();
            });

        Schema::table('course_user', function (Blueprint $table) {
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'course_id']);
        });
    }
};
