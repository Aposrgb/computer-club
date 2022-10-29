document.addEventListener('DOMContentLoaded', async () => {

})

async function getSchedule() {
    SlickLoader.enable();
    let data = await fetch('/schedule/week').then(data => data.json())
    console.log(data['data'])
    SlickLoader.disable();
}