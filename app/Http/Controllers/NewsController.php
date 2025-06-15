<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only('title', 'content', 'category_id');
        $data['user_id'] = Auth::id();
        $data['status'] = 'draft';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        News::create($data);

        return redirect()->route('dashboard')->with('success', 'Berita berhasil dibuat sebagai draft.');
    }

    public function approval()
    {
        $news = News::where('status', 'draft')->with('category', 'user')->get();
        return view('news.approval', compact('news'));
    }

    public function approve(News $news)
    {
        $news->update(['status' => 'published']);
        return redirect()->route('news.approval')->with('success', 'Berita disetujui.');
    }

    public function reject(News $news)
    {
        $news->update(['status' => 'rejected']);
        return redirect()->route('news.approval')->with('success', 'Berita ditolak.');
    }
}