<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games_by_date = Game::orderBy('created_at', 'ASC')->get();
        $games_by_rating = Game::where('rating', '>=', 4)->orderByDesc('rating')->get();
        $games_by_downloads = Game::orderBy('downloads', 'DESC')->get();
        $categories = Category::all();

        return [
            'new_games' => $games_by_date,
            'top_games' => $games_by_rating,
            'most_downloaded' => $games_by_downloads,
            'categories' => $categories
        ];
    }

    /**
     * return all games according to the logged in user
     */
    public function user_games($email)
    {
        $user = User::where('email', $email)->first();
        $games = Game::where('user_id', $user->id)->get();
        $categories = Category::all();

        if ($games) {
            return [
                'games' => $games,
                'categories' => $categories
            ];
        } else {
            return [
                'games' => null
            ];
        }
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
            'title' => 'required|string',
            'description' => 'required|string',
            'downloads' => 'required',
            'rating' => 'required',
            'category_id' => 'required',
            'image_wide' => 'nullable',
            'image_tall' => 'nullable',
        ]);

        $game = new Game();
        $game->title = $fields['title'];
        $game->description = $fields['description'];
        $game->downloads = $fields['downloads'];
        $game->category_id = $fields['category_id'];
        $game->rating = $fields['rating'];

        if ($fields['image_wide'] !== null && $fields['image_tall'] !== null) {
            $tmp__img_wide_url = Cloudinary::upload($fields['image_wide']->getRealPath())->getSecurePath();
            $tmp__img_tall_url = Cloudinary::upload($fields['image_tall']->getRealPath())->getSecurePath();

            $game->image_wide = $tmp__img_wide_url;
            $game->image_tall = $tmp__img_tall_url;
        }

        $game->user_id = auth()->user()->id;

        $game->save();

        return [
            'message' => 'Game details uploaded!',
            'game' => $game
        ];
    }

    /**
     * store wide image
     */
    public function store_image_wide(Request $request, $id)
    {
        $fields = $request->validate([
            'image_wide' => 'required',
        ]);

        $game = Game::find($id);

        if ($fields['image_wide']) {
            $tmp__img_wide_url = Cloudinary::upload($fields['image_wide']->getRealPath())->getSecurePath();
            $game->image_wide = $tmp__img_wide_url;
            $game->update();

            return [
                'message' => 'image wide uploaded!',
            ];
        } else {
            return [
                'message' => 'error uploading wide image'
            ];
        }
    }

    /**
     * store tall image
     */
    public function store_image_tall(Request $request, $id)
    {
        $fields = $request->validate([
            'image_tall' => 'required',
        ]);

        $game = Game::find($id);

        if ($fields['image_tall']) {
            $tmp__img_tall_url = Cloudinary::upload($fields['image_tall']->getRealPath())->getSecurePath();
            $game->image_tall = $tmp__img_tall_url;
            $game->update();

            return [
                'message' => 'image tall uploaded!',
            ];
        } else {
            return [
                'message' => 'error uploading tall image'
            ];
        }
    }
    /**
     * Display the specified game by category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $games = Game::where('category_id', $id)->get();

        if ($games) {
            return [
                'category_search_result' => $games
            ];
        } else {
            return [
                'category_search_result' => null
            ];
        }
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
        $fields = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'downloads' => 'required|integer',
            'rating' => 'required|integer',
            'category_id' => 'required|integer',
            'image_wide' => 'required|image|mimes:jpeg,png,jpg',
            'image_tall' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        $game = Game::find($id);
        $game->title = $fields['title'];
        $game->description = $fields['description'];
        $game->downloads = $fields['downloads'];
        $game->category_id = $fields['category_id'];
        $game->rating = $fields['rating'];

        $tmp__img_wide_url = Cloudinary::upload($fields['image_wide']->getRealPath())->getSecurePath();
        $tmp__img_tall_url = Cloudinary::upload($fields['image_tall']->getRealPath())->getSecurePath();

        $game->image_wide = $tmp__img_wide_url;
        $game->image_tall = $tmp__img_tall_url;

        $game->user_id = auth()->user()->id;

        $game->update();

        return [
            'message' => 'Game details updated!',
            'game' => $game
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $game = Game::find($id);
        $response = $game->delete();
        if ($response == 1) {
            return [
                'message' => 'game deleted',
            ];
        } else {
            return [
                'message' => 'NOT DELETED'
            ];
        }
    }
}
