/**
 * 5-Day Weather Forecast Manager
 * Handles weather data fetching and display
 * Last updated: Nov 6, 2025
 */

// Start up when the page is ready
document.addEventListener('DOMContentLoaded', () => {
    const forecastContainer = document.getElementById('forecast-container');
    if (forecastContainer) {
        // Begin loading the forecast
        initializeForecast();
    }
});

// Get the local weather for this area
async function initializeForecast() {
    // First, check if we can access location
    if (!navigator.geolocation) {
        showError('This browser cannot provide location data. Try using Chrome or Firefox.');
        return;
    }

    // Ask for the user's location
    navigator.geolocation.getCurrentPosition(
        position => getForecastData(position.coords),
        error => showError('We need access to location to show local weather. Please enable it in browser settings.')
    );
}

// Load weather data from our API
async function getForecastData(coords) {
    try {
        // Request the 5-day forecast
        const response = await fetch(`../api/get-weather.php?lat=${coords.latitude}&lon=${coords.longitude}&type=forecast`);
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        
        displayForecast(data);
    } catch (error) {
        showError('Could not load forecast data: ' + error.message);
    }
}

// Display forecast data
function displayForecast(data) {
    const locationName = document.getElementById('location-name');
    const forecastGrid = document.getElementById('forecast-grid');
    
    // Show which city we're looking at
    locationName.innerHTML = `<h3>Weather in ${data.city.name}</h3>`;
    
    // Get the mid-day forecast for each day
    // This gives the most representative temperature
    const dailyForecasts = data.list.filter(item => item.dt_txt.includes('12:00:00'));
    
    // Build the forecast cards
    let forecastHTML = '';
    dailyForecasts.forEach(day => {
        // Format the date nicely
        const date = new Date(day.dt * 1000);
        forecastHTML += `
            <div class="forecast-card">
                <h3>${date.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' })}</h3>
                <img src="https://openweathermap.org/img/wn/${day.weather[0].icon}@2x.png" 
                     alt="${day.weather[0].description}">
                <div class="temp">${Math.round(day.main.temp)}°C</div>
                <p>${day.weather[0].description}</p>
                <div class="details">
                    <span>Humidity: ${day.main.humidity}%</span>
                    <span>Wind: ${Math.round(day.wind.speed * 3.6)} km/h</span>
                </div>
            </div>
        `;
    });
    
    forecastGrid.innerHTML = forecastHTML;
}

// Show error message
function showError(message) {
    const errorDiv = document.getElementById('forecast-error');
    const forecastGrid = document.getElementById('forecast-grid');
    
    errorDiv.style.display = 'block';
    errorDiv.querySelector('p').textContent = message;
    forecastGrid.innerHTML = '';
}