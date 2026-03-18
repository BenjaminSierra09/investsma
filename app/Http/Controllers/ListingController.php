<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingInquiryRequest;
use App\Mail\ListingInquiryMail;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(): View
    {
        return $this->renderIndex();
    }

    public function sales(): View
    {
        return $this->renderIndex('sale');
    }

    public function rentals(): View
    {
        return $this->renderIndex('rent');
    }

    public function show(Listing $listing): View
    {
        abort_unless($listing->status === 'published', 404);

        return view('public.listings-show', [
            'listing' => $listing,
            'gallery' => collect($listing->gallery)
                ->filter()
                ->prepend($listing->cover_image)
                ->filter()
                ->unique()
                ->values(),
        ]);
    }

    public function inquire(StoreListingInquiryRequest $request, Listing $listing): RedirectResponse
    {
        abort_unless($listing->status === 'published', 404);

        $data = $request->validated();

        Mail::to($listing->contact_email ?: 'info@investsma.com')
            ->send(new ListingInquiryMail($listing, $data));

        return back()->with('listing_inquiry_status', 'Gracias, recibimos tu mensaje. Te contactamos en breve.');
    }

    private function renderIndex(?string $listingType = null): View
    {
        $query = Listing::query()
            ->published()
            ->latest('featured')
            ->latest('published_at')
            ->latest('id');

        if ($listingType) {
            $query->where('listing_type', $listingType);
        }

        return view('public.listings-index', [
            'listings' => $query->get(),
            'listingType' => $listingType,
        ]);
    }
}
