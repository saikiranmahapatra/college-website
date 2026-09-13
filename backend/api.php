<?php
require_once 'config.php';
require_once 'Faculty.php';
require_once 'Student.php';
require_once 'Course.php';
require_once 'Holiday.php';

// Get action from query parameter
$action = isset($_GET['action']) ? $_GET['action'] : '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        // Faculty Operations
        case 'getFaculty':
            $faculty = new Faculty($conn);
            echo json_encode($faculty->getAll());
            break;
            
        case 'addFaculty':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                $faculty = new Faculty($conn);
                echo json_encode($faculty->add($data));
            }
            break;
            
        case 'updateFaculty':
            if ($method === 'PUT') {
                $data = json_decode(file_get_contents("php://input"), true);
                $faculty = new Faculty($conn);
                echo json_encode($faculty->update($data));
            }
            break;
            
        case 'deleteFaculty':
            if ($method === 'DELETE') {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $faculty = new Faculty($conn);
                echo json_encode($faculty->delete($id));
            }
            break;

        // Student Operations
        case 'getStudents':
            $student = new Student($conn);
            echo json_encode($student->getAll());
            break;
            
        case 'addStudent':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                $student = new Student($conn);
                echo json_encode($student->add($data));
            }
            break;
            
        case 'updateStudent':
            if ($method === 'PUT') {
                $data = json_decode(file_get_contents("php://input"), true);
                $student = new Student($conn);
                echo json_encode($student->update($data));
            }
            break;
            
        case 'deleteStudent':
            if ($method === 'DELETE') {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $student = new Student($conn);
                echo json_encode($student->delete($id));
            }
            break;

        // Course Operations
        case 'getCourses':
            $course = new Course($conn);
            echo json_encode($course->getAll());
            break;
            
        case 'addCourse':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                $course = new Course($conn);
                echo json_encode($course->add($data));
            }
            break;
            
        case 'updateCourse':
            if ($method === 'PUT') {
                $data = json_decode(file_get_contents("php://input"), true);
                $course = new Course($conn);
                echo json_encode($course->update($data));
            }
            break;
            
        case 'deleteCourse':
            if ($method === 'DELETE') {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $course = new Course($conn);
                echo json_encode($course->delete($id));
            }
            break;

        // Holiday Operations
        case 'getHolidays':
            $holiday = new Holiday($conn);
            echo json_encode($holiday->getAll());
            break;
            
        case 'addHoliday':
            if ($method === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                $holiday = new Holiday($conn);
                echo json_encode($holiday->add($data));
            }
            break;
            
        case 'updateHoliday':
            if ($method === 'PUT') {
                $data = json_decode(file_get_contents("php://input"), true);
                $holiday = new Holiday($conn);
                echo json_encode($holiday->update($data));
            }
            break;
            
        case 'deleteHoliday':
            if ($method === 'DELETE') {
                $id = isset($_GET['id']) ? $_GET['id'] : null;
                $holiday = new Holiday($conn);
                echo json_encode($holiday->delete($id));
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>