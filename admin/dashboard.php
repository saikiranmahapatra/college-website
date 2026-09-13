<?php
/**
 * Admin Dashboard for Managing College Data
 * This file provides a simple interface for CRUD operations
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SBPMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            overflow-x: auto;
        }

        .tab-button {
            padding: 15px 30px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1rem;
            color: #666;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
        }

        .tab-button.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-button:hover {
            color: #667eea;
            background: #f8f9fa;
        }

        .content {
            padding: 30px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table thead {
            background: #f8f9fa;
        }

        .data-table th,
        .data-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            font-weight: 600;
            color: #333;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
        }

        @media (max-width: 768px) {
            .form-group input,
            .form-group textarea,
            .form-group select {
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .data-table {
                font-size: 0.9rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Admin Dashboard</h1>
            <p>Sri Bharata Pati Mahavidylay Samantiyapalli</p>
        </div>

        <div class="tabs">
            <button class="tab-button active" onclick="switchTab('faculty')">Faculty</button>
            <button class="tab-button" onclick="switchTab('students')">Students</button>
            <button class="tab-button" onclick="switchTab('courses')">Courses</button>
            <button class="tab-button" onclick="switchTab('holidays')">Holidays</button>
        </div>

        <div class="content">
            <!-- Faculty Tab -->
            <div id="faculty" class="tab-content active">
                <h2>Faculty Management</h2>
                <form id="facultyForm" onsubmit="submitFaculty(event)">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="facultyName" required>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <input type="text" id="facultyPosition" required>
                    </div>
                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" id="facultyQualification">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="facultyEmail" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="facultyPhone">
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" id="facultyDepartment">
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Add Faculty</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFacultyForm()">Reset</button>
                    </div>
                </form>
                <div id="facultyList"></div>
            </div>

            <!-- Students Tab -->
            <div id="students" class="tab-content">
                <h2>Student Management</h2>
                <form id="studentForm" onsubmit="submitStudent(event)">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="studentName" required>
                    </div>
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" id="studentRoll" required>
                    </div>
                    <div class="form-group">
                        <label>Course</label>
                        <input type="text" id="studentCourse" required>
                    </div>
                    <div class="form-group">
                        <label>Semester</label>
                        <input type="number" id="studentSemester">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="studentEmail">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" id="studentPhone">
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Add Student</button>
                        <button type="button" class="btn btn-secondary" onclick="resetStudentForm()">Reset</button>
                    </div>
                </form>
                <div id="studentList"></div>
            </div>

            <!-- Courses Tab -->
            <div id="courses" class="tab-content">
                <h2>Course Management</h2>
                <form id="courseForm" onsubmit="submitCourse(event)">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" id="courseName" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="courseDescription"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Duration</label>
                        <input type="text" id="courseDuration" placeholder="e.g., 4 Years" required>
                    </div>
                    <div class="form-group">
                        <label>Credits</label>
                        <input type="number" id="courseCredits">
                    </div>
                    <div class="form-group">
                        <label>Fee</label>
                        <input type="number" id="courseFee" step="0.01">
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Add Course</button>
                        <button type="button" class="btn btn-secondary" onclick="resetCourseForm()">Reset</button>
                    </div>
                </form>
                <div id="courseList"></div>
            </div>

            <!-- Holidays Tab -->
            <div id="holidays" class="tab-content">
                <h2>Holiday Management</h2>
                <form id="holidayForm" onsubmit="submitHoliday(event)">
                    <div class="form-group">
                        <label>Holiday Name</label>
                        <input type="text" id="holidayName" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" id="holidayDate" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="holidayDescription"></textarea>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Add Holiday</button>
                        <button type="button" class="btn btn-secondary" onclick="resetHolidayForm()">Reset</button>
                    </div>
                </form>
                <div id="holidayList"></div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        function switchTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            const buttons = document.querySelectorAll('.tab-button');

            tabs.forEach(tab => tab.classList.remove('active'));
            buttons.forEach(btn => btn.classList.remove('active'));

            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');

            // Load data for the tab
            if (tabName === 'faculty') loadFacultyList();
            else if (tabName === 'students') loadStudentList();
            else if (tabName === 'courses') loadCourseList();
            else if (tabName === 'holidays') loadHolidayList();
        }

        // Faculty functions
        function submitFaculty(e) {
            e.preventDefault();
            const data = {
                name: document.getElementById('facultyName').value,
                position: document.getElementById('facultyPosition').value,
                qualification: document.getElementById('facultyQualification').value,
                email: document.getElementById('facultyEmail').value,
                phone: document.getElementById('facultyPhone').value,
                department: document.getElementById('facultyDepartment').value
            };

            fetch('../backend/api.php?action=addFaculty', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(d => {
                alert(d.message);
                resetFacultyForm();
                loadFacultyList();
            })
            .catch(e => alert('Error: ' + e));
        }

        function loadFacultyList() {
            fetch('../backend/api.php?action=getFaculty')
                .then(r => r.json())
                .then(d => {
                    let html = '<h3 style="margin-top: 30px;">Faculty List</h3>';
                    if (d.data.length > 0) {
                        html += '<table class="data-table"><thead><tr><th>Name</th><th>Position</th><th>Email</th><th>Department</th><th>Actions</th></tr></thead><tbody>';
                        d.data.forEach(f => {
                            html += `<tr>
                                <td>${f.name}</td>
                                <td>${f.position}</td>
                                <td>${f.email}</td>
                                <td>${f.department}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="deleteFaculty(${f.id})">Delete</button></td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p>No faculty members found.</p>';
                    }
                    document.getElementById('facultyList').innerHTML = html;
                });
        }

        function deleteFaculty(id) {
            if (confirm('Are you sure?')) {
                fetch(`../backend/api.php?action=deleteFaculty&id=${id}`, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(d => {
                        alert(d.message);
                        loadFacultyList();
                    });
            }
        }

        function resetFacultyForm() {
            document.getElementById('facultyForm').reset();
        }

        // Student functions
        function submitStudent(e) {
            e.preventDefault();
            const data = {
                name: document.getElementById('studentName').value,
                roll_number: document.getElementById('studentRoll').value,
                course: document.getElementById('studentCourse').value,
                semester: document.getElementById('studentSemester').value,
                email: document.getElementById('studentEmail').value,
                phone: document.getElementById('studentPhone').value
            };

            fetch('../backend/api.php?action=addStudent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(d => {
                alert(d.message);
                resetStudentForm();
                loadStudentList();
            })
            .catch(e => alert('Error: ' + e));
        }

        function loadStudentList() {
            fetch('../backend/api.php?action=getStudents')
                .then(r => r.json())
                .then(d => {
                    let html = '<h3 style="margin-top: 30px;">Student List</h3>';
                    if (d.data.length > 0) {
                        html += '<table class="data-table"><thead><tr><th>Name</th><th>Roll Number</th><th>Course</th><th>Semester</th><th>Email</th><th>Actions</th></tr></thead><tbody>';
                        d.data.forEach(s => {
                            html += `<tr>
                                <td>${s.name}</td>
                                <td>${s.roll_number}</td>
                                <td>${s.course}</td>
                                <td>${s.semester}</td>
                                <td>${s.email}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="deleteStudent(${s.id})">Delete</button></td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p>No students found.</p>';
                    }
                    document.getElementById('studentList').innerHTML = html;
                });
        }

        function deleteStudent(id) {
            if (confirm('Are you sure?')) {
                fetch(`../backend/api.php?action=deleteStudent&id=${id}`, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(d => {
                        alert(d.message);
                        loadStudentList();
                    });
            }
        }

        function resetStudentForm() {
            document.getElementById('studentForm').reset();
        }

        // Course functions
        function submitCourse(e) {
            e.preventDefault();
            const data = {
                name: document.getElementById('courseName').value,
                description: document.getElementById('courseDescription').value,
                duration: document.getElementById('courseDuration').value,
                credits: document.getElementById('courseCredits').value,
                fee: document.getElementById('courseFee').value
            };

            fetch('../backend/api.php?action=addCourse', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(d => {
                alert(d.message);
                resetCourseForm();
                loadCourseList();
            })
            .catch(e => alert('Error: ' + e));
        }

        function loadCourseList() {
            fetch('../backend/api.php?action=getCourses')
                .then(r => r.json())
                .then(d => {
                    let html = '<h3 style="margin-top: 30px;">Course List</h3>';
                    if (d.data.length > 0) {
                        html += '<table class="data-table"><thead><tr><th>Name</th><th>Duration</th><th>Credits</th><th>Fee</th><th>Actions</th></tr></thead><tbody>';
                        d.data.forEach(c => {
                            html += `<tr>
                                <td>${c.name}</td>
                                <td>${c.duration}</td>
                                <td>${c.credits}</td>
                                <td>₹${c.fee}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="deleteCourse(${c.id})">Delete</button></td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p>No courses found.</p>';
                    }
                    document.getElementById('courseList').innerHTML = html;
                });
        }

        function deleteCourse(id) {
            if (confirm('Are you sure?')) {
                fetch(`../backend/api.php?action=deleteCourse&id=${id}`, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(d => {
                        alert(d.message);
                        loadCourseList();
                    });
            }
        }

        function resetCourseForm() {
            document.getElementById('courseForm').reset();
        }

        // Holiday functions
        function submitHoliday(e) {
            e.preventDefault();
            const data = {
                name: document.getElementById('holidayName').value,
                date: document.getElementById('holidayDate').value,
                description: document.getElementById('holidayDescription').value
            };

            fetch('../backend/api.php?action=addHoliday', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(r => r.json())
            .then(d => {
                alert(d.message);
                resetHolidayForm();
                loadHolidayList();
            })
            .catch(e => alert('Error: ' + e));
        }

        function loadHolidayList() {
            fetch('../backend/api.php?action=getHolidays')
                .then(r => r.json())
                .then(d => {
                    let html = '<h3 style="margin-top: 30px;">Holiday List</h3>';
                    if (d.data.length > 0) {
                        html += '<table class="data-table"><thead><tr><th>Holiday Name</th><th>Date</th><th>Description</th><th>Actions</th></tr></thead><tbody>';
                        d.data.forEach(h => {
                            html += `<tr>
                                <td>${h.name}</td>
                                <td>${h.date}</td>
                                <td>${h.description}</td>
                                <td><button class="btn btn-sm btn-danger" onclick="deleteHoliday(${h.id})">Delete</button></td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                    } else {
                        html += '<p>No holidays found.</p>';
                    }
                    document.getElementById('holidayList').innerHTML = html;
                });
        }

        function deleteHoliday(id) {
            if (confirm('Are you sure?')) {
                fetch(`../backend/api.php?action=deleteHoliday&id=${id}`, { method: 'DELETE' })
                    .then(r => r.json())
                    .then(d => {
                        alert(d.message);
                        loadHolidayList();
                    });
            }
        }

        function resetHolidayForm() {
            document.getElementById('holidayForm').reset();
        }

        // Load faculty list on page load
        window.addEventListener('load', loadFacultyList);
    </script>
</body>
</html>