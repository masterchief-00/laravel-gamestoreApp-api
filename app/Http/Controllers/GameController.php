<?php

namespace App\Http\Controllers;

use App\Models\Game;
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
        return Game::all();
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
                'message'=>'image wide uploaded!',                
            ];
        }
        else
        {
            return [
                'message'=>'error uploading wide image'
            ];
        }
    }
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
                'message'=>'image tall uploaded!',                
            ];
        }
        else
        {
            return [
                'message'=>'error uploading tall image'
            ];
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Game::find($id);
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
