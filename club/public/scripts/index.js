let rooms = [], computer_img = '/icons/computer_icon.png'
document.addEventListener('DOMContentLoaded', async () => {
    getRoom()
})

async function getRoom() {
    SlickLoader.enable();
    let data = await fetch('/api/room').then(data => data.json())
    if (data['data'] === undefined) {
        throwWindowError(data['message'])
    }
    data = data['data']
    rooms = []
    let room = new Room, computer = new Computer()
    try{
        for(let i=0;i<data.length;i++){
            room.id = data[i]['id']
            room.description = data[i]['description']
            room.price = data[i]['price']
            room.computers = []
            for(let j = 0;j<data[i]['computers'].length;j++){
                computer.id = data[i]['computers'][j]['id']
                computer.description = data[i]['computers'][j]['description']
                computer.price = data[i]['computers'][j]['price']
                computer.status = data[i]['computers'][j]['status']
                room.computers[j] = computer
            }
            rooms[i] = room
            loadRooms()
        }
    } catch (error){
        throwWindowError('Внезапная ошибка')
    }
    SlickLoader.disable();
}

function loadRooms(){
    let main = document.getElementById('main_rooms')
    main.innerHTML =
        `<div class="computer">
            <img src="${computer_img}" width="50" height="50" />
            <p>Компик</p>
        </div>`
}

class Room {
    id
    computers = []
    price
    description
}

class Computer{
    id
    price
    description
    status
}