<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiFilter {
    protected $safeParams = [];

    protected $columnMap = [];

    protected $operatorMap = [];

    /**
     * The expected relationship(s)
     * that corresponds to the model.
     */
    protected $relationships = [];


    /**
     * Transform the query parameters
     * into eloquent queries.
     *
     * @param  Request $request
     *
     * @return array
     */
    public function transform(Request $request): array
    {
        $eloQuery = [];

        foreach($this->safeParams as $param => $operators) {
            $query = $request->query($param);


            if (!isset($query)) continue;

            $column = $this->columnMap[$param] ?? $param;

            if (!in_array($param, array_keys($this->relationships))) {
                foreach ($operators as $operator) {
                    if (isset($query)) {
                        $eloQuery['whereQuery'][] = [$column, $this->operatorMap[$operator], $query];
                    }
                }
            }

            if (in_array($param, array_keys($this->relationships))) {
                foreach ($this->relationships as $model) {
                    $value = explode(',', $query);
                    $eloQuery['whereHasQuery'][] = [
                        'model' => $model,
                        'column' => 'id',
                        'value' => $value
                    ];
                }
            }
        }

        return $eloQuery;
    }

    /**
     * Query the given model.
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @param array $queryItems
     *
     * @return Illuminate\Database\Eloquent\Collection
     *
     */
    public function queryModel(Model $model, array $queryItems)
    {
        $query = $model->query();

        if (isset($queryItems['whereQuery'])) {
            $query->where($queryItems['whereQuery']);
        }

        if (isset($queryItems['whereHasQuery'])) {
            $whereHasQuery = $queryItems['whereHasQuery'];

            foreach ($whereHasQuery as $filter) {
                $query->whereHas($filter['model'], function ($query) use ($filter) {
                    $query->whereIn(
                        "{$filter['model']}.{$filter['column']}",
                        $filter['value']
                    );
                });
            }
        }

        return $query->get();
    }
}
