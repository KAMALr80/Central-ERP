# Google Cloud Maps Integration Guide

## Overview
This application uses Google Cloud Maps API for real-time location tracking of delivery agents. The system supports live tracking for both admin (viewing all agents) and delivery agents (tracking their own deliveries).

## Setup Instructions

### 1. Get Google Maps API Key

#### Step 1: Create a Google Cloud Project
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - **Maps JavaScript API** - For displaying maps
   - **Directions API** - For route calculation
   - **Distance Matrix API** - For calculating distances
   - **Geocoding API** - For address to coordinates conversion
   - **Places API** - For place search and autocomplete

#### Step 2: Create an API Key
1. Navigate to **APIs & Services** → **Credentials**
2. Click **Create Credentials** → **API Key**
3. Copy the generated API key

#### Step 3: Restrict Your API Key (Recommended)
1. Click on your API key in the credentials list
2. Under **Key restrictions**, select:
   - **Application restrictions**: HTTP referrers (websites)
   - **API restrictions**: Select the specific APIs enabled above
3. Add your domain(s) in the HTTP referrer restriction

### 2. Configure Application

#### Method 1: Environment Variables
Add to your `.env` file:
```env
# Google Cloud Maps API Key
GOOGLE_MAPS_API_KEY=AIzaSyC8Jc4HBUsp9w_I9-rUTBS3t7v0atcBzWc

# Map Defaults (Warehouse coordinates)
GOOGLE_DEFAULT_LAT=22.524768
GOOGLE_DEFAULT_LNG=72.955568

# Warehouse Location (for agent tracking)
WAREHOUSE_LAT=22.524768
WAREHOUSE_LNG=72.955568

# Logistics Configuration
ALLOWED_RADIUS_KM=1
OSRM_URL=http://router.project-osrm.org
```

**Note:** The warehouse coordinates are used as the default map center for all tracking views. Update these values to your warehouse location.

#### Method 2: Using Config Files
The API key is automatically read from `config/services.php`:
```php
'google' => [
    'maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    'default_lat' => env('GOOGLE_DEFAULT_LAT', 22.524768),
    'default_lng' => env('GOOGLE_DEFAULT_LNG', 72.955568),
],
```

## API Endpoints

### Get Maps Configuration (Public)
**Endpoint:** `GET /api/config/maps`
**Authentication:** Not required
**Response:**
```json
{
  "success": true,
  "data": {
    "google_maps_api_key": "YOUR_API_KEY",
    "default_latitude": 22.524768,
    "default_longitude": 72.955568,
    "warehouse_latitude": 22.524768,
    "warehouse_longitude": 72.955568,
    "osrm_url": "http://router.project-osrm.org",
    "map_provider": "google_maps",
    "allowed_radius_km": 1
  }
}
```

### Get Location Configuration (Authenticated)
**Endpoint:** `GET /api/config/location`
**Authentication:** Required (Bearer token)
**Response:**
```json
{
  "success": true,
  "timestamp": "2026-03-24T10:30:00Z",
  "config": {
    "google_api_key": "YOUR_API_KEY",
    "warehouse": {
      "lat": 22.524768,
      "lng": 72.955568,
      "name": "Main Warehouse"
    },
    "default_map_center": {
      "lat": 22.524768,
      "lng": 72.955568
    },
    "map_zoom_levels": {
      "street": 18,
      "city": 13,
      "region": 10,
      "country": 6
    }
  }
}
```

### Update Agent Location
**Endpoint:** `POST /api/agent-location/update`
**Authentication:** Required (Bearer token)
**Payload:**
```json
{
  "latitude": 22.524768,
  "longitude": 72.955568,
  "accuracy": 5,
  "speed": 25.5,
  "heading": 180,
  "battery_level": 85,
  "shipment_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "message": "Location updated successfully",
  "data": {
    "latitude": 22.524768,
    "longitude": 72.955568,
    "speed": 25.5,
    "battery_level": 85,
    "updated_at": "2026-03-24T10:30:00Z",
    "is_online": true
  }
}
```

## Frontend Integration

### Using the Delivery Map Component
```blade
@component('components.delivery-map')
@endcomponent
```

The component automatically:
1. Fetches the Google Maps API key from `/api/config/maps`
2. Initializes Leaflet.js map
3. Provides functions for updating agent locations
4. Handles map controls and legends

### JavaScript API
```javascript
// Update agent location on map
window.updateAgentLocation(lat, lng, agentName, speed, status);

// Add warehouse marker
window.addWarehouse(lat, lng, 'Main Warehouse');

// Add delivery destination
window.addDestination(lat, lng, 'Receiver Name', 'Address');

// Draw route
window.drawRoute([[lat1, lng1], [lat2, lng2]]);

// Draw path taken
window.drawPath(locationHistory);
```

## Real-time Location Tracking

### For Delivery Agents
The agent tracking view automatically:
1. Uses HTML5 Geolocation API to get current position every 5 seconds
2. Sends location data to `/api/agent-location/update`
3. Displays live map updates
4. Shows speed, distance, and ETA to destination
5. Records battery level

### For Admins
The admin tracking dashboard:
1. Fetches agent location every 3 seconds
2. Updates agent markers on map
3. Shows agent details (name, vehicle, status)
4. Handles multiple agent tracking
5. Displays route history

## Security Best Practices

1. **API Key Restriction:**
   - Always restrict API keys to specific domains/IPs
   - Use HTTP referrer restrictions in production
   - Separate keys for frontend and backend if possible

2. **Backend Validation:**
   - Validate all location data server-side
   - Check if agent is authorized for the shipment
   - Rate limit location update requests

3. **Data Privacy:**
   - Store location history securely
   - Implement access controls for location data
   - Clear old location data regularly

4. **CORS Configuration:**
   - Configure CORS properly in Google Cloud console
   - Only allow requests from trusted origins
   - Use POST requests for sensitive data

## Service Implementation

The `GoogleMapsService` class provides methods for:
- **Geocoding:** Convert addresses to coordinates
- **Directions:** Calculate routes between points
- **Distance Matrix:** Calculate distances between multiple points
- **Places:** Search for locations
- **Reverse Geocoding:** Convert coordinates to addresses

### Usage Example:
```php
$googleMaps = new GoogleMapsService();

// Geocode address
$result = $googleMaps->geocodeAddress('123 Main St, City');

// Calculate distance
$distance = $googleMaps->calculateDistance($lat1, $lng1, $lat2, $lng2);

// Get directions
$directions = $googleMaps->getDirections($origin, $destination);
```

## Troubleshooting

### "API key not valid" Error
- Verify API key is correct in `.env`
- Check if Maps JavaScript API is enabled
- Ensure domain is whitelisted in API key restrictions

### Map not showing
- Check browser console for errors
- Verify Leaflet.js is loaded
- Check if map container has height/width set

### Location updates not working
- Check if HTTPS is enabled (Geolocation requires HTTPS)
- Verify user granted location permission
- Check API endpoint `/api/agent-location/update`
- Ensure authentication token is valid

### High API costs
- Implement caching for geocoding results
- Batch requests where possible
- Use OSRM for route optimization instead of Google Directions
- Monitor usage in Google Cloud Console

## Configuration Examples

### Production Setup (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_FORCE_HTTPS=true

GOOGLE_MAPS_API_KEY=AIzaSyC8Jc4HBUsp9w_I9-rUTBS3t7v0atcBzWc
GOOGLE_DEFAULT_LAT=22.524768
GOOGLE_DEFAULT_LNG=72.955568
WAREHOUSE_LAT=22.524768
WAREHOUSE_LNG=72.955568
ALLOWED_RADIUS_KM=1
OSRM_URL=https://your-osrm-server.com
```

### Local Development Setup (.env)
```env
APP_ENV=local
APP_DEBUG=true
APP_FORCE_HTTPS=false

GOOGLE_MAPS_API_KEY=AIzaSyC8Jc4HBUsp9w_I9-rUTBS3t7v0atcBzWc
GOOGLE_DEFAULT_LAT=22.524768
GOOGLE_DEFAULT_LNG=72.955568
WAREHOUSE_LAT=22.524768
WAREHOUSE_LNG=72.955568
ALLOWED_RADIUS_KM=1
```

## Support & Documentation

- [Google Cloud Maps Platform](https://developers.google.com/maps)
- [Leaflet.js Documentation](https://leafletjs.com/)
- [HTML5 Geolocation API](https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API)
