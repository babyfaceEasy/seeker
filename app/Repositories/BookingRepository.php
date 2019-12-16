<?php


namespace App\Repositories;


use App\Models\Booking;
use App\Constants\Status;

class BookingRepository implements BookingRepositoryInterface
{

    /**
     * Returns all bookings in the system.
     * @return Booking[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Booking::all();
    }

    /**
     * book a service.
     * @param array $details
     * @return string
     */
    public function bookService(array $details)
    {
        $bookingDetails = Booking::create($details);

        if (empty($bookingDetails)){
            return Status::ERROR;
        }
        return $bookingDetails;
    }
}