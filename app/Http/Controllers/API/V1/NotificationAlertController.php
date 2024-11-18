<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class NotificationAlertController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($notifications, 'success', 200);
    }
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $notification->is_read = true;
        $notification->save();


        return $this->success(null, 'success', 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
