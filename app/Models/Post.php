<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'tags', 'summary', 'slug', 'description', 'photo', 'quote', 'post_cat_id', 'post_tag_id', 'added_by', 'status'];

    public function cat_info()
    {
        return $this->hasOne('App\Models\PostCategory', 'id', 'post_cat_id');
    }

    public function tag_info()
    {
        return $this->hasOne('App\Models\PostTag', 'id', 'post_tag_id');
    }

    public function author_info()
    {
        return $this->hasOne('App\Models\User', 'id', 'added_by');
    }

    public static function getAllPost()
    {
        return Post::with(['cat_info', 'author_info'])->orderBy('id', 'DESC')->paginate(10);
    }

    public static function getPostBySlug($slug)
    {
        return Post::with(['tag_info', 'author_info'])->where('slug', $slug)->where('status', 'active')->first();
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_id')->where('status', 'active')->with('user_info')->orderBy('id', 'DESC');
    }

    public function allComments()
    {
        return $this->hasMany(PostComment::class)->where('status', 'active');
    }

    public static function getBlogByTag($slug)
    {
        // Tìm posts có tags chứa slug (tags được lưu dưới dạng comma-separated slugs)
        // Ví dụ: tags = "vest-cong-so,ao-so-mi,phoi-do-nam"
        // Tìm chính xác slug, không bị match nhầm (ví dụ: "ao" không match "ao-so-mi")
        return Post::where(function ($query) use ($slug) {
            // Tìm chính xác slug trong danh sách tags (comma-separated)
            // Sử dụng nhiều điều kiện để cover tất cả các trường hợp
            $query->where('tags', $slug)  // Trường hợp tags chỉ có 1 tag: "slug"
                ->orWhere('tags', 'LIKE', $slug . ',%')  // Tag ở đầu: "slug,other,tags"
                ->orWhere('tags', 'LIKE', '%,' . $slug)  // Tag ở cuối: "other,tags,slug"
                ->orWhere('tags', 'LIKE', '%,' . $slug . ',%');  // Tag ở giữa: "other,tags,slug,another"
        })
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->paginate(8);
    }

    public static function countActivePost()
    {
        $data = Post::where('status', 'active')->count();
        if ($data) {
            return $data;
        }
        return 0;
    }
}
