// Run code when page loads
document.addEventListener('DOMContentLoaded', () => {
    // Check which page we're on
    if (document.getElementById('weather-card') && document.getElementById('aqi-card')) {
        console.log("Dashboard loading...");
        initializeDashboard();
    } 
    else if (document.getElementById('advice-container')) {
        console.log("Advice page loading...");
        initializeAdvicePage();
    }
    else if (document.getElementById('forecast-main')) {
        console.log("Forecast loading...");
        initializeForecast();
    }
});

// Dashboard page setup
function initializeDashboard() {
    if (!navigator.geolocation) {
        handleGeoError(null, ['weather-card', 'aqi-card']);
        return;
    }

    navigator.geolocation.getCurrentPosition(async (position) => {
        const { latitude: lat, longitude: lon } = position.coords;
        try {
            const [weather, aqi] = await Promise.all([
                fetchWeather(lat, lon),
                fetchAqi(lat, lon)
            ]);
            
            updateWeatherCard(weather, 'weather-card');
            updateAqiCard(aqi, 'aqi-card');
        } catch (error) {
            console.error("API error:", error);
            document.getElementById('weather-card').innerHTML = `<h3>Weather</h3><p style="color:red;">${error.message}</p>`;
            document.getElementById('aqi-card').innerHTML = `<h3>AQI</h3><p style="color:red;">${error.message}</p>`;
        }
    }, 
    (error) => handleGeoError(error, ['weather-card', 'aqi-card']));
}

// Get weather data
async function fetchWeather(lat, lon) {
    const response = await fetch(`../api/get-weather.php?lat=${lat}&lon=${lon}&type=weather`);
    const data = await response.json();
    if (data.error) throw new Error(data.error);
    return data;
}

// Get AQI data
async function fetchAqi(lat, lon) {
    const response = await fetch(`../api/get-weather.php?lat=${lat}&lon=${lon}&type=aqi`);
    const data = await response.json();
    if (data.error) throw new Error(data.error);
    return data;
}

// Update weather display
function updateWeatherCard(data, elementId) {
    const card = document.getElementById(elementId);
    if (!card) return;

    const titleTag = (elementId === 'weather-card') ? 'h3' : 'h4';
    
    card.innerHTML = 
        `<${titleTag}>Weather in ${data.name}</${titleTag}>
         <p class="api-value">${data.main.temp} °C</p>
         <p>${data.weather[0].description}</p>
         <p>Feels like: ${data.main.feels_like} °C</p>
         <p>Humidity: ${data.main.humidity}%</p>`;
}

// Update AQI display
function updateAqiCard(data, elementId) {
    const card = document.getElementById(elementId);
    if (!card) return;

    const aqi = data.list[0].main.aqi;
    const aqiLevels = {
        1: { text: 'Good', color: '#004D40' },
        2: { text: 'Fair', color: '#ffd600' },
        3: { text: 'Moderate', color: '#ff9100' },
        4: { text: 'Poor', color: '#ff3d00' },
        5: { text: 'Very Poor', color: '#b71c1c' }
    };

    const titleTag = (elementId === 'aqi-card') ? 'h3' : 'h4';
    
    card.innerHTML = 
        `<${titleTag}>Air Quality Index</${titleTag}>
         <p class="api-value" style="color: ${aqiLevels[aqi].color}">${aqiLevels[aqi].text}</p>
         <p>AQI Level: ${aqi}/5</p>`;
}
