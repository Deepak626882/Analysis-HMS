// Function to create popup
function createPopup(year, monthNumber, date, day, roomno, nextdaydate, nextmonth, nextyear) {
    // console.log('year: ' + year + ' monthNumber: ' + monthNumber + ' date: ' + date)
    let popup = document.createElement('div');
    popup.classList.add('popup');
    popup.draggable = true;

    popup.addEventListener('dragend', function (event) {
        popup.style.left = event.clientX + 'px';
        popup.style.top = event.clientY + 'px';
    });

    let closeButton = document.createElement('span');
    closeButton.innerHTML = '&times;';
    closeButton.classList.add('popup-close');
    closeButton.addEventListener('click', function () {
        document.body.removeChild(popup);
        activePopup = null;
    });

    let div1 = document.createElement('div');
    div1.classList.add('d-flex', 'justify-content-around');
    let arrivalp = document.createElement('b');
    let departurep = document.createElement('b');
    let div2 = document.createElement('div');
    div2.classList.add('d-flex', 'justify-content-around');
    div2.style.margin = '0 0 -15px 0';
    let departurepvalue = document.createElement('p');
    let arrivalpvalue = document.createElement('p');

    arrivalp.textContent = 'Arrival';
    arrivalpvalue.textContent = date + '-' + monthNumber + '-' + year;
    departurep.textContent = 'Departure';
    departurepvalue.textContent = nextdaydate + '-' + nextmonth + '-' + nextyear;

    div1.appendChild(arrivalp);
    div1.appendChild(departurep);
    div2.appendChild(arrivalpvalue);
    div2.appendChild(departurepvalue);

    let div = document.createElement('div');
    div.style.display = 'grid';

    let button = document.createElement('button');
    let button2 = document.createElement('button');

    button.textContent = 'Walk In / Reservation';
    button.classList.add('btn', 'btn-link', 'btnroom');

    button2.textContent = 'Maintenance Block';
    button2.classList.add('btn', 'btn-link', 'btnroom');

    div.appendChild(button);
    div.appendChild(button2);

    popup.appendChild(closeButton);
    popup.appendChild(div1);
    popup.appendChild(div2);
    popup.appendChild(div);

    return popup;
}


const info = document.getElementById('infoicon');
const bookingmodal = document.getElementById('bookingmodal');
let timeoutId;

info.addEventListener('mouseover', function () {
    clearTimeout(timeoutId);
    bookingmodal.style.opacity = '1';
    bookingmodal.style.display = 'block';
});

info.addEventListener('mouseout', function () {
    timeoutId = setTimeout(() => {
        bookingmodal.style.display = 'none';
        bookingmodal.style.opacity = '0';
    }, 3000);
});


document.addEventListener("DOMContentLoaded", function () {
    var checkAllRoomCat = document.getElementById("checkallroomcat");
    var roomCatCheckboxes = document.querySelectorAll(".roomcatcheckbox");

    checkAllRoomCat.addEventListener("click", function () {
        roomCatCheckboxes.forEach(function (checkbox) {
            checkbox.checked = checkAllRoomCat.checked;
            triggerCheckboxChange(checkbox);
        });
    });

    roomCatCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            triggerCheckboxChange(checkbox);
        });
    });

    function triggerCheckboxChange(checkbox) {
        let isChecked = checkbox.checked;
        let elementsWithSameDataValue = document.querySelectorAll(`[data-value="${checkbox.value}"]`);

        elementsWithSameDataValue.forEach(function (element) {
            if (isChecked) {
                element.style.display = 'table-row';
            } else {
                element.style.display = 'none';
            }
        });
    }
});

let clickedcount = 0;
let roomcategorybtn = document.getElementById('roomcategorybtn');
roomcategorybtn.addEventListener('click', function () {
    clickedcount++;
    if (clickedcount % 2 == 0) {
        document.getElementById('listroomcat').style.display = 'none';
    } else {
        document.getElementById('listroomcat').style.display = 'block';
    }
})