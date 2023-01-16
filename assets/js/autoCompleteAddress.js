const endpoint = new URL('https://api-adresse.data.gouv.fr/search/')

const autoCompleteAddress = (fieldSelector, onChoose) => {
    const searchElement = document.querySelector(fieldSelector)
    const resultContainer = createResultContainer()
    searchElement.after(resultContainer)
    let timer = null

    searchElement.addEventListener('keyup', (e) => {
        if (timer) {
            clearTimeout(timer)
        }

        if (e.target.value.length < 4) {
            resultContainer.innerHTML = ''
            return
        }

        if (e.keyCode === 16) {
            return
        }

        timer = setTimeout(() => {
            const userQuery = e.target.value.trim().replaceAll(' ', '+')
            search(userQuery).then(data => {
                resultContainer.innerHTML = ''
                data.features.forEach(address => {
                    const li = document.createElement('li')
                    li.classList.add('list-group-item')
                    li.innerText = address.properties.label
                    li.addEventListener('click', () => {
                        resultContainer.innerHTML = ''
                        searchElement.value = address.properties.label
                        onChoose(address)
                    })
                    resultContainer.appendChild(li)
                })
            })
        }, 500)
    })
}

const search = (query) => {
    endpoint.searchParams.set('q', query)
    endpoint.searchParams.set('autocomplete', '1')
    return fetch(endpoint).then(r => r.json())
}

const createResultContainer = () => {
    const resultContainer = document.createElement('ul')
    resultContainer.classList.add('list-group')
    resultContainer.style.position = 'absolute'
    return resultContainer
}

export default autoCompleteAddress