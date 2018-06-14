<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\Interfaces\ResponseCodesInterface;
use App\Helpers\JsonApiResponseHelper;
use CloudCreativity\JsonApi\Contracts\Http\Requests\RequestInterface as JsonApiRequest;
use CloudCreativity\LaravelJsonApi\Http\Controllers\EloquentController;
use App\Models\User;
use App\JsonApi\Users\Hydrator;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;

class UserController extends EloquentController
{
    use JsonApiResponseHelper;

    /**
     * @var UserService $service
     */
    protected $service;

    /**
     * @var User $model
     */
    protected $model;

    /**
     * @param UserService $service
     * @param User $user
     * @param Hydrator $hydrator
     */
    public function __construct(UserService $service, User $user, Hydrator $hydrator)
    {
        $this->service = $service;
        $this->model = $user;

        parent::__construct($user, $hydrator);
    }

    /**
     * Display a listing of the resource.
     *
     * @SWG\Get(
     *   path="/users",
     *   tags={"Users"},
     *   summary="get me",
     *   description="get me",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     in="query",
     *     name="filter[user]",
     *     description="",
     *     required=false,
     *     default="me",
     *     type="string"
     *   ),
     *   @SWG\Response(response="200", description="Return user"),
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
     *   path="/users/{id}",
     *   tags={"Users"},
     *   summary="get user by id",
     *   description="get user by id",
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
     *   @SWG\Response(response="200", description="Return user"),
     *   @SWG\Response(response="404", description="error, user not found"),
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
     * @SWG\Post(path="/users",
     *   tags={"Users"},
     *   summary="register user. Only for user with admin role",
     *   description="register user. Only for user with admin role",
     *   produces={"application/vnd.api+json"},
     *   consumes={"application/vnd.api+json"},
     *   @SWG\Parameter(
     *     name="Register user. Only for user with admin role",
     *     in="body",
     *     description="JSON Object which create cat",
     *     required=true,
     *     @SWG\Schema(
     *       @SWG\Property(
     *         property="data",
     *         type="object",
     *         @SWG\Property(property="type", type="string", default="users", example="users"),
     *         @SWG\Property(
     *           property="attributes",
     *           type="object",
     *           @SWG\Property(property="email", type="string", example="user@mail.com", description="required"),
     *           @SWG\Property(property="password", type="string", example="password", description="required"),
     *           @SWG\Property(property="name", type="string", example="Steven", description="required"),
     *           @SWG\Property(
     *             property="activated",
     *             type="string",
     *             description="0 - deactivated, 1 - activated",
     *             default="1",
     *             example="1"
     *           ),
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
        if (is_array($result = $this->service->checkOnAdminRole())) {
            return $this->sendFailedResponse($result, ResponseCodesInterface::HTTP_CODE_FORBIDDEN);
        }

        /**
         * hydrate all data to User entity
         *
         * @var User $user
         */
        $user = $this->hydrate($request->getDocument()->getResource(), $this->model);

        $this->service->saveModel($user);

        $this->service->attachUserRole($user);

        event(new Registered($user));

        return $this->reply()->created($user);
    }

    /**
     * @SWG\Patch(path="/users/{id}",
     *   tags={"Users"},
     *   summary="update user",
     *   description="update user",
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
     *   @SWG\Parameter(
     *     name="Update user by id",
     *     in="body",
     *     description="JSON Object which update user by id",
     *     required=true,
     *     @SWG\Schema(
     *       @SWG\Property(
     *         property="data",
     *         type="object",
     *         @SWG\Property(property="type", type="string", default="users", example="users"),
     *         @SWG\Property(property="id", type="string", default="1", example="1"),
     *         @SWG\Property(
     *           property="attributes",
     *           type="object",
     *           @SWG\Property(property="email", type="string", example="user@mail.com", description="required"),
     *           @SWG\Property(property="password", type="string", example="Password1", description="required"),
     *           @SWG\Property(property="name", type="string", example="Steven", description="required"),
     *           @SWG\Property(
     *             property="activated",
     *             type="string",
     *             description="0 - deactivated, 1 - activated",
     *             default="1",
     *             example="1"
     *           ),
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

        $resource = $request->getDocument()->getResource();
        $record = $this->getRecord($request);

        // hydrate all data to User entity
        $user = $this->hydrate($resource, $record);

        $this->service->saveModel($user);

        return $this->reply()->content($user);
    }

    /**
     * @SWG\Delete(
     *   path="/users/{id}",
     *   tags={"Users"},
     *   summary="delete user by id",
     *   description="delete user by id",
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
     *   @SWG\Response(response="404", description="error, user not found"),
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
