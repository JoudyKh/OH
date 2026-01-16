<?php

namespace App\Observers;

use App\Models\Section;

class SectionObserver
{
    public function deleting(Section $section)
    {
        if (!$section->isForceDeleting()) {
            if ($section->type === 'super_section') {
                $subSections = $section->subSections()->get();

                foreach ($subSections as $subSection) {
                    $subSection->lectures()->delete();
                }
                $section->subSections()->delete();

            } elseif ($section->type === 'library_section')
                $section->libraries()->delete();
            elseif ($section->type === 'sub_section')
                $section->lectures()->delete();
            elseif ($section->type === 'sub_library_section')
                $section->LibraryFiles()->delete();
        } else {
            if ($section->type === 'super_section') {
                $subSections = $section->subSections()->with('lectures')->get();

                foreach ($subSections as $subSection) {
                    $subSection->lectures()->forceDelete();
                }
                $section->subSections()->forceDelete();

            } elseif ($section->type === 'library_section')
                $section->libraries()->forceDelete();
            elseif ($section->type === 'sub_section')
                $section->lectures()->forceDelete();
            elseif ($section->type === 'sub_library_section')
                $section->LibraryFiles()->forceDelete();
        }

    }
    public function restoring(Section $section)
    {
        // Restore related records
        if ($section->type === 'super_section') {
            $subSections = $section->subSections()->with('lectures')->get();

            foreach ($subSections as $subSection) {
                $subSection->lectures()->restore();
            }
            $section->subSections()->restore();

        } elseif ($section->type === 'library_section')
            $section->libraries()->restore();
        elseif ($section->type === 'sub_section')
            $section->lectures()->restore();
        elseif ($section->type === 'sub_library_section')
            $section->LibraryFiles()->restore();
    }
}
