// Function to open the bid form
function openBidForm(orderId) {
    document.getElementById('order_id').value = orderId;
    document.getElementById('bid-form-container').style.display = 'block';
}

// Function to close the bid form
function closeBidForm() {
    document.getElementById('bid-form-container').style.display = 'none';
}

// Add search functionality
document.getElementById('search').addEventListener('input', function() {
    var query = this.value.toLowerCase();
    var rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        var cells = row.querySelectorAll('td');
        var matched = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(query));
        row.style.display = matched ? '' : 'none';
    });
});
