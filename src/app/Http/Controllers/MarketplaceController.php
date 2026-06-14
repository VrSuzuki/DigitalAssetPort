<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\CartItem;
use App\Models\Genre;
use App\Models\LibraryItem;
use App\Models\Order;
use App\Models\SubGenre;
use App\Models\User;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        return view('marketplace.index', [
            'contents' => $this->contentQuery($request)->paginate($this->perPage($request))->withQueryString(),
            'genres' => Genre::with('subGenres')->withCount('contents')->orderByDesc('contents_count')->get(),
            'authors' => User::whereHas('contents')->withCount('contents')->orderByDesc('contents_count')->get(),
            'followingUsers' => $this->followingUsers(),
            'formats' => $this->formats(),
            'sorts' => $this->sorts(),
            'activeFilters' => $this->activeFilters($request),
        ]);
    }

    public function about()
    {
        return view('marketplace.about', [
            'genres' => Genre::with('subGenres')->withCount('contents')->get(),
        ]);
    }

    public function search(Request $request)
    {
        return view('marketplace.search', [
            'genres' => Genre::with('subGenres')->get(),
            'subGenres' => SubGenre::with('genre')->get(),
            'formats' => $this->formats(),
            'sorts' => $this->sorts(),
        ]);
    }

    public function show(Content $content)
    {
        abort_unless($content->status === 'published', 404);

        $content->load([
            'author',
            'genre',
            'subGenre',
            'tags',
            'images',
            'comments.user',
        ])->loadCount(['favorites', 'comments']);

        $purchaseOrder = null;
        $hasPurchased = false;
        $inCart = false;
        $isFavorite = false;

        if (auth()->check()) {
            $purchaseOrder = Order::where('user_id', auth()->id())
                ->whereHas('items', function ($query) use ($content) {
                    $query->where('content_id', $content->id);
                })
                ->latest('purchased_at')
                ->first();

            $hasPurchased = LibraryItem::where('user_id', auth()->id())
                ->where('content_id', $content->id)
                ->exists();

            $inCart = CartItem::where('content_id', $content->id)
                ->whereHas('cart', function ($query) {
                    $query->where('user_id', auth()->id())->where('active', true);
                })
                ->exists();

            $isFavorite = $content->favorites()
                ->where('user_id', auth()->id())
                ->exists();
        }

        $authorMore = Content::published()
            ->where('user_id', $content->user_id)
            ->whereKeyNot($content->id)
            ->with(['author', 'genre', 'subGenre'])
            ->withCount(['favorites', 'comments'])
            ->latest()
            ->take(4)
            ->get();

        $related = Content::published()
            ->where('genre_id', $content->genre_id)
            ->whereKeyNot($content->id)
            ->with(['author', 'genre', 'subGenre'])
            ->withCount(['favorites', 'comments'])
            ->orderByDesc('favorites_count')
            ->take(4)
            ->get();

        return view('marketplace.show', compact('content', 'authorMore', 'related', 'purchaseOrder', 'hasPurchased', 'inCart', 'isFavorite'));
    }

    private function contentQuery(Request $request)
    {
        $query = Content::published()
            ->with(['author', 'genre', 'subGenre', 'tags'])
            ->withCount(['favorites', 'comments']);

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('tags', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('exclude_keyword')) {
            $exclude = $request->input('exclude_keyword');
            $query->where('title', 'not like', "%{$exclude}%")
                ->where('description', 'not like', "%{$exclude}%");
        }

        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', 'like', "%{$tag}%");
            });
        }

        if ($request->filled('format')) {
            $query->where('format', $request->input('format'));
        }

        if ($request->filled('genre')) {
            $query->where('genre_id', $request->input('genre'));
        }

        if ($request->filled('sub_genre')) {
            $query->where('sub_genre_id', $request->input('sub_genre'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        switch ($request->input('sort', 'newest')) {
            case 'favorites':
                $query->orderByDesc('favorites_count');
                break;
            case 'rating_count':
                $query->orderByDesc('ratings_count');
                break;
            case 'rating_rate':
                $query->orderByDesc('rating_rate');
                break;
            case 'comments':
                $query->orderByDesc('comments_count');
                break;
            case 'oldest':
                $query->orderBy('published_at');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'newest':
            default:
                $query->latest('published_at');
                break;
        }

        return $query;
    }

    private function perPage(Request $request)
    {
        $perPage = (int) $request->input('per_page', 20);

        return in_array($perPage, [20, 50, 100], true) ? $perPage : 20;
    }

    private function followingUsers()
    {
        if (!auth()->check()) {
            return collect();
        }

        return auth()->user()
            ->following()
            ->withCount('contents')
            ->orderByDesc('contents_count')
            ->get();
    }

    private function formats()
    {
        return [
            'text' => 'テキスト',
            'image' => '画像',
            'gif' => 'GIF',
            'audio' => '音声',
            'video' => '動画',
            'model_3d' => '3Dモデル',
            'animation' => 'アニメーション',
            'system' => 'システム',
            'external_tool' => '外部ツールデータ',
            'other' => 'その他',
        ];
    }

    private function sorts()
    {
        return [
            'favorites' => 'お気に入りが多い順',
            'rating_count' => '評価が多い順',
            'rating_rate' => '評価率が高い順',
            'comments' => 'コメントが多い順',
            'newest' => '発売日が新しい順',
            'oldest' => '発売日が古い順',
            'price_low' => '価格が安い順',
            'price_high' => '価格が高い順',
        ];
    }

    private function activeFilters(Request $request)
    {
        $filters = collect();

        if ($request->filled('exclude_keyword')) {
            $filters->push(['key' => 'exclude_keyword', 'label' => '除外: '.$request->input('exclude_keyword')]);
        }

        if ($request->filled('tag')) {
            $filters->push(['key' => 'tag', 'label' => 'タグ: '.$request->input('tag')]);
        }

        if ($request->filled('format')) {
            $filters->push(['key' => 'format', 'label' => '形式: '.($this->formats()[$request->input('format')] ?? $request->input('format'))]);
        }

        if ($request->filled('genre')) {
            $genre = Genre::find($request->input('genre'));
            $filters->push(['key' => 'genre', 'label' => 'ジャンル: '.optional($genre)->name]);
        }

        if ($request->filled('sub_genre')) {
            $subGenre = SubGenre::find($request->input('sub_genre'));
            $filters->push(['key' => 'sub_genre', 'label' => 'サブジャンル: '.optional($subGenre)->name]);
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $filters->push([
                'key' => 'price',
                'remove' => ['min_price', 'max_price'],
                'label' => '価格: ¥'.number_format((int) $request->input('min_price', 0)).'〜¥'.number_format((int) $request->input('max_price', 10000)),
            ]);
        }

        return $filters->filter(fn ($filter) => filled($filter['label'] ?? null));
    }
}
