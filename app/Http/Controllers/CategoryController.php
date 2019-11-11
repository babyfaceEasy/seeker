<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\ResponseMessage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as ResponseCode;
use App\Repositories\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    private $categoryRepository;


    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $categories =  $this->categoryRepository->all();

        return response()->sendJsonSuccess($categories);
    }

    public function show(Request $request, int $category_id)
    {
        $category = $this->categoryRepository->findById($category_id);
        return response()->sendJsonSuccess($category);
    }

    public function update(Request $request, int $category_id)
    {
        $validator =Validator::make($request->all(), [
            'name' => 'required|string|min:2'
        ]);

        if ($validator->fails()){
            return Response::sendJsonError($validator->errors(), ResponseMessage::INVALID_PARAMS,  ResponseCode::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->categoryRepository->update($category_id);

        return response()->sendJsonSuccess([], sprintf(ResponseMessage::UPDATE_WAS_SUCCESSFUL, 'Category'));

    }

    public function destroy(int $category_id)
    {
        $this->categoryRepository->delete($category_id);

        return response()->sendJsonSuccess([], sprintf(ResponseMessage::DELETE_WAS_SUCCESSFUL, 'Category'));
    }
}
