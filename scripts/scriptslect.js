function getfaculty(id_univ) {
    let faculDropDown = document.forms["my-form"].facul;
    let deptDropDown = document.forms["my-form"].depart;
    let filiereDropDown = document.forms["my-form"].filie;
    if (id_univ.trim() === "") {
        faculDropDown.disabled = true;
        faculDropDown.innerHTML = '<option value="">Choose a faculty</option>';
        return;
    }
    fetch(`functions.php?id_univ=${encodeURIComponent(id_univ)}`)
    .then(response => response.json())
    .then(function(faculties){
        let options = '<option value="">Choose a faculty</option>';
        faculties.forEach(faculty => {
            options += `<option value="${faculty.id_faculty}">${faculty.name_faculty}</option>`;
        });
        faculDropDown.innerHTML = options;
        faculDropDown.disabled = false;

        deptDropDown.innerHTML = '<option value="">Choose a department</option>';
        deptDropDown.disabled = true;
        filiereDropDown.innerHTML = '<option value="">Choose a filiere</option>';
        filiereDropDown.disabled = true;
    })
    .catch(error => console.error("Error fetching faculties:", error));
}

function getdepartment(id_faculty) {
    let deptDropDown = document.forms["my-form"].depart;
    let filiereDropDown = document.forms["my-form"].filie;

    if (id_faculty.trim() === "") {
        deptDropDown.disabled = true;
        deptDropDown.innerHTML = '<option value="">Choose a department</option>';
        return;
    }

    fetch(`functions.php?id_faculty=${encodeURIComponent(id_faculty)}`)
    .then(response => response.json())
    .then(function(departments){
        let options = '<option value="">Choose a department</option>';
        departments.forEach(department => {
            options += `<option value="${department.id_department}">${department.name_deprtment}</option>`;
        });
        deptDropDown.innerHTML = options;
        deptDropDown.disabled = false;

        
        filiereDropDown.innerHTML = '<option value="">Choose a filiere</option>';
        filiereDropDown.disabled = true;
    })
    .catch(error => console.error("Error fetching departments:", error));
}

function getfiliere(id_department) {
    let filiereDropDown = document.forms["my-form"].filie;

    if (id_department.trim() === "") {
        filiereDropDown.disabled = true;
        filiereDropDown.innerHTML = '<option value="">Choose a filiere</option>';
        return;
    }

    fetch(`functions.php?id_department=${encodeURIComponent(id_department)}`)
    .then(response => response.json())
    .then(function(filieres){
        let options = '<option value="">Choose a filiere</option>';
        filieres.forEach(filiere => {
            options += `<option value="${filiere.id_filiere }">${filiere.name_filiere}</option>`;
        });
        filiereDropDown.innerHTML = options;
        filiereDropDown.disabled = false;
    })
    .catch(error => console.error("Error fetching filieres:", error));
}

 // We'll extract the selected university's ID from the datalist
 function handleUniversityInput(inputValue) {
    const datalist = document.getElementById("universities");
    const options = datalist.options;
    const faculInput = document.getElementById("facul_input");
    let matchedId = "";
    
    for (let i = 0; i < options.length; i++) {
        if (options[i].value === inputValue) {
            matchedId = options[i].getAttribute("data-id");
            break;
        }
    }

    document.getElementById("univer_hidden").value = matchedId;
    
    if (matchedId !== "") {
        // University is selected
        faculInput.disabled = false;
        faculInput.style.backgroundColor = 'transparent';
        faculInput.style.color = '#333';
        faculInput.style.borderColor = '#333';
        faculInput.placeholder = "Your faculty";
        getfaculty(matchedId); 
        loadFacultyDatalist(matchedId);
    } else {
        // University selection was cleared or doesn't match
        faculInput.value = ""; // Clear the faculty input
        faculInput.disabled = true;
        faculInput.style.backgroundColor = '#f5f5f5';
        faculInput.style.color = '#999';
        faculInput.style.borderColor = '#ddd';
        document.getElementById("faculties").innerHTML = ""; // Clear datalist options
        document.getElementById("facul_hidden").value = ""; // Clear hidden value
        faculInput.disabled = true; // Disable faculty input
        faculInput.placeholder = "Select university first"; // Update placeholder
    }
}
//search faculty
function loadFacultyDatalist(id_univ) {
    const faculList = document.getElementById("faculties");
    const faculInput = document.getElementById("facul_input");

    if (id_univ.trim() === "") {
        faculInput.disabled = true;
        faculList.innerHTML = "";
        return;
    }

    fetch(`functions.php?id_univ=${encodeURIComponent(id_univ)}`)
    .then(response => response.json())
    .then(function(faculties){
        faculInput.disabled = false;
        faculList.innerHTML = "";

        faculties.forEach(faculty => {
            const option = document.createElement("option");
            option.value = faculty.name_faculty;
            option.setAttribute("data-id", faculty.id_faculty);
            faculList.appendChild(option);
        });
    })
    .catch(error => console.error("Error loading faculty datalist:", error));
}
function handleFacultyInput(inputValue) {
    const datalist = document.getElementById("faculties");
    const options = datalist.options;
    let matchedId = "";

    for (let i = 0; i < options.length; i++) {
        if (options[i].value === inputValue) {
            matchedId = options[i].getAttribute("data-id");
            break;
        }
    }

    document.getElementById("facul_hidden").value = matchedId;
}