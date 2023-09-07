<?php

namespace App\Services\Trading;

use App\Http\Resources\HomeworkResource;
use App\Models\Trading\Homework;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomeworkService
{
    protected PriceLevelService $priceLevelService;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->priceLevelService = new PriceLevelService();
    }

    public function get(Homework $homework): HomeworkResource
    {
        return new HomeworkResource($homework);
    }

    /**
     * Get resource index from the database
     * @param $data
     * @return AnonymousResourceCollection
     */
    public function index($data): AnonymousResourceCollection
    {
        $query = Homework::query();
        return HomeworkResource::collection($query->paginate(10));
    }

    /**
     * Creates resource in the database
     * @param  array  $data
     * @return Builder|Model|null
     */
    public function create(array $data): Model|Builder|null
    {
        $record = Homework::query()->create($data);
        if (!empty($record)) {
            return $record->fresh();
        } else {
            return null;
        }
    }

    /**
     * Updates resource in the database
     * @param Homework $homework
     * @param array $data
     * @return bool
     */
    public function update(Homework $homework, array $data): bool
    {
        return $homework->update($data);
    }

    /**
     * Deletes resource in the database
     * @param Homework $homework
     * @return bool
     */
    public function delete(Homework $homework): bool
    {
        return $homework->delete();
    }
}
