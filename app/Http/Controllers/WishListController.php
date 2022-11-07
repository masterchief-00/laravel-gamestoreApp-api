<?php

namespace App\Http\Controllers;

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
        return [
            'message' => 'game added to wishlist',
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

        if ($response == 1) {
            return [
                'message' => 'game removed from wishlist'
            ];
        } else {
            return [
                'message' => 'NOT DELETED'
            ];
        }
    }
}
