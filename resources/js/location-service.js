// resources/js/location-service.js

class LocationService {
    constructor() {
        this.watchId = null;
        this.currentPosition = null;
        this.listeners = [];
        this.isActive = false;
        this.updateInterval = null;
    }

    // Start watching location
    start() {
        if (this.isActive) return;

        if (!navigator.geolocation) {
            console.error('Geolocation not supported');
            return;
        }

        this.watchId = navigator.geolocation.watchPosition(
            (position) => {
                const location = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                    speed: position.coords.speed ? position.coords.speed * 3.6 : 0,
                    accuracy: position.coords.accuracy,
                    heading: position.coords.heading || 0,
                    timestamp: new Date().toISOString()
                };

                this.currentPosition = location;
                this.notifyListeners(location);
                this.sendToServer(location);
            },
            (error) => {
                console.error('Geolocation error:', error);
                this.notifyError(error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );

        this.isActive = true;

        // Also send location every 5 seconds as backup
        this.updateInterval = setInterval(() => {
            if (this.currentPosition) {
                this.sendToServer(this.currentPosition);
            }
        }, 5000);
    }

    // Stop watching location
    stop() {
        if (this.watchId) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
        this.isActive = false;
    }

    // Add listener for location updates
    addListener(callback) {
        this.listeners.push(callback);
        if (this.currentPosition) {
            callback(this.currentPosition);
        }
        return () => {
            this.listeners = this.listeners.filter(cb => cb !== callback);
        };
    }

    // Notify all listeners
    notifyListeners(location) {
        this.listeners.forEach(callback => {
            try {
                callback(location);
            } catch (e) {
                console.error('Listener error:', e);
            }
        });
    }

    // Notify error
    notifyError(error) {
        this.listeners.forEach(callback => {
            try {
                callback(null, error);
            } catch (e) {}
        });
    }

    // Send location to server
    async sendToServer(location) {
        try {
            await fetch('/agent/location/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    latitude: location.lat,
                    longitude: location.lng,
                    speed: location.speed,
                    accuracy: location.accuracy,
                    heading: location.heading
                })
            });
        } catch (error) {
            console.error('Failed to send location:', error);
        }
    }

    // Get current location
    getCurrentLocation() {
        return this.currentPosition;
    }
}

// Create singleton instance
window.locationService = new LocationService();
