<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\User;

class NotificationService
{
    /**
     * Send an in-app notification to a user.
     */
    public function send(User $user, string $type, string $title, string $message, string $channel = 'in_app'): NotificationModel
    {
        return NotificationModel::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'channel' => $channel,
            'is_read' => false,
        ]);
    }

    public function notifyDepartmentApproved(User $student, string $departmentName): NotificationModel
    {
        return $this->send(
            $student,
            'clearance_approved',
            'Department Cleared',
            "{$departmentName} has approved your clearance request."
        );
    }

    public function notifyDepartmentRejected(User $student, string $departmentName, ?string $remarks = null): NotificationModel
    {
        $message = "{$departmentName} has rejected your clearance request.";
        if ($remarks) {
            $message .= " Reason: {$remarks}";
        }

        return $this->send($student, 'clearance_rejected', 'Clearance Issue', $message);
    }

    public function notifyCertificateReady(User $student): NotificationModel
    {
        return $this->send(
            $student,
            'certificate_ready',
            'Certificate Ready',
            'Congratulations! You have been fully cleared and your certificate is ready to download.'
        );
    }
}