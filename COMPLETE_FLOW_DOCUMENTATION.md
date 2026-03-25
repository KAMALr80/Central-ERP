# 🚀 Complete Advanced Flow: Sales → Logistics → Agent → Admin

## Flow Summary
```
Customer Location (Sales) → Shipment (Logistics) → Agent Delivery → Admin Real-Time Tracking
```

---

## PHASE 1️⃣: SALES - LOCATION CAPTURE

### Where: `resources/views/sales/create.blade.php`
### Database: `sales` table

#### What Happens:
1. **Sales Manager** creates invoice with customer details
2. **Clicks location button** → Google Maps Opens
3. **Selects delivery address** on map
4. **Coordinates + Address** automatically filled

#### Form Fields Captured:
```html
<input type="hidden" name="customer_id" id="customer_id">
<input type="hidden" name="destination_latitude" id="destination_latitude">
<input type="hidden" name="destination_longitude" id="destination_longitude">
<input type="hidden" name="place_id" id="place_id">
<textarea name="shipping_address" id="shipping_address"></textarea>
<input type="text" name="city">
<input type="text" name="state">
<input type="text" name="pincode">
<input type="checkbox" name="requires_shipping"> (if checked → shipment auto-created)
<input type="text" name="receiver_name">
<input type="text" name="receiver_phone">
```

#### Data Structure:
| Field | Type | Purpose |
|-------|------|---------|
| customer_id | INT | Who is ordering |
| destination_latitude | DECIMAL(10,8) | Delivery point latitude |
| destination_longitude | DECIMAL(10,8) | Delivery point longitude |
| shipping_address | TEXT | Full address |
| place_id | VARCHAR | Google Places ID |
| city | VARCHAR | City name |
| state | VARCHAR | State name |
| pincode | VARCHAR | PIN code |
| receiver_name | VARCHAR | Who to deliver to |
| receiver_phone | VARCHAR | Delivery contact |

#### Code Flow in Controller:
```
📍 File: app/Http/Controllers/Sales/SalesController.php
   └─ public function store(Request $request)
       ├─ Validate: destination_latitude, destination_longitude, shipping_address
       ├─ Create Sale record with location data
       ├─ Check: if requires_shipping == true
       └─ Call: createShipmentFromSale($sale, $request)
```

---

## PHASE 2️⃣: LOGISTICS - SHIPMENT CREATION

### Where: Auto-created from Sales
### Database: `shipments` table

#### createShipmentFromSale() Method:
```php
// File: app/Http/Controllers/Sales/SalesController.php (Line 710)

$shipment = new Shipment();
$shipment->shipment_number = generateShipmentNumber();
$shipment->tracking_number = generateTrackingNumber();

// Copy all location data from sale
$shipment->destination_latitude = $request->destination_latitude;
$shipment->destination_longitude = $request->destination_longitude;
$shipment->shipping_address = $request->shipping_address;
$shipment->city = $request->city;
$shipment->state = $request->state;
$shipment->pincode = $request->pincode;
$shipment->place_id = $request->place_id;

// Copy receiver details
$shipment->receiver_name = $request->receiver_name ?? $sale->customer->name;
$shipment->receiver_phone = $request->receiver_phone ?? $sale->customer->mobile;

// Add value & quantity from sale
$shipment->declared_value = $sale->grand_total;
$shipment->quantity = $sale->items->sum('quantity');
$shipment->weight = $sale->items->sum(weight);

$shipment->status = 'pending';
$shipment->save();

// Create tracking record
$shipment->trackings()->create([
    'status' => 'pending',
    'location' => $request->city,
    'latitude' => $request->destination_latitude,
    'longitude' => $request->destination_longitude,
    'remarks' => 'Shipment created from invoice'
]);
```

#### Shipment Database Fields:
| Field | Type | From | Purpose |
|-------|------|------|---------|
| id | INT | Auto | Unique ID |
| shipment_number | VARCHAR | Generated | Like SHP-2026-000001 |
| tracking_number | VARCHAR | Generated | Public tracking number |
| sale_id | INT | Sale ID | Link to sales |
| customer_id | INT | Sale → Customer | Customer reference |
| destination_latitude | DECIMAL | Sale form | Delivery GPS latitude |
| destination_longitude | DECIMAL | Sale form | Delivery GPS longitude |
| place_id | VARCHAR | Google | Place ID for geocoding |
| shipping_address | TEXT | Sale form | Full address |
| receiver_name | VARCHAR | Sale form | Delivery to whom |
| receiver_phone | VARCHAR | Sale form | Contact number |
| city | VARCHAR | Sale form | City |
| state | VARCHAR | Sale form | State |
| pincode | VARCHAR | Sale form | PIN code |
| declared_value | DECIMAL | Sale amount | Package value |
| quantity | INT | Sale items | Number of items |
| weight | DECIMAL | Product weight | Total weight |
| status | VARCHAR | pending | Current status |
| assigned_to | INT | NULL initially | Agent ID when assigned |
| created_at | TIMESTAMP | system | When created |

#### Status Lifecycle:
```
pending → assigned → picked → in_transit → out_for_delivery → delivered
```

---

## PHASE 3️⃣: LOGISTICS - AGENT ASSIGNMENT

### Where: Logistics Dashboard
### Route: `POST /logistics/shipments/{id}/assign-agent`

#### What Admin Does:
1. **Navigate to:** Logistics → Shipments
2. **Select Shipment** (status = pending)
3. **Click "Assign Agent"** button
4. **Choose Agent** from list
5. **Confirm Assignment**

#### Database Update:
```sql
UPDATE shipments 
SET assigned_to = {agent_id},
    status = 'assigned'
WHERE id = {shipment_id}
```

#### Agent Record:
```
delivery_agents table
├─ id (Primary Key)
├─ user_id (FK to users)
├─ name
├─ phone
├─ email
├─ vehicle_type
├─ vehicle_number
├─ current_latitude (updated by app)
├─ current_longitude (updated by app)
├─ current_speed (updated by app)
├─ battery_level (updated by app)
├─ status (online/offline/busy)
└─ last_location_update
```

---

## PHASE 4️⃣: AGENT - SEES ASSIGNMENT

### Where: Agent Mobile App / Dashboard
### Route: `agent/deliveries/assigned`

#### What Agent Sees:
```
My Assigned Deliveries
┌─────────────────────────────┐
│ SHP-2026-000001             │  ← Shipment Number
│ Track: TRK-2026-000001      │  ← Tracking Number
│ ────────────────────────────│
│ Name: Rajesh Kumar          │
│ Phone: 98765-43210          │
│ Address: 123 Main St, City  │  ← ADDRESS FROM SALES
│ ────────────────────────────│
│ Delivery Instructions:      │
│ Gate no. 2, Ring bell 3x    │
│ ────────────────────────────│
│ [Start Delivery -->]        │ ← Button to start
└─────────────────────────────┘
```

### File: `resources/views/agent/delivery/show.blade.php`
- Shows all shipment details
- Shows receiver address
- **PROBLEM: NO MAP YET** - Agent can't see destination on map
- Button: "Track & Complete Delivery" → Takes to live tracking

---

## PHASE 5️⃣: AGENT - LIVE DELIVERY TRACKING

### Where: Agent Mobile App
### Route: `agent/tracking/live/{shipment_id}`

#### What Should Happen:
```
Agent Phone Screen (During Delivery)
┌──────────────────────────────────┐
│    🗺️  LIVE DELIVERY MAP          │
├──────────────────────────────────┤
│                                  │
│   [Map with:]                    │
│   📌 Agent Location (Green)      │ ← Real-time GPS
│   📍 Destination (Red)           │ ← From sales location
│   🚶 Route (Blue Line)           │ ← Google Maps route
│                                  │
├──────────────────────────────────┤
│ Speed: 🚴 25 km/h               │ ← From GPS
│ Distance: 4.5 km remaining       │ ← Calculated
│ ETA: 8 minutes                   │
│ Battery: 85%                     │
├──────────────────────────────────┤
│ [Mark as Delivering]             │
│ [Complete Delivery]              │
└──────────────────────────────────┘
```

#### API Flow:
```
Agent App (Every 5-10 seconds)
    ↓
POST /api/agent-location/update
{
    latitude: 22.5247,
    longitude: 72.9555,
    speed: 25,
    battery_level: 85,
    heading: 270,
    shipment_id: 156
}
    ↓
Backend: AgentLocationController@updateLocation()
    ├─ Update: delivery_agents table
    │   ├─ current_latitude
    │   ├─ current_longitude
    │   ├─ current_speed
    │   └─ battery_level
    └─ Create: agent_locations table (history)
        ├─ agent_id
        ├─ shipment_id
        ├─ latitude
        ├─ longitude
        ├─ speed
        ├─ battery_level
        ├─ heading
        └─ recorded_at
```

---

## PHASE 6️⃣: ADMIN - REAL-TIME TRACKING

### Where: Admin Dashboard
### Route: `admin/tracking/agent/{shipment_id}`

#### Admin Sees (Every 3 seconds polling):
```
Admin Dashboard (Desktop)
┌─────────────────────────────────────────────┐
│     📦 Tracking: SHP-2026-000001            │
├─────────────────────────────────────────────┤
│                                             │
│   Agent: Rajesh Kumar                       │
│   Status: Out for Delivery ✅              │
│   Current Speed: 🚴 45 km/h                │
│   Battery: 🔋 78%                          │
│   Location Updated: 2 seconds ago           │
│                                             │
│  ┌──────────────────────────────┐           │
│  │   🗺️  LIVE DELIVERY MAP       │           │
│  │                              │           │
│  │  📌 Agent: 22.5247, 72.9555 │ ← Agent  │
│  │  📍 Destination: 22.5311... │ ← Sales  │
│  │  🛣️  Route: 7.5 km, 18 min  │           │
│  │                              │           │
│  └──────────────────────────────┘           │
│                                             │
│  Delivery Details:                          │
│  ├─ Receiver: Rajesh Kumar                  │
│  ├─ Address: 123 Main St, City             │
│  ├─ Phone: 98765-43210                     │
│  ├─ Item: Product Name × 2                 │
│  ├─ Value: ₹5,000                          │
│  └─ Payment: COD                           │
│                                             │
│  Activity Timeline:                         │
│  ├─ 14:05 - Shipment Created               │
│  ├─ 14:10 - Assigned to Rajesh             │
│  ├─ 14:15 - Picked up from warehouse       │
│  ├─ 14:25 - In Transit                     │
│  └─ 14:45 - Out for Delivery               │
│                                             │
└─────────────────────────────────────────────┘
```

#### API Polling:
```
Admin Dashboard (Browser)
    ↓ Every 3 seconds
GET /api/track/agent/realtime/{shipment_id}
    ↓
Backend: AgentLocationController@getShipmentAgentLocation()
{
    success: true,
    data: {
        agent: {
            id, name, phone, email,
            photo, vehicle_type, vehicle_number,
            rating, total_deliveries,
            status, is_online
        },
        location: {
            latitude: 22.5247,
            longitude: 72.9555,
            speed_kmh: 45,
            battery_level: 78,
            heading: 270,
            accuracy: 5,
            recorded_at_human: "2 seconds ago",
            is_recent: true
        },
        shipment: {
            id, tracking_number, status,
            destination: {
                latitude: 22.5311,
                longitude: 72.9644,
                address: "123 Main St..."
            }
        }
    }
}
    ↓
JavaScript: Updates Map & Display
├─ Agent Marker Position
├─ Speed Badge
├─ Battery Indicator
├─ Live/Offline Status
├─ Timestamp
├─ Route Recalculation
└─ Distance/Duration
```

---

## DATABASE RELATIONSHIPS

```
sales table
    │
    ├─ destination_latitude
    ├─ destination_longitude
    ├─ shipping_address
    └─ (All location data)
            ↓
            │
        [createShipmentFromSale]
            │
            ↓
shipments table
    ├─ id
    ├─ destination_latitude
    ├─ destination_longitude
    ├─ shipping_address
    ├─ assigned_to (agent_id)
    └─ status
            ↓
            │
    [Agent Assigned]
            │
            ↓
delivery_agents table
    ├─ id
    ├─ current_latitude
    ├─ current_longitude
    ├─ current_speed
    ├─ battery_level
    └─ last_location_update
            ↓ (Updates every 5-10 seconds)
            │
agent_locations table (History)
    ├─ agent_id
    ├─ shipment_id
    ├─ latitude
    ├─ longitude
    ├─ speed
    ├─ battery_level
    ├─ heading
    └─ recorded_at
```

---

## KEY IMPROVEMENTS NEEDED

### ✅ Already Implemented:
1. ✅ Sales form captures location (Google Maps integration)
2. ✅ Shipment auto-created from sales with location
3. ✅ Agent can be assigned to shipment
4. ✅ Real-time location API endpoint exists
5. ✅ Admin dashboard exists for tracking
6. ✅ Live polling is implemented

### ⚠️ Enhancements Needed:
1. **Agent Delivery Page Map** - Add destination map on `agent/delivery/show.blade.php`
   - Show destination marker
   - Show address on map
   - Show receiver phone clickable

2. **Agent Live Tracking Map** - Enhance `agent/tracking/live` 
   - Add destination marker (red)
   - Add current location marker (green)
   - Show route to destination
   - Real-time speed display
   - Battery level display

3. **Admin Shipment Show Page Map** - Already done! ✅
   - Shows agent + destination
   - Real-time updates every 3 seconds
   - Speed, battery, status display

---

## DATA FLOW SUMMARY

```
┌─────────────────────────────────────────────────────────────────────┐
│ 1. SALES MANAGER CREATES SALE WITH LOCATION                        │
│    ↓ (destination_lat, destination_lng, shipping_address)          │
│                                                                      │
│ 2. SALE SAVED TO DATABASE                                          │
│    ↓ (requires_shipping = true)                                    │
│                                                                      │
│ 3. SHIPMENT AUTO-CREATED FROM SALE                                │
│    ↓ (All location data copied)                                    │
│                                                                      │
│ 4. ADMIN ASSIGNS AGENT TO SHIPMENT                                │
│    ↓ (shipments.assigned_to = agent_id)                           │
│                                                                      │
│ 5. AGENT SEES SHIPMENT DETAILS                                    │
│    ↓ (Address visible but NO MAP)                                 │
│                                                                      │
│ 6. AGENT STARTS DELIVERY                                           │
│    ↓ (Status = picked → in_transit → out_for_delivery)            │
│                                                                      │
│ 7. AGENT GPS APP SENDS LOCATION                                   │
│    ↓ (Every 5-10 seconds: POST /api/agent-location/update)        │
│                                                                      │
│ 8. LOCATION SAVED TO AGENT_LOCATIONS TABLE                        │
│    ↓ (As history for tracking)                                    │
│                                                                      │
│ 9. ADMIN POLLS REAL-TIME LOCATION                                 │
│    ↓ (Every 3 seconds: GET /api/track/agent/realtime)            │
│                                                                      │
│ 10. ADMIN SEES:                                                   │
│     ✓ Agent current location (GPS)                               │
│     ✓ Destination location (from sales)                          │
│     ✓ Route between them                                          │
│     ✓ Distance & Duration                                         │
│     ✓ Speed (real-time)                                           │
│     ✓ Battery level                                               │
│     ✓ Live/Offline status                                         │
│                                                                      │
│ 11. AGENT COMPLETES DELIVERY                                     │
│     ↓ (Status = delivered, with signature/photo)                 │
│                                                                      │
│ 12. TRACKING UPDATED                                              │
│     ✓ Shipment status = delivered                                │
│     ✓ Tracking record updated                                    │
│     ✓ Agent rating/performance metrics updated                   │
└─────────────────────────────────────────────────────────────────────┘
```

---

## FILES INVOLVED

### Sales Module:
- `app/Http/Controllers/Sales/SalesController.php` - store() & createShipmentFromSale()
- `resources/views/sales/create.blade.php` - Location form

### Logistics Module:
- `app/Models/Shipment.php` - Destination data storage
- `app/Http/Controllers/Logistics/ShipmentsController.php` - Assignment
- `resources/views/logistics/shipments/show.blade.php` - Admin view ✅

### Agent Module:
- `app/Http/Controllers/Agent/DeliveryController.php` - Agent deliveries
- `resources/views/agent/delivery/show.blade.php` - Agent sees shipment ⚠️ (No map)
- `resources/views/agent/tracking/live.blade.php` - Live tracking ⚠️ (Needs enhancement)

### API:
- `app/Http/Controllers/Api/AgentLocationController.php` - updateLocation() & getShipmentAgentLocation() ✅
- `routes/api.php` - API routes ✅

### Admin:
- `resources/views/admin/tracking/agent.blade.php` - Admin tracking dashboard ✅

---

## NEXT STEPS TO COMPLETE

1. **Enhance agent/delivery/show.blade.php** → Add destination map preview
2. **Enhance agent/tracking/live.blade.php** → Add real-time map with both markers
3. **Test end-to-end flow** → Sales → Shipment → Assignment → Delivery → Completion
4. **Mobile app integration** → Ensure GPS update happens every 5-10 seconds
5. **Performance optimization** → Cache admin polling responses

