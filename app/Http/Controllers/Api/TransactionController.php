<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Services\SignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(): JsonResponse
    {
        $transactions = Transaction::latest()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function store(
        StoreTransactionRequest $request,
        SignatureService $signatureService
    ): JsonResponse {
        $data = $request->validated();

        $signatureData = [
            'reference' => $data['reference'],
            'amount' => $data['amount'],
            'currency' => strtoupper($data['currency']),
        ];

        $signature = $signatureService->generate($signatureData);

        $transaction = Transaction::create([
            'reference' => $data['reference'],
            'amount' => $data['amount'],
            'currency' => strtoupper($data['currency']),
            'status' => 'pending',
            'signature' => $signature,
            'request_payload' => $data,
        ]);

        $responseData = [
            'id' => $transaction->id,
            'reference' => $transaction->reference,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'status' => $transaction->status,
            'signature' => $transaction->signature,
        ];

        $transaction->update([
            'response_payload' => $responseData,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully.',
            'data' => $responseData,
        ], 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load('callbacks');

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }
}
