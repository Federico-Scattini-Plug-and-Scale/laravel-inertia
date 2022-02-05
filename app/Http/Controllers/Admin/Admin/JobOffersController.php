<?php

namespace App\Http\Controllers\Admin\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobOfferEditRequest;
use App\Models\Category;
use App\Models\JobOffer;
use App\Models\JobOfferHistory;
use App\Models\Tag;
use App\Models\TagGroup;
use App\Notifications\JobOffers\JobOfferPublished;
use App\Notifications\JobOffers\JobOfferRestored;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class JobOffersController extends Controller
{
    public function index()
    {
        $filters = request()->has('filters') ? request()->get('filters') : [];
        $jobOffers = JobOffer::getForAdmin(getCountry(), 10, $filters);

        if (!empty($jobOffers))
        {
            $jobOffers->each(function($item) {
                if (!empty($item->published_at))
                {
                    $item->expiring_at = now('Europe/Rome')->subDays($item->validity_days)->endOfDay()->diffInDays(Carbon::parse($item->published_at, 'Europe/Rome'), false);
                }
                else
                {
                    $item->expiring_at = __('Expired');
                }
                $item->published_at_formatted = optional($item->published_at)->format('d-m-Y');
            });
        }

        return Inertia::render('Admin/JobOffers/Index', [
            'jobOffers' => $jobOffers,
            'statusOptions' => JobOffer::getStatusOptions(),
            'filters' => $filters
        ]);
    }

    public function approve(JobOffer $jobOffer)
    {
        $jobOffer->status = JobOffer::STATUS_ACTIVE;
        $jobOffer->published_at = now('Europe/Rome');
        $jobOffer->validity_days = $jobOffer->is_free ? JobOffer::FREE_VALIDITY : JobOffer::VALIDITY;
        $jobOffer->save();

        foreach ($jobOffer->tags as $tag)
        {
            $tag->is_approved = true;
            $tag->is_active = true;
            $tag->save();
        }

        $jobOffer->company->notify(new JobOfferPublished());

        return redirect()->route('admin.joboffers.index')->with('message', [
            'type' => 'success',
            'content' => __('The job offer has been approved.')
        ]);
    }

    public function edit(JobOffer $jobOffer)
    {
        if (getCountry() != $jobOffer->locale)
        {
            return redirect()->route('company.joboffers.index', $user)->with('message', [
                'type' => 'info',
                'content' => __('The job offer that you were editing is posted in another country. If yuo want to edit it, please change the country.')
            ]);
        }

        $jobOffer->published_at_formatted = $jobOffer->published_at->format('Y-m-d');
        $jobOffer->load('tags.tagGroup');
        $user = $jobOffer->company;

        return Inertia::render('Admin/JobOffers/Edit', [
            'jobOffer' => $jobOffer,
            'company' => $user,
            'statuses' => JobOffer::getStatusOptions(),
            'tags' => $jobOffer->tags->groupBy('tagGroup.type')->map(fn ($tagGroup) => $tagGroup->map(fn ($tag) => $tag->id))->toArray(),
            'categories' => Category::getOptions(getCountry()),
            'sectors' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_SECTOR, $user->id, getCountry()),
            'industries' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_INDUSTRY, $user->id, getCountry()),
            'languages' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_LANGUAGE, $user->id, getCountry()),
            'processes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_PROCESS_TYPE, $user->id, getCountry()),
            'machineTypes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE_TYPE, $user->id, getCountry()),
            'machines' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE, $user->id, getCountry()),
            'techSkills' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_TECH_SKILLS, $user->id, getCountry()),
            'exp' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_EXP, $user->id, getCountry()),
            'contracts' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_CONTRACT, $user->id, getCountry()),
        ]);
    }

    public function update(JobOffer $jobOffer, JobOfferEditRequest $request)
    {
        $payload = $request->validated();
        $user = $jobOffer->company;
        $tags = $this->prepareTagsJobOffer($payload, $user);

        $data = [
            'title' => Arr::get($payload, 'title'),
            'slug' => Str::slug(Arr::get($payload, 'title')),
            'description' => Arr::get($payload, 'description'),
            'specialization' => Arr::get($payload, 'specialization'),
            'max_salary' => Arr::get($payload, 'max_salary'),
            'min_salary' => Arr::get($payload, 'min_salary'),
            'currency' => Arr::get($payload, 'currency'),
            'address' => Arr::get($payload, 'address'),
            'region' => Arr::get($payload, 'region'),
            'province' => Arr::get($payload, 'province'),
            'city' => Arr::get($payload, 'city'),
            'country' => Arr::get($payload, 'country'),
            'postal_code' => Arr::get($payload, 'postal_code'),
            'latitude' => Arr::get($payload, 'latitude'),
            'longitude' => Arr::get($payload, 'longitude'),
            'company_id' => $user->id,
            'status' => $jobOffer->status,
            'locale' => getCountry(),
            'category_id' => Arr::get($payload, 'category'),
            'status' => Arr::get($payload, 'status'),
            'validity_days' => Arr::get($payload, 'validity_days'),
            'published_at' => Arr::get($payload, 'published_at'),
        ];
        
        $jobOffer->update($data);
        $jobOffer->tags()->sync($tags);

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The job offer has been modified successfully.')
        ]);
    }

    public function archive(JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return redirect()->route('admin.joboffers.index')->with('message', [
            'type' => 'success',
            'content' => __('The job offer has been archived.')
        ]);
    }

    public function restore($jobOfferId)
    {
        $jobOffer = JobOffer::withTrashed()->findOrFail($jobOfferId);
        $jobOffer->restore();

        $jobOffer->status = JobOfferHistory::getLastStatusById($jobOffer->id);
        $jobOffer->save();

        $jobOffer->company->notify(new JobOfferRestored());

        return redirect()->route('admin.joboffers.index')->with('message', [
            'type' => 'success',
            'content' => __('The job offer has been deleted.')
        ]);
    }

    public function destroy($jobOfferId)
    {
        $jobOffer = JobOffer::withTrashed()->findOrFail($jobOfferId);
        $jobOffer->forceDelete();

        return redirect()->route('admin.joboffers.index')->with('message', [
            'type' => 'success',
            'content' => __('The job offer has been deleted.')
        ]);
    }

    private function prepareTagsJobOffer($data, $user)
    {
        $tags = [];
        $newTags = [];

        if (Arr::has($data, 'sectors') && Arr::get($data, 'sectors') != 'no validation' && !empty(Arr::get($data, 'sectors')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_SECTOR, getCountry());

            foreach (Arr::get($data, 'sectors') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'industries') && Arr::get($data, 'industries') != 'no validation' && !empty(Arr::get($data, 'industries')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_INDUSTRY, getCountry());

            foreach (Arr::get($data, 'industries') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'languages') && Arr::get($data, 'languages') != 'no validation' && !empty(Arr::get($data, 'languages')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_LANGUAGE, getCountry());

            foreach (Arr::get($data, 'languages') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'processes') && Arr::get($data, 'processes') != 'no validation' && !empty(Arr::get($data, 'processes')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_PROCESS_TYPE, getCountry());

            foreach (Arr::get($data, 'processes') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'machineTypes') && Arr::get($data, 'machineTypes') != 'no validation' && !empty(Arr::get($data, 'machineTypes')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_MACHINE_TYPE, getCountry());

            foreach (Arr::get($data, 'machineTypes') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'machines') && Arr::get($data, 'machines') != 'no validation' && !empty(Arr::get($data, 'machines')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_MACHINE, getCountry());

            foreach (Arr::get($data, 'machines') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'techSkills') && Arr::get($data, 'techSkills') != 'no validation' && !empty(Arr::get($data, 'techSkills')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_TECH_SKILLS, getCountry());

            foreach (Arr::get($data, 'techSkills') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'exp') && Arr::get($data, 'exp') != 'no validation' && !empty(Arr::get($data, 'exp')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_EXP, getCountry());

            foreach (Arr::get($data, 'exp') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (Arr::has($data, 'contracts') && Arr::get($data, 'contracts') != 'no validation' && !empty(Arr::get($data, 'contracts')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_CONTRACT, getCountry());

            foreach (Arr::get($data, 'contracts') as $tag)
            {
                is_string($tag) ? array_push($newTags, [
                    $tag => [
                        'groupId' => $group->id,
                        'groupLocale' => $group->locale 
                    ]
                ]) : array_push($tags, $tag);
            }
        }

        if (!empty($newTags))
        {
            $newCreatedTags = [];
            
            foreach ($newTags as $tag) {
                $tag = Tag::create([
                    'name' => array_key_first($tag),
                    'is_active' => true,
                    'tag_group_id' => Arr::get($tag[array_key_first($tag)], 'groupId'),
                    'locale' => Arr::get($tag[array_key_first($tag)], 'groupLocale'),
                    'position' => 0,
                    'suggested_by' => $user->id,
                    'is_approved' => true
                ]);

                $newCreatedTags[] = $tag->id;
            }
        }

        if (isset($newCreatedTags))
        {
            $tags = array_merge($tags, $newCreatedTags);
        }

        return $tags;
    }
}
