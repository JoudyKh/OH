<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;  // Dynamic data to pass to the view
    public $subjectLine;  // Subject line for the email
    public $viewTemplate;  // View template to be used

    /**
     * Create a new message instance.
     *
     * @param mixed $object The object can be GraduationProjectRequest, InterviewRequest, or StudentProject
     */
    public function __construct($object)
    {
        // Assign the object-specific values dynamically
        if ($object instanceof \App\Models\GraduationProjectRequest) {
            $this->data = [
                'studentName' => $object->name,
                'projectSubject' => $object->subject,
                'studentUniversity' => $object->university->name,
            ];
            $this->subjectLine = 'مشروع تخرج جديد';
            $this->viewTemplate = 'emails.admin.new_project';  // Corresponding view template
        } elseif ($object instanceof \App\Models\InterviewRequest) {
            $this->data = [
                'interviewType' => $object->type,
                // 'studentName' => $object->student->name,
            ];
            $this->subjectLine = 'طلب لقاء جديد';
            $this->viewTemplate = 'emails.admin.new_interview';  // Corresponding view template
        } elseif ($object instanceof \App\Models\StudentProject) {
            $this->data = [
                'projectSubject' => $object->subject,
                'studentName' => $object->full_name,
                'studentYear' => $object->year,
                'studentUniversity' => $object->university->name,
            ];
            $this->subjectLine = 'مشروع جديد مرفق';
            $this->viewTemplate = 'emails.admin.new_student_project';  // Corresponding view template
        }
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view($this->viewTemplate)  // Use the dynamically selected view
                    ->with($this->data);  // Pass the relevant data to the view
    }
}
