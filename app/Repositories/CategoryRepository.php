<?php


namespace App\Repositories;


use App\Models\Category;
use App\Constants\Status;

class CategoryRepository implements CategoryRepositoryInterface
{

    /**
     * Get all categories in the app.
     * @return mixed
     */
    public function all()
    {
        return Category::orderBy('name')
            ->get()
            ->map(function($category){
                return $category->format();
            });
    }

    /**
     * Get a category details by ID provided.
     * @param int $categoryId
     * @return mixed
     */
    public function findById(int $categoryId)
    {
        return Category::where('id', $categoryId)->firstOrFail()->format();
    }

    /**
     * Update given category details
     * @param $categoryId
     * @return string
     */
    public function update($categoryId)
    {
        $category = Category::where('id', $categoryId)->firstOrFail();
        $category->update(request()->only('name'));

        return Status::SUCCESS;
    }

    /**
     * Delete category passed.
     * @param int $categoryId
     * @return string
     */
    public function delete(int $categoryId)
    {
        Category::where('id', $categoryId)->delete();

        return Status::SUCCESS;
    }

}