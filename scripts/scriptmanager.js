document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab");
  const pages = document.querySelectorAll(".content-section");
  const sidebarLinks = document.querySelectorAll(".sidebar a");

  const teacherForm = document.getElementById("form_teacher");
  if (teacherForm) {
    teacherForm.addEventListener("submit", function (e) {
      const modulesSelect = document.getElementById("modules");
      const selectedModules = Array.from(modulesSelect.selectedOptions).map(opt => opt.value);
      if (selectedModules.length === 0) {
        const confirmContinue = confirm("You did not select any module for this teacher. Are you sure you want to continue?");
        if (!confirmContinue) {
          e.preventDefault();
        }
      }
    });
  }
  //  التحقق عند تعديل أستاذ
  const editTeacherForm = document.getElementById("editTeacherForm");
  if (editTeacherForm) {
    editTeacherForm.addEventListener("submit", function (e) {
      const modulesSelect = document.getElementById("edit_modules");
      const selectedModules = Array.from(modulesSelect.selectedOptions).map(opt => opt.value);
      if (selectedModules.length === 0) {
        const confirmContinue = confirm("You did not select any module while editing the teacher. Do you want to continue without selecting any?");
        if (!confirmContinue) {
          e.preventDefault();
        }
      }
    });
  }
  // Function to switch between sections
  function switchSection(targetId) {
    // Hide all content sections
    pages.forEach(page => {
      page.classList.remove("active");
    });
    // Remove active class from all tabs
    tabs.forEach(tab => {
      tab.classList.remove("active");
    });
    // Show the target section
    const targetPage = document.getElementById(targetId);
    if (targetPage) {
      targetPage.classList.add("active");
    }
    // Set the active tab
    const activeTab = document.querySelector(`.tab[data-target="${targetId}"]`);
    if (activeTab) {
      activeTab.classList.add("active");
    }
  }

  // Event listeners for sidebar links
  sidebarLinks.forEach(link => {
    link.addEventListener("click", (event) => {
      event.preventDefault(); // Prevent default anchor behavior
      const targetId = link.getAttribute("href").substring(1); // Get the target section ID
      switchSection(targetId); // Switch to the target section
    });
  });

  // Event listeners for tabs
  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      const target = tab.getAttribute("data-target");
      switchSection(target); // Switch to the target section
    });
  });

  // Initial setup: Show the first section
  switchSection("Specialty"); // Default to the Specialty section
});
function editSpecialty(specialty) {
  // Hide add form and show edit form
  document.getElementById('specialtyForm').style.display = 'none';
  document.getElementById('editSpecialtyForm').style.display = 'block';
  // Populate edit form fields
  document.getElementById('edit_specialty_id').value = specialty.id;
  document.getElementById('edit_specialty_name').value = specialty.name;
  document.getElementById('edit_id_filiere').value = specialty.id_filiere;
}
function fetchLevels(specialtyId) {
  const levelSelect = document.getElementById('module_level_id');
  levelSelect.innerHTML = '<option value="" disabled selected>Select Level</option>'; // Reset levels
  fetch(`fetch_levels.php?specialty_id=${specialtyId}`)
    .then(response => response.json())
    .then(data => {
      data.forEach(level => {
        const option = document.createElement('option');
        option.value = level.id;
        option.textContent = level.name;
        levelSelect.appendChild(option);
      });
    }).catch(error => console.error('Error fetching levels:', error));
}
// Cancel Edit button handler
document.getElementById('cancelEdit').addEventListener('click', function () {
  document.getElementById('editSpecialtyForm').style.display = 'none';
  document.getElementById('specialtyForm').style.display = 'block';
  // Clear edit form fields
  document.getElementById('edit_specialty_id').value = '';
  document.getElementById('edit_specialty_name').value = '';
  document.getElementById('edit_id_filiere').value = '';
});
function editLevel(level) {
  document.getElementById('edit_level_id').value = level.id;
  document.getElementById('edit_level_name').value = level.name;
  document.getElementById('edit_student_count').value = level.student_count;
  document.getElementById('edit_specialty_id').value = level.specialty_id;
  // Show the edit form and hide the add form
  document.getElementById('editLevelForm').style.display = 'block';
  document.getElementById('levelForm').style.display = 'none'; // Assuming this is the ID of the add level form
}
// Cancel Edit button handler
document.getElementById('cancelLevelEdit').addEventListener('click', function () {
  document.getElementById('editLevelForm').style.display = 'none';
  document.getElementById('levelForm').style.display = 'block'; // Show the add form again
  // Clear edit form fields
  document.getElementById('edit_level_id').value = '';
  document.getElementById('edit_level_name').value = '';
  document.getElementById('edit_student_count').value = '';
  document.getElementById('edit_specialty_id').value = '';
});
// Fetch levels for edit form with a callback to set selected level after loading
function fetchLevelsForEdit(specialtyId, callback) {
  const levelSelect = document.getElementById('edit_module_level_id');
  levelSelect.innerHTML = '<option value="" disabled selected>Select Level</option>';
  if (!specialtyId) {
    if (callback) callback();
    return;
  }
  fetch(`fetch_levels.php?specialty_id=${specialtyId}`)
    .then(response => response.json())
    .then(data => {
      data.forEach(level => {
        const option = document.createElement('option');
        option.value = level.id;
        option.textContent = level.name;
        levelSelect.appendChild(option);
      });
      if (callback) callback();
    })
    .catch(error => {
      console.error('Error fetching levels:', error);
      if (callback) callback();
    });
}

// Edit module function called with module data object
function editModule(module) {
  // Show edit form and hide add form (adjust id if needed)
  document.getElementById('editModuleForm').style.display = 'block';
  const addForm = document.getElementById('moduleForm');
  if (addForm) addForm.style.display = 'none';

  // Set hidden id and name
  document.getElementById('edit_module_id').value = module.id || '';
  document.getElementById('edit_module_name').value = module.name || '';

  // Set specialty and fetch levels, then set level select value after levels load
  document.getElementById('edit_module_specialty_id').value = module.specialty_id || '';

  fetchLevelsForEdit(module.specialty_id, function () {
    document.getElementById('edit_module_level_id').value = module.level_id || '';
  });
}

// Cancel edit button
document.getElementById('cancelModuleEdit').addEventListener('click', function () {
  document.getElementById('editModuleForm').style.display = 'none';
  const addForm = document.getElementById('moduleForm');
  if (addForm) addForm.style.display = 'block';

  // Clear form fields
  document.getElementById('edit_module_id').value = '';
  document.getElementById('edit_module_name').value = '';
  document.getElementById('edit_module_specialty_id').value = '';
  const levelSelect = document.getElementById('edit_module_level_id');
  if (levelSelect) {
    levelSelect.innerHTML = '<option value="" disabled selected>Select Level</option>';
  }
});

// Function to populate the edit form with class data
function editRoom(room) {
  // Show the edit form and hide the add form
  document.getElementById('editClassForm').style.display = 'block';
  const addForm = document.querySelector('form[action="insert_class.php"]'); // Adjust the selector if needed
  if (addForm) addForm.style.display = 'none';

  // Populate the edit form fields
  document.getElementById('edit_class_id').value = room.id || '';
  document.getElementById('edit_class_name').value = room.name || '';
  document.getElementById('edit_count').value = room.student_count || '';
  document.getElementById('edit_supervisor_count').value = room.supervisor_count || '';
  document.getElementById('edit_id_deprtment').value = room.id_department || ''; // Assuming you have this field in your room data
}

// Cancel Edit button handler
document.getElementById('cancelClassEdit').addEventListener('click', function () {
  document.getElementById('editClassForm').style.display = 'none';
  const addForm = document.querySelector('form[action="insert_class.php"]'); // Adjust the selector if needed
  if (addForm) addForm.style.display = 'block'; // Show the add form again

  // Clear edit form fields
  document.getElementById('edit_class_id').value = '';
  document.getElementById('edit_class_name').value = '';
  document.getElementById('edit_student_count').value = '';
  document.getElementById('edit_supervisor_count').value = '';
  document.getElementById('edit_id_deprtment').value = ''; // Clear department selection
});
function editTeacher(teacher) {
  // Hide add form and show edit form
  document.getElementById('form_teacher').style.display = 'none';
  document.getElementById('editTeacherForm').style.display = 'block';
  // Populate edit form fields
  document.getElementById('edit_teacher_id').value = teacher.id;
  document.getElementById('edit_name').value = teacher.name;
  document.getElementById('edit_email').value = teacher.email;
  document.getElementById('edit_hourly_size').value = teacher.hourly_size;
  // Populate the modules select
  const modulesSelect = document.getElementById('edit_modules');
  for (let i = 0; i < modulesSelect.options.length; i++) {
    modulesSelect.options[i].selected = teacher.modules.includes(modulesSelect.options[i].value);
  }
}
