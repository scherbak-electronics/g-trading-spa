<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreateSessionRequest;
use App\Http\Requests\UpdateSessionRequest;
use App\Models\Exchange\ActiveSymbol;
use App\Models\Trading\Session;
use App\Services\Trading\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(protected readonly SessionService $sessionService)
    {}

    public function store(CreateSessionRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $session = $this->sessionService->create($validatedData);
        $data = ['new_session' => $session];
        ActiveSymbol::setActiveSymbol($validatedData['symbol']);
        return response()->json($data);
    }

    public function update(UpdateSessionRequest $request, Session $session): JsonResponse
    {
        $validatedData = $request->validated();
        $session->update($validatedData);
        //$session = $this->sessionService->update($validatedData);
        $data = ['session' => 'ok'];
        return response()->json($data);
    }

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

    public function start(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (empty($id)) {
            return response()->json([]);
        }
        $session = $this->sessionService->start($id);
        $data = ['session' => $session];
        return response()->json($data);
    }

    public function stop(Request $request): JsonResponse
    {
        $id = $request->query('id');
        if (empty($id)) {
            return response()->json([]);
        }
        $session = $this->sessionService->stop($id);
        $data = ['session' => $session];
        return response()->json($data);
    }
}
