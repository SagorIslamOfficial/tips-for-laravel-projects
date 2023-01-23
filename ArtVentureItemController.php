<?php

namespace App\Http\Controllers\BackEnd\Company\SingleCompany\ArtVenture\Service;

use App\Http\Controllers\Controller;
use App\Models\BackEnd\Company\SingleCompany\ArtVenture\Service\ArtVentureCategory;
use App\Models\BackEnd\Company\SingleCompany\ArtVenture\Service\ArtVentureItem;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ArtVentureItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $indexArtVentureServiceItems = ArtVentureItem::all();
        return view('backEnd.company.companies.art-venture.service.item.index', compact('indexArtVentureServiceItems'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $getArtVentureServiceCategories = ArtVentureCategory::all();
        return view('backEnd.company.companies.art-venture.service.item.create', compact('getArtVentureServiceCategories'));
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
            'art_venture_category_id' => 'required',
            'name' => 'required',
            'company' => 'required',

            'image' => 'required|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:100',
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp',

            'project_heading' => 'required',
            'project_description' => 'required',
            'portfolio_heading' => 'required',

            'portfolio_images' => 'required',
            'portfolio_images.*' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp'
        ]);

        $storeArtVentureServiceItem = new ArtVentureItem();

        $storeArtVentureServiceItem->art_venture_category_id = $request->art_venture_category_id;
        $storeArtVentureServiceItem->name = $request->name;
        $storeArtVentureServiceItem->slug = Str::slug($request->name);
        $storeArtVentureServiceItem->company = $request->company;

        //Find form image and storing into a variable
        $image = $request->file('image');

        //Using if statement and ensuring form data is available
        if (isset($image)) {
            //Creating image slug
            $slug = str_slug($request->name);

            //Make unique name for image
            $imageName = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            //Checking image storage folder, if not available then create a folder
            if (!Storage::disk('public')->exists('company/all-company/art-venture/service/item')) {
                Storage::disk('public')->makeDirectory('company/all-company/art-venture/service/item');
            }

            //Save image and resize image
            $imageResize = Image::make($image)->resize(260, 260)->stream();

            //And now put the image into storage disk
            Storage::disk('public')->put('company/all-company/art-venture/service/item/' . $imageName, $imageResize);

            //And save data into the database
            $storeArtVentureServiceItem->image = $imageName;
        }

        $itemImages = $request->file('images');
        $images = [];
        if (isset($itemImages)) {

            foreach ($request->file('images') as $file) {
                $slug = str_slug($request->name);
                $itemImageName = $slug . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                if (!Storage::disk('public')->exists('company/all-company/art-venture/service/item/details')) {
                    Storage::disk('public')->makeDirectory('company/all-company/art-venture/service/item/details');
                }

                $portfolioItemImage = Image::make($file)->resize(1115, 515)->stream();
                Storage::disk('public')->put('company/all-company/art-venture/service/item/details/' . $itemImageName, $portfolioItemImage);

                $images[] = $itemImageName;
            }
            $storeArtVentureServiceItem->images = json_encode($images);
        }

        $storeArtVentureServiceItem->project_heading = $request->project_heading;
        $storeArtVentureServiceItem->project_description = $request->project_description;
        $storeArtVentureServiceItem->project_details_heading = $request->project_details_heading;
        $storeArtVentureServiceItem->project_client = $request->project_client;
        $storeArtVentureServiceItem->project_client_content = $request->project_client_content;
        $storeArtVentureServiceItem->project_date = $request->project_date;
        $storeArtVentureServiceItem->project_date_content = $request->project_date_content;
        $storeArtVentureServiceItem->project_skills = $request->project_skills;
        $storeArtVentureServiceItem->project_skills_content = $request->project_skills_content;
        $storeArtVentureServiceItem->project_url = $request->project_url;
        $storeArtVentureServiceItem->project_url_content = $request->project_url_content;
        $storeArtVentureServiceItem->project_link = $request->project_link;
        $storeArtVentureServiceItem->portfolio_heading = $request->portfolio_heading;

        $itemImages = $request->file('portfolio_images');
        $images = [];
        if (isset($itemImages)) {

            foreach ($request->file('portfolio_images') as $file) {
                $slug = str_slug($request->name);
                $itemImageName = $slug . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                if (!Storage::disk('public')->exists('company/all-company/art-venture/service/item/portfolio')) {
                    Storage::disk('public')->makeDirectory('company/all-company/art-venture/service/item/portfolio');
                }

                $portfolioItemImage = Image::make($file)->stream();
                Storage::disk('public')->put('company/all-company/art-venture/service/item/portfolio/' . $itemImageName, $portfolioItemImage);

                $images[] = $itemImageName;
            }
            $storeArtVentureServiceItem->portfolio_images = json_encode($images);
        }

        $storeArtVentureServiceItem->save();

        return redirect()->route('art-venture-item.index')->with('success', 'Art Venture Service Item Saved Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $viewArtVentureServiceItem = ArtVentureItem::findOrFail($id);
        return view('backEnd.company.companies.art-venture.service.item.show', compact('viewArtVentureServiceItem'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $editArtVentureServiceItem = ArtVentureItem::findOrFail($id);
        $editArtVentureServiceCategory = ArtVentureCategory::all();
        return view('backEnd.company.companies.art-venture.service.item.edit', compact('editArtVentureServiceItem', 'editArtVentureServiceCategory'));
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
            'art_venture_category_id' => 'required',
            'name' => 'required',
            'company' => 'required',

            'image.*' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:100',
//            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp',

            'project_heading' => 'required',
            'project_description' => 'required',
            'portfolio_heading' => 'required',

//            'portfolio_images' => 'required',
            'portfolio_images.*' => 'image|mimes:jpg,jpeg,png,bmp,gif,svg,webp'
        ]);

        $updateArtVentureServiceItem = ArtVentureItem::findOrFail($id);

        $updateArtVentureServiceItem->art_venture_category_id = $request->art_venture_category_id;
        $updateArtVentureServiceItem->name = $request->name;
        $updateArtVentureServiceItem->slug = Str::slug($request->name);
        $updateArtVentureServiceItem->company = $request->company;

        //Find form image and storing into a variable
        $image = $request->file('image');

        //Using if statement and ensuring form data is available
        if (isset($image)) {
            //Creating image slug
            $slug = str_slug($request->name);

            //Make unique name for image
            $imageName = $slug . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            //Checking image storage folder, if not available then create a folder
            if (!Storage::disk('public')->exists('company/all-company/art-venture/service/item')) {
                Storage::disk('public')->makeDirectory('company/all-company/art-venture/service/item');
            }
            //Delete old image
            if (Storage::disk('public')->exists('company/all-company/art-venture/service/item/' . $updateArtVentureServiceItem->image)) {
                Storage::disk('public')->delete('company/all-company/art-venture/service/item/' . $updateArtVentureServiceItem->image);
            }

            //Save image and resize image
            $imageResize = Image::make($image)->resize(260, 260)->stream();

            //And now put the image into storage disk
            Storage::disk('public')->put('company/all-company/art-venture/service/item/' . $imageName, $imageResize);

            //And save data into the database
            $updateArtVentureServiceItem->image = $imageName;
        }

        $itemImages = $request->file('images');
        $existingImages = json_decode($updateArtVentureServiceItem->images);
        if (isset($itemImages)) {

            foreach ($request->file('images') as $file) {
                $slug = str_slug($request->name);
                $itemImageName = $slug . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                if (!Storage::disk('public')->exists('company/all-company/art-venture/service/item/details')) {
                    Storage::disk('public')->makeDirectory('company/all-company/art-venture/service/item/details');
                }

                $portfolioItemImage = Image::make($file)->resize(1115, 515)->stream();
                Storage::disk('public')->put('company/all-company/art-venture/service/item/details/' . $itemImageName, $portfolioItemImage);

                $existingImages[] = $itemImageName;
            }
            $updateArtVentureServiceItem->images = json_encode($existingImages);
        }

        $updateArtVentureServiceItem->project_heading = $request->project_heading;
        $updateArtVentureServiceItem->project_description = $request->project_description;
        $updateArtVentureServiceItem->project_details_heading = $request->project_details_heading;
        $updateArtVentureServiceItem->project_client = $request->project_client;
        $updateArtVentureServiceItem->project_client_content = $request->project_client_content;
        $updateArtVentureServiceItem->project_date = $request->project_date;
        $updateArtVentureServiceItem->project_date_content = $request->project_date_content;
        $updateArtVentureServiceItem->project_skills = $request->project_skills;
        $updateArtVentureServiceItem->project_skills_content = $request->project_skills_content;
        $updateArtVentureServiceItem->project_url = $request->project_url;
        $updateArtVentureServiceItem->project_url_content = $request->project_url_content;
        $updateArtVentureServiceItem->project_link = $request->project_link;
        $updateArtVentureServiceItem->portfolio_heading = $request->portfolio_heading;

        $itemImages = $request->file('portfolio_images');
        $existingImages = json_decode($updateArtVentureServiceItem->portfolio_images);
        if (isset($itemImages)) {

            foreach ($itemImages as $file) {
                $slug = str_slug($request->name);
                $itemImageName = $slug . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                if (!Storage::disk('public')->exists('company/all-company/art-venture/service/item/portfolio')) {
                    Storage::disk('public')->makeDirectory('company/all-company/art-venture/service/item/portfolio');
                }

                $portfolioItemImage = Image::make($file)->stream();
                Storage::disk('public')->put('company/all-company/art-venture/service/item/portfolio/' . $itemImageName, $portfolioItemImage);

                $existingImages[] = $itemImageName;
            }
            $updateArtVentureServiceItem->portfolio_images = json_encode($existingImages);
        }

        $updateArtVentureServiceItem->save();
        return redirect()->route('art-venture-item.index')->with('success', 'Art Venture Service Item Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $destroyArtVentureServiceItem = ArtVentureItem::findOrfail($id);

        if (Storage::disk('public')->exists('company/all-company/art-venture/service/item/' . $destroyArtVentureServiceItem->image)) {
            Storage::disk('public')->delete('company/all-company/art-venture/service/item/' . $destroyArtVentureServiceItem->image);
        }

        $getImages = json_decode($destroyArtVentureServiceItem->images);
        foreach ($getImages as $image) {
            if (Storage::disk('public')->exists('company/all-company/art-venture/service/item/details/' . $image)) {
                Storage::disk('public')->delete('company/all-company/art-venture/service/item/details/' . $image);
            }
        }

        $getImages = json_decode($destroyArtVentureServiceItem->portfolio_images);
        foreach ($getImages as $image) {
            if (Storage::disk('public')->exists('company/all-company/art-venture/service/item/portfolio/' . $image)) {
                Storage::disk('public')->delete('company/all-company/art-venture/service/item/portfolio/' . $image);
            }
        }

        $destroyArtVentureServiceItem->delete();

        return redirect()->route('art-venture-item.index')->with('success', 'Art Venture Service Item Deleted successfully');
    }
}
