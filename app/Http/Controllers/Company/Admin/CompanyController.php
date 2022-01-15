<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoiceDataRequest;
use App\Models\CompanyDetail;
use App\Models\InvoiceDetail;
use App\Models\Tag;
use App\Models\TagGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser');
    }

    public function index(User $user)
    {
        return Inertia::render('Company/Dashboard', [
            'company' => $user
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Company/Profile', [
            'company' => $user,
            'companySectors' => $user->tags->pluck('id'),
            'sectors' => Tag::getOptionsBasedOnType(TagGroup::GROUP_TYPE_SECTOR, $user->id, app()->getLocale())
        ]);
    }

    public function edit(User $user, Request $request)
    {
        $request->validate($this->validationRules($request->hasFile('logo')));

        $user->update([
            'email' => $request->email
        ]);
        
        $newSectors = [];
        $sectors = [];

        foreach ($request->get('sectors') as $sector) 
        {
            is_string($sector) ? array_push($newSectors, $sector) : array_push($sectors, $sector);
        }
        
        if (!empty($newSectors))
        {
            $tagsGroup = TagGroup::getByType(TagGroup::GROUP_TYPE_SECTOR, app()->getLocale());
            $newCreatedTags = [];

            foreach ($newSectors as $sector) {
                $tag = Tag::create([
                    'name' => $sector,
                    'is_active' => false,
                    'tag_group_id' => $tagsGroup->id,
                    'locale' => $tagsGroup->locale,
                    'position' => 0,
                    'suggested_by' => $user->id,
                    'is_approved' => false
                ]);

                $newCreatedTags[] = $tag->id;
            }
        }

        if (isset($newCreatedTags))
        {
            $sectors = array_merge($sectors, $newCreatedTags);
        }

        $user->tags()->sync($sectors);

        $oldLogo = '';
        if ($user->getHasCompanyDetails())
            $oldLogo = $user->detail->logo;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $imageName = $logo->getClientOriginalName();
            $logo->move(public_path() . '/img/', $imageName);

            if (!empty($oldLogo))
                $this->deleteImage($oldLogo);
        }

        $detail = CompanyDetail::updateOrCreate([
            'user_id' => $user->id
        ],[
            'name' => $request->name,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'logo' => !empty($imageName) ? $imageName : (!empty($oldLogo) ? $oldLogo : null),
            'website_link' => $request->website_link,
            'phone' => $request->phone,
            'description' => $request->description,
            'is_agency' => $request->is_agency,
            'contact_name' => $request->contact_name
        ]);

        if (!$user->getHasCompanyDetails())
            $detail->user()->save($user);

        return redirect()->route('company.profile.show', $user)->with('message', [
            'type' => 'success',
            'content' => __('Your data has been successfully saved.')
        ]);
    }

    public function invoiceData(User $user)
    {
        return Inertia::render('Company/InvoiceData', [
            'company' => $user->load('invoiceDetails')
        ]);
    }

    public function editInvoiceData(User $user, InvoiceDataRequest $request)
    {
        InvoiceDetail::updateOrCreate([
            'user_id' => $user->id
        ], array_merge($request->validated(), ['is_completed' => true]));

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => __('Your data has been successfully saved.')
        ]);
    }

    public function pricing()
    {
        return Inertia::render('Company/Pricing');
    }

    private function deleteImage($imageName)
    {
        if(File::exists(public_path('img/' . $imageName)))
            File::delete(public_path('img/' . $imageName));
    }

    private function validationRules($hasLogo)
    {
        $validationRules = [
            'name' => 'required|max:255',
            'email' => 'email|required',
            'address' => 'string|required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'required|max:20|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'is_agency' => 'required',
            'description' => 'max:500',
            'contact_name' => 'required|max:255',
            'sectors' => 'required'
        ];

        if ($hasLogo)
            $validationRules['logo'] = 'image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000';

        return $validationRules;
    }
}
