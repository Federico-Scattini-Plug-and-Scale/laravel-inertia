<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Tags/Index', [
            'tags' => TagGroup::orderBy('position')->get()
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'tags.*.name' => 'required'
        ]);

        $tagGroups = $request->get('tags');

        foreach ($tagGroups as $index => $group)
        {
            TagGroup::updateOrCreate(
                ['id' => Arr::get($group, 'id')],
                [
                    'name' => Arr::get($group, 'name'), 
                    'is_active' => Arr::get($group, 'is_active'),
                    'position' => $index
                ]
            );
        }

        return redirect()->route('admin.tags');
    }

    public function edit(TagGroup $taggroup)
    {
        return Inertia::render('Admin/Tags/Tag', [
            'taggroup' => $taggroup
        ]);
    }

    public function destroy(TagGroup $taggroup)
    {
        $taggroup->delete();

        return redirect()->route('admin.tags');
    }
}
