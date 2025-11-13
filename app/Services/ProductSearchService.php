<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProductSearchService
{
    public function search(array $params, int $limit = 8)
    {
        $q = trim((string)($params['q'] ?? ''));
        $brandId = $params['brand_id'] ?? null;
        $categoryId = $params['category_id'] ?? null;
        $priceMin = isset($params['price_min']) ? (float)$params['price_min'] : null;
        $priceMax = isset($params['price_max']) ? (float)$params['price_max'] : null;
        $size = $params['size'] ?? null;
        $condition = $params['condition'] ?? null;
        $inStock = isset($params['in_stock']) ? (bool)$params['in_stock'] : null;

        if ($q === '' && !$brandId && !$categoryId && $priceMin === null && $priceMax === null && !$size && !$condition && $inStock === null) {
            return collect();
        }

        $res = $this->runQuery($brandId, $categoryId, $priceMin, $priceMax, $size, $condition, $inStock, $q, $limit);
        if ($res->count()) return $res;

        // Fallback: try without some filters
        if ($brandId && $categoryId) {
            $res = $this->runQuery($brandId, null, $priceMin, $priceMax, $size, $condition, $inStock, $q, $limit);
            if ($res->count()) return $res;
        }

        $res = $this->runQuery($brandId, $categoryId, $priceMin, $priceMax, $size, $condition, $inStock, '', $limit);
        if ($res->count()) return $res;

        // Price range expansion
        if ($priceMin !== null || $priceMax !== null) {
            $span = ($priceMin !== null && $priceMax !== null)
                ? max(10.0, 0.1 * ($priceMax - $priceMin))
                : 50.0;
            $res = $this->runQuery(
                $brandId,
                $categoryId,
                $priceMin !== null ? max(0, $priceMin - $span) : null,
                $priceMax !== null ? $priceMax + $span : null,
                $size,
                $condition,
                $inStock,
                '',
                $limit
            );
            if ($res->count()) return $res;
        }

        return $res;
    }

    protected function runQuery(?int $brandId, ?int $categoryId, ?float $priceMin, ?float $priceMax, ?string $size, ?string $condition, ?bool $inStock, string $q, int $limit)
    {
        $query = Product::where('status', 'active')
            ->with(['cat_info', 'sub_cat_info', 'brand']);

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        if ($categoryId) {
            $query->where(function ($q) use ($categoryId) {
                $q->where('cat_id', $categoryId)
                    ->orWhere('child_cat_id', $categoryId);
            });
        }
        if ($priceMin !== null) {
            $query->where('price', '>=', $priceMin);
        }
        if ($priceMax !== null) {
            $query->where('price', '<=', $priceMax);
        }
        if ($size) {
            $query->where(function ($q) use ($size) {
                $sizes = explode(',', $size);
                foreach ($sizes as $s) {
                    $q->orWhere('size', 'like', '%' . trim($s) . '%');
                }
            });
        }
        if ($condition) {
            $query->where('condition', $condition);
        }
        if ($inStock !== null) {
            if ($inStock) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '<=', 0);
            }
        }

        $tokens = $this->tokenize($q);
        if (!empty($tokens)) {
            $query->where(function (Builder $b) use ($tokens) {
                foreach ($tokens as $token) {
                    $b->where(function (Builder $c) use ($token) {
                        $like = '%' . $token . '%';
                        $c->where('title', 'like', $like)
                            ->orWhere('slug', 'like', $like)
                            ->orWhere('summary', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
                }
            });
        }

        $query->orderBy('is_featured', 'DESC')
            ->orderBy('id', 'DESC');

        return $query->limit($limit)->get();
    }

    protected function tokenize(string $text): array
    {
        if ($text === '') return [];

        $text = str_replace(["\u{2013}", "\u{2014}"], '-', $text);

        $noise = [
            'tôi cần',
            'mình cần',
            'cần',
            'muốn',
            'tìm',
            'nhãn hiệu',
            'hãng',
            'thương hiệu',
            'tầm',
            'khoảng',
            'giá',
            'dưới',
            'trên',
            'hoặc',
            'và',
            'theo',
            'mới',
            'cho',
            'của',
            'a',
            'an',
            'the',
            'about',
            'around',
            'under',
            'over',
            'between',
            'có',
            'bán',
            'không',
            'size',
            'còn',
            'hàng',
            'bao nhiêu',
            'tiền',
            'vnđ',
            'đồng'
        ];

        $lower = Str::of($text)->lower();
        foreach ($noise as $n) {
            $lower = $lower->replace($n, ' ');
        }

        $raw = preg_split('/[^\p{L}\p{N}\.]+/u', (string)$lower, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_values(array_filter($raw, fn($t) => Str::length($t) >= 2));

        return $tokens;
    }

    public function toPromptBullets($products): array
    {
        return $products->map(function (Product $p) {
            $brandName = $p->brand ? $p->brand->title : 'N/A';
            $categoryName = $p->cat_info ? $p->cat_info->title : 'N/A';
            $finalPrice = $p->discount ? ($p->price * (1 - $p->discount / 100)) : $p->price;
            $priceStr = number_format($finalPrice, 0, ',', '.') . ' VNĐ';
            if ($p->discount) {
                $priceStr .= ' (giảm ' . $p->discount . '%)';
            }

            $sizeStr = $p->size ? ' | Size: ' . $p->size : '';
            $stockStr = $p->stock > 0 ? ' | Còn hàng: ' . $p->stock : ' | Hết hàng';
            $url = route('product-detail', $p->slug);

            return "- {$p->title} ({$brandName}, {$categoryName}) — {$priceStr}{$sizeStr}{$stockStr} — Link: {$url}";
        })->all();
    }

    public function guessFiltersFromText(string $text): array
    {
        $out = [];

        // Get all brands and categories from database
        $brands = Brand::where('status', 'active')->pluck('title', 'id')->toArray();
        $categories = Category::where('status', 'active')->pluck('title', 'id')->toArray();

        $low = Str::of($text)->lower();

        // Check for brand
        foreach ($brands as $id => $brandTitle) {
            if ($low->contains(Str::lower($brandTitle))) {
                $out['brand_id'] = $id;
                break;
            }
        }

        // Check for category keywords
        foreach ($categories as $id => $catTitle) {
            if ($low->contains(Str::lower($catTitle))) {
                $out['category_id'] = $id;
                break;
            }
        }

        // Common product keywords
        $productKeywords = [
            'áo' => ['áo', 'shirt', 'vest', 'sơ mi', 'áo khoác'],
            'quần' => ['quần', 'pants', 'jeans', 'trousers'],
            'giày' => ['giày', 'shoes', 'sneakers', 'boots'],
            'túi' => ['túi', 'bag', 'handbag', 'backpack'],
        ];

        // Size detection
        if (preg_match('/\b(s|m|l|xl|xxl|xs)\b/i', $text, $matches)) {
            $out['size'] = strtoupper($matches[1]);
        }

        // Condition detection
        if ($low->contains(['mới', 'new', 'hot'])) {
            $out['condition'] = 'new';
        } elseif ($low->contains(['hot', 'nổi bật'])) {
            $out['condition'] = 'hot';
        }

        // Stock detection
        if ($low->contains(['còn hàng', 'có hàng', 'in stock'])) {
            $out['in_stock'] = true;
        } elseif ($low->contains(['hết hàng', 'out of stock'])) {
            $out['in_stock'] = false;
        }

        // Price detection
        $textNorm = str_replace(["\u{2013}", "\u{2014}"], '-', $text);

        if (preg_match('/(\d+)\s*-\s*(\d+)\s*(?:k|nghìn|triệu|vnđ|đồng|vnd)?/i', $textNorm, $m)) {
            $a = (float)$m[1];
            $b = (float)$m[2];
            // Check if it's in thousands or millions
            if (preg_match('/triệu|million/i', $textNorm)) {
                $a *= 1000000;
                $b *= 1000000;
            } elseif (preg_match('/k|nghìn|thousand/i', $textNorm)) {
                $a *= 1000;
                $b *= 1000;
            }
            $out['price_min'] = min($a, $b);
            $out['price_max'] = max($a, $b);
        } elseif (preg_match('/(?:dưới|under|<=|tối đa|max)\s*(\d+)\s*(?:k|nghìn|triệu|vnđ|đồng|vnd)?/i', $textNorm, $m)) {
            $val = (float)$m[1];
            if (preg_match('/triệu|million/i', $textNorm)) {
                $val *= 1000000;
            } elseif (preg_match('/k|nghìn|thousand/i', $textNorm)) {
                $val *= 1000;
            }
            $out['price_max'] = $val;
        } elseif (preg_match('/(?:trên|over|>=|tối thiểu|min|từ)\s*(\d+)\s*(?:k|nghìn|triệu|vnđ|đồng|vnd)?/i', $textNorm, $m)) {
            $val = (float)$m[1];
            if (preg_match('/triệu|million/i', $textNorm)) {
                $val *= 1000000;
            } elseif (preg_match('/k|nghìn|thousand/i', $textNorm)) {
                $val *= 1000;
            }
            $out['price_min'] = $val;
        }

        return $out;
    }
}
