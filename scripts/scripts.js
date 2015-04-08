function validateEmail(Event) {
	var element = Event.currentTarget;
	var elementVal = Event.currentTarget.value;
	
	var error = false;
	
	if (elementVal == null | elementVal == "") {
		error = true;
	}
	else if (elementVal.match(/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/) == null ) {
		error = true;
	}
	
	if (error == true) {
		element.className = "error";
	}
	else {
		element.className = null;
	}
}

function validatePassword(Event) {
	var element = Event.currentTarget;
	var elementVal = Event.currentTarget.value;
	
	var error = false;
	
	if (elementVal == null | elementVal == "") {
		error = true;
	}
	else if (elementVal.match(/\s/) != null) {
		error = true;
	}
	else if (elementVal.length <= 8) {
		error = true;
	}
	
	if (error == true) {
		element.className = "error";
	}
	else {
		element.className = null;
	}
}

function validateName(Event) {
	var element = Event.currentTarget;
	var elementVal = Event.currentTarget.value;
	
	var error = false;
	
	if (elementVal == null | elementVal == "") {
		error = true;
	}
	else if (elementVal.match(/^\s+/) != null) {
		error = true;
	}
	else if (elementVal.match(/\s+$/) != null) {
		error = true;
	}
	
	if (error == true) {
		element.className = "error";
	}
	else {
		element.className = null;
	}
}

function validateBirthday(Event) {
    var year = document.getElementById("register-dob-year");
    var month = document.getElementById("register-dob-month");
    var day = document.getElementById("register-dob-day");

    var error = false;
    var errorStr = "";

    switch (parseInt(month.value)) {
        case 04:
        case 06:
        case 09:
        case 11:
            if (parseInt(day.value) > 30) {
                errorStr = "This month only has 30 days";
                error = true;
            }
            break;
        case 02:
            if (!leapYear(parseInt(year.value)) && parseInt(day.value) > 28) {
                errorStr = "This month only has 28 days";
                error = true;
            }
            else if (leapYear(parseInt(year.value)) && parseInt(day.value) > 29) {
                errorStr = "This month only has 29 days.";
                error = true;
            }
            break;
    }

    if (error == true) {
        document.getElementById("register-dob-error").innerHTML = errorStr;
        year.className = "error";
        month.className = "error";
        day.className = "error";
    }
    else {
        document.getElementById("register-dob-error").innerHTML = "";
        year.className = null;
        month.className = null;
        day.className = null;
    }

    return error;
}

function confirmPassword(Event) {	
	var passwordVal = document.getElementById("register-password").value;
	var element = Event.currentTarget;
	var elementVal = Event.currentTarget.value;
	
	if (elementVal != passwordVal) {
		element.className = "error";
	}
	else {
		element.className = null;
	}
}

function validatePost(Event) {
	var element = Event.currentTarget;
	var elementVal = Event.currentTarget.value;
	var charCounter = document.getElementById("charCounter");
	
	var error = false;
	
	charCounter.innerHTML = elementVal.length + "/1000";
	
	if (elementVal == null | elementVal == "") {
		error = true;
	}
	else if (elementVal.length >= 1000) {
		error = true;
		charCounter.className = "charCounterError";
	}
	
	if (error == true) {
		element.className = "error";
	}
	else {
		element.className = null;
		charCounter.className = "charCounter";
	}
}