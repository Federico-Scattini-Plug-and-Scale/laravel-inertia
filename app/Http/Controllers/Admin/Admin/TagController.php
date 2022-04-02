<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            $tags = TagGroup::getAll(getCountry());
        }

        return Inertia::render('Admin/Tags/Index', [
            'tags' => $tags,
            'tagTypes' => config('group-tags')
        ]);
    }

    public function save(Request $request)
    {
        $tagGroups = $request->get('tags');

        $validator = Validator::make($request->all(), $this->rules($tagGroups));

        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator, 'tagGroup');
        }

        foreach ($tagGroups as $index => $group)
        {
            TagGroup::updateOrCreate(
                ['id' => Arr::get($group, 'id')],
                [
                    'name' => Arr::get($group, 'name'), 
                    'type' => Arr::get($group, 'type'), 
                    'is_active' => Arr::get($group, 'is_active'),
                    'position' => $index,
                    'locale' => getCountry(),
                ]
            );
        }

        return redirect()->route('admin.tags.index')->with('message', [
            'type' => 'success',
            'content' => __('The tag groups have been saved successfully.')
        ]);
    }

    public function edit(TagGroup $taggroup)
    {
        if(!empty(Arr::get(request()->old(), 'tags')))
        {
            $tags = Arr::get(request()->old(), 'tags');
        }
        else
        {
            $tags = Tag::getByGroup($taggroup->id);
        }

        return Inertia::render('Admin/Tags/Tag', [
            'taggroup' => $taggroup,
            'tags' => $tags
        ]);
    }

    public function destroy(TagGroup $taggroup)
    {
        foreach ($taggroup->tags as $tag)
        {
            $tag->users()->detach();
        }

        $taggroup->delete();

        return redirect()->route('admin.tags.index')->with('message', [
            'type' => 'success',
            'content' => __('The tag group has been deleted successfully.')
        ]);
    }

    public function update(TagGroup $taggroup, Request $request)
    {
        $tags = $request->get('tags');
        $files = $request->file('tags', []);

        $validator = Validator::make($request->all(), $this->rules($tags));

        if ($validator->fails())
        {
            return redirect()->back()->withInput()->withErrors($validator, 'tags');
        }

        foreach ($tags as $index => $tag)
        {
            $imageName = null;

            if(!empty($files) && in_array($index, array_keys($files)))
            {
                $logo = Arr::get($files[$index], 'icon');
                if (!empty($logo))
                {
                    $imageName = time().'_'.$logo->getClientOriginalName();
                    $logo->move(public_path() . '/img/', $imageName);
                }
            }

            $data = [
                'name' => Arr::get($tag, 'name'), 
                'slug' => Str::slug(Arr::get($tag, 'name')), 
                'is_active' => Arr::get($tag, 'is_active'),
                'bg_color' => Arr::get($tag, 'bg_color'),
                'position' => $index,
                'tag_group_id' => $taggroup->id,
                'locale' => getCountry(),
                'is_approved' => Arr::get($tag, 'is_approved'),
            ];

            if (!empty($imageName))
            {
                $data['icon'] = $imageName;
            }

            Tag::updateOrCreate(
                ['id' => Arr::get($tag, 'id')],
                $data
            );
        }

        return redirect()->route('admin.tags.edit', $taggroup)->with('message', [
            'type' => 'success',
            'content' => __('The tags have been updated successfully.')
        ]);
    }

    public function destroyTag(TagGroup $taggroup, Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.tags.edit', $taggroup)->with('message', [
            'type' => 'success',
            'content' => __('The tag has been deleted successfully.')
        ]);
    }

    private function rules($elements)
    {
        $rules = [];
        
        foreach ($elements as $index => $element)
        {
            $rules['tags.'.$index.'.name'] = 'required';
            $rules['tags.'.$index.'.is_active'] = 'required';
            
            if (isset($element['type']))
            {
                $rules['tags.'.$index.'.type'] = [
                    'required',
                    Rule::unique('tag_groups', 'type')->ignore($element['id'], 'id')->where('locale', $element['locale'] ?? getCountry())
                ];
            }
        }

        return $rules;
    }
}
