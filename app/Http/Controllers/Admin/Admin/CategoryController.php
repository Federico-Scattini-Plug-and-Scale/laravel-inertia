<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        if(!empty(Arr::get(request()->old(), 'categories')))
        {
            $categories = Arr::get(request()->old(), 'categories');
        }
        else 
        {
            $categories = Category::getAll(getCountry());
        }

        return Inertia::render('Admin/Category/Index', [
            'categories' => $categories
        ]);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator, 'categories');
        }

        $categories = $request->get('categories');

        foreach ($categories as $index => $category)
        {
            Category::updateOrCreate(
                ['id' => Arr::get($category, 'id')],
                [
                    'name' => Arr::get($category, 'name'), 
                    'is_active' => Arr::get($category, 'is_active'),
                    'position' => $index,
                    'locale' => getCountry()
                ]
            );
        }

        return redirect()->route('admin.categories.index')->with('message', [
            'type' => 'success',
            'content' => __('The categories have been saved successfully.')
        ]);
    }

    public function edit(Category $category)
    {
        return Inertia::render('Admin/Category/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Category $category, CategoryRequest $request)
    {
        $category->update($request->all());

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The category has been updated successfully.')
        ]);    
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The category has been deleted successfully.')
        ]);
    }

    private function rules()
    {
        return [
            'categories.*.name' => 'required',
            'categories.*.is_active' => 'required'
        ];
    }
}
