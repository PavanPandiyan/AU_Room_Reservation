document.addEventListener("DOMContentLoaded", function () {
    function editUser(id) {
        let row = document.getElementById("row_" + id);
        if (!row) {
            console.error("Row not found for ID:", id);
            return;
        }

        let fields = row.querySelectorAll(".editable-field");

        if (fields.length === 0) {
            console.error("Editable fields not found for ID:", id);
            return;
        }

        fields.forEach(field => {
            field.contentEditable = true;
            field.style.border = "1px solid #ccc";
        });

        row.querySelector(".save-btn").style.display = "inline-block";
        row.querySelector(".edit-btn").style.display = "none";
    }

    function saveUser(id) {
        let row = document.getElementById("row_" + id);
        if (!row) {
            console.error("Row not found for ID:", id);
            return;
        }

        let fields = row.querySelectorAll(".editable-field");
        if (fields.length < 6) {
            console.error("Not enough editable fields found!");
            return;
        }

        let data = {
            id: id,
            username: fields[0].innerText.trim(),
            email: fields[1].innerText.trim(),
            phone: fields[2].innerText.trim(),
            gender: fields[3].innerText.trim(),
            age: fields[4].innerText.trim(),
            aadhar: fields[5].innerText.trim(),
            update: "true"
        };

        fetch("manage_user.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams(data).toString()
        })
        .then(response => response.text())
        .then(result => {
            console.log("Server Response:", result);

            if (result.trim() === "success") {
                fields.forEach(field => {
                    field.contentEditable = false;
                    field.style.border = "none";
                });

                row.querySelector(".edit-btn").style.display = "inline-block";
                row.querySelector(".save-btn").style.display = "none";
            } else {
                alert("Error updating user! Please try again.");
                console.error("Update failed:", result);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Error updating user! Check console for details.");
        });
    }

    function deleteUser(id) {
        if (!confirm("Are you sure you want to delete this user?")) return;

        fetch(`manage_user.php?delete=${id}`)
        .then(response => response.text())
        .then(result => {
            console.log("Server Response:", result);
            if (result.trim() === "success") {
                let row = document.getElementById("row_" + id);
                if (row) row.remove();
            } else {
                alert("Error deleting user! Server said: " + result);
                console.error("Delete failed:", result);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            alert("Error deleting user! Check console for details.");
        });
    }

    function filterUsers() {
        let searchInput = document.getElementById("searchInput").value.toLowerCase();
        let genderFilter = document.getElementById("genderFilter").value.toLowerCase();
        let rows = document.querySelectorAll("#userTable tbody tr");

        rows.forEach(row => {
            let username = row.querySelector(".username")?.textContent.toLowerCase() || "";
            let email = row.querySelector(".email")?.textContent.toLowerCase() || "";
            let phone = row.querySelector(".phone")?.textContent.toLowerCase() || "";
            let gender = row.querySelector(".gender")?.textContent.toLowerCase() || "";

            let matchesSearch = username.includes(searchInput) || email.includes(searchInput) || phone.includes(searchInput);
            let matchesGender = (genderFilter === "" || gender === genderFilter);

            row.style.display = matchesSearch && matchesGender ? "" : "none";
        });
    }

    // Apply filters when input changes
    document.getElementById("searchInput").addEventListener("keyup", filterUsers);
    document.getElementById("genderFilter").addEventListener("change", filterUsers);

    // Make functions globally accessible
    window.editUser = editUser;
    window.saveUser = saveUser;
    window.deleteUser = deleteUser;
});
