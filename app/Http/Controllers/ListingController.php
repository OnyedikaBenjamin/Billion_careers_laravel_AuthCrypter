<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListingController extends Controller
{
    // public function showAllListing(){
    //     return view('listings', [
    //         'listings' => Listing::all()
    //     ]);}
    // public function showAllListing(){
    //     $listings = Listing::paginate();

    //     return view('listings', compact('listings'));
    // }
    public function showAllListing()
    {
        $listings = Listing::paginate(10); // Paginate with 3 listings per page
        return view('listings', [
            'listings' => $listings,
        ]);
    }


    public function showSingleListing($id)
    {
        $listing = Listing::find($id);
        if ($listing) {
            return view('singleListing', [
                'singleListing' => Listing::find($id)
            ]);
        } else {
            abort('404');
        }
    }

    public function createListing()
    {
        return view('create');
    }

    public function jobPostSuccessful()
    {
        return view('successful-post');
    }
    public function jobPostUpdated()
    {
        return view('jobupdated');
    }

    public function storeListing(Request $request)
    {
        // dd($request ->file('logo'));
        $formFields = $request->validate([
            'role' => 'required',
            'location' => 'required',
            'job_type' => 'required',
            'description' => 'required',
            'salary_range' => 'required',
            'yearly_salary' => 'required',
            'no_vacancy' => 'required',
            'company' => 'required',
            'email' => ['required', 'email'],
            'company_description' => 'required',
            'website' => 'required',
            'logo' => 'required'
        ]);

        if ($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();  // maps the user creating the post to the post

        Listing::create($formFields);
        return redirect('/jobcreated');
    }
    public function editListing(Listing $listing)
    {
            return view('edit', ['fetchedListing' => $listing]);
    }

    public function updateListing(Request $request, Listing $listing){
        if($listing->user_id != auth()->id()) {   // Make sure logged in user is owner
            abort(403, 'Unauthorized Action');
        }
        $formFields = $request->validate([
            'role' => 'required',
            'location' => 'required',
            'job_type' => 'required',
            'description' => 'required',
            'salary_range' => 'required',
            'yearly_salary' => 'required',
            'no_vacancy' => 'required',
            'company' => 'required',
            'email' => ['required', 'email'],
            'company_description' => 'required',
            'website' => 'required',
            'logo' => 'required'
        ]);

        if ($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');}
        $listing->update($formFields);
        return redirect('/jobupdated');
    }

    public function deleteListing(Listing $listing){
        if($listing->user_id != auth()->id()) {   // Make sure logged in user is owner
            abort(403, 'Unauthorized Action');}
         $listing->delete();  
         return redirect('/');
    }

    public function manageListing(){
        return view('edit',  ['listings' => auth()->user()->listings()->get()]);
    }

}