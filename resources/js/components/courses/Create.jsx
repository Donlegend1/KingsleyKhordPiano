import ReactDOM from 'react-dom/client';
import React, { useState } from 'react';
import axios from 'axios';

const CourseForm = () => {
  const [course, setCourse] = useState({
    title: '',
    category: '',
    description: '',
    video_url: '',
    // image_path: '',
    level: 'beginner',
    enrollment_count: 0,
    status: 'active',
    prerequisites: '',
    // what_you_will_learn: '',

    rating_count: 0,
    average_rating: 0,
    // resources: [],
    // requirements: '',
    likes: 0,
    dislikes: 0
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setCourse({ ...course, [name]: value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
   try {
     
    console.log('Course data:', course);
      const response = await axios.post('/api/admin/course/store', course);
      console.log('Course created:', response.data);
    } catch (error) {
      console.error('Error creating course:', error);
    }
  };

  return (
    <div className="p-8 bg-white rounded-lg shadow-lg max-w-2xl mx-auto">
      <h2 className="text-2xl font-semibold mb-6 text-gray-800">Create New Course</h2>
      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <input name="title" placeholder="Title" value={course.title} onChange={handleChange} className="w-full p-3 border rounded-lg" />
          <input name="category" placeholder="Category" value={course.category} onChange={handleChange} className="w-full p-3 border rounded-lg" />
          <input name="video_url" placeholder="Video URL" value={course.video_url} onChange={handleChange} className="w-full p-3 border rounded-lg" />
          {/* <input name="image_path" placeholder="Image Path" value={course.image_path} onChange={handleChange} className="w-full p-3 border rounded-lg" /> */}
       <select name="status" value={course.status} onChange={handleChange} className="w-full p-3 border rounded-lg">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="draft">Draft</option>
      </select>
       
          <select name="level" value={course.level} onChange={handleChange} className="w-full p-3 border rounded-lg">
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
          </select>

         
        
        </div>

        <textarea name="description" placeholder="Description" value={course.description} onChange={handleChange} className="w-full p-3 border rounded-lg" rows="4"></textarea>
        {/* <textarea name="what_you_will_learn" placeholder="What You Will Learn" value={course.what_you_will_learn} onChange={handleChange} className="w-full p-3 border rounded-lg" rows="4"></textarea> */}

       

        <button type="submit" className="px-6 py-3 bg-black text-white rounded-lg hover:bg-blue-600 hover:text-black transition duration-300">
          Save Course
        </button>
      </form>
    </div>
  );
};

export default CourseForm;

if (document.getElementById('courses-create')) {
    const Index = ReactDOM.createRoot(document.getElementById("courses-create"));

    Index.render(
        <React.StrictMode>
            <CourseForm/>
        </React.StrictMode>
    )
}