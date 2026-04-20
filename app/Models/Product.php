<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product count()
 */
class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        // Basic Information
        'product_code',
        'name',
        'description',
        'category',
        'sub_category',
        'brand',

        // Pricing
        'price',
        'mrp',
        'cost_price',
        'wholesale_price',

        // Stock
        'quantity',
        'min_quantity',
        'max_quantity',
        'reorder_level',

        // ✅ NEW SHIPPING ATTRIBUTES
        'weight',
        'length',
        'width',
        'height',
        'dimension_unit',

        // ✅ NEW SHIPPING CATEGORIES
        'shipping_category',
        'hsn_code',
        'is_hazardous',
        'is_fragile',
        'requires_cold_storage',
        'max_temperature',
        'min_temperature',

        // ✅ NEW PACKAGING
        'package_type',
        'units_per_package',
        'max_quantity_per_shipment',

        // Media
        'image',
        'gallery_images',

        // Status
        'is_active',
        'is_featured',

        // SEO
        'meta_title',
        'meta_description',
        'meta_keywords',

        // Audit
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        // Pricing
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',

        // Stock
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'reorder_level' => 'integer',

        // ✅ Shipping Dimensions
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',

        // ✅ Temperature
        'max_temperature' => 'integer',
        'min_temperature' => 'integer',

        // ✅ Packaging
        'units_per_package' => 'integer',
        'max_quantity_per_shipment' => 'integer',

        // ✅ Booleans
        'is_hazardous' => 'boolean',
        'is_fragile' => 'boolean',
        'requires_cold_storage' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',

        // ✅ JSON
        'gallery_images' => 'array',

        // Dates
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /* ==================== EXISTING RELATIONSHIPS ==================== */

    /**
     * Get sale items for this product
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get purchases for this product
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /* ==================== NEW RELATIONSHIPS ==================== */

    /**
     * Get the user who created this product
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this product
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get shipments containing this product
     */
    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'shipment_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    /**
     * Get all sales for this product
     */
    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_items')
                    ->withPivot('quantity', 'price', 'total', 'mrp')
                    ->withTimestamps();
    }

    /* ==================== EXISTING ACCESSORS ==================== */

    public function getImageUrlAttribute()
    {
        if ($this->image && filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return $this->image ? asset('storage/' . $this->image) : asset('images/no-image.png');
    }

    public function getFormattedPriceAttribute()
    {
        return '₹ ' . number_format($this->price, 2);
    }

    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return ['text' => 'Out of Stock', 'color' => 'red', 'badge' => 'danger'];
        } elseif ($this->quantity <= ($this->min_quantity ?? 5)) {
            return ['text' => 'Critical', 'color' => 'red', 'badge' => 'danger'];
        } elseif ($this->quantity <= ($this->reorder_level ?? 10)) {
            return ['text' => 'Low Stock', 'color' => 'orange', 'badge' => 'warning'];
        } elseif ($this->quantity <= ($this->max_quantity ?? 20)) {
            return ['text' => 'Moderate', 'color' => 'yellow', 'badge' => 'info'];
        } else {
            return ['text' => 'Sufficient', 'color' => 'green', 'badge' => 'success'];
        }
    }

    /* ==================== NEW ACCESSORS ==================== */

    /**
     * Get formatted MRP
     */
    public function getFormattedMrpAttribute()
    {
        return $this->mrp ? '₹ ' . number_format($this->mrp, 2) : null;
    }

    /**
     * Get formatted cost price
     */
    public function getFormattedCostPriceAttribute()
    {
        return $this->cost_price ? '₹ ' . number_format($this->cost_price, 2) : null;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->mrp || $this->mrp <= 0 || $this->mrp <= $this->price) {
            return 0;
        }

        return round((($this->mrp - $this->price) / $this->mrp) * 100, 1);
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price <= 0) {
            return null;
        }

        return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
    }

    /**
     * Get formatted dimensions
     */
    public function getFormattedDimensionsAttribute()
    {
        $dimensions = [];

        if ($this->length) $dimensions[] = $this->length . ' cm';
        if ($this->width) $dimensions[] = $this->width . ' cm';
        if ($this->height) $dimensions[] = $this->height . ' cm';

        return !empty($dimensions) ? implode(' × ', $dimensions) : 'N/A';
    }

    /**
     * Get formatted weight
     */
    public function getFormattedWeightAttribute()
    {
        return $this->weight ? $this->weight . ' kg' : 'N/A';
    }

    /**
     * Get volumetric weight (for shipping calculation)
     */
    public function getVolumetricWeightAttribute()
    {
        if (!$this->length || !$this->width || !$this->height) {
            return $this->weight ?? 0;
        }

        // Standard volumetric factor (5000 for cm³ to kg)
        $volumetricWeight = ($this->length * $this->width * $this->height) / 5000;

        return max($this->weight ?? 0, $volumetricWeight);
    }

    /**
     * Get shipping category display
     */
    public function getShippingCategoryDisplayAttribute()
    {
        $categories = [
            'standard' => '📦 Standard',
            'fragile' => '🔴 Fragile',
            'hazardous' => '⚠️ Hazardous',
            'cold' => '❄️ Cold Storage',
            'oversized' => '📏 Oversized',
            'documents' => '📄 Documents'
        ];

        return $categories[$this->shipping_category] ?? $this->shipping_category ?? 'Standard';
    }

    /**
     * Get package type display
     */
    public function getPackageTypeDisplayAttribute()
    {
        $types = [
            'box' => '📦 Box',
            'envelope' => '✉️ Envelope',
            'pallet' => '📏 Pallet',
            'tube' => '🧪 Tube',
            'custom' => '📦 Custom'
        ];

        return $types[$this->package_type] ?? $this->package_type ?? 'Box';
    }

    /**
     * Get HSN code with GST rate
     */
    public function getHsnWithGstAttribute()
    {
        return $this->hsn_code ?? 'N/A';
    }

    /**
     * Check if product requires special handling
     */
    public function getRequiresSpecialHandlingAttribute()
    {
        return $this->is_hazardous || $this->is_fragile || $this->requires_cold_storage;
    }

    /**
     * Get special handling instructions
     */
    public function getHandlingInstructionsAttribute()
    {
        $instructions = [];

        if ($this->is_fragile) {
            $instructions[] = 'Handle with care - Fragile';
        }

        if ($this->is_hazardous) {
            $instructions[] = 'Hazardous material - Special handling required';
        }

        if ($this->requires_cold_storage) {
            $instructions[] = "Keep between {$this->min_temperature}°C and {$this->max_temperature}°C";
        }

        return !empty($instructions) ? implode(' | ', $instructions) : 'Standard handling';
    }

    /**
     * Get total sales quantity
     */
    public function getTotalSoldAttribute()
    {
        return $this->saleItems()->sum('quantity');
    }

    /**
     * Get total revenue from sales
     */
    public function getTotalRevenueAttribute()
    {
        return $this->saleItems()->sum('total');
    }

    /**
     * Get total profit
     */
    public function getTotalProfitAttribute()
    {
        if (!$this->cost_price) {
            return null;
        }

        $totalCost = $this->total_sold * $this->cost_price;
        return $this->total_revenue - $totalCost;
    }

    /**
     * Get formatted total revenue
     */
    public function getFormattedTotalRevenueAttribute()
    {
        return '₹ ' . number_format($this->total_revenue, 2);
    }

    /**
     * Get formatted total profit
     */
    public function getFormattedTotalProfitAttribute()
    {
        if ($this->total_profit === null) {
            return 'N/A';
        }

        return '₹ ' . number_format($this->total_profit, 2);
    }

    /**
     * Get gallery image URLs
     */
    public function getGalleryImageUrlsAttribute()
    {
        if (!$this->gallery_images) {
            return [];
        }

        return collect($this->gallery_images)->map(function($image) {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }
            return asset('storage/' . $image);
        })->toArray();
    }

    /**
     * Get product name with code
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . ($this->product_code ?? 'No Code') . ')';
    }

    /**
     * Get product summary for quick view
     */
    public function getSummaryAttribute()
    {
        return [
            'id' => $this->id,
            'code' => $this->product_code,
            'name' => $this->name,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'mrp' => $this->mrp,
            'formatted_mrp' => $this->formatted_mrp,
            'discount' => $this->discount_percentage,
            'stock' => $this->quantity,
            'stock_status' => $this->stock_status,
            'image' => $this->image_url,
            'shipping_category' => $this->shipping_category_display,
            'requires_special_handling' => $this->requires_special_handling,
            'handling_instructions' => $this->handling_instructions,
            'volumetric_weight' => $this->volumetric_weight
        ];
    }

    /* ==================== METHODS ==================== */

    /**
     * Check if product is in stock
     */
    public function inStock($quantity = 1)
    {
        return $this->quantity >= $quantity;
    }

    /**
     * Decrement stock
     */
    public function decrementStock($quantity = 1)
    {
        if (!$this->inStock($quantity)) {
            throw new \Exception("Insufficient stock for {$this->name}");
        }

        $this->decrement('quantity', $quantity);

        return $this;
    }

    /**
     * Increment stock
     */
    public function incrementStock($quantity = 1)
    {
        $this->increment('quantity', $quantity);

        return $this;
    }

    /**
     * Check if reorder is needed
     */
    public function needsReorder()
    {
        return $this->quantity <= ($this->reorder_level ?? 10);
    }

    /**
     * Calculate maximum quantity that can be shipped together
     */
    public function maxShippableQuantity()
    {
        return min(
            $this->quantity,
            $this->max_quantity_per_shipment ?? PHP_INT_MAX
        );
    }

    /**
     * Calculate shipping class based on dimensions and weight
     */
    public function getShippingClassAttribute()
    {
        $volumetricWeight = $this->volumetric_weight;

        if ($volumetricWeight <= 0.5) {
            return 'small';
        } elseif ($volumetricWeight <= 2) {
            return 'medium';
        } elseif ($volumetricWeight <= 5) {
            return 'large';
        } elseif ($volumetricWeight <= 10) {
            return 'xlarge';
        } else {
            return 'oversized';
        }
    }

    /**
     * Get estimated shipping cost (base calculation)
     */
    public function getEstimatedShippingCost($distance = 100)
    {
        $baseRate = 50;
        $weight = $this->volumetric_weight;

        // Weight factor
        $weightFactor = $weight <= 0.5 ? 1 : ($weight <= 2 ? 1.5 : ($weight <= 5 ? 2 : 3));

        // Distance factor (per 100 km)
        $distanceFactor = ceil($distance / 100);

        // Special handling factor
        $specialFactor = $this->requires_special_handling ? 1.5 : 1;

        return $baseRate * $weightFactor * $distanceFactor * $specialFactor;
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope in stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Scope out of stock products
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->where('quantity', '>', 0)
                     ->whereRaw('quantity <= COALESCE(reorder_level, 10)');
    }

    /**
     * Scope products by category
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope products by brand
     */
    public function scopeOfBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    /**
     * Scope products by shipping category
     */
    public function scopeShippingCategory($query, $category)
    {
        return $query->where('shipping_category', $category);
    }

    /**
     * Scope products requiring special handling
     */
    public function scopeRequiresSpecialHandling($query)
    {
        return $query->where(function($q) {
            $q->where('is_hazardous', true)
              ->orWhere('is_fragile', true)
              ->orWhere('requires_cold_storage', true);
        });
    }

    /**
     * Scope fragile products
     */
    public function scopeFragile($query)
    {
        return $query->where('is_fragile', true);
    }

    /**
     * Scope hazardous products
     */
    public function scopeHazardous($query)
    {
        return $query->where('is_hazardous', true);
    }

    /**
     * Scope cold storage products
     */
    public function scopeColdStorage($query)
    {
        return $query->where('requires_cold_storage', true);
    }

    /**
     * Scope search by name or code
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('product_code', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('hsn_code', 'like', "%{$term}%");
        });
    }

    /**
     * Scope products with HSN code
     */
    public function scopeWithHsn($query)
    {
        return $query->whereNotNull('hsn_code');
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return [
            'total' => self::count(),
            'active' => self::active()->count(),
            'in_stock' => self::inStock()->count(),
            'out_of_stock' => self::outOfStock()->count(),
            'low_stock' => self::lowStock()->count(),
            'featured' => self::featured()->count(),
            'fragile' => self::fragile()->count(),
            'hazardous' => self::hazardous()->count(),
            'cold_storage' => self::coldStorage()->count(),
            'total_value' => self::sum(\DB::raw('quantity * price')),
            'top_categories' => self::select('category', \DB::raw('count(*) as total'))
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    /**
     * Generate unique product code
     */
    public static function generateProductCode()
    {
        $prefix = 'PRD';
        $year = date('y');
        $month = date('m');

        $lastProduct = self::whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastProduct) {
            $lastCode = substr($lastProduct->product_code, -4);
            $sequence = intval($lastCode) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->product_code)) {
                $product->product_code = self::generateProductCode();
            }

            if (empty($product->is_active)) {
                $product->is_active = true;
            }

            if (empty($product->dimension_unit)) {
                $product->dimension_unit = 'cm';
            }

            if (empty($product->package_type)) {
                $product->package_type = 'box';
            }

            if (empty($product->units_per_package)) {
                $product->units_per_package = 1;
            }
        });

        static::updating(function ($product) {
            // Log stock changes if needed
            if ($product->isDirty('quantity')) {
                $oldStock = $product->getOriginal('quantity');
                $newStock = $product->quantity;

                if ($newStock <= ($product->reorder_level ?? 10) && $oldStock > ($product->reorder_level ?? 10)) {
                    // Trigger reorder alert
                    activity()
                        ->performedOn($product)
                        ->log("Stock fell below reorder level: {$newStock}");
                }
            }
        });
    }
}
