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
use Illuminate\Support\Str;
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
        $jobOffers = JobOffer::getByUser($user->id, getCountry(), 10, $filters);

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
                
                $item->published_at_formatted = optional($item->published_at)->format('d-m-Y');
                $item->canUpgrade = JobOfferType::getMoreExpensivePackages($item->jobOfferType->price, getCountry())->isNotEmpty();
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

    public function store(User $user, JobOfferCreateRequest $request)
    {
        $payload = $request->validated();
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
            'status' => JobOffer::STATUS_CART,
            'locale' => getCountry(),
            'category_id' => Arr::get($payload, 'category'),
        ];
        
        $jobOffer = JobOffer::create($data);
        $jobOffer->tags()->sync($tags);

        return redirect()->route('company.payment.packages', [$user, $jobOffer]);
    }

    public function edit(User $user, JobOffer $jobOffer)
    {
        if (getCountry() != $jobOffer->locale)
        {
            return redirect()->route('company.joboffers.index', $user)->with('message', [
                'type' => 'info',
                'content' => __('The job offer that you were editing is posted in another country. If yuo want to edit it, please change the country.')
            ]);
        }

        $jobOffer->load('tags.tagGroup');

        return Inertia::render('Company/JobOffers/Edit', [
            'jobOffer' => $jobOffer,
            'company' => $user,
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

    public function update(User $user, JobOffer $jobOffer, JobOfferEditRequest $request)
    {
        $payload = $request->validated();
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
