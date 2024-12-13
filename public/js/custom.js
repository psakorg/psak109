$(document).ready(function(){
    $('#btnEdit').click(function(){
        var noAcc = prompt("Please enter NO_ACC to edit:", ""); // Prompt user to enter NO_ACC
        if (noAcc != null && noAcc != "") {
            loadEditForm(noAcc);
        } else {
            alert("No NO_ACC provided. Please try again.");
            $('#editModal').modal('hide'); // Hide modal if no NO_ACC is provided
        }
    });

    function loadEditForm(noAcc) {
        $.ajax({
            url: base_url + 'corporate/home_tblmaster/get_edit_form/' + noAcc,
            method: 'GET',
            success: function(response) {
                $('#editFormContent').html(response);
            }
        });
    }
});