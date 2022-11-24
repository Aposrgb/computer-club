let isAuth = false

document.addEventListener('DOMContentLoaded', () => {
    checkApiKey()
})

function closeAuth() {
    let el = document.getElementById('auth')
    el.style.display = el.style.display === "block" ? "none" : "block"
}

function auth() {
    clearError()
    if (switchAuth()) {
        return;
    }
    let loginValue = document.getElementById('login').value;
    let passwordValue = document.getElementById('password').value;
    if (validate(loginValue, 6)) {
        return
    }
    if (validate(passwordValue, 6)) {
        return
    }
    fetch('/api/security/auth', {
        method: "POST",
        body: JSON.stringify({
            "email": loginValue,
            "password": passwordValue
        })
    })
        .then(data => data.json())
        .then(data => {
            if (checkError(data)) {
                return
            }
            saveToken(data['data']['token'])
        })
}

function register() {
    clearError()
    if (switchRegister()) {
        return;
    }
    let loginValue = document.getElementById('loginReg').value;
    let passwordValue = document.getElementById('passwordReg').value;
    let name = document.getElementById('name').value;
    let surname = document.getElementById('surname').value;
    let phone = document.getElementById('phone').value;
    if (validate(loginValue, 6)) {
        return
    }
    if (validate(passwordValue, 6)) {
        return
    }
    if (validate(name, 2)){
        return
    }
    if (validate(surname, 2)){
        return
    }
    let body = {
        "email": loginValue,
        "password": passwordValue,
        "name" : name,
        "surname" : surname
    }
    if(phone.length > 0){
        body['phone'] = phone
    }
    fetch('/api/security/registration', {
        method: "POST",
        body: JSON.stringify(body)
    })
        .then(data => data.json())
        .then(data => {
            if (checkError(data)) {
                return
            }
            saveToken(data['data']['token'])
        })

}

function validate(value, length) {
    if (value.length < length) {
        return throwWindowError('Значение должно быть больше ' + length + ' символов');
    }
    return false
}

function throwWindowError(msg) {
    document.getElementById('error').innerText = msg
    return true
}

function clearError() {
    document.getElementById('error').innerText = ''
}

function saveToken(token) {
    clearToken()
    localStorage.setItem('apiKey', token)
    successAuth()
}

function clearToken() {
    localStorage.removeItem('apiKey')
    localStorage.removeItem('nameUser')
    localStorage.removeItem('surnameUser')
    isAuth = false
    document.getElementById('button_auth').style.display = 'block'
    document.getElementById('name_user').innerText = ''
    document.getElementById('name_user').removeEventListener('click', tabProfile())
}

function checkApiKey(){
    if(localStorage.getItem('apiKey') !== null){
        successAuth()
    }
}

async function successAuth() {
    let apiKey = localStorage.getItem('apiKey')
    await fetch('/api/user', {
        method: "GET",
        headers: {
            'apiKey': apiKey
        }
    })
        .then(data => data.json())
        .then(data => {
            if (checkError(data)) {
                return
            }
            saveDataUser(data['data'])
        })
    checkAuthorization()
}

function checkAuthorization() {
    let name = localStorage.getItem('nameUser')
    let surname = localStorage.getItem('surnameUser')
    let apiKey = localStorage.getItem('apiKey')
    if(name !== null && surname !== null && apiKey !== null){
        isAuth = true
        document.getElementById('button_auth').style.display = 'none'
        document.getElementById('name_user').innerText = name + " " + surname
        document.getElementById('name_user').addEventListener('click', tabProfile())
    }
}

function tabProfile(){

}

function saveDataUser(data){
    localStorage.setItem('nameUser', data['name'])
    localStorage.setItem('surnameUser', data['surname'])
}

function checkError(ex) {
    if (ex.status === 400) {
        let body = ex['validationError']['body']
        if (body.length !== 0) {
            let allError = ''
            for (let i = 0; i < body.length; i++) {
                allError += body[i]['name'] + ": " + body[i]['message']
            }
            return throwWindowError(allError)
        }
        return throwWindowError(ex['message'])
    } else if(ex.status === 401){
        clearToken()
        return true
    } else if (ex.status === 404) {
        return throwWindowError(ex['message'])
    } else if (ex.status >= 500) {
        return throwWindowError('Серверная ошибка')
    }
    return false
}

function switchAuth() {
    let display = document.getElementById('authWindow').style.display
    if (display === 'none') {
        document.getElementById('authWindow').style.display = 'block'
        document.getElementById('registerWindow').style.display = 'none'
        return true
    }
    return false
}

function switchRegister() {
    let display = document.getElementById('registerWindow').style.display
    if (display === 'none') {
        document.getElementById('authWindow').style.display = 'none'
        document.getElementById('registerWindow').style.display = 'block'
        return true
    }
    return false
}