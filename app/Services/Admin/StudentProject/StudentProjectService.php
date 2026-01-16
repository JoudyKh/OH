<?php

namespace App\Services\Admin\StudentProject;
use App\Constants\Constants;
use App\Constants\Notifications;
use App\Exports\StudentProjectExport;
use App\Http\Requests\Api\Admin\StudentProject\UpdateStuProStatusRequest;
use App\Http\Resources\StudentProjectResource;
use App\Models\StudentProject;
use App\Models\StudentProjectFile;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Auth;
class StudentProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    use SearchTrait;
    public function index(Request $request)
    {
        $projects = StudentProject::where('status', $request->status)->with('files')->orderByDesc($request->trash ? 'deleted_at' : 'created_at');
        $this->applySearchAndSort($projects, $request, StudentProject::$searchable);
        $projects = $projects->paginate(config('app.pagination_limit'));
        return StudentProjectResource::collection($projects);
    }
    public function update(UpdateStuProStatusRequest $request, StudentProject $project)
    {
        $data = $request->validated();
        if ($data['status'] === Constants::PROJECT_STATUSES[0]) {
            $data['mark'] = null;
        }
        $project->update($data);
        $student = $project->student;
        pushNotification("تم تعديل حالة المشروع", "", Notifications::PROJECT_UPDATED['TYPE'], Notifications::PROJECT_UPDATED['STATE'], $student, class_basename($project), $project->id);
        return StudentProjectResource::make($project->load('files'));
    }
    public function destroy($project, $force = null)
    {
        if ($force) {
            authorize('force_delete_student_project');

            $project = StudentProject::onlyTrashed()->findOrFail($project);

            foreach ($project->files as $file) {
                if (Storage::disk('public')->exists($file->file)) {
                    Storage::disk('public')->delete($file->file);
                }
            }
            $project->forceDelete();
        } else {
            authorize(['delete_student_project', 'force_delete_student_project']);

            $project = StudentProject::where('id', $project)->first();
            $project->delete();
        }
        return true;
    }
    public function restore($project)
    {
        $project = StudentProject::withTrashed()->find($project);
        if ($project && $project->trashed()) {
            $project->restore();
            return true;
        }
        throw new \Exception(__('messages.not_found'), 404);
    }
    public function exportPdf(Request $request)
    {
        $studentProjects = (new StudentProjectExport($request))->collection();

        $mpdf = new Mpdf([
            'default_font' => 'DejaVuSans',  // Ensure the font supports Arabic
            'format' => 'A4',                // A4 paper size
            'mode' => 'utf-8',               // Ensure proper UTF-8 handling
            'directionality' => 'rtl',
        ]);

        $tableStyle = "
        <style>
            table {
                width: 100%;  /* Table covers full page width */
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                padding: 10px;
                border: 1px solid #000;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;  /* Light gray background for table headers */
            }
        </style>
    ";

        $mpdf->WriteHTML($tableStyle);

        $mpdf->WriteHTML('<table>');

        $headings = (new StudentProjectExport($request))->headings();
        $mpdf->WriteHTML('<thead><tr>');
        foreach ($headings as $heading) {
            $mpdf->WriteHTML("<th>{$heading}</th>");
        }
        $mpdf->WriteHTML('</tr></thead>');

        $mpdf->WriteHTML('<tbody>');
        foreach ($studentProjects as $studentProject) {
            $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td>' . ($studentProject->note ?? '') . '</td>');
            $mpdf->WriteHTML('<td>' . $studentProject->mark . '</td>');
            $mpdf->WriteHTML('<td>' . $studentProject->student->university_number . '</td>');
            $mpdf->WriteHTML('<td>' . $studentProject->full_name . '</td>');
            $mpdf->WriteHTML('</tr>');
        }
        $mpdf->WriteHTML('</tbody>');
        $mpdf->WriteHTML('</table>');
        $pdfContent = $mpdf->Output('', 'S');

        // Output the PDF  
        return response()->streamDownload(
            function () use ($pdfContent) {
                echo $pdfContent;
            },
            'Students_projects.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Students_projects.pdf"',
            ]
        );
    }


    public function downloadFile(StudentProjectFile $file)
    {
        $filePath = storage_path('app/public/' . $file->file);
        return response()->download($filePath);
    }
}
