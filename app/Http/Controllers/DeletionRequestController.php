<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeletionRequest;

class DeletionRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $deletionRequest = DeletionRequest::create([
            'email' => $request->input('email'),
        ]);
        return response()->json(['message' => 'Deletion request submitted successfully.']);
    }
}
