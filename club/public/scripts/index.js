let rooms = [], computer_img = '/icons/computer_icon.png', selectRoom = null, selectComputer = null
const ACTIVE = 1, NOT_SELECT = 3, ARCHIVE = 10
document.addEventListener('DOMContentLoaded', async () => {
    getRoom()
    newDatePicker('date_start_filter', (str) => {
        let endDate = document.getElementById('date_end_filter').value
        if (endDate) {
            getRoom()
        }
    })
    newDatePicker('date_end_filter', (str) => {
        let startDate = document.getElementById('date_start_filter').value
        if(startDate){
            getRoom()
        }
    })
    newDatePicker('date_start')
    newDatePicker('date_end')
})

async function getRoom() {
    SlickLoader.enable();
    let query = '?'
    let startDate = document.getElementById('date_start_filter').value
    let endDate = document.getElementById('date_end_filter').value
    if (startDate) {
        query += `search[startDate]=${startDate}&`
    }
    if (endDate) {
        query += `search[endDate]=${endDate}&`
    }
    let data = await fetch('/api/room' + query).then(data => data.json())
    if (data['data'] === undefined) {
        throwWindowError(data['message'])
        SlickLoader.disable();
        return
    }
    data = data['data']
    rooms = []
    let room, computer
    try {
        for (let i = 0; i < data.length; i++) {
            room = new Room()
            room.id = data[i]['id']
            room.description = data[i]['description']
            room.computers = []
            for (let j = 0; j < data[i]['computers'].length; j++) {
                computer = new Computer()
                computer.id = data[i]['computers'][j]['id']
                computer.description = data[i]['computers'][j]['description']
                computer.price = data[i]['computers'][j]['price']
                computer.status = data[i]['computers'][j]['status']
                room.computers[j] = computer
            }
            rooms[i] = room
        }
        if(selectRoom === null){
            selectRoom = 0
        }
        loadRooms()
        document.getElementById('computer_description').innerHTML = ''
    } catch (error) {
        throwWindowError('Внезапная ошибка')
        console.log(error)
    }
    SlickLoader.disable();
}

function loadRooms() {
    let main = document.getElementById('main_rooms')
    main.innerHTML = ''
    for (let i = 0; i < rooms[selectRoom].computers.length; i++) {
        main.innerHTML +=
            `<div onclick="getComputer(this)" computer-id="${rooms[selectRoom].computers[i].id}" class="computer">
                <img src="${computer_img}" width="70" height="70" />
                <h2>Компик ${rooms[selectRoom].computers[i].id}</h2>
            </div>`
    }
    document.getElementById('room_number').innerHTML = `<a href="room/${rooms[selectRoom].id}">Комната №${rooms[selectRoom].id}</a>`
    if (selectRoom > 0) {
        document.getElementById('back_arrow').style.display = 'block'
    }
    if (selectRoom === 0 && rooms.length > 1) {
        document.getElementById('next_arrow').style.display = 'block'
    }
    if (selectRoom >= rooms.length - 1) {
        document.getElementById('next_arrow').style.display = 'none'
    }
    if (selectRoom === 0 ){
        document.getElementById('back_arrow').style.display = 'none'
    }
}

function next() {
    if (selectRoom < rooms.length) {
        selectRoom++
        loadRooms()
        return
    }
    document.getElementById('next_arrow').style.display = 'none'
}

function back() {
    if (selectRoom > 0) {
        selectRoom--
        loadRooms()
        return
    }
    document.getElementById('back_arrow').style.display = 'none'
}

function getComputer(el) {
    document.getElementById('bron').style.display = 'none'
    let id = parseInt(el.getAttribute('computer-id'))
    let room = rooms[selectRoom]
    let computer = null
    for (let i = 0; i < room.computers.length; i++) {
        if (id === room.computers[i].id) {
            computer = room.computers[i]
        }
    }
    if (computer === null) {
        return
    }
    if (selectComputer !== null && selectComputer.id === computer.id && selectComputer.current) {
        selectComputer.current = false
        document.getElementById('computer_description').innerHTML = ''
        document.getElementById('bron').style.display = 'none'
        return;
    }
    let text, color
    if(computer.status === ACTIVE){
        document.getElementById('bron').style.display = 'block'
        text = 'Не занято'
        color = 'green'
    } else {
        text = 'Занято'
        color = 'grey'
    }
    document.getElementById('computer_description').innerHTML =
        `<div>
            <a href="/computer/${computer.id}">Компик №${computer.id}  (${computer.price} руб/час)</a>
            <p>${computer.description}</p>
            <p><span style="color: ${color}"> ${text}</span></p>
        </div>`
    selectComputer = computer
    selectComputer.current = true
}

function closeBron() {
    document.getElementById('input_bron').style.display = 'none'
}

function bron() {
    if (checkAuthorization()) {
        document.getElementById('input_bron').style.display = 'block'
        let startDate = document.getElementById('date_start_filter').value
        let endDate = document.getElementById('date_end_filter').value
        if (startDate) {
            document.getElementById('date_start').value = document.getElementById('date_start_filter').value
        }
        if (endDate) {
            document.getElementById('date_end').value = document.getElementById('date_end_filter').value
        }
        return
    }
    closeAuth()
}

async function sendBron() {
    if (checkAuthorization()) {
        let startDate = document.getElementById('date_start').value
        let endDate = document.getElementById('date_end').value
        if (!startDate || !endDate) {
            throwWindowError('Не все поля заполнены')
            return
        }
        let data = await fetch('/api/schedule/week', {
            method: 'POST',
            body: JSON.stringify({
                "dateStart": startDate,
                "dateEnd": endDate,
                "computerId": selectComputer.id
            })
        }).then(data => data.json())
        if (data['data'] === undefined) {
            throwWindowError(data['message'])
            return
        }
        throwWindowSuccess('Успешно')
        loadRooms()
    } else {
        throwWindowError('Вы не авторизованы')
        clearToken()
    }
}

class Room {
    id
    computers = []
    description
}

class Computer {
    id
    price
    description
    status
    files = []
    current = false
}