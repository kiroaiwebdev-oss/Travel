<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        // Trending destinations (admin-editable). Real Unsplash imagery.
        $destinations = [
            ['Goa', 'Beaches', 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=600&q=80'],
            ['Dubai', 'Luxury', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80'],
            ['Bali', 'Islands', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80'],
            ['Manali', 'Mountains', 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=600&q=80'],
            ['Jaipur', 'Heritage', 'https://images.unsplash.com/photo-1599661046289-e31897846e41?w=600&q=80'],
            ['Singapore', 'City', 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=600&q=80'],
            ['Kerala', 'Backwaters', 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=600&q=80'],
            ['Thailand', 'Tropical', 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=600&q=80'],
        ];

        foreach ($destinations as $i => [$name, $tag, $image]) {
            Destination::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'tag' => $tag, 'image_url' => $image, 'category' => 'hotels', 'sort_order' => $i, 'is_active' => true]
            );
        }

        // A few approved reviews so the landing page section isn't empty.
        $reviews = [
            ['Aarav S.', 'Trip to Goa', 5, 'Got ₹3,200 back on my Goa trip. Booking was exactly the same as always — money hit my UPI in 2 days!'],
            ['Meera K.', 'Dubai Holiday', 5, 'Compared flight + hotel prices in one place and earned ₹8,500 cashback. The AI even suggested the best areas to stay.'],
            ['Dev P.', 'Manali Weekend', 5, 'Was skeptical at first but found cheaper hotels than other apps, plus ₹1,800 back. Now I always check TripCash first.'],
            ['Priya R.', 'Kerala Backwaters', 4, 'Booked a houseboat and flights through TripCash. Loved Kerala and loved the ₹4,100 cashback even more!'],
        ];

        foreach ($reviews as [$name, $location, $rating, $message]) {
            Review::firstOrCreate(
                ['name' => $name, 'message' => $message],
                ['location' => $location, 'rating' => $rating, 'type' => 'review', 'status' => 'approved', 'is_featured' => true]
            );
        }
    }
}
