document.getElementById('select-all-modules').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.module-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = this.checked;
    }, this);

    updateSelectAllStatus(); // Update the "Select All" checkbox status
});

document.querySelectorAll('.module-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        updateSelectAllStatus(); // Update the "Select All" checkbox status
    });
});

function updateSelectAllStatus() {
    var checkboxes = document.querySelectorAll('.module-checkbox');
    var selectAllCheckbox = document.getElementById('select-all-modules');

    var allChecked = true;
    var anyChecked = false;

    checkboxes.forEach(function(checkbox) {
        if (!checkbox.checked) {
            allChecked = false;
        } else {
            anyChecked = true;
        }
    });

    if (anyChecked) {
        selectAllCheckbox.checked = allChecked;
    } else {
        selectAllCheckbox.checked = false;
    }
}
