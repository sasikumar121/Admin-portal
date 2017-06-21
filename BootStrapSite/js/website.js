
//code to validate the values that are entered in the form
function validateInput() {
    message = '';
    var successMessage = '';
    var isValid = true;
    isValid = validateFirstName("firstname");
    isValid = validateLastName("lastname");

    if (!validateRequiredText("streetaddress")) {
        addRemove("streetaddress");
        message = message + ' * Street Address is Required ' + "\n";
        isValid = false;
    }
    else {
        addSuccess("streetaddress");
    }
    if (!validateRequiredText("city")) {
        addRemove("city");
        message = message + ' * City is Required ' + "\n";
        isValid = false;
    }
    else {
        addSuccess("city");
    }
    isValid = validateState("state", message);

    if (!validateRequiredText("zipcode")) {
        addRemove("zipcode");
        message = message + ' * Zip Code is Required ' + "\n";
        isValid = false;
    }
    else {
        addSuccess("zipcode");
    }
    if (!validateRequiredText("contactmessage")) {
        addRemove("contactmessage");
        message = message + ' * Message is Required ' + "\n";
        isValid = false;
    }
    else {
        addSuccess("contactmessage");
    }
    if (!isValid) {
        alert(message);
    }
    else {
        //code to create alert message for successful form entry
        successMessage = successMessage + 'First Name :' + $("#firstname").val() + "\n";
        successMessage = successMessage + 'Last Name :' + $("#lastname").val() + "\n";
        successMessage = successMessage + 'Street Address :' + $("#streetaddress").val() + "\n";
        successMessage = successMessage + 'City :' + $("#city").val() + "\n";
        successMessage = successMessage + 'State :' + $("#state").val() + "\n";
        successMessage = successMessage + 'ZipCode :' + $("#zipcode").val() + "\n";
        successMessage = successMessage + 'Message :' + $("#contactmessage").val() + "\n";
        var canSend = confirm(successMessage);
        if (canSend) {

            //Creates a new object d with current date and time values.
            var d = new Date();
            var sentMessage = "Message was sent successfully on " + d;
            alert(sentMessage);
            $("form#contactform").submit();
        }
        else {

        }
    }

}
var message = '';
//code to add the glyphicon to show an error
function addRemove(id) {
    var div = $("#" + id).closest("div");
    div.removeClass("has-success");
    $("#glypcn" + id).remove();
    div.addClass("has-error");
    div.addClass("has-feedback");
    div.append('<span id="glypcn' + id + '" class="glyphicon glyphicon-remove form-control-feedback" ></span>')
}

//code to add the glyphicon to show a success
function addSuccess(id) {
    var div = $("#" + id).closest("div");
    div.removeClass("has-error");
    div.addClass("has-success has-feedback");
    $("#glypcn" + id).remove();
    div.append('<span id="glypcn' + id + '" class="glyphicon glyphicon-ok form-control-feedback" ></span>')
}
//code to validate first name
function validateFirstName(id) {
    var isValid = true
    if (!validateRequiredText("firstname")) {
        message = message + ' * First Name is Required ' + "\n";
        isValid = false;
    }
    else if (!validateFirstNameLength("firstname")) {
        message = message + ' * First Name Should be greater than 1 and less than 20 ' + "\n";
        isValid = false;
    }
    if (!isValid) {
        addRemove("firstname");
    }
    else {
        addSuccess("firstname");

    }
    return isValid;

}
//code to validate first name length
function validateFirstNameLength(id) {
    if ($("#" + id).val().length > 1 && $("#" + id).val().length < 20) {
        return true;
    }
    else {
        return false;
    }
}
//code to validate last name
function validateLastName(id) {
    var isValid = true
    if (!validateRequiredText("lastname")) {
        message = message + ' * Last Name is Required ' + "\n";
        isValid = false;
    }
    else if (!validateLastNameLength("lastname")) {
        message = message + ' * Last Name Should be greater than or equal to 3 and less than 20 ' + "\n";
        isValid = false;
    }
    if (!isValid) {
        addRemove("lastname");
    }
    else {
        addSuccess("lastname");
    }
    return isValid;

}
//code to validate first name length
function validateLastNameLength(id) {
    if ($("#" + id).val().length >= 3 && $("#" + id).val().length < 20) {
        return true;
    }
    else {
        return false;
    }
}

//code to validate state
function validateState(id) {
    var isValid = true
    if (!validateRequiredText("state")) {
        message = message + ' * State is Required ' + "\n";
        isValid = false;
    }
    else if (!validateStateLength("state")) {
        message = message + ' * State should be only 2 characters ' + "\n";
        isValid = false;
    }
    if (!isValid) {
        addRemove("state");
    }
    else {
        addSuccess("state");
    }
    return isValid;

}
//code to validate state length
function validateStateLength(id) {
    if ($("#" + id).val().length == 2) {
        return true;
    }
    else {
        return false;
    }
}
//code to validate if the text field is empty or null
function validateRequiredText(id) {
    if ($("#" + id).val() == null || $("#" + id).val() == "") {

        return false;
    }
    else {

        return true;
    }
}

