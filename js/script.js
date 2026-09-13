// Hamburger Menu Toggle
const hamburger = document.querySelector('.hamburger');
const sideNavbar = document.querySelector('.side-navbar');
const closeBtn = document.querySelector('.close-btn');
const sideNavLinks = document.querySelectorAll('.side-nav-links a');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    sideNavbar.classList.toggle('active');
});

closeBtn.addEventListener('click', () => {
    hamburger.classList.remove('active');
    sideNavbar.classList.remove('active');
});

sideNavLinks.forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        sideNavbar.classList.remove('active');
    });
});

// Smooth Scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Load data from PHP backend
function loadData() {
    loadFaculty();
    loadStudents();
    loadCourses();
    loadHolidays();
}

// Load Faculty Data
function loadFaculty() {
    fetch('backend/api.php?action=getFaculty')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('facultyContainer');
            container.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach((faculty, index) => {
                    const card = document.createElement('div');
                    card.className = 'faculty-card';
                    card.style.animationDelay = (index * 0.1) + 's';
                    card.innerHTML = `
                        <div class="faculty-image">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="faculty-info">
                            <div class="faculty-name">${faculty.name}</div>
                            <div class="faculty-position">${faculty.position}</div>
                            <div class="faculty-qualification"><strong>Qualification:</strong> ${faculty.qualification}</div>
                            <div class="faculty-email"><strong>Email:</strong> ${faculty.email}</div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = '<p style="text-align: center; color: #666;">No faculty data available.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading faculty:', error);
            document.getElementById('facultyContainer').innerHTML = '<p style="color: red;">Error loading faculty data</p>';
        });
}

// Load Students Data
function loadStudents() {
    fetch('backend/api.php?action=getStudents')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('studentsContainer');
            container.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach((student, index) => {
                    const card = document.createElement('div');
                    card.className = 'student-card';
                    card.style.animationDelay = (index * 0.1) + 's';
                    card.innerHTML = `
                        <div class="student-name">${student.name}</div>
                        <div class="student-info"><strong>Roll Number:</strong> ${student.roll_number}</div>
                        <div class="student-info"><strong>Course:</strong> ${student.course}</div>
                        <div class="student-info"><strong>Semester:</strong> ${student.semester}</div>
                        <div class="student-info"><strong>Email:</strong> ${student.email}</div>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = '<p style="text-align: center; color: #666;">No student data available.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            document.getElementById('studentsContainer').innerHTML = '<p style="color: red;">Error loading student data</p>';
        });
}

// Load Courses Data
function loadCourses() {
    fetch('backend/api.php?action=getCourses')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('coursesContainer');
            container.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                data.data.forEach((course, index) => {
                    const card = document.createElement('div');
                    card.className = 'course-card';
                    card.style.animationDelay = (index * 0.1) + 's';
                    card.innerHTML = `
                        <div class="course-name">${course.name}</div>
                        <div class="course-description">${course.description}</div>
                        <div class="course-duration"><strong>Duration:</strong> ${course.duration}</div>
                    `;
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = '<p style="text-align: center; color: #666;">No course data available.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            document.getElementById('coursesContainer').innerHTML = '<p style="color: red;">Error loading course data</p>';
        });
}

// Load Holidays Data
function loadHolidays() {
    fetch('backend/api.php?action=getHolidays')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('holidaysContainer');
            container.innerHTML = '';
            
            if (data.success && data.data.length > 0) {
                let tableHTML = `
                    <table class="holiday-table">
                        <thead>
                            <tr>
                                <th>Holiday Name</th>
                                <th>Date</th>
                                <th>Day</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                data.data.forEach(holiday => {
                    const date = new Date(holiday.date);
                    const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });
                    tableHTML += `
                        <tr>
                            <td>${holiday.name}</td>
                            <td class="holiday-date">${holiday.date}</td>
                            <td>${dayName}</td>
                        </tr>
                    `;
                });
                
                tableHTML += `
                        </tbody>
                    </table>
                `;
                container.innerHTML = tableHTML;
            } else {
                container.innerHTML = '<p style="text-align: center; color: #666;">No holiday data available.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading holidays:', error);
            document.getElementById('holidaysContainer').innerHTML = '<p style="color: red;">Error loading holiday data</p>';
        });
}

// Load data when page loads
window.addEventListener('load', loadData);

// Intersection Observer for animations on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.faculty-card, .student-card, .course-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    observer.observe(card);
});