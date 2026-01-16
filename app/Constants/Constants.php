<?php

namespace App\Constants;

class Constants
{
    const SUPER_ADMIN_ROLE = 'superadmin';
    const ADMIN_ROLE = 'admin';
    const CONTENT_MANAGER_ROLE = 'content_manager';
    const PROJECT_MANAGER_ROLE = 'project_manager';
    const USER_ROLE = 'user';
    const STUDENT_ROLE = 'student';
    const MALE_GENDER = 'MALE';
    const FEMALE_GENDER = 'FEMALE';
    
    const PARENTS = [
        'super_section',
        'super_library_section',
    ];
    const CHILDREN_OF = [
        'super_section' => ['sub_section'],
        'super_library_section' => ['sub_library_section'],
        'sub_section',
        'sub_library_section',
    ];
    const SECTIONS_TYPES = [
        'super_section' => [
            'attributes' => [
                'name',
                'image',
            ],
            'rules' => [
                'create' => [
                    'name' => 'required',
                    'image' => 'required|mimes:gif,webp,jpeg,png,jpg',
                ],
                'update' => [
                    'name' => 'nullable',
                    'image' => 'mimes:gif,webp,jpeg,png,jpg',
                ],
            ],
        ],
        'sub_section' => [
            'attributes' => [
                'name',
                'image',
            ],
            'rules' => [
                'create' => [
                    'name' => 'required',
                ],
                'update' => [
                    'name' => 'nullable',
                ],
            ],
        ],
        'library_section' => [
            'attributes' => [
                'name',
                'image',
            ],
            'rules' => [
                'create' => [
                    'name' => 'required',
                    'image' => 'required|mimes:gif,webp,jpeg,png,jpg,gif',
                ],
                'update' => [
                    'name' => 'nullable',
                    'image' => 'mimes:gif,webp,jpeg,png,jpg,gif',
                ],
            ],
        ],
        'super_library_section' => [
            'attributes' => [
                'name',
                'image',
                'description'
            ],
            'rules' => [
                'create' => [
                    'name' => 'required',
                    'image' => 'required|mimes:gif,webp,jpeg,png,jpg,gif',
                    'description' => 'required|string'
                ],
                'update' => [
                    'name' => 'nullable',
                    'image' => 'mimes:gif,webp,jpeg,png,jpg,gif',
                    'description' => 'sometimes|string',
                ],
            ],
        ],
        'sub_library_section' => [
            'attributes' => [
                'name',
                'image',
                'is_special',
            ],
            'rules' => [
                'create' => [
                    'name' => 'required',
                    'image' => 'required|mimes:gif,webp,jpeg,png,jpg,gif',
                    'is_special' => 'required|boolean',
                ],
                'update' => [
                    'name' => 'nullable',
                    'image' => 'mimes:gif,webp,jpeg,png,jpg,gif',
                    'is_special' => 'boolean',
                ],
            ],
        ],

    ];
    const PROJECT_STATUSES = [
        'pending',
        'completed',
    ];

    // const Interview requests
    const INTERVIEW_REQUESTS_TYPES = [
        'electronic_certificate',
        'cartoon_certificate',
        'participation',
    ];
    const AVAILABLE_INTERVIEW_REQUESTS_TYPES = [
        'electronic_certificate',
        'cartoon_certificate',
        'both',
    ];
    const SUB_SECTION_TYPES = [
        'lecture' => 0,
        'project' => 1
    ];
    const HOME_VIEW = 'home_view';
    const LIBRARIES_VIEW = 'libraries_view';
    const SUPER_SECTIONS_VIEW = 'super_sections_view';
    const LIBRARY_SECTIONS_VIEW = 'library_sections_view';
    const ONE_SECTION_VIEW = 'one_section_view';//
    const INTERVIEWS_VIEW = 'interviews_view';
    const ONE_INTERVIEW_VIEW = 'one_interview_view';
    const LECTURES_VIEW = 'lectures_view';
    const LECTURES_SECTIONS_VIEW = 'lectures_sections_view';
    const PROJECTS_VIEW = 'projects_view';
    const PROJECTS_SECTIONS_VIEW = 'projects_sections_view';
    const ONE_LECTURE_VIEW = 'one_lecture_view';
    const ONE_PROJECT_VIEW = 'one_project_view';
    const TERMS_VIEW = 'terms_view';
    const PRIVACY_VIEW = 'privacy_view';
    const CONTACT_US_VIEW = 'contact_us_view';
    const ONE_LIBRARY_VIEW = 'one_library_view';
    const SUPER_LIBRARY_SECTION_VIEW = 'super_library_section_view';
    const SUB_LIBRARY_FILES_VIEW = 'sub_library_files_view';

    const INTERVIEW_TYPES = [
        'digital' => [
            'ar' => 'رقمي',
            'en' => 'digital',
        ],
        'physical' => [
            'ar' => 'مكاني',
            'en' => 'physical'
        ],
    ];
}
