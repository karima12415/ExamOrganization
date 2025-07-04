function handleFiliereInput(value) {
    const options = document.querySelectorAll("#filieres option");
    let selectedId = "";
    
    // Find matching filiere and get its ID
    options.forEach(option => {
        if (option.value === value) {
            selectedId = option.getAttribute("data-id");
        }
    });
    
    // Set hidden input value
    document.getElementById("filiere_hidden").value = selectedId;
    
    // Enable specialty input and load specialties if filiere is selected
    const specialtyInput = document.getElementById("spcia_input");
    if (selectedId) {
        specialtyInput.disabled = false;
        loadSpecialties(selectedId);
    } else {
        specialtyInput.disabled = true;
        specialtyInput.value = "";
        document.getElementById("specialties").innerHTML = "";
        document.getElementById("specialties_hidden").value = "";
    }
}

function loadSpecialties(id_filiere) {
    const specialtiesDatalist = document.getElementById("specialties");
    
    fetch(`functions.php?id_filiere=${encodeURIComponent(id_filiere)}`)
    .then(response => response.json())
    .then(specialties => {
        // Clear existing options
        specialtiesDatalist.innerHTML = "";
        
        // Add new options
        specialties.forEach(specialty => {
            const option = document.createElement("option");
            option.value = specialty.name; // Adjust according to your DB column name
            option.setAttribute("data-id", specialty.id); // Adjust according to your DB column name
            specialtiesDatalist.appendChild(option);
        });
    })
    .catch(error => console.error("Error loading specialties:", error));
}

// In handlespecialtiesInput() function (timetable.js)
// Replace references to non-existent "level_input" with proper elements
function handlespecialtiesInput(inputValue) {
    const options = document.querySelectorAll("#specialties option");
    let matchedId = "";
    
    options.forEach(option => {
        if (option.value === inputValue) {
            matchedId = option.getAttribute("data-id");
        }
    });
    
    document.getElementById("specialties_hidden").value = matchedId;
}

