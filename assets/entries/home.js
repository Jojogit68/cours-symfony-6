import L from 'leaflet'
import '../../node_modules/leaflet/dist/leaflet.css'
import mapWorldIcon from '../img/map-world-icon.png'
import mapPinIcon from '../img/map-pin-icon.png'
import autoCompleteAddress from "../js/autoCompleteAddress";

const annonceMarkers = []
const positionMarkers = []
const circleLayer = []

const url = new URL(route)

const map = L.map('map', {
    center: [48.856962956340844, 2.3463447979091545],
    zoom: 8,
    scrollWheelZoom: false
})

const mapWorldMarker = L.icon({
    iconUrl: mapWorldIcon,
    iconSize: [50, 50]
})

const mapPinMarker = L.icon({
    iconUrl: mapPinIcon,
    iconSize: [50, 50]
})

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    minZoom: 8,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map)

const removeMarkers = (markers) => {
    for (let i = 0; i < markers.length; i++) {
        map.removeLayer(markers[i])
    }
}

const getAnnonces = (lat, lng, distance) => {

    removeMarkers(annonceMarkers)

    url.searchParams.set('lat', lat)
    url.searchParams.set('lng', lng)
    url.searchParams.set('distance', distance)

    fetch(url, {
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(annonces => {
        annonces.forEach(annonce => {
            const marker = L.marker([annonce.lat, annonce.lng], {
                icon: mapWorldMarker
            })
            marker.bindPopup(`
                <div class="card border-0">
                    <img src="${annonce.imageUrl}" alt="${annonce.title}">
                    <div class="card-body">
                        <h2 class="card-title pricing-card-title">${annonce.price / 100}<small class="text-muted fw-light">€</small></h2>
                        <h5 class="card-title">${annonce.title}</h5>
                        <p class="card-text">
                            ${annonce.description}
                        </p>
                        <a href="${annonce.link}" class="btn btn-secondary">Voir</a>
                      </div>
                    </div>
                    <div class="card-footer text-muted">Lat: ${annonce.lat} / Lng: ${annonce.lng}</div>
                </div>
            `)
            marker.addTo(map)
            annonceMarkers.push(marker)
        })
    })
}

/**
 * Remplit les champs #userLatitude et #userLongitude
 *
 * @param latitude
 * @param longitude
 */
const fillPositionForm = (latitude, longitude) => {
    document.querySelector('#userLatitude').value = latitude
    document.querySelector('#userLongitude').value = longitude
}

/**
 * Centre la carte aux coordonnées indiquées et ajoute une marker avec une popoup
 *
 * @param latitude
 * @param longitude
 * @param popupText
 */
const centerMap = (latitude, longitude, popupText) => {

    removeMarkers(positionMarkers)
    removeMarkers(annonceMarkers)

    map.setView([latitude, longitude], 13)

    const marker = L.marker([
        latitude,
        longitude
    ], {
        icon: mapPinMarker
    })

    const radius = parseInt(document.querySelector('#userDistance').value)

    const circle = L.circle([latitude, longitude], {
        color: '#725454',
        fillColor: '#725454',
        fillOpacity: 0.2,
        radius: radius * 1000
    })

    map.addLayer(marker)
    map.addLayer(circle)

    marker.bindPopup(popupText).openPopup()
    positionMarkers.push(marker)
    positionMarkers.push(circle)
}

document.querySelector('#localizeMe').addEventListener('click', () => {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(position => {
            centerMap(
                position.coords.latitude,
                position.coords.longitude,
                `Votre position ${position.coords.latitude}, ${position.coords.longitude}`
            )
            fillPositionForm(position.coords.latitude, position.coords.longitude)
        }, positionError => {
            alert(`Impossible de vous géolocaliser: ${positionError.message}`)
        })
    } else {
        alert('Votre navigateur ne supporte pas la géolocalisation')
    }
})

autoCompleteAddress('#searchAddress', address => {
    fillPositionForm(address.geometry.coordinates[1], address.geometry.coordinates[0])
    centerMap(
        address.geometry.coordinates[1],
        address.geometry.coordinates[0],
        `Adresse: ${address.properties.label}`
    )
})

document.querySelector('#searchOnMap').addEventListener('click', () => {
    const lat = document.querySelector('#userLatitude').value
    const lng = document.querySelector('#userLongitude').value
    const radius = parseInt(document.querySelector('#userDistance').value)

    getAnnonces(lat, lng, radius)
})

document.querySelector('#userDistance').addEventListener('change', e => {
    if (e.target.value.length < 1) {
        e.target.value = 100
    }
})