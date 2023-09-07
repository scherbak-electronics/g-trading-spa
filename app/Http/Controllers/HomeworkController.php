<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomeworkRequest;
use App\Http\Requests\UpdateHomeworkRequest;
use App\Models\Trading\Homework;
use App\Services\Trading\HomeworkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class HomeworkController extends Controller
{
    private HomeworkService $homeworkService;
    public function __construct(HomeworkService $homeworkService)
    {
        $this->homeworkService = $homeworkService;
        $this->authorizeResource(Homework::class, 'homework');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->homeworkService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return $this->responseDataSuccess([]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHomeworkRequest $request): JsonResponse
    {
        $input = $request->validated();
        $record = $this->homeworkService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['record' => $record]);
        } else {
            return $this->responseStoreFail();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Homework $homework): JsonResponse
    {
        $model = $this->homeworkService->get($homework);
        return $this->responseDataSuccess(['model' => $model]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Homework $homework): JsonResponse
    {
        return $this->show($homework);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHomeworkRequest $request, Homework $homework): JsonResponse
    {
        $data = $request->validated();
        if ($this->homeworkService->update($homework, $data)) {
            return $this->responseUpdateSuccess(['record' => $homework->fresh()]);
        } else {
            return $this->responseUpdateFail();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Homework $homework): JsonResponse
    {
        if ($this->homeworkService->delete($homework)) {
            return $this->responseDeleteSuccess(['record' => $homework]);
        }
        return $this->responseDeleteFail();
    }
}
