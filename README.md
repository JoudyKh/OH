OH - Academic Project Hub & Library Management System 🎓📚

OH is a custom-built academic portal designed for university instructors (Professors/Doctors) to manage student interactions, project submissions, and educational resources. This system acts as a bridge between the instructor's academic requirements and the student's deliverables.

🌟 Core Functionalities (Based on Codebase)

1. Project Submission Engine 📤

A dedicated module for students to upload their projects and assignments.

Features include deadline tracking and project categorization by subject.

2. Digital Academic Library 📖

A repository for the Professor to upload lectures, PDF references, and supporting materials.

Organized by subjects and departments for easy student access.

3. Office Hours & Scheduling ⏳

An interactive system for the Doctor to define "Office Hours" availability.

Allows students to reserve time slots for project discussions or academic inquiries.

4. Subject & Department Organization 🏛️

The system is structured around academic subjects, ensuring that projects and library files are correctly associated with the relevant curriculum.

🛠 Technical Architecture

Backend: Laravel 10.x

Database Logic: Relational schema managing Projects, LibraryFiles, Subjects, and Schedules.

File Handling: Secure storage logic for managing student project uploads and instructor's library resources.

Frontend: Responsive Blade templates optimized for an academic environment.

🚀 Local Installation

Clone the develop branch:

git clone -b develop [https://github.com/JoudyKh/OH.git](https://github.com/JoudyKh/OH.git)
cd OH


Backend Setup:

composer install
cp .env.example .env
php artisan key:generate


Database Migration:

Configure your MySQL DB in .env.

php artisan migrate


Frontend & Assets:

npm install && npm run dev


Start:

php artisan serve


📂 Key Code Insights

Project Model: Handles the logic for student uploads and metadata.

Library Model: Manages the instructor's shared educational assets.

Access Control: Differentiates between Student roles (upload/view) and the Doctor role (manage/evaluate).

👩‍💻 Developed By

Joudy Alkhatib

GitHub: @JoudyKh

LinkedIn: Joudy Alkhatib

Facilitating academic excellence through organized digital management.
