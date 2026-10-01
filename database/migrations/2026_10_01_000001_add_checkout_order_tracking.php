<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('order_number')->nullable()->after('id');
            $table->string('open_order_key')->nullable()->after('order_number');
            $table->string('paid_enrollment_key')->nullable()->after('open_order_key');
            $table->string('stripe_session_id')->nullable()->change();
        });

        DB::table('payments')->whereNull('order_number')->orderBy('id')->get()->each(function ($payment) {
            DB::table('payments')->where('id', $payment->id)->update(['order_number' => (string) Str::uuid()]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('order_number')->nullable(false)->change();
        });

        DB::table('payments')
            ->where('payment_status', 'paid')
            ->orderBy('id')
            ->get()
            ->each(function ($payment) {
                $key = $payment->user_id.':'.$payment->course_id;
                if (!DB::table('payments')->where('paid_enrollment_key', $key)->exists()) {
                    DB::table('payments')->where('id', $payment->id)->update(['paid_enrollment_key' => $key]);
                }

                DB::table('course_user')->updateOrInsert(
                    ['user_id' => $payment->user_id, 'course_id' => $payment->course_id],
                    ['created_at' => $payment->created_at, 'updated_at' => $payment->updated_at]
                );
            });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('order_number');
            $table->unique('open_order_key');
            $table->unique('paid_enrollment_key');
        });
    }

    public function down(): void
    {
        DB::table('payments')->whereNull('stripe_session_id')->delete();

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['order_number']);
            $table->dropUnique(['open_order_key']);
            $table->dropUnique(['paid_enrollment_key']);
            $table->dropColumn(['order_number', 'open_order_key', 'paid_enrollment_key']);
            $table->string('stripe_session_id')->nullable(false)->change();
        });
    }
};
