import L from 'leaflet'
import '../../node_modules/leaflet/dist/leaflet.css'
import mapWorldIcon from '../img/map-world-icon.png'

const mapElement = document.querySelector('#map')
const lat = mapElement.dataset.lat
const lng = mapElement.dataset.lng

const map = L.map('map', {
    center: [lat, lng],
    zoom: 10
})

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map)

L.marker([lat, lng], {
    icon: L.icon({
        iconUrl: mapWorldIcon,
        iconSize: [50, 50]
    })
}).addTo(map)