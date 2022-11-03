<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class UserController extends Controller
{
    public function signup(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'location' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'location' => $fields['location'],
            'password' => Hash::make($fields['password'])
        ]);
        $token = $user->createToken('gamestoreapp')->plainTextToken;

        $OnWishlist = Wishlist::find($user->id);
        if ($OnWishlist) {
            $ItemsOnWishlist = $OnWishlist->count();
        } else {
            $ItemsOnWishlist = 0;
        }

        $games = Game::where('user_id', $user->id);
        if ($games) {
            $allGames = $games->count();
        } else {
            $allGames = 0;
        }

        return [
            'message' => 'User registered',
            'user' => $user,
            'joinDate' => $user->created_at->diffForHumans(),
            'wishlist' => $ItemsOnWishlist,
            'games' => $allGames,
            'token' => $token
        ];
    }
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email',  $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return [
                'message' => 'Bad credentials',
            ];
        }
        $token = $user->createToken('gamestoreapp')->plainTextToken;

        $OnWishlist = Wishlist::find($user->id);
        if ($OnWishlist) {
            $ItemsOnWishlist = $OnWishlist->count();
        } else {
            $ItemsOnWishlist = 0;
        }

        $games = Game::where('user_id', $user->id);
        if ($games) {
            $allGames = $games->count();
        } else {
            $allGames = 0;
        }

        return [
            'message' => 'Logged in',
            'user' => $user,
            'joinDate' => $user->created_at->diffForHumans(),
            'wishlist' => $ItemsOnWishlist,
            'games' => $allGames,
            'token' => $token,
        ];
    }


    public function update(Request $request, $email)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'image' => 'image|mimes:png,jpg|nullable',
            'location' => 'required|string',
            'about' => 'required|string'
        ]);

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->name = $fields['name'];
            $user->location = $fields['location'];
            $user->about = $fields['about'];
            $user->update();


            $token = $user->createToken('gamestoreapp')->plainTextToken;

            $OnWishlist = Wishlist::find($user->id);
            if ($OnWishlist) {
                $ItemsOnWishlist = $OnWishlist->count();
            } else {
                $ItemsOnWishlist = 0;
            }

            $games = Game::where('user_id', $user->id);
            if ($games) {
                $allGames = $games->count();
            } else {
                $allGames = 0;
            }

            return [
                'message' => 'Logged in',
                'user' => $user,
                'joinDate' => $user->created_at->diffForHumans(),
                'wishlist' => $ItemsOnWishlist,
                'games' => $allGames,
                'token' => $token,
            ];
        } else {
            return [
                'message' => 'USER NOT FOUND',
            ];
        }
    }

    public function update_image(Request $request, $email)
    {
        $fields = $request->validate([
            'image' => 'required',
        ]);

        $user = User::where('email', $email)->first();

        if ($fields['image']) {
            $tmp__avatar_url = Cloudinary::upload($fields['image']->getRealPath())->getSecurePath();
            $user->image = $tmp__avatar_url;
            $user->update();

            $token = $user->createToken('gamestoreapp')->plainTextToken;

            $OnWishlist = Wishlist::find($user->id);
            if ($OnWishlist) {
                $ItemsOnWishlist = $OnWishlist->count();
            } else {
                $ItemsOnWishlist = 0;
            }

            $games = Game::where('user_id', $user->id);
            if ($games) {
                $allGames = $games->count();
            } else {
                $allGames = 0;
            }

            return [
                'message' => 'avatar uploaded!',
                'user' => $user,
                'joinDate' => $user->created_at->diffForHumans(),
                'wishlist' => $ItemsOnWishlist,
                'games' => $allGames,
                'token' => $token,
            ];
        } else {
            return [
                'message' => 'error avatar image'
            ];
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }



    public function destroy(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $response = $user->delete();
        $request->user()->currentAccessToken()->delete();

        if ($response == 1) {
            return [
                'message' => 'account deleted',
            ];
        } else {
            return [
                'message' => 'NOT DELETED'
            ];
        }
    }
}
