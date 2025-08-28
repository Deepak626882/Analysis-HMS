<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemoRequest;

class DemoRequestController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'hotel_name' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        // Store the form data in the database
        DemoRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'hotel_name' => $request->hotel_name,
            'message' => $request->message,
        ]);

        // Return a success response
        return response()->json(['message' => 'Request Submitted Successfully!'], 200);
    }
}
