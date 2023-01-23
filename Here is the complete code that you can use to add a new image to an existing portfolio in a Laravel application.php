<?php

    public function update(Request $request, $id)
    {
        $updateArtVentureServiceItem = ArtVentureItem::findOrFail($id);

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
        return redirect()->route();
    }
}
