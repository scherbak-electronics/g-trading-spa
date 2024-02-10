<?php
namespace App\Http\Controllers;

use App\Services\Trading\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(protected readonly SessionService $sessionService)
    {}

    public function getSession(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (empty($id)) {
            return response()->json([]);
        }
        $session = $this->sessionService->getSession($id);
        $data = ['session' => $session];
        return response()->json($data);
    }
}
