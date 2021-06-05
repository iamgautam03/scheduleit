function checkEmail(obj) {
    let emailRegExp = /^[0-9A-Za-z][0-9A-Za-z!#$%'*+-/=?^_`{}|]{0,63}@[A-Za-z0-9._]{1,253}.[a-z]{2,}$/;
    if(emailRegExp.test(obj.value) === true) {
        displayMsg(obj,"");
        return true;
    }
    displayMsg(obj,"Invalid Email");
    return false;    
}
function checkActNDScheduleName(obj) {
    let nameRegExp = /^[0-9A-Za-z ]{1,}$/;
    if(nameRegExp.test(obj.value) === true) {
        displayMsg(obj,"");
        return true;
    }
    displayMsg(obj,"Invalid Name");
    return false;
}
function checkConfPassword(obj) {
    var newPass = document.getElementsByName('txtNewPass');
    if(newPass[0].value === obj.value) {
        displayMsg(obj,"");
        return true;
    }
    displayMsg(obj,"Confirm Password is Not Matching");
    return false;
}
function checkPassword(obj) {
    if (obj.value.length >= 8) {
        displayMsg(obj,"");
        return true;
    }
    displayMsg(obj,"Invalid Password");
    return false;
}
function checkName(obj) {
    let nameRegExp = /^[A-Z]([A-Z]|[a-z])+$/;
    if(nameRegExp.test(obj.value) === true) {
        displayMsg(obj,"");
        return true;
    }
    displayMsg(obj,"Invalid Name");
    return false;
}
function displayMsg(obj,msg) {
    obj.nextElementSibling.innerHTML = msg;
    if(msg === "") {
        obj.style.border = '2px solid #0C0C0C';
    }
    else {
        obj.style.border = '2px solid Red';
    }
}
function validateLoginFields() {
    let res = checkEmail(document.getElementById('username')) && checkPassword(document.getElementById('password'));
    if  (res === true) {
        document.getElementById('loginForm').submit();
    }
}
function validateRegFields() {
    let res = checkName(document.getElementById('name')) && checkEmail(document.getElementById('username')) && checkPassword(document.getElementById('password'));
    if  (res === true) {
        document.getElementById('regForm').submit();
    }
}