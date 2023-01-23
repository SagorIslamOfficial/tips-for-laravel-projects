<?php

namespace App\Http\Controllers\BackEnd\Company;

use App\Http\Controllers\Controller;
use App\Models\BackEnd\Company\AddCompany;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AddCompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $indexAddCompanies = AddCompany::all();
        return view('backEnd.company.add-company.index', compact('indexAddCompanies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('backEnd.company.add-company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'text' => 'required',
            'link' => 'required',
            'image' => 'required|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:100'
        ]);

        $storeAddCompany = new AddCompany();

        $storeAddCompany->name = $request->name;
        $storeAddCompany->slug = Str::slug($request->name);
        $storeAddCompany->text = $request->text;
        $storeAddCompany->link = $request->link;

        //Find form image and storing into a variable
        $image = $request->file('image');

        //Using if statement and ensuring form data is available
        if (isset($image)) {
            //Creating image slug
            $slug = str_slug($request->name);

            //Make unique name for image
            $imageName = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            //Checking image storage folder, if not available then create a folder
            if (!Storage::disk('public')->exists('company/companies')) {
                Storage::disk('public')->makeDirectory('company/companies');
            }

            //Save image and resize image
            $imageResize = Image::make($image)->resize(110, 110)->stream();

            //And now put the image into storage disk
            Storage::disk('public')->put('company/companies/' . $imageName, $imageResize);

            //And save data into the database
            $storeAddCompany->image = $imageName;
        }

        $storeAddCompany->save();

        return redirect()->route('add-company.index')->with('success', 'Add Company Saved Successfully :)');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $editAddCompany = AddCompany::findOrFail($id);
        return view('backEnd.company.add-company.edit', compact('editAddCompany'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'text' => 'required',
            'link' => 'required',
            'image' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:100'
        ]);

        $updateAddCompany = AddCompany::findOrFail($id);

        $updateAddCompany->name = $request->name;
        $updateAddCompany->slug = Str::slug($request->name);
        $updateAddCompany->text = $request->text;
        $updateAddCompany->link = $request->link;

        //Find form image and storing into a variable
        $image = $request->file('image');

        //Using if statement and ensuring form data is available
        if (isset($image)) {
            //Creating image slug
            $slug = str_slug($request->name);

            //Make unique name for image
            $imageName = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            //Checking image storage folder, if not available then create a folder
            if (!Storage::disk('public')->exists('company/companies')) {
                Storage::disk('public')->makeDirectory('company/companies');
            }
            //Delete old image
            if (Storage::disk('public')->exists('company/companies/' . $updateAddCompany->image)) {
                Storage::disk('public')->delete('company/companies/' . $updateAddCompany->image);
            }

            //Save image and resize image
            $imageResize = Image::make($image)->resize(110, 110)->stream();

            //And now put the image into storage disk
            Storage::disk('public')->put('company/companies/' . $imageName, $imageResize);

            //And save data into the database
            $updateAddCompany->image = $imageName;
        }

        $updateAddCompany->save();

        return redirect()->route('add-company.index')->with('success', 'Add Company Updated Successfully :)');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $destroyAddCompany = AddCompany::findOrfail($id);

        if (Storage::disk('public')->exists('company/companies/' . $destroyAddCompany->image)) {
            Storage::disk('public')->delete('company/companies/' . $destroyAddCompany->image);
        }

        $destroyAddCompany->delete();

        return redirect()->route('add-company.index')->with('success', 'Add Company Deleted successfully !');
    }
}
