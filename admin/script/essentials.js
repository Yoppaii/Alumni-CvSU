// function populateYears(selectElementId, startYear, endYear = new Date().getFullYear()) {
//     const selectElement = document.getElementById(selectElementId);
//     if (!selectElement) return console.error(`Element with ID "${selectElementId}" not found.`);

//     selectElement.innerHTML = ""; // Clear existing options

//     for (let year = endYear; year >= startYear; year--) {
//         const option = document.createElement("option");
//         option.value = year;
//         option.textContent = year;
//         selectElement.appendChild(option);
//     }

//     selectElement.value = endYear;
// }

// // Export the function if using ES modules
// export { populateYears };
