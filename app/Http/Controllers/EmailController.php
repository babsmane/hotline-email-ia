<?php

namespace App\Http\Controllers;

use App\Models\InboundEmail;
use App\Services\EmailClassifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function store(Request $request, EmailClassifier $classifier): JsonResponse
    {
        $data = $request->validate([
            'sender'  => 'required|email',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string|max:20000',
        ]);

        $email = InboundEmail::create($data + ['status' => 'pending']);

        return response()->json($classifier->classify($email), 201);
    }

    public function index(Request $request)
    {
        return InboundEmail::query()
            ->when($request->query('category'), fn ($q, $v) => $q->where('category', $v))
            ->when($request->query('priority'), fn ($q, $v) => $q->where('priority', $v))
            ->when($request->boolean('needs_human'), fn ($q) => $q->where('needs_human', true))
            ->latest()
            ->paginate(20);
    }
}
