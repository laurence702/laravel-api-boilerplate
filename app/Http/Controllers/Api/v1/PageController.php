<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Interfaces\ResponseCodesInterface;
use App\Helpers\JsonApiResponseHelper;
use App\Models\Page;
use App\Services\PageService;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface as JsonApiRequest;
use CloudCreativity\LaravelJsonApi\Http\Controllers\EloquentController;
use App\JsonApi\Pages\Hydrator;
use Auth;

class PageController extends EloquentController
{
    use JsonApiResponseHelper;

    /**
     * @var PageService $service
     */
    protected $service;

    /**
     * @var Page $model
     */
    protected $model;

    /**
     * @param PageService $service
     * @param Page $page
     * @param Hydrator $hydrator
     */
    public function __construct(PageService $service, Page $page, Hydrator $hydrator)
    {
        $this->service = $service;
        $this->model = $page;

        parent::__construct($page, $hydrator);
    }

    /**
     * Display a listing of the resource.
     *
     * @SWG\Get(
     *   path="/pages",
     *   tags={"Pages"},
     *   summary="get pages",
     *   description="get pages",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="page[number]",
     *     description="",
     *     required=false,
     *     default="1",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="query",
     *     name="page[size]",
     *     description="",
     *     required=false,
     *     default="1",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     in="query",
     *     name="sort",
     *     description="if you want sorted by desc you must adding `-` before field",
     *     required=false,
     *     default="title,created_at",
     *     type="string"
     *   ),
     *   @SWG\Response(response="200", description="Return pages"),
     *   security={
     *     {"api_key_header": {}},
     *   }
     * )
     *
     * @param JsonApiRequest $request
     * @return mixed
     */

    /**
     * @SWG\Get(
     *   path="/pages/{id}",
     *   tags={"Pages"},
     *   summary="get page by id",
     *   description="get page by id",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     description="",
     *     required=true,
     *     default="1",
     *     type="integer"
     *   ),
     *   @SWG\Response(response="200", description="Return page"),
     *   @SWG\Response(response="404", description="error, page not found"),
     *   security={
     *     {"api_key_header": {}},
     *   }
     * )
     *
     * @param JsonApiRequest $request
     * @return mixed
     */
    public function read(JsonApiRequest $request)
    {
        if (is_array($result = $this->service->checkAccess())) {
            return $this->sendFailedResponse($result, ResponseCodesInterface::HTTP_CODE_FORBIDDEN);
        }

        return parent::read($request);
    }

    /**
     * @SWG\Post(path="/pages",
     *   tags={"Pages"},
     *   summary="create page",
     *   description="create page",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     name="Create page",
     *     in="body",
     *     description="JSON Object which create page",
     *     required=true,
     *     @SWG\Schema(
     *       @SWG\Property(
     *         property="data",
     *         type="object",
     *         @SWG\Property(property="type", type="string", example="pages"),
     *         @SWG\Property(
     *           property="attributes",
     *           type="object",
     *           @SWG\Property(property="title", type="string", example="Title", description="required"),
     *           @SWG\Property(property="alias", type="string", example="Alias", description="required"),
     *           @SWG\Property(property="keywords", type="string", example="Keywords", description="required"),
     *           @SWG\Property(property="description", type="string", example="Description", description="required"),
     *           @SWG\Property(property="content", type="string", example="Content", description="required"),
     *         )
     *       )
     *     )
     *   ),
     *   @SWG\Response(response="200", description="Return message"),
     *   security={
     *     {"api_key_header": {}},
     *   }
     * )
     *
     * @param JsonApiRequest $request
     * @return mixed
     */
    public function create(JsonApiRequest $request)
    {
        $user = Auth::guard('api')->user();

        $this->model->user_id = $user->id;

        return parent::create($request);
    }

    /**
     * @SWG\Patch(path="/pages/{id}",
     *   tags={"Pages"},
     *   summary="update pages",
     *   description="update pages",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     description="",
     *     required=true,
     *     default="1",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="Update page by id",
     *     in="body",
     *     description="JSON Object which update page by id",
     *     required=true,
     *     @SWG\Schema(
     *       @SWG\Property(
     *         property="data",
     *         type="object",
     *         @SWG\Property(property="type", type="string", example="pages"),
     *         @SWG\Property(property="id", type="integer", example="1"),
     *         @SWG\Property(
     *           property="attributes",
     *           type="object",
     *           @SWG\Property(property="title", type="string", example="Title", description="required"),
     *           @SWG\Property(property="alias", type="string", example="Alias", description="required"),
     *           @SWG\Property(property="keywords", type="string", example="Keywords", description="required"),
     *           @SWG\Property(property="description", type="string", example="Description", description="required"),
     *           @SWG\Property(property="content", type="string", example="Content", description="required"),
     *         )
     *       )
     *     )
     *   ),
     *   @SWG\Response(response="200", description="Return message"),
     *   security={
     *     {"api_key_header": {}},
     *   }
     * )
     *
     * @param JsonApiRequest $request
     * @return mixed
     */
    public function update(JsonApiRequest $request)
    {
        if (is_array($result = $this->service->checkAccess())) {
            return $this->sendFailedResponse($result, ResponseCodesInterface::HTTP_CODE_FORBIDDEN);
        }

        return parent::update($request);
    }

    /**
     * @SWG\Delete(
     *   path="/pages/{id}",
     *   tags={"Pages"},
     *   summary="delete page by id",
     *   description="delete page by id",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="id",
     *     description="",
     *     required=true,
     *     default="1",
     *     type="integer"
     *   ),
     *   @SWG\Response(response="200", description="success"),
     *   @SWG\Response(response="404", description="error, page not found"),
     *   security={
     *     {"api_key_header": {}},
     *   }
     * )
     *
     * @param JsonApiRequest $request
     * @return mixed
     */
    public function delete(JsonApiRequest $request)
    {
        if (is_array($result = $this->service->checkAccess())) {
            return $this->sendFailedResponse($result, ResponseCodesInterface::HTTP_CODE_FORBIDDEN);
        }

        return parent::delete($request);
    }
}
