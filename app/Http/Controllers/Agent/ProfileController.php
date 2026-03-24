<?php
// app/Http/Controllers/Agent/ProfileController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        return view('agent.profile.edit', compact('user', 'agent'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'vehicle_number' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
            'profile_photo' => 'nullable|image|max:2048'
        ]);

        // Update user
        $user->name = $request->name;

        // Update password if provided
        if ($request->new_password) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Update agent profile
        if ($agent) {
            $agent->name = $request->name;
            $agent->phone = $request->phone;
            $agent->address = $request->address;
            $agent->city = $request->city;
            $agent->state = $request->state;
            $agent->pincode = $request->pincode;
            $agent->vehicle_number = $request->vehicle_number;

            // Upload profile photo
            if ($request->hasFile('profile_photo')) {
                if ($agent->photo) {
                    Storage::disk('public')->delete($agent->photo);
                }
                $path = $request->file('profile_photo')->store('agent-photos', 'public');
                $agent->photo = $path;
            }

            $agent->save();
        }

        return redirect()->route('agent.profile')->with('success', 'Profile updated successfully.');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $agent = DeliveryAgent::where('user_id', Auth::id())->first();

        if ($agent) {
            $agent->current_latitude = $request->latitude;
            $agent->current_longitude = $request->longitude;
            $agent->last_location_update = now();
            $agent->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
