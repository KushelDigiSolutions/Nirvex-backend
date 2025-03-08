<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'Welcome Notification',
                'Order Confirmation',
                'Payment Confirmation',
                'Order Processing',
                'Order Out for Delivery',
                'Delivery Notification',
                'Order Cancellation',
                'Vendor Account Approval',
                'New Order Received',
                'Order Pickup Scheduled',
                'Order Delivered Confirmation',
                'Low Stock Alert'
            ]);
            $table->text('message');
            $table->json('additional_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
