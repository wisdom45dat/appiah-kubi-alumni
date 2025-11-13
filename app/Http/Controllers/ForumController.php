<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::with(['latestTopic.user', 'topics'])
            ->active()
            ->ordered()
            ->get();

        $recentTopics = ForumTopic::with(['user', 'forum'])
            ->latest()
            ->limit(10)
            ->get();

        $popularTopics = ForumTopic::with(['user', 'forum'])
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        return view('forum.index', compact('forums', 'recentTopics', 'popularTopics'));
    }

    public function showForum(Forum $forum)
    {
        $topics = $forum->topics()
            ->with(['user', 'lastReplyBy'])
            ->latest()
            ->paginate(20);

        return view('forum.forum', compact('forum', 'topics'));
    }

    public function showTopic(ForumTopic $topic)
    {
        $topic->incrementViews();
        
        $posts = $topic->posts()
            ->with(['user', 'replies.user', 'likes'])
            ->orderBy('created_at')
            ->get()
            ->groupBy('parent_id');

        return view('forum.topic', compact('topic', 'posts'));
    }

    public function createTopic(Forum $forum)
    {
        return view('forum.create-topic', compact('forum'));
    }

    public function storeTopic(Request $request, Forum $forum)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $topic = ForumTopic::create([
            'forum_id' => $forum->id,
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        // Create the first post (the topic itself)
        ForumPost::create([
            'topic_id' => $topic->id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        $forum->updateCounts();

        return redirect()->route('forum.topic', $topic)
            ->with('success', 'Topic created successfully!');
    }

    public function createPost(Request $request, ForumTopic $topic)
    {
        if (!$topic->canUserPost(auth()->user())) {
            return redirect()->back()->with('error', 'This topic is locked.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5',
            'parent_id' => 'nullable|exists:forum_posts,id',
        ]);

        $post = ForumPost::create([
            'topic_id' => $topic->id,
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'],
            'content' => $validated['content'],
        ]);

        $topic->updateLastReply($post);

        return redirect()->route('forum.topic', $topic) . '#post-' . $post->id
            ->with('success', 'Reply posted successfully!');
    }

    public function likePost(ForumPost $post)
    {
        $like = $post->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        if ($like->wasRecentlyCreated) {
            $post->increment('likes_count');
            return response()->json(['liked' => true, 'likes_count' => $post->likes_count]);
        }

        return response()->json(['liked' => false, 'likes_count' => $post->likes_count]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 3) {
            return redirect()->back()->with('error', 'Please enter at least 3 characters to search.');
        }

        $topics = ForumTopic::with(['forum', 'user'])
            ->where('title', 'like', '%' . $query . '%')
            ->orWhere('content', 'like', '%' . $query . '%')
            ->paginate(20);

        $posts = ForumPost::with(['topic.forum', 'user'])
            ->where('content', 'like', '%' . $query . '%')
            ->paginate(20);

        return view('forum.search', compact('topics', 'posts', 'query'));
    }
}
