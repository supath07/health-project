// Core API functions
async function fetchWeather(lat, lon) {
    const response = await fetch(`../api/get-weather.php?lat=${lat}&lon=${lon}&type=weather`);
    const data = await response.json();
    if (data.error) throw new Error(data.error);
    return data;
}

async function fetchAqi(lat, lon) {
    const response = await fetch(`../api/get-weather.php?lat=${lat}&lon=${lon}&type=aqi`);
    const data = await response.json();
    if (data.error) throw new Error(data.error);
    return data;
}

/**
 * Weather Information Manager
 * This module handles all weather and air quality data for our app
 * Written by: Your Name
 * Last updated: Nov 1, 2025
 */

// When the page loads, set up the right features
document.addEventListener('DOMContentLoaded', () => {
    // Figure out which page we're on
    const onDashboard = document.getElementById('weather-card') && document.getElementById('aqi-card');
    const onAdvicePage = document.getElementById('advice-container');
    const onForecastPage = document.getElementById('forecast-container');

    // Start the right features for this page
    if (onDashboard) {
        console.log("Setting up your dashboard...");
        initializeDashboard();
    } 
    else if (onAdvicePage) {
        console.log("Getting your health advice ready...");
        initializeAdvicePage();
    }
});

// Set up the dashboard with weather and air quality info
function initializeDashboard() {
    // First, make sure we can get the user's location
    if (!navigator.geolocation) {
        handleGeoError(null, ['weather-card', 'aqi-card']);
        return;
    }

    // Get their location and show weather info
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const { latitude: lat, longitude: lon } = position.coords;
            try {
                // Get weather and air quality at the same time
                const [weather, aqi] = await Promise.all([
                    fetchWeather(lat, lon),
                    fetchAqi(lat, lon)
                ]);
                
                // Show the information nicely
                updateWeatherCard(weather, 'weather-card');
                updateAqiCard(aqi, 'aqi-card');
            } catch (error) {
                // Something went wrong - let the user know
                console.error("Couldn't get weather info:", error);
                handleApiError(['weather-card', 'aqi-card'], error);
            }
        },
        // If we can't get their location, show a helpful message
        (error) => handleGeoError(error, ['weather-card', 'aqi-card'])
    );
}

// Set up the advice page with personalized recommendations
function initializeAdvicePage() {
    // Show any health logs we have
    displayHealthLogs();

    // Check if we can get location info
    if (!navigator.geolocation) {
        handleGeoError(null, ['advice-weather-card', 'advice-aqi-card']);
        return;
    }

    // Get location and create personalized advice
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const { latitude: lat, longitude: lon } = position.coords;
            try {
                // Get all the environmental data we need
                const [weather, aqi] = await Promise.all([
                    fetchWeather(lat, lon),
                    fetchAqi(lat, lon)
                ]);
                
                // Update all the cards with fresh data
                updateWeatherCard(weather, 'advice-weather-card');
                updateAqiCard(aqi, 'advice-aqi-card');
                
                // Create personalized health advice
                generateAdvice(weather, aqi, window.recentHealthLogs || []);
            } catch (error) {
                // Handle any problems smoothly
                console.error("Couldn't load environmental data:", error);
                handleApiError(
                    ['advice-weather-card', 'advice-aqi-card', 'advice-container'], 
                    error
                );
            }
        },
        // If location isn't available, let them know
        (error) => handleGeoError(error, [
            'advice-weather-card', 
            'advice-aqi-card', 
            'advice-container'
        ])
    );
}

// Display health logs in the advice page
function displayHealthLogs() {
    const logsCard = document.getElementById('my-logs-card');
    if (!logsCard || !window.recentHealthLogs) return;

    let logsHtml = '<h4>Your Recent Logs (7 days)</h4>';
    if (window.recentHealthLogs.length === 0) {
        logsHtml += '<p>No recent health logs found.</p>';
    } else {
        logsHtml += '<ul class="logs-list">';
        window.recentHealthLogs.forEach(log => {
            logsHtml += `<li>${new Date(log.log_date).toLocaleDateString()}: ${log.symptom}</li>`;
        });
        logsHtml += '</ul>';
    }
    logsCard.innerHTML = logsHtml;
}

// Generate personalized health advice
function generateAdvice(weather, aqi, logs) {
    const container = document.getElementById('advice-container');
    if (!container) return;

    let advice = '<h3>Today\'s Personal Health Tips</h3><div class="advice-content">';
    
    // Add greeting based on time of day
    const hour = new Date().getHours();
    let greeting = hour < 12 ? "<i><b>Good morning</i></b>" : hour < 18 ? "<i><b>Good afternoon</b></i>" : "<i><b>Good evening</b></i>";
    advice += `<p>${greeting}! Here's your personalized health advice based on current conditions:</p>`;
    
    // Weather-based advice
    const temp = weather.main.temp;
    const humidity = weather.main.humidity;
    const description = weather.weather[0].description.toLowerCase();

    // Always add current weather conditions first
    advice += `<p>🌡️ Current temperature is ${Math.round(temp)}°C with ${description}.</p>`;
    
    // Temperature-specific advice
    if (temp < 10) {
        advice += '<p>❄️ It\'s quite cold today: Bundle up well and consider indoor activities if possible.</p>';
    } else if (temp > 30) {
        advice += '<p>🌡️ High temperature alert: Stay hydrated and avoid prolonged sun exposure. Find shade when outdoors.</p>';
    } else if (temp > 25) {
        advice += '<p>🌤️ Warm conditions: Remember your sunscreen and stay hydrated.</p>';
    } else if (temp < 15) {
        advice += '<p>🌥️ Cool weather: Dress in layers to stay comfortable.</p>';
    }

    // Weather condition advice
    if (description.includes('rain')) {
        advice += '<p>☔ Rainy conditions: Keep dry and carry an umbrella.</p>';
    } else if (description.includes('snow')) {
        advice += '<p>❄️ Snowy conditions: Wear warm, waterproof clothing.</p>';
    }

    // AQI-based advice
    const aqiLevel = aqi.list[0].main.aqi;
    if (aqiLevel >= 4) {
        advice += '<p>😷 Poor air quality: Limit outdoor activities and wear a mask if needed.</p>';
    } else if (aqiLevel === 3) {
        advice += '<p>⚠️ Moderate air quality: Consider reducing extended outdoor activities.</p>';
    }

    // Log-based personalized advice
    if (logs && logs.length > 0) {
        const symptoms = logs.map(log => log.symptom.toLowerCase());
        
        if (symptoms.some(s => s.includes('cough') || s.includes('breath'))) {
            advice += '<p>🫁 Recent respiratory symptoms noted:</p>';
            advice += '<ul>';
            advice += '<li>Monitor air quality levels</li>';
            advice += '<li>Consider using an air purifier indoors</li>';
            if (humidity > 60) {
                advice += '<li>High humidity may affect breathing - consider a dehumidifier</li>';
            }
            advice += '</ul>';
        }
        
        if (symptoms.some(s => s.includes('headache'))) {
            advice += '<p>🤕 Recent headaches noted:</p>';
            advice += '<ul>';
            advice += '<li>Stay hydrated</li>';
            advice += '<li>Monitor for weather-related triggers</li>';
            advice += '<li>Ensure good ventilation indoors</li>';
            advice += '</ul>';
        }

        if (symptoms.some(s => s.includes('hay fever') || s.includes('hayfever'))) {
            advice += '<p>🤒Recent Hay Fever noted: </p>';
            advice += '<ul>'
            advice += '<li>Wear protective eye wear and a mask when outside</li>';
            advice += '<li>Stay inside on high pollen days</li>'
            advice += '<li>Drink soothing fluids</li>'
        }
    }

    advice += '</div>';
    container.innerHTML = advice;
}

// Update weather display
function updateWeatherCard(data, elementId) {
    const card = document.getElementById(elementId);
    if (!card) return;

    const titleTag = (elementId === 'weather-card') ? 'h3' : 'h4';
    
    card.innerHTML = 
        `<${titleTag}>Weather in ${data.name}</${titleTag}>
         <p class="api-value">${Math.round(data.main.temp)}°C</p>
         <p>${data.weather[0].description}</p>
         <p>Feels like: ${Math.round(data.main.feels_like)}°C</p>
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
        4: { text: 'Poor', color: '#d83605ff' },
        5: { text: 'Very Poor', color: '#b71c1c' }
    };

    const titleTag = (elementId === 'aqi-card') ? 'h3' : 'h4';
    
    card.innerHTML = 
        `<${titleTag}>Air Quality Index</${titleTag}>
         <p class="api-value" style="color: ${aqiLevels[aqi].color}">${aqiLevels[aqi].text}</p>
         <p>AQI Level: ${aqi}/5</p>`;
}

// Error handlers
function handleGeoError(error, elementIds) {
    const message = error ? 
        'Error getting location. Please enable location services.' : 
        'Your browser doesn\'t support geolocation.';
    
    elementIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = `<p class="error-message">${message}</p>`;
        }
    });
}

function handleApiError(elementIds, error) {
    elementIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = `<p class="error-message">Error: ${error.message}</p>`;
        }
    });
}