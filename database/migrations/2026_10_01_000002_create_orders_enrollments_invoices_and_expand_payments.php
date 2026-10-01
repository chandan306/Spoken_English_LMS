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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('course_name');
            $table->decimal('discount_price', 8, 2)->nullable()->after('price');
        });

        DB::table('courses')->orderBy('id')->get()->each(function ($course) {
            DB::table('courses')->where('id', $course->id)->update([
                'slug' => Str::slug($course->course_name).'-'.$course->id,
            ]);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->string('order_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->unique()->after('id');
            $table->string('payment_gateway')->default('stripe')->after('order_id');
            $table->string('transaction_id')->nullable()->after('payment_gateway');
            $table->string('payment_order_id')->nullable()->after('transaction_id');
            $table->string('payment_signature')->nullable()->after('payment_order_id');
            $table->json('payment_response')->nullable()->after('payment_signature');
            $table->string('status')->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('email_queued_at')->nullable()->after('paid_at');
            $table->unique('transaction_id');
            $table->unique('payment_order_id');
        });

        DB::table('payments')->orderBy('id')->get()->each(function ($payment) {
            $paymentStatus = strtolower((string) $payment->payment_status);
            $status = match ($paymentStatus) {
                'paid', 'succeeded', 'success' => 'paid',
                'pending', 'unpaid', '' => 'pending',
                default => 'failed',
            };
            if ($status === 'paid' && DB::table('payments')
                ->where('user_id', $payment->user_id)
                ->where('course_id', $payment->course_id)
                ->where('id', '<', $payment->id)
                ->whereIn('payment_status', ['paid', 'succeeded', 'success'])
                ->exists()) {
                $status = 'duplicate';
            }
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $payment->user_id,
                'course_id' => $payment->course_id,
                'order_number' => $payment->order_number,
                'amount' => $payment->amount,
                'currency' => strtoupper((string) $payment->currency),
                'status' => $status,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ]);

            DB::table('payments')->where('id', $payment->id)->update([
                'order_id' => $orderId,
                'payment_gateway' => 'stripe',
                'transaction_id' => $payment->payment_intent,
                'payment_order_id' => $payment->stripe_session_id,
                'status' => $status,
                'paid_at' => $status === 'paid' ? $payment->created_at : null,
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable(false)->change();
            $table->index(['user_id', 'course_id', 'status']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('enrolled_at');
            $table->string('status')->default('active')->index();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->timestamp('invoice_date');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        DB::table('payments')->where('status', 'paid')->orderBy('id')->get()->each(function ($payment) {
            $enrolledAt = $payment->paid_at ?? $payment->created_at;
            DB::table('enrollments')->updateOrInsert(
                ['order_id' => $payment->order_id],
                [
                    'user_id' => $payment->user_id,
                    'course_id' => $payment->course_id,
                    'payment_id' => $payment->id,
                    'enrolled_at' => $enrolledAt,
                    'status' => 'active',
                    'created_at' => $enrolledAt,
                    'updated_at' => $enrolledAt,
                ]
            );
            DB::table('invoices')->updateOrInsert(
                ['order_id' => $payment->order_id],
                [
                    'user_id' => $payment->user_id,
                    'payment_id' => $payment->id,
                    'invoice_number' => 'INV-'.$payment->order_number,
                    'amount' => $payment->amount,
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'total_amount' => $payment->amount,
                    'invoice_date' => $enrolledAt,
                    'created_at' => $enrolledAt,
                    'updated_at' => $enrolledAt,
                ]
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('enrollments');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropIndex(['user_id', 'course_id', 'status']);
            $table->dropUnique(['order_id']);
            $table->dropUnique(['transaction_id']);
            $table->dropUnique(['payment_order_id']);
            $table->dropColumn([
                'order_id', 'payment_gateway', 'transaction_id', 'payment_order_id',
                'payment_signature', 'payment_response', 'status', 'paid_at', 'email_queued_at',
            ]);
        });

        Schema::dropIfExists('orders');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'discount_price']);
        });
    }
};
