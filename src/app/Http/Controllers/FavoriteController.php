<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Content $content)
    {
        $favorite = Favorite::where('user_id', $request->user()->id)
            ->where('content_id', $content->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'お気に入りから外しました。';
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'content_id' => $content->id,
            ]);
            $message = 'お気に入りに追加しました。';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'favorited' => !$favorite,
                'count' => $content->favorites()->count(),
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }
}
