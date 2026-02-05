<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

class LikeButton extends Component
{
    public Post $post;
    public int $likesCount;
    public bool $likedByMe = false;

    public function mount(Post $post)
    {
        $this->post = $post;

        $this->likesCount = $post->likes()->count();

        $this->likedByMe = auth()->check()
            ? $post->likes()->where('user_id', auth()->id())->exists()
            : false;
    }

    public function toggleLike()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ($this->likedByMe) {
            $this->post->likes()
                ->where('user_id', auth()->id())
                ->delete();

            $this->likesCount--;
            $this->likedByMe = false;
        } else {
            $this->post->likes()->firstOrCreate([
                'user_id' => auth()->id(),
            ]);

            $this->likesCount++;
            $this->likedByMe = true;
        }
    }

    public function render()
    {
        return view('livewire.like-button');
    }
}
