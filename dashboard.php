<script>
    const conn = new WebSocket('ws://localhost:8080');

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
        // Update the reports_table and users_table divs here
        document.getElementById('reports_table').innerHTML = e.data;
    };

    function fetchReports() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_reports.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('reports_table').innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    function fetchUsers() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_users.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById('users_table').innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    // Fetch reports and users every 5 seconds
    setInterval(fetchReports, 5000);
    setInterval(fetchUsers, 5000);

    // Initial fetch
    fetchReports();
    fetchUsers();
</script>
