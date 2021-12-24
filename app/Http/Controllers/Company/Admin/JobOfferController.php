<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobOfferCreateRequest;
use App\Models\Tag;
use App\Models\TagGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobOfferController extends Controller
{
    public function create(User $user)
    {
        return Inertia::render('Company/JobOffersCreate', [
            'company' => $user,
            'sectors' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_SECTOR, $user->id, app()->getLocale()),
            'industries' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_INDUSTRY, $user->id, app()->getLocale()),
            'languages' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_LANGUAGE, $user->id, app()->getLocale()),
            'processes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_PROCESS_TYPE, $user->id, app()->getLocale()),
            'machineTypes' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE_TYPE, $user->id, app()->getLocale()),
            'machines' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_MACHINE, $user->id, app()->getLocale()),
            'techSkills' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_TECH_SKILLS, $user->id, app()->getLocale()),
            'exp' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_EXP, $user->id, app()->getLocale()),
            'contracts' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_CONTRACT, $user->id, app()->getLocale()),
        ]);
    }

    public function store(User $user, JobOfferCreateRequest $request)
    {
        dd($request->all());
    }
}
