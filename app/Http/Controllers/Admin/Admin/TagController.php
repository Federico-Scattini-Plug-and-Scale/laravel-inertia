<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TagController extends Controller
{
    public function index()
    {
        if(!empty(Arr::get(request()->old(), 'tags')))
        {
            $tags = Arr::get(request()->old(), 'tags');
        }
        else 
        {
            $tags = TagGroup::getAll(app()->getLocale());
        }

        return Inertia::render('Admin/Tags/Index', [
            'tags' => $tags
        ]);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tags.*.name' => 'required',
            'tags.*.is_active' => 'required'
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator, 'tagGroup');
        }

        $tagGroups = $request->get('tags');

        foreach ($tagGroups as $index => $group)
        {
            TagGroup::updateOrCreate(
                ['id' => Arr::get($group, 'id')],
                [
                    'name' => Arr::get($group, 'name'), 
                    'is_active' => Arr::get($group, 'is_active'),
                    'position' => $index,
                    'locale' => app()->getLocale()
                ]
            );
        }

        return redirect()->route('admin.tags');
    }

    public function edit(TagGroup $taggroup)
    {
        if(!empty(Arr::get(request()->old(), 'tags')))
        {
            $tags = Arr::get(request()->old(), 'tags');
        }
        else
        {
            $tags = $taggroup->tags;
        }

        return Inertia::render('Admin/Tags/Tag', [
            'taggroup' => $taggroup,
            'tags' => $tags
        ]);
    }

    public function destroy(TagGroup $taggroup)
    {
        $taggroup->delete();

        return redirect()->route('admin.tags');
    }

    public function update(TagGroup $taggroup, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tags.*.name' => 'required',
            'tags.*.is_active' => 'required'
        ]);

        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator, 'tags');
        }

        $tags = $request->get('tags');

        foreach ($tags as $index => $tag)
        {
            Tag::updateOrCreate(
                ['id' => Arr::get($tag, 'id')],
                [
                    'name' => Arr::get($tag, 'name'), 
                    'is_active' => Arr::get($tag, 'is_active'),
                    'position' => $index,
                    'tag_group_id' => $taggroup->id,
                    'locale' => app()->getLocale()
                ]
            );
        }

        return redirect()->route('admin.tags.edit', $taggroup);
    }

    public function destroyTag(TagGroup $taggroup, Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.tags.edit', $taggroup);
    }
}
