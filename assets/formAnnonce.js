import {searchAddresses} from "./js/autoCompleteAddress";
searchAddresses('#annonce_address', address => {
    document.querySelector('#annonce_street').value = address.properties.name
    document.querySelector('#annonce_postcode').value = address.properties.postcode
    document.querySelector('#annonce_city').value = address.properties.city
    document.querySelector('#annonce_lat').value = address.geometry.coordinates[1]
    document.querySelector('#annonce_lng').value = address.geometry.coordinates[0]
})
