# Sri Bharata Pati Mahavidylay Samantiyapalli - College Website

A modern, responsive college website built with HTML5, CSS3, JavaScript (frontend) and PHP (backend) for managing faculty, students, courses, and holidays.

## Features

✨ **Main Features:**
- 🏫 Attractive Home Page with College Name and Background Image
- 📱 Responsive Navigation Bar (Top & Side Navigation)
- 👨‍🏫 Faculty Management with Smooth Animations
- 👨‍🎓 Student Information Display
- 📚 Course Details Management
- 📅 Holiday Calendar
- 💻 RESTful PHP Backend API
- 🗄️ MySQL Database Integration

## Project Structure

```
college-website/
├── index.html              # Main HTML file
├── css/
│   ├── styles.css         # Main stylesheet with animations
│   └── navbar.css         # Navigation bar styling
├── js/
│   └── script.js          # Frontend JavaScript with AJAX calls
├── backend/
│   ├── config.php         # Database configuration
│   ├── api.php            # Main API endpoint handler
│   ├── Faculty.php        # Faculty class
│   ├── Student.php        # Student class
│   ├── Course.php         # Course class
│   └── Holiday.php        # Holiday class
├── database/
│   └── college_db.sql     # Database schema and sample data
├── images/
│   └── college-bg.jpg     # Background image (add your college photo)
└── README.md              # This file
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Server (or any PHP-compatible web server)
- Modern web browser

### Step 1: Database Setup

1. Open phpMyAdmin or MySQL command line
2. Import the database file:
   ```bash
   mysql -u root -p < database/college_db.sql
   ```
3. Or manually create the database by running SQL queries in `database/college_db.sql`

### Step 2: Update Database Configuration

Edit `backend/config.php` and update credentials:

```php
$servername = "localhost";
$username = "root";      // Your MySQL username
$password = "";         // Your MySQL password
$dbname = "college_db";
```

### Step 3: Add College Background Image

1. Create an `images` folder in the project root
2. Add your college background photo as `college-bg.jpg`
3. Ensure the image is high-quality and at least 1920x1080 pixels

### Step 4: Deploy

1. Move the entire project to your web server's root directory:
   - For Apache: `/var/www/html/college-website/`
   - For Windows: `C:\xampp\htdocs\college-website\`

2. Access the website:
   ```
   http://localhost/college-website/
   ```

## API Endpoints

### Faculty Operations
- `GET /backend/api.php?action=getFaculty` - Get all faculty
- `POST /backend/api.php?action=addFaculty` - Add new faculty
- `PUT /backend/api.php?action=updateFaculty` - Update faculty
- `DELETE /backend/api.php?action=deleteFaculty&id=1` - Delete faculty

### Student Operations
- `GET /backend/api.php?action=getStudents` - Get all students
- `POST /backend/api.php?action=addStudent` - Add new student
- `PUT /backend/api.php?action=updateStudent` - Update student
- `DELETE /backend/api.php?action=deleteStudent&id=1` - Delete student

### Course Operations
- `GET /backend/api.php?action=getCourses` - Get all courses
- `POST /backend/api.php?action=addCourse` - Add new course
- `PUT /backend/api.php?action=updateCourse` - Update course
- `DELETE /backend/api.php?action=deleteCourse&id=1` - Delete course

### Holiday Operations
- `GET /backend/api.php?action=getHolidays` - Get all holidays
- `POST /backend/api.php?action=addHoliday` - Add new holiday
- `PUT /backend/api.php?action=updateHoliday` - Update holiday
- `DELETE /backend/api.php?action=deleteHoliday&id=1` - Delete holiday

## Example API Requests

### Adding a Faculty Member
```javascript
fetch('backend/api.php?action=addFaculty', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        name: 'Dr. John Doe',
        position: 'Professor',
        qualification: 'Ph.D. in Computer Science',
        email: 'john@sbpms.edu.in',
        phone: '+91-9876543210',
        department: 'Computer Science'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

### Adding a Holiday
```javascript
fetch('backend/api.php?action=addHoliday', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        name: 'Diwali',
        date: '2024-11-01',
        description: 'Festival of lights'
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Features in Detail

### 1. Home Section
- College name: "Sri Bharata Pati Mahavidylay Samantiyapalli"
- Attractive background image with overlay
- Tagline and Call-to-Action button
- Smooth fade-in animations

### 2. Navigation
- **Top Navigation Bar:**
  - Logo with icon
  - Horizontal navigation links
  - Responsive hamburger menu
  - Sticky positioning
  - Gradient background with smooth hover effects

- **Side Navigation:**
  - Appears on mobile devices
  - Smooth slide-in animation
  - Overlay effect
  - Easy close button

### 3. Faculty Section
- Card-based layout with hover animations
- Faculty details: Name, Position, Qualification, Email
- Shimmer effect on images
- Smooth slide-up animations
- Grid responsive layout

### 4. Student Section
- Professional student cards
- Left border accent
- Information: Name, Roll Number, Course, Semester, Email
- Hover effects for interactivity
- Smooth animations on load

### 5. Courses Section
- Gradient cards with modern design
- Course details: Name, Description, Duration
- Hover scale effect
- Smooth fade-in animations
- Responsive grid layout

### 6. Holiday Section
- Clean table layout
- Holiday name, date, and day of week
- Alternating row colors for better readability
- Hover effects for better UX
- Sorted by date

## Animations & Effects

The website includes multiple animations:
- **Fade In:** Smooth opacity transition
- **Slide In:** Cards sliding in from different directions
- **Hover Effects:** Scale, translate, and shadow changes
- **Shimmer:** Lighting effect on faculty images
- **Smooth Scrolling:** Navigation links scroll smoothly

## Browser Compatibility

- Chrome (v90+)
- Firefox (v88+)
- Safari (v14+)
- Edge (v90+)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Customization

### Change College Name
Edit the college name in `index.html` and update the title tag

### Change Colors
Update gradient colors in `css/styles.css`:
```css
/* Change from this */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* To your college colors */
background: linear-gradient(135deg, #YourColor1 0%, #YourColor2 100%);
```

### Add More Faculty/Students/Courses
Use the API endpoints to add more data, or insert directly into the database.

## Database Backup & Maintenance

### Backup Database
```bash
mysqldump -u root -p college_db > backup_college_db.sql
```

### Restore Database
```bash
mysql -u root -p college_db < backup_college_db.sql
```

## Security Considerations

⚠️ **Important:** This is a basic implementation. For production:

1. **Add Authentication:** Implement login system for admin users
2. **Validate Input:** Always validate and sanitize user inputs
3. **Use HTTPS:** Enable SSL/TLS certificates
4. **Database Security:** Use environment variables for credentials
5. **CORS:** Restrict CORS to your domain only
6. **Rate Limiting:** Implement rate limiting on API endpoints
7. **SQL Injection:** Use prepared statements (already implemented)

## Troubleshooting

### Issue: "Database connection failed"
- Check MySQL is running
- Verify credentials in `backend/config.php`
- Ensure database exists

### Issue: "CORS error"
- Ensure CORS headers are enabled in `backend/config.php`
- Check your domain is allowed

### Issue: Data not loading
- Check browser console for errors
- Verify API endpoint URLs
- Check database has sample data

### Issue: Images not showing
- Add college background image to `images/college-bg.jpg`
- Check image path in CSS
- Verify image permissions

## Performance Optimization

- Minify CSS and JavaScript for production
- Enable gzip compression
- Use image optimization
- Implement database indexing (already done)
- Enable browser caching

## Future Enhancements

- 🔐 User authentication and role-based access
- 📧 Email notifications
- 📱 Mobile app version
- 🎓 Online admission system
- 📊 Student dashboard
- 📝 Assignment management
- 👥 Parent portal
- 📢 News and announcements

## License

This project is free to use for educational purposes.

## Support & Contact

For issues or questions:
- Email: info@sbpms.edu.in
- Phone: +91-XXXXXXXXXX

## Credits

Developed for Sri Bharata Pati Mahavidylay Samantiyapalli

---

**Last Updated:** 2024
**Version:** 1.0.0