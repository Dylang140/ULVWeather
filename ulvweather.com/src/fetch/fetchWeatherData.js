export async function fetchWeatherData(start, end) {
    var formData = new FormData();
    formData.append('start', start);
    formData.append('end', end);
    const requestOptions = {
        method: 'POST',
        body: formData
    };
    const url = 'https://api.ulvweather.com/api.php';
    const response = await fetch(url, requestOptions);
    let data = await response.json();
    return data;
}

export async function fetchLastWeatherData() {
    const requestOptions = {
        method: 'POST',
        body: ''
    };
    const url = 'https://api.ulvweather.com/api.php';
    const response = await fetch(url, requestOptions);
    let data = await response.json();
    return data;
}

export async function fetchCurrentWeatherData() {
    const requestOptions = {
        method: 'GET'
    };
    const url = 'https://api.ulvweather.com/currentWeatherData.php';
    const response = await fetch(url, requestOptions);
    let data = await response.json();
    console.log(data);
    return data;
}