<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Wishlist::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'game_id' => 'required'
        ]);
        $userID = auth()->user()->id;
        $wishlist = Wishlist::create([
            'game_id' => $fields['game_id'],
            'user_id' => $userID
        ]);

        $game = Game::find($fields['game_id']);
        $game->isOnWishlist = true;

        $game->update();

        $games_all = Game::all();
        $games_by_date = Game::orderBy('created_at', 'ASC')->get();
        $games_by_rating = Game::where('rating', '>=', 4)->orderByDesc('rating')->get();
        $games_by_downloads = Game::orderBy('downloads', 'DESC')->get();
        $categories = Category::all();

        $OnWishlist = Wishlist::where('user_id', auth()->user()->id)->get();
        if ($OnWishlist) {
            $ItemsOnWishlist = $OnWishlist->count();
        } else {
            $ItemsOnWishlist = 0;
        }

        $games = Game::where('user_id', auth()->user()->id);
        if ($games) {
            $allGames = $games->count();
        } else {
            $allGames = 0;
        }

        return [
            'message' => 'game added to wishlist',
            'new_games' => $games_by_date,
            'top_games' => $games_by_rating,
            'most_downloaded' => $games_by_downloads,
            'user_games' => $games_all,
            'wishlist' => $ItemsOnWishlist,
            'games_count' => $allGames,
            'categories' => $categories,
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Wishlist::find($id);
        return $item;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Wishlist::where('game_id', $id);
        $response = $item->delete();

        $game = Game::find($id);
        $game->isOnWishlist = false;
        $game->update();

        $games_all = Game::all();
        $games_by_date = Game::orderBy('created_at', 'ASC')->get();
        $games_by_rating = Game::where('rating', '>=', 4)->orderByDesc('rating')->get();
        $games_by_downloads = Game::orderBy('downloads', 'DESC')->get();
        $categories = Category::all();

        $OnWishlist = Wishlist::where('user_id', auth()->user()->id)->get();
        if ($OnWishlist) {
            $ItemsOnWishlist = $OnWishlist->count();
        } else {
            $ItemsOnWishlist = 0;
        }

        $games = Game::where('user_id', auth()->user()->id);
        if ($games) {
            $allGames = $games->count();
        } else {
            $allGames = 0;
        }

        if ($response == 1) {
            return [
                'message' => 'game removed from wishlist',
                'new_games' => $games_by_date,
                'top_games' => $games_by_rating,
                'most_downloaded' => $games_by_downloads,
                'user_games' => $games_all,
                'wishlist' => $ItemsOnWishlist,
                'games_count' => $allGames,
                'categories' => $categories,
            ];
        } else {
            return [
                'message' => 'NOT DELETED',
                'new_games' => null,
                'top_games' => null,
                'most_downloaded' => null,
                'user_games' => null,
                'wishlist' => null,
                'games_count' => null,
                'categories' => null,
            ];
        }
    }
}
