// Registration Page Validation
document.getElementById("register-fname").addEventListener("change", validateName, false);
document.getElementById("register-lname").addEventListener("change", validateName, false);
document.getElementById("register-email").addEventListener("change", validateEmail, false);
document.getElementById("register-dob-month").addEventListener("change", validateBirthday, false);
document.getElementById("register-dob-day").addEventListener("change", validateBirthday, false);
document.getElementById("register-dob-year").addEventListener("change", validateBirthday, false);
document.getElementById("register-password").addEventListener("change", validatePassword, false);
document.getElementById("register-passwordConf").addEventListener("change", confirmPassword, false);