<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CallbackRequest;
use App\Models\Callback;
use App\Models\Transaction;
use App\Services\SignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallbackController extends Controller
{
    public function store(
        CallbackRequest $request,
        SignatureService $signatureService
    ): JsonResponse {
        $data = $request->validated();

        $transaction = Transaction::where(
            'reference',
            $data['reference']
        )->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        // Data used to verify the callback signature
        $signatureData = [
            'event' => $data['event'],
            'reference' => $data['reference'],
            'status' => $data['status'],
        ];

        $signatureValid = $signatureService->verify(
            $signatureData,
            $data['signature']
        );

        // Save callback attempt
        $callback = Callback::create([
            'transaction_id' => $transaction->id,
            'event' => $data['event'],
            'payload' => $data,
            'signature' => $data['signature'],
            'signature_valid' => $signatureValid,
            'status' => $signatureValid ? 'processed' : 'rejected',
            'processed_at' => $signatureValid ? now() : null,
        ]);

        // Reject invalid signature
        if (!$signatureValid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature.',
                'callback_id' => $callback->id,
            ], 401);
        }

        // Update transaction only after successful verification
        DB::transaction(function () use ($transaction, $data) {
            $transaction->update([
                'status' => $data['status'],
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Callback verified successfully.',
            'data' => [
                'callback_id' => $callback->id,
                'reference' => $transaction->reference,
                'status' => $transaction->status,
            ],
        ]);
    }

    /**
     * Generate a callback signature for testing/demo purposes.
     */
    public function generateSignature(
        CallbackRequest $request,
        SignatureService $signatureService
    ): JsonResponse {
        $data = [
            'event' => $request->event,
            'reference' => $request->reference,
            'status' => $request->status,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'event' => $data['event'],
                'reference' => $data['reference'],
                'status' => $data['status'],
                'signature' => $signatureService->generate($data),
            ],
        ]);
    }
}
