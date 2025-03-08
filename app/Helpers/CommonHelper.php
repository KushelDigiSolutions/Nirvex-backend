<?php

use App\Models\Notification;

if (!function_exists('createUserNotification')) {
    function createUserNotification($userId, $type, $message, $additionalData = null)
    {
        $validTypes = [
            1 => 'Welcome Notification',
            2 => 'Order Confirmation',
            3 => 'Payment Confirmation',
            4 => 'Order Processing',
            5 => 'Order Out for Delivery',
            6 => 'Delivery Notification',
            7 => 'Order Cancellation',
            8 => 'Vendor Account Approval',
            9 => 'New Order Received',
            10 => 'Order Pickup Scheduled',
            11 => 'Order Delivered Confirmation',
            12 => 'Low Stock Alert'
        ];

        if (!isset($validTypes[$type])) {
            throw new InvalidArgumentException("Invalid notification type: $type");
        }

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'additional_data' => $additionalData ? json_encode($additionalData) : null
        ]);
    }
}
