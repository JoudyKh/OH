<?php

namespace App\Exports;

use App\Models\StudentProject;
use App\Traits\SearchTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentProjectExport implements FromCollection, WithHeadings, WithMapping
{
    use SearchTrait;
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $studentsProjects = StudentProject::where('status', 'completed');

        $this->applySearchAndSort($studentsProjects, $this->request, StudentProject::$searchable);

        return $studentsProjects->get(); // Make sure to fetch the records
    }
    public function map($studentProject): array
    {
        return [
            $studentProject->note,                          // note
            $studentProject->mark,                          // mark
            $studentProject->student->university_number,        // university_number
            $studentProject->full_name,                 // student_name
        ];
    }
    public function headings(): array
    {
        return [
            'ملاحظة',
            'العلامة',
            'الرقم الامتحاني',
            'الاسم',
        ];
    }
}
