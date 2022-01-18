<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobOfferCreateRequest;
use App\Http\Requests\Admin\JobOfferEditRequest;
use App\Models\Category;
use App\Models\JobOffer;
use App\Models\JobOfferType;
use App\Models\Tag;
use App\Models\TagGroup;
use App\Models\User;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class JobOfferController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser');
    }

    public function index(User $user)
    {
        $filters = request()->has('filters') ? request()->get('filters') : [];
        $jobOffers = JobOffer::getByUser($user->id, app()->getLocale(), 10, $filters);

        if (!empty($jobOffers))
        {
            $jobOffers->each(function($item) {
                if (!empty($item->published_at))
                {
                    $item->expiring_at = $item->getValidityDays();
                }
                else
                {
                    $item->expiring_at = __('Expired');
                }
                
                $item->canUpgrade = JobOfferType::getMoreExpensivePackages($item->jobOfferType->price, app()->getLocale())->isNotEmpty();
            });
        }

        return Inertia::render('Company/JobOffers/Index', [
            'jobOffers' => $jobOffers,
            'statusOptions' => JobOffer::getStatusOptions(),
            'company' => $user,
            'filters' => $filters
        ]);
    }

    public function create(User $user)
    {
        return Inertia::render('Company/JobOffers/Create', [
            'company' => $user,
            'categories' => Category::getOptions(app()->getLocale()),
            'sectors' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_SECTOR, $user->id, app()->getLocale()),
            'industries' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_INDUSTRY, $user->id, app()->getLocale()),
            'languages' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_LANGUAGE, $user->id, app()->getLocale()),
            'processes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_PROCESS_TYPE, $user->id, app()->getLocale()),
            'machineTypes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE_TYPE, $user->id, app()->getLocale()),
            'machines' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE, $user->id, app()->getLocale()),
            'techSkills' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_TECH_SKILLS, $user->id, app()->getLocale()),
            'exp' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_EXP, $user->id, app()->getLocale()),
            'contracts' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_CONTRACT, $user->id, app()->getLocale()),
            'packages' => JobOfferType::getOptions(app()->getLocale())
        ]);
    }

    public function store(User $user, JobOfferCreateRequest $request)
    {
        $payload = $request->validated();
        $tags = $this->prepareTagsJobOffer($payload, $user);

        $data = [
            'title' => Arr::get($payload, 'title'),
            'description' => Arr::get($payload, 'description'),
            'specialization' => Arr::get($payload, 'specialization'),
            'max_salary' => Arr::get($payload, 'max_salary'),
            'min_salary' => Arr::get($payload, 'min_salary'),
            'currency' => Arr::get($payload, 'currency'),
            'address' => Arr::get($payload, 'address'),
            'latitude' => Arr::get($payload, 'latitude'),
            'longitude' => Arr::get($payload, 'longitude'),
            'company_id' => $user->id,
            'status' => JobOffer::STATUS_CART,
            'locale' => app()->getLocale(),
            'category_id' => Arr::get($payload, 'category'),
        ];
        
        $jobOffer = JobOffer::create($data);
        $jobOffer->tags()->sync($tags);

        return redirect()->route('company.payment.packages', [$user, $jobOffer]);
    }

    public function edit(User $user, JobOffer $jobOffer)
    {
        $jobOffer->load('tags.tagGroup');

        return Inertia::render('Company/JobOffers/Edit', [
            'jobOffer' => $jobOffer,
            'company' => $user,
            'tags' => $jobOffer->tags->groupBy('tagGroup.type')->map(fn ($tagGroup) => $tagGroup->map(fn ($tag) => $tag->id))->toArray(),
            'categories' => Category::getOptions(app()->getLocale()),
            'sectors' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_SECTOR, $user->id, app()->getLocale()),
            'industries' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_INDUSTRY, $user->id, app()->getLocale()),
            'languages' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_LANGUAGE, $user->id, app()->getLocale()),
            'processes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_PROCESS_TYPE, $user->id, app()->getLocale()),
            'machineTypes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE_TYPE, $user->id, app()->getLocale()),
            'machines' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE, $user->id, app()->getLocale()),
            'techSkills' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_TECH_SKILLS, $user->id, app()->getLocale()),
            'exp' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_EXP, $user->id, app()->getLocale()),
            'contracts' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_CONTRACT, $user->id, app()->getLocale()),
            'packages' => JobOfferType::getOptions(app()->getLocale())
        ]);
    }

    public function update(User $user, JobOffer $jobOffer, JobOfferEditRequest $request)
    {
        $payload = $request->validated();
        $tags = $this->prepareTagsJobOffer($payload, $user);

        $data = [
            'title' => Arr::get($payload, 'title'),
            'description' => Arr::get($payload, 'description'),
            'specialization' => Arr::get($payload, 'specialization'),
            'max_salary' => Arr::get($payload, 'max_salary'),
            'min_salary' => Arr::get($payload, 'min_salary'),
            'currency' => Arr::get($payload, 'currency'),
            'address' => Arr::get($payload, 'address'),
            'latitude' => Arr::get($payload, 'latitude'),
            'longitude' => Arr::get($payload, 'longitude'),
            'company_id' => $user->id,
            'status' => $jobOffer->status,
            'locale' => app()->getLocale(),
            'category_id' => Arr::get($payload, 'category'),
        ];
        
        $jobOffer->update($data);
        $jobOffer->tags()->sync($tags);

        if ($jobOffer->status == $jobOffer::STATUS_ACTIVE)
        {
            $jobOffer->status = $jobOffer::STATUS_UNDER_APPROVAL;
            $jobOffer->save();
        }

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('The job offer has been modified successfully.')
        ]);
    }

    public function destroy(User $user, JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return redirect()->route('company.joboffers.index', $user)->with('message', [
            'type' => 'success',
            'content' => __('The job offer was deleted successfully.')
        ]);
    }

    private function prepareTagsJobOffer($data, $user)
    {
        $tags = [];
        $newTags = [];

        if (Arr::has($data, 'sectors') && Arr::get($data, 'sectors') != 'no validation' && !empty(Arr::get($data, 'sectors')))
        {
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_SECTOR, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_INDUSTRY, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_LANGUAGE, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_PROCESS_TYPE, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_MACHINE_TYPE, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_MACHINE, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_TECH_SKILLS, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_EXP, app()->getLocale());

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
            $group = TagGroup::getByType(TagGroup::GROUP_TYPE_CONTRACT, app()->getLocale());

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
                    'is_active' => false,
                    'tag_group_id' => Arr::get($tag[array_key_first($tag)], 'groupId'),
                    'locale' => Arr::get($tag[array_key_first($tag)], 'groupLocale'),
                    'position' => 0,
                    'suggested_by' => $user->id,
                    'is_approved' => false
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
