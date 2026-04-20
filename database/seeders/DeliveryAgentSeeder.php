<?php

namespace Database\Seeders;

use App\Models\DeliveryAgent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DeliveryAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DeliveryAgent::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $agents = [
            [
                'name' => 'Rajesh Kumar',
                'email' => 'rajesh.kumar@example.com',
                'phone' => '9876543210',
                'alternate_phone' => '9876543211',
                'address' => '123, MG Road, Indiranagar',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode' => '560038',
                'vehicle_type' => 'bike',
                'vehicle_number' => 'KA01AB1234',
                'license_number' => 'KA01202300001',
                'employment_type' => 'full_time',
                'joining_date' => '2024-01-15',
                'salary' => 18000,
                'commission_type' => 'percentage',
                'commission_value' => 5,
                'service_areas' => json_encode(['Bangalore', 'Mysore']),
                'total_deliveries' => 1250,
                'successful_deliveries' => 1187,
                'rating' => 4.7,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@example.com',
                'phone' => '9876543212',
                'alternate_phone' => '9876543213',
                'address' => '456, Lokhandwala Complex',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400053',
                'vehicle_type' => 'scooter',
                'vehicle_number' => 'MH02CD5678',
                'license_number' => 'MH02202300002',
                'employment_type' => 'full_time',
                'joining_date' => '2024-02-01',
                'salary' => 20000,
                'commission_type' => 'fixed',
                'commission_value' => 30,
                'service_areas' => json_encode(['Mumbai', 'Thane']),
                'total_deliveries' => 980,
                'successful_deliveries' => 931,
                'rating' => 4.8,
                'status' => 'busy',
                'is_active' => true,
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit.patel@example.com',
                'phone' => '9876543214',
                'alternate_phone' => '9876543215',
                'address' => '789, Satellite Road',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380015',
                'vehicle_type' => 'bike',
                'vehicle_number' => 'GJ01EF9012',
                'license_number' => 'GJ01202300003',
                'employment_type' => 'part_time',
                'joining_date' => '2024-03-10',
                'salary' => 12000,
                'commission_type' => 'percentage',
                'commission_value' => 6,
                'service_areas' => json_encode(['Ahmedabad', 'Gandhinagar']),
                'total_deliveries' => 450,
                'successful_deliveries' => 423,
                'rating' => 4.5,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'name' => 'Sunita Reddy',
                'email' => 'sunita.reddy@example.com',
                'phone' => '9876543216',
                'alternate_phone' => '9876543217',
                'address' => '321, Jubilee Hills',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'pincode' => '500033',
                'vehicle_type' => 'scooter',
                'vehicle_number' => 'TS03GH3456',
                'license_number' => 'TS03202300004',
                'employment_type' => 'full_time',
                'joining_date' => '2024-01-20',
                'salary' => 19000,
                'commission_type' => 'fixed',
                'commission_value' => 25,
                'service_areas' => json_encode(['Hyderabad', 'Secunderabad']),
                'total_deliveries' => 890,
                'successful_deliveries' => 845,
                'rating' => 4.6,
                'status' => 'busy',
                'is_active' => true,
            ],
            [
                'name' => 'Vikram Singh',
                'email' => 'vikram.singh@example.com',
                'phone' => '9876543218',
                'alternate_phone' => '9876543219',
                'address' => '567, Connaught Place',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'pincode' => '110001',
                'vehicle_type' => 'bike',
                'vehicle_number' => 'DL04IJ7890',
                'license_number' => 'DL04202300005',
                'employment_type' => 'full_time',
                'joining_date' => '2024-02-15',
                'salary' => 22000,
                'commission_type' => 'percentage',
                'commission_value' => 4,
                'service_areas' => json_encode(['Delhi', 'Noida', 'Gurugram']),
                'total_deliveries' => 1100,
                'successful_deliveries' => 1045,
                'rating' => 4.9,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'name' => 'Kavita Joshi',
                'email' => 'kavita.joshi@example.com',
                'phone' => '9876543220',
                'alternate_phone' => '9876543221',
                'address' => '890, Koregaon Park',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411001',
                'vehicle_type' => 'cycle',
                'vehicle_number' => 'MH05KL1234',
                'license_number' => 'MH05202300006',
                'employment_type' => 'part_time',
                'joining_date' => '2024-03-05',
                'salary' => 10000,
                'commission_type' => 'fixed',
                'commission_value' => 20,
                'service_areas' => json_encode(['Pune']),
                'total_deliveries' => 320,
                'successful_deliveries' => 304,
                'rating' => 4.4,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'name' => 'Rahul Verma',
                'email' => 'rahul.verma@example.com',
                'phone' => '9876543222',
                'alternate_phone' => '9876543223',
                'address' => '432, Salt Lake City',
                'city' => 'Kolkata',
                'state' => 'West Bengal',
                'pincode' => '700091',
                'vehicle_type' => 'bike',
                'vehicle_number' => 'WB06MN5678',
                'license_number' => 'WB06202300007',
                'employment_type' => 'full_time',
                'joining_date' => '2024-01-25',
                'salary' => 17000,
                'commission_type' => 'percentage',
                'commission_value' => 5,
                'service_areas' => json_encode(['Kolkata', 'Howrah']),
                'total_deliveries' => 780,
                'successful_deliveries' => 741,
                'rating' => 4.5,
                'status' => 'offline',
                'is_active' => false,
            ],
            [
                'name' => 'Anjali Desai',
                'email' => 'anjali.desai@example.com',
                'phone' => '9876543224',
                'alternate_phone' => '9876543225',
                'address' => '765, Alwarpet',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'pincode' => '600018',
                'vehicle_type' => 'scooter',
                'vehicle_number' => 'TN07OP9012',
                'license_number' => 'TN07202300008',
                'employment_type' => 'full_time',
                'joining_date' => '2024-02-20',
                'salary' => 18000,
                'commission_type' => 'fixed',
                'commission_value' => 28,
                'service_areas' => json_encode(['Chennai']),
                'total_deliveries' => 670,
                'successful_deliveries' => 636,
                'rating' => 4.7,
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'name' => 'Suresh Nair',
                'email' => 'suresh.nair@example.com',
                'phone' => '9876543226',
                'alternate_phone' => '9876543227',
                'address' => '234, Panampilly Nagar',
                'city' => 'Kochi',
                'state' => 'Kerala',
                'pincode' => '682036',
                'vehicle_type' => 'bike',
                'vehicle_number' => 'KL08QR3456',
                'license_number' => 'KL08202300009',
                'employment_type' => 'part_time',
                'joining_date' => '2024-03-12',
                'salary' => 14000,
                'commission_type' => 'percentage',
                'commission_value' => 5.5,
                'service_areas' => json_encode(['Kochi', 'Ernakulam']),
                'total_deliveries' => 410,
                'successful_deliveries' => 389,
                'rating' => 4.6,
                'status' => 'busy',
                'is_active' => true,
            ],
            [
                'name' => 'Meera Iyer',
                'email' => 'meera.iyer@example.com',
                'phone' => '9876543228',
                'alternate_phone' => '9876543229',
                'address' => '567, Race Course Road',
                'city' => 'Coimbatore',
                'state' => 'Tamil Nadu',
                'pincode' => '641018',
                'vehicle_type' => 'scooter',
                'vehicle_number' => 'TN09ST6789',
                'license_number' => 'TN09202300010',
                'employment_type' => 'full_time',
                'joining_date' => '2024-01-30',
                'salary' => 16000,
                'commission_type' => 'fixed',
                'commission_value' => 22,
                'service_areas' => json_encode(['Coimbatore']),
                'total_deliveries' => 540,
                'successful_deliveries' => 513,
                'rating' => 4.3,
                'status' => 'available',
                'is_active' => true,
            ],
        ];

        foreach ($agents as $agentData) {
            // Create user account for agent
            $user = User::updateOrCreate(
                ['email' => $agentData['email']],
                [
                    'name' => $agentData['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'delivery_agent',
                    'status' => 'active',
                ]
            );

            // Create delivery agent
            $agent = new DeliveryAgent();
            $agent->user_id = $user->id;
            $agent->agent_code = $this->generateAgentCode($agentData['joining_date']);
            $agent->fill($agentData);

            // Add device info
            $agent->device_id = 'DEV' . rand(10000, 99999);
            $agent->device_model = ['iPhone 13', 'Samsung M31', 'Redmi Note 10', 'OnePlus 9'][array_rand([0,1,2,3])];
            $agent->app_version = '1.0.' . rand(1, 9);

            // Add shift timings
            $agent->shift_start_time = '09:00:00';
            $agent->shift_end_time = '18:00:00';

            // Add emergency contact
            $agent->emergency_contact_name = 'Family Member';
            $agent->emergency_contact_phone = '9876543000';
            $agent->blood_group = ['A+', 'B+', 'O+', 'AB+'][array_rand([0,1,2,3])];

            // Add location
            $agent->current_latitude = $this->getLatitudeForCity($agentData['city']);
            $agent->current_longitude = $this->getLongitudeForCity($agentData['city']);
            $agent->last_location_update = now()->subMinutes(rand(5, 60));
            $agent->last_online_at = now()->subMinutes(rand(1, 30));

            // Performance metrics
            $agent->avg_delivery_time = rand(25, 45);
            $agent->on_time_delivery_rate = rand(85, 98);
            $agent->customer_feedback_count = rand(50, 200);

            $agent->save();

            $this->command->info("✅ Agent created: {$agentData['name']} ({$agent->agent_code})");
        }

        $this->command->info('✅ ' . count($agents) . ' delivery agents seeded successfully!');
    }

    /**
     * Generate agent code
     */
    private function generateAgentCode($joiningDate)
    {
        $prefix = 'AG';
        $date = Carbon::parse($joiningDate);
        $year = $date->format('y');
        $month = $date->format('m');
        $sequence = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $year . $month . $sequence;
    }

    /**
     * Get latitude for city
     */
    private function getLatitudeForCity($city)
    {
        $coordinates = [
            'Bangalore' => 12.9716,
            'Mumbai' => 19.0760,
            'Ahmedabad' => 23.0225,
            'Hyderabad' => 17.3850,
            'Delhi' => 28.6139,
            'Pune' => 18.5204,
            'Kolkata' => 22.5726,
            'Chennai' => 13.0827,
            'Kochi' => 9.9312,
            'Coimbatore' => 11.0168,
        ];

        return $coordinates[$city] ?? 28.6139;
    }

    /**
     * Get longitude for city
     */
    private function getLongitudeForCity($city)
    {
        $coordinates = [
            'Bangalore' => 77.5946,
            'Mumbai' => 72.8777,
            'Ahmedabad' => 72.5714,
            'Hyderabad' => 78.4867,
            'Delhi' => 77.2090,
            'Pune' => 73.8567,
            'Kolkata' => 88.3639,
            'Chennai' => 80.2707,
            'Kochi' => 76.2673,
            'Coimbatore' => 76.9558,
        ];

        return $coordinates[$city] ?? 77.2090;
    }
}
