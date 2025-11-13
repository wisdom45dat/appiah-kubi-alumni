<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index()
    {
        $articles = NewsArticle::published()
            ->with('author')
            ->latest('published_at')
            ->paginate(12);

        $featuredArticles = NewsArticle::published()
            ->featured()
            ->with('author')
            ->latest('published_at')
            ->limit(3)
            ->get();

        $popularArticles = NewsArticle::published()
            ->with('author')
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();

        return view('news.index', compact('articles', 'featuredArticles', 'popularArticles'));
    }

    public function show(NewsArticle $article)
    {
        if (!$article->is_published && !auth()->user()?->hasRole('admin')) {
            abort(404);
        }

        $article->incrementViews();
        $article->load('author');

        $relatedArticles = NewsArticle::published()
            ->where('id', '!=', $article->id)
            ->where(function($query) use ($article) {
                if ($article->tags) {
                    foreach ($article->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->limit(4)
            ->get();

        return view('news.show', compact('article', 'relatedArticles'));
    }

    public function create()
    {
        return view('news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        // Generate slug
        $slug = Str::slug($validated['title']);
        $counter = 1;
        $originalSlug = $slug;
        
        while (NewsArticle::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle featured image
        $featuredImage = null;
        if ($request->hasFile('featured_image')) {
            $featuredImage = $request->file('featured_image')->store('news', 'public');
        }

        // Process tags
        $tags = [];
        if ($validated['tags']) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $tags = array_slice($tags, 0, 10); // Limit to 10 tags
        }

        $article = NewsArticle::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'featured_image' => $featuredImage,
            'author_name' => auth()->user()->name,
            'author_id' => auth()->id(),
            'tags' => $tags,
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $request->has('publish'),
            'published_at' => $request->has('publish') ? now() : null,
        ]);

        if ($request->has('publish')) {
            return redirect()->route('news.show', $article)
                ->with('success', 'Article published successfully!');
        }

        return redirect()->route('news.edit', $article)
            ->with('success', 'Article saved as draft.');
    }

    public function edit(NewsArticle $article)
    {
        if ($article->author_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'You can only edit your own articles.');
        }

        return view('news.edit', compact('article'));
    }

    public function update(Request $request, NewsArticle $article)
    {
        if ($article->author_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'You can only edit your own articles.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        // Handle featured image
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('news', 'public');
        }

        // Process tags
        $tags = [];
        if ($validated['tags']) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $tags = array_slice($tags, 0, 10);
        }
        $validated['tags'] = $tags;

        // Update slug if title changed
        if ($article->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $counter = 1;
            $originalSlug = $slug;
            
            while (NewsArticle::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $article->update($validated);

        if ($request->has('publish') && !$article->is_published) {
            $article->publish();
        }

        return redirect()->route('news.show', $article)
            ->with('success', 'Article updated successfully!');
    }
}
