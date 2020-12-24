<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use Intervention\Image\Facades\Image;
use League\ColorExtractor\ColorExtractor;


class PageController extends Controller
{
    public function image()
    {
        $path = public_path('images');
        $files = array_diff(scandir($path), array('.', '..'));

        return view('pages/image', ['files' => $files]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $originalName = $request->image->getClientOriginalName();
        $imageName = time() . '-' . $originalName . '.' . $request->image->getClientOriginalExtension();
        $request->image->move(public_path('images'), $imageName);
        $palette = Palette::fromFilename('images/' . $imageName);
        $extractor = new ColorExtractor($palette);
        $colors = $extractor->extract(1);
        $rgb = Color::fromIntToRgb($colors[0]);
        $img = Image::make(public_path('images/' . $imageName));
        $rgbMax = array_keys($rgb, max($rgb));
        if ($rgbMax[0] === 'r') {
            $img->insert(public_path('watermark/black.jpg'), 'top-right', 10, 10);
        } elseif ($rgbMax[0] === 'g') {
            $img->insert(public_path('watermark/red.jpg'), 'top-right', 10, 10);
        } elseif ($rgbMax[0] === 'b') {
            $img->insert(public_path('watermark/yellow.jpg'), 'top-right', 10, 10);
        }
        $img->save();

        return redirect()->route('image');
    }
}
