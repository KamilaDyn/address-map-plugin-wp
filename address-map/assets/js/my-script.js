const map = L.map('mapid').setView([52.237049, 21.017532], 7);


L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);



const url_json = `${addressData.root_url}/wp-json/address-map/v1/search`;


function getAdresses() {
    return new Promise((resolve, reject) => {
        fetch(url_json, {
                method: "GET",
                headers: {
                    "Content-type": "application/json;charset=UTF-8",
                },
            })
            .then(response => {
                return response.json()
            })
            .then(response => {
                let data = response.addresses.map((item, index) => {
                    return object = {
                        name: item.name,
                        city: item.address.city,
                        postcode: item.address.postcode,
                        street: item.address.street,
                        number: item.address.number,
                        description: item.description,
                        url: item.url,
                        lat: item.geolocation.lat,
                        lon: item.geolocation.lon,
                        image: item.image
                    }
                })
                resolve(data)
            })

    })
}
getAdresses().then(data => {

    for (let i = 0; i < data.length; i++) {
        let location = new L.latLng(data[i].lat,
            data[i].lon);
        var marker = L.marker(location).addTo(map);

        let pupContent = `<div>${data[i].image}<div class="popup-text"><h4>${data[i].name}</h4><span>Adres:  ${data[i].street} ${data[i].number}, ${data[i].postcode} ${data[i].city} </span><p> ${data[i].description} <a href="${data[i].url}</div>">Więcej</a></p>`;



        marker.bindPopup(pupContent, {
            maxWidth: 340,
            maxHeight: 220,
            className: 'mypopup',
        }, ).openPopup();
        let obj = data[i];
        console.log(obj)
    }

})