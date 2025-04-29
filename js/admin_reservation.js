function applyFilter() {
    let search = document.getElementById("search").value.trim();
    let status = document.getElementById("status-filter").value;

    let params = new URLSearchParams();
    if (search) params.append("search", search);
    if (status !== "") params.append("status", status); // Only append if a status is selected

    window.location.href = "admin_reservation.php?" + params.toString();
}
document.addEventListener("DOMContentLoaded", function () {
    function applyFilter() {
        const searchInput = document.getElementById("search").value.trim();
        const statusFilter = document.getElementById("status-filter").value;
        
        let url = "admin_reservation.php?";
        
        if (searchInput) {
            url += `search=${encodeURIComponent(searchInput)}&`;
        }
        if (statusFilter) {
            url += `status=${encodeURIComponent(statusFilter)}&`;
        }

        // Remove trailing '&' if exists
        url = url.replace(/&$/, "");

        // Redirect to filtered results
        window.location.href = url;
    }

    // Attach event listener to the filter button
    document.querySelector("button[onclick='applyFilter()']").addEventListener("click", applyFilter);
});
