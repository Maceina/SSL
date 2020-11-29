const submit = document.querySelector('#submit');
const name = document.querySelector('#name');
const count = document.querySelector('#count');

submit.addEventListener('click', search);

function search() {
    let api_url = "http://tinklai.files/LD-3/1-dalis/zinutes-api.php" + "?name=" + name.value + "&count=" + count.value;
    fetch(api_url).then(response => {
        return response.json()
    }).then(data => {
        let table = "<table class='table' id='table'>" +
            "<tr><td>Id</td><td>Vardas</td><td>Gavėjas</td><td>Data</td><td>Žinutė</td></tr>";
        for (i in data) {
            table += "<tr><td>" + data[i].id + "</td><td>" + data[i].name + "</td><td>" + data[i].receiver_email + "</td><td>" + data[i].date + "</td><td>" + data[i].message + "</td></tr>"
        }

        table += "</table>"
        document.getElementById('messages_table').innerHTML = table;
    }).catch(error => {
        console.log(error)
    })
}