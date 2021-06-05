
document.addEventListener('DOMContentLoaded',function(ev) {
    if(document.location.href.split("/").pop() === "todayschedule") {
        return;
    }
    var menus = document.querySelectorAll('.menu-items');
    menus.forEach(menu => {
        menu.addEventListener('click',toggleMenus);
    });
    menus[0].click();
});
function toggleMenus(ev) {
    var menus = document.querySelectorAll('.menu-items');
    menus.forEach(menu => {
        menu.classList.remove('menu-items-active');
    });
    ev.currentTarget.classList.add('menu-items-active');
    toogleContent(ev);
}

function toogleContent(ev) {
    if(document.querySelector('.activity-wrapper') !== null) {
        activitySwitcher(ev);
    }
    else if(document.querySelector('.schedule-wrapper') !== null) {
        scheduleSwitcher(ev);
    }
    else if(document.querySelector('.profile-wrapper') !== null) {
        profileSwitcher(ev);
    }
    else if(document.querySelector('.set-schedules-wrapper') !== null) {
        setSchedulesSwitcher(ev);
    }
}

function activitySwitcher(ev) {
    if(ev.currentTarget.textContent === "AddActivity") {
        document.getElementById('addActForm').classList.remove('hide-content');
        document.getElementsByClassName('activity-shower')[0].classList.add('hide-content');
        document.getElementById('editActForm').classList.add('hide-content');
    }
    else if(ev.currentTarget.textContent === "ActivityList") {
        document.getElementById('addActForm').classList.add('hide-content');
        document.getElementsByClassName('activity-shower')[0].classList.remove('hide-content');
        document.getElementById('editActForm').classList.add('hide-content');
    }
    else if(ev.currentTarget.textContent === "EditActivity") {
        document.getElementById('addActForm').classList.add('hide-content');
        document.getElementsByClassName('activity-shower')[0].classList.add('hide-content');
        document.getElementById('editActForm').classList.remove('hide-content');
    }
}
function scheduleSwitcher(ev) {
    if(ev.currentTarget.textContent === "CreateSchedule") {
        document.getElementById('createScheduleForm').classList.remove('hide-content');
        document.getElementsByClassName('schedule-shower')[0].classList.add('hide-content');
        document.getElementById('editScheduleForm').classList.add('hide-content');
    }
    else if(ev.currentTarget.textContent === "ScheduleList") {
        document.getElementById('createScheduleForm').classList.add('hide-content');
        document.getElementsByClassName('schedule-shower')[0].classList.remove('hide-content');
        document.getElementById('editScheduleForm').classList.add('hide-content');
    }
    else if(ev.currentTarget.textContent === "EditSchedule") {
        document.getElementById('createScheduleForm').classList.add('hide-content');
        document.getElementsByClassName('schedule-shower')[0].classList.add('hide-content');
        document.getElementById('editScheduleForm').classList.remove('hide-content');
    }
}
function profileSwitcher(ev) {
    if(ev.currentTarget.textContent === "EditProfile") {
        document.getElementById("changePasswordForm").classList.add('hide-content');
        document.getElementById("editProfileForm").classList.remove("hide-content");
    }
    else if(ev.currentTarget.textContent === "ChangePassword") {
        document.getElementById("changePasswordForm").classList.remove('hide-content');
        document.getElementById("editProfileForm").classList.add("hide-content");
    }
}
function setSchedulesSwitcher(ev) {
    if(ev.currentTarget.textContent === "Assign Schedule Date") {
        document.getElementById('setScheduleForm').classList.remove('hide-content');
        document.getElementsByClassName('schedule-date-shower')[0].classList.add('hide-content');
        document.getElementById('editScheduleDateForm').classList.add('hide-content');
    }
    else if(ev.currentTarget.textContent === "Assigned Schedule Dates") {
        document.getElementById('setScheduleForm').classList.add('hide-content');
        document.getElementsByClassName('schedule-date-shower')[0].classList.remove('hide-content');
        document.getElementById('editScheduleDateForm').classList.add('hide-content');
    }
    else if(ev.currentTarget.textContent === "Change Schedule Date") {
        document.getElementById('setScheduleForm').classList.add('hide-content');
        document.getElementsByClassName('schedule-date-shower')[0].classList.add('hide-content');
        document.getElementById('editScheduleDateForm').classList.remove('hide-content');
    }
}
