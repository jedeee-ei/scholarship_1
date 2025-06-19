/**
 * Course Mapping Utilities
 * Shared functions for converting course names to codes and loading subjects
 */

// Course name to course code mapping
const COURSE_MAPPING = {
    'Bachelor of Science in Information Technology': 'BSIT',
    'Bachelor of Science in Computer Science': 'BSCS',
    'Bachelor of Science in Computer Engineering': 'BSCpE',
    'Bachelor of Science in Electronics and Communications Engineering': 'BSECE',
    'Bachelor of Science in Electrical Engineering': 'BSEE',
    'Bachelor of Science in Civil Engineering': 'BSCE',
    'Bachelor of Science in Mechanical Engineering': 'BSME',
    'Bachelor of Science in Business Administration Major in Marketing Management': 'BSBA-MM',
    'Bachelor of Science in Business Administration Major in Financial Management': 'BSBA-FM',
    'Bachelor of Science in Business Administration Major in Human Resource Management': 'BSBA-HRM',
    'Bachelor of Science in Accountancy': 'BSA',
    'Bachelor of Science in Management Accounting': 'BSMA',
    'Bachelor of Science in Tourism Management': 'BSTM',
    'Bachelor of Science in Hospitality Management': 'BSHM',
    'Bachelor of Science in Nursing': 'BSN',
    'Bachelor of Science in Medical Technology': 'BSMT',
    'Bachelor of Science in Physical Therapy': 'BSPT',
    'Bachelor of Science in Radiologic Technology': 'BSRT',
    'Bachelor of Science in Psychology': 'BSPsych',
    'Bachelor of Science in Biology': 'BSBio',
    'Bachelor of Science in Chemistry': 'BSChem',
    'Bachelor of Science in Mathematics': 'BSMath',
    'Bachelor of Arts in English': 'AB-Eng',
    'Bachelor of Arts in Political Science': 'AB-Pol',
    'Bachelor of Arts in History': 'AB-Hist',
    'Bachelor of Arts in Philosophy': 'AB-Phil',
    'Bachelor of Science in Development Communication': 'BSDC',
    'Bachelor of Science in Sociology': 'BSS',
    'Bachelor of Elementary Education': 'BEEd',
    'Bachelor of Secondary Education Major in English': 'BSEd-Eng',
    'Bachelor of Secondary Education Major in Mathematics': 'BSEd-Math',
    'Bachelor of Secondary Education Major in Science': 'BSEd-Sci',
    'Bachelor of Secondary Education Major in Social Studies': 'BSEd-SS',
    'Bachelor of Secondary Education Major in Filipino': 'BSEd-Fil',
    'Bachelor of Physical Education': 'BPED'
};

/**
 * Convert course name to course code
 * @param {string} courseName - Full course name
 * @returns {string} Course code or original name if not found
 */
function getCourseCode(courseName) {
    return COURSE_MAPPING[courseName] || courseName;
}

/**
 * Get subjects from ScholarshipDataHelper data
 * @param {string} course - Course name
 * @param {string} yearLevel - Year level (e.g., "1st Year")
 * @param {string} semester - Semester (e.g., "1st Semester")
 * @param {Object} subjectsData - Subjects data from ScholarshipDataHelper
 * @returns {Array} Array of subjects or empty array
 */
function getSubjectsFromHelper(course, yearLevel, semester, subjectsData) {
    // Convert course name to course code
    const courseCode = getCourseCode(course);
    
    // Convert year level to number
    const yearNum = parseInt(yearLevel.replace(/\D/g, ''));
    
    console.log('Looking for subjects:', {
        originalCourse: course,
        courseCode: courseCode,
        yearNum: yearNum,
        semester: semester,
        availableCourses: Object.keys(subjectsData)
    });
    
    if (subjectsData[courseCode] && subjectsData[courseCode][yearNum] && subjectsData[courseCode][yearNum][semester]) {
        return subjectsData[courseCode][yearNum][semester];
    }
    
    return [];
}

/**
 * Load subjects directly from helper data
 * @param {string} course - Course name
 * @param {string} yearLevel - Year level
 * @param {string} semester - Semester
 * @param {Object} subjectsData - Subjects data from ScholarshipDataHelper
 * @param {Function} displayCallback - Function to call with subjects data
 * @param {Function} errorCallback - Function to call when no subjects found
 */
function loadSubjectsDirectly(course, yearLevel, semester, subjectsData, displayCallback, errorCallback) {
    try {
        const subjects = getSubjectsFromHelper(course, yearLevel, semester, subjectsData);
        
        if (subjects && subjects.length > 0) {
            displayCallback(subjects);
        } else {
            errorCallback(course, yearLevel, semester);
        }
    } catch (error) {
        console.error('Error loading subjects:', error);
        errorCallback(course, yearLevel, semester);
    }
}
