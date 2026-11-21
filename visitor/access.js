
document.getElementById('visitorForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    try {
        const response = await fetch('generate_code.php', {
            method: 'POST',
            body: new FormData(this)
        });
        const data = await response.json();

        if (!data.success) {
            alert(data.message);
            return;
        }

        // Show the code
        document.getElementById('accessCode').textContent = data.code;
        document.getElementById('expiryDisplay').textContent = ' Expires: ' + data.expires;
        document.getElementById('codeDescription').textContent = 'Enter this code at the gate';

        // Add a new row at the top of the table
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${data.created}</td>
            <td>${data.name}</td>
            <td>${data.purpose}</td>
            <td>${data.code}</td>
            <td>${data.expires}</td>
            <td class="actives">Active</td>`;
        document.getElementById('accessCodeTable').prepend(row);

    } catch (err) {
        alert('Something went wrong. Please try again.');
        console.error(err);
    }
});