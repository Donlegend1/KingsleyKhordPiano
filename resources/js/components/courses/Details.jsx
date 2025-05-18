import ReactDOM from 'react-dom/client';
import React, { use, useEffect, useState } from 'react';
import axios from 'axios';

const CourseDetails = ({ course }) => {
  return (
    <div className="p-6 bg-white rounded shadow-lg">
      <h2 className="text-xl font-bold mb-4">{course.title}</h2>
      <p className="mb-2">Category: {course.category}</p>
      <p className="mb-4">{course.description}</p>

      {course.video_url && (
        <div className="mb-4">
          <video width="100%" controls>
            <source src={'https://www.youtube.com/watch?v=Mres4xDThPg'} type="video/mp4" />
            Your browser does not support the video tag.
          </video>
        </div>
      )}

      <div className="mb-4">
        <h3 className="font-semibold">What You Will Learn:</h3>
        <p>{course.what_you_will_learn}</p>
    </div>
    
    <div className='text-center'>
     <button className='bg-black p-3 text-white hover:text-gray-800 hover:bg-yellow-500 rounded-full'>Completed</button>
    </div>

     
    </div>
  );
};

const CoursesPage = () => {
  const [courses, setCourses] = useState([]);
 const [selectedCourse, setSelectedCourse] = useState(null);
 

const lastSegment = window.location.pathname.split('/').filter(Boolean).pop();
 
  useEffect(() => {
    const fetchCourses = async () => {
      try {
       const response = await axios.get(`/api/member/courses/${lastSegment}`);
       console.log(response);
        setCourses(response.data);
      } catch (error) {
        console.error('Error fetching courses:', error);
      }
    };
    fetchCourses();
  }, []);

  return (
    <div className="flex">
      {/* Left Side: Course List */}
      <div className="w-1/3 p-4 bg-gray-100 h-screen overflow-y-auto">
        <h2 className="text-lg font-bold mb-4 flex items-center gap-2">
          <span className='fa fa-book'></span>{lastSegment.toUpperCase()} Courses
        </h2>
        {courses && courses.length > 0 ? (
          courses.map(course => (
            <div
              key={course.id}
              className="p-3 bg-white mb-3 flex items-center justify-between cursor-pointer hover:bg-gray-200 rounded shadow-sm"
              onClick={() => setSelectedCourse(course)}
            >
              <div className="flex items-center gap-2">
                <span className='fa fa-chevron-right text-gray-500'></span>
                <span className="text-gray-700 font-medium">{course.title}</span>
              </div>
            </div>
          ))
        ) : (
          <p className="text-gray-500">No courses available.</p>
        )}
      </div>

      {/* Right Side: Course Details */}
      <div className="w-2/3 p-4">
        {selectedCourse ? (
          <CourseDetails course={selectedCourse} />
     ) : (
          <div className="p-6 bg-white rounded shadow-lg text-center">
            <h2 className="text-xl font-bold mb-4">Select a Course</h2>
            <p>Please select a course from the list to view details.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default CoursesPage;

if (document.getElementById('course-details')) {
  const root = ReactDOM.createRoot(document.getElementById('course-details'));
  root.render(
    <React.StrictMode>
      <CoursesPage />
    </React.StrictMode>
  );
}
